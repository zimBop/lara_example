@extends('admin.app')

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/admin/reports.min.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    {{-- Moment.js is not necessary for datepicker but used in schedule.js and here --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script>
        // dates for datepicker setup
        var startDate = '{{ $startDate }}';
        var endDate = '{{ $endDate }}';
        var selectedDate = '{{ $selectedDate }}';
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link type="text/css" rel="stylesheet" href="{{ mix('css/admin/schedule.min.css') }}">
@endpush

@section('title', 'Weekly report - ' . config('app.name'))

@section('content')
    <div class="row mb-3">
        <div class="col-12 col-lg-10 col-md-10">
            <form method="post" action="{{ route(R_ADMIN_WEEKLY_REPORT_DOWNLOAD) }}">
                @csrf
                <div class="row">
                    <label for="year" class="col-6 col-lg-1 col-md-3 col-form-label">Select week</label>
                    <div class="col-6 col-lg-3 col-md-5">
                        <input type="text" class="form-control" id="datepicker">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <input type="hidden" name="number" value="{{ $selectedWeek }}">
                    </div>
                    <div class="col-12 col-lg-2 col-md-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Download report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
