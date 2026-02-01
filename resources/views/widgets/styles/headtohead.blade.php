@php 
    $first = $results->first();
    $second = $results->get(1);
    // Calculate "Others" to ensure the bar equals 100%
    $othersPercent = $results->slice(2)->sum('percent');
    
    // Safety check for empty results
    if (!$first) return;
@endphp

<div class="h2h-container" style="padding: 20px; background: #fff; border-radius: 16px; border: 1px solid #edf2f7;">
    @if($first && $second)
        <div class="h2h-vs-row" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px;">
            {{-- Lead Candidate --}}
            <div class="h2h-player" style="flex: 1;">
                <div style="font-size: 10px; color: #38a169; font-weight: 800; text-transform: uppercase; margin-bottom: 4px;">▲ Leading</div>
                <div style="color: {{ $first->party_color }}; font-size: 1.8rem; font-weight: 900; line-height: 1;">
                    {{ number_format($first->percent, 1) }}%
                </div>
                <div style="font-weight: 700; font-size: 0.95rem; color: #2d3748; margin-top: 5px;">{{ $first->display_name }}</div>
                <div style="font-size: 11px; color: #718096;">{{ number_format($first->value) }} {{ strtolower($first->unit ?? 'votes') }}</div>
            </div>

            {{-- Gap/VS Indicator --}}
            <div class="h2h-gap" style="text-align: center; margin-bottom: 10px; flex: 0 0 100px;">
                <div style="font-size: 11px; font-weight: 800; color: #cbd5e0; margin-bottom: 5px;">VS</div>
                <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; color: #475569; white-space: nowrap;">
                    {{ number_format($first->percent - $second->percent, 1) }}% GAP
                </span>
            </div>

            {{-- Second Candidate --}}
            <div class="h2h-player" style="text-align: right; flex: 1;">
                <div style="font-size: 10px; color: #718096; font-weight: 800; text-transform: uppercase; margin-bottom: 4px;">Trailing</div>
                <div style="color: {{ $second->party_color }}; font-size: 1.8rem; font-weight: 900; line-height: 1;">
                    {{ number_format($second->percent, 1) }}%
                </div>
                <div style="font-weight: 700; font-size: 0.95rem; color: #2d3748; margin-top: 5px;">{{ $second->display_name }}</div>
                <div style="font-size: 11px; color: #718096;">{{ number_format($second->value) }} {{ strtolower($second->unit ?? 'votes') }}</div>
            </div>
        </div>
        
        {{-- Triple-Segment Progress Bar --}}
        <div class="h2h-bar-wrapper" style="height: 16px; background: #f1f5f9; border-radius: 50px; overflow: hidden; display: flex; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
            <div title="{{ $first->display_name }}" style="width: {{ $first->percent }}%; background: {{ $first->party_color }}; transition: width 0.5s ease;"></div>
            <div title="Others" style="width: {{ $othersPercent }}%; background: #e2e8f0; transition: width 0.5s ease;"></div>
            <div title="{{ $second->display_name }}" style="width: {{ $second->percent }}%; background: {{ $second->party_color }}; transition: width 0.5s ease; border-left: 2px solid #fff;"></div>
        </div>
        
        <div style="display: flex; justify-content: center; margin-top: 10px;">
             <span style="font-size: 10px; color: #94a3b8; font-weight: 600;">Other Candidates: {{ number_format($othersPercent, 1) }}%</span>
        </div>
    @else
        <div style="text-align: center; padding: 20px; color: #64748b;">
            Waiting for more candidates to report results...
        </div>
    @endif
</div>