<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class ImgBBService{
    private string $apiKey;

    public function __construct(string $imgbbApiKey)
    {
        $this->apiKey = $imgbbApiKey;
    }

    public function uploadImage(string $filePath): ?string
    {
        $client = HttpClient::create();
        $response = $client->request('POST', 'https://api.imgbb.com/1/upload', [
            'body' => [
                'key' => $this->apiKey,
                'image' => base64_encode(file_get_contents($filePath))
            ],
        ]);

        $data = $response->toArray();

        return $data['data']['url'] ?? null; // Retourne l'URL de l'image si elle est valide
    }
}