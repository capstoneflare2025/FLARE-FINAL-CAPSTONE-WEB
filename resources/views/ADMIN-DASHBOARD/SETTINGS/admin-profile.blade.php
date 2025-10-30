<!-- Admin Profile (Partial) -->
<div class="bg-white p-8 rounded-lg shadow w-full mx-auto" style="height: 650px;">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Profile</h1>

  <form id="admin-form" method="POST">
    @csrf

    <div class="space-y-6">
      <!-- Full Name -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-name" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Full Name</label>
        <input type="text" id="admin-name" name="admin_name"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Full name" required disabled>
      </div>

      <!-- Email -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-email" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Email</label>
        <input type="email" id="admin-email" name="admin_email"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Email" required disabled>
      </div>

      <!-- Phone -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-phone" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Phone Number</label>
        <input type="text" id="admin-phone" name="admin_phone"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="e.g. 0999-123-4567" required disabled>
      </div>

      <!-- Station Status -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-status" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Station Status</label>
        <select id="admin-status" name="admin_status"
                class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                disabled>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>

      <!-- Change Password -->
      <div class="text-xl font-semibold text-gray-800">Change Password</div>

      <!-- Current Password + Verify -->
      <div class="flex flex-col md:flex-row items-center pb-1">
        <label for="current-password" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Current Password</label>
        <div class="ml-4 w-full md:w-3/4 flex gap-3">
          <input type="password" id="current-password"
                 class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Enter current password" disabled>
          <button type="button" id="verify-btn"
                  class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-800 transition"
                  disabled>Verify</button>
        </div>
      </div>
      <div id="verify-msg" class="ml-0 md:ml-[25%] text-sm h-5 text-gray-500"></div>

      <!-- New/Confirm Password (hidden until verified) -->
      <div id="new-pass-block" class="hidden">
        <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
          <label for="admin-password" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">New Password</label>
          <input type="password" id="admin-password" name="admin_password"
                 class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Enter new password" disabled>
        </div>
        <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
          <label for="admin-password-confirm" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Confirm Password</label>
          <input type="password" id="admin-password-confirm" name="admin_password_confirm"
                 class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Confirm new password" disabled>
        </div>
      </div>
    </div>

    <!-- Buttons -->
    <div class="mt-6 flex justify-between">
      <button type="button" id="edit-btn"
              class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition">
        Edit
      </button>
      <button type="submit" id="save-btn"
              class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 transition hidden">
        Save Changes
      </button>
    </div>
  </form>
</div>

@push('scripts')
  <script src="{{ asset('js/ADMIN-PROFILE-BLADE/profile.js') }}"></script>
@endpush
