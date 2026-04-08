<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

function zktecoGetToken()
{
    $url = env("ZKTECO_IP") . '/api-token-auth/';  // Ensure this is the correct API endpoint
    $username = env("ZKTECO_USERNAME");  // Your ZKTeco username
    $password = env("ZKTECO_PASSWORD");  // Your ZKTeco password

    $response = Http::post($url, [
        'username' => $username,
        'password' => $password,
    ]);

    if ($response->successful()) {
        return $response->json('token'); // Returns the token
    } else {
        return null; // Debugging: check the error message
    }
}


function getZKTecoAreas($page = 1, $page_size = 10, $area_code = null, $area_name = null, $ordering = 'id')
{
    // Get Token
    $token = zktecoGetToken(); // Call your function to get the token
    if (!$token) {
        return "Failed to retrieve token.";
    }
    $url = env("ZKTECO_IP") . '/personnel/api/areas/';

    // Query parameters
    $queryParams = [
        'page' => $page,
        'page_size' => $page_size,
        'ordering' => $ordering,
        'ordering' => $ordering,
    ];

    if ($area_code) {
        $queryParams['area_code'] = $area_code;
    }
    if ($area_name) {
        $queryParams['area_name'] = $area_name;
    }
    // Make GET request with Authorization Header
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => "Token $token",
    ])->get($url, $queryParams);

    // Return response or error message
    if ($response->successful()) {
        return $response->json();
    } else {
        return $response->body(); // Debugging: check error message
    }
}


function createZKTecoEmployee(array $employeeData)
{
    // dd('zk create ',$employeeData);
    $token = zktecoGetToken(); // Fetch JWT Token
    if (!$token) {
        return "Error: Token not retrieved!";
    }

    $url = env("ZKTECO_IP") . '/personnel/api/employees/';

    // Debugging: Check what data is being sent
    Log::info("Creating Employee Request Data: ", $employeeData);

    // Send Request to ZKTeco API
    $response = Http::withHeaders([
        'Authorization' => "Token $token",
        'Content-Type'  => 'application/json',
    ])->post($url, $employeeData);

    // Debugging: Log response
    if ($response->successful()) {
        return $response->json(); // Return the API response
    } else {
        Log::error("Employee Creation Failed: " . $response->body());
        return $response->body(); // Return error message
    }
}
function editZKTecoEmployee( $id,array $employeeData)
{
    // dd($employeeData);
    $token = zktecoGetToken(); // Fetch JWT Token
    if (!$token) {
        return "Error: Token not retrieved!";
    }

    $url = env("ZKTECO_IP") . "/personnel/api/employees/$id/";
    // Send Request to ZKTeco API
    $response = Http::withHeaders([
        'Authorization' => "Token $token",
        'Content-Type'  => 'application/json',
    ])->put($url, $employeeData);

    // Debugging: Log response
    if ($response->successful()) {
        return $response->json(); // Return the API response
    } else {
        return $response->body(); // Return error message
    }
}
