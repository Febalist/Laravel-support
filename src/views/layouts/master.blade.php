<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @stack('meta')

  <link rel="stylesheet" href="{{ css('app') }}">
  @stack('styles')

  <title>
    @hasSection('title') @yield('title') &middot; @endif
    {{ config('app.name') }}
  </title>

  <!--[if lt IE 9]>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
@yield('body')

<script>
  window.Laravel = {!! json_encode(transfer()) !!};
</script>
<script src="{{ js('app') }}"></script>
@stack('scripts')
</body>
</html>
