<?php

namespace Botble\EdnElection\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\EdnElection\Traits\HasImportExport;
use Illuminate\Http\Request;
use Botble\Base\Http\Responses\BaseHttpResponse; // CORRECT IMPORT
use Botble\EdnElection\Exports\ElectionExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ExportImportController extends BaseController
{
    use HasImportExport;

    /**
     * Define unique columns based on the model class.
     */
    protected function getUniqueColumns(string $modelClass): array
    {
        return match (true) {
            str_contains($modelClass, 'Woreda')  => ['name', 'zone_id'],
            str_contains($modelClass, 'Zone')    => ['name', 'region_id'],
            str_contains($modelClass, 'Polling') => ['name', 'woreda_id'],
            str_contains($modelClass, 'Region')  => ['name', 'code'],
            str_contains($modelClass, 'Candidate')   => ['name', 'party_id'],
            str_contains($modelClass, 'Party')   => ['name', 'abbreviation'],
            default                              => ['name'],
        };
    }

    /**
     * Handle the initial file upload and show the mapping preview.
     */
    public function postImportPreview(Request $request)
    {
        $modelClass = $request->input('model_class');

        if (!$modelClass || !class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Import context (Model) is missing.');
        }

        try {
            $uniqueColumns = $this->getUniqueColumns($modelClass);
            return $this->performImportPreview($request, $modelClass, $uniqueColumns);
        } catch (Exception $e) {
            if (config('app.debug')) {
                dd('Preview Error: ' . $e->getMessage());
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * This is the method the form actually calls.
     */
    public function postImportSave(Request $request, BaseHttpResponse $response)
    {
        $modelClass = $request->input('model_class');

        if (!$modelClass || !class_exists($modelClass)) {
            return $response->setError()->setMessage('Invalid Model Class');
        }

        try {
            $uniqueColumns = $this->getUniqueColumns($modelClass);
            
            // Execute the save logic
            $this->executeImportSaving($request, $modelClass, $uniqueColumns);

            /**
             * Determine redirect URL based on your specific route names.
             * Based on your controllers, the routes are prefixed with 'election.'
             */
            $nextUrl = match (true) {
                str_contains($modelClass, 'Woreda')  => route('election.woredas.index'),
                str_contains($modelClass, 'Zone')    => route('election.zones.index'),
                str_contains($modelClass, 'Region')  => route('election.regions.index'),
                str_contains($modelClass, 'Candidate') => route('election.candidates.index'),
                str_contains($modelClass, 'Polling') => route('election.pollings.index'),
                str_contains($modelClass, 'Party')   => route('election.parties.index'),
                default                              => url()->previous(), // Fallback to where they came from
            };

            return $response
                ->setNextUrl($nextUrl)
                ->setMessage(trans('core/base::notices.update_success_message'));

        } catch (Exception $e) {
            return $response->setError()->setMessage('Save failed: ' . $e->getMessage());
        }
    }

    /**
     * Internal logic to handle the actual database insertion/update.
     */
    protected function executeImportSaving(Request $request, string $modelClass, array $checkColumns)
    {
        $items = $request->input('items', []);

        DB::transaction(function () use ($items, $modelClass, $checkColumns) {
            foreach ($items as $item) {
                $formattedData = [];
                
                foreach ($item as $header => $value) {
                    // Turn "Region ID" into "region_id" to match DB columns
                    $key = Str::snake(trim($header));
                    $formattedData[$key] = $value;
                }

                $searchCriteria = [];
                foreach ($checkColumns as $col) {
                    if (array_key_exists($col, $formattedData)) {
                        $searchCriteria[$col] = $formattedData[$col];
                    }
                }

                if (!empty($searchCriteria)) {
                    // Update if exists, otherwise create
                    $modelClass::updateOrCreate($searchCriteria, $formattedData);
                } else {
                    $modelClass::create($formattedData);
                }
            }
        });

        if ($request->has('temp_path')) {
            Storage::delete($request->input('temp_path'));
        }

        return true;
    }

    /**
     * Handle Export
     */
    public function export(Request $request)
{
    $table = $request->input('table');
    // Botble sends selected IDs as an array named 'ids'
    $ids = $request->input('ids'); 

    if (!$table) {
        return redirect()->back()->with('error', 'Table parameter is missing.');
    }

    $filename = "selected_{$table}_" . date('Y-m-d') . ".xlsx";

    try {
        // Pass both the table name and the selected IDs to the Export class
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \Botble\EdnElection\Exports\ElectionExport($table, $ids), 
            $filename
        );
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}
}