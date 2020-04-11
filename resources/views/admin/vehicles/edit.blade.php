@extends('admin.app')

@section('title', 'Vehicles - ' . config('app.name'))

@section('content')
    <div class="row mb-3">
        <div class="col d-flex justify-content-start">
            <a href="{{ route(R_ADMIN_VEHICLES_LIST) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> To the list</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4">
                    <h3>
                        @isset($vehicle->id)
                            Edit vehicle «{{ $vehicle->name }}» (id: {{ $vehicle->id }})
                        @else
                            Create vehicle
                        @endisset
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route(R_ADMIN_VEHICLES_STORE, isset($vehicle) ? compact('vehicle') : '') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="license_plate">License plate<sup>*</sup></label>
                            <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate', $vehicle->license_plate ?? '') }}"
                                   class="form-control @error('license_plate') is-invalid @enderror">
                            @error('license_plate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <!-- Brand -->
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="brand">Brand<sup>*</sup></label>
                                    <select name="brand_id" id="brand"
                                            class="form-control @error('brand_id') is-invalid @enderror" required>
                                        @forelse($brands as $brand)
                                            @if(old('brand_id', optional($vehicle ?? null)->brand_id) === $brand->id)
                                                <option value="{{ $brand->id }}" selected>{{ $brand->name }}</option>
                                            @else
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endif
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- /Brand -->
                            <!-- Model -->
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="model">Model<sup>*</sup></label>
                                    <select name="model_id" id="model"
                                            class="form-control @error('model_id') is-invalid @enderror" required>
                                        @forelse($models as $model)
                                            @if(old('model_id', optional($vehicle ?? null)->model_id) === $model->id)
                                                <option value="{{ $model->id }}" selected>{{ $model->name }}</option>
                                            @else
                                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                                            @endif
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('model_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- /Model -->
                        </div>
                        <div class="row">
                            <!-- Color -->
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <select name="color_id" id="color"
                                            class="form-control @error('color_id') is-invalid @enderror">
                                        <option value="">Do not specify color</option>
                                        @forelse($colors as $color)
                                            @if(old('color_id', optional($vehicle ?? null)->color_id) === $color->id)
                                                <option value="{{ $color->id }}" selected>{{ ucfirst($color->name) }}</option>
                                            @else
                                                <option value="{{ $color->id }}">{{ ucfirst($color->name) }}</option>
                                            @endif
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('color_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- /Color -->
                            <!-- Status -->
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="status">Status<sup>*</sup></label>
                                    <select name="status_id" id="status"
                                            class="form-control @error('status_id') is-invalid @enderror" required>
                                        @forelse($statuses as $status)
                                            @if(old('status_id', optional($vehicle ?? null)->status_id) === $status->id)
                                                <option value="{{ $status->id }}" selected>{{ ucfirst($status->name) }}</option>
                                            @else
                                                <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                                            @endif
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- /Status -->
                        </div>
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
