<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Services\Settings\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsConfigurationApiController extends Controller
{
    //code...
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }


    public function stats()
    {
        return response()->json($this->smsService->getStats());
    }

    // GET /api/sms-configuration/config
    public function getConfig()
    {
        $config = $this->smsService->getConfig();

        if (!$config) {
            return response()->json(null);
        }

        return response()->json([
            'provider'  => $config->provider,
            'sender_id' => $config->sender_id,
            'api_url'   => $config->api_url,
            'api_key' => $config->api_key,
            'username'  => $config->username,
            'enabled'   => $config->enabled,
        ]);
    }

    // POST /api/sms-configuration/config
    public function saveConfig(Request $request)
    {

        $data = $request->validate([
            'provider'  => 'required|string|max:255',
            'sender_id' => 'nullable|string|max:255',
            'api_url'   => 'required|string|max:500',
            'api_key'   => 'required|string',
            'username'  => 'nullable|string|max:255',
            'password'  => 'nullable|string|max:255',
            'enabled'   => 'boolean',
        ]);

        $config = $this->smsService->saveConfig($data, Auth::id());

        return response()->json(['success' => true, 'id' => $config->id]);
    }

    // POST /api/sms-configuration/config/test
    public function testConnection()
    {
        $result = $this->smsService->testConnection();
        return response()->json($result);
    }

    // GET /api/sms-configuration/templates
    public function templatesIndex()
    {
        return response()->json($this->smsService->listTemplates());
    }

    // POST /api/sms-configuration/templates
    public function templatesStore(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $template = $this->smsService->createTemplate($data, Auth::id());

        return response()->json($template, 201);
    }

    // PUT /api/sms-configuration/templates/{id}
    public function templatesUpdate(Request $request, SmsTemplate $template)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $template = $this->smsService->updateTemplate($template, $data, Auth::id());

        return response()->json($template);
    }

    // DELETE /api/sms-configuration/templates/{id}
    public function templatesDestroy(SmsTemplate $template)
    {
        $this->smsService->deleteTemplate($template);
        return response()->json(['success' => true]);
    }

    // GET /api/sms-configuration/recipients-count?type=all
    public function recipientsCount(Request $request)
    {
        return response()->json(['count' => $this->smsService->allEmployeeCount()]);
    }

    // GET /api/sms-configuration/departments
    public function departments()
    {
        return response()->json($this->smsService->departmentsWithCount());
    }

    // GET /api/sms-configuration/contacts?search=...
    public function contacts(Request $request)
    {

        $term = trim((string) $request->query('search', ''));
        if (strlen($term) < 2) {

            return response()->json([]);
        }


        return response()->json($this->smsService->searchContacts($term));
    }

    // POST /api/sms-configuration/send
    public function send(Request $request)
    {

        $data = $request->validate([
            'template_id'     => 'nullable',
            'message'         => 'required|string|max:1000',
            'recipient_type'  => 'required|in:all,department,single',
            'department_ids'  => 'required_if:recipient_type,department|array',
            'contact_id'      => 'nullable|string',
            'contact_type'    => 'nullable|string',
            'phone'           => 'nullable|string|max:20',
        ]);



        $result = $this->smsService->send($data, Auth::id());

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
