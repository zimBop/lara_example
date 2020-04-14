@extends('admin.app')

@section('title', 'Drivers - ' . config('app.name'))

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
                    <h3>Drivers</h3>
                </div>
                {{--<div class="col-12 col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-inline">
                                @csrf
                                <div class="flex-fill">
                                    <input type="text" class="form-control w-100" placeholder="Search for driver"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Created at</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($drivers as $driver)
                        <tr>
                            <td>{{ $driver->id }}</td>
                            <td>{{ optional($driver)->full_name }}</td>
                            <td>{{ optional($driver)->email }}</td>
                            <td>{{ $driver->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route(R_ADMIN_DRIVERS_EDIT, $driver) }}" title="Edit" data-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route(R_ADMIN_DRIVERS_DELETE, $driver) }}" title="Remove" data-toggle="tooltip" class="are-you-sure">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5">No drivers yet</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
@endsection
