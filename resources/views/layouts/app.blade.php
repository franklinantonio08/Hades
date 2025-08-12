<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Option 1: CoreUI for Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@4.2.0/dist/css/coreui.min.css" rel="stylesheet" integrity="sha384-UkVD+zxJKGsZP3s/JuRzapi4dQrDDuEf/kHphzg8P3v8wuQ6m9RLjTkPGeFcglQU" crossorigin="anonymous">

    <!-- Option 2: CoreUI PRO for Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@4.3.4/dist/css/coreui.min.css" rel="stylesheet" integrity="sha384-B25jn3HrWNnbfszQBjQT5iHKf8BuG+Og9Al4zXNJgLl6orefC7UQYjD/Uxo1jMis" crossorigin="anonymous"> -->

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="css/vendors/simplebar.css">

    <link href="css/style.css" rel="stylesheet">

    <link href="css/examples.css" rel="stylesheet">
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
              'gtm.start': new Date().getTime(),
              event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
              j = d.createElement(s),
              dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
              'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
          })(window, document, 'script', 'dataLayer', 'GTM-KX4JH47');
    </script>
    <link href="{{ asset('vendors/@coreui/chartjs/css/coreui-chartjs.css') }}" rel="stylesheet">
</head>

<body>

    @include('includes.navadmin') @include('includes.headermenuadmin')

    <div class="body flex-grow-1 px-3">
        <div class="container-lg">
            <div class="fs-2 fw-semibold">Dashboard</div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item">
                        <span>Home</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <span>Dashboard</span>
                    </li>
                </ol>
            </nav>


            <div class="row">
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col">
                                            <div class="card-title fs-4 fw-semibold">Total Migrantes</div>
                                            <div class="card-subtitle text-disabled">{{ $primeraSolicitud }} - {{ $ultimaSolicitud }} {{ $year }}</div>
                                        </div>
                                        <div class="col text-end text-primary fs-4 fw-semibold">{{ $totalMigrantes }}</div>
                                    </div>
                                </div>
                                <div class="chart-wrapper mt-3" style="height:150px;">
                                    <canvas class="chart" id="card-chart-new1" height="75"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between">
                                      <div class="card-title text-disabled">Masculino</div>
                                      <div class="bg-primary bg-opacity-25 text-primary p-2 rounded">
                                          <svg class="icon icon-xl">
                                              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                                          </svg>
                                      </div>
                                  </div>
                                  <div class="fs-4 fw-semibold pb-3" id="totalMasculino">0</div>
                                  <small class="text-primary" id="porcentajeMasculino">(-0% 
                                    {{-- <svg class="icon"> 
                                      <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-arrow-bottom">
                                      </use> 
                                    </svg> --}}
                                    )
                                  </small>
                              </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between">
                                      <div class="card-title text-disabled">Femenino</div>
                                      <div class="bg-primary bg-opacity-25 text-primary p-2 rounded">
                                          <svg class="icon icon-xl">
                                              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user-female"></use>
                                          </svg>
                                      </div>
                                  </div>
                                  <div class="fs-4 fw-semibold pb-3" id="totalFemenino">0</div>
                                  <small class="text-primary" id="porcentajeFemenino">(0% 
                                    {{-- <svg class="icon"> 
                                      <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-arrow-top">
                                      </use> 
                                    </svg> --}}
                                    ) 
                                  </small>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <div class="card-title fs-4 fw-semibold">Trafico Mensual</div>
                            <div class="card-subtitle text-disabled">{{ $primeraSolicitud }} - {{ $ultimaSolicitud }} {{ $year }}</div>
                            <div class="chart-wrapper" style="height:300px;margin-top:40px;">
                                <canvas class="chart" id="main-bar-chart"  width="400"  height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            


            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <div class="card-title fs-4 fw-semibold">Trafico Semanal</div>
                            <div class="card-subtitle text-disabled border-bottom mb-3 pb-4">Ultima Semana</div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="border-start border-start-4 border-start-info px-3 mb-3"><small class="text-disabled">Total Año Actual</small>
                                              <div class="fs-5 fw-semibold" id="totalYearly">0</div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="border-start border-start-4 border-start-danger px-3 mb-3"><small class="text-disabled">Total Mes Actual</small>
                                              <div class="fs-5 fw-semibold" id="totalMonthly">0</div>
                                            </div>
                                        </div>

                                    </div>

                                      <div id="migrantes-semanal-weekly"></div>
                                  

                                </div>

                               

                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="border-start border-start-4 border-start-warning px-3 mb-3"><small class="text-disabled">Total Ultima Semana</small>
                                              <div class="fs-5 fw-semibold" id="totalLastWeek">0</div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="border-start border-start-4 border-start-success px-3 mb-3"><small class="text-disabled">Total Semana Actual</small>
                                              <div class="fs-5 fw-semibold" id="totalCurrentWeek">0</div>
                                            </div>
                                        </div>

                                    </div>



                                   
                                  <div class="card-body" id="migrantes-edad">
                                    <!-- Aquí se insertarán los datos de los grupos de edad -->
                                </div>
                                  
                                
                                </div>


                                

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <footer class="footer">
        <div><a href="https://www.migracion.gob.pa/">Servicio Nacional de Migración </a> © 2024 Todos los Derechos Reservados</div>
        {{--
        <div class="ms-auto">Powered by&nbsp;<a href="https://coreui.io/docs/">CoreUI PRO UI Components</a></div> --}}
    </footer>


    <script src="vendors/@coreui/coreui-pro/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>
    <script>
        if (document.body.classList.contains('dark-theme')) {
                var element = document.getElementById('btn-dark-theme');
                if (typeof(element) != 'undefined' && element != null) {
                  document.getElementById('btn-dark-theme').checked = true;
                }
              } else {
                var element = document.getElementById('btn-light-theme');
                if (typeof(element) != 'undefined' && element != null) {
                  document.getElementById('btn-light-theme').checked = true;
                }
              }
        
              function handleThemeChange(src) {
                var event = document.createEvent('Event');
                event.initEvent('themeChange', true, true);
        
                if (src.value === 'light') {
                  document.body.classList.remove('dark-theme');
                }
                if (src.value === 'dark') {
                  document.body.classList.add('dark-theme');
                }
                document.body.dispatchEvent(event);
              }
    </script>

    <script src="{{ asset('vendors/chart.js/js/chart.min.js') }}"></script>
    <script src="{{ asset('vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>
    <script src="{{ asset('vendors/@coreui/utils/js/coreui-utils.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
    </script>

    
</body>

</html>

