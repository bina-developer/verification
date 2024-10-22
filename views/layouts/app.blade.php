<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My App')</title>

</head>
<body>

    <!-- بخش محتوای متغیر -->
    <div class="container">
        @yield('content')
    </div>
</body>
</html>