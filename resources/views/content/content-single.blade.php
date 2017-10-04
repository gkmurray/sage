<article @php(post_class('entry'))>
  <header>
    <h1 class="entry__title">{{ get_the_title() }}</h1>
    @include('partials/entry-meta')
  </header>
  <div class="entry__content">
    @php(the_content())
  </div>
  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
  @php(comments_template('/partials/comments.blade.php'))
</article>
