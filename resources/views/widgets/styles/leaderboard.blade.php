<div class="leaderboard-wrapper" style="background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
    @foreach($results as $result)
        @php 
            $pColor = $result->party_color ?? '#666'; 
            $isParliamentary = ($election->type === 'parliamentary' && ($scope ?? '') !== 'woreda');
        @endphp
        
        @if($loop->first)
            {{-- Winner / Leader Card --}}
            <div class="hero-winner" style="padding: 20px; border-top: 6px solid {{ $pColor }}; background: linear-gradient(to bottom, #fff, #f8fafc);">
                <div class="hero-badge" style="display: inline-block; background: {{ $pColor }}; color: #fff; font-size: 10px; font-weight: 800; padding: 4px 12px; border-radius: 50px; text-transform: uppercase; margin-bottom: 12px;">
                    Current Leader
                </div>
                <div class="hero-flex" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="hero-info">
                        <div class="hero-name" style="font-size: 1.4rem; font-weight: 900; color: #1e293b; line-height: 1.1;">{{ $result->display_name }}</div>
                       @if(!empty($result->sub_name))
    <div class="hero-sub" style="font-size: 11px; color: #64748b; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">
        {{ $result->sub_name }}
    </div>
@endif
                    </div>
                    <div class="hero-stats" style="text-align: right;">
                        <div class="hero-percent" style="font-size: 1.8rem; font-weight: 900; color: {{ $pColor }}; line-height: 1;">{{ number_format($result->percent, 1) }}%</div>
                        <div class="hero-votes" style="font-size: 12px; color: #64748b; font-weight: 600;">{{ number_format($result->value) }} {{ strtolower($result->unit ?? 'votes') }}</div>
                    </div>
                </div>
                
                <div class="hero-progress-container" style="height: 10px; background: #e2e8f0; border-radius: 5px; margin-top: 15px; overflow: hidden;">
                    <div class="hero-progress-bar" style="height: 100%; width: {{ $result->percent }}%; background: {{ $pColor }}; border-radius: 5px; transition: width 0.8s ease-out;"></div>
                </div>
            </div>

            @if($results->count() > 1)
                <div class="chaser-list" style="padding: 10px 20px;">
                    <div style="font-size: 10px; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">Trailing Candidates</div>
            @endif
        @else
            {{-- Runner ups --}}
            <div class="chaser-row" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f8fafc;">
                <span class="chaser-name" style="font-weight: 700; color: #334155; font-size: 0.9rem;">
                    <span style="color: #cbd5e0; margin-right: 8px;">#{{ $loop->iteration }}</span> {{ $result->display_name }}
                </span>
                <span class="chaser-stats" style="text-align: right;">
                    <strong style="color: #475569;">{{ number_format($result->percent, 1) }}%</strong>
                    <small style="color: #94a3b8; display: block; font-size: 10px;">{{ number_format($result->value) }}</small>
                </span>
            </div>
        @endif
        
        @if($loop->last && $results->count() > 1)
                </div> {{-- Close chaser-list --}}
        @endif
    @endforeach
</div>

{{-- Dynamic Footer Stats --}}
<div class="leaderboard-footer" style="padding: 15px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
    <div class="foot-item">
        <span style="display: block; font-size: 9px; color: #94a3b8; text-transform: uppercase; font-weight: 800;">Scope</span>
        <span style="font-weight: 700; color: #475569; font-size: 0.85rem;">{{ strtoupper($scope ?? 'National') }}</span>
    </div>
    <div class="foot-item" style="text-align: right;">
        <span style="display: block; font-size: 9px; color: #94a3b8; text-transform: uppercase; font-weight: 800;">Total {{ $results->first()->unit ?? 'Votes' }}</span>
        <span style="font-weight: 700; color: #475569; font-size: 0.85rem;">{{ number_format($isParliamentary ? $results->sum('value') : ($totalVotes ?? 0)) }}</span>
    </div>
</div>