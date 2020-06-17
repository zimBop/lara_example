<select class="form-control form-control-sm driver-select"
        name="drivers[{{ $shift->id }}]" data-week-day="{{ $gap->week_day }}">
    <option value="" {{ $shift->driver_id ? '' : 'selected' }}>Driver</option>
    @foreach($drivers as $driver)
        <option value="{{ $driver->id }}" data-value="{{ $driver->id }}"
                {{ $shift->driver_id === $driver->id ? 'selected' : '' }}>
            {{ $driver->full_name }}
        </option>
    @endforeach
</select>
