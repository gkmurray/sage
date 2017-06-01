@if($pagination != false)
  <nav class="page-navigation">
    <ul class="pagination">

      @if($pagination['start_page'] >= 2 && $pagination['pages_to_show'] < $pagination['max_page'])
        <li>
          <a href="{{ get_pagenum_link() }}" title="{{ $pagination['first_text'] }}">
            {{ $pagination['first_text'] }}
          </a>
        </li>
      @endif

      <li>@php(previous_posts_link(__('Previous', 'sage')))</li>

      @for($i = $pagination['start_page']; $i <= $pagination['end_page']; $i++)
        @if($i == $pagination['paged'])
          <li class="current">{{ $i }}</li>
        @else
          <li><a href="{{ get_pagenum_link($i) }}">{{ $i }}</a></li>
        @endif
      @endfor

      <li>@php(next_posts_link(__('Next', 'sage'), 0))</li>

      @if($pagination['end_page'] < $pagination['max_page'])
        <li>
          <a href="{{ get_pagenum_link($max_page) }}" title="{{ $pagination['last_text'] }}">
            {{ $pagination['last_text'] }}
          </a>
        </li>
      @endif

    </ul>
  </nav>
@endif
