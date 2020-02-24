<?php
namespace App\Api;

use Symfony\Component\HttpClient\HttpClient;

abstract class AbstractApi
{
    protected function call(string $service, array $params = [])
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $_ENV['API_URL'], [
            'query' => array_merge([
                'wsfunction' => $service,
                'wstoken'=> $_ENV['API_TOKEN'],
                'moodlewsrestformat' => 'json',
            ], $params)
        ]);
        return $response->toArray();
    }
}
