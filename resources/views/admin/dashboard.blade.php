@extends('admin.app')

@section('title', 'Dashboard ' . config('app.name'))

@section('content')
    <div class="row">
        <div class="col-xl-6 col-lg-8 col-md-10 col-12">
            <h3>{{ now()->format('m/d/y') }} statistics</h3>
            <div class="row">
                <div class="col">
                    Number of rides: <span class="text-info">{{ $tripsCount }}</span>
                </div>
                <div class="col">
                    Earned: <span class="text-info">${{ centsToDollars($earned['priceSum']) }}</span>
                </div>
                <div  class="col">
                    Tips: <span class="text-info">${{ centsToDollars($earned['tipsSum']) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
