@extends('admin.app')

@section('title', 'Drivers - ' . config('app.name'))

@push('css')
    <link type="text/css" rel="stylesheet" href="{{ mix('css/admin/driver.min.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        bsCustomFileInput.init();
    </script>
@endpush

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

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone<sup>*</sup></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $driver->phone ?? '') }}"
                                   placeholder="+1 (XXX) XXX-XXXX"
                                   pattern="^(\+\s?)?(1\s?)?((\([0-9]{3}\))|[0-9]{3})[\s\-]?[\0-9]{3}[\s\-]?[0-9]{4}$"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- /Phone -->

                        <!-- Avatar -->
                        <div class="form-group row">
                            @isset($driver->avatar)
                                <div class="col-12 col-md-2">
                                    <img src="{{ $driver->avatar_url }}" class="img-fluid" title="Avatar" alt="Avatar"/>
                                </div>
                            @endisset
                            <div class="col col-md-4">
                                <div class="custom-file">
                                    <label for="avatar" class="custom-file-label">Avatar</label>
                                    <input type="file" class="form-control-file custom-file-input" id="avatar" name="avatar">
                                </div>
                            </div>
                            @isset($driver->avatar)
                            <div class="col col-md-4">
                                <label for="delete_avatar" class="checkbox-label">
                                    <input type="checkbox" name="delete_avatar" id="delete_avatar" style="vert-align: middle;">
                                    &nbsp;Delete Avatar
                                </label>
                            </div>
                            @endisset
                        </div>

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
