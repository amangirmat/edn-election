@extends('core/base::layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Review Import: {{ $modelName }}</h4>
            <p class="text-muted">Uniqueness Check: <strong>{{ implode(' + ', $uniqueColumns) }}</strong></p>
        </div>
        <div class="card-body">
            <form action="{{ route('edn.election.import.save') }}" method="POST">
                @csrf
                <input type="hidden" name="temp_path" value="{{ $tempPath }}">
                <input type="hidden" name="model_class" value="{{ $modelClass }}">
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="preview-table">
                        <thead>
                            <tr>
                                @foreach($headers as $header)
                                    <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                                @endforeach
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $dbRecords = json_decode($existingData, true);
                            @endphp

                            @foreach($rows as $index => $row)
                                @php 
                                    $rowFormatted = [];
                                    foreach($headers as $k => $v) {
                                        // Standardize key to snake_case to match DB columns
                                        $cleanKey = \Illuminate\Support\Str::snake(strtolower(trim($v)));
                                        $rowFormatted[$cleanKey] = trim($row[$k] ?? '');
                                    }

                                    // SERVER SIDE INITIAL CHECK
                                    $exists = collect($dbRecords)->contains(function($record) use ($rowFormatted, $uniqueColumns) {
                                        foreach($uniqueColumns as $col) {
                                            $dbVal = strtolower(trim((string)($record[$col] ?? '')));
                                            $inputVal = strtolower(trim((string)($rowFormatted[$col] ?? '')));
                                            if ($dbVal !== $inputVal) return false;
                                        }
                                        return true;
                                    });
                                @endphp

                                <tr class="import-row {{ $exists ? 'table-warning' : '' }}">
                                    @foreach($row as $cellIndex => $cellValue)
    @php 
        // Use Str::snake to ensure "Region ID" becomes "region_id"
        $headerName = \Illuminate\Support\Str::snake(strtolower(trim($headers[$cellIndex])));
        
        // This check now works for all tables because $uniqueColumns is always an array
        $isUniqueCol = in_array($headerName, $uniqueColumns);
    @endphp
    <td>
        <input type="text" 
               name="items[{{ $index }}][{{ $headers[$cellIndex] }}]" 
               value="{{ trim($cellValue) }}" 
               class="form-control {{ $isUniqueCol ? 'verify-input' : '' }}"
               data-col="{{ $headerName }}">
        
        {{-- Show warning on the FIRST column of the unique set (usually 'name') --}}
        @if($isUniqueCol && $headerName === $uniqueColumns[0])
            <div class="warning-text mt-1 {{ $exists ? '' : 'd-none' }}">
                <small class="text-danger fw-bold">
                    <i class="fa fa-exclamation-triangle"></i> Conflict: Exists in DB.
                </small>
            </div>
        @endif
    </td>
@endforeach
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove()">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Confirm & Save All</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<script>
$(document).ready(function() {
    const dbRecords = {!! $existingData !!};
    const uniqueCols = {!! json_encode($uniqueColumns) !!};

    $(document).on('input', '.verify-input', function() {
        const $row = $(this).closest('tr');
        const $warning = $row.find('.warning-text');
        
        // 1. Grab everything typed in the row for our verified columns
        let currentRow = {};
        $row.find('.verify-input').each(function() {
            const col = $(this).data('col'); // This is the snake_case name
            currentRow[col] = $(this).val().trim().toLowerCase();
        });

        // 2. The Logic Check
        const isDuplicate = dbRecords.some(record => {
            return uniqueCols.every(col => {
                const dbVal = String(record[col] || '').trim().toLowerCase();
                const inputVal = String(currentRow[col] || '').trim().toLowerCase();
                return dbVal === inputVal;
            });
        });

        // 3. Update the UI
        if (isDuplicate) {
            $row.addClass('table-warning');
            $warning.removeClass('d-none');
        } else {
            $row.removeClass('table-warning');
            $warning.addClass('d-none');
        }
    });
});
</script>
@stop