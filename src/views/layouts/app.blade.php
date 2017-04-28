@extends('support::layouts.master')

@section('body')
  @includeIf('layouts.header')
  <div class="container">
    @yield('content')
  </div>
  @includeIf('layouts.footer')
@endsection
