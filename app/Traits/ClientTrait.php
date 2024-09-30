<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

trait ClientTrait
{
    public function get(string $uri): array
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $uri);
            return ['status' => $response->getStatusCode(), 'content' => $response->getBody()->getContents()];
        } catch (GuzzleException $e) {
            $message = 'Caught exception: ' . $e->getMessage();
            Log::error($message);
            return ['status' => '', 'content' => ''];
        }
    }
}
