<div class="results-minimal" style="display: flex; flex-direction: column; gap: 12px;">
    @foreach($results as $result)
        @php $pColor = $result->party_color ?? '#666'; @endphp
        <div class="minimal-row" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0;">
            <div class="minimal-left" style="display: flex; align-items: center; gap: 10px;">
                {{-- A small colored dot --}}
                <div class="minimal-dot" style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $pColor }}; flex-shrink: 0;"></div>
                <div>
                    <div class="minimal-name" style="font-weight: 600; font-size: 0.95rem; color: #1a1a1a;">{{ $result->display_name }}</div>
                    @if(!empty($result->sub_name))
                        <div style="font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 0.5px;">{{ $result->sub_name }}</div>
                    @endif
                </div>
            </div>

            <div class="minimal-right" style="text-align: right;">
                <div class="minimal-percent" style="font-weight: 800; font-size: 1rem; color: #111;">{{ number_format($result->percent, 1) }}%</div>
                <span class="minimal-votes" style="font-size: 11px; color: #64748b;">
                    {{-- Use 'value' to match your provider logic --}}
                    {{ number_format($result->value) }} {{ strtolower($result->unit ?? 'votes') }}
                </span>
            </div>
        </div>
    @endforeach
</div>

{{-- Simplified Stats Footer --}}
<div style="background: #fdfdfd; padding: 15px 20px; border-top: 1px solid #eee; display: flex; justify-content: space-between; margin-top: 10px;">
    <div>
        <span style="font-size: 9px; color: #aaa; text-transform: uppercase; display: block; letter-spacing: 0.5px;">
            {{ ($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda') ? 'Seats Won' : 'Turnout' }}
        </span>
        <span style="font-weight: 700; font-size: 0.9rem; color: #334155;">
            @if($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda')
                {{ number_format($results->sum('value')) }}
            @elseif(($registeredVoters ?? 0) > 0)
                {{ number_format(($totalVotes / $registeredVoters) * 100, 1) }}%
            @else
                0%
            @endif
        </span>
    </div>
    <div style="text-align: right;">
        <span style="font-size: 9px; color: #aaa; text-transform: uppercase; display: block; letter-spacing: 0.5px;">
             {{ ($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda') ? 'Total Seats' : 'Total Cast' }}
        </span>
        <span style="font-weight: 700; font-size: 0.9rem; color: #334155;">
            {{-- Shows either the total chamber seats or the sum of all votes cast --}}
            {{ ($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda') ? number_format($totalValue ?? 0) : number_format($totalVotes ?? 0) }}
        </span>
    </div>
</div>