<?php
// Added: 2026-08-24

namespace App\Services\Settings;

use App\Models\Employee;
use App\Models\SmsConfiguration;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SmsService
{
    /**
     * ----------------------------------------------------------------
     * PORTAL CONFIG + STATS
     * ----------------------------------------------------------------
     */

    private function buildRecipientVars($employee): array
    {
        return [
            'name'          => $employee->name,
            'contact_number' => $employee->personal_phone,
            'address'       => $employee->address ?? '',        // TODO-CONFIRM: actual column name
            'department'    => $employee->position_name ?? '',   // positions table e grouped, department = designation
            'designation'   => $employee->position_name ?? '',
            'company_name'  => config('app.name', 'WTBL'),        // TODO-CONFIRM: company settings theke asha uchit hole bolo
            'date'          => now()->format('d M, Y'),
        ];
    }

    private function fillTemplate(string $text, array $vars): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($m) use ($vars) {
            return $vars[$m[1]] ?? $m[0];
        }, $text);
    }


    public function getConfig(): ?SmsConfiguration
    {
        return SmsConfiguration::first();
    }

    public function saveConfig(array $data, $userId): SmsConfiguration
    {
        $config = SmsConfiguration::first();

        $payload = [
            'provider'   => $data['provider'],
            'sender_id'  => $data['sender_id'] ?? null,
            'api_url'    => $data['api_url'],
            'username'   => $data['username'] ?? null,
            'enabled'    => $data['enabled'] ?? true,
            'updated_by' => $userId,
        ];


        if (!empty($data['api_key'])) {
            $payload['api_key'] = $data['api_key'];
        }
        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        if ($config) {
            $config->update($payload);
            return $config->fresh();
        }

        $payload['created_by'] = $userId;
        return SmsConfiguration::create($payload);
    }

    public function getStats(): array
    {
        $config = $this->getConfig();

        $sentCount = SmsLog::where('status', 'Sent')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $rejectedCount = SmsLog::where('status', 'Rejected')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'balance'        => $config->last_known_balance ?? 0,   // config null hole 0
            'sent_count'     => $sentCount,
            'rejected_count' => $rejectedCount,
            'connected'      => (bool) ($config && $config->enabled),
            'provider'       => $config->provider ?? null,
            'sender_id'      => $config->sender_id ?? null,
        ];
    }

    /**
     * TODO-CONFIRM: Every SMS provider (Alpha SMS, Bulk SMS BD, etc.) has its
     * own balance-check / test endpoint and response shape. Adjust the
     * request below once you confirm which provider WTBL is using and share
     * their API docs -- for now this just checks that api_url responds.
     */
    public function testConnection(): array
    {
        $config = $this->getConfig();

        if (!$config || !$config->api_url) {
            return ['success' => false, 'message' => 'No SMS portal configured yet.'];
        }

        try {
            $response = Http::timeout(10)->get($config->api_url, [
                // TODO-CONFIRM: replace with provider's actual auth query params
                'api_key'  => $config->api_key,
                'username' => $config->username,
            ]);

            $ok = $response->successful();

            if ($ok) {
                $config->update(['balance_checked_at' => Carbon::now()]);
            }

            return [
                'success' => $ok,
                'message' => $ok
                    ? 'Connection successful.'
                    : 'Provider responded with an error. Please check your credentials.',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Could not reach the SMS provider: ' . $e->getMessage()];
        }
    }



    public function allEmployeeCount(): int
    {
        $employee = Employee::where('employee_status', 'present')
            ->count();
        return  $employee;
    }

    public function departmentsWithCount()
    {
        return DB::table('positions')
            ->leftJoin('employees', function ($join) {
                $join->on('employees.position_id', '=', 'positions.id')
                    // ->where('employees.status', 'Active')
                    ->where('employees.employee_status', 'present');
            })
            ->select('positions.id', 'positions.name', DB::raw('COUNT(employees.id) as employee_count'))
            ->groupBy('positions.id', 'positions.name')
            ->orderBy('positions.name')
            ->get();
    }

    public function searchContacts(string $term)
    {
        $term = '%' . $term . '%';

        $employees = DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->where(function ($query) use ($term) {
                $query->where('employees.name', 'like', "%{$term}%")
                    ->orWhere('employees.id_card', 'like', "%{$term}%");
            })
            ->limit(5)
            ->get([
                'employees.id',
                'employees.name',
                'employees.id_card',
                'employees.personal_phone as phone',
                'positions.name as position',
            ])
            ->map(fn($r) => [
                'id' => $r->id,
                'empCode' => $r->id_card,
                'name' => $r->name,
                'phone' => $r->phone,
                'type' => $r->position ?? 'Employee',
            ]);



        // $customers = DB::table('customers')
        //     ->where('name', 'like', $term) // TODO-CONFIRM
        //     ->limit(5)
        //     ->get(['id', 'name', 'phone'])
        //     ->map(fn($r) => ['id' => 'cus-' . $r->id, 'name' => $r->name, 'phone' => $r->phone, 'type' => 'Customer']);



        // $suppliers = DB::table('suppliers')
        //     ->where('name', 'like', $term) // TODO-CONFIRM
        //     ->limit(5)
        //     ->get(['id', 'name', 'phone'])
        //     ->map(fn($r) => ['id' => 'sup-' . $r->id, 'name' => $r->name, 'phone' => $r->phone, 'type' => 'Supplier']);


        // return $employees->concat($customers)->concat($suppliers)->values();
        return $employees->values();
    }



    public function send(array $payload, $userId): array
    {

        $config = $this->getConfig();


        if (!$config || !$config->enabled) {
            return ['success' => false, 'message' => 'SMS portal is not configured or is disabled.'];
        }

        $recipients = $this->resolveRecipients($payload);


        if (empty($recipients)) {
            return ['success' => false, 'message' => 'No recipients found for the selected option.'];
        }

        $sentCount = 0;
        $failedCount = 0;
        $lastErrorMessage = null;


        foreach ($recipients as $recipient) {
            $personalizedMessage = $this->fillTemplate($payload['message'], $recipient['vars'] ?? []);

            $status = 'Pending';
            $providerResponse = null;

            try {

                $number = preg_replace('/^\+?88/', '', $recipient['phone']);
                if (substr($number, 0, 1) !== '0') {
                    $number = '0' . $number;
                }
                $number = '880' . substr($number, 1);

                if (empty($number) || strlen($number) < 13) {
                    $status = 'Rejected';
                    $providerResponse = 'Invalid phone number format: ' . $recipient['phone'];
                } else {

                    $data = [
                        "apikey" => $config->api_key,
                        "secretkey" => $config->username,
                        "callerID" => $config->sender_id,
                        "toUser" => $number,
                        "messageContent" => $personalizedMessage
                    ];

                    $response = null;

                    if ($config->enabled == 1) {

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $config->api_url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        $response = curl_exec($ch);
                        curl_close($ch);
                    }

                    $providerResponse = $response;
                    $body = json_decode($providerResponse ?? '', true);

                    $status = (isset($body['Status']) && (string) $body['Status'] === '0' && !empty($body['Message_ID']))
                        ? 'Sent'
                        : 'Rejected';

                    if ($status === 'Rejected') {
                        $lastErrorMessage = $body['StatusDescription'] ?? $body['Text'] ?? 'Unknown provider error';
                    }
                }
            } catch (\Throwable $e) {
                $status = 'Rejected';
                $providerResponse = $e->getMessage();
                $lastErrorMessage = $e->getMessage();
            }

            $status === 'Sent' ? $sentCount++ : $failedCount++;

            SmsLog::create([
                'sms_template_id'   => $payload['template_id'] ?? null,
                'recipient_type'    => $payload['recipient_type'],
                'recipient_name'    => $recipient['name'] ?? null,
                'recipient_phone'   => $recipient['phone'],
                'message'           => $personalizedMessage,
                'status'            => $status,
                'provider_response' => $providerResponse,
                'sent_by'           => $userId,
            ]);
        }

        if ($sentCount > 0) {
            $this->refreshBalance($config);
        }

        $message = "SMS queued: {$sentCount} sent, {$failedCount} failed.";
        if ($failedCount > 0 && $lastErrorMessage) {
            $message .= " Last error: {$lastErrorMessage}";
        }

        return [
            'success' => $sentCount > 0,
            'message' => $message,
        ];
    }


    private function resolveRecipients(array $payload): array
    {
        if ($payload['recipient_type'] === 'single') {


            if (!empty($payload['phone']) && empty($payload['contact_id'])) {
                return [[
                    'name'  => null,
                    'phone' => $payload['phone'],
                    'vars'  => [
                        'name' => '',
                        'contact_number' => $payload['phone'],
                        'address' => '',
                        'department' => '',
                        'designation' => '',
                        // 'company_name' => config('app.name', 'WTBL'),
                        'company_name' => '',
                        'date' => now()->format('d M, Y'),
                    ],
                ]];
            }

            // Employee select kora hoyeche (search theke)
            if (!empty($payload['contact_id'])) {
                $employeeId = (int) $payload['contact_id']; // searchContacts() ekhon raw id dey, 'emp-' prefix nei

                $employee = DB::table('employees')
                    ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
                    ->where('employees.id', $employeeId)
                    ->where('employees.employee_status', 'present')
                    ->first([
                        'employees.name',
                        'employees.personal_phone',
                        'employees.present_address',       // TODO-CONFIRM column name
                        'positions.name as position_name',
                    ]);

                if (!$employee || empty($employee->personal_phone)) {
                    return [];
                }

                return [[
                    'name'  => $employee->name,
                    'phone' => $employee->personal_phone,
                    'vars'  => $this->buildRecipientVars($employee),
                ]];
            }

            return [];
        }

        if ($payload['recipient_type'] === 'department') {

            return DB::table('employees')
                ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
                ->whereIn('employees.position_id', $payload['department_ids'] ?? [])
                ->where('employees.employee_status', 'present')
                ->get([
                    'employees.name',
                    'employees.personal_phone',
                    'employees.present_address',
                    'positions.name as position_name',
                ])
                ->filter(fn($r) => !empty($r->personal_phone))
                ->map(fn($r) => [
                    'name'  => $r->name,
                    'phone' => $r->personal_phone,
                    'vars'  => $this->buildRecipientVars($r),
                ])
                ->values()
                ->toArray();
        }


        if ($payload['recipient_type'] === 'selected') {
            $employeeIds = $payload['employee_ids'] ?? [];

            if (empty($employeeIds)) {
                return [];
            }

            return DB::table('employees')
                ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
                ->whereIn('employees.id', $employeeIds)
                ->where('employees.employee_status', 'present')
                ->get([
                    'employees.name',
                    'employees.personal_phone',
                    'employees.present_address',
                    'positions.name as position_name',
                ])
                ->filter(fn($r) => !empty($r->personal_phone))
                ->map(fn($r) => [
                    'name'  => $r->name,
                    'phone' => $r->personal_phone,
                    'vars'  => $this->buildRecipientVars($r),
                ])
                ->values()
                ->toArray();
        }



        return DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->where('employees.status', 'Active')
            ->get([
                'employees.name',
                'employees.personal_phone',
                'employees.present_address',
                'positions.name as position_name',
            ])
            ->filter(fn($r) => !empty($r->personal_phone))
            ->map(fn($r) => [
                'name'  => $r->name,
                'phone' => $r->personal_phone,
                'vars'  => $this->buildRecipientVars($r),
            ])
            ->values()
            ->toArray();
    }

    private function refreshBalance(SmsConfiguration $config): void
    {
        try {
            $urlBalanceCheck = 'https://portal.khudebarta.com:3770/api/v3/balance';
            $balanceData = [
                'apikey'        => $config->api_key,
                'secretkey'     => $config->username,
                'clienttransid' => (string) \Illuminate\Support\Str::uuid(),
            ];

            $response = null;

            if ($config->enabled == 1) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $urlBalanceCheck);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($balanceData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Khudebarta cert expired thakle/hang korle jate SMS send block na hoy
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $response = curl_exec($ch);
                curl_close($ch);
            }

            $providerResponse = $response;
            $body = json_decode($providerResponse ?? '', true);

            // Khudebarta balance nested thake statusInfo.availablebalance-e
            $balance = $body['statusInfo']['availablebalance'] ?? null;

            if ($balance !== null) {
                $config->update([
                    'last_known_balance' => (float) $balance,
                    'balance_checked_at' => Carbon::now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silent fail -- balance check e problem hole SMS sending process block hobe na.
        }
    }

    public function listTemplates()
    {
        return SmsTemplate::orderByDesc('id')->get();
    }

    public function createTemplate(array $data, $userId): SmsTemplate
    {
        return SmsTemplate::create($data + ['created_by' => $userId]);
    }

    public function updateTemplate(SmsTemplate $template, array $data, $userId): SmsTemplate
    {
        $template->update($data + ['updated_by' => $userId]);
        return $template->fresh();
    }

    public function deleteTemplate(SmsTemplate $template): void
    {
        $template->delete();
    }
}
