<?php

namespace Botble\EdnElection\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ChamberRequest extends Request
{
    public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'level' => 'required|string',
        'total_seats' => 'required|integer|min:1',
        
        // Allow the main field to be an array
        'regional_seats' => 'nullable|array',
        
        /** * ❌ REMOVE THIS:
         * 'regional_seats.*' => 'required|integer|min:0', 
         *
         * ✅ USE THESE INSTEAD (targets the nested keys):
         */
        'regional_seats.*.region_id'  => 'nullable|integer',
        'regional_seats.*.seat_count' => 'nullable|integer|min:0',
    ];
}

    public function authorize(): bool
    {
        return true;
    }
}
