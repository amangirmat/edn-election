@if($results->count() === 0)
    <div style="padding:12px; color:#64748b;">
        No results reported for this region.
    </div>
@else
<div class="table-head">
    <span>Party</span>
    <span>Share</span>
    <span style="text-align:right;">Votes</span>
</div>

<div class="results-body">
@foreach($results as $result)
    <div class="result-row" style="border-left:4px solid {{ $result->party_color }};">
        <div class="progress-bar-bg"
             style="width: {{ $result->percent }}%;
                    background-color: {{ $result->party_color }};">
        </div>

        <div class="cell-data">
            <strong>{{ $result->display_name }}</strong>
        </div>

        <div class="cell-data">
            {{ number_format($result->percent, 1) }}%
        </div>

        <div class="cell-data vote-label">
            {{ number_format($result->votes) }}
        </div>
    </div>
@endforeach
</div>
@endif

<div class="stats-grid">
    <div class="stat-item">
        <span class="stat-label">Registered Voters</span>
        <span class="stat-value">{{ number_format($registeredVoters) }}</span>
    </div>
    <div class="stat-item">
        <span class="stat-label">Votes Cast</span>
        <span class="stat-value">
            {{ number_format($totalVotes) }}
            @if($registeredVoters > 0)
                <span class="turnout-perc">
                    {{ number_format(($totalVotes / $registeredVoters) * 100, 1) }}%
                </span>
            @endif
        </span>
    </div>
</div>
