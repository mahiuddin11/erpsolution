<?php

namespace App\Http\Controllers\Backend\Test;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZktecoSyncController extends Controller
{
    /**
     * Reconciliation পেজ — সব employee-র list, প্রতিটার পাশে Check/Update action
     */
    public function index()
    {
        $employees = Employee::select('id', 'id_card', 'am_name', 'device_id')
            ->whereNotNull('id_card')
            ->orderBy('name')
            ->get();

        return view('employe_deviceid', get_defined_vars());
    }

    /**
     * একটা নির্দিষ্ট employee-কে device-এর সাথে match করে দেখা (শুধু preview, কোনো save হয় না)
     *
     * Cascade lookup order:
     *  1) device_id দিয়ে (pk হিসেবে)
     *  2) না মিললে id_card দিয়ে (emp_code হিসেবে)
     *  3) না মিললে am_name দিয়ে (first_name হিসেবে)
     * যেই step-এ record পাওয়া যায়, সেটা নিয়ে emp_code/first_name/card_no তিনটা field-ই
     * ফাইনাল ভাবে compare করা হয় — তাই "কোন method দিয়ে পাওয়া গেছে" সেটা matched হওয়া মানেই না
     * যে সব field মিলেছে, সেটা নিচের comparison থেকেই বোঝা যাবে।
     */
    public function check($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        $resolved = $this->resolveZktecoEmployeeCascade($employee);

        if ($resolved['status'] === 'token_error') {
            return response()->json([
                'status' => 'token_error',
                'employee_id' => $employee->id,
            ]);
        }

        if ($resolved['status'] !== 'found') {
            return response()->json([
                'status' => 'not_found',
                'employee_id' => $employee->id,
                'message' => 'device_id, emp_code (id_card), name (am_name) — তিনটার কোনোটা দিয়েই device-এ কেউ পাওয়া যায়নি।',
            ]);
        }

        $deviceData  = $resolved['data'];
        $matchedVia  = $resolved['matched_via']; // 'device_id' | 'emp_code' | 'first_name'

        $comparison = $this->buildComparison($employee, $deviceData);

        $allMatched = $comparison['emp_code']['match']
            && $comparison['first_name']['match']
            && $comparison['card_no']['match'];

        return response()->json([
            'status' => 'found',
            'employee_id' => $employee->id,
            'matched_via' => $matchedVia, // কোন step-এ device record খুঁজে পাওয়া গেছে
            'all_matched' => $allMatched,
            'comparison' => $comparison,
            'current_device_id' => $employee->device_id,
            'device_owner' => [
                'id' => $deviceData['id'] ?? null,
                'emp_code' => $deviceData['emp_code'] ?? null,
                'first_name' => $deviceData['first_name'] ?? null,
                'card_no' => $deviceData['card_no'] ?? null,
            ],
        ]);
    }

    /**
     * Admin চোখে দেখে Confirm করার পর — device_id DB তে save করা হয়
     * নিরাপত্তার জন্য আবার fresh cascade lookup করা হয়, পুরনো cached data trust করা হয় না
     */
    public function confirm($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        $resolved = $this->resolveZktecoEmployeeCascade($employee);

        if ($resolved['status'] !== 'found') {
            return response()->json([
                'status' => $resolved['status'],
                'message' => 'Device-এ verify করা যায়নি, device_id update করা হয়নি।',
            ], 422);
        }

        $deviceData = $resolved['data'];
        $comparison = $this->buildComparison($employee, $deviceData);

        // Safety gate: emp_code, name, card_no — তিনটার যেকোনো একটা না মিললে save হবে না
        if (!$comparison['emp_code']['match'] || !$comparison['first_name']['match'] || !$comparison['card_no']['match']) {
            Log::warning('ZKTeco device_id confirm blocked — mismatch on final check', [
                'employee_id' => $employee->id,
                'matched_via' => $resolved['matched_via'],
                'device_data' => $deviceData,
            ]);

            return response()->json([
                'status' => 'mismatch',
                'message' => 'শেষ মুহূর্তে data mismatch পাওয়া গেছে, নিরাপত্তার জন্য save বাতিল করা হলো।',
            ], 422);
        }

        $oldDeviceId = $employee->device_id;
        $employee->device_id = $deviceData['id'];
        $employee->save();

        Log::info('ZKTeco device_id reconciled', [
            'employee_id' => $employee->id,
            'old_device_id' => $oldDeviceId,
            'new_device_id' => $deviceData['id'],
            'matched_via' => $resolved['matched_via'],
            'confirmed_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'employee_id' => $employee->id,
            'device_id' => $deviceData['id'],
            'matched_via' => $resolved['matched_via'],
        ]);
    }

    /**
     * emp_code/first_name/card_no তিনটা field compare করে array বানায় — preview এবং
     * confirm safety-gate দুই জায়গাতেই একই logic ব্যবহারের জন্য আলাদা করা হলো (DRY)
     */
    private function buildComparison(Employee $employee, array $deviceData): array
    {
        return [
            'emp_code' => [
                'software' => $employee->id_card,
                'device' => $deviceData['emp_code'] ?? null,
                'match' => trim((string) $employee->id_card) === trim((string) ($deviceData['emp_code'] ?? '')),
            ],
            'first_name' => [
                'software' => $employee->am_name,
                'device' => $deviceData['first_name'] ?? null,
                'match' => trim((string) $employee->am_name) === trim((string) ($deviceData['first_name'] ?? '')),
            ],
            'card_no' => [
                'software' => $employee->id_card,
                'device' => $deviceData['card_no'] ?? null,
                'match' => trim((string) $employee->id_card) === trim((string) ($deviceData['card_no'] ?? '')),
            ],
        ];
    }

    /**
     * 3-step cascade: device_id -> emp_code (id_card) -> first_name (am_name)
     * যেই step-এ প্রথম match পাওয়া যায় সেখানেই থেমে যায়
     */
    private function resolveZktecoEmployeeCascade(Employee $employee): array
    {
        // Step 1: device_id দিয়ে (pk হিসেবে)
        if (!empty($employee->device_id)) {
            $result = $this->zktecoLookupById((string) $employee->device_id);
            if ($result['status'] === 'token_error') {
                return $result;
            }
            if ($result['status'] === 'found') {
                return ['status' => 'found', 'matched_via' => 'device_id', 'data' => $result['data']];
            }
        }

        // Step 2: id_card দিয়ে (emp_code হিসেবে) — এই pattern আগে থেকেই working ছিল
        if (!empty($employee->id_card)) {
            $result = $this->zktecoLookupByEmpCode((string) $employee->id_card);
            if ($result['status'] === 'token_error') {
                return $result;
            }
            if ($result['status'] === 'found') {
                return ['status' => 'found', 'matched_via' => 'emp_code', 'data' => $result['data']];
            }
        }

        // Step 3: am_name দিয়ে (first_name হিসেবে) — সবচেয়ে দুর্বল match, তাই সবার শেষে
        if (!empty($employee->am_name)) {
            $result = $this->zktecoLookupByFirstName((string) $employee->am_name);
            if ($result['status'] === 'token_error') {
                return $result;
            }
            if ($result['status'] === 'found') {
                return ['status' => 'found', 'matched_via' => 'first_name', 'data' => $result['data']];
            }
        }

        return ['status' => 'not_found'];
    }

    /**
     * device_id (pk) দিয়ে single record lookup — path param
     */
    private function zktecoLookupById(string $deviceId): array
    {
        return $this->zktecoGetSingle(env('ZKTECO_IP') . "/personnel/api/employees/{$deviceId}/");
    }

    /**
     * id_card দিয়ে emp_code হিসেবে lookup — path param (আগের working endpoint)
     */
    private function zktecoLookupByEmpCode(string $empCode): array
    {
        return $this->zktecoGetSingle(env('ZKTECO_IP') . "/personnel/api/employees/{$empCode}/");
    }

    /**
     * am_name দিয়ে first_name হিসেবে lookup — এটা list/filter endpoint, path lookup না
     * ⚠️ query param নাম (`first_name`) আন্দাজে বসানো, deploy করার আগে actual ZKBioTime API
     *    doc/Postman দিয়ে verify করে নিতে হবে
     */
    private function zktecoLookupByFirstName(string $firstName): array
    {
        $token = zktecoGetToken();
        if (!$token) {
            return ['status' => 'token_error'];
        }

        $url = env('ZKTECO_IP') . '/personnel/api/employees/';

        $response = Http::withHeaders([
            'Authorization' => "Token $token",
            'Content-Type'  => 'application/json',
        ])->get($url, [
            'first_name' => $firstName,
        ]);

        if (!$response->successful()) {
            return ['status' => 'error', 'response' => $response->body()];
        }

        $json = $response->json();

        // list endpoint সাধারণত paginated results দেয় ({"data": [...]} বা {"results": [...]})
        $list = $json['data'] ?? $json['results'] ?? (is_array($json) && array_is_list($json) ? $json : []);

        if (empty($list)) {
            return ['status' => 'not_found'];
        }

        // প্রথম match নেওয়া হলো — নাম দিয়ে একাধিক employee থাকলে ভুল employee ধরার ঝুঁকি থাকে,
        // তাই এই step-এর match-কেই সবচেয়ে দুর্বল ধরা হচ্ছে, ফাইনাল field compare-ই আসল filter
        return ['status' => 'found', 'data' => $list[0]];
    }

    /**
     * সাধারণ single-record GET (id বা emp_code path lookup-এর জন্য শেয়ার্ড লজিক)
     */
    private function zktecoGetSingle(string $url): array
    {
        $token = zktecoGetToken();
        if (!$token) {
            return ['status' => 'token_error'];
        }

        $response = Http::withHeaders([
            'Authorization' => "Token $token",
            'Content-Type'  => 'application/json',
        ])->get($url);

        if ($response->status() == 404) {
            return ['status' => 'not_found'];
        }

        if ($response->successful()) {
            return ['status' => 'found', 'data' => $response->json()];
        }

        return ['status' => 'error', 'response' => $response->body()];
    }
}
