<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\EdnElection\Forms\ChamberForm;
use Botble\EdnElection\Http\Requests\ChamberRequest;
use Botble\EdnElection\Models\Chamber;
use Botble\EdnElection\Tables\ChamberTable;
use Botble\Base\Forms\FormBuilder;
use Exception;
use Illuminate\Http\Request;

class ChamberController extends BaseController
{
    public function index(ChamberTable $table)
    {
        page_title()->setTitle('Manage Chambers');

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle('Create New Chamber');

        return $formBuilder->create(ChamberForm::class)->renderForm();
    }

    public function store(ChamberRequest $request, BaseHttpResponse $response)
    {
        $chamber = Chamber::query()->create($request->input());

        event(new CreatedContentEvent('chamber', $request, $chamber));

        // Sync the regional seat allocations from the repeater
        $this->syncRegionalSeats($chamber, $request->input('regional_seats', []));

        return $response
            ->setPreviousUrl(route('election.chambers.index'))
            ->setNextUrl(route('election.chambers.edit', $chamber->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit($id)
{
    $chamber = Chamber::findOrFail($id);

    return ChamberForm::createFromModel($chamber)->renderForm();
}


    public function update(int|string $id, ChamberRequest $request, BaseHttpResponse $response)
    {
        $chamber = Chamber::query()->findOrFail($id);

        $chamber->fill($request->input());
        $chamber->save();

        event(new UpdatedContentEvent('chamber', $request, $chamber));

        // Sync the regional seat allocations from the repeater
        $this->syncRegionalSeats($chamber, $request->input('regional_seats', []));

        return $response
            ->setPreviousUrl(route('election.chambers.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $chamber = Chamber::query()->findOrFail($id);

            $chamber->delete();

            event(new DeletedContentEvent('chamber', $request, $chamber));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * Process the repeater data and save to edn_chamber_seats table
     */
   /**
 * Process the repeater data and save to edn_chamber_seats table
 */
protected function syncRegionalSeats(Chamber $chamber, $data)
{
    // 1. Clear existing relations
    $chamber->regionalSeats()->delete();

    // 2. If no data, clear the JSON column and exit
    if (empty($data) || !is_array($data)) {
        $chamber->regional_seats = [];
        $chamber->save();
        return;
    }

    $formattedData = [];

    foreach ($data as $item) {
        $regionId = null;
        $seatCount = null;

        /**
         * Botble Repeaters can send data in two ways:
         * 1. Associative: ['region_id' => 1, 'seat_count' => 10]
         * 2. Key-Value pairs: [['key' => 'region_id', 'value' => 1], ...]
         */
        if (isset($item['region_id'])) {
            // Case 1: Associative
            $regionId = $item['region_id'];
            $seatCount = $item['seat_count'];
        } else {
            // Case 2: Key-Value pairs
            foreach ($item as $row) {
                if (isset($row['key'])) {
                    if ($row['key'] === 'region_id') $regionId = $row['value'];
                    if ($row['key'] === 'seat_count') $seatCount = $row['value'];
                }
            }
        }

        // Only save if we have a valid region and seat count
        if ($regionId && $seatCount !== null) {
            $chamber->regionalSeats()->create([
                'region_id'  => $regionId,
                'seat_count' => (int)$seatCount,
            ]);

            $formattedData[] = [
                'region_id'  => (int)$regionId,
                'seat_count' => (int)$seatCount,
            ];
        }
    }

    // 3. Save the formatted array back to the main table JSON column
    $chamber->regional_seats = $formattedData;
    $chamber->save();
}

public function getWidgetData($chamberId)
{
    $chamber = Chamber::with(['regionalSeats.region'])->findOrFail($chamberId);

    // Aggregate seats by party (assuming ChamberSeat has a party relationship or party_name)
    // For this example, we'll group by region or party as per your schema
    $results = $chamber->regionalSeats()
        ->select('party_name', 'party_color', \DB::raw('SUM(seat_count) as total_seats'))
        ->groupBy('party_name', 'party_color')
        ->orderByDesc('total_seats')
        ->get()
        ->map(function ($item) use ($chamber) {
            return (object) [
                'display_name' => $item->party_name,
                'party_color'  => $item->party_color ?? '#3498db',
                'value'        => $item->total_seats,
                'percent'      => ($chamber->total_seats > 0) ? ($item->total_seats / $chamber->total_seats) * 100 : 0,
                'unit'         => 'Seats'
            ];
        });

    $totalSeatsWon = $results->sum('value');
    
    return view('plugins/edn-election::widgets.chamber-leaderboard', [
        'results'          => $results,
        'chamber'          => $chamber,
        'totalSeatsWon'    => $totalSeatsWon,
        'isParliamentary'  => true,
    ]);
}


}