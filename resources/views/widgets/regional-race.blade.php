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
        background: #dee2e6;
        border-top: 2px solid #eef0f2;
        border-bottom: 1px solid #eef0f2;
    }

    .stat-item {
        background: #f8f9fa;
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


    /* Grid Layout Specifics */
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    padding: 20px;
}

.grid-card {
    background: #f8f9fa;
    border: 1px solid #eef0f2;
    border-radius: 6px;
    padding: 15px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease;
}

.grid-card:hover {
    transform: translateY(-3px);
    border-color: #542292;
}

.grid-party-color-strip {
    height: 4px;
    width: 100%;
    position: absolute;
    top: 0; left: 0;
    border-radius: 6px 6px 0 0;
}

.grid-name { font-weight: 700; font-size: 1rem; color: #1a1a1a; margin-bottom: 5px; }
.grid-subtext { font-size: 11px; color: #6c757d; font-weight: 600; text-transform: uppercase; }
.grid-votes { font-size: 1.25rem; font-weight: 800; color: #000; margin-top: 10px; }
.grid-percent { font-size: 0.9rem; font-weight: 700; color: #542292; }


/* Minimal Layout Specifics */
.results-minimal {
    padding: 10px 20px;
}

.minimal-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f1f1;
}

.minimal-row:last-child {
    border-bottom: none;
}

.minimal-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.minimal-dot {
    height: 10px;
    width: 10px;
    border-radius: 50%;
}

.minimal-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: #333;
}

.minimal-right {
    text-align: right;
}

.minimal-percent {
    font-weight: 800;
    font-size: 1rem;
    color: #1a1a1a;
}

.minimal-votes {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    display: block;
}

/* Table Layout Specifics */
.results-table-container {
    padding: 15px 20px;
    overflow-x: auto;
}

.election-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.election-table th {
    text-align: left;
    padding: 12px 10px;
    border-bottom: 2px solid #542292;
    color: #542292;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.election-table td {
    padding: 14px 10px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.election-table tr:hover {
    background-color: #fcfaff;
}

.table-party-indicator {
    display: inline-block;
    width: 4px;
    height: 18px;
    margin-right: 10px;
    vertical-align: middle;
    border-radius: 2px;
}

.table-votes {
    font-family: 'Monaco', 'Consolas', monospace;
    font-weight: 600;
    color: #444;
}

.table-percent {
    font-weight: 800;
    color: #000;
}

/* Leaderboard Style */
.hero-winner { background: #fcfaff; padding: 20px; border-radius: 0 0 8px 8px; margin-bottom: 15px; }
.hero-badge { background: #542292; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 10px; text-transform: uppercase; }
.hero-flex { display: flex; justify-content: space-between; align-items: center; }
.hero-name { font-size: 1.3rem; font-weight: 800; color: #1a1a1a; }
.hero-percent { font-size: 1.8rem; font-weight: 900; color: #542292; }
.hero-progress-container { height: 6px; background: #eee; border-radius: 3px; margin-top: 15px; overflow: hidden; }
.hero-progress-bar { height: 100%; transition: width 1s ease; }
.chaser-row { display: flex; justify-content: space-between; padding: 10px 20px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }

/* Interactive Donut Styles */
.donut-interactive-wrapper {
    padding: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.donut-viewport {
    position: relative;
    width: 200px;
    height: 200px;
    margin-bottom: 30px;
}

.donut-chart-main {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.donut-viewport:hover .donut-chart-main {
    transform: scale(1.05);
}

/* The Center Hole with "Pop-up" Text */
.donut-center-info {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 5;
    text-align: center;
}

/* Interactive Grid Cards */
.interactive-legend-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    width: 100%;
}

.party-card {
    position: relative;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 12px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

/* The "Pop-up" Tooltip effect on hover */
.interactive-donut-container {
    width: 300px;
    margin: 0 auto;
    position: relative;
}

.donut-svg {
    width: 100%;
    height: auto;
}

/* Slice Animations */
.donut-segment {
    transition: stroke-width 0.3s, filter 0.3s;
    cursor: pointer;
}

.donut-slice-group:hover .donut-segment {
    stroke-width: 7; /* Makes the hovered slice pop out */
    filter: drop-shadow(0 0 3px rgba(0,0,0,0.2));
}

/* Tooltip Content Logic */
.slice-tooltip {
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.donut-slice-group:hover .slice-tooltip {
    opacity: 1;
}

/* Hide the default center text when any slice is hovered */
.donut-svg:has(.donut-slice-group:hover) .donut-default-center {
    opacity: 0;
}

/* Styling the Popup Text */
.tooltip-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
}

.t-name { font-size: 2px; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 0.5px; }
.t-val { font-size: 3.5px; font-weight: 900; color: #542292; line-height: 1; }
.t-votes { font-size: 1.5px; color: #64748b; font-weight: 600; }

/* Styling Default Center Text */
.center-total { font-size: 4px; font-weight: 900; fill: #1a1a1a; text-anchor: middle; }
.center-label { font-size: 1.5px; fill: #94a3b8; font-weight: 800; text-anchor: middle; letter-spacing: 0.1px; }

.donut-default-center { transition: opacity 0.2s; pointer-events: none; }


/* Map */

.map-container { position: relative; overflow: visible; }
.ethiopia-svg { filter: drop-shadow(0 10px 15px rgba(0,0,0,0.05)); }

.region-path {
    stroke: #ffffff;
    stroke-width: 1.5;
    transition: all 0.25s ease;
}

.region-group:hover .region-path {
    stroke-width: 3;
    filter: brightness(1.1);
    transform: translateY(-2px);
}

/* Tooltip positioning logic */
.map-tooltip {
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    /* Use CSS to position tooltip relative to the group */
    overflow: visible;
}

.region-group:hover .map-tooltip {
    opacity: 1;
}

.map-tooltip-box {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border-bottom: 4px solid #542292;
    text-align: center;
}

.region-path {
    transition: fill 0.4s ease, filter 0.3s ease;
    cursor: pointer;
}

/* Brighten the party color on hover */




.t-party {
    font-size: 14px;
    font-weight: 900;
    text-transform: uppercase;
    margin: 4px 0;
}

.t-region { font-size: 10px; text-transform: uppercase; color: #94a3b8; font-weight: 800; display: block; }
.t-winner { font-size: 14px; font-weight: 900; color: #1e293b; margin: 2px 0; }
.t-percent { font-size: 18px; font-weight: 900; color: #542292; }


.pulse-dot {
    animation: pulse-red 2s infinite;
    box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.7);
}

@keyframes pulse-red {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(211, 47, 47, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(211, 47, 47, 0); }
}
</style>

<<div class="election-widget-container">
    {{-- Dynamic Header --}}
    @include($headerView)

    {{-- Dynamic Body Style --}}
    <div class="election-body">
        @include($styleView)
    </div>

    {{-- Dynamic Footer --}}
    @if($shortcode->footer_style !== 'none')
        @include($footerView)
    @endif
</div>