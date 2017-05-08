<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

require_once "../class/cuentas_mayor.class.php";

$mayor = new CuentasMayor();
if(isset($_REQUEST['inicio'])){
    $periodo[0] = $_REQUEST['inicio'];
    $periodo[1] = $_REQUEST['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}

require_once $url[0]."class/admin.class.php";
$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
$ctas=$mayor->get_mayor($periodo);

header("Content-type: application/ms-excel");
header("Content-disposition: attachment; filename=libro_mayor".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".xls");
?>
<!DOCTYPE html>
<html lang="es">


	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Libro Mayor</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Correspondiente <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
								<thead>
									<tr style="background-color:#7A8196; color:#FFFFFF">
										<th width="10%" >Cuenta</th>
										<th width="15%" >Nombre</th>
										<th style="text-align:right" width="12.5%"></th>
										<th style="text-align:right" width="12.5%"></th>
										<th style="text-align:right" width="12.5%">Saldo Inicial</th>
										<th style="text-align:center" width="12.5%" colspan="2">Acumulados</th>
									
										
									</tr>
									<tr style="background-color:#e8e9ed ; color:#000000">
										<th width="10%" >Fecha</th>
										<th width="15%" >Periodo</th>
										<th style="text-align:right"  width="12.5%">Cargos</th>
										<th style="text-align:right"  width="12.5%">Abonos</th>
										<th style="text-align:right"  width="12.5%">Saldo</th>
										<th style="text-align:right"  width="12.5%">Cargos</th>
										<th style="text-align:right"  width="12.5%">Abonos</th>
									</tr>
								</thead>
		
							</table>
							<table class="table table-striped table-bordered" width="100%" cellspacing="1" border="1">
								<tbody>
									<?php
									foreach ($ctas as $cta){
										$ini      =$mayor->get_saldo_ini($periodo,$cta['cta']);
										@$idebe   =$ini->debe;
										@$ihaber  =$ini->haber;
										$totini   =0;
										$totdebe  =0;
										$totdebe_acumulado  =0;
										$tothaber_acumulado  =0;
										$tothaber =0;
										$totact   =0;
										if($cta['natur']=='D'){
											$inicial =$idebe-$ihaber;
											$sact    =$inicial+$cta['debe']-$cta['haber'];
										}else{
											$inicial =$ihaber-$idebe;
											$sact    =$inicial+$cta['haber']-$cta['debe'];
										}

										if($cta['debe']!=0 || $cta['haber']!=0 || $inicial !=0){
											$totini   +=$inicial;
											$totdebe  +=$cta['debe'];
											$tothaber +=$cta['haber'];
											$totact   +=$sact;
										?>
										<tr>
											<td width="10%"><strong><?=$cta['cta']?></strong></td>
											<td width="15%"><strong><?=$cta['descta']?></strong></td>
											<td align="right" width="12.5%"></td>
											<td align="right" width="12.5%"></td>
											<td align="right" width="12.5%"><strong><?=$moneda." ".number_format($inicial,2)?></strong></td>
											<td align="right" width="12.5%"></td>
											<th style="text-align:right" width="12.5%"></th>
										</tr>
										<?php
											$pol        =$mayor->get_pol_mayor($periodo, $cta['cta']);
											
											$totalhaber =0;
											$totaldebe  =0;

                                            if($pol>0){
                                                foreach ($pol as $pl){
													$tipo       = ($pl['tipo_pol']=='I') ? 'Ingreso': 'N/A' ;
													$tipo       = ($pl['tipo_pol']=='E') ? 'Egreso': 'N/A' ;
													$tipo       = ($pl['tipo_pol']=='D') ? 'Diario': 'N/A' ;
													$totaldebe  +=$pl['debe'];
													$totalhaber +=$pl['haber'];
													$now        = new DateTime($pl['fecha']);
												}
												$totdebe_acumulado  +=$totaldebe;
												$tothaber_acumulado +=$totalhaber;
												$tothaber           +=$totalhaber;
												$inicial            =$inicial+$totaldebe-$totalhaber;
											?>
												<tr>
													<td width="10%"><?=$now->format('d-m-Y')?></td>
													<td width="15%"><?=@$arrayMeses[((int)$now->format('m'))-1]?></td>
														<?php if ($pl['debe']>0): ?>
															<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totaldebe,2)?></td>
														<?php else: ?>
															<td width="12.5%" style="text-align:right">$ 0.00</td>
														<?php endif ?>
														<?php if ($pl['haber']>0): ?>
															<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totalhaber,2)?></td>
														<?php else: ?>
															<td width="12.5%" style="text-align:right">$ 0.00</td>
														<?php endif ?>
													<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($inicial,2)?></td>
													<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totdebe,2)?></td>
													<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($tothaber,2)?></td>
												</tr>		
                                            <?php 
                                        	}
											?> 
											<!--<tr>
												<td width="10%" style="text-align:right"><strong>Total:</strong></td>
												<td width="15%" style="text-align:right"><strong><?php //$moneda." ".number_format($totaldebe,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong>Total:</strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
											</tr>-->
											<tr >
												<td style="text-align:right">
													<br>
												</td>
												<td width="15%" style="text-align:right"></td>
												<td width="12.5%" style="text-align:right"></td>
												<td width="12.5%" style="text-align:right"></td>
												<td width="12.5%" style="text-align:right"></td>
												<td width="12.5%" style="text-align:right"></td>
												<td width="12.5%" style="text-align:right"></td>
											</tr>
											<?php
											}
									}
									?>
								</tbody>
							</table>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
