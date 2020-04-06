<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">

    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="address=no">

    <title>@yield('title')</title>

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ mix('css/admin/custom.min.css') }}">
    @stack('css')

</head>
<body>

<!-- main-wrapper -->
<div class="wrapper">
    @include('admin.blocks.header')
    @include('admin.blocks.sidebar')

    <div id="content" role="main">
        <div class="container-fluid">
            <div class="row mt-5 mb-3">
                @foreach (['success', 'error', 'info'] as $status)
                    @if (session($status))
                        @foreach ((array)session($status) as $alert)
                            <div class="alert alert-dismissible fade show col-12
                                alert-{{ $status === 'error' ? 'danger' : $status }}" role="alert">
                                {{ $alert }}
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>

            @yield('content')
        </div>
    </div>

</div>
<!-- /main-wrapper -->

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
</script>

<script src="{{ mix('js/admin/custom.min.js') }}"></script>
@stack('scripts')

@if (!empty($errors) && $errors->any())
    @foreach ($errors->all() as $error)
        {{ Debugbar::error($error) }}
    @endforeach
@endif

</body>
</html>
