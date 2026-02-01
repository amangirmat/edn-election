<?php

namespace Botble\EdnElection\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckElectionApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Get the key from the request header
        $apiKey = $request->header('X-Election-API-Key');

        // Compare with the key stored in your .env file
        if (!$apiKey || $apiKey !== config('plugins.edn-election.api.key')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing API Key.'
            ], 401);
        }

        return $next($request);
    }
}