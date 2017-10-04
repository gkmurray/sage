<form role="search-form" method="get" class="search-form" action="{{ home_url('/') }}">
  <span class="search-form__label"><i class="fa fa-search"></i></span>
  <input
    class="search-form__field"
    type="text"
    value="{{ get_search_query() }}"
    name="s"
    id="s"
    placeholder="{{ __('Search...', 'sage') }}" />
  <div class="input-group-button">
    <input type="submit" class="search-form__submit button" value="{{ esc_attr__('Search', 'sage') }}" />
  </div>
</form>
