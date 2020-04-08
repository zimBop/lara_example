@extends('admin.app')

@section('title', 'Clients - ' . config('app.name'))

@section('content')
    <div class="row mb-3 justify-content-end">
        {{--<div class="col-12 col-md-6">
            Some extra text or controls
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-end">
            <a href="#" class="btn btn-secondary mr-3">Add client</a>
            <a href="#" class="btn btn-primary">Another action button</a>
        </div>--}}
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>Clients</h3>
                </div>
                {{--<div class="col-12 col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-inline">
                                @csrf
                                <div class="flex-fill">
                                    <input type="text" class="form-control w-100" placeholder="Search for client"/>
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
                        <th scope="col">Phone</th>
                        <th scope="col">Email</th>
                        <th scope="col">Birthday</th>
                        <th scope="col">Created at</th>
                        <th scope="col">Statuses</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->full_name }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->birthday->format('M d, Y') }} <span title="Age">({{ $client->age }})</span></td>
                            <td>{{ $client->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($client->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">InActive</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" title="Edit" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                                <a href="#" title="Remove" data-toggle="tooltip"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-5">No clients yet</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $clients->links() }}
            </div>
        </div>
    </div>
@endsection
