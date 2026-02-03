namespace EdnElection\Imports;

use App\Models\Result;
use App\Models\Woreda;
use App\Models\Party;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ResultsSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Lookup IDs by the names provided in Excel
        $woreda = Woreda::where('name', $row['woreda_name'])->first();
        $party  = Party::where('abbreviation', $row['party_abbr'])->first();

        if ($woreda && $party) {
            return new Result([
                'election_id'  => $row['election_id'],
                'woreda_id'    => $woreda->id,
                'candidate_id' => $party->id,
                'votes_count'  => $row['votes'],
            ]);
        }
    }
}