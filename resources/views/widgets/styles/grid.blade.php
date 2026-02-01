<style>
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .grid-card {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
        padding-bottom: 12px;
    }
    .grid-party-color-strip { height: 4px; width: 100%; }
    .grid-content { padding: 12px; flex-grow: 1; }
    .grid-name { font-weight: 700; color: #2d3748; font-size: 0.9rem; line-height: 1.2; }
    .grid-subtext { font-size: 10px; color: #718096; text-transform: uppercase; margin-top: 4px; }
    .grid-votes-container { margin-top: 12px; padding: 0 12px; }
    .grid-votes { font-weight: 800; font-size: 1.1rem; color: #1a202c; display: flex; align-items: baseline; gap: 4px; }
    .grid-percent { color: #4a5568; font-weight: 600; font-size: 0.85rem; }
    .lead-tag { 
        background: #f0fff4; color: #276749; font-size: 10px; font-weight: 700; 
        padding: 4px 8px; border-radius: 4px; margin: 8px 12px 0 12px; text-align: center;
    }
</style>

<div class="results-grid">
    @foreach($results as $result)
        @php 
            $pColor = $result->party_color ?? '#666'; 
            $isParliamentary = ($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda');
        @endphp
        <div class="grid-card">
            <div class="grid-party-color-strip" style="background-color: {{ $pColor }};"></div>
            
            <div class="grid-content">
                <div class="grid-name">{{ $result->display_name }}</div>
                @if(!empty($result->sub_name))
                    <div class="grid-subtext">{{ $result->sub_name }}</div>
                @endif
            </div>

            <div class="grid-votes-container">
                <div class="grid-votes">
                    {{-- Fixed: Using value instead of votes --}}
                    {{ number_format($result->value) }}
                    <span style="font-size: 10px; color: #6c757d; font-weight: normal; text-transform: lowercase;">
                        {{ $result->unit ?? 'votes' }}
                    </span>
                </div>
                <div class="grid-percent">
                    {{ number_format($result->percent, 1) }}% share
                </div>
            </div>

            @if($loop->first && ($result->margin_ahead ?? 0) > 0)
                <div class="lead-tag">
                    ▲ {{ number_format($result->margin_ahead) }} lead
                </div>
            @endif
        </div>
    @endforeach
</div>

{{-- Standard Stats Grid Footer --}}
<div class="stats-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
    <div class="stat-item">
        <span class="stat-label" style="display: block; font-size: 10px; color: #718096; text-transform: uppercase; font-weight: 700;">
            {{ $isParliamentary ? 'Chamber Capacity' : 'Registered Voters' }}
        </span>
        <span class="stat-value" style="font-size: 1.1rem; font-weight: 800; color: #2d3748;">
            {{ number_format($isParliamentary ? ($totalValue ?? 547) : ($registeredVoters ?? 0)) }}
        </span>
    </div>
    
    <div class="stat-item" style="text-align: right;">
        <span class="stat-label" style="display: block; font-size: 10px; color: #718096; text-transform: uppercase; font-weight: 700;">
            {{ $isParliamentary ? 'Total Seats Won' : 'Total Votes Cast' }}
        </span>
        <div class="stat-value" style="font-size: 1.1rem; font-weight: 800; color: #2d3748;">
            {{ number_format($isParliamentary ? $results->sum('value') : ($totalVotes ?? 0)) }}
            @if(!$isParliamentary && ($registeredVoters ?? 0) > 0)
                <span class="turnout-perc" style="font-size: 0.75rem; color: #38a169; background: #f0fff4; padding: 2px 4px; border-radius: 4px;">
                    {{ number_format(($totalVotes / $registeredVoters) * 100, 1) }}%
                </span>
            @endif
        </div>
    </div>
</div>