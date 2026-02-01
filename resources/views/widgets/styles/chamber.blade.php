@php
    // 1. Safety fallback for totalValue
    // Priority: Passed variable -> Chamber model -> Election model -> Default Ethiopian HPR (547)
    $totalValue = $totalValue ?? ($chamber->total_seats ?? ($election->total_seats ?? 547));

    $totalSeats = count($woredaWinners);
    $half = ceil($totalSeats / 2);
    $leftBlock = array_slice($woredaWinners, 0, $half);
    $rightBlock = array_slice($woredaWinners, $half);

    $cols = 18; 
    $dotSize = 12; 
    $gap = 5;
    $aisleWidth = 50;
    
    $blockWidth = ($cols * $dotSize) + (($cols - 1) * $gap);
    $totalWidth = ($blockWidth * 2) + $aisleWidth;

    $seatCounts = collect($woredaWinners)
        ->where('is_empty', false)
        ->groupBy('party_name')
        ->map->count();

    $declaredSeats = $seatCounts->sum();
    
    // 2. Now $totalValue is guaranteed to exist
    $vacantSeats = $totalValue - $declaredSeats;
@endphp

<style>
    .hpr-dashboard { font-family: 'Inter', sans-serif; }
    .woreda-dot { transition: all 0.2s ease; cursor: pointer; }
    .woreda-dot:hover { transform: scale(1.8); z-index: 50; border-radius: 50% !important; }
    
    /* Compact Stat Item */
    .party-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>

<div class="hpr-dashboard" style="background: #fff; border-radius: 24px; padding: 30px; display: flex; flex-direction: column; align-items: center; border: 1px solid #f1f5f9;">
    
    <div style="margin-bottom: 20px; font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">
    {{-- Display Chamber Name or fallback to generic title --}}
    {{ $chamber->name ?? 'House of Peoples Representatives' }}: 
    {{ number_format($totalValue) }} Total Seats
</div>

    {{-- The Parliament Floor --}}
    <div style="display: flex; gap: {{ $aisleWidth }}px; margin-bottom: 30px; justify-content: center;">
        <div style="display: grid; grid-template-columns: repeat({{ $cols }}, {{ $dotSize }}px); gap: {{ $gap }}px;">
            @foreach($leftBlock as $seat)
                <div class="woreda-dot" title="{{ $seat['party_name'] }}"
                     style="width: {{ $dotSize }}px; height: {{ $dotSize }}px; background: {{ $seat['color'] }}; border-radius: 2px; @if($seat['is_empty']) opacity: 0.2; @endif">
                </div>
            @endforeach
        </div>

        <div style="display: grid; grid-template-columns: repeat({{ $cols }}, {{ $dotSize }}px); gap: {{ $gap }}px;">
            @foreach($rightBlock as $seat)
                <div class="woreda-dot" title="{{ $seat['party_name'] }}"
                     style="width: {{ $dotSize }}px; height: {{ $dotSize }}px; background: {{ $seat['color'] }}; border-radius: 2px; @if($seat['is_empty']) opacity: 0.2; @endif">
                </div>
            @endforeach
        </div>
    </div>

    {{-- Compact Podium --}}
    <div style="width: 120px; height: 4px; background: #e2e8f0; border-radius: 10px; margin-bottom: 40px;"></div>

    {{-- Compact Statistics Grid --}}
    <div style="width: 100%; display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px;">
        @foreach($results as $party)
            @php $actualSeatsWon = $seatCounts[$party->display_name] ?? 0; @endphp
            @if($actualSeatsWon > 0)
                <div class="party-row">
                    <div style="width: 10px; height: 10px; background: {{ $party->party_color }}; border-radius: 2px; flex-shrink: 0;"></div>
                    
                    <div style="flex-grow: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 12px; font-weight: 700; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 5px;">
                                {{ $party->display_name }}
                            </span>
                            <span style="font-size: 12px; font-weight: 900; color: #0f172a;">{{ $actualSeatsWon }}</span>
                        </div>
                        {{-- Tiny Progress Bar --}}
                        <div style="width: 100%; height: 3px; background: #e2e8f0; border-radius: 10px; margin-top: 4px; overflow: hidden;">
                            <div style="width: {{ ($actualSeatsWon / $totalValue) * 100 }}%; height: 100%; background: {{ $party->party_color }};"></div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Compact Vacant Item --}}
        @if($vacantSeats > 0)
            <div class="party-row" style="background: #fff; border-style: dashed; opacity: 0.7;">
                <div style="width: 10px; height: 10px; background: #cbd5e1; border-radius: 2px;"></div>
                <div style="flex-grow: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; font-weight: 600; color: #64748b;">Remaining</span>
                        <span style="font-size: 12px; font-weight: 800; color: #64748b;">{{ $vacantSeats }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>