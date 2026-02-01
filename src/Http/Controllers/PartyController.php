<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Party; // IMPORTANT
use Botble\EdnElection\Tables\PartyTable;
use Botble\EdnElection\Forms\PartyForm;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Http\Request;

class PartyController extends BaseController
{
    public function index(PartyTable $table)
    {
        $this->pageTitle('Political Parties');
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Party');
        return $formBuilder->create(PartyForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
    {
        $party = Party::create($request->input());
        return $response
            ->setPreviousUrl(route('election.parties.index'))
            ->setMessage('Party created successfully');
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $party = Party::findOrFail($id);

        $this->pageTitle('Edit Party: ' . $party->name);

        return $formBuilder->create(PartyForm::class, ['model' => $party])->renderForm();
    }

    public function update(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $party = Party::findOrFail($id);

        $party->update($request->input());

        return $response
            ->setPreviousUrl(route('election.parties.index'))
            ->setMessage('Party updated successfully');
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        $party = Party::findOrFail($id);
        $party->delete();

        return $response->setMessage('Party deleted successfully');
    }
}