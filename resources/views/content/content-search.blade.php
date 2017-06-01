<article @php(post_class(['entry', 'entry--search-item']))>
  <header>
    <h2 class="entry__title"><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></h2>
    @if (get_post_type() === 'post')
      @include('partials/entry-meta')
    @endif
  </header>
  <div class="entry__summary">
    @php(the_excerpt())
  </div>
  <div class="entry__actions">
    <a href="{{ get_permalink() }}">{{ __('Continued', 'sage') }}</a>
  </div>
</article>
