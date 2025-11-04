<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ResponseController extends Controller
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Map any incoming reportType variant to the service key used by FirebaseService.
     * FirebaseService expects: 'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
     */
    private function normalizeReportType(string $t): string
    {
        $t = strtolower(trim($t));

        if (in_array($t, ['fire', 'firereports', 'tagumfire'], true)) {
            return 'fire';
        }
        if (in_array($t, ['otheremergency', 'othereports', 'tagumotheremergency'], true)) {
            return 'otherEmergency';
        }
        if (in_array($t, ['ems', 'emsreports', 'emergencymedicalservices', 'tagumems'], true)) {
            return 'emergencyMedicalServices';
        }
        if (in_array($t, ['sms', 'smsreport', 'smsreports', 'tagumsms'], true)) {
            return 'sms';
        }

        return 'otherEmergency';
    }

    /**
     * PATCH/POST: Update a report's status.
     * Body: { prefix, reportType, incidentId, status }
     */
    public function updateReportStatus(Request $request)
    {
        try {
            $data = $request->validate([
                'prefix'     => 'required|string',
                'incidentId' => 'required|string',
                'reportType' => 'required|string|in:fire,fireReports,otherEmergency,emsReports,emergencyMedicalServices,sms,smsReports',
                'status'     => 'required|string',
            ]);

            $normalizedType = $this->normalizeReportType($data['reportType']);

            // prefix ignored in new structure, but method signature preserved
            $ok = $this->firebaseService->updateScopedReportStatus(
                $data['prefix'],
                $normalizedType,
                $data['incidentId'],
                $data['status']
            );

            return response()->json(['success' => (bool) $ok]);
        } catch (\Throwable $e) {
            Log::error("Error updating report status: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update report status'], 500);
        }
    }
public function storeResponse(Request $request)
{
    try {
        $data = $request->validate([
            'prefix'          => 'required|string',
            'reportType'      => 'required|string|in:fire,fireReports,otherEmergency,emsReports,emergencyMedicalServices,sms,smsReports',
            'incidentId'      => 'required|string',
            'responseMessage' => 'required|string',
            'reporterName'    => 'nullable|string',
            'contact'         => 'nullable|string',
        ]);

        // ✅ Pull station info from session (set at login)
        $stationKey   = Session::get('station');        // e.g. CapstoneFlare/LaFilipinaFireStation
        $stationLabel = Session::get('station_label');  // e.g. La Filipina Fire Station

        if (!$stationKey || !$stationLabel) {
            return response()->json([
                'success' => false,
                'message' => 'Station session missing. Please log in again.'
            ], 403);
        }

        $normalizedType = $this->normalizeReportType($data['reportType']);
        $now = now();

        $contact = $data['contact'] ?? '';
        $name    = $data['reporterName'] ?? '';

        // ✅ Include station details automatically
        $threadMsg = [
            'incidentId'      => $data['incidentId'],
            'type'            => 'station',
            'text'            => $data['responseMessage'],
            'imageBase64'     => null,
            'audioBase64'     => null,
            'uid'             => null,
            'reporterName'    => $name,
            'contact'         => $contact,
            'fireStationName' => $stationLabel, // 👈 pulled from Auth session
            'stationNode'     => $stationKey,   // 👈 CapstoneFlare/LaFilipinaFireStation
            'date'            => $now->format('Y-m-d'),
            'time'            => $now->format('H:i:s'),
            'timestamp'       => $now->getTimestamp() * 1000,
            'isRead'          => false,
        ];

        // ✅ Store under that specific station path
        $ok = $this->firebaseService->storeUnifiedMessage(
            $stationKey,       // <– prefix now is actual station node
            $normalizedType,
            $data['incidentId'],
            $threadMsg
        );

        return response()->json(['success' => $ok]);
    } catch (\Throwable $e) {
        Log::error("Error storing response: {$e->getMessage()}", [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to store response'
        ], 500);
    }
}


}
