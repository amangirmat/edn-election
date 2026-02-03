<form action="{{ route('election.regions.import-confirm') }}" method="POST">
    @csrf
    <input type="hidden" name="file_path" value="{{ $filePath }}">
    
    <table class="table table-striped">
        <thead>
            <tr><th>Name</th></tr>
        </thead>
        <tbody>
            @foreach(array_slice($rows, 1, 10) as $row) {{-- Show first 10 rows --}}
                <tr><td>{{ $row[0] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-success">Confirm and Import All</button>
</form>