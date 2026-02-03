<?php

namespace Botble\EdnElection\Exports;

use Botble\EdnElection\Models\Result; // Updated to match your file: Result.php
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResultsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        // Eager load relationships to prevent N+1 query issues
        return Result::with(['woreda', 'candidate'])->get();
    }

    public function map($result): array
    {
        return [
            $result->woreda ? $result->woreda->name : 'N/A',
            $result->candidate ? $result->candidate->name : 'N/A', // Using candidate relation
            $result->votes_count, // Matches your $fillable 'votes_count'
        ];
    }

    public function headings(): array 
    { 
        return ['Woreda', 'Candidate', 'Votes Count']; 
    }

    public function title(): string 
    { 
        return 'Election Results'; 
    }
}