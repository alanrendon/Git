<?php
// MOVIMIENTOS BANCARIOS
// Tabla 'movimientos_bancarios'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";

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

if (!isset($_GET['numfilas'])) {
	$tpl->newBlock("numfilas");
	$tpl->printToScreen();
}

$tpl->newBlock("captura");
// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Crear los renglones
for ($i=0;$i<$_GET['numfilas'];$i++) 
{
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
			// Generar listado de catalogo
			$db = DB::connect($dsn);
			if (DB::isError($db)) 
			{
				echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
				die($db->getMessage());
			}
			
			$sql = "SELECT * FROM catalogo_mov_bancos ORDER BY cod_mov";
			$result = $db->query($sql);
			$db->disconnect();
			if (DB::isError($result)) 
			{
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
				die($result->getMessage());
			}
			while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) 
			{
				$tpl->newBlock("movimientos");
				$tpl->assign("id",$row->cod_mov);
				$tpl->assign("descripcion",$row->descripcion);
			}
	$tpl->gotoBlock("_ROOT");
	
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) 
{
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) 
{
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();


?>
