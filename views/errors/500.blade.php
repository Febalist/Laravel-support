@extends('support::errors.layout')

@section('refresh', '900')
@section('title', trans('support::errors.500'))
@section('error', '500 Internal Server Error')

@section('content')
  @if(app()->bound('sentry') && config('sentry.dsn') && !empty(Sentry::getLastEventID()))
    <p>
      {{ trans('support::errors.error_code') }}: <code>{{ Sentry::getLastEventID() }}</code>
    </p>
  @endif
@endsection
