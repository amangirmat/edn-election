<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Election;
use Botble\EdnElection\Models\Result;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Models\Region;
use Botble\EdnElection\Tables\ElectionTable;
use Botble\EdnElection\Forms\ElectionForm;
use Botble\EdnElection\Http\Requests\ElectionRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectionController extends BaseController
{
    public function index(ElectionTable $table)
    {
        $this->pageTitle('Elections');
        
        // This single line renders the entire Botble Table UI!
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Election');
        return $formBuilder->create(ElectionForm::class)->renderForm();
    }

     public function store(ElectionRequest $request, BaseHttpResponse $response)
{
    // Now that the column exists, we use input() to include 'type'
    $election = Election::create($request->input());
    event(new CreatedContentEvent('election', $request, $election));

    return $response
        ->setPreviousUrl(route('election.index'))
        ->setNextUrl(route('election.edit', $election->id))
        ->setMessage('Election created successfully');
}

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $election = Election::findOrFail($id);
        $this->pageTitle('Edit Election: ' . $election->name);
        return $formBuilder->create(ElectionForm::class, ['model' => $election])->renderForm();
    }



public function update(int|string $id, ElectionRequest $request, BaseHttpResponse $response)
{
    $election = Election::findOrFail($id);
    
    // This will now successfully save the 'type' field
    $election->update($request->input());
    
    event(new UpdatedContentEvent('election', $request, $election));

    return $response
        ->setPreviousUrl(route('election.index'))
        ->setMessage('Election updated successfully');
}

    /**
     * Show the Election Summary Dashboard with Real Data
     */
    /**
     * Show the Election Summary Dashboard
     */
    public function getSummary(int|string $electionId, BaseHttpResponse $response)
{
    $election = Election::findOrFail($electionId);
    $this->pageTitle('Election Dashboard: ' . $election->name);

    // 1. Core Counts - Using the correct column 'total_voters'
    // Total Registered across all woredas
    $totalRegistered = Woreda::sum('total_voters') ?: 0;
    
    // Total Votes for this election
    $totalVotes = Result::where('election_id', $electionId)->sum('votes_count');

    // 2. Filtered Candidate Count (Only those appearing in results for this election)
    $totalCandidates = Result::where('election_id', $electionId)
        ->distinct('candidate_id')
        ->count('candidate_id');

    // 3. Filtered Party Count (Only parties of candidates in this election)
    $totalParties = Result::where('election_id', $electionId)
        ->join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
        ->distinct('edn_candidates.party_id')
        ->count('edn_candidates.party_id');

    // 4. Calculations
    $turnout = $totalRegistered > 0 ? ($totalVotes / $totalRegistered) * 100 : 0;
    $totalWoredas = Woreda::count();
    $reportingWoredas = Result::where('election_id', $electionId)
        ->distinct('woreda_id')
        ->count('woreda_id');

    // 5. Candidate Standings
    $candidateStandings = Result::where('election_id', $electionId)
        ->join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
        ->leftJoin('edn_parties', 'edn_candidates.party_id', '=', 'edn_parties.id')
        ->select(
            'edn_candidates.name', 
            'edn_parties.name as party_name',
            DB::raw('SUM(edn_election_results.votes_count) as total_votes')
        )
        ->groupBy('edn_candidates.id', 'edn_candidates.name', 'party_name')
        ->orderBy('total_votes', 'DESC')
        ->get()
        ->map(function($item) use ($totalVotes) {
            $item->percentage = $totalVotes > 0 ? ($item->total_votes / $totalVotes) * 100 : 0;
            return $item;
        });

    // 6. Regional Stats - Corrected column name here too
    $regionalStats = Region::select('name', 'id')->get()->map(function ($region) use ($electionId) {
        $votes = Result::where('election_id', $electionId)
            ->whereHas('woreda.zone', function($query) use ($region) {
                $query->where('region_id', $region->id);
            })->sum('votes_count');

        // Summing the total_voters column per region
        $regInRegion = Woreda::whereHas('zone', function($q) use ($region) {
            $q->where('region_id', $region->id);
        })->sum('total_voters') ?: 0;

        return (object) [
            'name' => $region->name,
            'votes' => $votes,
            'registered' => $regInRegion,
            'reporting_perc' => $regInRegion > 0 ? round(($votes / $regInRegion) * 100, 1) : 0
        ];
    });

    return view('plugins/edn-election::results.summary', compact(
        'election', 'totalVotes', 'turnout', 'totalWoredas', 'reportingWoredas',
        'candidateStandings', 'regionalStats', 'totalRegistered', 'totalParties', 'totalCandidates'
    ));
}

}