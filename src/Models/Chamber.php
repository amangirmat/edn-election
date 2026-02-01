<?php

namespace Botble\EdnElection\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chamber extends BaseModel
{
    protected $table = 'edn_chambers';

    protected $fillable = [
        'name',
        'level',
        'total_seats',
        'election_id', // <--- ADD THIS LINE
        'regional_seats', // JSON column
        'status',
    ];

    protected $casts = [
        'regional_seats' => 'array',
    ];

    /**
     * Relationship to the secondary table.
     * This is the method the Controller was looking for!
     */
    public function regionalSeats(): HasMany
    {
        return $this->hasMany(ChamberSeat::class, 'chamber_id');
    }

    public function election()
{
    return $this->belongsTo(Election::class, 'election_id');
}


}