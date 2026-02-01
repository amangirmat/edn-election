<div class="results-table-container">
    <table class="election-table">
        <thead>
            <tr>
                <th>{{ ($scope ?? '') === 'woreda' ? 'Candidate / Party' : 'Political Party' }}</th>
                {{-- Dynamic Header based on unit --}}
                <th style="text-align: right;">{{ $results->first()->unit ?? 'Votes' }}</th>
                <th style="text-align: right;">Share</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                @php $pColor = $result->party_color ?? '#666'; @endphp
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="table-party-indicator" style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background-color: {{ $pColor }};"></span>
                            <div>
                                <span style="font-weight: 700; color: #1a1a1a; display: block;">{{ $result->display_name }}</span>
                                @if(!empty($result->sub_name))
                                    <span style="font-size: 11px; color: #6c757d;">{{ $result->sub_name }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: 700;" class="table-votes">
                        {{-- Use ->value because that is what you set in the Provider --}}
                        {{ number_format($result->value) }}
                    </td>
                    <td style="text-align: right;" class="table-percent">
                        {{ number_format($result->percent, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
