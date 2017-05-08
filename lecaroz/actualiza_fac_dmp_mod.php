<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

//print_r ($_POST);

for ($i=0; $i<$_POST['cont']; $i++)
{
	$sql="UPDATE catalogo_productos_proveedor set presentacion = " . $_POST['presentacion' . $i] . ", contenido='".$_POST['contenido'.$i]."', precio='".$_POST['precio'.$i]."', desc1='".($_POST['desc1'.$i] > 0 ? $_POST['desc1'.$i] : 0)."', desc2='".($_POST['desc2'.$i] > 0 ? $_POST['desc2'.$i] : 0)."', desc3='".($_POST['desc3'.$i] > 0 ? $_POST['desc3'.$i] : 0)."', iva='".($_POST['iva'.$i] > 0 ? $_POST['iva'.$i] : 0)."', ieps='".($_POST['ieps'.$i] > 0 ? $_POST['ieps'.$i] : 0)."', para_pedido=" . ($_POST['para_pedido'.$i]==1?'TRUE':'FALSE') . " where id='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	// if($_POST['eliminar'.$i]==0)
	// {
	// 	$sql="UPDATE catalogo_productos_proveedor set presentacion = " . $_POST['presentacion' . $i] . ", contenido='".$_POST['contenido'.$i]."', precio='".$_POST['precio'.$i]."', desc1='".($_POST['desc1'.$i] > 0 ? $_POST['desc1'.$i] : 0)."', desc2='".($_POST['desc2'.$i] > 0 ? $_POST['desc2'.$i] : 0)."', desc3='".($_POST['desc3'.$i] > 0 ? $_POST['desc3'.$i] : 0)."', iva='".($_POST['iva'.$i] > 0 ? $_POST['iva'.$i] : 0)."', ieps='".($_POST['ieps'.$i] > 0 ? $_POST['ieps'.$i] : 0)."' where id='".$_POST['id'.$i]."'";
	// 	ejecutar_script($sql,$dsn);
	// }
	// else if($_POST['eliminar'.$i]==1)
	// {
	// 	$sql="DELETE FROM catalogo_productos_proveedor where id='".$_POST['id'.$i]."'";
//		echo $_POST['id'.$i];
	// 	ejecutar_script($sql,$dsn);
	// }
}

header("location: ./fac_dmp_con.php");

?>
