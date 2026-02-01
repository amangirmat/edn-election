<?php

namespace Botble\EdnElection\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Facades\DashboardMenu;

class EdnElectionServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('plugins/edn-election')
            ->loadAndPublishConfigurations(['permissions']);
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/edn-election')
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->loadRoutes(['web', 'api']);

               $this->loadViewsFrom(__DIR__ . '/../resources/views', 'edn-election');


        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                // 1. Parent Menu
                ->registerItem([
                    'id'          => 'cms-plugins-edn-election',
                    'priority'    => 5,
                    'parent_id'   => null,
                    'name'        => 'Election Center',
                    'icon'        => 'fa fa-voted-yea',
                    'url'         => null,
                    'permissions' => ['election.index'],
                ])
                // 2. Sub-menu: Elections
                ->registerItem([
                    'id'          => 'cms-plugins-edn-election-list',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Elections',
                    'icon'        => 'fa fa-list',
                    'url'         => route('election.index'),
                    'permissions' => ['election.index'],
                ])
                // 3. Sub-menu: Political Parties
                ->registerItem([
                    'id'          => 'cms-plugins-edn-election-parties',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Political Parties',
                    'icon'        => 'fa fa-users',
                    'url'         => route('election.parties.index'),
                    'permissions' => ['election.index'],
                ])
                // 4. Sub-menu: Candidates
                ->registerItem([
                    'id'          => 'cms-plugins-edn-election-candidates',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Candidates',
                    'icon'        => 'fa fa-user-tie',
                    'url'         => route('election.candidates.index'),
                    'permissions' => ['election.index'],
                ])
                // 5. Sub-menu: Regions
                ->registerItem([
                    'id'          => 'cms-plugins-edn-regions',
                    'priority'    => 4,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Regions',
                    'icon'        => 'fa fa-map',
                    'url'         => route('election.regions.index'),
                    'permissions' => ['election.index'],
                ])
                // 6. Sub-menu: Zones
                ->registerItem([
                    'id'          => 'cms-plugins-edn-zones',
                    'priority'    => 5,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Zones',
                    'icon'        => 'fa fa-map-pin',
                    'url'         => route('election.zones.index'),
                    'permissions' => ['election.index'],
                ])
                // 7. Sub-menu: Woredas
                ->registerItem([
                    'id'          => 'cms-plugins-edn-woredas',
                    'priority'    => 6,
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Woredas',
                    'icon'        => 'fa fa-city',
                    'url'         => route('election.woredas.index'),
                    'permissions' => ['election.index'],
                ])

                ->registerItem([
                    'id'          => 'cms-plugins-edn-results',
                    'priority'    => 0, // Make it the top item
                    'parent_id'   => 'cms-plugins-edn-election',
                    'name'        => 'Results Entry',
                    'icon'        => 'fa fa-chart-bar',
                    'url'         => route('election.results.index'),
                    'permissions' => ['election.index'],
                ])
                
            ->registerItem([
                'id'          => 'cms-plugins-edn-election-chambers', // Unique ID
                'priority'    => 2,                                   // Order in the list
                'parent_id'   => 'cms-plugins-edn-election',          // ID of your main Election menu
                'name'        => 'Chambers',                          // Display name
                'icon'        => 'fa fa-university',                  // FontAwesome icon
                'url'         => route('election.chambers.index'),    // The route we created
                'permissions' => ['election.chambers.index'],         // Permission key
            ]);
        });

// Shortcode registration
    if (class_exists('Botble\Shortcode\Providers\ShortcodeServiceProvider')) {
        
        // 1. Map Shortcode
        add_shortcode('election_map', 'Election Map', 'Display Interactive Election Map', function ($shortcode) {
            $electionId = $shortcode->election_id ?? 1;
            return view('plugins/edn-election::shortcodes.election-map', compact('electionId'))->render();
        });

        // 2. Result Cards Shortcode
   add_shortcode('election-result-cards', 'Election Result Cards', 'Display candidate cards', function ($shortcode) {
    $electionId = $shortcode->election_id;

    // 1. Eager load election and region
    $election = \Botble\EdnElection\Models\Election::with(['candidates.party', 'region'])->find($electionId);

    if (!$election) return '';


    // --- NEW LOGIC FOR CHAMBER STYLE ---
    if ($cardStyle === 'chamber') {
        // Find the chamber associated with this election (adjust query if needed)
        $chamber = \Botble\EdnElection\Models\Chamber::where('election_id', $electionId)->first();
        
        if ($chamber) {
            $formattedResults = $chamber->regionalSeats()
                ->select('party_name', 'party_color', \DB::raw('SUM(seat_count) as total_seats'))
                ->groupBy('party_name', 'party_color')
                ->orderByDesc('total_seats')
                ->get()
                ->map(function ($item) use ($chamber) {
                    return (object) [
                        'display_name' => $item->party_name,
                        'party_color'  => $item->party_color ?: '#3498db',
                        'value'        => (int)$item->total_seats,
                        'votes'        => (int)$item->total_seats, // For blade compatibility
                        'percent'      => ($chamber->total_seats > 0) ? ($item->total_seats / $chamber->total_seats) * 100 : 0,
                        'unit'         => 'Seats',
                        'margin_ahead' => 0
                    ];
                });

           
        }
    }

    // 2. Calculate Total Voters (Handle National vs Regional)
$regionTotalVoters = 0;

if ($election->region_id) {
    $regionTotalVoters = \DB::table('edn_woredas')
        ->join('edn_zones', 'edn_woredas.zone_id', '=', 'edn_zones.id')
        ->where('edn_zones.region_id', $election->region_id)
        ->sum('edn_woredas.total_voters');
} else {
    // If no region_id, it's a National election - sum ALL woredas
    $regionTotalVoters = \DB::table('edn_woredas')->sum('total_voters');
}

    // 3. Group by Party to merge multiple candidates into one Party Total
    $partyResults = $election->candidates->groupBy('party_id')->map(function ($group) use ($electionId) {
        $party = $group->first()->party;
        $candidateIds = $group->pluck('id')->toArray();

        // Sum all votes for this party's candidates nationwide
        $totalPartyVotes = \DB::table('edn_election_results')
            ->where('election_id', $electionId)
            ->whereIn('candidate_id', $candidateIds)
            ->sum('votes_count');

        return (object)[
            'party_name'  => $party ? $party->name : 'Independent',
            'party_color' => $party ? $party->color : '#666',
            'votes'       => (int)$totalPartyVotes,
        ];
    })->sortByDesc('votes')->values();

    $totalVotesCast = $partyResults->sum('votes');
    

    // 4. Calculate Percentages and Lead Margins
    foreach ($partyResults as $index => $result) {
        $next = $partyResults->get($index + 1);
        $result->percent = $totalVotesCast > 0 ? ($result->votes / $totalVotesCast) * 100 : 0;
        $result->margin_ahead = ($index === 0 && $next) ? ($result->votes - $next->votes) : 0;
    }

    return view('plugins/edn-election::widgets.race-card', [
        'election'         => $election,
        'partyResults'     => $partyResults->take(5),
        'lastUpdated'      => now()->format('M d, Y h:i A'),
        'totalVotes'       => (int)$totalVotesCast, 
        'registeredVoters' => (int)$regionTotalVoters, 
    ]);
});
        // 3. Shortcode UI Config
shortcode()->setAdminConfig('election-result-cards', function ($attributes) {
    // Check if the model exists to prevent a 500 error if the table is empty
    try {
        $elections = \Botble\EdnElection\Models\Election::pluck('name', 'id')->all();
    } catch (\Exception $e) {
        $elections = [];
    }

    // Use .render() to ensure the AJAX call receives a full HTML string
    return view('plugins/edn-election::admin.shortcode-config', compact('attributes', 'elections'))->render();
});


// 1. Register the Regional Race Shortcode
add_shortcode('regional-race', 'Regional Race Results', 'Display results for National, Region, Zone, or Woreda', function ($shortcode) {
    $electionId = $shortcode->election_id;
    $regionId = $shortcode->region_id;
    $zoneId = $shortcode->zone_id;
    $woredaId = $shortcode->woreda_id;
    $cardStyle = $shortcode->card_style ?: 'default';

    if (!$electionId) return '';
    $election = \Botble\EdnElection\Models\Election::find($electionId);
    if (!$election) return '';

    // --- 1. Identify Linked Chamber ---
    $chamber = \Botble\EdnElection\Models\Chamber::where('election_id', $electionId)->first();
    $totalChamberSeats = (int)($chamber ? $chamber->total_seats : ($election->total_seats ?: 547));

    // --- 2. Scope & Titles ---
    $scope = 'national';
    $displayTitle = 'National Results';
    $parentTitle = null;
    $regionName = null;

    if ($woredaId) {
        $woreda = \Botble\EdnElection\Models\Woreda::with('zone.region')->find($woredaId);
        if ($woreda) {
            $scope = 'woreda'; 
            $displayTitle = $woreda->name . ' Woreda';
            $parentTitle = $woreda->zone ? $woreda->zone->name . ' Zone' : null;
            $regionName = ($woreda->zone && $woreda->zone->region) ? $woreda->zone->region->name : null;
        }
    } elseif ($zoneId) {
        $zone = \Botble\EdnElection\Models\Zone::with('region')->find($zoneId);
        if ($zone) { 
            $scope = 'zone'; 
            $displayTitle = $zone->name . ' Zone'; 
            $parentTitle = $zone->region ? $zone->region->name : null; 
        }
    } elseif ($regionId) {
        $region = \Botble\EdnElection\Models\Region::find($regionId);
        if ($region) { 
            $scope = 'region'; 
            $displayTitle = $region->name; 
        }
    }

    // --- 3. Voter Counts (For Footer Info) ---
    $voterQuery = \DB::table('edn_woredas');
    if ($scope === 'woreda') { 
        $voterQuery->where('id', $woredaId); 
    } elseif ($scope === 'zone') { 
        $voterQuery->where('zone_id', $zoneId); 
    } elseif ($scope === 'region') { 
        $voterQuery->join('edn_zones', 'edn_woredas.zone_id', '=', 'edn_zones.id')
                   ->where('edn_zones.region_id', $regionId); 
    }
    $totalRegisteredVotersCount = $voterQuery->sum('total_voters') ?: 0;

   // --- 4. DATA CALCULATION LOGIC ---
$query = \DB::table('edn_election_results')
    ->join('edn_candidates', 'edn_election_results.candidate_id', '=', 'edn_candidates.id')
    ->join('edn_parties', 'edn_candidates.party_id', '=', 'edn_parties.id')
    ->join('edn_woredas', 'edn_election_results.woreda_id', '=', 'edn_woredas.id') // Join woredas to filter by zone/region
    ->join('edn_zones', 'edn_woredas.zone_id', '=', 'edn_zones.id')
    ->where('edn_election_results.election_id', $electionId);

// Apply Dynamic Filters
if ($woredaId) {
    $query->where('edn_election_results.woreda_id', $woredaId);
} elseif ($zoneId) {
    $query->where('edn_woredas.zone_id', $zoneId);
} elseif ($regionId) {
    $query->where('edn_zones.region_id', $regionId);
}

$woredaData = $query->select([
        'edn_election_results.woreda_id',
        'edn_parties.id as party_id',
        'edn_parties.name as party_name',
        'edn_parties.abbreviation as party_short',
        'edn_parties.color as party_color',
        \DB::raw('SUM(edn_election_results.votes_count) as total_votes')
    ])
    ->groupBy('edn_election_results.woreda_id', 'edn_parties.id', 'edn_parties.name', 'edn_parties.abbreviation', 'edn_parties.color')
    ->get()
    ->groupBy('woreda_id');

$partyStats = [];
$woredaWinners = [];
$totalRawVotesCast = 0;

foreach ($woredaData as $wId => $resultsInWoreda) {
    // 1. Identify the winner of this specific Woreda
    $winner = $resultsInWoreda->sortByDesc('total_votes')->first();
    
    // 2. Sum up every party's votes in this Woreda for raw totals
    foreach ($resultsInWoreda as $row) {
        $pid = $row->party_id;
        if (!isset($partyStats[$pid])) {
            $partyStats[$pid] = [
                'name' => $row->party_name,
                'short' => $row->party_short,
                'color' => $row->party_color,
                'seats' => 0,
                'total_votes' => 0
            ];
        }
        $partyStats[$pid]['total_votes'] += $row->total_votes;
        $totalRawVotesCast += $row->total_votes;
    }

    // 3. Assign the Seat to the winner
    if ($winner && $winner->total_votes > 0) {
        $partyStats[$winner->party_id]['seats'] += 1;
    }
}

// Determine if we should display Seats or Votes in the list
$isParliamentary = ($election->type === 'parliamentary' && $scope !== 'woreda');

$formattedResults = collect($partyStats)->map(function ($data) use ($totalChamberSeats, $totalRawVotesCast, $isParliamentary) {
    // If parliamentary, value = seats. Otherwise, value = votes.
    $val = $isParliamentary ? $data['seats'] : $data['total_votes'];
    $totalForPerc = $isParliamentary ? $totalChamberSeats : $totalRawVotesCast;

    return (object)[
        'display_name' => $data['short'] ?: $data['name'],
        'party_color'  => $data['color'] ?: '#666',
        'value'        => $val, 
        'raw_votes'    => $data['total_votes'],
        'seats'        => $data['seats'],
        'percent'      => $totalForPerc > 0 ? ($val / $totalForPerc) * 100 : 0,
        'unit'         => $isParliamentary ? 'Seats' : 'Votes',
    ];
})->sortByDesc('value');

// --- 5. Build the Dots (Always based on Seats) ---
foreach ($formattedResults->sortByDesc('seats') as $res) {
    for ($i = 0; $i < $res->seats; $i++) {
        $woredaWinners[] = [
            'color' => $res->party_color,
            'party_name' => $res->display_name,
            'is_empty' => false
        ];
    }
}

// Fill remaining vacant seats
$allocatedCount = count($woredaWinners);
while ($allocatedCount < $totalChamberSeats) {
    $woredaWinners[] = ['color' => '#334155', 'party_name' => 'Vacant', 'is_empty' => true];
    $allocatedCount++;
}

$totalWins = array_sum(array_column($partyStats, 'seats'));

    // --- 5. Map & Regional Data ---
    $regionalVoters = \DB::table('edn_regions')
        ->join('edn_zones', 'edn_regions.id', '=', 'edn_zones.region_id')
        ->join('edn_woredas', 'edn_zones.id', '=', 'edn_woredas.zone_id')
        ->join('slugs', function($join) {
            $join->on('edn_regions.id', '=', 'slugs.reference_id')
                 ->where('slugs.reference_type', '=', 'Botble\EdnElection\Models\Region');
        })
        ->select('slugs.key as slug', \DB::raw('SUM(edn_woredas.total_voters) as total'))
        ->groupBy('slug')->pluck('total', 'slug');

    // --- 6. View Resolution ---
    $headerStyle = $shortcode->header_style ?: 'default';
    $footerStyle = $shortcode->footer_style ?: 'default';
    $styleViewName = $shortcode->card_style ?: 'default';

    $headerView = "plugins/edn-election::widgets.partials.headers.{$headerStyle}";
    $footerView = "plugins/edn-election::widgets.partials.footers.{$footerStyle}";
    $styleView  = "plugins/edn-election::widgets.styles.{$styleViewName}";

    if (!view()->exists($headerView)) $headerView = 'plugins/edn-election::widgets.partials.headers.default';
    if (!view()->exists($footerView)) $footerView = 'plugins/edn-election::widgets.partials.footers.default';
    if (!view()->exists($styleView))  $styleView  = 'plugins/edn-election::widgets.styles.default';

    return view('plugins/edn-election::widgets.regional-race', [
        'election'         => $election,
        'displayTitle'     => $displayTitle,
        'parentTitle'      => $parentTitle,
        'regionName'       => $regionName,
        'regionalVoters'   => $regionalVoters,
        'partyResults'     => $formattedResults->take(10),
        'results'          => $formattedResults,
        'woredaWinners'    => $woredaWinners,
        'lastUpdated'      => now()->format('M d, Y h:i A'),
        'totalVotes'       => (int)$totalRawVotesCast,
        'totalWins' => (int)$totalWins, // Keep this if you need seat totals elsewhere        'totalValue'       => (int)$totalChamberSeats, 
        'registeredVoters' => (int)($totalRegisteredVotersCount ?? 0),
        'scope'            => $scope,
        'headerView'       => $headerView,
        'footerView'       => $footerView,
        'styleView'        => $styleView,
        'shortcode'        => $shortcode,
        'chamber'          => $chamber,
    ]);
});

// 2. Register Admin Configuration UI
shortcode()->setAdminConfig('regional-race', function ($attributes) {
    $elections = \Botble\EdnElection\Models\Election::pluck('name', 'id')->all();
    $regions = ['' => '--- National ---'] + \Botble\EdnElection\Models\Region::pluck('name', 'id')->all();
    $zones = ['' => '--- All Zones ---'] + \Botble\EdnElection\Models\Zone::pluck('name', 'id')->all();
    $woredas = ['' => '--- All Woredas ---'] + \Botble\EdnElection\Models\Woreda::pluck('name', 'id')->all();

    $headerStyles = [
        'default' => 'Default (Title & Subtitle)',
        'minimal' => 'Minimal (Icon Only)',
        'modern'  => 'Modern (Centered Bold)'
    ];

    $footerStyles = [
        'default' => 'Default (Links & Time)',
        'minimal'  => 'Minimal (Time Only)',
        'none'    => 'Hide Footer'
    ];

    $cardStyles = [
        'default'     => 'Default (Progress Bars)',
        'minimal'     => 'Minimal (Clean List)',
        'grid'        => 'Grid Card (Side by Side)',
        'table'       => 'Detailed Table',
        'leaderboard' => 'Leaderboard (Winner Focus)',
        'donut'       => 'Donut (Share of Pie)',
        'headtohead'  => 'Head-to-Head (Top 2 Only)',
        'map'         => 'Regional Map',
        'chamber'         => 'Chamber seats'
    ];

    return view('plugins/edn-election::admin.regional-race-admin', 
        compact('attributes', 'elections', 'regions', 'zones', 'woredas', 'cardStyles', 'headerStyles', 'footerStyles')
    )->render();
});

    }}

    

}