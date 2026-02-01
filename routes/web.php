<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\EdnElection\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/edn-election', 'middleware' => 'auth'], function () {
        
        // --- ELECTION MANAGER GROUP ---
        Route::group(['prefix' => 'elections', 'as' => 'election.'], function () {
            Route::resource('', 'ElectionController')->parameters(['' => 'election']);

            // FIXED: Moving summary INSIDE this group gives it the 'election.summary' name
            Route::get('summary/{election_id}', [
                'as'   => 'summary',
                'uses' => 'ElectionController@getSummary',
            ]);
        });

        // --- PARTY MANAGER ---
        Route::group(['prefix' => 'parties', 'as' => 'election.parties.'], function () {
            Route::resource('', 'PartyController')->parameters(['' => 'party']);
        });

        // --- CANDIDATE MANAGER ---
        Route::group(['prefix' => 'candidates', 'as' => 'election.candidates.'], function () {
            Route::resource('', 'CandidateController')->parameters(['' => 'candidate']);
        });

        // --- Chamber MANAGER ---

        Route::group(['prefix' => 'chambers', 'as' => 'election.chambers.'], function () {
            Route::resource('', 'ChamberController')->parameters(['' => 'chamber']);
        });

        Route::group(['namespace' => 'Botble\EdnElection\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    
    // Add this specific route
    Route::get('elections/region/{id}', [
        'as'   => 'public.election.region',
        'uses' => 'PublicElectionController@getRegion',
    ]);

});

        // --- RESULTS MANAGEMENT GROUP ---
        Route::group(['prefix' => 'results', 'as' => 'election.results.'], function () {
            Route::get('', [
                'as'   => 'index',
                'uses' => 'ResultController@index',
            ]);

            Route::get('get-zones', [
                'as'   => 'get-zones',
                'uses' => 'ResultController@getZones',
            ]);

            Route::post('save-grid', [
                'as'   => 'store',
                'uses' => 'ResultController@store',
            ]);

            Route::get('edit/{id}', [
                'as'   => 'edit',
                'uses' => 'ResultController@getEdit',
                'permission' => 'election.edit',
            ]);
            
            Route::post('edit/{id}', [
                'as'   => 'update',
                'uses' => 'ResultController@postEdit',
                'permission' => 'election.edit',
            ]);
        });

        

        // --- GEOGRAPHIC ROUTES ---
        Route::group(['prefix' => 'geographic', 'as' => 'election.'], function () {
            Route::resource('regions', 'RegionController')->parameters(['regions' => 'region']);
            Route::resource('zones', 'ZoneController')->parameters(['zones' => 'zone']);
            Route::resource('woredas', 'WoredaController')->parameters(['woredas' => 'woreda']);
        });
    });
});