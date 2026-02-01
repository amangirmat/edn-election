<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;

class Party extends BaseModel
{
    protected $table = 'edn_parties';

    protected $fillable = [
        'name',
        'abbreviation',
        'logo',
        'color',
    ];
}