<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends BaseModel
{
    protected $table = 'edn_regions';
    protected $fillable = ['name', 'status'];

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}