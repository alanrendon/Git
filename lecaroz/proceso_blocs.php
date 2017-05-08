<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);


if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	die();
}
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$sql="select distinct(idcia) from bloc where idcia < 101 order by idcia";
$cias=ejecutar_script($sql,$dsn);

for($z=0;$z<count($cias);$z++){

	$sql="select * from bloc where idcia = ".$cias[$z]['idcia']." order by let_folio,folio_inicio";
	$blocs=ejecutar_script($sql,$dsn);
	
	for($y=0;$y<count($blocs);$y++){
	
		$sql="UPDATE bloc SET folios_usados=0, estado=false WHERE id=".$blocs[$y]['id'];
		ejecutar_script($sql,$dsn);
		
		$contador=$blocs[$y]['num_folios'];
		$folio=$blocs[$y]['folio_inicio'];
		$letra=$blocs[$y]['let_folio'];
		
		for($i=0;$i<$contador;$i++)
		{
//			$tpl->newBlock("rows");
			$resta=0;
			echo "let_folio".$letra."<br>";
			echo "num_folio",$folio."<br>";
			if(existe_registro("venta_pastel",array("num_remi","letra_folio","num_cia"),array($folio,$letra,$cias[$z]['idcia']),$dsn))
			{
				$sql="select * from venta_pastel where num_remi=".$folio." and letra_folio='".$letra."' and num_cia=".$cias[$z]['idcia']." and dev_base is NULL order by id";
				$factura=ejecutar_script($sql,$dsn);
				for($j=0;$j<count($factura);$j++){
					if(count($factura)==1){
						if($factura[$j]['resta_pagar'] == 0 and $factura[$j]['fecha_entrega'] != ""){
		//				echo "control amarillo pagado $folio <br>";
		//******************************************************************************************MODIFICARA Y ARREGLARA LOS BLOCS
							$sql="SELECT * FROM bloc WHERE id=".$blocs[$y]['id'];
							$bloc=ejecutar_script($sql,$dsn);
							$num_folios=$bloc[0]['folios_usados'];
							$num_folios++;
							$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$blocs[$y]['id'];
							ejecutar_script($sql,$dsn);
							if($num_folios >=$bloc[0]['num_folios']){
								$sql="UPDATE bloc SET estado=true WHERE id=".$blocs[$y]['id'];
								ejecutar_script($sql,$dsn);
							}
		//******************************************************************************************
							echo "abono ".number_format($factura[$j]['cuenta'],2,'.',',')."<br>";
							echo "total".number_format($factura[$j]['total_factura'],2,'.',',')."<br>";
		//					break;
						}
						else if($factura[$j]['resta_pagar'] > 0 and $factura[$j]['fecha_entrega'] != ""){
							echo "abono".number_format($factura[$j]['cuenta'],2,'.',',')."<br>";
							echo "total".number_format($factura[$j]['total_factura'],2,'.',',')."<br>";
							echo "resta".number_format($factura[$j]['resta_pagar'],2,'.',',')."<br>";
							echo "fecha_entrega".$factura[$j]['fecha_entrega']."<br>";
						}
					}
					else if(count($factura) > 1){
		//				echo "control amarillo y verde $folio <br>";			
						if($factura[$j]['fecha_entrega']!=""){
							echo "abono".number_format($factura[$j]['cuenta'],2,'.',',')."<br>";
							echo "total".number_format($factura[$j]['total_factura'],2,'.',',')."<br>";
							echo "fecha_entrega".$factura[$j]['fecha_entrega']."<br>";
							$resta=number_format($factura[$j]['total_factura'],2,'.','');
							
							if($factura[$j+1]['resta']==$factura[$j]['resta_pagar']){
		//******************************************************************************************MODIFICARA Y ARREGLARA LOS BLOCS
								$sql="SELECT * FROM bloc WHERE id=".$blocs[$y]['id'];
								$bloc=ejecutar_script($sql,$dsn);
								$num_folios=$bloc[0]['folios_usados'];
								$num_folios++;
								$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$blocs[$y]['id'];
								ejecutar_script($sql,$dsn);
								if($num_folios >=$bloc[0]['num_folios']){
									$sql="UPDATE bloc SET estado=true WHERE id=".$blocs[$y]['id'];
									ejecutar_script($sql,$dsn);
								}
		//******************************************************************************************
								break;
							}
							else{
								echo "resta",number_format($factura[$j]['resta_pagar'],2,'.',',')."<br>";
							}
						}
					}
				}
			}
			$folio++;
		}
	}
}
?>