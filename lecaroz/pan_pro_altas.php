<?php
// CONTROL PRODUCCION
// Tabla 'control_produccion'
// Menu

define ('IDSCREEN',1212); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "Número de turno no existe en la Base de Datos.";
$descripcion_error[3] = "El código del producto no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Generar listado de turnos
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM catalogo_turnos ORDER BY cod_turno";
$result = $db->query($sql);
//$db->disconnect();
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
//------------------------------------BOrrar esto-------------------------
/*	$CIA = 55;
	$claveros = 0;
	//--------------------control produccion
	echo $sql="TRUNCATE TABLE controlproduccion;";
	$sql="SELECT * FROM control_produccion WHERE num_cia = $CIA AND cod_turno < 5";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO controlproduccion (IdTurno,IdProducto,PrecioRaya,PorRaya,PrecioVenta,Status,num_orden) VALUES (".(($row->cod_turno >0)?$row->cod_turno:"''").",".(($row->cod_producto >0)?$row->cod_producto:"''").",".(($row->precio_raya >0)?$row->precio_raya:"''").",".(($row->porc_raya >0)?$row->porc_raya:"''").",".(($row->precio_venta >0)?$row->precio_venta:"''").",'1',".(($row->num_orden >0)?$row->num_orden:"''").");";
	}
	$sql="SELECT * FROM control_produccion WHERE num_cia = $CIA AND cod_turno = 8";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO controlproduccion (IdTurno,IdProducto,PrecioRaya,PorRaya,PrecioVenta,Status,num_orden) VALUES ('5',".(($row->cod_producto >0)?$row->cod_producto:"''").",".(($row->precio_raya >0)?$row->precio_raya:"''").",".(($row->porc_raya >0)?$row->porc_raya:"''").",".(($row->precio_venta >0)?$row->precio_venta:"''").",'1',".(($row->num_orden >0)?$row->num_orden:"''").");";
	}
	$sql="SELECT * FROM control_produccion WHERE num_cia = $CIA AND cod_turno = 9";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO controlproduccion (IdTurno,IdProducto,PrecioRaya,PorRaya,PrecioVenta,Status,num_orden) VALUES ('6',".(($row->cod_producto >0)?$row->cod_producto:"''").",".(($row->precio_raya >0)?$row->precio_raya:"''").",".(($row->porc_raya >0)?$row->porc_raya:"''").",".(($row->precio_venta >0)?$row->precio_venta:"''").",'1',".(($row->num_orden >0)?$row->num_orden:"''").");";
	}
	$sql="SELECT * FROM control_produccion WHERE num_cia = $CIA AND cod_turno = 10";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO controlproduccion (IdTurno,IdProducto,PrecioRaya,PorRaya,PrecioVenta,Status,num_orden) VALUES ('7',".(($row->cod_producto >0)?$row->cod_producto:"''").",".(($row->precio_raya >0)?$row->precio_raya:"''").",".(($row->porc_raya >0)?$row->porc_raya:"''").",".(($row->precio_venta >0)?$row->precio_venta:"''").",'1',".(($row->num_orden >0)?$row->num_orden:"''").");";
	}
	echo $sql="update controlproduccion set num_cia = $CIA;";
	//-----------------------catalogo de produtos
	
	echo $sql="TRUNCATE TABLE catproductos;";
	$sql="SELECT * FROM catalogo_productos ORDER BY cod_producto";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO catproductos (CodProducto,NombreProducto,Status) VALUES (".(($row->cod_producto >0)?$row->cod_producto:"''").",'".$row->nombre."','1');";
	}
	
	//------------------------catalogo Materias Primas
	echo $sql="TRUNCATE TABLE catmatprimas;";
	$sql="SELECT * FROM catalogo_mat_primas WHERE controlada = 'TRUE' AND tipo_cia = 'TRUE' ORDER BY codmp";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo $sql="INSERT INTO catmatprimas (IdCodMP,CodMP,Nombre,UnidadConsumo,Status,tipo_cia) VALUES (".$row->codmp.",".$row->codmp.",'".$row->nombre."','".$row->unidadconsumo."','1','1');";
	}
	$sql="SELECT * FROM catalogo_mat_primas WHERE tipo_cia = 'FALSE' ORDER BY codmp";
	$ConProd = $db->query($sql);
	$filas = count($ConProd);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
		echo $sql="INSERT INTO catmatprimas (IdCodMP,CodMP,Nombre,UnidadConsumo,Status,tipo_cia) VALUES (".$row->codmp.",".$row->codmp.",'".$row->nombre."','".$row->unidadconsumo."','1','0');";
	}
	//--------------------------Control Avio
	echo $sql="TRUNCATE TABLE controlavio;";
	$sql="SELECT * FROM control_avio WHERE num_cia = $CIA AND cod_turno < 5 ORDER BY codmp";
	$ConProd = $db->query($sql);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
		echo $sql="INSERT INTO controlavio (IdCodMP,IdTurno,Orden) VALUES (".$row->codmp.",'".$row->cod_turno."','".$row->num_orden."');";
	}
	$sql="SELECT * FROM control_avio WHERE num_cia = $CIA AND cod_turno = 8 ORDER BY codmp";
	$ConProd = $db->query($sql);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
			echo $sql="INSERT INTO controlavio (IdCodMP,IdTurno,Orden) VALUES (".$row->codmp.",'5','".$row->num_orden."');";
	}
	$sql="SELECT * FROM control_avio WHERE num_cia = $CIA AND cod_turno = 9 ORDER BY codmp";
	$ConProd = $db->query($sql);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
			echo $sql="INSERT INTO controlavio (IdCodMP,IdTurno,Orden) VALUES (".$row->codmp.",'6','".$row->num_orden."');";
	}
	$sql="SELECT * FROM control_avio WHERE num_cia = $CIA AND cod_turno = 10 ORDER BY codmp";
	$ConProd = $db->query($sql);
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
			echo $sql="INSERT INTO controlavio (IdCodMP,IdTurno,Orden) VALUES (".$row->codmp.",'7','".$row->num_orden."');";
	}
	echo $sql="update controlavio set num_cia = $CIA;";
	//------------------------------------Catalogo Rosticerias
	echo $sql="TRUNCATE TABLE tbl_precio_pollos;";
	$sql="SELECT codmp, nombre, precio_venta FROM inventario_real LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $claveros AND codmp NOT IN (90, 425, 194, 138, 364) ORDER BY orden";
	$ConProd = $db->query($sql);
	$r = 1;
	while ($row = $ConProd->fetchRow(DB_FETCHMODE_OBJECT)) {
			echo $sql="INSERT INTO tbl_precio_pollos (CodMP,concepto,precio,orden,claveros) VALUES (".$row->codmp.",'".$row->nombre."',".$row->precio_venta.",$r,$claveros);";
			$r++;
	}
	
// ---------------------------- Expendios ------------------	
	echo $sql="TRUNCATE TABLE catexpendios;";
	$sql="SELECT nombre, porciento_ganancia, num_expendio FROM catalogo_expendios WHERE num_cia = $CIA ORDER BY num_expendio";
	$Expendio = $db->query($sql);
	while ($row = $Expendio->fetchRow(DB_FETCHMODE_OBJECT)) {
			echo $sql="INSERT INTO `catexpendios` (`IdExpendio` , `Nombre`  , `NumExpendio` , `TipoExpendio` , `PorGanancia` , `Status`) VALUES (".$row->num_expendio.",'".$row->nombre."',".$row->num_expendio.",'1',".$row->porciento_ganancia.",'1');";
	}
	echo $sql="update catexpendios set num_cia = $CIA;";
//------------------------------------Asta Aqui Borrar---------------------*/
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("turno");
	$tpl->assign("value",$row->cod_turno);
	$tpl->assign("nombre",$row->descripcion);
}
$tpl->gotoBlock("_ROOT");


// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();

?>
