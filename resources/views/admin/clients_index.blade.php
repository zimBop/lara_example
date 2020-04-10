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
                        <th scope="col">Is&nbsp;active?</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ optional($client)->full_name }}</td>
                            <td>{{ optional($client)->phone }}</td>
                            <td>{{ optional($client)->email }}</td>
                            <td>{{ optional($client->birthday)->format('M d, Y') }} @if($client->birthday)<span title="Age">({{ $client->age }})</span>@endif</td>
                            <td>{{ $client->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="custom-control custom-switch" title="{{ $client->is_active ? 'Active' : 'Inactive' }}">
                                    <input type="checkbox" class="custom-control-input client-activity-switch" data-client-id="{{ $client->id }}"
                                           id="customSwitch{{ $client->id }}" @if($client->is_active) checked @endif>
                                    <label class="custom-control-label" for="customSwitch{{ $client->id }}"></label>
                                </div>
                            </td>
                            <td>
                                {{--<a href="#" title="Edit" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                                <a href="#" title="Remove" data-toggle="tooltip"><i class="fas fa-trash"></i></a>--}}
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
