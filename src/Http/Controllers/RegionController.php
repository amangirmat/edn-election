<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Models\Region;
use Botble\EdnElection\Tables\RegionTable;
use Botble\EdnElection\Forms\RegionForm;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Botble\EdnElection\Traits\HasImportExport;

class RegionController extends BaseController
{
    public function index(RegionTable $table)
    {
        $this->pageTitle('Regions');
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle('Create Region');
        return $formBuilder->create(RegionForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
    {
        $region = Region::create($request->input());

        // ✅ Call the new dynamic sync method
        $this->syncChamberSeats($region->id, $request);

        return $response
            ->setPreviousUrl(route('election.regions.index'))
            ->setMessage('Region and Chamber Allocations created successfully');
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $region = Region::findOrFail($id);
        $this->pageTitle('Edit Region: ' . $region->name);
        return $formBuilder->create(RegionForm::class, ['model' => $region])->renderForm();
    }

    public function update(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $region = Region::findOrFail($id);
        $region->update($request->input());

        // ✅ Sync the dynamic repeater data
        $this->syncChamberSeats($region->id, $request);

        return $response
            ->setPreviousUrl(route('election.regions.index'))
            ->setMessage('Updated successfully');
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        // Also clean up pivot data on delete
        DB::table('edn_chamber_seats')->where('region_id', $id)->delete();
        Region::findOrFail($id)->delete();
        
        return $response->setMessage('Deleted successfully');
    }

    /**
     * ✅ New Dynamic Sync Method for Multi-Chamber support
     */
    protected function syncChamberSeats($regionId, Request $request)
{
    $allocations = $request->input('chamber_allocations', []);
    DB::table('edn_chamber_seats')->where('region_id', $regionId)->delete();

    foreach ($allocations as $item) {
        // [0] is the chamber select, [1] is the seat count number
        $chamberId = $item[0]['value'] ?? null;
        $seatCount = $item[1]['value'] ?? 0;

        if ($chamberId) {
            DB::table('edn_chamber_seats')->insert([
                'region_id'  => $regionId,
                'chamber_id' => $chamberId,
                'seat_count' => $seatCount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
// C:\xampp\htdocs\DailyNews\platform\plugins\edn-election\src\Http\Controllers\RegionController.php

use HasImportExport;

public function postImportPreview(Request $request) {
    // FIX: Changed from performImportSave to performImportPreview
    return $this->performImportPreview($request, Region::class);
}

public function postImportSave(Request $request) {
    return $this->performImportSave($request, Region::class, ['name', 'code']);

}
}