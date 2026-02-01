<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .election-map-container { display: flex; height: 550px; border: 2px solid #2c3e50; border-radius: 8px; overflow: hidden; background: #fff; font-family: sans-serif; }
    #leaflet-map-canvas { flex: 2; background: #eef2f3; }
    .election-map-info { flex: 1; padding: 20px; border-left: 2px solid #2c3e50; overflow-y: auto; background: #fafafa; }
    .party-winner { padding: 10px; border-radius: 4px; color: white; font-weight: bold; margin-top: 10px; }
    
    /* Legend Styling */
    .info.legend { padding: 10px; background: white; box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px; line-height: 24px; font-size: 12px; }
    .legend i { width: 14px; height: 14px; float: left; margin-right: 8px; margin-top: 5px; opacity: 0.8; }
    
    #reset-zoom { cursor: pointer; padding: 5px 10px; background: #2c3e50; color: white; border: none; border-radius: 4px; font-size: 11px; transition: 0.3s; }
    #reset-zoom:hover { background: #34495e; }
</style>

<div class="election-map-container">
    <div id="leaflet-map-canvas"></div>
    <div class="election-map-info">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 id="area-name" style="margin: 0; font-size: 20px;">National Overview</h2>
            <button id="reset-zoom" style="display:none;">Reset View</button>
        </div>
        <hr>
        <div id="area-details">
            <p>Click on a region to view specific results from the <strong>{{ $electionId }}</strong> election.</p>
        </div>
    </div>
</div>

<script>
    (function() {
    const partyColors = {
        'PP': '#e63946',
        'EZEMA': '#457b9d', 
        'NAMA': '#1d3557',
        'TPLF': '#ffd60a',
        'default': '#95a5a6'
    };

    function initElectionMap() {
        const mapContainer = document.getElementById('leaflet-map-canvas');
        if (!mapContainer || typeof L === 'undefined') return;

        const map = L.map('leaflet-map-canvas', {
            zoomSnap: 0.5,
            attributionControl: false
        }).setView([9.145, 40.489], 6);

        // FIX: Force Leaflet to recalculate container size
        setTimeout(() => { map.invalidateSize(); }, 200);

        // Add Base Tile Layer (Optional but recommended so you see a map background)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        const resetBtn = document.getElementById('reset-zoom');
        let geoJsonLayer;

        // LOAD JSON - Verify this path in your browser (it should not return 404)
        const jsonPath = '/vendor/core/plugins/edn-election/geo/ethiopia-regions.json';
        
        fetch(jsonPath)
            .then(response => {
                if (!response.ok) throw new Error(`File not found at: ${jsonPath}`);
                return response.json();
            })
            .then(geojsonData => {
                geoJsonLayer = L.geoJson(geojsonData, {
                    style: (feature) => ({
                        fillColor: partyColors[feature.properties.winner] || partyColors['default'],
                        weight: 2,
                        opacity: 1,
                        color: '#2c3e50',
                        fillOpacity: 0.8
                    }),
                    onEachFeature: (feature, layer) => {
                        layer.on({
                            click: (e) => {
                                map.fitBounds(e.target.getBounds());
                                displayAreaStats(feature.properties);
                                resetBtn.style.display = 'block';
                            }
                        });
                    }
                }).addTo(map);
                map.fitBounds(geoJsonLayer.getBounds());
            })
            .catch(err => {
                console.error("Map Load Error:", err);
                document.getElementById('area-details').innerHTML = `<b style="color:red;">Error:</b> ${err.message}`;
            });
    }

    if (document.readyState === 'complete') initElectionMap();
    else window.addEventListener('load', initElectionMap);
})();
</script>