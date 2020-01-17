<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>评审通|评审造价行业管理系统</title>
    <meta name='keywords' content='评审通,评审造价行业管理系统' />
    <meta name='description' itemprop='description' content='评审通是评审造价行业管理系统软件，为工程评审造价行业整体办公管理系统平台，主要的适用范围包括：评审中心、造价咨询公司、建设单位、审计单位等，努力打造高效的评审造价行业办公管理系统'
    />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '评审通') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')

</head>

<body>
    @include('layouts.header')
    @yield('content')
    @include('layouts.footer')
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scriptsAfterJs')
</body>

</html>