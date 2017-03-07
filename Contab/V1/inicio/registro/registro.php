<?php
 $url[0] = '../';
require ('../class/pais.class.php');
$pais = new Pais();
$list_pais = $pais->get_paises();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Formulario de registro </title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
    
     <!-- Select2 -->
    <link href="<?php echo $url[0] . '../vendors/select2/dist/css/select2.min.css' ?>" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
  
        <!-- page content -->
        <div class="right_col" role="">
          <div class="">
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Registrar</h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <!-- Smart Wizard -->
                    <div id="wizard" class="form_wizard wizard_horizontal">
                      <ul class="wizard_steps">
                        <li>
                          <a href="#step-1">
                            <span class="step_no">1</span>
                            <span class="step_descr">
                                Paso 1 <br />
                                <small>Validar conexión.</small>
                            </span>
                          </a>
                        </li>
                        <li>
                          <a href="#step-2">
                            <span class="step_no">2</span>
                            <span class="step_descr">
                                Paso 2<br />
                                <small>Datos de contacto</small>
                            </span>
                          </a>
                        </li>
                        <li>
                          <a href="#step-3">
                            <span class="step_no">3</span>
                            <span class="step_descr">
                                Paso  3<br />
                                <small>Datos de empresa</small>
                            </span>
                          </a>
                        </li>
                      </ul>
                      <div id="step-1">
                        <form class="form-horizontal form-label-left"  action="../put/registro.php" id="form_step1">
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="url_step1" >Dirección dominio <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="url" required="required" class="form-control col-md-7 col-xs-12" name="url_step1" placeholder="https://www.direccion.com/">
                              <small style="color:red;"></small>
                            </div>
                            
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" >Cupón promocional <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" name="cupon_step1" class="form-control col-md-7 col-xs-12"  required="required" placeholder="AAFS3434">
                              <small style="color:red;"></small>
                            </div>
                          </div>
                         <button type="submit" style=" visibility: hidden;" class="submit">Enviar</button>
                        </form>

                      </div>
                      <div id="step-2">
                        <form class="form-horizontal form-label-left" id="form_step2">
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nombre(s) <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="nombre_step2" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" placeholder="Nombre1 Nombre2">
                            </div>
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Apellidos <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="apellidos_step2" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" placeholder="Apellidos">
                            </div>
                          </div>
                           <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Correo electrónico <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="email" required="required" class="form-control col-md-7 col-xs-12" name="correo_step2" placeholder="ejemplo@dominio.com">
                            </div>
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Teléfono <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="tel" required="required" class="form-control col-md-7 col-xs-12" name="telefono_step2" pattern="[0-9]{10}" placeholder="0000000000">
                            </div>
                          </div>
                          <button type="submit" style=" visibility: hidden;" class="submit">Enviar</button>
                        </form>
                      </div>
                      <div id="step-3">
                         <form class="form-horizontal form-label-left" id="form_step3">
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Empresa <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="empresa_step3" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" placeholder="Nombre Empresa">
                            </div>
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">País <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                             <select name="pais_step3" class="select2_single form-control col-md-7 col-xs-12" required>
                                 <?php foreach($list_pais as $pais): ?>
                                     <option value="<?php echo $pais->label; ?>"><?php echo $pais->label; ?></option>
                                 <?php endforeach ?>
                             </select>
                            </div>
                          </div>
                           <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Ciudad <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="ciudad_step3" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" placeholder="Nombre ciudad">
                            </div>
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Código Postal <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="cp_step3" pattern="[0-9]{5}" placeholder="72560">
                            </div>
                          </div>
                          <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Teléfono <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="tel" required="required" class="form-control col-md-7 col-xs-12" name="telefono_step3" pattern="[0-9]{10}" placeholder="0000000000">
                            </div>
                          </div>
                           <div class=" form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Dirección <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <textarea required="required" class="form-control col-md-7 col-xs-12" name="cp_step3" cols="50" rows="2" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" placeholder="Dirección"></textarea>
                            </div>
                          </div>
                          <button type="submit" style=" visibility: hidden;" class="submit" >Enviar</button>
                        </form>
                      </div>
                    </div>
                    <!-- End SmartWizard Content -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- jQuery Smart Wizard -->
    <script src="../vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
    

    <!-- Custom Theme Scripts -->
    
    <script src="../build/js/custom.min.js"></script>
    
    <!-- Select2 -->
    <script src="<?php echo $url[0] . '../vendors/select2/dist/js/select2.full.min.js' ?>"></script>


    
     <!-- jQuery Smart Wizard -->
    <script>
        $(document).ready(function() {
            
            $('form').submit(function() {
              return false;
            });

        $(".select2_single").select2({
            placeholder: "Seleccione una opción"
          });
        $('#wizard').smartWizard(
            {
                labelNext:'Siguiente', // label for Next button
                labelPrevious:'Atrás', // label for Previous button
                labelFinish:'Finalizar',
                onLeaveStep:leaveAStepCallback,
                onFinish:onFinishCallback,
                ajaxType: 'POST',
            }  
        );
          
        $('.buttonNext').addClass('btn btn-success');
        $('.buttonPrevious').addClass('btn btn-primary');
        $('.buttonFinish').addClass('btn btn-default');
          
        function leaveAStepCallback(obj, context){
            return validateSteps(context.fromStep); // return false to stay on step and true to continue navigation 
        }

        function onFinishCallback(objs, context){
            if(validateAllSteps()){
                var data_Paso1;
                var data_Paso2;
                var data_Paso3;
                //Registro Paso 1

                $.ajax({ 
                        url:"../put/put_registro_paso1.php",
                        method: "POST",
                        data: $("#form_step1").serialize(),
                        dataType:"JSON",
                        success: function (data1){
                            if(data1.id){
                                 $.ajax({ 
                                    url:"../put/put_registro_paso2.php",
                                    method: "POST",
                                    data:  $("#form_step2").serialize()+'&fk='+data1.id,
                                     dataType:"JSON",
                                    success: function (data2){
                                            if(data2.id){
                                                 $.ajax({ 
                                                    url:"../put/put_registro_paso3.php",
                                                    method: "POST",
                                                    data: $("#form_step3").serialize()+'&fk='+data2.id,
                                                     dataType:"JSON",
                                                    success: function (data3){
                                                        alert(data3.error); 
                                                        location.reload(); 
                                                    }
                                                });
                                            }
                                    }
                                });
                            }
                        }
                    });
            
            }
        }


        // Your Step validation logic
        function validateSteps(stepnumber){
            var isStepValid = true;
            // validate step 1
    
            if(stepnumber == 1){
                
                var $myForm =  $("#form_step1");
                if (!$("#form_step1")[0].checkValidity()) {
                    $myForm.find(':submit').click();
                    isStepValid = false;
                }
                $.ajax({ 
                    url:"../validate/validar_paso1_registro.php",
                    method: "POST",
                    data: { url_step1: $("input[name=url_step1]").val()},
                    success: function (data){
                       if(data){
                           alert(data);
                           isStepValid = false;
                       }
                    }
                });
                    
            }else{
                var $myForm =  $("#form_step2");
                if (!$("#form_step2")[0].checkValidity()) {
                    $myForm.find(':submit').click();
                    isStepValid = false;
                }
            }
           return isStepValid;  
        }
            
        function validateAllSteps(){
            var isStepValid = true;
        
            //Validar paso 1
            
            var $myForm =  $("#form_step1");
            if (!$("#form_step1")[0].checkValidity()) {
                $myForm.find(':submit').click();
                isStepValid = false;
            }
            $.ajax({ 
                url:"../validate/validar_paso1_registro.php",
                method: "POST",
                data: { url_step1: $("input[name=url_step1]").val()},
                success: function (data){
                    if(data){
                           alert(data);
                           isStepValid = false;
                       }
                }
            });
            
            //Validar paso 2
            var $myForm =  $("#form_step2");
            if (!$("#form_step2")[0].checkValidity()) {
                $myForm.find(':submit').click();
                isStepValid = false;
            }
            
            //Validar paso 3
            var $myForm =  $("#form_step3");
            if (!$("#form_step3")[0].checkValidity()) {
                $myForm.find(':submit').click();
                isStepValid = false;
            }
            return isStepValid;
        }          
      });

    </script>
  </body>
</html>