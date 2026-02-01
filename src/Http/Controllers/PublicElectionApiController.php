<?php

namespace Botble\EdnElection\Http\Controllers\Api;

use Botble\EdnElection\Models\Election;
use Botble\EdnElection\Models\Result;
use Botble\EdnElection\Models\Woreda;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PublicElectionApiController extends Controller
{
    // Caching for 5 minutes to ensure high performance under load
    protected $cacheTime = 300; 

    public function getElections()
    {
        return Cache::remember('api_elections_list', $this->cacheTime, function () {
            return Election::where('status', 'published')->get(['id', 'name', 'type', 'status']);
        });
    }

    public function getNationalResults(Request $request)
    {
        $electionId = $request->query('election_id');
        
        return Cache::remember("results_national_{$electionId}", $this->cacheTime, function () use ($electionId) {
            $totalVotes = Result::where('election_id', $electionId)->sum('votes_count');
            $totalRegistered = Woreda::sum('total_voters');

            $standings = Result::where('election_id', $electionId)
                ->join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
                ->selectRaw('edn_candidates.name, SUM(votes_count) as votes')
                ->groupBy('edn_candidates.id', 'edn_candidates.name')
                ->orderBy('votes', 'desc')
                ->get();

            return response()->json([
                'election_id' => $electionId,
                'summary' => [
                    'total_registered' => (int)$totalRegistered,
                    'total_votes' => (int)$totalVotes,
                    'turnout_percentage' => $totalRegistered > 0 ? round(($totalVotes / $totalRegistered) * 100, 2) : 0,
                ],
                'standings' => $standings
            ]);
        });
    }

    public function getRegionResults($id, Request $request)
    {
        $electionId = $request->query('election_id');
        // Logic similar to National but filtered by Region ID via Woreda->Zone relationship
    }
}