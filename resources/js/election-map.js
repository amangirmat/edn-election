document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([9.145, 40.489], 6);
    
    // Add base map tiles (Greyscale/Light)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    // 1. Define Party Colors
    const partyColors = {
        'Prosperity Party': '#e63946',
        'EZEMA': '#457b9d',
        'NAMA': '#1d3557',
        'TPLF': '#ffd60a',
        'default': '#cccccc'
    };

    let geojsonLayer;

    // 2. Styling Function
    function style(feature) {
        return {
            fillColor: partyColors[feature.properties.leading_party] || partyColors['default'],
            weight: 1,
            opacity: 1,
            color: 'white',
            fillOpacity: 0.7
        };
    }

    // 3. Interaction Functions
    function onEachFeature(feature, layer) {
        layer.on({
            mouseover: (e) => {
                e.target.setStyle({ weight: 3, fillOpacity: 0.9 });
            },
            mouseout: (e) => {
                geojsonLayer.resetStyle(e.target);
            },
            click: (e) => {
                const props = e.target.feature.properties;
                map.fitBounds(e.target.getBounds());
                updateSidebar(props);
            }
        });
    }

    // 4. Update Sidebar with API Data
    async function updateSidebar(props) {
        document.getElementById('selected-area-name').innerText = props.name;
        // Fetch real data from your Phase 4 API here
        const response = await fetch(`/api/v1/results/region/${props.id}?election_id=1`, {
            headers: { 'X-Election-API-Key': 'your_secret_key' }
        });
        const data = await response.json();
        
        // Update the Sidebar HTML
        document.getElementById('selected-area-stats').innerHTML = `
            <strong>Leading:</strong> ${data.standings[0].party}<br>
            <strong>Votes:</strong> ${data.standings[0].votes.toLocaleString()}
        `;
    }

    // 5. Load Initial GeoJSON (Regions)
    fetch('/storage/geo/ethiopia-regions.json')
        .then(res => res.json())
        .then(data => {
            geojsonLayer = L.geoJson(data, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);
        });
});