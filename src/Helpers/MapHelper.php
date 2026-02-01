<?php

namespace Botble\EdnElection\Helpers;

use Illuminate\Support\Collection;

class MapHelper
{

public function getEthiopiaRegions() {
    $path = storage_path('geo/ethiopia-regions.json');
    $json = json_decode(file_get_contents($path), true);
    
    return collect($json['features'])->map(function ($feature) {
        return [
            'id'   => $feature['properties']['shapeISO'],
            'name' => $feature['properties']['shapeName']
        ];
    });
}
}