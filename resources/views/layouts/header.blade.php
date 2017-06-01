<header class="header">
    <a class="brand" href="{{ home_url('/') }}">{{ $site_name }}</a>
    <nav class="menu">
      @if (has_nav_menu('menu'))
        {!! App\menu() !!}
      @endif
    </nav>
</header>
