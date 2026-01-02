<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>{{ $title }}</title>

  <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/phosphor/bold/style.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
  @livewireStyles
</head>

<body class="zd-nav-fixed">
  <div
    id="page-loader"
    class="position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none d-flex justify-content-center align-items-center"
    style="z-index:9999"
  >
    <div class="spinner-border text-danger"></div>
  </div>

  <header>
    <nav class="zd-topnav navbar navbar-expand navbar-light">
      <div class="navbar-brand ps-3 pe-1">
        <span class="text-white fw-bold fs-6">
          TDE
        </span>
        
        <span id="sidebarToggle">
          <i class="ph-bold ph-sidebar-simple"></i>
        </span>
      </div>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ph-bold ph-user"></i>
            <span>{{ session('username') ?? 'User' }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <!-- <li><a class="dropdown-item" href="/change-password" load><i class="ph-bold ph-key"></i> Change Password</a></li> -->
            <li><livewire:auth.logout /></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <div id="layoutSidenav">
    <aside id="layoutSidenav_nav">
      <nav class="zd-sidenav accordion zd-sidenav-light" id="sidenavAccordion">
        <div class="zd-sidenav-menu">
          <div class="nav">
            <h2 class="zd-sidenav-menu-heading">Menu</h2>

            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="/home" load>
              <div class="zd-nav-link-icon"><i class="ph-bold ph-house"></i></div>
              <span>Home</span>
            </a>

            <a class="nav-link {{ request()->is('door-log*','alarm-door-log*') ? 'active' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapse1">
              <div class="zd-nav-link-icon"><i class="ph-bold ph-receipt"></i></div>
              <span>Transaction</span>
              <div class="zd-sidenav-collapse-arrow"><i class="ph-bold ph-caret-down"></i></div>
            </a>

            <div class="collapse show" id="collapse1" data-bs-parent="#sidenavAccordion">
              <nav class="zd-sidenav-menu-nested nav">
                <a class="nav-link {{ request()->routeIs('daily-monthly-report') ? 'active' : '' }}" href="/daily-monthly-report" load>
                  <i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>
                  Daily/Monthly Report
                </a>
                <a class="nav-link {{ request()->routeIs('door-report') ? 'active' : '' }}" href="/door-report" load><i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>Door Report</a>
                <a class="nav-link {{ request()->routeIs('door-alarm-report') ? 'active' : '' }}" href="/door-alarm-report" load><i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>Door Alarm Report</a>
                <a class="nav-link {{ request()->routeIs('matrix-access') ? 'active' : '' }}" href="/matrix-access" load><i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>Matrix Access</a>
                <a class="nav-link {{ request()->routeIs('list-authorize') ? 'active' : '' }}" href="/list-authorize" load><i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>List Authorize</a>
                <a class="nav-link {{ request()->routeIs('personnel-report') ? 'active' : '' }}" href="/personnel-report" load><i class="ph-bold ph-dot-outline zd-nav-sub-link-icon"></i>Personnel Report</a>
              </nav>
            </div>
          </div>
        </div>
      </nav>
    </aside>

    <main id="layoutSidenav_content">
      <section class="container-fluid px-4">
        <header class="my-4 mb-3">
          <h2 class="h6 mb-0 fw-bold">{{ $title }}</h2>
        </header>

        <div class="row">
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-body">
                {{ $slot }}
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  @livewireScripts
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>