<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/v1/election',
    'namespace' => 'Botble\EdnElection\Http\Controllers\API',
    'middleware' => ['api']
], function () {
    // Endpoints will go here in Phase 2
});