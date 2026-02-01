@extends('core/base::layouts.master')

@section('head')
<style>
    /* Card & Layout Refinements */
    .filter-card { background: #fff; border: 1px solid #cbd5e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .woreda-card { border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 25px; background: #fff; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    
    /* Compact Woreda Header */
    .woreda-header { background: #1e293b; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
    .stat-pill { padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .pill-total { background: #1e40af; color: #fff; border: 1px solid #3b82f6; }
    .pill-remaining { background: #7c2d12; color: #ffedd5; border: 1px solid #ea580c; transition: all 0.3s ease; }
    .pill-error { background: #b91c1c !important; color: #fff !important; border-color: #f87171 !important; }

    /* Compact Candidate UI */
    .candidate-grid { display: flex; flex-wrap: wrap; gap: 12px; padding: 15px; background: #f1f5f9; }
    .candidate-item { 
        background: white; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; 
        flex: 1 1 190px; max-width: 230px; display: flex; flex-direction: column; align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .candidate-label { font-size: 0.9rem; font-weight: 800; color: #1e293b; text-align: center; margin-bottom: 2px; line-height: 1.2; }
    .party-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-align: center; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Large Result Numbers */
    .vote-input-modern { 
        width: 100%; border: 2px solid #94a3b8; border-radius: 6px; padding: 8px; 
        text-align: center; font-size: 1.6rem; font-weight: 900; color: #1e3a8a; background: #fff;
    }
    .vote-input-modern:focus { border-color: #2563eb; outline: none; background: #eff6ff; }

    /* Sticky Save Bar */
    .save-bar-sticky-container { position: sticky; bottom: 15px; z-index: 1000; margin-top: 30px; width: 100%; }
    .save-bar-content { 
        background: #fff; border: 2px solid #1e293b; border-radius: 12px; padding: 18px 25px; 
        display: flex; justify-content: space-between; align-items: center; box-shadow: 0 -8px 20px rgba(0,0,0,0.12);
    }
</style>
@endsection

@section('content')
<div class="filter-card">
    <form action="{{ route('election.results.index') }}" method="GET" id="filterForm" class="row align-items-end">
        <input type="hidden" name="election_id" value="{{ $election->id }}">
        
        <div class="col-md-5">
            <label class="font-weight-bold small text-uppercase text-muted"><i class="fa fa-map"></i> 1. Region</label>
            <select name="region_id" id="region_select" class="form-control select-search-full" onchange="this.form.submit()">
                <option value="">-- Select Region --</option>
                @foreach($regions as $id => $name)
                    <option value="{{ $id }}" {{ $selectedRegion == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label class="font-weight-bold small text-uppercase text-muted"><i class="fa fa-layer-group"></i> 2. Zone</label>
            <select name="zone_id" id="zone_select" class="form-control select-search-full" onchange="this.form.submit()" {{ !$selectedRegion ? 'disabled' : '' }}>
                <option value="">-- Select Zone --</option>
                @if($selectedRegion)
                    @foreach(\Botble\EdnElection\Models\Zone::where('region_id', $selectedRegion)->pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}" {{ $selectedZone == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="col-md-2">
            <a href="{{ route('election.results.index', ['election_id' => $election->id]) }}" class="btn btn-outline-secondary btn-block"><i class="fa fa-sync"></i> Reset</a>
        </div>
    </form>
</div>

@if($selectedZone)
    <form action="{{ route('election.results.store') }}" method="POST" id="resultsForm">
        @csrf
        <input type="hidden" name="election_id" value="{{ $election->id }}">

        @forelse($woredas as $woreda)
            @php
                $totalReg = $woreda->total_voters;
                $votesSum = collect($resultsMap[$woreda->id] ?? [])->sum();
                $remaining = $totalReg - $votesSum;
            @endphp
            <div class="woreda-card" data-woreda-id="{{ $woreda->id }}" data-total="{{ $totalReg }}">
                <div class="woreda-header">
                    <span class="h5 mb-0 font-weight-bold"><i class="fa fa-map-pin mr-2"></i> {{ $woreda->name }}</span>
                    <div class="d-flex" style="gap: 12px;">
                        <div class="stat-pill pill-total">
                            <i class="fa fa-users"></i> Registered: {{ number_format($totalReg) }}
                        </div>
                        <div class="stat-pill pill-remaining {{ $remaining < 0 ? 'pill-error' : '' }}">
                            <i class="fa fa-calculator"></i> Remaining: <span class="remaining-count">{{ number_format($remaining) }}</span>
                        </div>
                    </div>
                </div>
                <div class="candidate-grid">
                    @foreach($woreda->candidates as $candidate)
                        <div class="candidate-item">
                            <span class="candidate-label">{{ $candidate->name }}</span>
                            {{-- Party Name Display --}}
                            <span class="party-label text-muted">{{ $candidate->party->name ?? 'Independent' }}</span>
                            
                            <input type="number" 
                                   name="results[{{ $woreda->id }}][{{ $candidate->id }}]" 
                                   value="{{ $resultsMap[$woreda->id][$candidate->id] ?? '' }}"
                                   class="vote-input-modern vote-counter-input" 
                                   data-woreda="{{ $woreda->id }}"
                                   placeholder="0"
                                   min="0">
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> No Woredas found.</div>
        @endforelse

        @if($woredas->isNotEmpty())
            <div class="save-bar-sticky-container">
                <div class="save-bar-content">
                    <button type="button" id="btn-clear-votes" class="btn btn-link text-danger font-weight-bold">
                        <i class="fa fa-trash-alt"></i> Clear All Votes
                    </button>
                    <div id="validation-msg" class="text-danger font-weight-bold" style="display:none;">
                        <i class="fa fa-exclamation-triangle"></i> Over-voting detected!
                    </div>
                    <button type="submit" id="save-btn" class="btn btn-success btn-lg px-5 font-weight-bold shadow">
                        <i class="fa fa-save mr-2"></i> SAVE ALL RESULTS
                    </button>
                </div>
            </div>
        @endif
    </form>
@else
    <div class="text-center py-5 bg-white border rounded">
        <h5 class="text-muted">Select a Region and Zone to begin.</h5>
    </div>
@endif
@stop

@section('footer')
<script>
    $(document).ready(function() {
        // --- LIVE MATH LOGIC ---
        function updateAllWoredaMath() {
            $('.woreda-card').each(function() {
                let $card = $(this);
                let total = parseInt($card.data('total')) || 0;
                let sum = 0;
                $card.find('.vote-counter-input').each(function() {
                    sum += parseInt($(this).val()) || 0;
                });
                let remaining = total - sum;
                $card.find('.remaining-count').text(remaining.toLocaleString());
                
                if (remaining < 0) { $card.find('.pill-remaining').addClass('pill-error'); } 
                else { $card.find('.pill-remaining').removeClass('pill-error'); }
            });
            checkGlobalSaveStatus();
        }

        function checkGlobalSaveStatus() {
            let hasError = $('.pill-error').length > 0;
            $('#save-btn').prop('disabled', hasError).toggleClass('btn-secondary', hasError).toggleClass('btn-success', !hasError);
            $('#validation-msg').toggle(hasError);
        }

        $(document).on('input', '.vote-counter-input', function() {
            updateAllWoredaMath();
        });

        // --- FIXED CLEAR ALL LOGIC ---
        $(document).on('click', '#btn-clear-votes', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to clear all vote numbers in this zone?')) {
                // Find all vote inputs and set to empty string
                $('.vote-counter-input').val('');
                // Refresh the remaining voter counts
                updateAllWoredaMath();
            }
        });

        // --- ORIGINAL AJAX ---
        $(document).on('change', '#region_select', function() {
            var regionId = $(this).val();
            var $zoneSelect = $('#zone_select');
            $zoneSelect.empty().append('<option value="">-- Loading Zones... --</option>');
            if (regionId) {
                $.ajax({
                    url: '{{ route("election.results.get-zones") }}',
                    type: 'GET',
                    data: { region_id: regionId },
                    success: function(data) {
                        $zoneSelect.empty().append('<option value="">-- Select Zone --</option>');
                        $.each(data, function(id, name) {
                            $zoneSelect.append('<option value="' + id + '">' + name + '</option>');
                        });
                        $zoneSelect.prop('disabled', false);
                        if ($zoneSelect.data('select2')) { $zoneSelect.select2('destroy'); }
                        $zoneSelect.select2({ width: '100%' });
                    }
                });
            }
        });
    });
</script>
@endsection