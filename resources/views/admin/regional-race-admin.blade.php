<div class="form-group mb-3">
    <label class="control-label">Select Election</label>
    {!! Form::customSelect('election_id', $elections, $attributes['election_id'] ?? null) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">Region (Optional)</label>
    {!! Form::customSelect('region_id', $regions, $attributes['region_id'] ?? null, ['id' => 'select-region']) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">Zone (Optional)</label>
    {!! Form::customSelect('zone_id', $zones, $attributes['zone_id'] ?? null, ['id' => 'select-zone']) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">Woreda (Optional)</label>
    {!! Form::customSelect('woreda_id', $woredas, $attributes['woreda_id'] ?? null, ['id' => 'select-woreda']) !!}
</div>

<div class="help-block">
    <small>The levels work hierarchically: Region → Zone → Woreda. Selecting a lower level provides more specific results.</small>
</div>


<div class="form-group mb-3">
    <label class="control-label">Header Style</label>
    {!! Form::customSelect('header_style', $headerStyles, Arr::get($attributes, 'header_style')) !!}
</div>


<div class="form-group mb-3">
    <label class="control-label">Card Display Style</label>
    {!! Form::customSelect('card_style', $cardStyles, $attributes['card_style'] ?? 'default') !!}
    <small class="text-muted">Choose which layout template to use for this race.</small>
</div>

<div class="form-group mb-3">
    <label class="control-label">Footer Style</label>
    {!! Form::customSelect('footer_style', $footerStyles, Arr::get($attributes, 'footer_style')) !!}
</div>
<script>
    'use strict';
    $(document).ready(function () {
        // 1. Load data from PHP
        const allZones = @json(\Botble\EdnElection\Models\Zone::select('id', 'name', 'region_id')->get());
        const allWoredas = @json(\Botble\EdnElection\Models\Woreda::select('id', 'name', 'zone_id')->get());

        const $regionSelect = $('#select-region');
        const $zoneSelect = $('#select-zone');
        const $woredaSelect = $('#select-woreda');

        // 2. Region Change -> Filter Zones
        $regionSelect.on('change', function () {
            const regionId = $(this).val();
            
            $zoneSelect.empty().append('<option value="">--- All Zones ---</option>');
            $woredaSelect.empty().append('<option value="">--- All Woredas ---</option>');

            const filteredZones = regionId 
                ? allZones.filter(zone => zone.region_id == regionId)
                : allZones;

            filteredZones.forEach(zone => {
                $zoneSelect.append(`<option value="${zone.id}">${zone.name}</option>`);
            });
            
            $zoneSelect.trigger('change');
        });

        // 3. Zone Change -> Filter Woredas
        $zoneSelect.on('change', function () {
            const zoneId = $(this).val();
            
            // Clear Woredas
            $woredaSelect.empty().append('<option value="">--- All Woredas ---</option>');

            // If a zone is selected, filter. If no zone is selected, we can show all or stay empty.
            // Usually, it's better to keep it empty until a zone is picked.
            if (zoneId) {
                const filteredWoredas = allWoredas.filter(woreda => woreda.zone_id == zoneId);
                filteredWoredas.forEach(woreda => {
                    $woredaSelect.append(`<option value="${woreda.id}">${woreda.name}</option>`);
                });
            } else if (!$regionSelect.val()) {
                // If "National" (no region and no zone), show all woredas
                allWoredas.forEach(woreda => {
                    $woredaSelect.append(`<option value="${woreda.id}">${woreda.name}</option>`);
                });
            }
            
            $woredaSelect.trigger('change');
        });
    });
</script>