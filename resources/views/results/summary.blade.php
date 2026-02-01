@extends('core/base::layouts.master')

@section('head')
<style>
    .dashboard-stat-row { margin-bottom: 25px; display: flex; flex-wrap: wrap; }
    /* Adjusted to 5 columns for the 5 cards */
    .stat-col { flex: 0 0 20%; max-width: 20%; padding: 0 10px; margin-bottom: 15px; }
    
    .card-stat { 
        border: none; border-radius: 12px; padding: 15px; color: white; 
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); height: 100%;
    }
    .bg-gradient-blue { background: linear-gradient(45deg, #1e3a8a, #3b82f6); }
    .bg-gradient-green { background: linear-gradient(45deg, #064e3b, #10b981); }
    .bg-gradient-orange { background: linear-gradient(45deg, #7c2d12, #f59e0b); }
    .bg-gradient-purple { background: linear-gradient(45deg, #4c1d95, #8b5cf6); }
    .bg-gradient-cyan { background: linear-gradient(45deg, #0891b2, #22d3ee); }
    
    .stat-value { font-size: 1.4rem; font-weight: 800; }
    .stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.9; }

    .leader-card { border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
    .leader-row { display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #f1f5f9; }
    .leader-row:last-child { border-bottom: none; }
    .leader-rank { width: 35px; height: 35px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 800; margin-right: 15px; }
    .rank-1 { background: #fef3c7; color: #92400e; }
    
    .progress-thin { height: 6px; border-radius: 10px; background: #f1f5f9; margin-top: 5px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-0">{{ $election->name }}</h2>
            <p class="text-muted"><i class="fa fa-info-circle"></i> Live Results Summary</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('election.results.index', ['election_id' => $election->id]) }}" class="btn btn-primary">
                <i class="fa fa-edit"></i> Enter Results
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fa fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <div class="row dashboard-stat-row">
        <div class="stat-col">
            <div class="card-stat bg-gradient-blue">
                <div>
                    <div class="stat-label">Reg. Voters</div>
                    <div class="stat-value">{{ number_format($totalRegistered) }}</div>
                </div>
                <i class="fa fa-users fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-col">
            <div class="card-stat bg-gradient-green">
                <div>
                    <div class="stat-label">Votes Cast</div>
                    <div class="stat-value">{{ number_format($totalVotes) }}</div>
                </div>
                <i class="fa fa-poll fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-col">
            <div class="card-stat bg-gradient-purple">
                <div>
                    <div class="stat-label">Total Parties</div>
                    <div class="stat-value">{{ $totalParties }}</div>
                </div>
                <i class="fa fa-flag fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-col">
            <div class="card-stat bg-gradient-cyan">
                <div>
                    <div class="stat-label">Candidates</div>
                    <div class="stat-value">{{ $totalCandidates }}</div>
                </div>
                <i class="fa fa-user-tie fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-col">
            <div class="card-stat bg-gradient-orange">
                <div>
                    <div class="stat-label">Turnout</div>
                    <div class="stat-value">{{ round($turnout, 1) }}%</div>
                </div>
                <i class="fa fa-user-check fa-2x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="leader-card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold">Candidate Standings</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($candidateStandings as $index => $standing)
                    <div class="leader-row">
                        <div class="leader-rank {{ $index == 0 ? 'rank-1' : '' }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold">{{ $standing->name }}</span>
                                <span class="font-weight-bold">{{ number_format($standing->total_votes) }}</span>
                            </div>
                            <div class="small text-muted mb-1">{{ $standing->party_name ?: 'Independent' }}</div>
                            <div class="progress-thin">
                                <div class="progress-bar bg-primary" style="width: {{ $standing->percentage }}%"></div>
                            </div>
                        </div>
                        <div class="ml-4 text-right" style="min-width: 60px;">
                            <span class="h5 mb-0 font-weight-bold">{{ round($standing->percentage, 1) }}%</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">No results recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-12">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold">Reporting by Region</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="text-muted small">
                                <th>REGION</th>
                                <th class="text-right">REG. VOTERS</th>
                                <th class="text-right">VOTES</th>
                                <th class="text-right">PROGRESS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regionalStats as $stat)
                            <tr>
                                <td class="font-weight-bold">{{ $stat->name }}</td>
                                <td class="text-right text-muted small">{{ number_format($stat->registered) }}</td>
                                <td class="text-right">{{ number_format($stat->votes) }}</td>
                                <td class="text-right">
                                    <span class="badge {{ $stat->reporting_perc > 0 ? 'badge-info' : 'badge-light' }} border">
                                        {{ $stat->reporting_perc }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop