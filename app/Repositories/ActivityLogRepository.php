<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogRepository
{
    protected $activityLogs;

    public function __construct(ActivityLog $activityLogs)
    {
        $this->activityLogs = $activityLogs;
    }


    public function getList($request)
    {
        $columns = [
            0 => 'id',
            1 => 'created_at',
            2 => 'user_name',
            3 => 'action',
            4 => 'module',
        ];

        $query = $this->activityLogs::query();

        // =============================
        // 1. FILTERS
        // =============================
        if ($request->filled('module') && $request->module != '') {
            $query->where('module', $request->module);
        }

        if ($request->filled('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        // =============================
        // 2. SEARCH
        // =============================
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // =============================
        // 3. Final Data
        // =============================
        $logs = $query->orderBy('id', 'desc')
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];

        foreach ($logs as $key => $value) {

            $nestedData = [];

            $nestedData['id']           = $value->id;
            $nestedData['sl']           = $start + $key + 1;
            $nestedData['created_at']   = $value->created_at ? $value->created_at->format('d M, Y h:i A') : '';
            $nestedData['user_name']    = $value->user_name ?? 'System';
            $nestedData['action']       = '<span class="badge badge-' . ($value->status == 'success' ? 'success' : 'danger') . '">' . ucfirst($value->action) . '</span>';
            $nestedData['module']       = '<strong>' . $value->module . '</strong>';
            $nestedData['description']  = $value->description ?? 'N/A';
            $nestedData['changed_fields'] = $this->formatChangedFields($value->changed_fields);
            $nestedData['status']       = '<span class="badge badge-' . ($value->status == 'success' ? 'success' : 'warning') . '">' . ucfirst($value->status) . '</span>';
            $nestedData['ip_address']   = $this->maskIPAddress($value->ip_address) ?? '';
            $nestedData['user_agent']   = $this->maskUserAgent($value->user_agent) ?? '';
            $data[] = $nestedData;
        }

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];
    }

    private function formatChangedFields($changedFields)
    {
        if (empty($changedFields)) {
            return '<span class="text-muted">No changes</span>';
        }

        $html = '<ul style="padding-left:15px; margin:0;">';
        foreach ($changedFields as $field => $data) {
            $old = $data['old'] ?? 'NULL';
            $new = $data['new'] ?? 'NULL';

            $html .= "<li><strong>" . ucfirst(str_replace('_', ' ', $field)) . ":</strong> 
                    <span class='text-danger'>{$old}</span> 
                    → 
                    <span class='text-success'>{$new}</span></li>";
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * IP Address Masking
     */
    private function maskIPAddress($ip)
    {
        if (empty($ip) || $ip == '127.0.0.1' || $ip == '::1') {
            return $ip;
        }

        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            $parts[3] = 'XXX';
            return implode('.', $parts);
        }

        return substr($ip, 0, 7) . 'XXX';
    }

    /**
     * User Agent Masking
     */
    private function maskUserAgent($userAgent)
    {
        if (empty($userAgent)) {
            return 'Unknown Device';
        }


        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }

        return 'Unknown Browser';
    }
}
