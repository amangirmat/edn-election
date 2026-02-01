<footer class="election-footer">
    @if($scope === 'zone')
        <a href="{{ url('elections/zone/' . ($zoneId ?? '')) }}" class="action-link">Full {{ $displayTitle }} Breakdown <span>⮕</span></a>
    @elseif($scope === 'region')
<a href="{{ url('elections/region/' . ($regionId ?? '')) }}" class="action-link">Full {{ $displayTitle }} Breakdown <span>⮕</span></a>
    @elseif($scope === 'region')
        @else
        <a href="{{ url('elections/national') }}" class="action-link">National Summary <span>⮕</span></a>
    @endif
    <span class="timestamp">Last Updated: {{ $lastUpdated }}</span>
</footer>