<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="">
    <div class="wrapper ">
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="logo">
                <a style="padding-left: 8px;" href="{{ url('perfil') }}" class="simple-text logo-normal">
                  <i class="fa fa-user"></i> {{ explode(" ", Auth::user()->name)[0] }}
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                <li class="active ">
                    <a href="{{ url('home') }}">
                    <i class="nc-icon nc-bank"></i>
                    <p>Início</p>
                    </a>
                </li>
                <li>
                  <a href="{{ url('certificados') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                      <p>Certificados</p>
                  </a>
                </li>
                <li>
                    <a href="{{ url('eventos') }}">
                    <i class="nc-icon nc-tag-content"></i>
                    <p>Eventos</p>
                    </a>
                </li>
                <li>
                    <a href="{{ url('estatisticas') }}">
                    <i class="nc-icon nc-chart-bar-32"></i>
                    <p>Estatísticas</p>
                    </a>
                </li>
                <li>
                    <a href="{{ url('modelos') }}">
                    <i class="fa fa-file-o"></i>
                    <p>Modelos</p>
                    </a>
                </li> 
                <li>
                    <a href="{{ url('notificacoes') }}">
                    <i class="fa fa-send"></i>
                    <p>Notificações</p>
                    </a>
                </li>                 
                <li>
                    <a href="{{ url('participantes') }}">
                    <i class="fa fa-group"></i>
                    <p>Participantes</p>
                    </a>
                </li>    
                <li>
                    <a href="{{ url('usuarios') }}">
                    <i class="nc-icon nc-circle-10"></i>
                    <p>Usuários</p>
                    </a>
                </li>  
                <li>
                    <a href="{{ url('arquivos') }}">
                    <i class="nc-icon nc-circle-10"></i>
                    <p>Arquivos</p>
                    </a>
                </li>                
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                      <i class="nc-icon nc-lock-circle-open"></i>
                      <p>Sair</p>
                    </a>
                </li>
              </ul>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
        </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="javascript:;">Sistema de Gerenciamento de Certificados</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                  <i class="fa fa-sign-out"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="row">

        @yield('content')

          
        </div>
      </div>
      <footer class="footer footer-black  footer-white ">
        <div class="container-fluid">
          <div class="row">
            
            <div class="credits ml-auto">
              <span class="copyright">
                © <script>
                  document.write(new Date().getFullYear())
                </script> - Sistema de Gerenciamento de Certificados
              </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/core/moment.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
 
  <!-- Chart JS -->
  <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
 
  <script src="{{ asset('js/paper-dashboard.min.js?v=2.0.1') }}" type="text/javascript"></script>
  <script src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-datetimepicker.js') }}"></script>
  <script src="{{ asset('js/plugins/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('demo/demo.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
  @yield('script')
  <script>
    function setFormValidation(id) {
      $(id).validate({
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
          $(element).closest('.form-check').removeClass('has-success').addClass('has-danger');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
          $(element).closest('.form-check').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement: function(error, element) {
          $(element).closest('.form-group').append(error);
        },
      });
    }

    $(document).ready(function() {
      // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
      //demo.initChartsPages();
      demo.initDateTimePicker();
      setFormValidation('#RegisterValidation');
    });
  </script>
  <script>
    $(document).ready(function() {

      $('.select2').select2();

      $(".btn-discovery").click(function(){
               alert("Teste");
            });

      $("#id_tipo_participacao_tip").change(function(){
        let valor = $('#id_tipo_participacao_tip option:selected').val(); 
        let texto = $('#id_tipo_participacao_tip option:selected').text(); 
        if(valor != 0)
          $("#tipo_participacao").html(texto);
      });

      $("#total_horas_cer").keyup(function(){
        let valor = $(this).val();

        if(valor != ''){
          if(valor > 0)
            $(".carga_horaria").css('display','block');
          else
            $(".carga_horaria").css('display','none');
          $("#carga_horaria").html(valor);
        }else{
          $(".carga_horaria").css('display','none');
        }
    
      });

      $("#ds_conteudo_cer").keyup(function(){
        let texto = $(this).val();

        if(texto != '')
          $("#conteudo").html(texto);
      });

      $("#complemento_cer").keyup(function(){
        let texto = $(this).val();

        if(texto != '')
          $("#complemento").html(texto);
      });

      $('#arquivo').on('change',function(){
        var fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);
      });
      
      $('#datatable').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: true,
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search records",
        }

      });

      var table = $('#datatable').DataTable();

      // Edit record
      table.on('click', '.edit', function() {
        $tr = $(this).closest('tr');

        var data = table.row($tr).data();
        alert('You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.');
      });

      // Delete a record
      table.on('click', '.remove', function(e) {
        $tr = $(this).closest('tr');
        table.row($tr).remove().draw();
        e.preventDefault();
      });

      //Like record
      table.on('click', '.like', function() {
        alert('You clicked on Like button');
      });
    });
  </script>
</body>

</html>