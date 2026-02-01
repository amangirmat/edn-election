@php
    $cumulativePercent = 0;
    $radius = 15.91549430918954; 
@endphp

<style>
    .interactive-donut-container {
        max-width: 300px;
        margin: auto;
    }
    .donut-svg {
        display: block;
        width: 100%;
        height: auto;
    }
    .donut-segment {
        transition: stroke-width 0.2s ease, opacity 0.2s ease;
    }
    
    /* CRITICAL FIX: Only trigger hover when mouse is on the STROKE.
       This prevents the "hole" in the middle from triggering the hover.
    */
    .donut-trigger {
        pointer-events: stroke; 
        cursor: pointer;
    }

    .donut-slice-group:hover .donut-segment {
        stroke-width: 7;
    }
    
    .slice-tooltip {
        opacity: 0;
        pointer-events: none; /* Never let the tooltip block the mouse */
        transition: opacity 0.2s ease;
    }
    
    .donut-slice-group:hover .slice-tooltip {
        opacity: 1;
    }
    
    /* Hide the default total when a slice is hovered */
    .donut-svg:has(.donut-slice-group:hover) .donut-default-center {
        opacity: 0;
    }
    
    .donut-default-center {
        transition: opacity 0.2s ease;
        text-anchor: middle;
        dominant-baseline: middle;
        pointer-events: none; /* Ignore mouse so slices behind it can be hovered */
    }

    .center-total { font-weight: bold; font-size: 5px; fill: #1e293b; }
    .center-label { font-size: 2px; fill: #64748b; letter-spacing: 0.2px; }

    .tooltip-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        line-height: 1.1;
    }
    .t-name { font-size: 2.2px; font-weight: bold; color: #1e293b; margin-bottom: 0.5px; }
    .t-val { font-size: 4.5px; font-weight: 800; color: #0f172a; }
    .t-votes { font-size: 1.8px; color: #64748b; }
</style>

<div class="interactive-donut-container">
    <svg viewBox="0 0 42 42" class="donut-svg">
        {{-- Background Track --}}
        <circle class="donut-ring" cx="21" cy="21" r="{{ $radius }}" fill="transparent" stroke="#f1f5f9" stroke-width="5"></circle>

        {{-- Default Center View (Placed before slices so it's 'behind' them) --}}
        <g class="donut-default-center">
            <text x="21" y="20.5" class="center-total">{{ number_format($totalVotes) }}</text>
            <text x="21" y="24.5" class="center-label">TOTAL VOTES</text>
        </g>

        @foreach($results as $result)
            @php
                $strokeLength = $result->percent;
                $offset = 100 - $cumulativePercent + 25; 
                $cumulativePercent += $result->percent;
            @endphp
            
            <g class="donut-slice-group">
                <circle class="donut-segment" 
                        cx="21" cy="21" r="{{ $radius }}" 
                        fill="transparent" 
                        stroke="{{ $result->party_color ?? '#666' }}" 
                        stroke-width="5" 
                        stroke-dasharray="{{ $strokeLength }} {{ 100 - $strokeLength }}" 
                        stroke-dashoffset="{{ $offset }}">
                </circle>
                
                {{-- Trigger only on the stroke --}}
                <circle class="donut-trigger" 
                        cx="21" cy="21" r="{{ $radius }}" 
                        fill="transparent" 
                        stroke="transparent" 
                        stroke-width="7" 
                        stroke-dasharray="{{ $strokeLength }} {{ 100 - $strokeLength }}" 
                        stroke-dashoffset="{{ $offset }}">
                </circle>

                <foreignObject x="10" y="10" width="22" height="22" class="slice-tooltip">
                    <div class="tooltip-content" xmlns="http://www.w3.org/1999/xhtml">
                        <div class="t-name">{{ $result->display_name ?? 'Candidate' }}</div>
                        <div class="t-val">{{ number_format($result->percent ?? 0, 1) }}%</div>
                        <div class="t-votes">{{ number_format($result->votes ?? ($result->votes_count ?? 0)) }}</div>
                    </div>
                </foreignObject>
            </g>
        @endforeach
    </svg>
</div>