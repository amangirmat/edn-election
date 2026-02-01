@extends('core/base::layouts.master')
@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('election.results.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label>Election</label>
                    <select name="election_id" class="form-control select-search-full">
                        @foreach($elections as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Woreda</label>
                    <select name="woreda_id" id="woreda_select" class="form-control select-search-full">
                        <option value="">-- Select Woreda --</option>
                        @foreach($woredas as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Party</th>
                        <th style="width: 200px;">Votes</th>
                    </tr>
                </thead>
                <tbody id="candidate_table_body">
                    <tr>
                        <td colspan="3" class="text-center text-info">Please select a Woreda to load candidates.</td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success mt-3">Submit Results</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const woredaSelect = document.getElementById('woreda_select');
        const tableBody = document.getElementById('candidate_table_body');

        if (woredaSelect) {
            woredaSelect.addEventListener('change', function() {
                const woredaId = this.value;
                console.log('Selected Woreda ID:', woredaId); // Debugging

                if (!woredaId) {
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center">Please select a Woreda</td></tr>';
                    return;
                }

                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading candidates...</td></tr>';

                // Use the route helper
                fetch(`{{ route('election.results.get-candidates') }}?woreda_id=${woredaId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        console.log('Candidates loaded successfully');
                        tableBody.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching candidates:', error);
                        tableBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error loading candidates. Check console.</td></tr>';
                    });
            });
        }
    });
</script>
@stop