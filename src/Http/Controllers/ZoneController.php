<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Zone;
use Botble\EdnElection\Tables\ZoneTable;
use Botble\EdnElection\Forms\ZoneForm;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Http\Request;
use Botble\EdnElection\Traits\HasImportExport;


class ZoneController extends BaseController
{
    public function index(ZoneTable $table)
    {
        $this->pageTitle('Zones');
        
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Zone');
        return $formBuilder->create(ZoneForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
    {
        Zone::create($request->input());
        return $response->setPreviousUrl(route('election.zones.index'))->setMessage('Zone created successfully');
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $zone = Zone::findOrFail($id);
        $this->pageTitle('Edit Zone: ' . $zone->name);
        return $formBuilder->create(ZoneForm::class, ['model' => $zone])->renderForm();
    }

    public function update(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $zone = Zone::findOrFail($id);
        $zone->update($request->input());
        return $response->setPreviousUrl(route('election.zones.index'))->setMessage('Updated successfully');
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        Zone::findOrFail($id)->delete();
        return $response->setMessage('Deleted successfully');
    }

    use HasImportExport;

  // ZoneController.php

public function postImportPreview(Request $request) {
    // Standardize to array
    return $this->performImportPreview($request, Zone::class, ['name', 'region_id']);
}

public function postImportSave(Request $request) {
    return $this->performImportSave($request, Zone::class, ['name', 'region_id']);
}



}