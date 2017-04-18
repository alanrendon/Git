<?php
if ( !isset($url) || !is_array($url) ) {
$url[0] = "";
$url[1] = "configuracion/";
$url[2] = "asignacion/";
$url[3] = "cuentas/";
$url[4] = "polizas/";
$url[5] = "conf/";
$url[6] = "periodos/";
$url[7] = "reportes/";
}

require_once $url[0]."class/admin.class.php";
require_once $url[0]."login/login.php";
require_once $url[0]."class/periodo.class.php";
require_once ($url[0]."class/multidivisa.class.php");


$periodo_nav  = new Periodo();
$divisa       = new Divisa();
$eDolibarr    = new admin();
$user_empresa = new Usuario();

$periodo_nav    =$periodo_nav->get_ultimo_periodo_abierto();
$valores        = $eDolibarr->get_user();
$moneda         = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
$empresa_entity = $user_empresa->get_multiempresa(ENTITY);


?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Módulo Contabilidad | Auribox Consulting</title>

    <!-- Bootstrap -->
    <link href="<?php echo $url[0].'../vendors/bootstrap/dist/css/bootstrap.min.css'?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo $url[0].'../vendors/font-awesome/css/font-awesome.min.css'?>" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?php echo $url[0].'../vendors/iCheck/skins/flat/green.css'?>" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="<?php echo $url[0].'../vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css'?>" rel="stylesheet">
    <!-- jVectorMap -->
    <link href="<?php echo $url[0].'css/maps/jquery-jvectormap-2.0.3.css'?>" rel="stylesheet"/>
    <!-- Custom Theme Style -->
    <link href="<?php echo $url[0].'../build/css/custom.css'?>" rel="stylesheet">
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="<?php echo $url[0].'index.php'?>" class="site_title"><i class="fa  fa-cube"></i> <span>Contab PRO 1.0</span></a>
                    </div>
                    <div class="clearfix"></div>
                    <!-- menu profile quick info -->

                    <!-- /menu profile quick info -->
                    <br />
                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <h3>General</h3>
                            <ul class="nav side-menu">
                                <li><a href="<?php echo $url[0].'index.php'?>"><i class="fa fa-home"></i> Inicio</a></li>
                                <li><a><i class="fa fa-cog"></i> Configuración<span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <!-- <li><a href="<?php //echo $url[5].'agrupacion_balance_test.php'?>">Condición de Pago</a></li> -->
                                        <li><a href="<?php echo $url[5].'agrupacion_balance.php'?>">Agrupación del Balance General</a></li>
                                        <li><a href="<?php echo $url[5].'agrupacion_resultados.php'?>">Agrupación del Estado de Resultados</a></li>
                                        <li><a href="<?php echo $url[5].'carga_saldos.php'?>">Saldos iniciales</a></li>
                                        <li><a href="<?php echo $url[5].'agrupacion_ctas.php'?>">Agrupación cuentas</a></li>
                                        <li><a href="<?php echo $url[5].'prearmado_polizas.php'?>">Pre-Armado de pólizas</a></li>
                                        <li><a href="<?php echo $url[5].'registro_impuestos.php'?>">Registro impuestos</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo $url[6].'consulta.php'?>"><i class="fa fa-calendar"></i> Periodos</a>
                                </li>
                                <li><a><i class="fa fa-puzzle-piece"></i> Asignación<span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo $url[2].'clientes.php'?>">Clientes</a></li>
                                        <li><a href="<?php echo $url[2].'ntacredito.php'?>">Notas de crédito</a></li>
                                        <li><a href="<?php echo $url[2].'proveedores.php'?>">Proveedores</a></li>
                                        <li><a href="<?php echo $url[2].'societe_operation.php'?>">Asignación Proveedores DIOT</a></li>
                                        <li><a href="<?php echo $url[2].'productos.php'?>">Productos</a></li>
                                        <li><a href="<?php echo $url[2].'servicios.php'?>">Servicios</a></li>
                                        <li><a href="<?php echo $url[2].'cuentas_bancarias.php'?>">Cuentas bancarias</a></li>
                                        <li><a href="<?php echo $url[2].'almacenes.php'?>">Almacenes</a></li>
                                        <li><a href="<?php echo $url[2].'impuestos.php'?>">Impuestos</a></li>
                                        <li><a href="<?php echo $url[2].'movimientos_stock.php'?>">Movimientos de Stock</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-calculator"></i> Cuentas<span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo $url[3].'registrar.php'?>">Registrar</a></li>
                                        <li><a href="<?php echo $url[3].'consulta.php'?>">Consultar</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-briefcase"></i> Pólizas  <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo $url[4].'registrar.php'?>">Registrar</a></li>
                                        <li><a href="<?php echo $url[4].'consulta.php'?>">Consultar</a></li>
                                        <li><a href="<?php echo $url[4].'fact_clie_pendientes.php'?>">Fact. Clie. Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'fact_prov_pendientes.php'?>">Fact. Prov. Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'ant_prov_pendientes.php'?>">Ant. Prov. Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'transfert_pendientes.php'?>">Movimientos Bancarios Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'commande_pedido.php'?>">Pedidos a proveedor</a></li>
                                        <?php if ($divisa->check_if_active()): ?> 
                                            <li><a href="<?php echo $url[4].'perdida_ganancia.php'?>">Pérdida/Ganancia</a></li> 
                                        <?php endif ?> 
                                        
                                        <li><a href="<?php echo $url[4].'stock_pendientes.php'?>">Movimientos de Stock Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'gastos_pendientes.php'?>">Gastos Pendientes</a></li>
                                        <li><a href="<?php echo $url[4].'template.php'?>">Plantillas</a></li>
                                        
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-file-text-o"></i> Reportes  <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo $url[7].'anexos_catalogo.php'?>">Anexos de catálogo</a></li>
                                        <li><a href="<?php echo $url[7].'balance_general.php'?>">Balance General</a></li>
                                        <li><a href="<?php echo $url[7].'estado_resultados.php'?>">Estado de Resultados</a></li>
                                        <li><a href="<?php echo $url[7].'balance_comprobacion.php'?>">Balanza de Comprobación</a></li>
                                        <li><a href="<?php echo $url[7].'libro_diario.php'?>">Libro Diario</a></li>
                                        <li><a href="<?php echo $url[7].'cuentas_mayor.php'?>">Libro Mayor</a></li>
                                        <li><a href="<?php echo $url[7].'auxiliar.php'?>">Auxiliar de cuentas</a></li>
                                        <li><a href="<?php echo $url[7].'consulta_diot.php'?>">DIOT</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="sidebar-footer hidden-small"></div>
                </div>
            </div>
            <div class="top_nav">
                <div class="nav_menu">
                    <nav class="" role="navigation">
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li>

                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <?php  isset($valores['MAIN_INFO_SOCIETE_NOM']) ? print $valores['MAIN_INFO_SOCIETE_NOM']: print 'Sin nombre'; ?>
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <?php if($empresa_entity):?>
                                    <li><a href="<?php echo $url[0].'multiempresa.php'; ?>"><i class="fa fa-building pull-right"></i> Cambiar Entidad</a></li>
                                    <?php endif ?>
                                    <li><a href="<?php echo $url[0].'login/login.php?salir=1'; ?>"><i class="fa fa-sign-out pull-right"></i> Salir</a></li>
                                </ul>
                            </li>
                            <li >
                                <a>
                                    <?php if(isset($periodo_nav->mes_name) || $periodo_nav->mes_name != ''):?>
                                    <span style="color:#2A3F54">Periodo: <?php echo $periodo_nav->mes_name.' de '.$periodo_nav->anio; ?></span>
                                    <?php else:?>
                                    <span style="color:#FF0000">No hay periodo abierto</span>
                                    <?php endif?>
                                </a>

                            </li>
                            <li>
                                <a href="<?php echo $url[0].'multiempresa.php'; ?>">
                                    <?php if($empresa_entity):?>
                                    <span style="color:#2A3F54">Entidad: <?php echo $empresa_entity->label; ?></span>
                                    <?php endif?>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
