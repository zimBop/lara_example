@extends('admin.app')

@section('title', 'Vehicles - ' . config('app.name'))

@section('content')
    <div class="row mb-3 justify-content-end">
       {{-- <div class="col-12 col-md-6">
            Some extra text or controls
        </div>--}}
        <div class="col-12 col-md-6 d-flex justify-content-end">
            <a href="#" class="btn btn-primary disabled">Add vehicle</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>Vehicles</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">License plate</th>
                        <th scope="col">Model</th>
                        <th scope="col">Color</th>
                        <th scope="col">Created</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->id }}</td>
                            <td>{{ $vehicle->license_plate }}</td>
                            <td>{{ $vehicle->name }}</td>
                            <td>
                                <span class="px-2 mr-2 border" style="background-color: {{ $vehicle->color_data['hex'] }}"></span>
                                <span>{{ $vehicle->color_data['name'] }}</span>
                            </td>
                            <td>{{ $vehicle->created_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge badge-{{ $vehicle->status_data['badge'] }}">{{ $vehicle->status_data['name'] }}</span></td>
                            <td>
                                {{--<a href="#" title="Edit" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                                <a href="#" title="Remove" data-toggle="tooltip"><i class="fas fa-trash"></i></a>--}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-5">No vehicles yet</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
@endsection
