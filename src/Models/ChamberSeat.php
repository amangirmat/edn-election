<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Botble\Location\Models\Region;

class ChamberSeat extends BaseModel
{
    protected $table = 'edn_chamber_seats';

    protected $fillable = ['chamber_id', 'region_id', 'seat_count'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}