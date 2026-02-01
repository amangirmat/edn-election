<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

    .election-card {
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        font-family: 'Inter', -apple-system, sans-serif;
        width: 100%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #eef0f2;
        margin-bottom: 30px;
    }

    /* Header Styling */
    .election-header {
        background: #542292; 
        background: linear-gradient(135deg, #542292 0%, #3a1866 100%);
        color: #fff;
        padding: 20px;
    }

    .status-container {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-live { background: rgba(255, 77, 77, 0.2); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.3); }
    .badge-upcoming { background: rgba(135, 199, 255, 0.2); color: #87c7ff; border: 1px solid rgba(135, 199, 255, 0.3); }
    .badge-counting { background: rgba(93, 255, 142, 0.2); color: #5dff8e; border: 1px solid rgba(93, 255, 142, 0.3); }
    .badge-final { background: rgba(255, 255, 255, 0.15); color: #ffffff; }

    .pulse-dot {
        height: 8px; width: 8px; background-color: #ff4d4d;
        border-radius: 50%; display: inline-block;
        animation: pulse-red 1.5s infinite;
    }

    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255, 77, 77, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
    }

    .region-title { 
        margin: 0; 
        font-size: 1.6rem; 
        font-weight: 800; 
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    /* Table Labels */
    .table-head {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        padding: 12px 20px;
        background: #f8f9fa;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #6c757d;
        border-bottom: 1px solid #eee;
    }

    /* Result Rows */
    .result-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        position: relative;
        padding: 18px 20px;
        align-items: center;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s ease;
    }

    .result-row:hover { background: #fafafa; }

    .progress-bar-bg {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        z-index: 0;
        transition: width 1s cubic-bezier(0.1, 0.7, 1.0, 0.1);
        opacity: 0.12;
    }

    .cell-data { position: relative; z-index: 1; }

    .party-name-label { font-weight: 700; color: #1a1a1a; font-size: 1.05rem; }
    .percent-label { font-weight: 800; color: #000; font-size: 1.25rem; }
    .vote-label { font-weight: 600; color: #444; font-size: 0.95rem; text-align: right; }

    .lead-tag { 
        display: block; 
        color: #0d8a2d; 
        font-size: 10px; 
        font-weight: 800; 
        margin-top: 4px;
        letter-spacing: 0.05em;
    }

    /* Stats Section at the Bottom */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: #dee2e6; /* Border color for divider */
        border-top: 2px solid #eef0f2;
        border-bottom: 1px solid #eef0f2;
    }

    .stat-item {
        background: #f8f9fa; /* Sleek different BG color (light news gray) */
        padding: 15px 20px;
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        font-size: 10px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a1a;
    }

    .turnout-perc {
        font-size: 11px;
        font-weight: 700;
        color: #542292;
        margin-left: 5px;
        background: rgba(84, 34, 146, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Footer */
    .election-footer { 
        padding: 20px; 
        background: #ffffff; 
        border-top: 1px solid #eee;
    }

    .action-link { 
        text-decoration: none; 
        color: #cc0000; 
        font-weight: 800; 
        font-size: 13px; 
        text-transform: uppercase; 
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: gap 0.2s ease;
    }

    .action-link:hover { gap: 12px; }

    .timestamp { 
        display: block; 
        font-size: 10px; 
        color: #adb5bd; 
        margin-top: 12px; 
        text-transform: uppercase; 
        font-weight: 600;
        letter-spacing: 1px;
    }
</style>

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
        <h3 class="region-title">{{ $election->region->name ?? 'National Summary' }}</h3>
    </header>

    <div class="table-head">
        <span>Political Party</span>
        <span>Share</span>
        <span style="text-align: right;">Total Votes</span>
    </div>

    <div class="results-body">
        @foreach($partyResults as $result)
            @php $pColor = $result->party_color ?? '#666'; @endphp
            <div class="result-row" style="border-left: 5px solid {{ $pColor }};">
                <div class="progress-bar-bg" style="width: {{ $result->percent }}%; background-color: {{ $pColor }};"></div>
                <div class="cell-data"><span class="party-name-label">{{ $result->party_name }}</span></div>
                <div class="cell-data"><span class="percent-label">{{ number_format($result->percent, 1) }}%</span></div>
                <div class="cell-data vote-label">
                    <span>{{ number_format($result->votes) }}</span>
                    @if($loop->first && $result->margin_ahead > 0)
                        <span class="lead-tag">▲ {{ number_format($result->margin_ahead) }} LEADING</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

  <div class="stats-grid">
    <div class="stat-item">
        <span class="stat-label">Total Registered</span>
        <span class="stat-value">{{ number_format($registeredVoters) }}</span>
    </div>
    <div class="stat-item">
        <span class="stat-label">Total Votes Cast</span>
        <div class="stat-value">
            {{ number_format($totalVotes) }}
            @if($registeredVoters > 0)
                <span class="turnout-perc">{{ number_format(($totalVotes / $registeredVoters) * 100, 1) }}%</span>
            @endif
        </div>
    </div>
</div>

    <footer class="election-footer">
        @if($election->region_id)
            <a href="{{ url('elections/region/' . $election->region_id) }}" class="action-link">Full Region Breakdown <span>⮕</span></a>
        @endif
        <span class="timestamp">Last Updated: {{ $lastUpdated }}</span>
    </footer>
</div>