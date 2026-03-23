<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — PHILCST CCS OJT System</title>
    <meta name="description" content="Login to PHILCST CCS On-the-Job Training Document Submission, Monitoring, and Record Management System">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/philcst_logo.png') }}" type="image/png">
</head>
<body>
    @yield('content')
</body>
</html>
