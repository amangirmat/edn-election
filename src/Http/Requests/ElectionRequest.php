<?php

namespace Botble\EdnElection\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ElectionRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:220',
            'type'          => 'required|string',
            'election_date' => 'required|date',
            'status'        => 'required|string',
        ];
    }
}