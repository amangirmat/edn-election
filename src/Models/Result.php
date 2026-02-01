<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends BaseModel
{
    protected $table = 'edn_election_results';

    protected $fillable = [
        'election_id',
        'woreda_id',
        'candidate_id',
        'votes_count',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class)->withDefault();
    }

    public function woreda(): BelongsTo
    {
        return $this->belongsTo(Woreda::class)->withDefault();
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class)->withDefault();
    }
}