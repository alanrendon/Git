<?php
// GASTOS DE OFICINA
// Tabla 'gastos_oficina'
// Menu

define ('IDSCREEN',6213); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";

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

// Crear los renglones
for ($i=0;$i<10;$i++) 
{
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("fecha",date("d/m/Y"));
			// Generar listado de catalogo
			$db = DB::connect($dsn);
			if (DB::isError($db)) 
			{
				echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
				die($db->getMessage());
			}
			
			$sql = "SELECT * FROM catalogo_gastos ORDER BY codgastos";
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
				$tpl->newBlock("codigo");
				$tpl->assign("codgastos",$row->codgastos);
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