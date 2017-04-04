<?php
    $url[0]='';
    require $url[0]."login/login.php";
    $user_empresa = new Usuario();
    $multiempresa = $user_empresa->multiempresa();
    if(!$multiempresa)
        header('Location: index.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Módulo Contabilidad | Auribox Consulting</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://colorlib.com/polygon/gentelella/css/animate.min.css" rel="stylesheet">
    
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
          <div class="login_wrapper">
            <div class="animate form login_form">
              <section class="login_content">
                <form method="POST" action="login/multiempresa.php">
                  <h1>Multiempresa</h1>
                   <div class="row">
                      <div class="col-sm-12">
                           <select name="empresa" class="select2_single col-sm-12">
                                 <?php foreach($multiempresa as $m ):?>
                                    <option value="<?php  echo $m->rowid ?>"> <?php  echo $m->label ?></option>
                                <?php endforeach?>
                           </select>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                    <input type="submit" class="btn btn-default" value="Seleccionar">
                  </div>

                  <div class="clearfix"></div>
                  <div class="separator">
                    <div class="clearfix"></div>
                    <br />
                    <div>
                      <h1><i class="fa fa-cube"></i> Contab PRO 1.0</h1>
                    </div>
                  </div>
                </form>
              </section>
            </div>
          </div>
        </div>

            <!-- jQuery -->
        <script src="<?php echo'vendors/jquery/dist/jquery.min.js'?>"></script>
        <!-- Bootstrap -->
        <script src="<?php echo 'vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
        <!-- FastClick -->
        <script src="<?php echo 'vendors/fastclick/lib/fastclick.js'?>"></script>

        <script src="<?php echo 'vendors/iCheck/icheck.min.js'?>"></script>
    
        <!-- Select2 -->
        <script src="vendors/select2/dist/js/select2.full.min.js"></script>
        <script>
            $(".select2_single").select2({
                placeholder: "Seleccione una opción"
              });
            $('#form').submit(ingresarSistema);

            function ingresarSistema() {
                $.ajax({
                    url: 'login/login.php', //Url a donde enviaremos los datos
                    type: 'POST', // Tipo de envio
                    dataType: 'json', //Tipo de Respuesta
                    data: $('#form').serialize(), //Serializamos el formulario
                    success: function (data)
                    {
                        if (data.url) {
                            window.location.replace(data.url);
                        } else if (data.mensaje) {
                            alert(data.mensaje);
                        } else {
                            alert(data.err);
                        }
                        ;
                    }
                });
                return false;
            }
     </script>
  </body>
</html>
