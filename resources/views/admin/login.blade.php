<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <meta name="robots" content="noindex"/>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
          integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ mix('css/admin/login.min.css') }}"/>

    <!-- ReCaptcha -->
    {{--<script src='https://www.google.com/recaptcha/api.js?render=6LcFWYEUAAAAALnp53TQ2x2oUwOHMYHt4Y9p_VUm'></script>--}}
</head>
<body class="h-100">
<div class="container h-100">
    <div class="row h-100 align-items-center">
        <div class="col-sm-12 col-md-5  mx-auto">
            <h3 class="text-center mb-3 header">{{ config('app.name') }}</h3>
            <div class="card card-block fadeIn animated">
                <div class="p-3">
                    <div class="form-side">
                        <form action="{{ route(R_ADMIN_LOGIN_SUBMIT) }}" method="post" class="form-horizontal mt-2"
                              id="auth-form">
                            @csrf
                            {{--<input type="hidden" name="g-recaptcha-response" class="g-token" value="">--}}
                            <div class="form-group row">
                                <div class="col-12">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email" autofocus
                                           placeholder="Email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password" placeholder="Password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group text-center row">
                                <div class="col-12">
                                    <button class="btn btn-info btn-block" type="submit" name="submit" value="1">Sign
                                        in
                                    </button>
                                </div>
                            </div>

                            {{-- Message Alert --}}
                            @if(session('message'))
                                <div class="alert alert-{{ session('alert_type') }} alert-dismissible fade show"
                                     role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

{{-- TODO: Captcha--}}

{{--<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6LcFWYEUAAAAALnp53TQ2x2oUwOHMYHt4Y9p_VUm', {action: 'admin_login'})
            .then(function(token) {
                $('.g-token').val(token);
            });
    });
</script>--}}
</body>
</html>
