<div class="table-head" style="display: flex; justify-content: space-between; padding: 10px 15px; background: #f8f9fa; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #666; margin-bottom: 10px;">
    <span>{{ ($scope ?? '') === 'woreda' ? 'Candidate' : ($election->type === 'referendum' ? 'Option' : 'Political Party') }}</span>
    <span>{{ ($election->type === 'parliamentary' && $scope !== 'woreda') ? 'Seat Share' : 'Vote Share' }}</span>
    <span style="text-align: right;">Total {{ $results->first()->unit ?? 'Votes' }}</span>
</div>

<div class="results-body" style="display: flex; flex-direction: column; gap: 8px;">
    @foreach($results as $result)
        @php 
            $pColor = $result->party_color ?? '#666'; 
            $isParliamentary = ($election->type === 'parliamentary' && $scope !== 'woreda');
        @endphp
        
        <div class="result-row" style="position: relative; height: 50px; display: flex; align-items: center; border-radius: 6px; overflow: hidden; background: #fff; border: 1px solid #edf2f7; transition: transform 0.2s;">
            
            {{-- Dynamic Progress Background --}}
            <div class="progress-bar-bg" style="position: absolute; left: 0; top: 0; height: 100%; width: {{ $result->percent }}%; background-color: {{ $pColor }}; opacity: 0.1; z-index: 1;"></div>
            
            {{-- Color Accent Strip --}}
            <div style="width: 4px; height: 100%; background: {{ $pColor }}; position: relative; z-index: 2;"></div>

            <div style="display: flex; width: 100%; justify-content: space-between; align-items: center; padding: 0 15px; z-index: 2;">
                {{-- Column 1: Identity --}}
                <div class="cell-data" style="flex: 2;">
                    <span class="party-name-label" style="font-weight: 700; color: #2d3748; font-size: 0.95rem;">{{ $result->display_name }}</span>
                    @if(!empty($result->sub_name))
                        <div style="font-size: 10px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">{{ $result->sub_name }}</div>
                    @endif
                </div>

                {{-- Column 2: Percentage --}}
                <div class="cell-data" style="flex: 1; text-align: center;">
                    <span class="percent-label" style="font-weight: 800; color: #4a5568; font-size: 0.9rem;">{{ number_format($result->percent, 1) }}%</span>
                </div>

                {{-- Column 3: Raw Value (Seats or Votes) --}}
                <div class="cell-data vote-label" style="flex: 1; text-align: right; display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-weight: 800; color: #1a202c; font-size: 1rem;">{{ number_format($result->value) }}</span>
                    
                    @if($loop->first && ($result->margin_ahead ?? 0) > 0)
                        <span class="lead-tag" style="font-size: 9px; background: #000; color: #fff; padding: 2px 6px; border-radius: 4px; margin-top: 2px; font-weight: 900;">
                           +{{ number_format($result->margin_ahead) }} LEADING
                        </span>
                    @elseif($isParliamentary && $result->value > 0)
                         <span style="font-size: 9px; color: #718096; font-weight: 600;">SEATS WON</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Footer Stats --}}
<div class="stats-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
    <div class="stat-item">
        <span class="stat-label" style="display: block; font-size: 0.7rem; color: #718096; text-transform: uppercase; font-weight: 700;">
            {{ $isParliamentary ? 'Total House Seats' : 'Registered Voters' }}
        </span>
        <span class="stat-value" style="font-size: 1.1rem; font-weight: 800; color: #2d3748;">
            {{ number_format($isParliamentary ? $election->total_seats : ($registeredVoters ?? 0)) }}
        </span>
    </div>
    
    <div class="stat-item" style="text-align: right;">
        <span class="stat-label" style="display: block; font-size: 0.7rem; color: #718096; text-transform: uppercase; font-weight: 700;">
            {{ $isParliamentary ? 'Seats Declared' : 'Total Votes Cast' }}
        </span>
        <div class="stat-value" style="font-size: 1.1rem; font-weight: 800; color: #2d3748;">
            {{ number_format($totalValue ?? $totalVotes) }}
            @if(!$isParliamentary && ($registeredVoters ?? 0) > 0)
                <span class="turnout-perc" style="font-size: 0.75rem; color: #38a169; background: #f0fff4; padding: 2px 6px; border-radius: 4px; margin-left: 5px;">
                    {{ number_format(($totalVotes / $registeredVoters) * 100, 1) }}% Turnout
                </span>
            @endif
        </div>
    </div>
</div>