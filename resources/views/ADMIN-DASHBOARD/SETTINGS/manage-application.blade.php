<div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl" style="height: 650px;">
  <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Manage Report Application</h1>

  <!-- Report Type -->
  <div class="mb-4">
    <label for="report-type" class="text-lg font-semibold text-gray-700">Select Report Type</label>
    <select id="report-type" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="fire">Fire Report</option>
      <option value="other">Other Emergency Report</option>
      <option value="ems">Emergency Medical Services Report</option>
    </select>
  </div>

  <!-- Add New Option -->
  <div class="mb-6">
    <label for="new-option" class="text-lg font-semibold text-gray-700">Add New Option</label>
    <div class="flex items-center">
      <input id="new-option" type="text"
             class="mt-2 w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
             placeholder="Enter new option"/>
      <button id="add-option-btn"
              class="mt-2 ml-4 w-1/4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        Add Option
      </button>
    </div>
  </div>

  <!-- Options Table -->
  <div class="mt-6">
    <table class="min-w-full table-auto">
      <thead class="sticky top-0 bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-gray-600">#</th>
          <th class="px-4 py-2 text-left text-gray-600">Option</th>
          <th class="px-4 py-2 text-left text-gray-600">Action</th>
        </tr>
      </thead>
      <tbody id="report-options-body"></tbody>
    </table>
  </div>
</div>

@push('scripts')
  {{-- One external JS, no inline Firebase again (layout already loads SDKs) --}}
  <script src="{{ asset('js/MANAGE-APPLICATION-BLADE/delete.js') }}"></script>
@endpush
