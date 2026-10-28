<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Graduation Details Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 760px;">
    <div class="text-center mb-4">
        <h1 class="h3 mb-0">{{ config('app.name') }}</h1>
        <div class="text-muted">Graduation Details Confirmation</div>
    </div>

    @include('partials.flash')
    @yield('content')
</div>
</body>
</html>
