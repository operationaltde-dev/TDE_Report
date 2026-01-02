<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="TDE VMS">

  <title>{{ $title }}</title>

  <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/phosphor/bold/style.css') }}" rel="stylesheet" />
  @livewireStyles
</head>

<body class="bg-light bg-login">
  <div
    id="page-loader"
    class="position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none d-flex justify-content-center align-items-center"
    style="z-index:9999"
  >
    <div class="spinner-border text-danger"></div>
  </div>
  {{ $slot }}
  @livewireScripts
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>