<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Election;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Models\Candidate;
use Botble\EdnElection\Models\Result;
use Botble\EdnElection\Models\Region; 
use Botble\EdnElection\Models\Zone;
use Botble\EdnElection\Services\SeatWinnerService;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;

class ResultController extends BaseController
{
    public function index(Request $request)
    {
        $electionId = $request->input('election_id');
        $election = $electionId ? Election::find($electionId) : Election::latest()->first();

        // Get filter data
        $regions = Region::pluck('name', 'id')->all();
        $selectedRegion = $request->input('region_id');
        $selectedZone = $request->input('zone_id');

        $woredas = collect();
        
        if ($selectedZone && $election) {
            // Load woredas for the zone and ONLY candidates assigned for this specific election
            $woredas = Woreda::where('zone_id', $selectedZone)
                ->with(['candidates' => function($query) use ($election) {
                    $query->wherePivot('election_id', $election->id);
                }])
                ->get();
        }

        // Map existing results so we can show them in the inputs
        $resultsMap = Result::where('election_id', $election?->id)
            ->get()
            ->groupBy('woreda_id')
            ->map(fn($item) => $item->keyBy('candidate_id')->map->votes_count);

        return view('plugins/edn-election::results.grid', compact(
            'election', 
            'woredas', 
            'resultsMap', 
            'regions', 
            'selectedRegion', 
            'selectedZone'
        ));
    }

    public function store(Request $request, BaseHttpResponse $response)
{
    $electionId = $request->input('election_id');
    $election = Election::findOrFail($electionId);
    
    // FIX: Define $gridData from the request
    $gridData = $request->input('results', []);
    
    $seatService = new SeatWinnerService();

    // 1. Save all incoming vote data first
    foreach ($gridData as $woredaId => $votes) {
        foreach ($votes as $candidateId => $votesCount) {
            if ($votesCount !== null && $votesCount !== '') {
                Result::updateOrCreate(
                    [
                        'election_id'  => $electionId,
                        'woreda_id'    => $woredaId,
                        'candidate_id' => $candidateId,
                    ],
                    ['votes_count' => (int)$votesCount]
                );
            }
        }
    }

    // 2. Determine Seat Allocation Logic
    if ($election->type === 'parliamentary') {
        // PROPORTIONAL REPRESENTATION: Distribute seats across the whole election
        $seatService->calculatePR((int)$electionId, (int)$election->total_seats);
    } else {
        // WINNER-TAKE-ALL: Mark individual winners for each Woreda updated
        foreach (array_keys($gridData) as $woredaId) {
            $seatService->handleWinnerTakeAll((int)$electionId, (int)$woredaId);
        }
    }

    return $response
        ->setPreviousUrl(route('election.results.index', [
            'election_id' => $electionId,
            'region_id'   => $request->input('region_id'),
            'zone_id'     => $request->input('zone_id')
        ]))
        ->setMessage('Election results and seat allocations updated successfully.');
}

    public function getZones(Request $request)
{
    // Validate that region_id exists
    if (!$request->region_id) {
        return response()->json([]);
    }

    $zones = Zone::where('region_id', $request->region_id)
        ->pluck('name', 'id')
        ->all();

    return response()->json($zones);
}

public function getHoRPStatus($chamberId)
{
    return Chamber::with(['regionalSeats.region'])->find($chamberId);
}


}