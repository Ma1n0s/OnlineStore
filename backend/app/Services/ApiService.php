<?php
namespace App\Services;

use App\Jobs\SendApiRequest;

class ApiService
{
    public function sendRequest(string $url, array $data)
    {
        
        SendApiRequest::dispatch(
            $url,
            $data,
            ['Authorization' => 'Bearer ' . config('services.external_api.token')]
        )->onQueue('api-requests');
        
    }
}