<?php

namespace Botble\EdnElection\Services;

use Botble\EdnElection\Models\Result;
use Botble\EdnElection\Models\Election;
use DB;

class SeatWinnerService
{
    /**
     * Logic for "Winner-Take-All" (Woreda level)
     * Used for local races where one candidate wins the seat.
     */
    public function handleWinnerTakeAll(int $electionId, int $woredaId)
{
    // Do nothing here if you aren't saving to the DB.
    // Or just return the ID of the winner to the caller:
    return Result::where('election_id', $electionId)
        ->where('woreda_id', $woredaId)
        ->orderByDesc('votes_count')
        ->first();
}

    /**
     * D'Hondt Method for Proportional Representation
     */
    public function calculatePR(int $electionId, int $totalSeats)
    {
        // 1. Reset all winners for this election to start fresh
        Result::where('election_id', $electionId)->update(['is_winner' => false]);

        // 2. Sum votes by Party
        $partyVotes = Result::join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
            ->where('edn_election_results.election_id', $electionId)
            ->select('edn_candidates.party_id', DB::raw('SUM(votes_count) as total_votes'))
            ->groupBy('edn_candidates.party_id')
            ->get();

        $allocations = $partyVotes->mapWithKeys(fn($item) => [
            $item->party_id => [
                'votes' => (int)$item->total_votes,
                'seats_won' => 0
            ]
        ])->toArray();

        // 3. Iteratively allocate seats
        for ($i = 0; $i < $totalSeats; $i++) {
            $winnerPartyId = null;
            $maxQuotient = -1;

            foreach ($allocations as $partyId => $data) {
                // Formula: Votes / (Seats + 1)
                $quotient = $data['votes'] / ($data['seats_won'] + 1);

                // Tie breaker: If quotients are equal, we check total votes
                if ($quotient > $maxQuotient) {
                    $maxQuotient = $quotient;
                    $winnerPartyId = $partyId;
                } elseif ($quotient == $maxQuotient && $winnerPartyId) {
                    if ($data['votes'] > $allocations[$winnerPartyId]['votes']) {
                        $winnerPartyId = $partyId;
                    }
                }
            }

            if ($winnerPartyId) {
                $allocations[$winnerPartyId]['seats_won']++;
            }
        }

        // 4. Update the 'is_winner' flag for the top candidates of those parties
        foreach ($allocations as $partyId => $data) {
            if ($data['seats_won'] > 0) {
                $this->markPartyWinners($electionId, $partyId, $data['seats_won']);
            }
        }
    }

    protected function markPartyWinners($electionId, $partyId, $seatCount)
    {
        $topResults = Result::join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
            ->where('edn_election_results.election_id', $electionId)
            ->where('edn_candidates.party_id', $partyId)
            ->orderByDesc('edn_election_results.votes_count')
            ->select('edn_election_results.id')
            ->take($seatCount)
            ->pluck('id');

        Result::whereIn('id', $topResults)->update(['is_winner' => true]);
    }
}