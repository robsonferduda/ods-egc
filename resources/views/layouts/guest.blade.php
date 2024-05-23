<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="base-url" content="{{ env('BASE_URL') }}">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>{{ config('app.name', 'Laravel') }}</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('demo/demo.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/jquery.loader.min.css') }}" rel="stylesheet" />
</head>

<body>
    
  <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
    <div class="container">
      <div class="navbar-wrapper">
        <div class="navbar-toggle">
          <button type="button" class="navbar-toggler">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
          </button>
        </div>
        <a class="navbar-brand" href="{{ url('/') }}">
          <span style="text-transform: none;"> 
            <img style="width: 30px; margin-right: 8px;" src="{{ asset('img/ods-icone/ods.png') }}" class="img-fluid" alt="Responsive image">PERFIL ODS
          </span>
        </a>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-start" id="navigation">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link">
            <i class="fa fa-home mr-2"></i> Início
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('dashboard') }}" class="nav-link">
            <i class="fa fa-pie-chart mr-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('colaborar') }}" class="nav-link">
            <i class="fa fa-users mr-2"></i> COLABORAR
            </a>
          </li>
          <!--
          <li class="nav-item">
            <a href="{{ url('classificar') }}" class="nav-link">
            <i class="fa fa-tags mr-2"></i> Classificar
            </a>
          </li>    
        -->     
          <li class="nav-item ">
            <a href="{{ url('descobrir') }}" class="nav-link">
              <i class="fa fa-files-o"></i> DESCOBRIR
            </a>
          </li>
          
          <li class="nav-item ">
            <a href="{{ url('login') }}" class="nav-link">
              <i class="fa fa-lock"></i> Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->
  <div class="wrapper wrapper-full-page ">
    <div class="full-page">
      <div class="content" style="padding-top: 10vh !important; padding-bottom: 0px !important;">
        <div class="container">


         
          @yield('content')


        </div>
      </div>
      <footer class="footer footer-black footer-white">
        <div class="container-fluid">
          
        </div>
      </footer>
      
    </div>
  </div>



  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
 
  <!-- Chart JS -->
  <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('js/paper-dashboard.min.js?v=2.0.1') }}" type="text/javascript"></script><!-- Paper Dashboard DEMO methods, don't include it in your project! -->
  <script src="{{ asset('js/plugins/jquery.loader.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.min.js"></script>
  @yield('script')
  <script>
    $(document).ready(function() {

      

     
    });
  </script>
</body>
</html>