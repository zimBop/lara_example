@extends('admin.app')

@section('title', 'Schedule - ' . config('app.name'))

@section('content')
    <div class="row mb-3 justify-content-end">
        {{--<div class="col-12 col-md-6">
            Some extra text or controls
        </div>--}}
        <div class="col-12 col-md-6 d-flex justify-content-end">
            <a href="{{ route(R_ADMIN_DRIVERS_CREATE) }}" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add driver</a>
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
            <div class="table-responsive-sm">
                @isset($week)
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
                            @foreach(range(1,7) as $dayNumber)
                                @foreach($week->gaps->where('week_day', $dayNumber) as $gap)
                                    <th scope="col">
                                        {{ $gap->start_formatted . ' - ' . $gap->end_formatted }}
                                    </th>
                                @endforeach
                            @endforeach
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
                            @foreach($week->gaps->sortBy('week_day') as $gap)
                                <td>
                                    @include('admin.schedule.blocks.driver_select')
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($week->gaps->sortBy('week_day') as $gap)
                                <td>
                                    @include('admin.schedule.blocks.city_select')
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
                @else
                    Schedule for this week is not found.
                @endisset
{{--                {{ $vehicles->links() }}--}}
            </div>
        </div>
    </div>
@endsection
