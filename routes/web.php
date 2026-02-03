<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

// ONE main group for all admin routes in the plugin
Route::group([
    'namespace'  => 'Botble\EdnElection\Http\Controllers',
    'middleware' => ['web', 'core', 'auth'],
    'prefix'     => BaseHelper::getAdminPrefix() . '/edn-election',
], function () {

   // --- IMPORT / EXPORT ROUTES ---
// platform/plugins/edn-election/routes/web.php

// C:\xampp\htdocs\DailyNews\platform\plugins\edn-election\routes\web.php

Route::group(['prefix' => 'edn-election', 'as' => 'edn.election.'], function () {
    
    // The Preview Route (The one that was refreshing)
    Route::post('import/preview', [
        'as'   => 'import.preview',
        'uses' => 'ExportImportController@postImportPreview',
    ]);

    // The Save Route (Triggered from the preview page)
    Route::post('import/save', [
        'as'   => 'import.save',
        'uses' => 'ExportImportController@postImportSave',
    ]);

    // The Export Route
    Route::get('export', [
        'as'   => 'export',
        'uses' => 'ExportImportController@export',
    ]);
});
    // --- ELECTION MANAGER GROUP ---
    Route::group(['prefix' => 'elections', 'as' => 'election.'], function () {
        Route::resource('', 'ElectionController')->parameters(['' => 'election']);
        
        Route::get('summary/{election_id}', [
            'as'   => 'summary',
            'uses' => 'ElectionController@getSummary',
        ]);
    });

    // --- GEOGRAPHIC ROUTES ---
    Route::group(['prefix' => 'geographic', 'as' => 'election.'], function () {
        Route::resource('regions', 'RegionController')->parameters(['regions' => 'region']);
        Route::resource('zones', 'ZoneController')->parameters(['zones' => 'zone']);
        Route::resource('woredas', 'WoredaController')->parameters(['woredas' => 'woreda']);
    });

    // --- PARTY & CANDIDATE & CHAMBER ---
    Route::group(['as' => 'election.'], function() {
        Route::resource('parties', 'PartyController')->parameters(['parties' => 'party'])->names('parties');
        Route::resource('candidates', 'CandidateController')->parameters(['candidates' => 'candidate'])->names('candidates');
        Route::resource('chambers', 'ChamberController')->parameters(['chambers' => 'chamber'])->names('chambers');
    });

    // --- RESULTS MANAGEMENT ---
    Route::group(['prefix' => 'results', 'as' => 'election.results.'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'ResultController@index']);
        Route::get('get-zones', ['as' => 'get-zones', 'uses' => 'ResultController@getZones']);
        Route::post('save-grid', ['as' => 'store', 'uses' => 'ResultController@store']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ResultController@getEdit']);
        Route::post('edit/{id}', ['as' => 'update', 'uses' => 'ResultController@postEdit']);
    });
});

// --- PUBLIC ROUTES (No Auth / No Admin Prefix) ---
Route::group(['namespace' => 'Botble\EdnElection\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('elections/region/{id}', [
        'as'   => 'public.election.region',
        'uses' => 'PublicElectionController@getRegion',
    ]);
});