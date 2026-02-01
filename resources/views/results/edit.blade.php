@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Enter Results: {{ $woreda->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('election.results.update', $woreda->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="election_id" value="1"> <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Candidate / Party</th>
                                <th width="200">Votes Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $candidate)
                            <tr>
                                <td>
                                    <strong>{{ $candidate->name }}</strong><br>
                                    <small class="text-muted">{{ $candidate->party->name }}</small>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="votes[{{ $candidate->id }}]" 
                                           class="form-control" 
                                           value="{{ $existingResults[$candidate->id] ?? 0 }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save All Results</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop