<?php
    $url[0]='';
    $ingresar =true;
    require $url[0]."login/login.php";
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

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form id="form">
              <h1>Inicio de sesión</h1>
              <div>
                <input type="text" class="form-control" placeholder="Usuario" required="" name="txt_usuario"/>
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Contraseña" required="" name="txt_contra"/>
              </div>
              <div>
                  <input type="submit" class="btn btn-default" value="Entrar">
                  <a class="reset_pass" href="#">¿Olvidaste tu contraseña?</a>
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

    <script>

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
