@extends('admin.app')

@section('title', 'Drivers - ' . config('app.name'))

@section('content')
    <div class="row mb-3">
        <div class="col d-flex justify-content-start">
            <a href="{{ route(R_ADMIN_DRIVERS_LIST) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> To the list</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-6">
                    <h3>
                        @isset($driver->id)
                            Edit driver «{{ $driver->full_name }}» (id: {{ $driver->id }})
                        @else
                            Create driver
                        @endisset
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route(R_ADMIN_DRIVERS_STORE, $driver ?? null) }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-sm-6">
                        <!-- First Name -->
                        <div class="form-group">
                            <label for="first-name">First Name<sup>*</sup></label>
                            <input type="text" name="first_name" id="first-name" value="{{ old('first_name', $driver->first_name ?? '') }}"
                                   class="form-control @error('first_name') is-invalid @enderror">
                            @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- /First Name -->

                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="last-name">Last Name<sup>*</sup></label>
                            <input type="text" name="last_name" id="last-name" value="{{ old('last_name', $driver->last_name ?? '') }}"
                                   class="form-control @error('last_name') is-invalid @enderror">
                            @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- /Last name -->

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email<sup>*</sup></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $driver->email ?? '') }}"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- /Email -->

                        @if(request()->routeIs(R_ADMIN_DRIVERS_CREATE))
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password<sup>*</sup></label>
                            <input type="password" name="password" id="password" value=""
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- /Password -->
                        @endif

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" name="save" value="save">Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
