<select class="form-control form-control-sm" >
    <option value="" disabled {{ $gap->driver_id ? '' : 'selected' }}>Driver</option>
    @foreach($drivers as $driver)
        <option value="{{ $driver->id }}" {{ $gap->driver_id === $driver->id ? 'selected' : '' }}>
            {{ $driver->full_name }}
        </option>
    @endforeach
</select>
