    @extends('ADMIN-DASHBOARD.app')
    @section('page_name', 'incident-reports')
    @section('title', 'Incident Reports')

    @section('content')

        <!-- =========================================================
        = Container & Page Header
        ========================================================= -->
        <div class="container mx-auto p-6">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Incident Report Management</h1>

            <!-- =========================================================
          = Toast Notification
          ========================================================= -->
            <div id="toast" style="margin-right: 600px;"
                class="hidden fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg">
                <span id="toastMessage">Report Accepted</span>
                <button id="toastOkButton" class="ml-4 text-white underline">OK</button>
            </div>


            <!-- =========================================================
            = Modal: Assign on Receive
            ========================================================= -->
            <div id="assignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden"
                style="z-index: 60;">

                <div class="bg-white rounded-lg p-6 w-full max-w-[900px] md:max-w-[1000px] shadow-lg relative">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Assign to Fire Station</h3>

                    <p class="text-sm text-gray-600 mb-3">
                        Choose which station will receive this report.
                    </p>

                    <form id="assignForm" class="space-y-3">
                        <!-- RECEIVE: will be filled with ONLY current station's FF accounts that don't have the report -->
                        <div id="receiveSection"></div>

                        <!-- BACKUP: will be filled with OTHER stations' FF accounts that don't have the report -->
                        <div id="backupSection" class="hidden"></div>

                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" onclick="closeAssignModal()"
                                class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800">Cancel</button>
                            <button type="submit" id="assignSubmitBtn"
                                class="px-4 py-2 rounded bg-blue-600 text-white opacity-50 cursor-not-allowed"
                                disabled>Assign</button>
                        </div>
                    </form>

                    <button onclick="closeAssignModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>


            <!-- =========================================================
          = Report Type Selector (Vehicle removed; EMS added)
          ========================================================= -->
            <select id="incidentType" class="px-4 py-2 rounded bg-gray-200" onchange="toggleIncidentTables()">
                <option value="allReports" selected>All Reports</option>
                <option value="fireReports">Fire Reports</option>
                <option value="otherEmergency">Other Emergency</option>
                <option value="emsReports">Emergency Medical Services</option>
                <option value="smsReports">SMS Reports</option>
                <option value="fireFighterChatReports">Fire Fighter Chat</option>
            </select>

            <br><br>




            <!-- =========================================================
          = Section: All Reports
          ========================================================= -->
            <div id="allReportsSection" class="bg-white p-6 shadow rounded-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">All Reports</h2>

                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Type</th>
                                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left text-gray-600 col-datetime">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <!-- Filter Row -->
                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input type="text" id="allTypeSearch" placeholder="Search Type..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterAllReportsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <input type="text" id="allLocationSearch" placeholder="Search Location..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterAllReportsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="allDateTimeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="handleAllDateTimeFilterChange()">
                                        <option value="all" selected>All</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                    <input type="date" id="allDateSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterAllReportsTable()" />
                                    <input type="time" id="allTimeSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterAllReportsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="allStatusFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="filterAllReportsTable()">
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="allReportsBody"></tbody>
                    </table>
                </div>
            </div>


            <!-- =========================================================
          = Section: Fire Incident Reports (no alert level / houses affected)
          ========================================================= -->
            <div id="fireReportsSection" class="bg-white p-6 shadow rounded-lg hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Fire Incident Reports</h2>
                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto  table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Type</th>
                                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime"
                                    onclick="focusFireDatePicker()">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input type="text" id="fireTypeSearch" placeholder="Search Type..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterFireReportTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <input type="text" id="fireLocationSearch" placeholder="Search Location..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterFireReportTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="fireDateTimeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="handleDateTimeFilterChange()">
                                        <option value="all" selected>All</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                    <input type="date" id="fireDateSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterFireReportTable()" />
                                    <input type="time" id="fireTimeSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterFireReportTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="fireStatusFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="filterFireReportTable()">
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="fireReportsBody">
                            @forelse($fireReports as $index => $report)
                                @php
                                    $statusRaw = $report['status'] ?? 'Pending';
                                    $status = ucfirst(strtolower($statusRaw));
                                    $color =
                                        $status === 'Ongoing'
                                            ? 'red'
                                            : ($status === 'Completed'
                                                ? 'green'
                                                : ($status === 'Pending'
                                                    ? 'orange'
                                                    : ($status === 'Received'
                                                        ? 'blue'
                                                        : 'yellow')));
                                @endphp
                                <tr id="reportRow{{ $report['id'] }}" class="border-b"
                                    data-report='@json($report)' data-type="fireReports">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ $report['type'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }}
                                        {{ $report['reportTime'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
                                    <td class="px-4 py-2 space-x-2 flex items-center">
                                        <a href="javascript:void(0);" class="msg-btn"
                                            data-key="fireReports|{{ $report['id'] }}"
                                            onclick="openMessageModal('{{ $report['id'] }}', 'fireReports')">
                                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                            <span
                                                class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
                                        </a>

                                        @if (isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                                            <a href="javascript:void(0);"
                                                onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                                                <img src="{{ asset('images/location.png') }}" alt="Location"
                                                    class="w-6 h-6">
                                            </a>
                                        @endif
                                        <a href="javascript:void(0);"
                                            onclick="openDetailsModal('{{ $report['id'] }}', 'fireReports')">
                                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr id="noFireReportsRow">
                                    <td colspan="6" class="text-center text-gray-500 py-4">
                                        No reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- =========================================================
          = Spinner (Other Emergency Loading)
          ========================================================= -->
            <div id="spinner" class="hidden flex justify-center items-center mt-6">
                <div
                    class="spinner-border animate-spin inline-block w-12 h-12 border-4 border-solid border-gray-200 border-t-gray-600 rounded-full">
                </div>
            </div>

            <!-- =========================================================
          = Section: Other Emergency Incidents
          ========================================================= -->
            <div id="otherEmergencySection" class="bg-white p-6 shadow rounded-lg hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Other Emergency Incidents</h2>
                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto  table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left text-gray-600">Emergency Type</th>
                                <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime"
                                    onclick="focusOtherDatePicker()">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input type="text" id="otherLocationSearch" placeholder="Search Location..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterOtherEmergencyTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="emergencyTypeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="filterOtherEmergencyTable()">
                                        <option value="">All</option>
                                        <option value="Gas Leak">Gas Leak</option>
                                        <option value="Flooding">Flooding</option>
                                        <option value="Fallen Tree">Fallen Tree</option>
                                        <option value="Building Collapse">Building Collapse</option>
                                    </select>
                                </th>
                                <th class="px-4 py-2">
                                    <select id="otherDateTimeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="handleOtherDateTimeFilterChange()">
                                        <option value="all" selected>All</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                    <input type="date" id="otherDateSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterOtherEmergencyTable()" />
                                    <input type="time" id="otherTimeSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterOtherEmergencyTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="otherStatusFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="filterOtherEmergencyTable()">
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="otherEmergencyTableBody">
                            @forelse($otherEmergencyReports as $index => $report)
                                @php
                                    $statusRaw = $report['status'] ?? 'Pending';
                                    $status = ucfirst(strtolower($statusRaw));
                                    $color =
                                        $status === 'Ongoing'
                                            ? 'red'
                                            : ($status === 'Completed'
                                                ? 'green'
                                                : ($status === 'Pending'
                                                    ? 'orange'
                                                    : ($status === 'Received'
                                                        ? 'blue'
                                                        : 'yellow')));
                                @endphp
                                <tr id="reportRow{{ $report['id'] }}" class="border-b"
                                    data-report='@json($report)' data-type="otherEmergency">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['emergencyType'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }}
                                        {{ $report['reportTime'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
                                    <td class="px-4 py-2 flex space-x-4 items-center">
                                        <a href="javascript:void(0);" class="msg-btn"
                                            data-key="otherEmergency|{{ $report['id'] }}"
                                            onclick="openMessageModal('{{ $report['id'] }}', 'otherEmergency')">
                                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                            <span
                                                class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
                                        </a>

                                        @if (isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                                            <a href="javascript:void(0);"
                                                onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                                                <img src="{{ asset('images/location.png') }}" alt="Location"
                                                    class="w-6 h-6">
                                            </a>
                                        @endif

                                        <a href="javascript:void(0);"
                                            onclick="openDetailsModal('{{ $report['id'] }}', 'otherEmergency')">
                                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr id="noFireReportsRow">
                                    <td colspan="6" class="text-center text-gray-500 py-4">
                                        No reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- =========================================================
          = Section: Emergency Medical Services (EMS)
          ========================================================= -->
            <div id="emsSection" class="bg-white p-6 shadow rounded-lg hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Emergency Medical Services</h2>
                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Type</th>
                                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime"
                                    onclick="focusEmsDatePicker()">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input type="text" id="emsTypeSearch" placeholder="Search Type..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterEmsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <input type="text" id="emsLocationSearch" placeholder="Search Location..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterEmsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="emsDateTimeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="handleEmsDateTimeFilterChange()">
                                        <option value="all" selected>All</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                    <input type="date" id="emsDateSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterEmsTable()" />
                                    <input type="time" id="emsTimeSearch"
                                        class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                        onchange="filterEmsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="emsStatusFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        onchange="filterEmsTable()">
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="emsBody">
                            @foreach ($emsReports ?? [] as $index => $report)
                                @php
                                    $statusRaw = $report['status'] ?? 'Pending';
                                    $status = ucfirst(strtolower($statusRaw));
                                    $color =
                                        $status === 'Ongoing'
                                            ? 'red'
                                            : ($status === 'Completed'
                                                ? 'green'
                                                : ($status === 'Pending'
                                                    ? 'orange'
                                                    : ($status === 'Received'
                                                        ? 'blue'
                                                        : 'yellow')));
                                @endphp
                                <tr id="reportRow{{ $report['id'] }}" class="border-b"
                                    data-report='@json($report)' data-type="emsReports">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ $report['type'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }}
                                        {{ $report['reportTime'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
                                    <td class="px-4 py-2 space-x-2 flex items-center">
                                        <a href="javascript:void(0);" class="msg-btn"
                                            data-key="emsReports|{{ $report['id'] }}"
                                            onclick="openMessageModal('{{ $report['id'] }}', 'emsReports')">
                                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                            <span
                                                class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
                                        </a>

                                        @if (isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                                            <a href="javascript:void(0);"
                                                onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                                                <img src="{{ asset('images/location.png') }}" alt="Location"
                                                    class="w-6 h-6">
                                            </a>
                                        @endif
                                        <a href="javascript:void(0);"
                                            onclick="openDetailsModal('{{ $report['id'] }}', 'emsReports')">
                                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- =========================================================
          = Section: SMS Reports
          ========================================================= -->
            <div id="smsReportsSection" class="bg-white p-6 shadow rounded-lg hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">SMS Reports</h2>
                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left text-gray-600 col-datetime">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input id="smsLocationSearch" type="text" placeholder="Search location..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        oninput="filterSmsReportsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="smsDateTimeFilter"
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        onchange="handleSmsDateTimeFilterChange()">
                                        <option value="all" selected>All</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                    <input id="smsDateSearch" type="date"
                                        class="hidden w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        onchange="filterSmsReportsTable()" />
                                    <input id="smsTimeSearch" type="time"
                                        class="hidden w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        onchange="filterSmsReportsTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <select id="smsStatusFilter"
                                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        onchange="filterSmsReportsTable()">
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="smsReportsBody">
                            @forelse(($smsReports ?? []) as $index => $report)
                                @php
                                    $lat = $report['latitude'] ?? null;
                                    $lng = $report['longitude'] ?? null;
                                    $statusRaw = $report['status'] ?? 'Pending';
                                    $status = ucfirst(strtolower($statusRaw));
                                    $color =
                                        $status === 'Ongoing'
                                            ? 'red'
                                            : ($status === 'Completed'
                                                ? 'green'
                                                : ($status === 'Pending'
                                                    ? 'orange'
                                                    : ($status === 'Received'
                                                        ? 'blue'
                                                        : 'yellow')));
                                @endphp
                                <tr id="reportRow{{ $report['id'] }}" class="border-b"
                                    data-report='@json($report)' data-type="smsReports">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 break-anywhere">{{ $report['location'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['time'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
                                    <td class="px-4 py-2 space-x-2 flex items-center">
                                        <a href="javascript:void(0);" class="msg-btn"
                                            data-key="smsReports|{{ $report['id'] }}"
                                            onclick="openMessageModal('{{ $report['id'] }}', 'smsReports')">
                                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                            <span
                                                class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
                                        </a>

                                        @if (!is_null($lat) && !is_null($lng))
                                            <a href="javascript:void(0);"
                                                onclick="openLocationModal({{ $lat }}, {{ $lng }})">
                                                <img src="{{ asset('images/location.png') }}" alt="Location"
                                                    class="w-6 h-6">
                                            </a>
                                        @endif
                                        <a href="javascript:void(0);"
                                            onclick="openDetailsModal('{{ $report['id'] }}', 'smsReports')">
                                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                                        </a>
                                    </td>

                                </tr>

                            @empty
                                <tr id="noFireReportsRow">
                                    <td colspan="6" class="text-center text-gray-500 py-4">
                                        No reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- =========================================================
          = Section: Fire Fighter Chat Table (Name, Contact, Action)
          ========================================================= -->
            <div id="fireFighterChatSection" class="bg-white p-6 shadow rounded-lg hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Fire Fighter Chat Reports</h2>
                <div class="table-wrap max-h-96 overflow-y-auto">
                    <table class="min-w-full table-auto table-min">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-gray-600">#</th>
                                <th class="px-4 py-2 text-left text-gray-600">Name</th>
                                <th class="px-4 py-2 text-left text-gray-600">Contact</th>
                                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                            </tr>

                            <tr class="bg-gray-50 text-sm">
                                <th></th>
                                <th class="px-4 py-2">
                                    <input id="fireFighterChatNameSearch" type="text" placeholder="Search Name..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterFireFighterChatTable()" />
                                </th>
                                <th class="px-4 py-2">
                                    <input id="fireFighterChatContactSearch" type="text"
                                        placeholder="Search Contact..."
                                        class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                        oninput="filterFireFighterChatTable()" />
                                </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="fireFighterChatBody">
                            @foreach ($fireFighterChatReports ?? [] as $index => $report)
                                @php
                                    $lat = $report['latitude'] ?? null;
                                    $lng = $report['longitude'] ?? null;
                                @endphp
                                <tr id="reportRow{{ $report['id'] }}" class="border-b"
                                    data-report='@json($report)' data-type="fireFighterChatReports">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ $report['name'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $report['contact'] ?? 'N/A' }}</td>

                                    <!-- Action: Message, Location (if coords), Details -->
                                    <td class="px-4 py-2 space-x-3 flex items-center">
                                        <!-- Message -->
                                        <a href="javascript:void(0);" class="msg-btn inline-flex items-center"
                                            title="Open messages" aria-label="Open messages"
                                            data-ff-account="{{ $report['accountKey'] }}"
                                            onclick="openFFChatMessageModal('{{ $report['accountKey'] }}')">
                                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                            <span>1</span>
                                        </a>


                                        <!-- Location (only if lat/lng exist) -->
                                        @if (!is_null($lat) && !is_null($lng))
                                            <a href="javascript:void(0);" class="inline-flex items-center"
                                                title="Open location" aria-label="Open location"
                                                onclick="openLocationModal({{ $lat }}, {{ $lng }})">
                                                <img src="{{ asset('images/location.png') }}" alt="Location"
                                                    class="w-6 h-6">
                                            </a>
                                        @endif

                                        <!-- Details -->
                                        <a href="javascript:void(0);" class="inline-flex items-center"
                                            title="View details" aria-label="View details"
                                            onclick="openDetailsModal('{{ $report['id'] }}', 'fireFighterChatReports')">
                                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===========================
             FF CHAT: MESSAGE MODAL
        =========================== -->
            <div id="ffChatMessageModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg relative  modal-panel">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">
                        <span id="ffChatMsgStationName" class="text-blue-700"></span>
                    </h3>
                    <!-- Chat message thread with fixed height and scrollable content -->
                    <div id="ffChatMsgThread"
                        class="h-64 overflow-y-auto border border-gray-200 p-3 rounded mb-4 bg-gray-50"></div>
                    <form id="ffChatMsgForm" class="flex gap-2">
                        <input type="hidden" id="ffChatMsgStationKey">
                        <input id="ffChatMsgInput" type="text"
                            class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Type a message..." required>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                            type="submit">Send</button>
                    </form>
                    <button onclick="closeFFChatMessageModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>


            <!-- ===========================
             FF CHAT: LOCATION MODAL
        =========================== -->
            <div id="ffChatLocationModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Station Location: <span id="ffChatLocStationName" class="text-blue-700"></span>
                    </h3>
                    <div id="ffChatLocationInfo" class="text-gray-700 mb-4"></div>
                    <div id="ffChatLocationMap"
                        class="w-full h-80 rounded border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center">
                        <span class="text-gray-500 text-sm">No map to show.</span>
                    </div>
                    <button onclick="closeFFChatLocationModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>




            <!-- ===========================
             FF CHAT: DETAILS MODAL
        =========================== -->
            <div id="ffChatDetailsModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
                <div
                    class="bg-white rounded-lg p-6 w-full max-w-6xl min-w-[700px] max-h-[80vh] overflow-auto shadow-lg relative">
                    <!-- Flex Container for Header and Report Summary -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Fire Fighter Details</h3>

                        <!-- Report Summary Section -->
                        <div class="ml-6 flex items-center space-x-4">
                            <h4 class="font-semibold text-gray-800 mb-0">Report Summary</h4>

                            <!-- Dropdown Section -->
                            <div class="text-center" style="margin-right: 80px;">
                                <select id="fighterReportType" class="p-2 border rounded-md text-gray-700">
                                    <option value="AllReport" selected>All Reports</option> <!-- Default option -->
                                    <option value="FireReport">Fire Report</option>
                                    <option value="OtherEmergencyReport">Other Emergency Report</option>
                                    <option value="EmergencyMedicalServicesReport">Emergency Medical Services Report
                                    </option>
                                    <option value="SmsReport">SMS Report</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Flex Container for Left and Right Sections -->
                    <div class="flex space-x-6">
                        <!-- Left Side: Fire Fighter Details (Card with Outline) -->
                        <div class="w-1/2 min-w-[250px] border p-4 rounded-lg border-gray-300 space-y-6 text-gray-700">
                            <div>
                                <strong>Name:</strong>
                                <span id="ffChatDetName" class="whitespace-nowrap">Tagum City West Fire Sub-Station Fire
                                    Fighter oh</span>
                            </div>
                            <div>
                                <strong>Contact:</strong> <span id="ffChatDetContact"></span>
                            </div>
                            <div>
                                <strong>Email:</strong> <span id="ffChatDetEmail"></span>
                            </div>
                            <div id="ffChatDetExtra" class="mt-4 text-sm text-gray-600"></div>
                        </div>

                        <!-- Right Side: Report Summary and Report Table (Card with Outline) -->
                        <div class="flex-1 border p-4 rounded-lg border-gray-300 space-y-6">
                            <!-- Report Table Section -->
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">All Reports</h4>
                                <div id="ffChatDetAllReports" class="overflow-auto max-h-60">
                                    <table class="w-full table-auto text-sm">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-left p-3">#</th>
                                                <th class="text-left p-3">Type</th>
                                                <th class="text-left p-3">Status</th>
                                                <th class="text-left p-3">Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody id="allFighterReportsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (Message and Close) -->
                    <div class="mt-5 flex justify-end gap-2">
                        <button class="px-4 py-2 rounded bg-blue-600 text-white"
                            onclick="openFFChatMessageModal()">Message</button>
                        <button class="px-4 py-2 rounded bg-gray-200" onclick="closeFFChatDetailsModal()">Close</button>
                    </div>

                    <button onclick="closeFFChatDetailsModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>

            <!-- =========================================================
          = Modal: Fire Message
          ========================================================= -->
            <div id="fireMessageModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-6xl shadow-lg relative  modal-panel">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800" id="fireMessageNameValue"></h2>

                    <div class="mb-4">
                        <p><strong>Incident ID:</strong> <span id="fireMessageIncidentIdValue"></span></p>
                        <p><strong>Contact:</strong> <span id="fireMessageContactValue"></span></p>
                    </div>

                    <div id="fireMessageThread"
                        class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth"
                        style="height: 500px;"></div>

                    <form id="fireMessageForm" class="flex gap-2">
                        <input type="hidden" id="fireMessageIncidentInput">
                        <input type="text" id="fireMessageInput"
                            class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Type a message..." required>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
                    </form>
                    <button onclick="closeFireMessageModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-5xl leading-none">&times;</button>
                </div>
            </div>

            <!-- =========================================================
          = Modal: Other Emergency Message
          ========================================================= -->
            <div id="otherEmergencyMessageModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative  modal-panel">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Other Emergency Incident Chat
                        <span id="otherEmergencyMessageIncidentId" class="text-sm text-gray-500"></span>
                    </h3>

                    <div id="otherEmergencyMessageThread"
                        class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth">
                    </div>

                    <form id="otherEmergencyMessageForm" class="flex gap-2">
                        <input type="hidden" id="otherEmergencyMessageIncidentInput">
                        <input type="text" id="otherEmergencyMessageInput"
                            class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Type a message...">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
                    </form>
                    <button onclick="closeOtherEmergencyMessageModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>

            <!-- =========================================================
          = Modal: EMS Message
          ========================================================= -->
            <div id="emsMessageModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative  modal-panel">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">EMS Incident Chat
                        <span id="emsMessageIncidentId" class="text-sm text-gray-500"></span>
                    </h3>

                    <div id="emsMessageThread"
                        class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth">
                    </div>

                    <form id="emsMessageForm" class="flex gap-2">
                        <input type="hidden" id="emsMessageIncidentInput">
                        <input type="text" id="emsMessageInput"
                            class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Type a message...">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
                    </form>
                    <button onclick="closeEmsMessageModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>

            <!-- =========================================================
          = Modal: Location (Route & Geofence Maps)
          ========================================================= -->
            <div id="locationModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full shadow-lg relative modal-shell"
                    style="max-width: 1100px; width: calc(100% - 280px); margin-left: 350px;">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Report Location</h3>

                    <div class="flex gap-4">
                        <div style="flex:1; display:flex; flex-direction:column;">
                            <div id="routeMap" style="flex:1; height: 470px;"></div>
                            <div id="routeInfo" class="mt-2 text-sm text-gray-700"
                                style="min-height: 40px; max-height:120px; overflow-y:auto;">
                                <em>Finding best routes…</em>
                            </div>
                        </div>

                        <div id="fenceMap" style="flex:1; height: 470px;"></div>
                    </div>

                    <button onclick="closeLocationModal()"
                        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">
                        &times;
                    </button>
                </div>
            </div>


            <!-- =========================================================
        = Modal: Details (Fire / Other / EMS / SMS)
        ========================================================= -->
            <div id="detailsModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-4xl overflow-y-auto shadow-lg relative"
                    style="width:95vw; max-height:90vh; overflow-x:hidden;">


                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Incident Report Details</h3>

                    <div class="space-y-6">

                        <!-- ============================
                   Fire Report Details
              ============================= -->
                        <div id="fireReportDetails" class="hidden">
                            <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
                                <!-- LEFT: Text -->
                                <div class="flex-1 space-y-2">

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
                                        <span id="detailName" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
                                        <span id="detailContact" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Fire Type:</strong>
                                        <span id="detailFireType" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
                                        <span id="detailLocation" class="text-gray-600 flex-1 truncate"
                                            title=""></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
                                        <span id="detailDate" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
                                        <span id="detailReportTime" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
                                        <span id="detailStatus" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                </div>

                                <!-- RIGHT: Photo -->
                                <div class="flex justify-center items-start mt-4 md:mt-0 md:w-105 w-full">
                                    <div id="detailFirePhoto" class="w-full overflow-hidden rounded-lg shadow"
                                        style="max-height:500px;">
                                        <img id="detailFirePhotoImg" src="" alt="Fire Photo"
                                            class="w-full h-auto object-contain rounded-lg" style="display:block;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================
                   Other Emergency Details
                   (matches Fire)
              ============================= -->
                        <div id="otherEmergencyDetails" class="hidden">
                            <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
                                <!-- LEFT: Text -->
                                <div class="flex-1 space-y-2">

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
                                        <span id="detailNameOther" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
                                        <span id="detailContactOther" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Emergency Type:</strong>
                                        <span id="detailEmergencyType" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <!-- Exact Location single line -->
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
                                        <span id="detailLocationOther" class="text-gray-600 flex-1 truncate"
                                            title=""></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
                                        <span id="detailDateOther" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
                                        <span id="detailReportTimeOther" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
                                        <span id="detailStatusOther" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                </div>

                                <!-- RIGHT: Photo (same size as Fire) -->
                                <div class="flex justify-center items-start mt-4 md:mt-0 md:w-105 w-full">
                                    <div id="detailOtherPhoto" class="w-full overflow-hidden rounded-lg shadow"
                                        style="max-height:500px;">
                                        <img id="detailOtherPhotoImg" src="" alt="Incident Photo"
                                            class="w-full h-auto object-contain rounded-lg" style="display:block;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================
                   EMS Report Details
                   (matches Fire)
              ============================= -->
                        <div id="emsDetails" class="hidden">
                            <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
                                <!-- LEFT: Text -->
                                <div class="flex-1 space-y-2">

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
                                        <span id="detailNameEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
                                        <span id="detailContactEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">EMS Type:</strong>
                                        <span id="detailTypeEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <!-- Exact Location single line -->
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
                                        <span id="detailLocationEms" class="text-gray-600 flex-1 truncate"
                                            title=""></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
                                        <span id="detailDateEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
                                        <span id="detailReportTimeEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
                                        <span id="detailStatusEms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                </div>

                                <!-- RIGHT: Photo (same size as Fire) -->
                                <div class="flex justify-center items-start mt-4 md:mt-0 md:w-105 w-full">
                                    <div id="detailEmsPhoto" class="w-full overflow-hidden rounded-lg shadow"
                                        style="max-height:500px;">
                                        <img id="detailEmsPhotoImg" src="" alt="EMS Photo"
                                            class="w-full h-auto object-contain rounded-lg" style="display:block;">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- ============================
                SMS Report Details
            ============================= -->
                        <div id="smsDetails" class="hidden">
                            <div class="flex flex-col md:flex-row md:items-start md:space-x-8">

                                <!-- LEFT: Text -->
                                <div class="flex-1 space-y-2">

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
                                        <span id="detailNameSms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
                                        <span id="detailContactSms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <!-- Report text / message -->
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Report Text:</strong>
                                        <span id="detailSmsReportText" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
                                        <span id="detailDateSms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
                                        <span id="detailReportTimeSms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
                                        <span id="detailStatusSms" class="text-gray-600 flex-1 break-words"></span>
                                    </div>
                                    <br>

                                    <!-- Exact Location single line (string location from DB) -->
                                    <div class="flex">
                                        <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
                                        <span id="detailLocationSms" class="text-gray-600 flex-1 truncate"
                                            title=""></span>
                                    </div>
                                    <br>


                                    <br>

                                </div>
                            </div>
                        </div>


                        <!-- ============================
                   SMS Extra Panel (unchanged)
              ============================= -->
                        <div id="smsExtra" class="space-y-4 hidden mt-6">
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Nearest Station:</strong>
                                <span id="detailSmsStation" class="text-gray-600"></span>
                            </div>
                            <div>
                                <strong class="text-gray-700 block mb-1">Report Details:</strong>
                                <p id="detailSmsReportText" class="text-gray-600"></p>
                            </div>
                        </div>


                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end mt-6 space-x-4">
                        <button onclick="closeDetailsModal()"
                            style="background-color: #E00024; height:45px; width:90px; margin-top:7px;"
                            class="px-6 py-2 text-white rounded-md hover:bg-gray-600">Close</button>
                        <div id="statusActionButtons" class="flex gap-2"></div>
                    </div>

                    <button onclick="closeDetailsModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>

        </div>


        <script>
            /* =========================================================
             * LIFECYCLE / ENTRYPOINTS
             * ========================================================= */

            document.addEventListener("DOMContentLoaded", () => {
                // Render immediately so All Reports isn't empty on first paint
                safeRenderAllReports();
                handleUrlParams();
                safeInitRealtime();

                toggleIncidentTables();
                normalizeInitialTimes();
                renderAllReports();
                safeInitFireFighterAccounts();
                hardLoadFF();
                // Delay a bit to ensure DOM and reports fully rendered
                setTimeout(() => {
                    ensureMessageBadges();
                }, 1000); // wait 1 second

            });



            /* =========================================================
             * FIRE FIGHTER CHAT — PER-STATION (matches your latest RTDB)
             * Node shape used everywhere:
             *   <root>/FireFighter/FireFighterAccount
             *     ├─ AdminMessages
             *     ├─ AllReport/<FireReport|OtherEmergencyReport|EmergencyMedicalServicesReport|SmsReport>/<id>
             *     └─ LiveLocation { latitude, longitude, updatedAt, ... }
             * ========================================================= */

            window.CURRENT_STATION_ROOT = @json(session('station')); // e.g. "CapstoneFlare/CanocotanFireStation"
            window.CURRENT_STATION_LABEL = @json(session('station_label')); // e.g. "Canocotan Fire Station"
            window.AUTH_EMAIL = @json(session('firebase_user_email'));

            /* -------- Constant paths -------- */
            const STATION_ROOT = window.CURRENT_STATION_ROOT;
            const FF_ACCOUNT_PATH = `${STATION_ROOT}/FireFighter/FireFighterAccount`; // <-- single account node
            const HQ_PROFILE_PATH = `${STATION_ROOT}/Profile`;
            const ADMIN_MESSAGES = `${FF_ACCOUNT_PATH}/AdminMessages`;
            const LIVE_LOCATION_PATH = `${FF_ACCOUNT_PATH}/LiveLocation`;

            const REPORT_TYPES = [
                'FireReport',
                'OtherEmergencyReport',
                'EmergencyMedicalServicesReport',
                'SmsReport'
            ];

            /* -------- DOM helpers -------- */
            const _$ = id => document.getElementById(id);
            const _show = id => _$(id)?.classList.remove('hidden');
            const _hide = id => _$(id)?.classList.add('hidden');
            const _safe = (v, d = 'N/A') => (v == null || String(v).trim() === '') ? d : String(v);

            /* =========================================================
             * HQ coords
             * ========================================================= */
            async function getHQCoords() {
                const s = await firebase.database().ref(HQ_PROFILE_PATH).once('value');
                const p = s.val() || {};
                const lat = parseFloat(p.latitude);
                const lng = parseFloat(p.longitude);
                return (Number.isFinite(lat) && Number.isFinite(lng)) ? {
                    lat,
                    lng
                } : null;
            }

            /* =========================================================
             * TABLE — single row for this station’s FireFighterAccount
             * ========================================================= */
            function loadFireFighterAccountRow() {
                const body = _$('fireFighterChatBody');
                if (!body) return;

                if (!firebase?.apps?.length || typeof firebase.database !== 'function') {
                    setTimeout(loadFireFighterAccountRow, 400);
                    return;
                }

                firebase.database().ref(FF_ACCOUNT_PATH).once('value')
                    .then(snap => {
                        if (!snap.exists()) {
                            body.innerHTML = `
          <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">
            Firefighter account not found for this station.
          </td></tr>`;
                            return;
                        }
                        const v = snap.val() || {};
                        const name = _safe(v.name, 'FireFighterAccount');
                        const contact = _safe(v.contact);

                        body.innerHTML = `
        <tr class="border-b fire-fighter-row">
          <td class="px-4 py-2">1</td>
          <td class="px-4 py-2 name-cell">${name}</td>
          <td class="px-4 py-2 contact-cell">${contact}</td>
          <td class="px-4 py-2 space-x-3 flex items-center">
            <!-- Message -->
            <a href="javascript:void(0);" title="Message"
               class="inline-flex items-center msg-btn"
               onclick="openFFChatMessageModal()">
              <img src="/images/message.png" alt="Message" class="w-6 h-6">
              <span class="msg-badge hidden">0</span>
            </a>
            <!-- Location -->
            <a href="javascript:void(0);" title="Location"
               class="inline-flex items-center"
               onclick="openFFChatLocationModal()">
              <img src="/images/location.png" alt="Location" class="w-6 h-6">
            </a>
            <!-- Details -->
            <a href="javascript:void(0);" title="Details"
               class="inline-flex items-center"
               onclick="openFFChatDetailsModal()">
              <img src="/images/details.png" alt="Details" class="w-6 h-6">
            </a>
          </td>
        </tr>`;

                        initUnreadBadge(); // live unread count
                        document.addEventListener('DOMContentLoaded', initUnreadBadge);
                    })
                    .catch(err => {
                        console.error('[FF] Read error:', err);
                        body.innerHTML = `
        <tr><td colspan="4" class="px-4 py-3 text-center text-red-600">
          ${err?.message || 'Error reading data.'}
        </td></tr>`;
                    });
            }

            /* Live unread badge (firefighter→admin messages: isRead === true && sender != 'admin') */
            function initUnreadBadge() {
                const badge = document.querySelector('#fireFighterChatBody .msg-badge');
                if (!badge) return;

                firebase.database()
                    .ref(ADMIN_MESSAGES)
                    .orderByChild('isRead')
                    .equalTo(true)
                    .on('value', snap => {
                        let count = 0;
                        snap.forEach(c => {
                            const m = c.val() || {};
                            const sender = String(m.sender || '').toLowerCase();
                            if (sender !== 'admin') count++;
                        });
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : String(count);
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    });
            }



            const REPORT_LABELS = {
                FireReport: 'Fire Report',
                OtherEmergencyReport: 'Other Emergency Report',
                EmergencyMedicalServicesReport: 'Emergency Medical Services Report',
                SmsReport: 'SMS Report'
            };


            // Normalize free-form statuses to buckets we care about
            function normalizeStatusBucket(s = '') {
                const x = String(s).trim().toLowerCase().replace(/\s|_/g, '');
                if (/(on)?going|inprogress|active|responding/.test(x)) return 'ongoing';
                if (/pending|new|queued|open|received|reported|unassigned/.test(x)) return 'pending';
                if (/done|closed|resolved|complete|completed|finished/.test(x)) return 'completed';
                return 'other';
            }

            // Helper function to set text content in an element by its ID
            function setText(id, txt) {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = txt ?? ''; // Set text or clear it if txt is undefined
                }
            }

            // Function to get the Firebase path for the currently selected station
            function getStationPath() {
                const station = window.CURRENT_STATION_ROOT; // Dynamically get the station path
                console.log(`Fetching data from station path: ${station}/FireFighter/FireFighterAccount`);
                return `${station}/FireFighter/FireFighterAccount`; // Full path to firefighter account
            }

            document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to the dropdown after the DOM is fully loaded
    document.getElementById('fighterReportType').addEventListener('change', renderReports);
});


           async function getReportsForFirefighter() {
    const base = `${FF_ACCOUNT_PATH}/AllReport`; // Firebase path for fetching reports
    const db = firebase.database();
    const allReports = [];
    const reportType = document.getElementById('fighterReportType').value; // Get selected report type from dropdown

    console.log(`Fetching reports for type: ${reportType}`);
    console.log(`Firebase path: ${base}`);

    // If "All Reports" is selected, fetch all report types
    if (reportType === 'AllReport') {
        const reportTypes = ['FireReport', 'OtherEmergencyReport', 'EmergencyMedicalServicesReport', 'SmsReport'];
        for (const type of reportTypes) {
            try {
                const snap = await db.ref(`${base}/${type}`).once('value');
                console.log(`Fetching data for report type: ${type}`);
                if (snap.exists()) {
                    snap.forEach(c => {
                        const report = c.val();
                        const timestamp = new Date(Number(report.timestamp) || 0).toLocaleString(); // Convert timestamp to readable format
                        const status = normalizeStatusBucket(report.status); // Normalize status

                        // Push the report data into the array
                        allReports.push({
                            id: c.key,
                            type: type, // The report type
                            status: status,
                            timestamp: timestamp
                        });
                    });
                } else {
                    console.warn(`No reports found for type: ${type}`);
                }
            } catch (e) {
                console.warn(`[Details] Read failed for report type: ${reportType}`, e);
            }
        }
    } else {
        // If a specific report type is selected, fetch only that type
        try {
            const snap = await db.ref(`${base}/${reportType}`).once('value');
            console.log(`Fetching data for report type: ${reportType}`);
            if (snap.exists()) {
                snap.forEach(c => {
                    const report = c.val();
                    const timestamp = new Date(Number(report.timestamp) || 0).toLocaleString(); // Convert timestamp to readable format
                    const status = normalizeStatusBucket(report.status); // Normalize status

                    // Push the report data into the array
                    allReports.push({
                        id: c.key,
                        type: reportType, // The report type
                        status: status,
                        timestamp: timestamp
                    });
                });
            } else {
                console.warn(`No reports found for type: ${reportType}`);
            }
        } catch (e) {
            console.warn(`[Details] Read failed for report type: ${reportType}`, e);
        }
    }

    return allReports;
}

async function renderReports() {
    const reports = await getReportsForFirefighter(); // Fetch reports based on the selected type
    console.log('Fetched reports:', reports);

    const allReportsBody = document.getElementById('allFighterReportsBody');
    allReportsBody.innerHTML = ''; // Clear table before adding rows

    // Handle empty case
    if (!reports || reports.length === 0) {
        allReportsBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                    No reports available.
                </td>
            </tr>`;
        return;
    }

    // Loop and render rows
    reports.forEach((report, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="p-3 border">${index + 1}</td>
            <td class="p-3 border">${report.type}</td>
            <td class="p-3 border">${report.status}</td>
            <td class="p-3 border">${report.timestamp}</td>
        `;
        allReportsBody.appendChild(row);
    });
}


            // Function to open the modal and load the firefighter details and reports
            async function openFFChatDetailsModal() {
                try {
                    if (!firebase?.apps?.length || typeof firebase.database !== 'function') {
                        throw new Error('Firebase not initialized yet.');
                    }

                    const STATION_ROOT = window.CURRENT_STATION_ROOT;
                    if (!STATION_ROOT || typeof STATION_ROOT !== 'string') {
                        console.warn('[Details] Missing CURRENT_STATION_ROOT:', STATION_ROOT);
                        alert('Station context is missing. Please pick a station first.');
                        return;
                    }
                    const FF_ACCOUNT_PATH = getStationPath();

                    let accVal = {};
                    try {
                        const accSnap = await firebase.database().ref(FF_ACCOUNT_PATH).once('value');
                        accVal = accSnap.val() || {};
                    } catch (e) {
                        console.warn('[Details] FF account read failed', e);
                        alert('Could not read Firefighter account for this station.');
                        return;
                    }

                    setText('ffChatDetName', accVal.name || '');
                    setText('ffChatDetContact', accVal.contact || '');
                    setText('ffChatDetEmail', accVal.email || '');
                    if (document.getElementById('ffChatDetExtra')) document.getElementById('ffChatDetExtra').textContent =
                        accVal.notes || '';

                    // Render the reports in the table
                    await renderReports();

                    _show('ffChatDetailsModal');
                } catch (e) {
                    console.error('[FF Chat][Details] load failed', e);
                    alert(e?.message || 'Could not load station details.');
                }
            }

            function closeFFChatDetailsModal() {
                _hide('ffChatDetailsModal');
            }


            /* =========================
             * Messaging (single renderer)
             * ========================= */
            let __threadRef = null;
            const _el = id => document.getElementById(id);
            // const _safe = (v, d='N/A') => (v==null || String(v).trim()==='') ? d : String(v);

            /* One renderer with date headers (your original, cleaned a hair) */
            function renderThread(items) {
                const box = _el('ffChatMsgThread');
                if (!box) return;

                const esc = (s = '') => s.replace(/[&<>"']/g, c => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [c]));
                const pad = n => String(n).padStart(2, '0');
                const MONTHS = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
                const fmtFull = ms => {
                    const d = new Date(ms);
                    return `${MONTHS[d.getMonth()]} ${pad(d.getDate())} ${d.getFullYear()} - ${pad(d.getHours())}:${pad(d.getMinutes())}`;
                };
                const fmtTime = ms => {
                    const d = new Date(ms);
                    return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
                };
                const SIX = 6 * 60 * 60 * 1000;
                const newDay = (a, b) => {
                    const A = new Date(a),
                        B = new Date(b);
                    return A.toDateString() !== B.toDateString();
                };

                const body = m => {
                    if ((m.text || '').trim()) return `<div class="whitespace-pre-wrap break-words">${esc(m.text)}</div>`;
                    if (m.imageBase64) return `<a href="data:image/jpeg;base64,${m.imageBase64}" target="_blank" rel="noopener">
                                        <img src="data:image/jpeg;base64,${m.imageBase64}"
                                             style="display:block;width:260px;max-width:100%;height:auto;max-height:700px;object-fit:cover;border-radius:8px"/>
                                      </a>`;
                    if (m.audioBase64)
                        return `<audio controls preload="metadata" src="data:audio/mp4;base64,${m.audioBase64}" class="h-9 w-[220px]"></audio>`;
                    return `<em class="opacity-70">Unsupported/empty message</em>`;
                };

                let lastHeader = null;
                const html = (items || [])
                    .sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0))
                    .map(m => {
                        const ts = m.timestamp || Date.now();
                        const header = (!lastHeader || (ts - lastHeader) >= SIX || newDay(ts, lastHeader)) ?
                            `<div class="text-center my-3"><span class="inline-block text-xs px-3 py-1 rounded-full bg-gray-200 text-gray-700">${fmtFull(ts)}</span></div>` :
                            '';
                        if (header) lastHeader = ts;

                        const fromAdmin = String(m.sender || '').toLowerCase() === 'admin';
                        return `${header}
        <div class="mb-2 ${fromAdmin ? 'text-right' : ''}">
          <div class="inline-block px-3 py-2 rounded ${fromAdmin ? 'bg-blue-600 text-white' : 'bg-gray-200'}" style="max-width:75%;">${body(m)}</div>
          <div class="text-xs text-gray-500 mt-1">${fmtTime(ts)}</div>
        </div>`;
                    })
                    .join('');

                box.innerHTML = html || `<em class="text-gray-500">No messages yet.</em>`;
                box.scrollTop = box.scrollHeight;
            }

            /* Open modal: single listener ordered by timestamp, appends to array */
            async function openFFChatMessageModal() {

                try {
                    if (!firebase?.apps?.length || typeof firebase.database !== 'function')
                        throw new Error('Firebase not initialized yet.');

                    closeFFChatDetailsModal()

                    const modal = _el('ffChatMessageModal');
                    const nameEl = _el('ffChatMsgStationName');
                    const thread = _el('ffChatMsgThread');
                    if (!modal || !nameEl || !thread) throw new Error('Chat modal elements missing.');

                    // Station name
                    const accSnap = await firebase.database().ref(FF_ACCOUNT_PATH).once('value');
                    nameEl.textContent = _safe((accSnap.val() || {}).name, 'FireFighterAccount');

                    // Show modal
                    modal.classList.remove('hidden');
                    thread.innerHTML = '<em class="text-gray-500">Loading messages…</em>';

                    // Clean old listener
                    if (__threadRef) {
                        try {
                            __threadRef.off();
                        } catch (_) {}
                        __threadRef = null;
                    }

                    const ref = firebase.database().ref(ADMIN_MESSAGES).orderByChild('timestamp');
                    __threadRef = ref;

                    // Mark firefighter→admin messages as read (isRead: true -> false) once
                    try {
                        const unread = await firebase.database().ref(ADMIN_MESSAGES).orderByChild('isRead').equalTo(true)
                            .once('value');
                        if (unread.exists()) {
                            const up = {};
                            unread.forEach(c => {
                                const m = c.val() || {};
                                if (String(m.sender || '').toLowerCase() !== 'admin') up[`${c.key}/isRead`] = false;
                            });
                            if (Object.keys(up).length) await firebase.database().ref(ADMIN_MESSAGES).update(up);
                        }
                    } catch (e) {
                        console.warn('[FF Chat] mark-read failed:', e);
                    }

                    // Stream messages in order
                    const buf = [];
                    ref.on('child_added', snap => {
                        buf.push({
                            id: snap.key,
                            ...(snap.val() || {})
                        });
                        renderThread(buf);
                    });

                    // Also refresh on edits
                    ref.on('child_changed', snap => {
                        const i = buf.findIndex(m => m.id === snap.key);
                        if (i >= 0) {
                            buf[i] = {
                                id: snap.key,
                                ...(snap.val() || {})
                            };
                            renderThread(buf);
                        }
                    });

                } catch (err) {
                    console.error('[FF Chat][Message] open failed', err);
                    alert(err?.message || 'Could not open messages.');
                }
            }

            function closeFFChatMessageModal() {
                try {
                    if (__threadRef) __threadRef.off();
                } catch (_) {}
                __threadRef = null;
                _el('ffChatMessageModal')?.classList.add('hidden');
            }

            /* Send admin text */
            (() => {
                const form = document.getElementById('ffChatMsgForm');
                const input = document.getElementById('ffChatMsgInput');
                if (!form || !input) return;

                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const text = input.value.trim();
                    if (!text) return;

                    try {
                        if (!firebase?.apps?.length || typeof firebase.database !== 'function')
                            throw new Error('Firebase not initialized yet.');

                        const now = new Date(),
                            pad = n => String(n).padStart(2, '0');
                        const date = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
                        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

                        await firebase.database().ref(ADMIN_MESSAGES).push({
                            sender: 'admin',
                            text,
                            timestamp: now.getTime(),
                            date,
                            time,
                            isRead: false
                        });

                        input.value = '';
                    } catch (err) {
                        console.error('[FF Chat][Message] send failed', err);
                        alert(err?.message || 'Send failed.');
                    }
                });
            })();


            /* =========================================================
             * GEO WRITE (for firefighter browsers only) -> LiveLocation
             * If your admins use this UI, this block will effectively no-op.
             * ========================================================= */
            const EMAIL_TO_IS_FIREFIGHTER = {
                'tcwfssff123@gmail.com': true,
                'lffssff123@gmail.com': true,
                'tccfsff123@gmail.com': true
            };

            let __geoWatchId = null;

            function startFirefighterLocationTracking() {
                const email = (window.AUTH_EMAIL || '').toLowerCase();
                if (!EMAIL_TO_IS_FIREFIGHTER[email]) return; // only FF browsers push LiveLocation
                if (!('geolocation' in navigator)) return;

                __geoWatchId = navigator.geolocation.watchPosition(
                    pos => {
                        const {
                            latitude,
                            longitude,
                            accuracy,
                            heading,
                            speed
                        } = pos.coords || {};
                        const payload = {
                            latitude,
                            longitude,
                            accuracy: Number.isFinite(accuracy) ? accuracy : null,
                            heading: Number.isFinite(heading) ? heading : null,
                            speed: Number.isFinite(speed) ? speed : null,
                            updatedAt: Date.now()
                        };
                        firebase.database().ref(LIVE_LOCATION_PATH).set(payload).catch(e => console.error(
                            '[geo] set failed', e));
                    },
                    err => {
                        console.warn('[geo] watch error', err);
                        navigator.geolocation.getCurrentPosition(p => {
                            const {
                                latitude,
                                longitude,
                                accuracy
                            } = p.coords || {};
                            firebase.database().ref(LIVE_LOCATION_PATH).set({
                                latitude,
                                longitude,
                                accuracy,
                                updatedAt: Date.now()
                            }).catch(e => console.error('[geo] oneshot failed', e));
                        }, () => {}, {
                            enableHighAccuracy: true,
                            timeout: 8000,
                            maximumAge: 0
                        });
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 5000
                    }
                );
            }

            function stopFirefighterLocationTracking() {
                try {
                    if (__geoWatchId != null) navigator.geolocation.clearWatch(__geoWatchId);
                } catch (_) {}
                __geoWatchId = null;
            }
            startFirefighterLocationTracking();

            /* =========================================================
             * LOCATION MODAL — Leaflet: LiveLocation + HQ + Ongoing coords
             * ========================================================= */
            let __map = null,
                __mapForStation = true; // single-station view
            let __ffMarker = null,
                __hqMarker = null,
                __liveRef = null,
                __reportMarkers = [];

            function stopLiveListener() {
                try {
                    __liveRef && __liveRef.off();
                } catch (_) {}
                __liveRef = null;
            }

            function getIcons() {
                return {
                    station: L.icon({
                        iconUrl: '/images/fire-truck.png',
                        iconSize: [40, 40],
                        iconAnchor: [20, 40],
                        popupAnchor: [0, -35]
                    }),
                    hq: L.icon({
                        iconUrl: '/images/fire-station.png',
                        iconSize: [42, 42],
                        iconAnchor: [21, 42],
                        popupAnchor: [0, -35]
                    }),
                    report: L.icon({
                        iconUrl: '/images/current_location.png',
                        iconSize: [34, 34],
                        iconAnchor: [17, 34],
                        popupAnchor: [0, -30]
                    })
                };
            }

            async function fetchOngoingReportCoords() {
                const base = `${FF_ACCOUNT_PATH}/AllReport`;
                const db = firebase.database();
                const out = [];
                for (const type of REPORT_TYPES) {
                    const snap = await db.ref(`${base}/${type}`).once('value');
                    if (!snap.exists()) continue;
                    snap.forEach(c => {
                        const v = c.val() || {};
                        const status = String(v.status || '').replace(/-/g, '').toLowerCase();
                        if (status === 'ongoing') {
                            const lat = parseFloat(v.latitude ?? v.lat);
                            const lng = parseFloat(v.longitude ?? v.lng);
                            if (Number.isFinite(lat) && Number.isFinite(lng)) out.push({
                                type,
                                id: c.key,
                                lat,
                                lng
                            });
                        }
                    });
                }
                return out;
            }


            // tiny helper: wait for an element to exist (max ~1s)
            function waitEl(id, tries = 20, delay = 50) {
                return new Promise((resolve, reject) => {
                    let n = 0;
                    (function tick() {
                        const el = document.getElementById(id);
                        if (el) return resolve(el);
                        if (++n > tries) return reject(new Error(`#${id} not found`));
                        setTimeout(tick, delay);
                    })();
                });
            }

            async function openFFChatLocationModal() {
                try {
                    _show('ffChatLocationModal');

                    // ensure required elements actually exist (and create info div if missing)
                    const modal = await waitEl('ffChatLocationModal');
                    const mapEl = await waitEl('ffChatLocationMap');
                    let info = document.getElementById('ffChatLocationInfo');
                    if (!info) {
                        // create the info block just above the map if template omitted it
                        info = document.createElement('div');
                        info.id = 'ffChatLocationInfo';
                        info.className = 'text-gray-700 mb-4';
                        mapEl.parentNode.insertBefore(info, mapEl);
                    }

                    const nameSpan = document.getElementById('ffChatLocStationName');
                    if (nameSpan) nameSpan.textContent = window.CURRENT_STATION_LABEL || 'Station';

                    info.innerHTML = '<span class="text-gray-500">Loading…</span>';

                    if (!window.L) {
                        info.textContent = 'Leaflet not loaded.';
                        return;
                    }

                    const ICONS = getIcons();

                    // init (once) Leaflet map
                    if (!__map) {
                        __map = L.map(mapEl, {
                            zoomControl: true,
                            attributionControl: true
                        });
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(__map);
                    }

                    // clear previous report markers
                    __reportMarkers.forEach(m => m.remove());
                    __reportMarkers = [];

                    // HQ marker
                    const hq = await getHQCoords();
                    let hqHTML = '';
                    if (hq && Number.isFinite(hq.lat) && Number.isFinite(hq.lng)) {
                        if (!__hqMarker) {
                            __hqMarker = L.marker([hq.lat, hq.lng], {
                                    icon: ICONS.hq
                                })
                                .addTo(__map)
                                .bindPopup(`<b>${_safe(window.CURRENT_STATION_LABEL,'HQ')}</b>`);
                        } else {
                            __hqMarker.setLatLng([hq.lat, hq.lng]);
                        }
                        hqHTML = `
        <div class="mt-2 border-t border-gray-300 pt-1">
          <strong>Headquarters:</strong> ${_safe(window.CURRENT_STATION_LABEL,'HQ')}<br>
          <span class="text-sm text-gray-600">
            Latitude: ${hq.lat.toFixed(6)}, Longitude: ${hq.lng.toFixed(6)}
          </span>
        </div>`;
                    }

                    // ongoing report pins + list
                    const ongoing = await fetchOngoingReportCoords();
                    let ongoingHTML = '';
                    if (ongoing.length) {
                        __reportMarkers = ongoing.map(o =>
                            L.marker([o.lat, o.lng], {
                                icon: ICONS.report
                            })
                            .addTo(__map)
                            .bindPopup(`<b>${o.type}</b><br>${o.lat.toFixed(6)}, ${o.lng.toFixed(6)}`)
                        );
                        ongoingHTML = `
        <div class="mt-2 border-t border-gray-300 pt-1">
          <strong>Ongoing Report Coordinates:</strong>
          <ul class="text-sm text-gray-700 list-disc ml-5 mt-1">
            ${ongoing.map(o => `<li>${o.type} (${o.id}): ${o.lat.toFixed(6)}, ${o.lng.toFixed(6)}</li>`).join('')}
          </ul>
        </div>`;
                    }

                    // live location listener
                    stopLiveListener();
                    const ref = firebase.database().ref(LIVE_LOCATION_PATH);
                    __liveRef = ref;

                    const fitAll = (live) => {
                        const b = L.latLngBounds([]);
                        if (live) b.extend(live);
                        if (__hqMarker) b.extend(__hqMarker.getLatLng());
                        __reportMarkers.forEach(m => b.extend(m.getLatLng()));
                        if (b.isValid()) __map.fitBounds(b, {
                            padding: [20, 20],
                            maxZoom: 15
                        });
                    };

                    ref.on('value', snap => {
                        const v = snap.val() || {};
                        const lat = parseFloat(v.latitude),
                            lng = parseFloat(v.longitude);
                        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                            info.innerHTML =
                                `<div><strong>LiveLocation:</strong> unavailable</div>${hqHTML}${ongoingHTML}`;
                            setTimeout(() => {
                                try {
                                    __map.invalidateSize();
                                } catch (_) {}
                            }, 200);
                            return;
                        }
                        const ll = [lat, lng];
                        if (!__ffMarker) {
                            __ffMarker = L.marker(ll, {
                                    icon: ICONS.station
                                })
                                .addTo(__map)
                                .bindPopup('<b>FireFighter</b>');
                        } else {
                            __ffMarker.setLatLng(ll);
                        }

                        info.innerHTML = `
        <div><strong>LiveLocation:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
        ${v.updatedAt ? `<div class="text-xs text-gray-500">Updated: ${new Date(v.updatedAt).toLocaleString()}</div>` : ''}
        ${hqHTML}
        ${ongoingHTML}
      `;

                        fitAll(ll);
                        setTimeout(() => {
                            try {
                                __map.invalidateSize();
                            } catch (_) {}
                        }, 200);
                    });

                } catch (e) {
                    console.error('[FF Chat][Location] open failed:', e);
                    alert(e.message || 'Could not load station location.');
                }
            }

            function closeFFChatLocationModal() {
                stopLiveListener();
                _hide('ffChatLocationModal');
            }

            /* =========================================================
             * simple search filter
             * ========================================================= */
            function filterFireFighterChatTable() {
                const nameFilter = (_el('fireFighterChatNameSearch')?.value || '').toLowerCase();
                const contactFilter = (_el('fireFighterChatContactSearch')?.value || '').toLowerCase();
                document.querySelectorAll('#fireFighterChatBody .fire-fighter-row').forEach(row => {
                    const n = row.querySelector('.name-cell')?.textContent.toLowerCase() || '';
                    const c = row.querySelector('.contact-cell')?.textContent.toLowerCase() || '';
                    row.style.display = n.includes(nameFilter) && c.includes(contactFilter) ? '' : 'none';
                });
            }


            /* =========================================================
             * URL PARAMS / NAV DEEP-LINKING
             * ========================================================= */
            function handleUrlParams() {
                const params = new URLSearchParams(window.location.search);
                const incidentId = params.get('incidentId');
                const type = params.get('type');

                if (incidentId && type && typeof openDetailsModal === 'function') {
                    setTimeout(() => {
                        const typeDropdown = document.getElementById('incidentType');
                        if (typeDropdown && typeDropdown.value !== type) {
                            typeDropdown.value = type;
                            toggleIncidentTables();
                        }

                        const row = document.getElementById(`reportRow${incidentId}`);
                        if (row) {
                            row.scrollIntoView({
                                behavior: "smooth",
                                block: "center"
                            });
                            row.classList.add('bg-yellow-100');
                            setTimeout(() => row.classList.remove('bg-yellow-100'), 3000);
                        }

                        openDetailsModal(incidentId, type);

                        const newUrl = new URL(window.location);
                        newUrl.searchParams.delete('incidentId');
                        newUrl.searchParams.delete('type');
                        window.history.replaceState({}, document.title, newUrl.toString());
                    }, 500);
                }
            }


            /* =========================================================
             * DATA INITIALIZATION / GLOBALS
             * ========================================================= */

            let fireReports = @json($fireReports);
            let otherEmergencyReports = @json($otherEmergencyReports);
            let emsReports = @json($emsReports ?? []);
            let smsReports = @json($smsReports ?? []);

            // ---- keep only the current station's items right after hydration ----
            (function filterInitialDataToStation() {
                // coerce object-or-array to array of values
                const toArray = (v) => Array.isArray(v) ? v : (v && typeof v === 'object') ? Object.values(v) : [];

                function currentStationKey() {
                    const root =
                        (typeof CURRENT_STATION_ROOT === 'string' && CURRENT_STATION_ROOT) ||
                        (typeof window !== 'undefined' && window.CURRENT_STATION_ROOT) ||
                        'CapstoneFlare/CanocotanFireStation';
                    const parts = root.split('/').filter(Boolean);
                    return parts[parts.length - 1] || '';
                }

                function stationKeyFromLabel(name = '') {
                    const n = String(name).toLowerCase();
                    if (n.includes('mabini') || n.includes('west')) return 'MabiniFireStation';
                    if (n.includes('filipina')) return 'LaFilipinaFireStation';
                    if (n.includes('canocotan') || n.includes('central')) return 'CanocotanFireStation';
                    return null; // <- IMPORTANT: unknown label = no match
                }

                function belongsToCurrentStation(r = {}) {
                    const me = currentStationKey();

                    // 1) Prefer explicit DB path marker if present
                    const explicitRoot = r.__root || r.stationRoot || r.sourceRoot || '';
                    if (explicitRoot) {
                        const last = explicitRoot.split('/').filter(Boolean).pop() || '';
                        return last === me;
                    }

                    // 2) Fall back to human-readable label matching
                    const label = r.fireStationName || r.stationName || r.prefix || '';
                    const labelKey = stationKeyFromLabel(label);
                    if (labelKey) return labelKey === me;

                    // 3) Unknown → exclude to avoid cross-station bleed
                    return false;
                }


                function belongsToCurrentStation(r = {}) {
                    const me = currentStationKey();
                    // 1) explicit path wins
                    const explicitRoot = r.__root || r.stationRoot || r.sourceRoot || '';
                    if (explicitRoot) {
                        const last = explicitRoot.split('/').filter(Boolean).pop() || '';
                        return last === me;
                    }
                    // 2) label fallback
                    const label = r.fireStationName || r.stationName || r.prefix || '';
                    const labelKey = stationKeyFromLabel(label);
                    if (label) return labelKey === me;
                    // 3) unknown → keep out (conservative)
                    return false;
                }

                const keep = (r) => belongsToCurrentStation(r || {});

                // IMPORTANT: coerce first, then filter
                fireReports = toArray(fireReports).filter(keep);
                otherEmergencyReports = toArray(otherEmergencyReports).filter(keep);
                emsReports = toArray(emsReports).filter(keep);
                smsReports = toArray(smsReports).filter(keep);

                // quick visibility for sanity checks
                console.info('[filter] station=', currentStationKey(), {
                    fire: fireReports.length,
                    other: otherEmergencyReports.length,
                    ems: emsReports.length,
                    sms: smsReports.length
                });
            })();



            // --- wipe SSR rows and re-render from filtered arrays ---
            (function hydrateTablesFromFilteredGlobals() {
                try {
                    // Fire
                    const fireBody = document.getElementById('fireReportsBody');
                    if (fireBody) {
                        fireBody.innerHTML = '';
                        renderSortedReports(fireReports, 'fireReports');
                    }

                    // Other Emergency
                    const otherBody = document.getElementById('otherEmergencyTableBody');
                    if (otherBody) {
                        otherBody.innerHTML = '';
                        renderSortedReports(otherEmergencyReports, 'otherEmergency');
                    }

                    // EMS
                    const emsBody = document.getElementById('emsBody');
                    if (emsBody) {
                        emsBody.innerHTML = '';
                        renderEmsTable();
                    }

                    // SMS
                    const smsBody = document.getElementById('smsReportsBody');
                    if (smsBody) {
                        smsBody.innerHTML = '';
                        renderSmsReports();
                    }

                    // Merged
                    renderAllReports();
                } catch (e) {
                    console.warn('hydrateTablesFromFilteredGlobals failed:', e);
                }
            })();



            // Globals
            let currentReport = null;
            let currentReportType = 'fireReports';
            let liveListeners = [];
            let storedMessages = [];
            let heardSmsIds = new Set();

            // put this near your other module-level vars
            const __seenReplyKeys = new Set();
            const __seenResponseKeys = new Set();


            // Chat bubble coalescing
            let __lastBubble = {
                type: null,
                ts: 0,
                el: null
            };
            const __GROUP_WINDOW_MS = 15000;


            /* =========================================================
             * NORMALIZATION HELPERS (DATE/TIME)
             * ========================================================= */

            function to24h(t) {
                if (!t) return '';
                const m = String(t).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/i);
                if (!m) return t;
                let [, hh, mm, ap] = m;
                let h = parseInt(hh, 10);
                if (ap) {
                    const up = ap.toUpperCase();
                    if (up === 'PM' && h !== 12) h += 12;
                    if (up === 'AM' && h === 12) h = 0;
                }
                return `${String(h).padStart(2, '0')}:${mm}`;
            }

            function dateToISO(dmy) {
                if (!dmy) return '';
                const parts = dmy.split('/');
                if (parts.length !== 3) return '';
                const [dd, mm, yy] = parts;
                const yyyy = yy.length === 2 ? `20${yy}` : yy;
                return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
            }


            /* =========================================================
             * STATION CONTEXT / NODES (LOCKED TO CANOCOTAN)
             * ========================================================= */


            function nodes() {
                const ALL = `${CURRENT_STATION_ROOT}/AllReport`;
                return {
                    base: ALL,
                    fireReport: `${ALL}/FireReport`,
                    otherEmergency: `${ALL}/OtherEmergencyReport`,
                    ems: `${ALL}/EmergencyMedicalServicesReport`,
                    sms: `${ALL}/SmsReport`,
                    smsCandidates: [`${ALL}/SmsReport`],
                    profile: `${CURRENT_STATION_ROOT}/Profile`,
                    firefighters: `${CURRENT_STATION_ROOT}/FireFighter`
                };
            }



            async function resolveSmsPathById(id) {
                const n = nodes();
                if (!n) return null;

                for (const base of n.smsCandidates) {
                    const snap = await firebase.database().ref(`${base}/${id}`).once('value');
                    if (snap.exists()) return base;
                }
                return n.smsCandidates[0] || null;
            }


            /* =========================================================
             * REAL-TIME LISTENERS (FIRE / OTHER / EMS / SMS)
             * ========================================================= */

            function initializeRealTimeListener() {
                const n = nodes();
                if (!n) {
                    console.error("No station prefix.");
                    return;
                }

                // FIRE
                firebase.database().ref(n.fireReport).on('child_added', (snapshot) => {
                    const r = snapshot.val();
                    if (!r) return;
                    const id = snapshot.key;
                    if (document.getElementById(`reportRow${id}`)) return;
                    r.id = id;
                    insertNewReportRow(r, 'fireReports');
                    renderAllReports();
                });
                firebase.database().ref(n.fireReport).on('child_changed', (snap) => {
                    applyRealtimePatch(snap, 'fireReports');
                    renderAllReports();
                });
                firebase.database().ref(n.fireReport).on('child_removed', (snap) => {
                    removeRow(snap.key);
                    const i = (fireReports || []).findIndex(x => x.id === snap.key);
                    if (i !== -1) fireReports.splice(i, 1);
                    renderAllReports();
                });

                // OTHER EMERGENCY
                firebase.database().ref(n.otherEmergency).on('child_added', (snapshot) => {
                    const r = snapshot.val();
                    if (!r) return;
                    r.id = snapshot.key;
                    if (!document.getElementById(`reportRow${r.id}`)) insertNewReportRow(r, 'otherEmergency');
                    renderAllReports();
                });
                firebase.database().ref(n.otherEmergency).on('child_changed', (snap) => {
                    applyRealtimePatch(snap, 'otherEmergency');
                    renderAllReports();
                });
                firebase.database().ref(n.otherEmergency).on('child_removed', (snap) => {
                    removeRow(snap.key);
                    const i = (otherEmergencyReports || []).findIndex(x => x.id === snap.key);
                    if (i !== -1) otherEmergencyReports.splice(i, 1);
                    renderAllReports();
                });

                // EMS
                firebase.database().ref(n.ems).on('child_added', (snapshot) => {
                    const r = snapshot.val();
                    if (!r) return;
                    r.id = snapshot.key;
                    if (!document.getElementById(`reportRow${r.id}`)) insertNewEmsRow(r);
                    renderAllReports();
                });
                firebase.database().ref(n.ems).on('child_changed', (snap) => {
                    applyRealtimePatchEms(snap);
                    renderAllReports();
                });
                firebase.database().ref(n.ems).on('child_removed', (snap) => {
                    removeRow(snap.key);
                    const i = (emsReports || []).findIndex(x => x.id === snap.key);
                    if (i !== -1) emsReports.splice(i, 1);
                    renderAllReports();
                });

                // SMS (listen on all candidate paths)
                (n.smsCandidates || []).forEach((path) => {
                    const ref = firebase.database().ref(path);

                    ref.on('child_added', (snapshot) => {
                        const r = snapshot.val();
                        if (!r) return;
                        const id = snapshot.key;
                        if (heardSmsIds.has(id)) return;
                        heardSmsIds.add(id);
                        r.id = id;
                        insertNewSmsRow(r);
                        renderAllReports();
                    });

                    ref.on('child_changed', (snap) => {
                        applyRealtimePatchSms(snap);
                        renderSmsReports(); // keep the SMS table fresh too
                        renderAllReports();
                    });

                    ref.on('child_removed', (snap) => {
                        removeRow(snap.key);
                        const i = (smsReports || []).findIndex(x => x.id === snap.key);
                        if (i !== -1) smsReports.splice(i, 1);
                        renderAllReports();
                    });
                });
            }

            function applyRealtimePatchSms(snapshot) {
                const id = snapshot.key;
                const patch = snapshot.val() || {};

                const i = (smsReports || []).findIndex(r => r.id === id);
                if (i !== -1) smsReports[i] = {
                    ...smsReports[i],
                    ...patch,
                    id
                };

                const row = document.getElementById(`reportRow${id}`);
                if (!row) return;
                row.setAttribute('data-report', JSON.stringify(smsReports[i] || patch));

                if (typeof patch.location !== 'undefined') row.children[1].textContent = patch.location || 'N/A';

                if (typeof patch.date !== 'undefined' || typeof patch.time !== 'undefined') {
                    const r = JSON.parse(row.getAttribute('data-report')) || {};
                    const t = to24h(r.time) || r.time || 'N/A';
                    row.children[2].textContent = `${r.date || 'N/A'} ${t}`;
                }

                if (typeof patch.status !== 'undefined') {
                    const s = capStatus(patch.status);
                    const statusCell = row.querySelector('.status');
                    if (statusCell) {
                        statusCell.textContent = s;
                        statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
                    }
                }
            }

            function applyRealtimePatchEms(snapshot) {
                const id = snapshot.key;
                const patch = snapshot.val() || {};
                const i = (emsReports || []).findIndex(r => r.id === id);
                if (i !== -1) emsReports[i] = {
                    ...emsReports[i],
                    ...patch,
                    id
                };

                const row = document.getElementById(`reportRow${id}`);
                if (!row) return;
                row.setAttribute('data-report', JSON.stringify(emsReports[i] || patch));

                // columns: 0 #, 1 type, 2 location, 3 datetime, 4 status, 5 action
                if (typeof patch.type !== 'undefined') row.children[1].textContent = patch.type || 'N/A';
                if (typeof patch.exactLocation !== 'undefined') row.children[2].textContent = patch.exactLocation || 'N/A';

                if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                    const r = JSON.parse(row.getAttribute('data-report')) || {};
                    row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
                }

                if (typeof patch.status !== 'undefined') {
                    const s = capStatus(patch.status);
                    const statusCell = row.querySelector('.status');
                    if (statusCell) {
                        statusCell.textContent = s;
                        statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
                    }
                }
            }




            /* =========================================================
             * PATCH HELPERS (FIRE / OTHER)
             * ========================================================= */

            function applyRealtimePatch(snapshot, reportType) {
                const id = snapshot.key;
                const patch = snapshot.val() || {};
                const arr = reportType === 'fireReports' ? fireReports : otherEmergencyReports;

                const i = arr.findIndex(r => r.id === id);
                if (i !== -1) arr[i] = {
                    ...arr[i],
                    ...patch,
                    id
                };

                const row = document.getElementById(`reportRow${id}`);
                if (!row) return;

                row.setAttribute('data-report', JSON.stringify(arr[i] || patch));

                if (typeof patch.status !== 'undefined') {
                    const statusCell = row.querySelector('.status');
                    if (statusCell) {
                        const s = capStatus(patch.status);
                        statusCell.textContent = s;
                        statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
                    }
                }

                if (reportType === 'fireReports') {
                    // 0 #, 1 type, 2 location, 3 datetime, 4 status, 5 action
                    if (typeof patch.type !== 'undefined') row.children[1].textContent = patch.type || 'N/A';
                    if (typeof patch.exactLocation !== 'undefined') row.children[2].textContent = patch.exactLocation || 'N/A';

                    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                        const r = JSON.parse(row.getAttribute('data-report')) || {};
                        row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
                    }
                } else {
                    // other: 0 #, 1 location, 2 emergencyType, 3 datetime, 4 status, 5 action
                    if (typeof patch.exactLocation !== 'undefined') row.children[1].textContent = patch.exactLocation || 'N/A';
                    if (typeof patch.emergencyType !== 'undefined') row.children[2].textContent = patch.emergencyType || 'N/A';

                    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                        const r = JSON.parse(row.getAttribute('data-report')) || {};
                        row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
                    }
                }
            }

            function removeRow(id) {
                const el = document.getElementById(`reportRow${id}`);
                if (el && el.parentNode) el.parentNode.removeChild(el);
            }


            /* =========================================================
             * RENDERING: FIRE / OTHER / EMS TABLES
             * ========================================================= */

            function insertNewReportRow(report, reportType) {

                report.__root = window.CURRENT_STATION_ROOT; // <— add
                const tableBodyId = reportType === 'fireReports' ?
                    'fireReportsBody' :
                    'otherEmergencyTableBody';

                const tableBody = document.getElementById(tableBodyId);
                if (!tableBody) return;

                // Don't duplicate an existing row
                if (document.getElementById(`reportRow${report.id}`)) return;

                // 1) Normalize date/time (use 24h so sorting is stable)
                const now = new Date();
                const pad = n => String(n).padStart(2, '0');

                report.date = report.date ||
                    `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()}`; // DD/MM/YYYY
                report.reportTime = to24h(report.reportTime) || `${pad(now.getHours())}:${pad(now.getMinutes())}`;

                // 2) Normalize lat/lng for Other Emergency (so Location icon appears even if strings)
                if (reportType === 'otherEmergency') {
                    const lat = parseFloat(report.latitude);
                    const lng = parseFloat(report.longitude);
                    report.latitude = Number.isFinite(lat) ? lat : null;
                    report.longitude = Number.isFinite(lng) ? lng : null;
                }

                // 3) Robust DMY + 24h parser for sorting
                function parseDateTime(dmy, hhmm) {
                    if (!dmy) return 0;
                    const [dd, mm, yy] = String(dmy).split('/');
                    const yyyy = yy && yy.length === 2 ? `20${yy}` : yy;
                    const t24 = to24h(hhmm || '') || '00:00';
                    const dt = new Date(`${yyyy}-${mm}-${dd}T${t24}:00`);
                    return dt.getTime() || 0;
                }

                // 4) Insert → sort newest first → render with highlight
                if (reportType === 'fireReports') {
                    fireReports.unshift(report);
                    fireReports.sort((a, b) =>
                        parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime)
                    );
                    renderSortedReports(fireReports, 'fireReports', report.id);
                } else {
                    otherEmergencyReports.unshift(report);
                    otherEmergencyReports.sort((a, b) =>
                        parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime)
                    );
                    renderSortedReports(otherEmergencyReports, 'otherEmergency', report.id);
                }

                // Keep “All Reports” in sync
                renderAllReports();
            }


            function insertNewEmsRow(report) {
                report.__root = window.CURRENT_STATION_ROOT; // <— add
                const body = document.getElementById('emsBody');
                if (!body) return;

                report.timestamp = Number.isFinite(report.timestamp) ? report.timestamp : Date
                    .now(); // ← ensure freshness in All Reports
                report.date = report.date || new Date().toLocaleDateString();
                report.reportTime = report.reportTime || new Date().toLocaleTimeString();
                report.createdAt = report.createdAt ?? report.timestamp;

                const idx = (emsReports || []).findIndex(r => r.id === report.id);
                if (idx === -1) emsReports.unshift(report);
                else emsReports[idx] = {
                    ...emsReports[idx],
                    ...report
                };

                renderEmsTable(report.id);
            }



            function renderSortedReports(reportsArray, reportType, highlightId = null) {
                const tableBodyId = reportType === 'fireReports' ? 'fireReportsBody' : 'otherEmergencyTableBody';
                const tableBody = document.getElementById(tableBodyId);
                if (!tableBody) return;

                tableBody.style.visibility = 'hidden';
                const fragment = document.createDocumentFragment();

                reportsArray.forEach((report, index) => {
                    const rowId = `reportRow${report.id}`;
                    const statusTxt = capStatus(report.status || 'Unknown');
                    const color = statusColor(statusTxt);

                    // ✅ normalize coords once
                    const lat = parseFloat(report.latitude);
                    const lng = parseFloat(report.longitude);
                    const hasLL = Number.isFinite(lat) && Number.isFinite(lng);

                    const row = document.createElement('tr');
                    row.id = rowId;
                    row.className = 'border-b';
                    row.classList.toggle('bg-yellow-100', !!(highlightId && report.id === highlightId));
                    row.setAttribute('data-report', JSON.stringify(report));
                    row.setAttribute('data-type', reportType);

                    let cells;
                    if (reportType === 'fireReports') {
                        cells = `
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.type || 'N/A'}</td>
        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
        <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
        <td class="px-4 py-2 status text-${color}-500">${statusTxt}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
          <!-- Message should always be present -->
          <a href="javascript:void(0);"
            class="msg-btn relative inline-flex items-center"
            data-key="fireReports|${report.id}"
            onclick="openMessageModal('${report.id}', 'fireReports')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
            <span class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
            </a>

          ${hasLL ? `
                            <a href="javascript:void(0);" onclick="openLocationModal(${lat}, ${lng})">
                              <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                            </a>` : ''}
          <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'fireReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
          </a>
        </td>`;
                    } else {
                        // OTHER EMERGENCY: make order = Message → (Location) → Details
                        cells = `
    <td class="px-4 py-2">${index + 1}</td>
    <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
    <td class="px-4 py-2">${report.emergencyType || 'N/A'}</td>
    <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
    <td class="px-4 py-2 status text-${color}-500">${statusTxt}</td>
    <td class="px-4 py-2 space-x-2 flex items-center">
      <a href="javascript:void(0);"
         class="msg-btn relative inline-flex items-center"
         data-key="otherEmergency|${report.id}"
         onclick="openMessageModal('${report.id}', 'otherEmergency')">
         <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
         <span class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
      </a>

      ${hasLL ? `
                        <a href="javascript:void(0);" onclick="openLocationModal(${lat}, ${lng})">
                          <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                        </a>` : ''}
      <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'otherEmergency')">
        <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
      </a>
    </td>`;
                    }


                    row.innerHTML = cells;
                    fragment.appendChild(row);
                });

                tableBody.innerHTML = '';
                tableBody.appendChild(fragment);
                tableBody.style.visibility = 'visible';
                ensureMessageBadges();
            }


            function renderEmsTable(highlightId = null) {
                const body = document.getElementById('emsBody');
                if (!body) return;

                const arr = asArray(emsReports).slice();
                // sort newest → oldest using datetime
                function parseDateTime(dateStr, timeStr) {
                    const [day, month, year] = (dateStr || '').split('/');
                    const normalizedYear = year && year.length === 2 ? '20' + year : year;
                    const dt = new Date(`${normalizedYear}-${month}-${day}T${timeStr || '00:00'}`);
                    return dt.getTime() || 0;
                }
                arr.sort((a, b) => parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime));

                body.innerHTML = arr.map((report, index) => {
                    const status = capStatus(report.status || 'Pending');
                    const color = statusColor(status);
                    const hasLL = report.latitude != null && report.longitude != null;

                    return `
      <tr id="reportRow${report.id}" class="border-b ${highlightId && report.id===highlightId ? 'bg-yellow-100' : ''}"
          data-report='${JSON.stringify(report)}' data-type="emsReports">
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.type || 'N/A'}</td>
        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
        <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
        <td class="px-4 py-2 status text-${color}-500">${status}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
      <a href="javascript:void(0);"
    class="msg-btn relative inline-flex items-center"
    data-key="emsReports|${report.id}"
    onclick="openMessageModal('${report.id}', 'emsReports')">
    <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
    <span class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
    </a>

        ${hasLL ? `<a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
                                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                                    </a>` : ''}
        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'emsReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>
      </tr>`;
                }).join('');
                ensureMessageBadges();
            }


            /* =========================================================
             * RENDERING: SMS TABLE (ADD/RENDER HELPERS)
             * ========================================================= */

            function renderSmsReports(highlightId = null) {
                const body = document.getElementById('smsReportsBody');
                if (!body) return;

                const arr = asArray(smsReports).slice();
                arr.sort((a, b) => parseDT(b.date, b.time, b.timestamp ?? b.createdAt ?? b.updatedAt) -
                    parseDT(a.date, a.time, a.timestamp ?? a.createdAt ?? a.updatedAt));

                const rows = arr.map((report, index) => {
                    const status = capStatus(report.status || 'Pending');
                    const color = statusColor(status);
                    const hasLL = report.latitude != null && report.longitude != null;
                    const dt = `${report.date || 'N/A'} ${to24h(report.time) || report.time || 'N/A'}`;
                    const locBtn = hasLL ?
                        `<a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
           <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
         </a>` : '';
                    return `
      <tr id="reportRow${report.id}" class="border-b ${highlightId && report.id===highlightId ? 'bg-yellow-100' : ''}"
          data-report='${JSON.stringify(report)}' data-type="smsReports">
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.location || 'N/A'}</td>
        <td class="px-4 py-2">${dt}</td>
        <td class="px-4 py-2 status text-${color}-500">${status}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
       <a href="javascript:void(0);"
    class="msg-btn relative inline-flex items-center"
    data-key="smsReports|${report.id}"
    onclick="openMessageModal('${report.id}', 'smsReports')">
    <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
   <span class="msg-badge hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">1</span>
    </a>

        ${locBtn}
        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'smsReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>

      </tr>`;
                }).join('');

                body.innerHTML = rows;
            }

            function insertNewSmsRow(report) {
                report.__root = window.CURRENT_STATION_ROOT; // <— add
                report.date = report.date || new Date().toISOString().slice(0, 10);
                report.time = report.time || new Date().toTimeString().slice(0, 5);
                const idx = (smsReports || []).findIndex(r => r.id === report.id);
                if (idx === -1) smsReports.unshift(report);
                else smsReports[idx] = {
                    ...smsReports[idx],
                    ...report
                };
                renderSmsReports(report.id);
                ensureMessageBadges();
            }



            function hasLL(r = {}) {
                const lat = parseFloat(r.latitude);
                const lng = parseFloat(r.longitude);
                return Number.isFinite(lat) && Number.isFinite(lng);
            }

            function normalizeLL(r = {}) {
                const n = {
                    ...r
                };
                const lat = parseFloat(n.latitude);
                const lng = parseFloat(n.longitude);
                n.latitude = Number.isFinite(lat) ? lat : null;
                n.longitude = Number.isFinite(lng) ? lng : null;
                return n;
            }



            /* =========================================================
             * ALL REPORTS (MERGE + RENDER)
             * ========================================================= */

            // --- station filter helpers (NEW) ---
            function currentStationKey() {
                const root = (typeof CURRENT_STATION_ROOT === 'string' && CURRENT_STATION_ROOT) ||
                    (typeof window !== 'undefined' && window.CURRENT_STATION_ROOT) ||
                    'CapstoneFlare/CanocotanFireStation';
                const parts = root.split('/').filter(Boolean);
                return parts[parts.length - 1] || '';
            }

            function stationKeyFromLabel(name = '') {
                const n = String(name).toLowerCase();
                if (n.includes('mabini') || n.includes('west')) return 'MabiniFireStation';
                if (n.includes('filipina')) return 'LaFilipinaFireStation';
                return 'CanocotanFireStation';
            }

            /** Keep only reports that belong to the current station.
             *  Works with any of these fields if present:
             *  - __root | stationRoot | sourceRoot: full db path (preferred)
             *  - fireStationName | stationName | prefix: human label fallback
             */
            function belongsToCurrentStation(r = {}) {
                const me = currentStationKey();

                const explicitRoot = r.__root || r.stationRoot || r.sourceRoot || '';
                if (explicitRoot) {
                    const parts = explicitRoot.split('/').filter(Boolean);
                    return (parts[parts.length - 1] || '') === me;
                }

                const label = r.fireStationName || r.stationName || r.prefix || '';
                if (label) return stationKeyFromLabel(label) === me;

                // If we can't tell, default to excluding to avoid cross-station bleed.
                return false;
            }


            function parseDT(d, t, fallbackTs, preferMDY = false) {
                // t → 24h
                const time24 = to24h(t || '') || (t || '00:00');

                let best = NaN;
                if (d && d.includes('/')) {
                    const [p1, p2, p3] = d.split('/');
                    const yyyy = (p3 && p3.length === 2) ? `20${p3}` : (p3 || '');
                    const A = String(p1 || '').padStart(2, '0'); // could be day or month
                    const B = String(p2 || '').padStart(2, '0'); // could be month or day
                    // MDY:  MM/DD/YYYY
                    const mdy = Date.parse(`${yyyy}-${A}-${B} ${time24}`);
                    // DMY:  DD/MM/YYYY
                    const dmy = Date.parse(`${yyyy}-${B}-${A} ${time24}`);

                    // choose by preference, but fall back to whichever is valid
                    best = preferMDY ?
                        (!isNaN(mdy) ? mdy : dmy) :
                        (!isNaN(dmy) ? dmy : mdy);
                }

                if (isNaN(best)) {
                    const fb = (typeof fallbackTs === 'number') ? fallbackTs : 0;
                    return fb;
                }
                return best;
            }


            function asArray(v) {
                return Array.isArray(v) ? v : (v ? Object.values(v) : []);
            }

            function firstFinite(...vals) {
                for (const v of vals) {
                    const n = Number(v);
                    if (Number.isFinite(n)) return n;
                }
                return NaN;
            }


            function buildAllReports() {
                const fire = asArray(fireReports)
                    .filter(belongsToCurrentStation) // ← NEW
                    .map(r => ({
                        id: r.id,
                        type: 'fireReports',
                        location: r.exactLocation || r.location || 'N/A',
                        date: r.date || '',
                        time: r.reportTime || '',
                        status: r.status || 'Unknown',
                        lat: r.latitude,
                        lng: r.longitude,
                        sortTs: (() => {
                            const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
                            return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, false);
                        })()
                    }));

                const other = asArray(otherEmergencyReports)
                    .filter(belongsToCurrentStation) // ← NEW
                    .map(r => ({
                        id: r.id,
                        type: 'otherEmergency',
                        location: r.exactLocation || r.location || 'N/A',
                        date: r.date || '',
                        time: r.reportTime || '',
                        status: r.status || 'Unknown',
                        lat: r.latitude,
                        lng: r.longitude,
                        sortTs: (() => {
                            const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
                            return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, false);
                        })()
                    }));

                const ems = asArray(emsReports)
                    .filter(belongsToCurrentStation) // ← NEW
                    .map(r => ({
                        id: r.id,
                        type: 'emsReports',
                        location: r.exactLocation || r.location || 'N/A',
                        date: r.date || '',
                        time: r.reportTime || '',
                        status: r.status || 'Unknown',
                        lat: r.latitude,
                        lng: r.longitude,
                        sortTs: (() => {
                            const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
                            return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, false);
                        })()
                    }));

                const sms = asArray(smsReports)
                    .filter(belongsToCurrentStation) // ← NEW
                    .map(r => ({
                        id: r.id,
                        type: 'smsReports',
                        location: r.location || 'N/A',
                        date: r.date || '',
                        time: r.time || '',
                        status: r.status || 'N/A',
                        lat: r.latitude,
                        lng: r.longitude,
                        sortTs: (() => {
                            const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
                            return Number.isFinite(num) ? num : parseDT(r.date, r.time, 0, true);
                        })()
                    }));

                return [...fire, ...other, ...ems, ...sms].sort((a, b) => b.sortTs - a.sortTs);
            }


            function statusColor(s) {
                return s === 'Ongoing' ? 'red' :
                    s === 'Completed' ? 'green' :
                    s === 'Pending' ? 'orange' :
                    s === 'Received' ? 'blue' :
                    'yellow';
            }

            function safeRenderAllReports() {
                try {
                    renderAllReports();
                } catch (e) {
                    console.error('renderAllReports failed:', e);
                }
            }

            function safeInitRealtime() {
                try {
                    if (window.firebase && firebase.apps && firebase.apps.length && typeof firebase.database === 'function') {
                        initializeRealTimeListener();
                    } else {
                        setTimeout(safeInitRealtime, 500);
                    }
                } catch (e) {
                    console.error('initializeRealTimeListener error:', e);
                }
            }


            function renderAllReports() {
                const body = document.getElementById('allReportsBody');
                if (!body) return;

                const rows = buildAllReports();

                // If there's NO data at all → render only the empty state row and bail.
                if (!rows || rows.length === 0) {
                    body.innerHTML = `
      <tr id="noAllReportsRow" aria-live="polite">
        <td colspan="6" class="text-center text-gray-500 py-4 italic">
          No reports found.
        </td>
      </tr>`;
                    return;
                }


                body.innerHTML = rows.map((r, i) => {
                    const time24 = to24h(r.time) || r.time || 'N/A';
                    const dateStr = r.date || 'N/A';
                    const locBtn = (r.lat != null && r.lng != null) ?
                        `<a href="javascript:void(0);" onclick="openLocationModal(${r.lat}, ${r.lng})"><img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6"></a>` :
                        '';

                    const statusDisp = capStatus(r.status);
                    const colorClass = (r.type === 'smsReports' && statusDisp === 'Pending') ?
                        'text-black' :
                        `text-${statusColor(statusDisp)}-500`;

                    // NOTE: All Reports columns are (#, Type, Location, DateTime, Status, Action)
                    const typeLabel =
                        r.type === 'fireReports' ? 'Fire' :
                        r.type === 'otherEmergency' ? 'Other Emergency' :
                        r.type === 'emsReports' ? 'EMS' :
                        'SMS';

                    return `<tr class="border-b" data-merged="1" data-type="${r.type}" data-id="${r.id}">
      <td class="px-4 py-2">${i + 1}</td>
      <td class="px-4 py-2">${typeLabel}</td>
      <td class="px-4 py-2">${r.location}</td>
      <td class="px-4 py-2">${dateStr} ${time24}</td>
      <td class="px-4 py-2 status ${colorClass}">${statusDisp}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
        <a href="javascript:void(0);" onclick="openMessageModal('${r.id}','${r.type}')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
        </a>
        ${locBtn}
        <a href="javascript:void(0);" onclick="openDetailsModal('${r.id}','${r.type}')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>

    </tr>`;
                }).join('');

                if (typeof filterAllReportsTable === 'function') filterAllReportsTable();
                ensureMessageBadges();
            }


            /* =========================================================
             * FILTERS (FIRE / OTHER / ALL / SMS / EMS)
             * ========================================================= */

            function filterFireReportTable() {
                const statusFilter = document.getElementById('fireStatusFilter').value.toLowerCase();
                const locationSearch = document.getElementById('fireLocationSearch').value.toLowerCase();
                const typeSearch = document.getElementById('fireTypeSearch').value.toLowerCase();

                const mode = document.getElementById('fireDateTimeFilter').value;
                const dateSearch = mode === 'date' ? document.getElementById('fireDateSearch').value : '';
                const timeSearch = mode === 'time' ? document.getElementById('fireTimeSearch').value : '';

                const rows = document.querySelectorAll('#fireReportsBody tr');

                rows.forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report'));

                    const matchesStatus = !statusFilter || (report.status && report.status.toLowerCase() ===
                        statusFilter);
                    const matchesType = !typeSearch || ((report.type || '').toLowerCase().includes(typeSearch));
                    const matchesLocation = !locationSearch || ((report.exactLocation || '').toLowerCase().includes(
                        locationSearch));

                    const reportDateISO = dateToISO(report.date || '');
                    const matchesDate = !dateSearch || (reportDateISO === dateSearch);

                    const matchesTime = (() => {
                        if (!timeSearch) return true;
                        const t = to24h(report.reportTime || '');
                        return !!t && t === timeSearch;
                    })();

                    row.style.display = (matchesStatus && matchesType && matchesLocation && matchesDate &&
                        matchesTime) ? '' : 'none';
                });
            }

            function handleDateTimeFilterChange() {
                const mode = document.getElementById('fireDateTimeFilter').value;
                const d = document.getElementById('fireDateSearch');
                const t = document.getElementById('fireTimeSearch');

                if (mode === 'date') {
                    d.classList.remove('hidden');
                    t.classList.add('hidden');
                } else if (mode === 'time') {
                    t.classList.remove('hidden');
                    d.classList.add('hidden');
                } else {
                    d.classList.add('hidden');
                    t.classList.add('hidden');
                    d.value = '';
                    t.value = '';
                }
                filterFireReportTable();
            }

            function handleOtherDateTimeFilterChange() {
                const mode = document.getElementById('otherDateTimeFilter').value;
                const d = document.getElementById('otherDateSearch');
                const t = document.getElementById('otherTimeSearch');

                if (mode === 'date') {
                    d.classList.remove('hidden');
                    t.classList.add('hidden');
                } else if (mode === 'time') {
                    t.classList.remove('hidden');
                    d.classList.add('hidden');
                } else {
                    d.classList.add('hidden');
                    t.classList.add('hidden');
                    d.value = '';
                    t.value = '';
                }
                filterOtherEmergencyTable();
            }

            function filterOtherEmergencyTable() {
                const typeFilter = document.getElementById('emergencyTypeFilter').value.toLowerCase();
                const statusFilter = document.getElementById('otherStatusFilter').value.toLowerCase();
                const locationSearch = document.getElementById('otherLocationSearch').value.toLowerCase();

                const mode = document.getElementById('otherDateTimeFilter').value;
                const dateSearch = mode === 'date' ? document.getElementById('otherDateSearch').value : '';
                const timeSearch = mode === 'time' ? document.getElementById('otherTimeSearch').value : '';

                const rows = document.querySelectorAll('#otherEmergencyTableBody tr');

                rows.forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report'));
                    const matchesType = !typeFilter || ((report.emergencyType || '').toLowerCase() === typeFilter);
                    const matchesStatus = !statusFilter || (report.status && report.status.toLowerCase() ===
                        statusFilter);
                    const matchesLocation = !locationSearch || ((report.exactLocation || '').toLowerCase().includes(
                        locationSearch));

                    const reportDateISO = (() => {
                        if (!report.date) return '';
                        const parts = report.date.split('/');
                        if (parts.length === 3)
                            return `${parts[2].length === 2 ? '20' + parts[2] : parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        return '';
                    })();

                    const matchesDate = !dateSearch || (reportDateISO === dateSearch);

                    const matchesTime = (() => {
                        if (!timeSearch) return true;
                        const t = to24h(report.reportTime || '');
                        return !!t && t === timeSearch;
                    })();

                    row.style.display = (matchesType && matchesStatus && matchesLocation && matchesDate &&
                        matchesTime) ? '' : 'none';
                });
            }

            function handleAllDateTimeFilterChange() {
                const mode = document.getElementById('allDateTimeFilter').value;
                const d = document.getElementById('allDateSearch');
                const t = document.getElementById('allTimeSearch');

                if (mode === 'date') {
                    d.classList.remove('hidden');
                    t.classList.add('hidden');
                    t.value = '';
                } else if (mode === 'time') {
                    t.classList.remove('hidden');
                    d.classList.add('hidden');
                    d.value = '';
                } else {
                    d.classList.add('hidden');
                    t.classList.add('hidden');
                    d.value = '';
                    t.value = '';
                }

                filterAllReportsTable();
            }

            function filterAllReportsTable() {
                const typeQ = (document.getElementById('allTypeSearch')?.value || '').toLowerCase();
                const locQ = (document.getElementById('allLocationSearch')?.value || '').toLowerCase();
                const statusQ = (document.getElementById('allStatusFilter')?.value || '').toLowerCase();
                const mode = document.getElementById('allDateTimeFilter')?.value || 'all';
                const dateQ = mode === 'date' ? (document.getElementById('allDateSearch')?.value || '') : '';
                const timeQ = mode === 'time' ? (document.getElementById('allTimeSearch')?.value || '') : '';

                const rows = document.querySelectorAll('#allReportsBody tr');
                rows.forEach(row => {
                    const tds = row.querySelectorAll('td');
                    const typeTxt = (tds[1]?.textContent || '').toLowerCase();
                    const loc = (tds[2]?.textContent || '').toLowerCase();
                    const dtText = (tds[3]?.textContent || '').trim();
                    const status = (tds[4]?.textContent || '').toLowerCase();

                    let okDate = true;
                    if (dateQ) {
                        const dmy = (dtText.split(' ')[0] || '');
                        const iso = dateToISO(dmy);
                        okDate = (iso === dateQ);
                    }

                    let okTime = true;
                    if (timeQ) {
                        const rawTime = (dtText.split(' ')[1] || '');
                        const norm = to24h(rawTime) || rawTime;
                        okTime = (norm === timeQ);
                    }

                    const okType = !typeQ || typeTxt.includes(typeQ);
                    const okLoc = !locQ || loc.includes(locQ);
                    const okStatus = !statusQ || status === statusQ;

                    row.style.display = (okType && okLoc && okStatus && okDate && okTime) ? '' : 'none';
                });
            }

            function handleSmsDateTimeFilterChange() {
                const mode = document.getElementById('smsDateTimeFilter').value;
                const d = document.getElementById('smsDateSearch');
                const t = document.getElementById('smsTimeSearch');

                if (mode === 'date') {
                    d.classList.remove('hidden');
                    t.classList.add('hidden');
                    t.value = '';
                } else if (mode === 'time') {
                    t.classList.remove('hidden');
                    d.classList.add('hidden');
                    d.value = '';
                } else {
                    d.classList.add('hidden');
                    t.classList.add('hidden');
                    d.value = '';
                    t.value = '';
                }
                filterSmsReportsTable();
            }

            function filterSmsReportsTable() {
                const qLoc = (document.getElementById('smsLocationSearch')?.value || '').toLowerCase();
                const mode = document.getElementById('smsDateTimeFilter')?.value || 'all';
                const dateQ = mode === 'date' ? (document.getElementById('smsDateSearch')?.value || '') : '';
                const timeQ = mode === 'time' ? (document.getElementById('smsTimeSearch')?.value || '') : '';
                const statQ = (document.getElementById('smsStatusFilter')?.value || '').toLowerCase();

                const rows = document.querySelectorAll('#smsReportsBody tr');
                rows.forEach(row => {
                    const tds = row.querySelectorAll('td');
                    const loc = (tds[1]?.textContent || '').toLowerCase();
                    const dtText = (tds[2]?.textContent || '').trim().replace(/\s+/g, ' ').trim();
                    const status = (tds[3]?.textContent || '').toLowerCase();

                    let okDate = true;
                    if (dateQ) {
                        const d = (dtText.split(' ')[0] || '');
                        okDate = (d === dateQ);
                    }

                    let okTime = true;
                    if (timeQ) {
                        const rawTime = (dtText.split(' ')[1] || '');
                        okTime = rawTime.startsWith(timeQ);
                    }

                    const okLoc = !qLoc || loc.includes(qLoc);
                    const okStatus = !statQ || status === statQ;

                    row.style.display = (okLoc && okDate && okTime && okStatus) ? '' : 'none';
                });
            }


            /* =========================================================
             * UX HELPERS (FOCUS PICKERS, NORMALIZE INITIAL)
             * ========================================================= */

            function focusFireDatePicker() {
                const sel = document.getElementById('fireDateTimeFilter');
                if (sel) sel.value = 'date';
                handleDateTimeFilterChange();
                const input = document.getElementById('fireDateSearch');
                if (!input) return;
                input.classList.remove('hidden');
                if (typeof input.showPicker === 'function') input.showPicker();
                input.focus();
            }

            function focusOtherDatePicker() {
                const sel = document.getElementById('otherDateTimeFilter');
                if (sel) sel.value = 'date';
                handleOtherDateTimeFilterChange();
                const input = document.getElementById('otherDateSearch');
                if (!input) return;
                input.classList.remove('hidden');
                if (typeof input.showPicker === 'function') input.showPicker();
                input.focus();
            }

            function focusEmsDatePicker() {
                const sel = document.getElementById('emsDateTimeFilter');
                if (!sel) return;
                sel.value = 'date';
                handleEmsDateTimeFilterChange();
                const input = document.getElementById('emsDateSearch');
                if (!input) return;
                input.classList.remove('hidden');
                if (typeof input.showPicker === 'function') input.showPicker();
                input.focus();
            }

            function handleEmsDateTimeFilterChange() {
                const mode = document.getElementById('emsDateTimeFilter').value;
                const d = document.getElementById('emsDateSearch');
                const t = document.getElementById('emsTimeSearch');

                if (mode === 'date') {
                    d.classList.remove('hidden');
                    t.classList.add('hidden');
                } else if (mode === 'time') {
                    t.classList.remove('hidden');
                    d.classList.add('hidden');
                } else {
                    d.classList.add('hidden');
                    t.classList.add('hidden');
                    d.value = '';
                    t.value = '';
                }
                filterEmsTable();
            }

            function filterEmsTable() {
                const typeQ = (document.getElementById('emsTypeSearch')?.value || '').toLowerCase();
                const locQ = (document.getElementById('emsLocationSearch')?.value || '').toLowerCase();
                const statusQ = (document.getElementById('emsStatusFilter')?.value || '').toLowerCase();
                const mode = document.getElementById('emsDateTimeFilter')?.value || 'all';
                const dateQ = mode === 'date' ? (document.getElementById('emsDateSearch')?.value || '') : '';
                const timeQ = mode === 'time' ? (document.getElementById('emsTimeSearch')?.value || '') : '';

                const rows = document.querySelectorAll('#emsBody tr');
                rows.forEach(row => {
                    const rpt = JSON.parse(row.getAttribute('data-report') || '{}');
                    const okType = !typeQ || ((rpt.type || '').toLowerCase().includes(typeQ));
                    const okLoc = !locQ || ((rpt.exactLocation || '').toLowerCase().includes(locQ));
                    const okStatus = !statusQ || ((rpt.status || '').toLowerCase() === statusQ);

                    let okDate = true;
                    if (dateQ) {
                        const iso = dateToISO(rpt.date || '');
                        okDate = (iso === dateQ);
                    }

                    let okTime = true;
                    if (timeQ) {
                        const norm = to24h(rpt.reportTime || '') || '';
                        okTime = norm === timeQ;
                    }

                    row.style.display = (okType && okLoc && okStatus && okDate && okTime) ? '' : 'none';
                });
            }

            function normalizeInitialTimes() {
                // Fire rows
                document.querySelectorAll('#fireReportsBody tr').forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report') || '{}');
                    if (report.reportTime) {
                        const d = report.date || 'N/A';
                        const t = to24h(report.reportTime) || 'N/A';
                        row.children[3].textContent = `${d} ${t}`;
                    }
                });

                // Other Emergency rows
                document.querySelectorAll('#otherEmergencyTableBody tr').forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report') || '{}');
                    if (report.reportTime) {
                        const d = report.date || 'N/A';
                        const t = to24h(report.reportTime) || 'N/A';
                        row.children[3].textContent = `${d} ${t}`;
                    }
                });

                // EMS rows
                renderEmsTable();
            }


            /* =========================================================
             * VISIBILITY / SECTION TOGGLE
             * ========================================================= */

            function toggleIncidentTables() {
                const v = document.getElementById('incidentType').value;

                // hide all
                document.getElementById('allReportsSection').classList.add('hidden');
                document.getElementById('fireReportsSection').classList.add('hidden');
                document.getElementById('otherEmergencySection').classList.add('hidden');
                document.getElementById('emsSection').classList.add('hidden');
                document.getElementById('smsReportsSection').classList.add('hidden');
                document.getElementById('fireFighterChatSection').classList.add('hidden');

                // show selected
                if (v === 'allReports') {
                    document.getElementById('allReportsSection').classList.remove('hidden');
                    renderAllReports();
                } else if (v === 'fireReports') {
                    document.getElementById('fireReportsSection').classList.remove('hidden');
                } else if (v === 'otherEmergency') {
                    document.getElementById('otherEmergencySection').classList.remove('hidden');
                } else if (v === 'emsReports') {
                    document.getElementById('emsSection').classList.remove('hidden');
                    renderEmsTable();
                } else if (v === 'smsReports') {
                    document.getElementById('smsReportsSection').classList.remove('hidden');
                } else if (v === 'fireFighterChatReports') {
                    document.getElementById('fireFighterChatSection').classList.remove('hidden');
                    loadFireFighterAccountRow();
                    // <- ensure paint when user opens the tab
                }
            }


            /* =========================================================
             * DETAILS MODAL (OPEN/CLOSE + STATUS BUTTONS)
             * ========================================================= */

            function openDetailsModal(incidentId, reportType) {
                // resolve the full report object
                let full = null;
                if (reportType === 'fireReports') full = (fireReports || []).find(r => r.id === incidentId);
                else if (reportType === 'otherEmergency') full = (otherEmergencyReports || []).find(r => r.id === incidentId);
                else if (reportType === 'emsReports') full = (emsReports || []).find(r => r.id === incidentId);
                else if (reportType === 'smsReports') full = (smsReports || []).find(r => r.id === incidentId);

                if (!full) {
                    const rowFromSection =
                        document.getElementById(`reportRow${incidentId}`) ||
                        document.querySelector(`#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`);
                    if (!rowFromSection) return;
                    try {
                        full = JSON.parse(rowFromSection.getAttribute('data-report') || '{}');
                    } catch {
                        full = {};
                    }
                    full.id = full.id || incidentId;
                }

                // helpers
                const pick = (...ks) => {
                    for (const k of ks)
                        if (full[k] != null && String(full[k]).trim() !== '') return full[k];
                    return 'N/A';
                };
                const t24 = (v) => to24h(v) || v || 'N/A';

                // hide all detail panels first
                document.getElementById('fireReportDetails').classList.add('hidden');
                document.getElementById('otherEmergencyDetails').classList.add('hidden');
                document.getElementById('emsDetails').classList.add('hidden');
                document.getElementById('smsDetails').classList.add('hidden');
                document.getElementById('smsExtra').classList.add('hidden');

                // ----- FIRE -----
                if (reportType === 'fireReports') {
                    document.getElementById('detailName').innerText = pick('name', 'reporterName', 'userName');
                    document.getElementById('detailContact').innerText = pick('contact', 'phone', 'phoneNumber', 'mobile');
                    document.getElementById('detailFireType').innerText = pick('type');
                    document.getElementById('detailLocation').innerText = pick('exactLocation', 'location', 'address');
                    document.getElementById('detailDate').innerText = pick('date');
                    document.getElementById('detailReportTime').innerText = t24(pick('reportTime'));
                    document.getElementById('detailStatus').innerText = capStatus(pick('status'));

                    const b64 = (full.photoBase64 || '').toString().trim();
                    document.getElementById('detailFirePhoto').innerHTML = b64 ?
                        `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="Photo">` : '';

                    document.getElementById('fireReportDetails').classList.remove('hidden');
                }

                // ----- OTHER EMERGENCY -----
                else if (reportType === 'otherEmergency') {
                    document.getElementById('detailNameOther').innerText = pick('name', 'reporterName', 'userName');
                    document.getElementById('detailContactOther').innerText = pick('contact', 'phone', 'phoneNumber', 'mobile');
                    document.getElementById('detailEmergencyType').innerText = pick('emergencyType', 'type');
                    document.getElementById('detailLocationOther').innerText = pick('exactLocation', 'location', 'address');
                    document.getElementById('detailDateOther').innerText = pick('date');
                    document.getElementById('detailReportTimeOther').innerText = t24(pick('reportTime'));
                    document.getElementById('detailStatusOther').innerText = capStatus(pick('status'));

                    const b64 = (full.photoBase64 || '').toString().trim();
                    document.getElementById('detailOtherPhoto').innerHTML = b64 ?
                        `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="Incident Photo">` : '';

                    document.getElementById('otherEmergencyDetails').classList.remove('hidden');
                }

                // ----- EMS -----
                else if (reportType === 'emsReports') {
                    document.getElementById('detailNameEms').innerText = pick('name', 'reporterName', 'userName');
                    document.getElementById('detailContactEms').innerText = pick('contact', 'phone', 'phoneNumber', 'mobile');
                    document.getElementById('detailTypeEms').innerText = pick('type');
                    document.getElementById('detailLocationEms').innerText = pick('exactLocation', 'location', 'address');
                    document.getElementById('detailDateEms').innerText = pick('date');
                    document.getElementById('detailReportTimeEms').innerText = t24(pick('reportTime'));
                    document.getElementById('detailStatusEms').innerText = capStatus(pick('status'));

                    const b64 = (full.photoBase64 || '').toString().trim();
                    document.getElementById('detailEmsPhoto').innerHTML = b64 ?
                        `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="EMS Photo">` : '';

                    document.getElementById('emsDetails').classList.remove('hidden');
                }

                // ----- SMS -----
                else if (reportType === 'smsReports') {
                    // Basic info
                    document.getElementById('detailNameSms').innerText = pick('name', 'reporterName', 'userName');
                    document.getElementById('detailContactSms').innerText = pick('contact', 'phone', 'phoneNumber', 'mobile');

                    // Report text
                    document.getElementById('detailSmsReportText').innerText =
                        pick('fireReport', 'message', 'reportText', 'details', 'description');

                    // Date / Time / Status
                    document.getElementById('detailDateSms').innerText = pick('date'); // e.g., 10/13/2025
                    document.getElementById('detailReportTimeSms').innerText = t24(pick('time', 'reportTime'));
                    document.getElementById('detailStatusSms').innerText = capStatus(pick('status'));

                    // Location
                    const loc = pick('location', 'exactLocation', 'address');
                    const locEl = document.getElementById('detailLocationSms');
                    locEl.innerText = loc || 'N/A';
                    locEl.title = loc && loc !== 'N/A' ? String(loc) : '';



                    // Show the details panel
                    document.getElementById('smsDetails').classList.remove('hidden');
                }

                // ----- Status action button (works for ALL types, including SMS) -----
                // ----- Status action button (works for ALL types, including SMS) -----
                const statusActionDiv = document.getElementById('statusActionButtons');
                statusActionDiv.innerHTML = '';

                const curStatus = capStatus(full.status || 'Pending');

                if (curStatus === 'Pending') {
                    // RECEIVE → open assignment modal
                    const btn = document.createElement('button');
                    btn.id = `acceptButton${full.id}`;
                    btn.className = 'px-4 py-2 rounded mt-2 text-white';
                    btn.style.backgroundColor = '#F3C011';
                    btn.onmouseenter = () => (btn.style.backgroundColor = '#d1a500');
                    btn.onmouseleave = () => (btn.style.backgroundColor = '#F3C011');
                    btn.textContent = 'Receive';
                    btn.onclick = () => openAssignModal(full.id, reportType, 'receive');

                    statusActionDiv.appendChild(btn);

                } else if (curStatus === 'Ongoing') {
                    // REQUEST BACKUP → open assignment modal
                    const btnBackup = document.createElement('button');
                    btnBackup.type = 'button'; // 👈 add this
                    btnBackup.className = 'px-4 py-2 rounded mt-2 text-white mr-3';
                    btnBackup.style.backgroundColor = '#F3C011'; // amber
                    btnBackup.onmouseenter = () => (btnBackup.style.backgroundColor = '#d1a500');
                    btnBackup.onmouseleave = () => (btnBackup.style.backgroundColor = '#F3C011');
                    btnBackup.textContent = 'Request Backup';
                    btnBackup.onclick = () => openAssignModal(full.id, reportType, 'backup');
                    statusActionDiv.appendChild(btnBackup);

                    // DONE → mark Completed
                    const btn = document.createElement('button');
                    btn.id = `acceptButton${full.id}`;
                    btn.className = 'px-4 py-2 rounded mt-2 text-white';
                    btn.style.backgroundColor = '#22c55e';
                    btn.onmouseenter = () => (btn.style.backgroundColor = '#16a34a');
                    btn.onmouseleave = () => (btn.style.backgroundColor = '#22c55e');
                    btn.textContent = 'Done';
                    btn.onclick = () => updateReportStatus(full.id, reportType, 'Completed');
                    statusActionDiv.appendChild(btn);
                }



                // finally, show the modal
                document.getElementById('detailsModal').classList.remove('hidden');
            }



            /* =========================================================
             * ASSIGN-ON-RECEIVE: MODAL + WRITE + STATUS FLIP (FINAL)
             * ========================================================= */

            let __assignContext = {
                incidentId: null,
                reportType: null,
                reportObject: null,
                mode: 'backup'
            };

            /* ---------- Station / Account helpers ---------- */

            // Active station root, e.g. "CapstoneFlare/CanocotanFireStation"
            function currentStationRoot() {
                return (typeof window !== 'undefined' && window.CURRENT_STATION_ROOT) ||
                    'CapstoneFlare/CanocotanFireStation';
            }

            // Last segment only, e.g. "CanocotanFireStation"
            function currentStationKey() {
                const parts = (currentStationRoot() || '').split('/').filter(Boolean);
                return parts[parts.length - 1] || '';
            }

            // Fallback FF account key by station node
            function ffKeyFallbackFromStationRoot(root) {
                const last = (root || '').split('/').filter(Boolean).pop();
                return last === 'CanocotanFireStation' ? 'CanocotanFireFighterAccount' :
                    last === 'LaFilipinaFireStation' ? 'LaFilipinaFireFighterAccount' :
                    last === 'MabiniFireStation' ? 'MabiniFireFighterAccount' :
                    '';
            }

            function currentFireFighterAccountKey() {
                const fromWindow = (typeof CURRENT_FF_ACCOUNT_KEY === 'string' && CURRENT_FF_ACCOUNT_KEY) ||
                    (typeof window !== 'undefined' && window.CURRENT_FF_ACCOUNT_KEY);
                if (fromWindow) return fromWindow;
                return ffKeyFallbackFromStationRoot(currentStationRoot());
            }

            // Given an <input>, figure out which station root + account key it targets.
            // Supports either "root::accountKey" in value OR value=accountKey + data-root attribute.
            function parseTargetFromInput(inputEl) {
                const raw = (inputEl.value || '').trim();
                if (raw.includes('::')) {
                    const [root, accountKey] = raw.split('::');
                    return {
                        root: root.trim(),
                        accountKey: (accountKey || '').trim()
                    };
                }
                const dataRoot = (inputEl.getAttribute('data-root') || '').trim();
                return {
                    root: dataRoot || currentStationRoot(),
                    accountKey: raw
                };
            }

            // Hide/disable the current station’s accounts in backup chooser
            function excludeCurrentStationFromChoices(containerEl) {
                try {
                    const me = currentStationKey();
                    if (!containerEl || !me) return;

                    containerEl.querySelectorAll('input[name="station[]"]').forEach(cb => {
                        const v = (cb.value || '').trim();
                        let stationLastSeg = '';
                        if (v.includes('::')) {
                            const root = v.split('::')[0].trim();
                            const parts = root.split('/').filter(Boolean);
                            stationLastSeg = parts[parts.length - 1] || '';
                        } else {
                            const root = (cb.getAttribute('data-root') || '').trim();
                            const parts = root.split('/').filter(Boolean);
                            stationLastSeg = parts[parts.length - 1] || '';
                        }
                        if (stationLastSeg === me) {
                            cb.checked = false;
                            cb.disabled = true;
                            const row = cb.closest('label,div,li,tr') || cb;
                            row.classList.add('hidden');
                        }
                    });
                } catch (_) {}
            }

            /* ---------- Type helpers ---------- */

            function accountsBase(root) {
                return `${root}/FireFighter/FireFighterAccount`;
            }

            // Determine whether a station keeps a single FF node or multiple keyed nodes
            async function resolveAccountShape(root) {
                const base = accountsBase(root);
                const snap = await firebase.database().ref(base).once('value');
                const v = snap.val();

                if (!v || typeof v !== 'object') {
                    return {
                        shape: 'multi',
                        base,
                        key: ffKeyFallbackFromStationRoot(root)
                    };
                }

                const keys = Object.keys(v);
                const looksSingle =
                    ('name' in v || 'contact' in v || 'email' in v) &&
                    !keys.some(k => v[k] && typeof v[k] === 'object' && ('AllReport' in v[k] || 'name' in v[k]));

                if (looksSingle) return {
                    shape: 'single',
                    base
                };

                const someKey = keys.find(k => v[k] && typeof v[k] === 'object') || ffKeyFallbackFromStationRoot(root);
                return {
                    shape: 'multi',
                    base,
                    key: someKey
                };
            }

            function typeNodeFor(reportType) {
                return reportType === 'fireReports' ? 'FireReport' :
                    reportType === 'otherEmergency' ? 'OtherEmergencyReport' :
                    reportType === 'emsReports' ? 'EmergencyMedicalServicesReport' :
                    reportType === 'smsReports' ? 'SmsReport' :
                    'Unknown';
            }

            /* ---------- Inventory / “already has it?” checks ---------- */

            // Does this station (in any FF account) already have the incident?
            async function stationHasReport(root, reportType, incidentId) {
                const t = typeNodeFor(reportType);
                const base = accountsBase(root);

                // Check single-shape path
                const single = await firebase.database()
                    .ref(`${base}/AllReport/${t}/${incidentId}`)
                    .once('value');
                if (single.exists()) return true;

                // Check multi-shape children (any key)
                const listSnap = await firebase.database().ref(base).once('value');
                const obj = listSnap.val() || {};
                const keys = Object.keys(obj).filter(k => obj[k] && typeof obj[k] === 'object');

                for (const k of keys) {
                    const s = await firebase.database()
                        .ref(`${base}/${k}/AllReport/${t}/${incidentId}`)
                        .once('value');
                    if (s.exists()) return true;
                }
                return false;
            }

            /* ---------- Core write (direct-to-FFAccount) ---------- */
            /* targets: [{ root: 'CapstoneFlare/MabiniFireStation', accountKey?: '...' }, ...] */
            async function writeAssignmentsDirect({
                incidentId,
                reportType,
                reportObject,
                targets
            }) {
                const typeNode = typeNodeFor(reportType);
                if (typeNode === 'Unknown') throw new Error('Unknown report type.');

                const writes = [];
                const accountKeysWritten = [];

                for (const tgt of targets) {
                    const {
                        root
                    } = tgt;
                    if (!root) continue;

                    const shapeInfo = await resolveAccountShape(root);
                    let refPath;

                    if (shapeInfo.shape === 'single') {
                        refPath = `${shapeInfo.base}/AllReport/${typeNode}/${incidentId}`;
                        accountKeysWritten.push('single');
                    } else {
                        const key = tgt.accountKey || shapeInfo.key || ffKeyFallbackFromStationRoot(root);
                        refPath = `${shapeInfo.base}/${key}/AllReport/${typeNode}/${incidentId}`;
                        accountKeysWritten.push(key);
                    }

                    const payload = {
                        ...reportObject,
                        assignedStationAccounts: accountKeysWritten,
                        assignedAt: Date.now(),
                        status: 'Ongoing'
                    };
                    writes.push(firebase.database().ref(refPath).set(payload));
                }

                const results = await Promise.allSettled(writes);
                const fails = results.filter(r => r.status === 'rejected');
                if (fails.length === results.length) throw new Error('All assignments failed.');

                const n = nodes?.();
                const srcPath =
                    reportType === 'fireReports' ? n?.fireReport :
                    reportType === 'otherEmergency' ? n?.otherEmergency :
                    reportType === 'emsReports' ? n?.ems :
                    reportType === 'smsReports' ? n?.sms : null;

                if (srcPath) {
                    await firebase.database().ref(`${srcPath}/${incidentId}`).update({
                        status: 'Ongoing',
                        assignedStationAccounts: accountKeysWritten
                    });
                }

                try {
                    setStatusEverywhere?.(incidentId, reportType, 'Ongoing');
                    const ok = results.length - fails.length;
                    showToast?.(`Assigned to ${ok} station${ok>1?'s':''}${fails.length?` (${fails.length} failed)`:''}`);
                } catch {}
                if (fails.length) alert(`${fails.length} assignment(s) failed. See console.`);
            }

            /* ---------- Optional account fetchers (kept intact) ---------- */

            async function fetchAccounts(root) {
                const snap = await firebase.database().ref(accountsBase(root)).once('value');
                const obj = snap.val() || {};
                if (Object.keys(obj).length === 0) return [];
                if (!Object.values(obj).some(v => v && typeof v === 'object' && 'name' in v)) {
                    return [{
                        key: '',
                        name: obj.name || 'Fire Fighter',
                        contact: obj.contact || ''
                    }];
                }
                return Object.keys(obj).map(k => ({
                    key: k,
                    name: obj[k]?.name || k,
                    contact: obj[k]?.contact || ''
                }));
            }

            async function alreadyAssigned(root, accountKey, reportType, incidentId) {
                const t = typeNodeFor(reportType);
                const p = `${root}/FireFighter/FireFighterAccount/${accountKey}/AllReport/${t}/${incidentId}`;
                const s = await firebase.database().ref(p).once('value');
                return s.exists();
            }

            /* ---------- UI helpers ---------- */

            function getSelectedInputs() {
                return Array.from(document.querySelectorAll('#assignForm input[name="station[]"]:checked'));
            }

            function updateAssignButton() {
                const btn = document.getElementById('assignSubmitBtn');
                const any = getSelectedInputs().length > 0;
                if (!btn) return;
                btn.disabled = !any;
                btn.classList.toggle('opacity-50', !any);
                btn.classList.toggle('cursor-not-allowed', !any);
            }

            /* ---------- Open Assign Modal (receive vs backup) ---------- */

            function openAssignModal(incidentId, reportType, mode = 'backup') {
                const row = document.getElementById(`reportRow${incidentId}`) ||
                    document.querySelector(`#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`);
                let rpt = {};
                try {
                    rpt = JSON.parse(row?.getAttribute('data-report') || '{}');
                } catch {}
                rpt.id = rpt.id || incidentId;

                __assignContext = {
                    incidentId,
                    reportType,
                    reportObject: rpt,
                    mode
                };

                // RECEIVE → auto-assign to my own station without showing modal
                if (mode === 'receive') {
                    const meRoot = currentStationRoot();
                    const meFF = currentFireFighterAccountKey();
                    if (!meRoot || !meFF) {
                        alert('Missing station/account context.');
                        return;
                    }

                    writeAssignmentsDirect({
                            incidentId,
                            reportType,
                            reportObject: rpt,
                            targets: [{
                                root: meRoot,
                                accountKey: meFF
                            }]
                        }).then(() => {
                            closeAssignModal?.();
                            closeDetailsModal?.();
                        })
                        .catch(e => alert(e?.message || 'Failed to assign.'));
                    return;
                }

                // BACKUP → show modal with other stations only
                const modal = document.getElementById('assignModal');
                const receiveBox = document.getElementById('receiveSection');
                const backupBox = document.getElementById('backupSection');

                receiveBox?.classList.add('hidden');
                backupBox?.classList.remove('hidden');
                modal?.classList.remove('hidden');

                renderBackupChooser({
                        incidentId,
                        reportType
                    })
                    .catch(e => {
                        console.error(e);
                        if (backupBox) backupBox.innerHTML =
                            `<div class="text-sm text-red-600">Could not load accounts.</div>`;
                    });
            }

            /* ---------- Submit: take checked stations and write ---------- */

            (function wireAssignSubmit() {
                const form = document.getElementById('assignForm');
                if (!form) return;

                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const btn = document.getElementById('assignSubmitBtn');
                    const checks = Array.from(form.querySelectorAll('input[name="station[]"]:checked'));
                    if (!checks.length) return;

                    const {
                        incidentId,
                        reportType,
                        reportObject
                    } = __assignContext || {};
                    if (!incidentId || !reportType) {
                        alert('Missing context.');
                        return;
                    }

                    // targets are just station roots; accountKey resolved per station
                    const targets = checks.map(cb => ({
                        root: cb.value
                    }));

                    try {
                        btn.disabled = true;
                        btn.classList.add('opacity-60', 'cursor-wait');
                        await writeAssignmentsDirect({
                            incidentId,
                            reportType,
                            reportObject,
                            targets
                        });
                        closeAssignModal();
                        closeDetailsModal();
                    } catch (err) {
                        console.error(err);
                        alert(err?.message || 'Failed to assign.');
                    } finally {
                        btn.disabled = false;
                        btn.classList.remove('opacity-60', 'cursor-wait');
                    }
                });
            })();

            /* ---------- Backup chooser (station-level; show NODE names only) ---------- */

            async function renderBackupChooser({
                incidentId,
                reportType
            }) {
                const me = currentStationRoot();
                const others = STATION_ROOTS.filter(r => r !== me);

                const box = document.getElementById('backupSection');
                const btn = document.getElementById('assignSubmitBtn');
                box.innerHTML = '';
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');

                // Filter out stations that already have the report
                const eligibleRoots = [];
                for (const root of others) {
                    const has = await stationHasReport(root, reportType, __assignContext.incidentId);
                    if (!has) eligibleRoots.push(root);
                }

                if (!eligibleRoots.length) {
                    try {
                        showToast?.('Already assigned to all stations.');
                    } catch {}
                    closeAssignModal?.();
                    return;
                }

                // Build simple station-name checkboxes (node names only, no numbers)
                eligibleRoots.forEach(root => {
                    const nodeName = root.split('/').pop(); // e.g., "MabiniFireStation"
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-2 py-1';
                    label.innerHTML = `
      <input type="checkbox" name="station[]" value="${root}" class="h-4 w-4">
      <span class="text-sm font-medium">${nodeName}</span>
    `;
                    box.appendChild(label);
                });

                // Enable the Assign button when any is checked
                box.querySelectorAll('input[name="station[]"]').forEach(cb => {
                    cb.addEventListener('change', () => {
                        const any = box.querySelector('input[name="station[]"]:checked');
                        btn.disabled = !any;
                        btn.classList.toggle('opacity-50', !any);
                        btn.classList.toggle('cursor-not-allowed', !any);
                    });
                });
            }

            // parse value "root::accountKey::shape" (kept for legacy)
            function parseTargetFromInput(inputEl) {
                const raw = (inputEl.value || '').trim();
                const [root, accountKey, shape] = raw.split('::');
                return {
                    root,
                    accountKey: accountKey || '',
                    shape: shape || 'multi'
                };
            }

            /* ---------- Close modal ---------- */

            function closeAssignModal() {
                document.getElementById('assignModal')?.classList.add('hidden');
                __assignContext = {
                    incidentId: null,
                    reportType: null,
                    reportObject: null,
                    mode: 'backup'
                };
            }

            /* ---------- Safety net: legacy submit handler (backup-mode behavior) ---------- */
            /* NOTE: No-op when our new station-only checkboxes are used (no '::' in value). */

            (() => {
                const form = document.getElementById('assignForm');
                if (!form) return;

                excludeCurrentStationFromChoices(form);
                updateAssignButton();

                form.addEventListener('submit', async (e) => {
                    // Detect new flow (values are just station roots). If so, let the main handler do it.
                    const rawVals = Array.from(form.querySelectorAll('input[name="station[]"]:checked')).map(
                        cb => cb.value || '');
                    if (rawVals.length && rawVals.every(v => !String(v).includes('::'))) {
                        return; // new handler already processed
                    }

                    // Legacy flow below (values like "root::accountKey::shape")
                    e.preventDefault();

                    const btn = document.getElementById('assignSubmitBtn');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-60', 'cursor-wait');
                    }

                    const inputs = getSelectedInputs();
                    const targets = inputs.map(parseTargetFromInput).filter(t => t.root && t.accountKey);
                    const {
                        incidentId,
                        reportType,
                        reportObject
                    } = __assignContext || {};

                    if (!targets.length) {
                        alert('Please select at least one FireFighter account for backup.');
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('opacity-60', 'cursor-wait');
                        }
                        return;
                    }
                    if (!incidentId || !reportType) {
                        alert('Missing context for assignment.');
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('opacity-60', 'cursor-wait');
                        }
                        return;
                    }

                    try {
                        await writeAssignmentsDirect({
                            incidentId,
                            reportType,
                            reportObject,
                            targets
                        });
                        queueMicrotask(() => {
                            closeAssignModal();
                            closeDetailsModal();
                        });
                    } catch (err) {
                        console.error('[assign][legacy] error:', err);
                        alert(err?.message || 'Failed to assign.');
                    } finally {
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('opacity-60', 'cursor-wait');
                        }
                    }
                }, {
                    once: true
                });
            })();

            /* =========================================================
             * STATUS FAN-OUT (unchanged behavior for central lists)
             * ========================================================= */

            /* =========================================================
             * FAN-OUT STATUS TO THE 3 STATIONS (station-only, no "central")
             * - Updates station AllReport item + any FFAccount copies that exist
             * - Skips non-existent nodes (no rule errors)
             * - Clear toast summary
             * ========================================================= */

            function __allReportTypePath(reportType, base) {
                return reportType === 'fireReports' ? `${base}/FireReport` :
                    reportType === 'otherEmergency' ? `${base}/OtherEmergencyReport` :
                    reportType === 'emsReports' ? `${base}/EmergencyMedicalServicesReport` :
                    `${base}/SmsReport`;
            }

            async function __updateStationAllReportStatus(stationRoot, reportType, incidentId, statusLabel) {
                const base = `${stationRoot}/AllReport`;
                const typePath = __allReportTypePath(reportType, base);
                const itemRef = firebase.database().ref(`${typePath}/${incidentId}`);

                try {
                    const snap = await itemRef.once('value');
                    if (!snap.exists()) return {
                        ok: false,
                        reason: 'not_found'
                    };
                    await itemRef.child('status').set(statusLabel);
                    return {
                        ok: true
                    };
                } catch (e) {
                    console.warn('[fanOut][AllReport]', stationRoot, e?.code || e?.message || e);
                    return {
                        ok: false,
                        reason: e?.code || 'write_failed'
                    };
                }
            }

            async function __updateStationFFCopies(stationRoot, reportType, incidentId, statusLabel) {
                const baseFFA = accountsBase(stationRoot);
                const typeNode = typeNodeFor(reportType);
                let touched = 0;
                const writes = [];

                try {
                    // single shape
                    const singlePath = `${baseFFA}/AllReport/${typeNode}/${incidentId}`;
                    const singleSnap = await firebase.database().ref(singlePath).once('value');
                    if (singleSnap.exists()) {
                        writes.push(firebase.database().ref(`${singlePath}/status`).set(statusLabel));
                        touched++;
                    }

                    // multi shape
                    const listSnap = await firebase.database().ref(baseFFA).once('value');
                    const obj = listSnap.val() || {};
                    const keys = Object.keys(obj).filter(k => obj[k] && typeof obj[k] === 'object');
                    for (const k of keys) {
                        const path = `${baseFFA}/${k}/AllReport/${typeNode}/${incidentId}`;
                        const s = await firebase.database().ref(path).once('value');
                        if (s.exists()) {
                            writes.push(firebase.database().ref(`${path}/status`).set(statusLabel));
                            touched++;
                        }
                    }

                    if (writes.length) await Promise.allSettled(writes);
                    return {
                        ok: true,
                        touched
                    };
                } catch (e) {
                    console.warn('[fanOut][FFCopies]', stationRoot, e?.code || e?.message || e);
                    return {
                        ok: false,
                        reason: e?.code || 'write_failed',
                        touched
                    };
                }
            }

            async function fanOutStatus(incidentId, reportType, newStatus) {
                const statusLabel = capStatus(newStatus);
                const results = [];

                for (const stationRoot of STATION_ROOTS) {
                    const allReport = await __updateStationAllReportStatus(stationRoot, reportType, incidentId,
                        statusLabel);
                    const ffCopies = await __updateStationFFCopies(stationRoot, reportType, incidentId, statusLabel);
                    const okForStation = !!(allReport.ok || (ffCopies.touched > 0)); // station counted if any copy updated
                    results.push({
                        stationRoot,
                        okForStation,
                        allReport,
                        ffCopies
                    });
                }

                const okStations = results.filter(r => r.okForStation).length;
                return {
                    okStations,
                    totalStations: STATION_ROOTS.length,
                    details: results
                };
            }

            async function updateReportStatus(incidentId, reportType, newStatus) {
                try {
                    const summary = await fanOutStatus(incidentId, reportType, newStatus);

                    // reflect in UI
                    setStatusEverywhere(incidentId, reportType, newStatus);
                    closeDetailsModal?.();

                    // toast summary
                    if (summary.okStations === 0) {
                        showToast?.('No station copy updated (item not found or blocked by rules).');
                    } else if (summary.okStations < summary.totalStations) {
                        showToast?.(
                            `Status set to ${capStatus(newStatus)} (${summary.okStations}/${summary.totalStations} stations updated).`
                        );
                    } else {
                        showToast?.(`Status updated to ${capStatus(newStatus)} across all stations.`);
                    }
                } catch (err) {
                    console.error('[status] fanOut failed:', err);
                    alert('Failed to update status. See console for details.');
                }
            }

            // Simple toast helper (safe even if called many times)
            function showToast(message, duration = 3000) {
                // remove any existing toast
                const old = document.getElementById('globalToast');
                if (old) old.remove();

                const div = document.createElement('div');
                div.id = 'globalToast';
                div.textContent = message;
                div.className =
                    'fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-sm px-4 py-2 rounded shadow-lg z-50';
                document.body.appendChild(div);

                setTimeout(() => div.remove(), duration);
            }



            function setStatusEverywhere(incidentId, reportType, newStatus) {
                const arr =
                    reportType === 'fireReports' ? fireReports :
                    reportType === 'otherEmergency' ? otherEmergencyReports :
                    reportType === 'emsReports' ? emsReports :
                    reportType === 'smsReports' ? smsReports : null;

                const disp = capStatus(newStatus);
                const col = statusColor(disp);

                if (arr) {
                    const i = arr.findIndex(r => r.id === incidentId);
                    if (i !== -1) arr[i] = {
                        ...arr[i],
                        status: disp
                    };
                }

                const typeRow = document.getElementById(`reportRow${incidentId}`);
                if (typeRow) {
                    const rpt = JSON.parse(typeRow.getAttribute('data-report') || '{}');
                    rpt.status = disp;
                    typeRow.setAttribute('data-report', JSON.stringify(rpt));
                    const st = typeRow.querySelector('.status');
                    if (st) {
                        st.className = `px-4 py-2 status text-${col}-500`;
                        st.textContent = disp;
                    }
                }

                const allRow = document.querySelector(
                    `#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`
                );
                if (allRow) {
                    const st = allRow.querySelector('.status');
                    if (st) {
                        st.className = `px-4 py-2 status text-${col}-500`;
                        st.textContent = disp;
                    }
                }

                renderAllReports?.();
            }

            /* ---------- misc helpers reused above ---------- */

            function normalizeStations(val) {
                if (!val) return [];
                if (Array.isArray(val)) return val.filter(Boolean);
                if (typeof val === 'object') return Object.keys(val).filter(Boolean);
                return [];
            }

            function capStatus(s) {
                if (!s) return 'Unknown';
                const t = String(s).toLowerCase();
                return t === 'pending' ? 'Pending' :
                    t === 'ongoing' ? 'Ongoing' :
                    t === 'completed' ? 'Completed' :
                    t === 'received' ? 'Received' :
                    s;
            }

            /* ---------- Constants + helpers you defined (tidied) ---------- */

            const STATION_ROOTS = [
                'CapstoneFlare/CanocotanFireStation',
                'CapstoneFlare/LaFilipinaFireStation',
                'CapstoneFlare/MabiniFireStation'
            ];

            const LABEL_BY_ROOT = {
                'CapstoneFlare/CanocotanFireStation': 'Canocotan Fire Station',
                'CapstoneFlare/LaFilipinaFireStation': 'La Filipina Fire Station',
                'CapstoneFlare/MabiniFireStation': 'Mabini Fire Station'
            };

            // default account-key name if the station uses multi-account shape
            function fallbackAccountKeyFor(root) {
                const last = (root || '').split('/').filter(Boolean).pop();
                return last === 'CanocotanFireStation' ? 'CanocotanFireFighterAccount' :
                    last === 'LaFilipinaFireStation' ? 'LaFilipinaFireFighterAccount' :
                    last === 'MabiniFireStation' ? 'MabiniFireFighterAccount' :
                    'FireFighterAccount';
            }

            // Per-station FF account keys (literal, no computed array keys)
            const ACCOUNTS_BY_STATION = {
                'CapstoneFlare/CanocotanFireStation': ['CanocotanFireFighterAccount'],
                'CapstoneFlare/LaFilipinaFireStation': ['LaFilipinaFireFighterAccount'],
                'CapstoneFlare/MabiniFireStation': ['MabiniFireFighterAccount']
            };

            // Helpers
            function otherStationRoots() {
                return STATION_ROOTS.filter(r => r !== currentStationRoot());
            }




            function closeDetailsModal() {
                document.getElementById('detailsModal').classList.add('hidden');
            }


            /* =========================================================
             * MESSAGING (THREADS / RESPONSES / LIVE)
             * ========================================================= */

            function stationNodesForReportType(reportType) {
                const ALL = `${CURRENT_STATION_ROOT}/AllReport`;
                const labelByRoot = {
                    'CapstoneFlare/CanocotanFireStation': 'Canocotan Fire Station',
                    'CapstoneFlare/LaFilipinaFireStation': 'La Filipina Fire Station',
                    'CapstoneFlare/MabiniFireStation': 'Mabini Fire Station'
                };
                const label = labelByRoot[CURRENT_STATION_ROOT] || 'Canocotan Fire Station';

                const collectionBase =
                    reportType === 'fireReports' ? `${ALL}/FireReport` :
                    reportType === 'emsReports' ? `${ALL}/EmergencyMedicalServicesReport` :
                    reportType === 'smsReports' ? `${ALL}/SmsReport` :
                    `${ALL}/OtherEmergencyReport`;

                return {
                    repliesBase: collectionBase,
                    serviceReportType: reportType === 'fireReports' ? 'fire' : reportType === 'emsReports' ?
                        'emergencyMedicalServices' : reportType === 'smsReports' ? 'sms' : 'otherEmergency',
                    stationBase: ALL,
                    prefix: label
                };
            }


            function repliesRef(incidentId, _reportType) {
                const ALL = `${CURRENT_STATION_ROOT}/AllReport`;
                // Central list of user replies; filter by incidentId
                return firebase.database()
                    .ref(`${ALL}/ReplyMessage`)
                    .orderByChild('incidentId')
                    .equalTo(incidentId);
            }

            function stationResponsesQuery(incidentId, _reportType) {
                const ALL = `${CURRENT_STATION_ROOT}/AllReport`;
                return firebase.database()
                    .ref(`${ALL}/ResponseMessage`)
                    .orderByChild('incidentId')
                    .equalTo(incidentId);
            }

            function getFireStationNameByEmail(_email, callback) {
                try {
                    return callback("Tagum City Central Fire Station");
                } catch (_) {
                    return callback("Tagum City Central Fire Station");
                }
            }



            /* =========================================================
             * OPEN MESSAGE MODAL
             * ========================================================= */
            function openMessageModal(incidentId, reportType) {
                currentReportType = reportType;

                currentReport =
                    reportType === 'fireReports' ? (fireReports || []).find(r => r.id === incidentId) :
                    reportType === 'otherEmergency' ? (otherEmergencyReports || []).find(r => r.id === incidentId) :
                    reportType === 'emsReports' ? (emsReports || []).find(r => r.id === incidentId) :
                    reportType === 'smsReports' ? (smsReports || []).find(r => r.id === incidentId) :
                    null;

                if (!currentReport) return;

                const modal = document.getElementById('fireMessageModal');
                if (!modal) return;
                modal.classList.remove('hidden');

                document.getElementById('fireMessageIncidentIdValue').innerText = currentReport.id || '';
                document.getElementById('fireMessageNameValue').innerText = currentReport.name || 'No Name Provided';
                document.getElementById('fireMessageContactValue').innerText = currentReport.contact || 'N/A';
                document.getElementById('fireMessageIncidentInput').value = currentReport.id || '';

                try {
                    getFireStationNameByEmail(null, (n) => {
                        currentReport.fireStationName = n || "Canocotan Fire Station";
                    });
                } catch (_) {
                    currentReport.fireStationName = "Canocotan Fire Station";
                }

                resetChatThread();
                resetListeners();
                storedMessages = [];
                __lastBubble = {
                    type: null,
                    ts: 0,
                    el: null
                };

                // Enable chat input
                const input = document.getElementById('fireMessageInput');
                const btn = document.querySelector('#fireMessageForm button[type="submit"]');
                input.disabled = false;
                btn.disabled = false;
                input.placeholder = 'Type a message...';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');

                fetchThread(incidentId, reportType);
                subscribeThread(incidentId, reportType);

                // mark all replies read in DB
                markThreadRead(incidentId, reportType).catch(() => {});

                // instantly clear the badge in UI
                setBadgeCount(`${reportType}|${incidentId}`, 0);
            }

            function closeFireMessageModal() {
                document.getElementById('fireMessageModal').classList.add('hidden');
                resetListeners();
            }

            /* =========================================================
             * THREAD FETCH / LIVE SUBSCRIPTION
             * ========================================================= */

            function resetChatThread() {
                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';
            }

            function resetListeners() {
                liveListeners.forEach(ref => {
                    try {
                        ref.off?.();
                    } catch (_) {}
                });
                liveListeners = [];
            }

            /* Base64 helpers */
            function cleanB64(b64) {
                return (b64 || '').toString().replace(/\s+/g, "").replace(/[^A-Za-z0-9+/=]/g, "");
            }

            function guessImageMime(b64) {
                if (!b64) return "image/jpeg";
                const head = b64.slice(0, 12);
                if (/^iVBOR/.test(head)) return "image/png";
                if (/^R0lGOD/.test(head)) return "image/gif";
                if (/^(UklGR|R0lGU)/.test(head)) return "image/webp";
                return "image/jpeg";
            }

            function guessAudioMime() {
                return "audio/mp4";
            }

            function groupMessages(raw) {
                // No grouping, every message is treated as a separate entity
                return raw.map(m => ({
                    type: m.type,
                    timestamp: m.timestamp,
                    parts: [{
                        ts: m.timestamp || Date.now(),
                        kind: m.audioBase64 ? 'audio' : m.imageBase64 ? 'image' : 'text',
                        audioBase64: m.audioBase64 || '',
                        imageBase64: m.imageBase64 || '',
                        text: m.text || ''
                    }]
                }));
            }


            function fetchThread(incidentId, reportType) {
                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';

                const nn = stationNodesForReportType(reportType);
                if (!nn) return;

                // Path to messages node
                const messagesRef = firebase.database().ref(`${nn.repliesBase}/${incidentId}/messages`);

                messagesRef.once('value').then(snap => {
                    const out = [];
                    snap.forEach(c => {
                        const v = c.val() || {};
                        const hasAny = v.text || v.imageBase64 || v.audioBase64;
                        if (!hasAny) return;

                        out.push({
                            type: v.type || 'reply',
                            text: v.text || '',
                            imageBase64: v.imageBase64 || '',
                            audioBase64: v.audioBase64 || '',
                            timestamp: v.timestamp || 0,
                            reporterName: v.reporterName || '',
                            fireStationName: v.fireStationName || ''
                        });
                    });

                    out.sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
                    storedMessages = groupMessages(out);
                    renderMessages(storedMessages);
                    thread.scrollTop = thread.scrollHeight;
                });
            }


            function subscribeThread(incidentId, reportType) {
                const nn = stationNodesForReportType(reportType);
                if (!nn) return;

                const messagesRef = firebase.database().ref(`${nn.repliesBase}/${incidentId}/messages`);
                messagesRef.on('child_added', snap => {
                    const v = snap.val() || {};
                    const hasAny = v.text || v.imageBase64 || v.audioBase64;
                    if (!hasAny) return;

                    renderBubble({
                        type: v.type || 'reply',
                        text: v.text || '',
                        imageBase64: v.imageBase64 || '',
                        audioBase64: v.audioBase64 || '',
                        timestamp: v.timestamp || Date.now(),
                        reporterName: v.reporterName || '',
                        fireStationName: v.fireStationName || ''
                    });

                    const thread = document.getElementById('fireMessageThread');
                    thread.scrollTop = thread.scrollHeight;
                });

                liveListeners.push(messagesRef);
            }



            function renderMessages(messages) {
                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';
                __lastBubble = {
                    type: null,
                    ts: 0,
                    el: null
                };
                messages.forEach(renderBubble);
            }



            function renderBubble(msg) {
                const thread = document.getElementById('fireMessageThread');
                const nowTs = Number(msg.timestamp || Date.now());

                // Choose bubble color and alignment
                const isStation = (msg.type === 'response' || msg.type === 'station');
                const shell = document.createElement('div');
                shell.className = isStation ?
                    "message bg-blue-500 text-white p-4 rounded-lg my-2 max-w-xs ml-auto text-right shadow-md" :
                    "message bg-gray-300 text-black p-4 rounded-lg my-2 max-w-xs mr-auto text-left shadow-sm";

                const content = document.createElement('div');
                content.className = 'bubble-content';

                // Message body (text, image, or audio)
                const parts = Array.isArray(msg.parts) ?
                    msg.parts : [{
                        ts: nowTs,
                        kind: msg.audioBase64 ? 'audio' : msg.imageBase64 ? 'image' : 'text',
                        audioBase64: msg.audioBase64 || '',
                        imageBase64: msg.imageBase64 || '',
                        text: msg.text || ''
                    }];

                parts.forEach(p => {
                    if (p.kind === 'text' && p.text) {
                        const t = document.createElement('div');
                        t.textContent = p.text;
                        t.style.whiteSpace = 'pre-line';
                        t.style.wordBreak = 'break-word';
                        t.style.fontSize = '1rem';
                        t.style.fontWeight = '500';
                        content.appendChild(t);
                    }
                    if (p.kind === 'image' && p.imageBase64) {
                        const raw = cleanB64(p.imageBase64);
                        if (raw) {
                            const img = document.createElement('img');
                            img.style.marginTop = '8px';
                            img.style.maxWidth = '100%';
                            img.style.borderRadius = '8px';
                            img.src = `data:${guessImageMime(raw)};base64,${raw}`;
                            img.alt = 'Image';
                            content.appendChild(img);
                        }
                    }
                    if (p.kind === 'audio' && p.audioBase64) {
                        const raw = cleanB64(p.audioBase64);
                        if (raw) {
                            const aud = document.createElement('audio');
                            aud.controls = true;
                            aud.style.display = 'block';
                            aud.style.marginTop = '8px';
                            aud.src = `data:${guessAudioMime(raw)};base64,${raw}`;
                            content.appendChild(aud);
                        }
                    }
                });

                // Timestamp (24-hour format)
                const small = document.createElement('small');
                small.className = 'bubble-ts text-xs block mt-2 opacity-80';
                const formattedTime = new Date(nowTs).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                small.textContent = formattedTime;

                shell.appendChild(content);
                shell.appendChild(small);
                thread.appendChild(shell);
                thread.scrollTop = thread.scrollHeight;
            }





            /* =========================================================
             * SUBMIT REPLY
             * ========================================================= */
            const fireForm = document.getElementById('fireMessageForm');
            if (fireForm) {
                fireForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!currentReport) return;

                    const incidentId = document.getElementById('fireMessageIncidentInput').value;
                    const responseMessage = document.getElementById('fireMessageInput').value.trim();
                    if (!responseMessage) return;

                    const nn = stationNodesForReportType(currentReportType);
                    if (!nn) return;

                    const fireStationName = currentReport.fireStationName || `${nn.prefix} Fire Station`;

                    const payload = {
                        prefix: nn.prefix,
                        reportType: nn
                            .serviceReportType, // 'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
                        collectionPath: nn.repliesBase, // direct Firebase path (kept for backend payload)
                        incidentId,
                        reporterName: currentReport.name || '',
                        contact: currentReport.contact || '',
                        fireStationName,
                        responseMessage
                    };

                    fetch('/store-response', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(r => r.json())
                        .then(() => {
                            document.getElementById('fireMessageInput').value = '';
                        })
                        .catch(console.error);
                });
            }

            // put near the top, next to helpers
            function contentKind(m) {
                if (m.audioBase64) return (m.type || 'reply') + ':audio';
                if (m.imageBase64) return (m.type || 'reply') + ':image';
                if (m.text) return (m.type || 'reply') + ':text';
                return (m.type || 'reply') + ':empty';
            }



            /* =========================================================
             * MAPS / ROUTING / GEOFENCE (LEAFLET + OSRM + OVERPASS)
             * ========================================================= */

            async function snapToRoad(lat, lng) {
                try {
                    const url = `https://router.project-osrm.org/nearest/v1/car/${lng},${lat}`;
                    const res = await fetch(url);
                    const data = await res.json();
                    const wp = data?.waypoints?.[0]?.location;
                    return Array.isArray(wp) ? [wp[1], wp[0]] : [lat, lng];
                } catch {
                    return [lat, lng];
                }
            }

            let __routeMap, __fenceMap, __routeCtrl;

            async function openLocationModal(reportLat, reportLng) {
                const n = nodes();
                if (!n) return;

                try {
                    const snap = await firebase.database().ref(n.profile).once('value');
                    const meta = snap.val() || {};
                    const stationLat = parseFloat(meta.latitude);
                    const stationLng = parseFloat(meta.longitude);

                    if (![reportLat, reportLng, stationLat, stationLng].every(Number.isFinite)) {
                        console.error("Invalid coordinates.");
                        return;
                    }

                    const modal = document.getElementById('locationModal');
                    modal.classList.remove('hidden');

                    await updateTwoLeafletMaps({
                        reportLat,
                        reportLng,
                        stationLat,
                        stationLng
                    });

                    setTimeout(() => {
                        __routeMap?.invalidateSize();
                        __fenceMap?.invalidateSize();
                    }, 60);

                } catch (e) {
                    console.error('Error fetching station profile:', e);
                }
            }

            const OVERPASS_ENDPOINTS = [
                "https://overpass-api.de/api/interpreter",
                "https://overpass.kumi.systems/api/interpreter",
                "https://overpass.openstreetmap.ru/api/interpreter"
            ];

            function fetchWithTimeout(url, opts = {}, ms = 12000) {
                const ctrl = new AbortController();
                const t = setTimeout(() => ctrl.abort(), ms);
                return fetch(url, {
                    ...opts,
                    signal: ctrl.signal
                }).finally(() => clearTimeout(t));
            }

            async function countBuildingsWithin(lat, lng, radiusMeters) {
                const q = `
    [out:json][timeout:25];
    (
      node["building"](around:${radiusMeters},${lat},${lng});
      way["building"](around:${radiusMeters},${lat},${lng});
      relation["building"](around:${radiusMeters},${lat},${lng});
    );
    out ids;
  `.trim();

                for (const url of OVERPASS_ENDPOINTS) {
                    try {
                        const res = await fetchWithTimeout(url, {
                            method: "POST",
                            body: q
                        });
                        if (!res.ok) continue;
                        const data = await res.json();
                        const ids = new Set((data.elements || []).map(e => `${e.type}/${e.id}`));
                        return ids.size;
                    } catch {
                        /* try next mirror */
                    }
                }
                throw new Error("All Overpass mirrors failed or timed out.");
            }

            async function updateTwoLeafletMaps({
                reportLat,
                reportLng,
                stationLat,
                stationLng
            }) {
                try {
                    __routeCtrl && __routeCtrl.remove();
                } catch {}
                try {
                    __routeMap && __routeMap.remove();
                } catch {}
                try {
                    __fenceMap && __fenceMap.remove();
                } catch {}

                const mkTile = () =>
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    });

                const stationIcon = L.icon({
                    iconUrl: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                    iconSize: [28, 28]
                });
                const reportIcon = L.icon({
                    iconUrl: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    iconSize: [28, 28]
                });

                // Left: Routing map
                __routeMap = L.map('routeMap').setView([reportLat, reportLng], 13);
                mkTile().addTo(__routeMap);
                L.marker([stationLat, stationLng], {
                    icon: stationIcon
                }).addTo(__routeMap).bindPopup('Fire Station');
                L.marker([reportLat, reportLng], {
                    icon: reportIcon
                }).addTo(__routeMap).bindPopup('Report Location');

                const [snapStationLat, snapStationLng] = await snapToRoad(stationLat, stationLng);
                const [snapReportLat, snapReportLng] = await snapToRoad(reportLat, reportLng);

                __routeCtrl = L.Routing.control({
                    waypoints: [L.latLng(snapStationLat, snapStationLng), L.latLng(snapReportLat, snapReportLng)],
                    router: L.Routing.osrmv1({
                        serviceUrl: 'https://router.project-osrm.org/route/v1',
                        profile: 'car',
                        options: {
                            geometries: 'geojson',
                            overview: 'full'
                        }
                    }),
                    addWaypoints: false,
                    draggableWaypoints: false,
                    routeWhileDragging: false,
                    fitSelectedRoutes: true,
                    showAlternatives: false,
                    lineOptions: {
                        color: '#1976d2',
                        weight: 6,
                        opacity: 0.9
                    },
                    createMarker: () => null
                }).addTo(__routeMap);

                // Right: Fence map
                __fenceMap = L.map('fenceMap').setView([reportLat, reportLng], 17);
                mkTile().addTo(__fenceMap);
                L.marker([reportLat, reportLng], {
                    icon: reportIcon
                }).addTo(__fenceMap).bindPopup('Report Location').openPopup();
                L.marker([stationLat, stationLng], {
                    icon: stationIcon
                }).addTo(__fenceMap).bindPopup('Fire Station');

                // Conditional geofencing
                const MIN_BUILDINGS = 5;
                const DEFAULT_RADIUS_METERS = 50;

                try {
                    const buildingCount = await countBuildingsWithin(reportLat, reportLng, DEFAULT_RADIUS_METERS);
                    if (buildingCount >= MIN_BUILDINGS) {
                        L.circle([reportLat, reportLng], {
                            radius: DEFAULT_RADIUS_METERS,
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.25,
                            weight: 2
                        }).addTo(__fenceMap).bindPopup(
                            `Geofence: ~${Math.round(DEFAULT_RADIUS_METERS)} m • ${buildingCount} buildings`);
                    }
                } catch (e) {
                    console.warn("Fencing skipped (Overpass error):", e.message || e);
                }
            }

            function closeLocationModal() {
                document.getElementById('locationModal').classList.add('hidden');
            }



            // ==== Message badge wiring (diagnostic edition, isRead version) ====
            const __badgeSubs = new Map(); // key -> ref

            // Helper function to update the message badge count
            function setBadgeCount(key, count) {
                const badgeElement = document.querySelector(`[data-key="${key}"] .msg-badge`);
                if (!badgeElement) return;

                if (count <= 0) {
                    badgeElement.classList.add('hidden');
                } else {
                    badgeElement.textContent = count > 99 ? '99+' : count;
                    badgeElement.classList.remove('hidden');
                }
            }

            function subscribeBadge(key) {
                if (__badgeSubs.has(key)) return;

                const [type, incidentId] = key.split('|');
                const base = `${CURRENT_STATION_ROOT}/AllReport`;
                const typePath =
                    type === 'fireReports' ? 'FireReport' :
                    type === 'otherEmergency' ? 'OtherEmergencyReport' :
                    type === 'emsReports' ? 'EmergencyMedicalServicesReport' :
                    'SmsReport';

                const refPath = `${base}/${typePath}/${incidentId}/messages`;
                const ref = firebase.database().ref(refPath);

                console.log(`[BadgeSubscribe] Watching (fixed): ${refPath}`);

                const handler = ref.on('value', snap => {
                    let unread = 0;
                    let total = 0;

                    snap.forEach(c => {
                        const v = c.val() || {};
                        total++;

                        const isReply = v.type === 'reply';
                        const isUnread = v.isRead === false || v.isRead === "false";

                        // Real badge count for Fire, Other Emergency, EMS, and SMS
                        if ((type === 'fireReports' || type === 'otherEmergency' || type === 'emsReports' ||
                                type === 'smsReports') && isReply && isUnread) {
                            unread++;
                        }
                    });

                    // Set display count for all types of reports (fire, emergency, EMS, SMS)
                    const displayCount = unread > 0 ? unread : 0; // Show unread count, else default to 1

                    console.log(
                        `[BadgeResult] ${type}|${incidentId}: total=${total}, unread=${unread}, display=${displayCount}`
                    );
                    setBadgeCount(key, displayCount);
                });

                __badgeSubs.set(key, {
                    ref,
                    handler
                });
            }


            function ensureMessageBadges() {
                document.querySelectorAll('.msg-btn[data-key]').forEach(a => {
                    subscribeBadge(a.getAttribute('data-key'));
                });
            }

            /* =========================================================
             * MARK THREAD READ (so badge updates)
             * ========================================================= */
            async function markThreadRead(incidentId, reportType) {
                const base = `${CURRENT_STATION_ROOT}/AllReport`;
                const typePath =
                    reportType === 'fireReports' ? 'FireReport' :
                    reportType === 'otherEmergency' ? 'OtherEmergencyReport' :
                    reportType === 'emsReports' ? 'EmergencyMedicalServicesReport' :
                    'SmsReport';

                const refPath = `${base}/${typePath}/${incidentId}/messages`;
                const ref = firebase.database().ref(refPath);

                const snapshot = await ref.once('value');

                const updates = {};
                snapshot.forEach(child => {
                    const key = child.key;
                    const val = child.val();
                    // Mark only replies that are currently unread
                    if (val && val.type === 'reply' && (val.isRead === false || val.isRead === "false")) {
                        updates[`${key}/isRead`] = true;
                    }
                });

                if (Object.keys(updates).length > 0) {
                    await ref.update(updates);
                    console.log(
                        `[markThreadRead] Updated ${Object.keys(updates).length} replies to isRead=true for ${reportType}|${incidentId}`
                    );
                } else {
                    console.log(`[markThreadRead] No unread replies found for ${reportType}|${incidentId}`);
                }
            }
        </script>


    @endsection
