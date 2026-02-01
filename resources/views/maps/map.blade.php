@extends('core/base::layouts.master')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #election-map-wrapper { display: flex; height: 700px; background: #fff; border: 1px solid #ddd; border-radius: 5px; }
    #map { flex: 3; z-index: 1; }
    #map-sidebar { flex: 1; padding: 20px; border-left: 1px solid #eee; overflow-y: auto; }
    .legend { background: white; padding: 10px; line-height: 18px; color: #555; }
    .legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; }
</style>

<div id="election-map-wrapper">
    <div id="map"></div>
    <div id="map-sidebar">
        <h3 id="selected-area-name">Ethiopia National</h3>
        <hr>
        <div id="selected-area-stats">
            <p>Click a region to see results.</p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('vendor/core/plugins/edn-election/maps/js/election-map.js') }}"></script>
@stop