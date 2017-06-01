@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('content.content-single-'.get_post_type())
  @endwhile
@endsection
