<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Tables\WoredaTable;
use Botble\EdnElection\Forms\WoredaForm;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Http\Request;

class WoredaController extends BaseController
{
    public function index(WoredaTable $table)
    {
        $this->pageTitle('Woredas');
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Woreda');
        return $formBuilder->create(WoredaForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
    {
        Woreda::create($request->input());
        return $response->setPreviousUrl(route('election.woredas.index'))->setMessage('Woreda created successfully');
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $woreda = Woreda::findOrFail($id);
        $this->pageTitle('Edit Woreda: ' . $woreda->name);
        return $formBuilder->create(WoredaForm::class, ['model' => $woreda])->renderForm();
    }

    public function update(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $woreda = Woreda::findOrFail($id);
        $woreda->update($request->input());
        return $response->setPreviousUrl(route('election.woredas.index'))->setMessage('Updated successfully');
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        Woreda::findOrFail($id)->delete();
        return $response->setMessage('Deleted successfully');
    }
}