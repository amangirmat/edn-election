<div class="election-card">
    <header class="election-header">
    <div class="status-container">
        @php $status = strtolower((string)$election->status); @endphp
        @if(in_array($status, ['ongoing', 'published', '1', 'live']))
            <span class="badge badge-live"><span class="pulse-dot"></span> Live Results</span>
        @elseif($status == 'upcoming')
            <span class="badge badge-upcoming">Upcoming</span>
        @elseif($status == 'counting')
            <span class="badge badge-counting">Counting</span>
        @else
            <span class="badge badge-final">Final Result</span>
        @endif
    </div>
    
    <h3 class="region-title" style="margin-bottom: 2px;">{{ $displayTitle }}</h3>
    
{{-- Show Region Name if we are in Zone scope --}}
    @if($scope === 'zone' && $parentTitle)
        <div style="font-size: 0.85rem; opacity: 0.8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ $parentTitle }} Region
        </div>
    @endif

    {{-- UPDATED: Show Region and Zone for Woreda Scope --}}
    @if($scope === 'woreda')
        <div style="font-size: 0.85rem; opacity: 0.8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
            @if(isset($regionName)) 
                {{ $regionName }} Region <span style="margin: 0 5px; opacity: 0.5;">|</span> 
            @endif
            {{ $parentTitle }}
        </div>
    @endif
</header>