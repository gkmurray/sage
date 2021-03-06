<!doctype html>
<html @php(language_attributes())>
  @include('partials.head')
  <body @php(body_class())>
    @php(do_action('get_header'))
    @include('layouts.header')
    <div class="wrap container" role="document">
      <div class="content">
        <main class="main column small-12 {{ App\display_sidebar() ? 'large-8' : '' }}">
          @yield('content')
        </main>
        @if(App\display_sidebar())
          <aside class="sidebar column large-4 show-for-large">
            @include('partials.sidebar-'.((get_post_type() !== 'page') ? get_post_type() : get_post()->post_name))
          </aside>
        @endif
      </div>
    </div>
    @php(do_action('get_footer'))
    @include('layouts.footer')
    @php(wp_footer())
  </body>
</html>
