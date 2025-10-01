<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <title>{{ $PAGE_TITLE ?? '' }} | {{ env('APP_NAME') }}</title>

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />

        @yield('CORE_HEADER_CUSTOM_CSS')
        <link rel='stylesheet' href='{{ url('/') }}/base-reset.css' type='text/css' media='all' />
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="{{ url('/') }}/template/css/sb-admin-2.min.css" rel="stylesheet">
        <link href="{{ url('/') }}/components/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- ========== -->

        <!-- Custom styles for this template-->
        <link href="{{ url('/') }}/template/custom.css" rel="stylesheet" />
    </head>

    <body id="page-top">
        @yield('CORE_BODY_CONTENT')

        <!-- Bootstrap core JavaScript-->
        <script src="{{ url('/') }}/components/jquery/jquery-3.7.1.min.js"></script>
        <script src="{{ url('/') }}/components/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        @yield('CORE_FOOTER_CUSTOM_JS')
        <script src="{{ url('/') }}/template/custom.js"></script>
    </body>
</html>
