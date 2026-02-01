<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Add this

class Election extends BaseModel
{
    protected $table = 'edn_elections';

    protected $fillable = [
        'name',
        'type',
        'election_date',
        'status',
        'region_id', // Ensure this matches your database column name
    ];

    protected $casts = [
        'status' => 'string',
        'election_date' => 'date',
    ];

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'edn_candidate_woreda')
                    ->withPivot('woreda_id');
    }

    // Add this relationship
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id')->withDefault([
            'name' => 'National'
        ]);
    }
}