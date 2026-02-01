<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Candidate;
use Botble\EdnElection\Tables\CandidateTable;
use Botble\EdnElection\Forms\CandidateForm;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Botble\EdnElection\Models\Election;  // <--- ADD THIS LINE
use Illuminate\Http\Request;

class CandidateController extends BaseController
{
    public function index(CandidateTable $table)
    {
        $this->pageTitle('Candidates');
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Candidate');
        return $formBuilder->create(CandidateForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
{
    $candidate = Candidate::create($request->input());

    if ($request->has('woredas')) {
        // 1. Get the current active election ID
        $electionId = Election::latest()->first()?->id; 
        
        $woredaIds = collect($request->input('woredas'))->flatten()->filter()->all();
        
        // 2. Prepare data with election_id for the pivot table
        $syncData = [];
        foreach ($woredaIds as $id) {
            $syncData[$id] = ['election_id' => $electionId];
        }

        $candidate->woredas()->sync($syncData);
    }

    return $response->setMessage('Candidate created and assigned to Woredas');
}

    public function update(int|string $id, Request $request, BaseHttpResponse $response)
{
    $candidate = Candidate::findOrFail($id);
    $candidate->update($request->except('woredas'));

    $woredas = $request->input('woredas', []);
    $woredaIds = collect($woredas)->flatten()->all();

    // Option A: If election_id is Nullable
    $candidate->woredas()->sync($woredaIds);

    // Option B: If you want to link to an election (e.g., Election ID 1)
    // $syncData = [];
    // foreach ($woredaIds as $id) { $syncData[$id] = ['election_id' => 1]; }
    // $candidate->woredas()->sync($syncData);

    return $response
        ->setPreviousUrl(route('election.candidates.index'))
        ->setMessage('Candidate updated successfully');
}

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $candidate = Candidate::findOrFail($id);
        $this->pageTitle('Edit Candidate: ' . $candidate->name);
        return $formBuilder->create(CandidateForm::class, ['model' => $candidate])->renderForm();
    }



    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        Candidate::findOrFail($id)->delete();
        return $response->setMessage('Deleted successfully');
    }
}