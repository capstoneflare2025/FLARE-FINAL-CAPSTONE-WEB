@extends('ADMIN-DASHBOARD.app')

@section('title', 'Fire Fighters')

@section('content')
<div class="container mx-auto p-6">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
      Fire Fighters
      <span class="ml-2 inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
        {{ session('station_label') }}
      </span>
    </h1>
    <span class="text-sm text-gray-500">Total: <span id="rowCount">0</span></span>
  </div>

  <div class="bg-white p-6 shadow rounded-lg">
    <div class="flex items-center justify-between mb-4">
      <button id="addFirefighterBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg">
        Add New Fire Fighter
      </button>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="min-w-full table-auto responsive-table table-xs">
        <thead>
          <tr class="bg-gray-100 align-top">
            <th class="px-4 py-2 text-left text-gray-600">ID</th>
            <th class="px-4 py-2 text-left text-gray-600">Name</th>
            <th class="px-4 py-2 text-left text-gray-600">Contact</th>
            <th class="px-4 py-2 text-left text-gray-600">Date Added</th>
            <th class="px-4 py-2 text-center text-gray-600 w-20 sm:w-24">Actions</th>
          </tr>
        </thead>
        <tbody id="firefighterTableBody"></tbody>
      </table>
    </div>
  </div>

  <!-- View modal -->
  <div id="viewModal" class="hidden fixed inset-0 bg-gray-500/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 sm:w-1/2">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h3 class="text-xl font-semibold text-gray-800">Fire Fighter Details</h3>
          <p class="text-xs text-gray-500 mt-1">{{ session('station_label') }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">ID</div>
          <div id="viewFirefighterId" class="text-gray-800"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Name</div>
          <div id="viewFirefighterName" class="text-gray-800"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Email</div>
          <div id="viewFirefighterEmail" class="text-gray-800 break-all"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Contact</div>
          <div id="viewFirefighterContact" class="text-gray-800"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Birthday</div>
          <div id="viewFirefighterBirthday" class="text-gray-800"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Date Added</div>
          <div id="viewFirefighterCreated" class="text-gray-800"></div>
        </div>

        <div class="space-y-1">
          <div class="text-xs font-semibold text-gray-500 tracking-wide">Last Updated</div>
          <div id="viewFirefighterUpdated" class="text-gray-800"></div>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button id="closeViewModalBtn"
                class="px-5 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Add/Edit modal -->
  <div id="addModal" class="hidden fixed inset-0 bg-gray-500/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 sm:w-1/2">
      <h3 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Add New Fire Fighter</h3>

      <form method="POST" id="addFirefighterForm">
        @csrf
        <input type="hidden" id="firefighterRecordKey" />
        <input type="hidden" id="firefighterBucketKey" />

        <!-- Station is fixed to current; shown as read-only -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Station</label>
          <input type="text" id="fireStationNamePreview"
                 class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50"
                 value="{{ session('station_label') }}" readonly />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Name:</label>
          <input type="text" id="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Email:</label>
          <input type="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          <p class="text-xs text-gray-500 mt-1">Email cannot be changed after creation.</p>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Contact:</label>
          <input type="text" id="contact" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Birthday:</label>
          <input type="date" id="birthday" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>

        <!-- Right-aligned controls -->
        <div class="mt-4 flex justify-end gap-2">
          <button type="button" id="toggleEditBtn"
                  class="hidden px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Edit
          </button>

          <!-- NEW: Cancel (only when editing an existing record) -->
          <button type="button" id="cancelEditBtn"
                  class="hidden px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
            Cancel
          </button>

          <button type="submit" id="submitBtn"
                  class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Save
          </button>

          <button type="button" id="closeAddModalBtn"
                  class="px-5 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800">
            Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<script>
/* ------------ Station context from PHP session ------------ */
const STATION_KEY   = @json(session('station'));
const STATION_LABEL = @json(session('station_label'));
if (!STATION_KEY) console.error('Missing station in session. Ensure login sets Session::station.');

/* ------------ Firebase init ------------ */
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

/* ------------ Paths (single-bucket per station) ------------ */
const ROOT_STATION = `${STATION_KEY}/FireFighter`;
const BUCKET_KEY   = 'FireStationFireFighter';
const FULL_BUCKET  = `${ROOT_STATION}/${BUCKET_KEY}`;

/* ------------ Elements ------------ */
const rowCount            = document.getElementById('rowCount');
const tbody               = document.getElementById('firefighterTableBody');
const addBtn              = document.getElementById('addFirefighterBtn');
const addModal            = document.getElementById('addModal');
const addForm             = document.getElementById('addFirefighterForm');
const submitBtn           = document.getElementById('submitBtn');
const toggleEditBtn       = document.getElementById('toggleEditBtn');
const cancelEditBtn       = document.getElementById('cancelEditBtn'); // NEW
const recordKeyInput      = document.getElementById('firefighterRecordKey');
const recordBucketInput   = document.getElementById('firefighterBucketKey');
const fireStationNamePrev = document.getElementById('fireStationNamePreview');
const closeAddModalBtn    = document.getElementById('closeAddModalBtn');
const viewModal           = document.getElementById('viewModal');
const closeViewModalBtn   = document.getElementById('closeViewModalBtn');

const nameEl     = document.getElementById('name');
const emailEl    = document.getElementById('email');
const contactEl  = document.getElementById('contact');
const birthdayEl = document.getElementById('birthday');

/* ------------ Local state ------------ */
let bucketData = {};      // { autoKey: firefighterObj }
let isEditExisting = false;
let isEditingNow   = false;
let originalSnapshot = null; // NEW: for Cancel restore

/* ------------ Time helpers ------------ */
function manilaFormatter(epochMs) {
  if (!epochMs) return '';
  const parts = new Intl.DateTimeFormat('en-CA', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit',
    hour12: false, timeZone: 'Asia/Manila'
  }).formatToParts(new Date(epochMs));

  const grab = t => parts.find(p => p.type === t)?.value ?? '';
  const y  = grab('year');
  const m  = (grab('month')  || '').toString().padStart(2, '0');
  const d  = (grab('day')    || '').toString().padStart(2, '0');
  const hh = (grab('hour')   || '00').toString().padStart(2, '0');
  const mm = (grab('minute') || '00').toString().padStart(2, '0');
  return `${y}/${m}/${d} - ${hh}:${mm}`;
}
function nowTimestamp() {
  const epoch = Date.now();
  return { text: manilaFormatter(epoch), epoch };
}

/* ------------ ID helpers ------------ */
function getCurrentYear() { return new Date().getFullYear().toString(); }
function buildReadableId(prefix, seq, year) {
  const seqStr = String(seq).padStart(3, '0');
  return `${prefix}-${seqStr}${year}`;
}
const ID_PREFIX = 'FF';

async function generateSequentialId() {
  const ref  = firebase.database().ref(FULL_BUCKET);
  const snap = await ref.once('value');
  const year = getCurrentYear();
  const data = snap.val() || {};
  const ids = Object.values(data).map(f => f.id).filter(id => id && id.includes(year));

  let max = 0;
  ids.forEach(id => {
    const numPart = parseInt(id.split('-')[1]?.substring(0, 3));
    if (!isNaN(numPart) && numPart > max) max = numPart;
  });
  return buildReadableId(ID_PREFIX, max + 1, year);
}

/* ------------ Data helpers ------------ */
function mergedRows() {
  const map = bucketData || {};
  return Object.entries(map).map(([autoKey, ff]) => ({ _autoKey: autoKey, ...ff }));
}

/* ------------ Render table ------------ */
function applyRender() {
  let rows = mergedRows();

  rows.sort((a, b) => (b.createdAtEpoch || 0) - (a.createdAtEpoch || 0)
    || (a.name || '').localeCompare(b.name || ''));

  tbody.innerHTML = '';
  for (const row of rows) {
    const tr = document.createElement('tr');
    tr.className = 'border-b';

    const createdText = manilaFormatter(row.createdAtEpoch) || (row.createdAt || '');

    tr.innerHTML = `
      <td class="px-4 py-2">${row.id || ''}</td>
      <td class="px-4 py-2 font-semibold">${row.name || ''}</td>
      <td class="px-4 py-2">${row.contact || ''}</td>
      <td class="px-4 py-2">${createdText}</td>
      <td class="px-4 py-2 text-center">
        <div class="flex items-center justify-center gap-2 sm:gap-3 whitespace-nowrap">
          <button class="viewBtn" data-key="${row._autoKey}">
            <img src="/images/details.png" class="h-5 w-5 sm:h-7 sm:w-7" alt="View">
          </button>
          <button class="editBtn" data-key="${row._autoKey}">
            <img src="/images/edit.png" class="h-5 w-5 sm:h-7 sm:w-7" alt="Edit">
          </button>
          <button class="deleteBtn" data-key="${row._autoKey}">
            <img src="/images/delete.png" class="h-5 w-5 sm:h-7 sm:w-7" alt="Delete">
          </button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  }

  if (rowCount) rowCount.textContent = rows.length;
  bindRowButtons();
}

async function checkDuplicate(name, email) {
  const snap = await firebase.database().ref(FULL_BUCKET).once('value');
  const v = snap.val() || {};
  return Object.values(v).some(ff => ff.name === name && ff.email === email);
}

/* ------------ Realtime listener ------------ */
function attachBucket() {
  const ref = firebase.database().ref(FULL_BUCKET);
  ref.on('value', snap => {
    bucketData = snap.val() || {};
    applyRender();
  });
}

/* ------------ Field lock/unlock ------------ */
function setFormReadOnly(readonly, lockEmail) {
  nameEl.disabled     = readonly;
  contactEl.disabled  = readonly;
  birthdayEl.disabled = readonly;
  emailEl.disabled    = !!lockEmail; // email always locked in edit/view
}
function takeSnapshot() {
  originalSnapshot = {
    name: nameEl.value,
    contact: contactEl.value,
    birthday: birthdayEl.value
  };
}
function restoreSnapshot() {
  if (!originalSnapshot) return;
  nameEl.value     = originalSnapshot.name;
  contactEl.value  = originalSnapshot.contact;
  birthdayEl.value = originalSnapshot.birthday;
}

/* ------------ Row actions ------------ */
function bindRowButtons() {
  document.querySelectorAll('.viewBtn').forEach(btn => {
    btn.onclick = () => {
      const key = btn.dataset.key;
      firebase.database().ref(`${FULL_BUCKET}/${key}`).once('value').then(s => {
        const d = s.val() || {};
        document.getElementById('viewFirefighterId').innerText = d.id || '';
        document.getElementById('viewFirefighterName').innerText = d.name || '';
        document.getElementById('viewFirefighterEmail').innerText = d.email || '';
        document.getElementById('viewFirefighterContact').innerText = d.contact || '';
        document.getElementById('viewFirefighterBirthday').innerText = d.birthday || '';

        document.getElementById('viewFirefighterCreated').innerText =
          manilaFormatter(d.createdAtEpoch) || (d.createdAt || '—');

        document.getElementById('viewFirefighterUpdated').innerText =
          manilaFormatter(d.updatedAtEpoch) || (d.updatedAt || '—');

        viewModal.classList.remove('hidden');
      });
    };
  });

  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
      const key = btn.dataset.key;
      firebase.database().ref(`${FULL_BUCKET}/${key}`).once('value').then(s => {
        const d = s.val() || {};
        document.getElementById('modalTitle').innerText = 'Edit Fire Fighter';
        addForm.reset();

        recordKeyInput.value = key;
        recordBucketInput.value = BUCKET_KEY;

        fireStationNamePrev.value = d.fireStationName || STATION_LABEL || '';

        nameEl.value     = d.name || '';
        emailEl.value    = d.email || '';
        contactEl.value  = d.contact || '';
        birthdayEl.value = d.birthday || '';

        // snapshot for Cancel
        takeSnapshot();

        // Edit modal opens in VIEW (read-only), email permanently locked
        isEditExisting = true;
        isEditingNow   = false;
        setFormReadOnly(true, true);

        // Buttons: show Edit, hide Save/Cancel initially
        toggleEditBtn.textContent = 'Edit';
        toggleEditBtn.classList.remove('hidden');
        submitBtn.classList.add('hidden');
        cancelEditBtn.classList.add('hidden');

        addModal.classList.remove('hidden');
      });
    };
  });

  document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.onclick = () => {
      const key = btn.dataset.key;
      if (confirm('Delete this firefighter?')) {
        firebase.database().ref(`${FULL_BUCKET}/${key}`).remove();
      }
    };
  });
}

/* ------------ Toggle Edit / Cancel / Save ------------ */
toggleEditBtn.onclick = () => {
  // Switch to editing (email stays locked)
  isEditingNow = true;
  setFormReadOnly(false, true);
  toggleEditBtn.classList.add('hidden');
  submitBtn.classList.remove('hidden');      // show single Save
  cancelEditBtn.classList.remove('hidden');  // show Cancel
};

cancelEditBtn.onclick = () => {
  // Revert values and go back to read-only
  restoreSnapshot();
  isEditingNow = false;
  setFormReadOnly(true, true);
  toggleEditBtn.classList.remove('hidden');  // back to Edit
  submitBtn.classList.add('hidden');         // hide Save
  cancelEditBtn.classList.add('hidden');     // hide Cancel
};

/* ------------ Modal open/close ------------ */
addBtn.onclick = () => {
  document.getElementById('modalTitle').innerText = 'Add New Fire Fighter';
  addForm.reset();
  recordKeyInput.value = '';
  recordBucketInput.value = BUCKET_KEY;
  fireStationNamePrev.value = STATION_LABEL || '';

  // Add mode
  isEditExisting = false;
  isEditingNow   = true;

  // All fields editable in Add (email editable on create)
  setFormReadOnly(false, false);

  // Buttons: show Save; hide Edit/Cancel
  submitBtn.textContent = 'Save';
  submitBtn.classList.remove('hidden');
  toggleEditBtn.classList.add('hidden');
  cancelEditBtn.classList.add('hidden');

  addModal.classList.remove('hidden');
};

closeAddModalBtn.onclick  = () => addModal.classList.add('hidden');
closeViewModalBtn.onclick = () => viewModal.classList.add('hidden');

/* ------------ Save (Add/Update) ------------ */
addForm.onsubmit = async (e) => {
  e.preventDefault();

  // keep email locked during edit updates
  if (isEditExisting) emailEl.disabled = true;

  const editingKey   = recordKeyInput.value.trim();
  const name         = nameEl.value.trim();
  const email        = emailEl.value.trim();
  const contact      = contactEl.value.trim();
  const birthday     = birthdayEl.value;
  const fireStationName = STATION_LABEL || '';

  if (!editingKey) {
    // ADD
    if (await checkDuplicate(name, email)) {
      alert('A firefighter with the same name and email already exists.');
      return;
    }

    const ref = firebase.database().ref(FULL_BUCKET);
    const newKey = ref.push().key;
    const id = await generateSequentialId();
    const t  = nowTimestamp();

    const data = {
      id, name, email, contact, birthday,
      stationPath: STATION_KEY,
      fireStationKey: BUCKET_KEY,
      fireStationName,
      createdAt: t.text,
      createdAtEpoch: t.epoch,
      updatedAt: t.text,
      updatedAtEpoch: t.epoch
    };
    await ref.child(newKey).set(data);
    addModal.classList.add('hidden');
    return;
  }

  // UPDATE
  const path = `${FULL_BUCKET}/${editingKey}`;
  const s = await firebase.database().ref(path).once('value');
  const old = s.val() || {};

  // Email must remain the same on update
  const lockedEmail = old.email || email;

  // Name change duplicate check (email is locked)
  const changedIdentity = (old.name !== name);
  if (changedIdentity && await checkDuplicate(name, lockedEmail)) {
    alert('A firefighter with the same name and email already exists.');
    return;
  }

  const t = nowTimestamp();
  const data = {
    id: old.id || null,
    name,
    email: lockedEmail,
    contact,
    birthday,
    stationPath: STATION_KEY,
    fireStationKey: BUCKET_KEY,
    fireStationName,
    createdAt: old.createdAt || t.text,
    createdAtEpoch: old.createdAtEpoch || t.epoch,
    updatedAt: t.text,
    updatedAtEpoch: t.epoch
  };
  await firebase.database().ref(path).set(data);
  addModal.classList.add('hidden');
};

/* ------------ Bootstrap ------------ */
window.addEventListener('DOMContentLoaded', () => {
  attachBucket();
});
</script>
@endsection
