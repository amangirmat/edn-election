<?php

namespace Botble\EdnElection\Traits;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

trait HasImportExport
{
   public function performImportPreview(Request $request, string $modelClass, $uniqueColumn = 'name')
    {
        $response = \Botble\Base\Http\Responses\BaseHttpResponse::make();

        if (!$request->hasFile('file')) {
            return $response->setError()->setMessage('No file found in request.');
        }

        try {
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('file'));
            $rows = $data[0] ?? [];
            
            if (empty($rows)) {
                return $response->setError()->setMessage('The uploaded file is empty.');
            }

            $headers = array_shift($rows); 
            $uniqueColumns = (array) $uniqueColumn;
            
            // CLEANING FUNCTION: Remove everything except a-z and 0-9
            $cleanString = function($str) {
                return preg_replace('/[^a-z0-9]/', '', strtolower(trim($str)));
            };

            // Standardize Excel headers
            $fileHeaders = array_map($cleanString, $headers);

            $missingColumns = [];
            foreach ($uniqueColumns as $required) {
                // Standardize the required column name (e.g., "region_id" becomes "regionid")
                $cleanRequired = $cleanString($required);
                
                if (!in_array($cleanRequired, $fileHeaders)) {
                    $missingColumns[] = ucwords(str_replace('_', ' ', $required));
                }
            }

            if (!empty($missingColumns)) {
                $modelName = (new \ReflectionClass($modelClass))->getShortName();
                return $response->setError()
                    ->setMessage("Invalid file! Required columns missing: " . implode(', ', $missingColumns));
            }

            // Success: Proceed to preview
            $existingData = $modelClass::select($uniqueColumns)->get();
            $tempPath = $request->file('file')->store('temp-imports');

            return view('plugins/edn-election::admin.import.preview', [
                'headers'        => $headers,
                'rows'           => $rows,
                'existingData'   => json_encode($existingData),
                'uniqueColumns'  => $uniqueColumns,
                'tempPath'       => $tempPath,
                'modelClass'     => $modelClass,
                'modelName'      => (new \ReflectionClass($modelClass))->getShortName(),
            ]);

        } catch (\Exception $e) {
            return $response->setError()->setMessage('Error: ' . $e->getMessage());
        }
    }

    public function postImportSave(Request $request, BaseHttpResponse $response)
{
    $modelClass = $request->input('model_class');

    if (!$modelClass || !class_exists($modelClass)) {
        return $response->setError()->setMessage('Invalid Model Class');
    }

    try {
        // 1. Get the specific unique validator for this model
        $uniqueColumns = $this->getUniqueColumns($modelClass);
        
        // 2. Perform the save via the Trait
        $this->performImportSave($request, $modelClass, $uniqueColumns);

        // 3. DYNAMIC REDIRECT
        // We get the short name (e.g., "Woreda") to find the correct route (e.g., "woreda.index")
        $reflect = new \ReflectionClass($modelClass);
        $shortName = strtolower($reflect->getShortName());

        // We check both admin and web routes based on your setup
        $nextUrl = route('dashboard.index'); // Default fallback
        
        if (\Route::has($shortName . '.index')) {
            $nextUrl = route($shortName . '.index');
        } elseif (\Route::has('admin.' . $shortName . '.index')) {
            $nextUrl = route('admin.' . $shortName . '.index');
        }

        return $response
            ->setNextUrl($nextUrl)
            ->setMessage(trans('core/base::notices.update_success_message'));

    } catch (Exception $e) {
        return $response->setError()->setMessage('Save failed: ' . $e->getMessage());
    }
}
}