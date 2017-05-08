<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");
$tabla = $_GET['tabla'];
$fecha = getdate(mktime(0, 0, 0, date('n'), date('j') < 5 ? 0 : date('n'), date('Y')));
$cont = 0;
	for ($i=0;$i<20;$i++) 
	{
		//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
		if($_POST['num_cia'.$i] != "")
		{
			//revisa que el gasto se encuentre dentro del catalogo de gastos
			//if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn))
			if ($db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia = {$_POST['num_cia' . $i]}"))
			{
				//if(existe_registro("importe_efectivos",array("num_cia","fecha"),array($_POST['num_cia'.$i], $_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year']),$dsn))
				if($db->query("SELECT * FROM importe_efectivos WHERE num_cia = {$_POST['num_cia' . $i]} AND fecha = '" . $_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year'] . "'"))
				{
					$sql="UPDATE importe_efectivos SET importe=".$_POST['importe'.$i]." where num_cia=".$_POST['num_cia'.$i]." and fecha='".$_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year']."'";
					$db->query($sql);
				}
				else{
					/*$datos['num_cia'.$i]=$_POST['num_cia'.$i];
					$datos['fecha'.$i]=$_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year'];
					$datos['importe'.$i]=$_POST['importe'.$i];*/
					$datos[$cont]['num_cia']=$_POST['num_cia'.$i];
					$datos[$cont]['fecha']=$_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year'];
					$datos[$cont]['importe']=$_POST['importe'.$i];
					$cont++;
				}
			}
			else
			{
				header("location: ./pan_efe_cap.php?codigo_error=1");
				die;
			}
		}
	}

//$db = new DBclass($dsn, $tabla, $datos);

//$db->xinsertar();
if ($cont > 0) $db->query($db->multiple_insert($tabla, $datos));
header("location: ./pan_efe_cap.php");
?>