<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Enums\BaseStatusEnum; // ADD THIS LINE
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends BaseModel
{
    protected $table = 'edn_regions';

    protected $fillable = [
        'name',
        'code', 
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}