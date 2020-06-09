@foreach(range(1,7) as $dayNumber)
    @foreach($week->gaps->where('week_day', $dayNumber) as $gap)
        <th scope="col">
            <select class="form-control form-control-sm" name="start[{{ $gap->id }}]">
                @foreach($timeSelectOptions as $timeInDbFormat => $timeInDisplayFormat)
                    <option value="{{ $timeInDbFormat }}" {{ $gap->start === $timeInDbFormat ? 'selected' : '' }}>
                        {{ $timeInDisplayFormat }}
                    </option>
                @endforeach
            </select>
            -
            <select class="form-control form-control-sm" name="end[{{ $gap->id }}]">
                @foreach($timeSelectOptions as $timeInDbFormat => $timeInDisplayFormat)
                    <option value="{{ $timeInDbFormat }}" {{ $gap->end === $timeInDbFormat ? 'selected' : '' }}>
                        {{ $timeInDisplayFormat }}
                    </option>
                @endforeach
            </select>
        </th>
    @endforeach
@endforeach
