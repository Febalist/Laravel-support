<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @hasSection('refresh')
    <meta http-equiv="refresh" content="@yield('refresh')">
  @endif

  <title>@yield('title')</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300">

  <style>
    html, body {
      background-color: #fff;
      color: #636b6f;
      font-family: 'Roboto', sans-serif;
      font-weight: 300;
      height: 100vh;
      margin: 0;
    }

    .center {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      height: 90vh;
      text-align: center;
    }

    .content {
      font-size: 2rem;
    }

    .title {
      font-size: 4rem;
      font-weight: 100;
    }
  </style>
</head>
<body>
<div class="center">
  <div class="content">
    <div class="title">@yield('title')</div>
    @yield('content')
  </div>
</div>
</body>
</html>
