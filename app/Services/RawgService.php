<?php
// filepath: c:\Users\raiha\gaming-progress-tracker\app\Services\RawgService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RawgService
{
    protected $baseUrl = 'https://api.rawg.io/api';

    public function searchGames($query)
    {
        $response = Http::timeout(30)
            ->retry(3, 200)
            ->get($this->baseUrl . '/games', [
                'key' => config('services.rawg.key'),
                'search' => $query,
                'search_precise' => true,
                'page_size' => 40,
            ]);

        if ($response->failed()) {
            return [
                'results' => []
            ];
        }

        return $response->json();
    }

    public function getGameDetails($id)
    {
        $response = Http::timeout(30)
            ->retry(3, 200)
            ->get($this->baseUrl . '/games/' . $id, [
                'key' => config('services.rawg.key'),
            ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        return [
            'description' => $data['description'] ?? null,
            'description_raw' => $data['description_raw'] ?? null,
            'genres' => $data['genres'] ?? [],
            'platforms' => $data['platforms'] ?? [],
            'background_image' => $data['background_image'] ?? null,
            'released' => $data['released'] ?? null,
        ];
    }
}