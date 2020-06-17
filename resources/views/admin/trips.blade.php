@extends('admin.app')

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script type="text/javascript" src="{{ mix('js/admin/trips.min.js') }}"></script>
@endpush

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
@endpush

@section('title', 'Finished trips - ' . config('app.name'))

@section('content')
    <div class="col-12 col-lg-9 col-md-12 mb-3">
        <form method="get" id="filter" class="form-inline" action="{{ request()->url() }}">
            @csrf
                <label for="driver" class="mb-2 mr-1">Select driver</label>
                <select class="form-control form-control-sm mb-2 mr-3 @error('driver_id') is-invalid @enderror" id="driver" name="driver_id">
                    <option value="">All</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}"
                            {{ isset($input['driver_id']) && $input['driver_id'] == $driver->id ? 'selected' : '' }}>
                            {{ $driver->full_name }}
                        </option>
                    @endforeach
                </select>
                <label for="start" class="mb-2 mr-1">From date</label>
                <input type="text" name="start" id="start" class="form-control datepicker form-control-sm mb-2 mr-3"
                       value="{{ $input['start'] ?? '' }}" />
                <label for="end" class="mb-2 mr-1">To date</label>
                <input type="text" name="end" id="end" class="form-control datepicker form-control-sm mb-2 mr-3"
                       value="{{ $input['end'] ?? '' }}" />
                <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <div class="row">
            <div class="col">
                @error('driver_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @error('start')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @error('end')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>Finished trips</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Driver</th>
                        <th scope="col">Car</th>
                        <th scope="col">Client</th>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col">Finished at</th>
                        <th scope="col">CO<sup>2</sup> saved lb</th>
                        <th scope="col">Price $</th>
                        <th scope="col">Tips $</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($trips as $trip)
                        <tr>
                            <td>{{ $trip->id }}</td>
                            <td>{{ $trip->shift->driver->full_name }}</td>
                            <td>
                                @if($trip->shift->vehicle->color_data)
                                    <span class="px-2 mr-2 border" style="background-color: {{ $trip->shift->vehicle->color_data['hex'] }}"></span>
                                @endif
                                {{ $trip->shift->vehicle->license_plate }}
                            </td>
                            <td>{{ $trip->client->full_name }}</td>
                            <td>{{ $trip->origin['label'] }}</td>
                            <td>{{ $trip->destination['label'] }}</td>
                            <td>{{ $trip->updated_at->format('M d, Y H:i') }}</td>
                            <td>{{ $trip->co2 }}</td>
                            <td>{{ centsToDollars($trip->price) }}</td>
                            <td>{{ centsToDollars($trip->tips_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center p-5">No finished trips found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $trips->links() }}
            </div>
        </div>
    </div>
@endsection
