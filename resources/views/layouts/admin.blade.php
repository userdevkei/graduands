<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Graduation Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Graduation Admin</a>
        <ul class="navbar-nav me-auto">
            @foreach ([
                'admin.dashboard' => 'Dashboard',
                'admin.cohorts.index' => 'Cohorts',
                'admin.graduands.index' => 'Graduands',
                'admin.imports.create' => 'Import',
                'admin.logs.index' => 'Audit Log',
            ] as $route => $label)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs($route === 'admin.dashboard' ? $route : \Illuminate\Support\Str::beforeLast($route, '.').'.*') ? 'active' : '' }}" href="{{ route($route) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
        <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf
            <button class="btn btn-sm btn-outline-light">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid py-4">
    @include('partials.flash')
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
