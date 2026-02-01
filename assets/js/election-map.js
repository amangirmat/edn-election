const ElectionMap = {
    init() {
        const containers = document.querySelectorAll('.eth-election-map');
        containers.forEach(el => this.buildMap(el));
    },

    async buildMap(container) {
        const level = container.dataset.level || 'region';
        const results = JSON.parse(container.dataset.results || '[]');
        
        // Setup Container
        container.innerHTML = `<div id="leaflet-canvas"></div><div class="map-legend" id="legend"></div>`;
        const map = L.map(container.querySelector('#leaflet-canvas'), {
            zoomSnap: 0.5, attributionControl: false
        }).setView([9.145, 40.489], 6);

        // Load Local HDX File
        const response = await fetch(`/assets/geo/${level}s.json`);
        const geoData = await response.json();

        const geoLayer = L.geoJson(geoData, {
            style: (feature) => this.getStyle(feature, results, level),
            onEachFeature: (feature, layer) => this.initInteractions(feature, layer, results, level)
        }).addTo(map);

        map.fitBounds(geoLayer.getBounds());
        this.renderLegend(container.querySelector('#legend'), results);
    },

    getStyle(feature, results, level) {
        const pcodeKey = `ADM${level === 'region' ? 1 : level === 'zone' ? 2 : 3}_PCODE`;
        const pcode = feature.properties[pcodeKey];
        const result = results.find(r => r.code === pcode);

        const color = result ? result.color : '#333';
        const isLow = result && result.reporting < 50;

        return {
            fillColor: color,
            weight: 1,
            opacity: 1,
            color: '#555',
            fillOpacity: isLow ? 0.3 : 0.8,
            dashArray: isLow ? '3' : ''
        };
    },

    initInteractions(feature, layer, results, level) {
        const pcodeKey = `ADM${level === 'region' ? 1 : level === 'zone' ? 2 : 3}_PCODE`;
        const nameKey = `ADM${level === 'region' ? 1 : level === 'zone' ? 2 : 3}_EN`;
        const result = results.find(r => r.code === feature.properties[pcodeKey]);

        const tooltipContent = `
            <div style="padding:5px">
                <strong>${feature.properties[nameKey]}</strong><br/>
                Party: ${result ? result.party : 'No Data'}<br/>
                Votes: ${result ? result.votes.toLocaleString() : '-'}<br/>
                Reporting: ${result ? result.reporting : 0}%
            </div>`;

        layer.bindTooltip(tooltipContent, { sticky: true, className: 'map-tooltip' });
        
        layer.on({
            mouseover: (e) => e.target.setStyle({ weight: 2, color: '#fff', fillOpacity: 0.9 }),
            mouseout: (e) => e.target.setStyle({ weight: 1, color: '#555', fillOpacity: result && result.reporting < 50 ? 0.3 : 0.8 }),
            click: (e) => map.fitBounds(e.target.getBounds())
        });
    },

    renderLegend(target, results) {
        const uniqueParties = [...new Map(results.map(item => [item.party, item])).values()];
        target.innerHTML = '<strong>Live Results</strong>';
        uniqueParties.forEach(p => {
            target.innerHTML += `
                <div class="legend-item">
                    <div class="color-swatch" style="background:${p.color}"></div>
                    <span>${p.party}</span>
                </div>`;
        });
    }
};

document.addEventListener('DOMContentLoaded', () => ElectionMap.init());