<select class="form-control form-control-sm" name="cities[{{ $shift->id }}]">
    <option value="" disabled {{ $shift->city_id ? '' : 'selected' }}>City</option>
    @foreach($cities as $city)
        <option value="{{ $city->id }}" {{ $shift->city_id === $city->id ? 'selected' : '' }}>
            {{ $city->name }}
        </option>
    @endforeach
</select>
