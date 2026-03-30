<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SteamService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.steampowered.com';

    public function __construct()
    {
        $this->apiKey = config('services.steam.key');
    }

    /**
     * Resolve Steam ID from profile URL or Steam ID
     */
    public function resolveSteamId($profileInput): ?string
    {
        // If it's already a numeric Steam ID
        if (is_numeric($profileInput) && strlen($profileInput) >= 17) {
            return $profileInput;
        }

        // Try to resolve vanity URL (custom profile username)
        if (strpos($profileInput, 'steamcommunity.com') !== false || !is_numeric($profileInput)) {
            $vanity = basename($profileInput);
            return $this->resolveVanityUrl($vanity);
        }

        return null;
    }

    /**
     * Resolve vanity URL to Steam ID
     */
    private function resolveVanityUrl($vanity): ?string
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/ISteamUser/ResolveVanityURL/v0001/", [
                'key' => $this->apiKey,
                'vanityurl' => $vanity,
            ]);

            $data = $response->json();

            if ($data['response']['success'] ?? false) {
                return $data['response']['steamid'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error('Steam vanity URL resolution failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch owned games for a Steam user
     */
    public function getOwnedGames($steamId): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/IPlayerService/GetOwnedGames/v0001/", [
                'key' => $this->apiKey,
                'steamid' => $steamId,
                'include_appid' => 1,
                'include_played_free_games' => 1,
            ]);

            $data = $response->json();

            if (isset($data['response']['games'])) {
                return $data['response']['games'];
            }
        } catch (\Exception $e) {
            \Log::error('Steam owned games fetch failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get game details from Steam (app ID to title mapping)
     */
    public function getGameDetails($appId): ?array
    {
        try {
            $response = Http::timeout(10)->get("https://store.steampowered.com/api/appdetails", [
                'appids' => $appId,
            ]);

            $data = $response->json();

            if ($data[$appId]['success'] ?? false) {
                $details = $data[$appId]['data'];
                return [
                    'name' => $details['name'] ?? null,
                    'header_image' => $details['header_image'] ?? null,
                    'steam_appid' => $appId,
                ];
            }
        } catch (\Exception $e) {
            \Log::error("Steam game details fetch failed for app {$appId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Batch fetch multiple game details (with caching to respect rate limits)
     */
    public function getGameDetailsBatch(array $appIds): array
    {
        $games = [];
        foreach ($appIds as $appId) {
            $details = $this->getGameDetails($appId);
            if ($details) {
                $games[] = $details;
            }
            // Respect Steam API rate limits
            usleep(100000); // 100ms delay between requests
        }
        return $games;
    }
}
