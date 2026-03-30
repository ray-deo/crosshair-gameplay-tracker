<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RawgService;

class SearchController extends Controller
{
    protected $rawg;

    public function __construct(RawgService $rawg)
    {
        $this->rawg = $rawg;
    }

    public function index(Request $request)
    {
        $games = [];
        $query = $request->input('q');

        if ($query) {

            $response = $this->rawg->searchGames($query);

            $results = collect($response['results'] ?? []);

            $search = strtolower($query);
            $search = preg_replace('/[^a-z0-9\s]/', '', $search);

            $games = $results
                ->map(function ($game) use ($search) {

                    $name = strtolower($game['name']);
                    $clean = preg_replace('/[^a-z0-9\s]/', '', $name);

                    $score = 0;

                    // Exact phrase match
                    if (strpos($clean, $search) !== false) {
                        $score += 100;
                    }

                    // Individual word matches
                    foreach (explode(' ', $search) as $word) {
                        if (strpos($clean, $word) !== false) {
                            $score += 10;
                        }
                    }

                    $game['score'] = $score;

                    return $game;
                })
                ->sortByDesc('score')
                ->take(20) // show more relevant results
                ->values()
                ->toArray();
        }

        return view('search.index', compact('games', 'query'));
    }
}