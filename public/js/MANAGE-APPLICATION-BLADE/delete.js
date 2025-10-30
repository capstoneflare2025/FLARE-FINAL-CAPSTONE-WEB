(function () {
  document.addEventListener('DOMContentLoaded', function () {
    // Require Firebase SDKs from layout
    if (typeof firebase === 'undefined') {
      console.error('[manage-application] Firebase SDK not loaded in layout.');
      return;
    }

    // If your layout already initialized the app, reuse it
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
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

    const db = firebase.database();

    // DOM
    const reportTypeSelect = document.getElementById('report-type');
    const reportOptionsBody = document.getElementById('report-options-body');
    const newOptionInput = document.getElementById('new-option');
    const addOptionBtn = document.getElementById('add-option-btn');

    // Refs (comma-separated strings per node)
    const refs = {
      fire:  db.ref('TagumCityCentralFireStation/ManageApplication/FireReport/Option'),
      other: db.ref('TagumCityCentralFireStation/ManageApplication/OtherEmergencyReport/Option'),
      ems:   db.ref('TagumCityCentralFireStation/ManageApplication/EmergencyMedicalServicesReport/Option'),
    };

    let currentRef = null;
    let currentListener = null;

    // Render table rows
    function renderOptionsCSV(csv) {
      const options = (csv ? String(csv) : '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);

      reportOptionsBody.innerHTML = '';
      options.forEach((option, idx) => {
        const tr = document.createElement('tr');
        tr.className = 'bg-white border-b hover:bg-gray-50';
        tr.innerHTML = `
          <td class="px-4 py-2 text-sm font-medium text-gray-900">${idx + 1}</td>
          <td class="px-4 py-2 text-sm text-gray-700 break-anywhere">${option}</td>
          <td class="px-4 py-2 text-sm">
            <button class="text-blue-600 hover:text-blue-800" data-action="edit" data-index="${idx}">
              <img src="/images/edit.png" alt="Edit" class="w-6 h-6 inline-block" />
            </button>
            <button class="text-red-600 hover:text-red-800 ml-2" data-action="delete" data-index="${idx}">
              <img src="/images/delete.png" alt="Delete" class="w-6 h-6 inline-block" />
            </button>
          </td>
        `;
        reportOptionsBody.appendChild(tr);
      });
    }

    // Swap realtime listener when report type changes
    function attachRealtime(type) {
      // detach previous
      if (currentRef && currentListener) currentRef.off('value', currentListener);

      currentRef = refs[type];
      currentListener = (snap) => renderOptionsCSV(snap.val());
      currentRef.on('value', currentListener);
    }

    // CSV helpers
    function tableOptions() {
      return Array.from(reportOptionsBody.querySelectorAll('td:nth-child(2)'))
        .map(td => td.textContent.trim());
    }
    function writeCSV(options) {
      return currentRef.set(options.join(','));
    }
    function sanitizeOption(s) {
      // prevent commas from corrupting the CSV format
      return s.replace(/,/g, ' / ').trim();
    }

    // Delegated click handler for edit/delete buttons
    reportOptionsBody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button[data-action]');
      if (!btn) return;

      const idx = Number(btn.dataset.index);
      const action = btn.dataset.action;
      const options = tableOptions();

      if (action === 'edit') {
        const prev = options[idx] || '';
        const next = prompt('Edit option:', prev);
        if (next !== null) {
          const cleaned = sanitizeOption(next);
          if (cleaned.length === 0) return;
          options[idx] = cleaned;
          await writeCSV(options);
        }
      }

      if (action === 'delete') {
        if (confirm('Delete this option?')) {
          options.splice(idx, 1);
          await writeCSV(options);
        }
      }
    });

    // Add new option
    addOptionBtn.addEventListener('click', async () => {
      const raw = newOptionInput.value;
      const cleaned = sanitizeOption(raw);
      if (!cleaned) {
        alert('Please enter a valid option.');
        return;
      }
      const options = tableOptions();
      options.push(cleaned);
      await writeCSV(options);
      newOptionInput.value = '';
    });

    // Initial
    attachRealtime('fire');
    reportTypeSelect.addEventListener('change', (e) => attachRealtime(e.target.value));
  });
})();
