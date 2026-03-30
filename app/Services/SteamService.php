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
            $profileInput = trim($profileInput);

            // Check if it's a full Steam profile URL
            if (strpos($profileInput, 'steamcommunity.com') !== false) {
                // Extract from URL like steamcommunity.com/profiles/76561198... or steamcommunity.com/id/username
                if (preg_match('/\/profiles\/(\d+)/', $profileInput, $matches)) {
                    // It's a numeric profile ID
                    return $matches[1];
                } elseif (preg_match('/\/id\/([^\/]+)/', $profileInput, $matches)) {
                    // It's a vanity URL (custom username)
                    return $this->resolveVanityUrl($matches[1]);
                }
            }

            // If it's already a numeric Steam ID (17 digits)
            if (is_numeric($profileInput) && strlen($profileInput) >= 17) {
                return $profileInput;
            }

            // If it doesn't look like a URL, try treating it as a vanity username
            if (!is_numeric($profileInput)) {
                return $this->resolveVanityUrl($profileInput);
            }

            return null;
    }

    /**
     * Resolve vanity URL to Steam ID
     */
    private function resolveVanityUrl($vanity): ?string
    {
        try {
            if (!$this->apiKey) {
                \Log::error('Steam API key not configured');
                return null;
            }

            $response = Http::timeout(10)->get("{$this->baseUrl}/ISteamUser/ResolveVanityURL/v0001/", [
                'key' => $this->apiKey,
                'vanityurl' => $vanity,
            ]);

            $data = $response->json();
            \Log::info('Vanity URL resolution response: ' . json_encode($data));

            if (isset($data['response']) && ($data['response']['success'] ?? 0) == 1) {
                $steamId = $data['response']['steamid'] ?? null;
                \Log::info("Resolved vanity '{$vanity}' to Steam ID: {$steamId}");
                return $steamId;
            } else {
                \Log::warning("Failed to resolve vanity '{$vanity}': " . json_encode($data));
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
                    'cover_image' => "https://cdn.cloudflare.steamstatic.com/steam/apps/{$appId}/library_600x900_2x.jpg",
                    'steam_appid' => $appId,
                ];
            }
        } catch (\Exception $e) {
            \Log::error("Steam game details fetch failed for app {$appId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get normalized details for game show page.
     */
    public function getGameDetailsForShow($appId): ?array
    {
        try {
            $response = Http::timeout(10)->get('https://store.steampowered.com/api/appdetails', [
                'appids' => $appId,
                'l' => 'en',
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (!is_array($data)) {
                return null;
            }

            if (!isset($data[$appId]) || !is_array($data[$appId])) {
                return null;
            }

            if (!($data[$appId]['success'] ?? false)) {
                return null;
            }

            $details = $data[$appId]['data'] ?? [];

            if (!is_array($details)) {
                return null;
            }

            $genres = array_map(function ($genre) {
                return ['name' => $genre['description'] ?? 'Unknown'];
            }, is_array($details['genres'] ?? null) ? $details['genres'] : []);

            $platforms = [];
            $platformData = $details['platforms'] ?? [];
            foreach (['windows', 'mac', 'linux'] as $platformKey) {
                if (!empty($platformData[$platformKey])) {
                    $platforms[] = ['platform' => ['name' => ucfirst($platformKey)]];
                }
            }

            $descriptionRaw = $details['detailed_description']
                ?? $details['short_description']
                ?? null;

            return [
                'description' => $details['short_description'] ?? null,
                'description_raw' => $descriptionRaw ? trim(strip_tags($descriptionRaw)) : null,
                'genres' => $genres,
                'platforms' => $platforms,
                'background_image' => $details['header_image'] ?? null,
                'released' => $details['release_date']['date'] ?? null,
            ];
        } catch (\Throwable $e) {
            \Log::error("Steam game details fetch failed for show app {$appId}: " . $e->getMessage());
            return null;
        }
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
