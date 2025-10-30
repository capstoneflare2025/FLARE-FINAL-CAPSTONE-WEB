<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class IncidentReportsController extends Controller
{
    public function __construct(private FirebaseService $firebase) {}

    public function index(Request $request)
    {
        // Require an authenticated session
        if (!session()->has('firebase_user_email')) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to view this page.');
        }

        // Which station is this session tied to? (set in AuthController)
        // e.g. "CapstoneFlare/LaFilipinaFireStation"
        $stationKey   = session('station');
        $stationLabel = session('station_label') ?: 'Fire Station';

        if (!$stationKey) {
            return redirect()->route('login')->with('error', 'Missing station context.');
        }

        try {
            // Pull reports from the logged-in station’s AllReport structure
            // NOTE: $prefix arg is ignored internally but kept for compatibility.
            $fireReports = $this->firebase->getFireReports($stationKey);
            $otherReports = $this->firebase->getOtherEmergencyReports($stationKey);
            $emsReports = $this->firebase->getEmergencyMedicalServicesReports($stationKey);
            $smsReports = $this->firebase->getSmsReports($stationKey);

            // Sort newest → oldest (robust date/time parsing, fallback to embedded timestamp)
            $fireReports = collect($fireReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $otherReports = collect($otherReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $emsReports = collect($emsReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $smsReports = collect($smsReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['time'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to fetch incident reports. Please try again.');
        }

        return view('ADMIN-DASHBOARD.incident-reports', [
            'stationKey'           => $stationKey,
            'stationLabel'         => $stationLabel,
            'fireReports'          => $fireReports,
            'otherEmergencyReports'=> $otherReports,
            'emsReports'           => $emsReports,
            'smsReports'           => $smsReports,
        ]);
    }

    /**
     * Turn various date/time formats into a comparable timestamp.
     * Tries Android formats first:
     *  - Fire:          MM/dd/yyyy
     *  - Other/EMS:     MM/dd/yy
     * Also tries:
     *  - dd/MM/yyyy, dd/MM/yy
     *  - yyyy-MM-dd
     * Falls back to strtotime() then $fallbackTs.
     */
    private function toTs(?string $date, ?string $time, int|string|null $fallbackTs = 0): int
    {
        if (!$date) return (int) $fallbackTs;

        $time = $time ?: '00:00';

        $candidates = [
            'm/d/Y H:i:s', 'm/d/Y H:i', 'm/d/Y',
            'm/d/y H:i:s', 'm/d/y H:i', 'm/d/y',
            'd/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y',
            'd/m/y H:i:s', 'd/m/y H:i', 'd/m/y',
            'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d',
        ];

        foreach ($candidates as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, trim("$date $time"));
            if ($dt instanceof \DateTime) {
                return $dt->getTimestamp();
            }
        }

        $ts = strtotime(trim("$date $time"));
        return $ts !== false ? $ts : (int) $fallbackTs;
    }
}
