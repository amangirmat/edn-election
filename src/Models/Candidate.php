<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Candidate extends BaseModel
{
    protected $table = 'edn_candidates';

    protected $fillable = [
        'name',
        'party_id',
        'chamber_id', // Added this to allow saving from the form
        'image',
    ];

    /**
     * Relationship to the Political Party
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id')->withDefault();
    }

    /**
     * Relationship to the Chamber (HoRP or Regional Council)
     */
    public function chamber(): BelongsTo
    {
        return $this->belongsTo(Chamber::class, 'chamber_id')->withDefault();
    }

    /**
     * Relationship to Woredas (Pivot)
     */
    public function woredas(): BelongsToMany
    {
        return $this->belongsToMany(
            Woreda::class, 
            'edn_candidate_woreda', 
            'candidate_id',         
            'woreda_id'             
        );
    }
}