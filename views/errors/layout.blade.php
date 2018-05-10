<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  @hasSection('refresh')
    <meta http-equiv="refresh" content="@yield('refresh')">
  @endif
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') (@yield('error'))</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Slab:300,400,700&amp;subset=cyrillic">
  <style type="text/css">
    html {
      height: 100%;
    }

    * {
      font-family: 'Roboto Slab', serif;
    }

    h1 {
      font-weight: 700;
    }
  </style>
</head>
<body class="h-100 bg-dark text-light">

<div class="container h-100">
  <div class="row h-100">
    <div class="col my-auto text-center">

      <h1 class="mb-0">@yield('title')</h1>
      <small class="mb-3">@yield('error')</small>
      <div class="lead mt-3">@yield('content')</div>
    </div>
  </div>
</div>
</body>
</html>
