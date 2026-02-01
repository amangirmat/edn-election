<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// ADD THIS LINE:
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Woreda extends BaseModel
{
    protected $table = 'edn_woredas';
    protected $fillable = ['name', 'zone_id', 'total_voters'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Relationship to candidates through the 3-way pivot table
     */
   public function candidates(): BelongsToMany
{
    return $this->belongsToMany(
        Candidate::class, 
        'edn_candidate_woreda', // Pivot table name
        'woreda_id',           // Foreign key on pivot table for Woreda
        'candidate_id'         // Foreign key on pivot table for Candidate
    )->withPivot('election_id');
}
}