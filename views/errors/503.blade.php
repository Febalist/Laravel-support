@extends('support::errors.layout')

@section('refresh', '30')
@section('title', trans('support::errors.503'))
@section('error', '503 Service Unavailable')

@section('content')
  @if(isset($exception))
    <p>
      {{ $exception->getMessage() }}
    </p>
  @endif
@endsection
