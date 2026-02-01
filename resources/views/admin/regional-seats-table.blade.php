<table class="table table-bordered">
    <thead>
        <tr>
            <th>Region</th>
            <th>Seats</th>
        </tr>
    </thead>
    <tbody>
        @foreach($regions as $id => $name)
            <tr>
                <td>{{ $name }}</td>
                <td>
                    <input type="number" 
                           name="regional_seats[{{ $id }}]" 
                           value="{{ $regionalSeats[$id] ?? 0 }}" 
                           class="form-control" 
                           min="0" required>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
