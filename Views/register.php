<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Registro</title>
        <link href="<?php echo base_url; ?>Assets/css/styles.css" rel="stylesheet" />
        <script src="<?php echo base_url; ?>Assets/js/all.min.js" crossorigin="anonymous"></script>
        <style>
        #layoutAuthentication_content {
            background-image: url('<?php echo base_url; ?>Assets/css/image.png'); 
            width: 100%;
            left: 100%;
        
        }
        .full-screen-image {
            position: relative;
            opacity: 0.7;
            left: 40%;
            width: 20%;
            height: 5%;
        }
    </style>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Registro de usuarios</h3></div>
                                    <div class="card-body">
                                        <form id="frmRegister">
                                            <div class="form-group">
                                                <label class="small mb-1" for="document"><i class="fas fa-user"></i>Numero de documento</label>
                                                <input class="form-control py-4" id="document" name="document" type="text" placeholder="Escribe tu numero de documento" />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="firstname"><i class="fas fa-user"></i>Primer apellido</label>
                                                <input class="form-control py-4" id="firstname" name="firstname" type="text" placeholder="Escribe tu primer apellido" autocomplete="family-name" />

                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="birthdate"><i class="fas fa-user"></i>Fecha de Nacimiento</label>
                                                <input class="form-control py-4" id="birthdate" name="birthdate" type="date" placeholder="Selecciona tu fecha de nacimiento" />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="password"><i class="fas fa-key"></i>Contraseña</label>
                                                <input class="form-control py-4" id="password" name="password" type="password" placeholder="Escribe tu contraseña" autocomplete="new-password" />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="confirm_password"><i class="fas fa-key"></i>Confirmar contraseña</label>
                                                <input class="form-control py-4" id="confirm_password" name="confirm_password" type="password" placeholder="Confirma tu contraseña" autocomplete="new-password" />
                                            </div>
                                            <div class="alert alert-danger text-center d-none" id="alerta" role="alert">
                                                
                                            </div>
                                            <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary" type="button" onclick="frmRegister(event);">Registrarse</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <img src="<?php echo base_url; ?>/Assets/css/v9_58.png" alt="Img" class="full-screen-image">
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Tecnologia Perfect Body (Diseñado por: Anderson Maldonado)</div>
                            <div>
                                <div class="text-muted">Derechos reservados</div>
                               
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="<?php echo base_url; ?>Assets/js/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url; ?>Assets/js/scripts.js"></script>
        <script>
            const base_url = "<?php echo base_url; ?>";
        </script>
        <script src="<?php echo base_url; ?>/Assets/js/functions.js"></script>
    </body>
</html>
