@extends('admin.app')

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/admin/schedule.min.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    {{-- Moment.js is not necessary for datepicker but used in schedule.js and here --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script>
        // dates for datepicker setup
        var startDate = moment().year({{ optional($firstWeek)->year ?? $selectedYear }})
            .week({{ optional($firstWeek)->number ?? $selectedWeek }}).day('monday').format('MM/DD/YYYY');
        var selectedDate = moment().year({{ $selectedYear }})
            .week({{ $selectedWeek }}).day('monday').format('MM/DD/YYYY');
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link type="text/css" rel="stylesheet" href="{{ mix('css/admin/schedule.min.css') }}">
@endpush

@section('title', 'Schedule - ' . config('app.name'))

@section('content')
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <form method="get" id="week-form">
                @csrf
                <div class="row">
                    <label for="year" class="col-6 col-md-2 col-form-label">Select week</label>
                    <div class="col-6 col-md-3">
                        <input type="text" class="form-control" id="datepicker">
                        <input type="hidden" name="year">
                        <input type="hidden" name="number">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12 col-md-6">
            @if (!$nextWeekExists)
                <a href="{{ route(R_ADMIN_SCHEDULE_GENERATE) }}" class="btn btn-primary float-right">
                    Generate next week schedule
                </a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>Schedule</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            @isset($week)
            <form action="{{ route(R_ADMIN_SCHEDULE_UPDATE, compact('week')) }}" method="POST" enctype="application/x-www-form-urlencoded">
                @csrf
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">Cars</th>
                                @foreach(range(1,7) as $dayNumber)
                                <th colspan="2" scope="colgroup">
                                    {{ now()->startOfWeek()->addDays($dayNumber - 1)->format('l') }}
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                @include('admin.schedule.blocks.time_selects')
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td rowspan="2">
                                    @if($vehicle->color_data)
                                        <span class="px-2 mr-2 border" style="background-color: {{ $vehicle->color_data['hex'] }}"></span>
                                    @endif
                                    {{ $vehicle->license_plate }}
                                </td>
                                @foreach($week->gaps as $gap)
                                    <td>
                                        {{-- TODO refactor and move Shift query into the controller. It won't be a big problem :) --}}
                                        @include('admin.schedule.blocks.driver_select', [
                                            'shift' => $gap->shifts->where('vehicle_id', $vehicle->id)->first()
                                        ])
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($week->gaps as $gap)
                                    <td>
                                        @include('admin.schedule.blocks.city_select', [
                                            'shift' => $gap->shifts->where('vehicle_id', $vehicle->id)->first()
                                        ])
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center p-5"><h3>No vehicles yet</h3></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="form-group d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" name="save" value="save">
                        Save
                    </button>
                </div>
            </form>
            @else
            Schedule for this week is not found.
            @endisset
            {{--                {{ $vehicles->links() }}--}}
        </div>
    </div>
@endsection
