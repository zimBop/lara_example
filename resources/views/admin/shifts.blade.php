@extends('admin.app')

@section('title', 'Active shifts - ' . config('app.name'))

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>Shifts</h3>
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
                        <th scope="col">City</th>
                        <th scope="col">Started at</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $shift->id }}</td>
                            <td>{{ $shift->driver->full_name }}</td>
                            <td>
                                @if($shift->vehicle->color_data)
                                    <span class="px-2 mr-2 border" style="background-color: {{ $shift->vehicle->color_data['hex'] }}"></span>
                                @endif
                                {{ $shift->vehicle->license_plate }}
                            </td>
                            <td>{{ $shift->city->name }}</td>
                            <td>{{ $shift->started_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route(R_ADMIN_SHIFTS_FINISH, $shift) }}" title="Finish" data-toggle="tooltip" class="red"
                                    onclick="if(!confirm('Do you really want to finish {{ $shift->driver->full_name }}\'s shift?'))return false;">
                                    <i class="fas fa-stop-circle text-danger"></i> <span class="text-danger">Finish</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5">No active shifts</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $shifts->links() }}
            </div>
        </div>
    </div>
@endsection
