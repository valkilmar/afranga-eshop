<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Untitled')</title>

    @vite('resources/sass/app.scss')

</head>

<body>

            <section class="content">
                @yield('content')
            </section>

    @vite('resources/js/app.js')
</body>

</html>