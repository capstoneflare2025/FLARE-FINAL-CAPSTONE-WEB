/* resources/js/ADMIN-PROFILE-BLADE/profile.js
   Admin Profile page logic:
   - Loads profile from Realtime DB at `${stationKey}/Profile`
   - Edit/Cancel/Save flow
   - Optional password change with secondary Firebase app for re-auth
*/

(function () {
  document.addEventListener('DOMContentLoaded', function () {
    // ---- Station key from <meta>, fallback to default
    const metaStation = document.querySelector('meta[name="station-key"]');
    const stationKey = (window.__STATION_KEY__ || metaStation?.content || 'TagumCityCentralFireStation').trim();

    // ---- Firebase config (must match your project)
    const firebaseConfig = {
      apiKey: "AIzaSyCrjSyOI-qzCaJptEkWiRfEuaG28ugTmdE",
      authDomain: "capstone-flare-2025.firebaseapp.com",
      databaseURL: "https://capstone-flare-2025-default-rtdb.firebaseio.com",
      projectId: "capstone-flare-2025",
      storageBucket: "capstone-flare-2025.firebasestorage.app",
      messagingSenderId: "685814202928",
      appId: "1:685814202928:web:9b484f04625e5870c9a3f5",
      measurementId: "G-QZ8P5VLHF2"
    };

    // Guard Firebase init (root may already have it)
    if (!window.firebase) {
      console.error('[Admin Profile] Firebase SDK not found on page.');
      return;
    }
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

    const db = firebase.database();
    const PROFILE_PATH = `${stationKey}/Profile`;
    const profileRef = db.ref(PROFILE_PATH);

    // ---------- Elements ----------
    const form   = document.getElementById('admin-form');
    const editBtn = document.getElementById('edit-btn');
    const saveBtn = document.getElementById('save-btn');

    const nameEl   = document.getElementById('admin-name');
    const emailEl  = document.getElementById('admin-email');
    const phoneEl  = document.getElementById('admin-phone');
    const statusEl = document.getElementById('admin-status');

    const currentPassEl = document.getElementById('current-password');
    const verifyBtn     = document.getElementById('verify-btn');
    const verifyMsg     = document.getElementById('verify-msg');

    const newPassBlock  = document.getElementById('new-pass-block');
    const passEl        = document.getElementById('admin-password');
    const pass2El       = document.getElementById('admin-password-confirm');

    const allInputs = document.querySelectorAll('#admin-form input, #admin-form select');

    // ---------- State ----------
    let passwordVerified = false;
    let verifiedWithPassword = '';

    // ---------- Helpers ----------
    const lockFields = () => { allInputs.forEach(el => el.disabled = true); };
    const unlockFields = () => { allInputs.forEach(el => el.disabled = false); };

    function loadProfileIntoForm() {
      return profileRef.once('value').then(snap => {
        const data = snap.val() || {};
        nameEl.value  = (data.name || '').trim();
        emailEl.value = (data.email || '').trim();
        phoneEl.value = (data.contact || '').trim();
        const st = (data.status || '').trim();
        statusEl.value = (st === 'Active' || st === 'Inactive') ? st : 'Active';
      });
    }

    function resetPasswordUI() {
      passwordVerified = false;
      verifiedWithPassword = '';
      currentPassEl.value = '';
      passEl.value = '';
      pass2El.value = '';
      newPassBlock.classList.add('hidden');
      passEl.disabled = true;
      pass2El.disabled = true;
      verifyBtn.disabled = true; // disabled unless in edit mode
      verifyMsg.textContent = '';
      verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-gray-500';
    }

    // ---------- Initial load ----------
    lockFields();
    loadProfileIntoForm().catch(err => console.error('[Admin Profile] load failed:', err));

    // ---------- Edit / Cancel ----------
    editBtn.addEventListener('click', async function () {
      const isEdit = (editBtn.textContent.trim().toLowerCase() === 'edit');

      if (isEdit) {
        unlockFields();
        // Only enable current password + verify first; keep new pass disabled/hidden
        verifyBtn.disabled = false;
        newPassBlock.classList.add('hidden');
        passEl.disabled = true;
        pass2El.disabled = true;

        editBtn.textContent = 'Cancel';
        saveBtn.classList.remove('hidden');

        verifyMsg.textContent = 'Enter your current password, then click Verify.';
        verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-gray-600';
      } else {
        // Cancel → reload values & lock
        try { await loadProfileIntoForm(); } catch {}
        lockFields();
        editBtn.textContent = 'Edit';
        saveBtn.classList.add('hidden');
        resetPasswordUI();
      }
    });

    // ---------- Verify current password (secondary app to avoid touching main state) ----------
    verifyBtn.addEventListener('click', async function () {
      const email = (emailEl.value || '').trim();
      const pwd   = (currentPassEl.value || '').trim();

      if (!email || !pwd) {
        verifyMsg.textContent = 'Enter your current password first.';
        verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-red-600';
        return;
      }

      let secondaryApp = null;
      try {
        secondaryApp = firebase.apps.find(a => a.name === 'verifyApp') ||
                       firebase.initializeApp(firebaseConfig, 'verifyApp');

        const cred = await secondaryApp.auth().signInWithEmailAndPassword(email, pwd);
        if (!cred || !cred.user) throw new Error('No user returned');

        // success
        passwordVerified = true;
        verifiedWithPassword = pwd;

        verifyMsg.textContent = 'Password verified.';
        verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-green-700';

        currentPassEl.disabled = true;
        verifyBtn.disabled = true;

        newPassBlock.classList.remove('hidden');
        passEl.disabled = false;
        pass2El.disabled = false;

        await secondaryApp.auth().signOut();
      } catch (e) {
        console.error('[Admin Profile] verify failed:', e);
        passwordVerified = false;
        verifiedWithPassword = '';
        verifyMsg.textContent = 'Current password is incorrect.';
        verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-red-600';
      }
    });

    // ---------- Save ----------
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const rawStatus = (statusEl.value || '').trim();
      const status = rawStatus === 'Active' ? 'Active'
                   : rawStatus === 'Inactive' ? 'Inactive'
                   : 'Inactive';

      const updatedData = {
        name: nameEl.value,
        email: emailEl.value,
        contact: phoneEl.value,
        status: status
      };

      const newPass1 = (passEl.value || '').trim();
      const newPass2 = (pass2El.value || '').trim();

      try {
        // If a new password is requested, perform update via secondary app
        if (newPass1 || newPass2) {
          if (!passwordVerified) {
            alert('Please verify your current password first.');
            return;
          }
          if (newPass1.length < 6) {
            alert('New password must be at least 6 characters.');
            return;
          }
          if (newPass1 !== newPass2) {
            alert('New passwords do not match.');
            return;
          }

          const email = (emailEl.value || '').trim();
          const secondaryApp = firebase.apps.find(a => a.name === 'verifyApp') ||
                               firebase.initializeApp(firebaseConfig, 'verifyApp');

          const cred = await secondaryApp.auth().signInWithEmailAndPassword(email, verifiedWithPassword);
          if (!cred || !cred.user) throw new Error('Re-auth failed');

          await cred.user.updatePassword(newPass1);
          await secondaryApp.auth().signOut();
        }

        // Update profile fields in DB
        await profileRef.update(updatedData);

        alert('Profile updated successfully.');
        lockFields();
        editBtn.textContent = 'Edit';
        saveBtn.classList.add('hidden');
        resetPasswordUI();
      } catch (err) {
        console.error('[Admin Profile] save failed:', err);
        alert('Failed to save changes. Check console for details.');
      }
    });
  });
})();
