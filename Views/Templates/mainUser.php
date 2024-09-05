<?php 
include_once 'Models/RegistersClinicalModel.php';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Registro clínico</title>

        <link href="<?php echo base_url; ?>Assets/css/styles.css" rel="stylesheet" />
        <link href="<?php echo base_url; ?>Assets/css/bootstrap.min.css" rel="stylesheet" />
        <script src="<?php echo base_url; ?>/Assets/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed sb-sidenav-toggled">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <img src="<?php echo base_url; ?>/Assets/css/v9_58.png" id="imglogo">
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <!-- Button trigger modal -->
 
                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalHelp" onclick="modalHelp1();">
                            ¿Necesitas Ayuda?
                        </button>



                        <div class="dropdown-divider"></div>
                    <form id="logout" method="POST" action="<?php echo base_url; ?>Users/logout">
                        <button type="submit" class="dropdown-item">Cerrar sesión</button>
                    </form>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-4">Historial Clínico</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Escriba el nombre del registro que desea buscar, puede buscar ya sea por el nombre del registro, por el tipo del registro o por la fecha</li>
                        </ol>
                        
                        <div class="container mt-5">       
                            <div class="rowSearch">
                                <form class="d-flex">
                                    <input type="date" class="form-control light-table-filter" id="dateinit" name="dateinit"  style="width: 16%">
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="date" class="form-control light-table-filter" id="datefinal" name="datefinal"  style="width: 16%">
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="text" class="form-control light-table-filter" data-table="table" id="search_table" placeholder="Buscar registros clinicos"  style="width: 50%">
                                    
                                </form>

                            </div>
                            <br>
                            <br>
                            <table  class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Registros Clínicos</th>
                                        <th>Tipo de Servicio</th>
                                        <th>Fechas</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                            
                                <?php if (!empty($data)) : ?>
                                    <?php foreach ($data as $row) : ?>
                                        <tr>
                                            <td><?php print_r($row->Nombre); ?></td> 
                                            <td><?php print_r($row->Descrip); ?></td> 
                                            <td><?php print_r($row->FechaHora); ?></td> 
                                            <td class="text-center"><button class="btn btn-primary">Ver</button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4">No se encontraron registros clínicos.</td>
                                    </tr>
                                <?php endif; ?>
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
        <script src="<?php echo base_url; ?>Assets/js/popper.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/js/scripts.js"></script>
        <script src="<?php echo base_url; ?>Assets/js/functions.js"></script>
        

    </body>

       <!-- Modal -->
       <div class="modal fade" id="modalHelp" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newModalLabel">Información de ayuda y soporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <h1>Hola a todos</h1>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="#modalHelp" onclick="modalClose();">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
      



</html>

