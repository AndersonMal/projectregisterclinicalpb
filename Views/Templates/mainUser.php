<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Registro clínico</title>
        <link href="<?php echo base_url; ?>/Assets/css/styles.css" rel="stylesheet" />
        <script src="<?php echo base_url; ?>Assets/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <img src="<?php echo base_url; ?>/Assets/css/v9_58.png" id="imglogo">
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">Ayuda</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="login.html">Cerrar sesión</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                           
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Notificaciones
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Sesion iniciada por:</div>
                        Administrador
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-4">Historial Clínico</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Escriba el nombre del registro que desea buscar, puede buscar ya sea por el nombre del registro, por el tipo del registro o por la fecha</li>
                        </ol>
                        
                        <div class="container mt-5">                         
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Registros Clínicos</th>
                                        <th>Tipo de Registro</th>
                                        <th>Fechas</th>
                                        <th>Ver</th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control" placeholder="Buscar por Registros Clínicos"></td>
                                        <td><input type="text" class="form-control" placeholder="Buscar por Tipo de Registro"></td>
                                        <td><input type="date" class="form-control" placeholder="Buscar por Fechas"></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Registro 1</td>
                                        <td>Tipo A</td>
                                        <td>2024-01-01</td>
                                        <td><button class="btn btn-primary">Ver</button></td>
                                    </tr>
                                    <tr>
                                        <td>Registro 2</td>
                                        <td>Tipo B</td>
                                        <td>2024-02-15</td>
                                        <td><button class="btn btn-primary">Ver</button></td>
                                    </tr>
                                    <!-- Más filas de datos -->
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-secondary">Anterior</button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button class="btn btn-secondary">Siguiente</button>
                                </div>
                            </div>

                        </div>



                    </div>
                </main>
                




<footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2020</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="<?php echo base_url; ?>Assets/js/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/js/scripts.js"></script>
        <script src="<?php echo base_url; ?>Assets/js/Chart.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/demo/chart-area-demo.js"></script>
        <script src="<?php echo base_url; ?>Assets/demo/chart-bar-demo.js"></script>
        <script src="<?php echo base_url; ?>Assets/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    </body>
</html>

