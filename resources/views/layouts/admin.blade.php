@php
    $user = auth()->user();
    $initials = collect(explode(' ', trim($user->name)))->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
    $nav = [
        'Overview' => [['admin.dashboard', 'admin.dashboard', 'bi-grid-1x2', 'Dashboard']],
        'Records' => [
            ['admin.cohorts.index', 'admin.cohorts.*', 'bi-collection', 'Cohorts'],
            ['admin.graduands.index', 'admin.graduands.*', 'bi-people', 'Graduands'],
            ['admin.imports.create', 'admin.imports.*', 'bi-cloud-arrow-up', 'Import'],
        ],
        'System' => [['admin.logs.index', 'admin.logs.*', 'bi-clock-history', 'Audit log']],
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>
    @include('partials.theme')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="scrim" onclick="document.body.classList.remove('nav-open')"></div>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <span class="brand-mark"><img src="{{ asset('images/teau-logo.png') }}" alt="TEAU crest"></span>
            <span>
                <span class="brand-name d-block">{{ config('app.name') }}</span>
                <span class="brand-sub">Graduation records</span>
            </span>
        </a>

        @foreach ($nav as $group => $links)
            <div class="nav-group">{{ $group }}</div>
            @foreach ($links as [$route, $pattern, $icon, $label])
                <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i><span>{{ $label }}</span>
                </a>
            @endforeach
        @endforeach

        <div class="side-user">
            <span class="avatar">{{ $initials }}</span>
            <div>
                <div class="nm">{{ $user->name }}</div>
                <div class="rl">{{ $user->role === 'super_admin' ? 'Super admin' : 'Registry officer' }}</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="ms-auto">@csrf
                <button type="submit" title="Sign out" aria-label="Sign out"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <button type="button" aria-label="Open menu" onclick="document.body.classList.add('nav-open')"><i class="bi bi-list"></i></button>
            <strong style="font-family:var(--font-display)">{{ config('app.name') }}</strong>
        </div>

        <div class="content">
            @include('partials.flash')

            @hasSection('header')
                @yield('header')
            @else
                <div class="page-head">
                    <div>
                        <h1 class="page-title">@yield('title')</h1>
                        @hasSection('subtitle')<p class="page-sub">@yield('subtitle')</p>@endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">@yield('actions')</div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $.fn.dataTable.ext.errMode = 'console';
    window.esc = s => $('<div>').text(s ?? '').html();
    window.initials = name => (String(name || '?').trim().split(/\s+/).filter(Boolean).map((p, i, a) => (i === 0 || i === a.length - 1) ? p[0] : '').join('') || '?').toUpperCase();
    window.dtLang = {
        search: '', searchPlaceholder: 'Search...', lengthMenu: 'Show _MENU_',
        info: 'Showing _START_ to _END_ of _TOTAL_', infoEmpty: 'Nothing to show',
        infoFiltered: '(filtered from _MAX_)', processing: 'Loading...',
        paginate: { previous: '<i class="bi bi-chevron-left"></i>', next: '<i class="bi bi-chevron-right"></i>' }
    };
    window.dtDom = "<'row align-items-center g-2 mb-3'<'col-sm-6'l><'col-sm-6'f>>t<'row align-items-center'<'col-md-5'i><'col-md-7'p>>";
</script>
@stack('scripts')
</body>
</html>
