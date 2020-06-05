<select class="form-control form-control-sm" >
    <option value="" disabled {{ $gap->city_id ? '' : 'selected' }}>City</option>
    @foreach($cities as $city)
        <option value="{{ $city->id }}" {{ $gap->city_id === $city->id ? 'selected' : '' }}>
            {{ $city->name }}
        </option>
    @endforeach
</select>
