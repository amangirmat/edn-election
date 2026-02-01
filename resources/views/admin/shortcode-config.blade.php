<div class="form-group mb-3">
    <label class="control-label">Select Election</label>
    <div class="ui-select-wrapper">
        <select name="election_id" class="form-control ui-select">
            <option value="">-- Select Election --</option>
            @foreach($elections as $id => $name)
                <option value="{{ $id }}" {{ Arr::get($attributes, 'election_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        <svg class="svg-next-icon svg-next-icon-size-16">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
        </svg>
    </div>
</div>