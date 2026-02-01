<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends BaseModel
{
    protected $table = 'edn_zones';
    protected $fillable = ['name', 'region_id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function woredas(): HasMany
    {
        return $this->hasMany(Woreda::class);
    }
}