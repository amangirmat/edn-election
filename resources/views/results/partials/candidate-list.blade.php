@foreach($candidates as $candidate)
<tr>
    <td>
        <strong>{{ $candidate->name }}</strong>
    </td>
    <td>{{ $candidate->party->name }}</td>
    <td>
        <input type="number" 
               name="votes[{{ $candidate->id }}]" 
               class="form-control" 
               placeholder="Enter votes" 
               min="0">
    </td>
</tr>
@endforeach

@if($candidates->isEmpty())
<tr>
    <td colspan="3" class="text-center text-muted">No candidates assigned to this Woreda.</td>
</tr>
@endif