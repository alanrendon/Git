<?php // InserciÃ³n de datos en BD
//include 'DB.php';
include './includes/class.session.inc.php';
//include './includes/class.TemplatePower.inc.php';
include './includes/class.db2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass();
$session->validar_sesion();

$tabla = $_GET['tabla'];

switch ($tabla) {
	/* ----------------------------------CATALOGOS EN MENU FACTURAS Y PROVEEDORES---------------------------------- */	
	case "catalogo_proveedores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el número de proveedor
		$sql = "SELECT * FROM $tabla WHERE numero_proveedor = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		// Si no existe proveedor, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			header("location: ./mainframe.php?id_screen=3111&mensaje=Se+registro+proveedor+con+exito");
		}
		else {
			header("location: ./mainframe.php?id_screen=3111&codigo_error=12");
		}
	break;
	case "catalogo_puestos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el código de puesto
		$sql = "SELECT * FROM $tabla WHERE cod_puestos = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		// Si no existe puesto, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			header("location: ./mainframe.php?id_screen=3211&mensaje=Se+creo+puesto+con+exito");
		}
		else {
			header("location: ./mainframe.php?id_screen=3211&codigo_error=7");
		}
	break;
	case "catalogo_horarios":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el código de horario
		$sql = "SELECT * FROM $tabla WHERE cod_horario = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		// Si no existe horario, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			header("location: ./mainframe.php?id_screen=3212&mensaje=Se+creo+horario+con+exito");
		}
		else {
			header("location: ./mainframe.php?id_screen=3212&codigo_error=9");
		}
	break;
	case "catalogo_turnos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el código de horario
		$sql = "SELECT * FROM catalogohorarios WHERE cod_horario = $_POST[campo1]";
		$result = $db->query($sql);
		$db->disconnect();

		// Si existe horario...
		if ($result->numRows() > 0) {
			// Conectandose a la base de datos
			$db = DB::connect($dsn);
			if (DB::isError($db))
				die($db->getMessage());
			
			// Consultar si existe el turno
			$sql = "SELECT * FROM $tabla WHERE cod_turno = $_POST[campo0] AND cod_horario = $_POST[campo1]";
			$result = $db->query($sql);
			$db->disconnect();
			// Si no existe turno, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->determinar_campos($ins->tabla_info());
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				header("location: ./mainframe.php?id_screen=3213&mensaje=Se+creo+turno+con+exito");
			}
			else {
				header("location: ./mainframe.php?id_screen=3213&codigo_error=10");
			}
		}
		else {
			header("location: ./mainframe.php?id_screen=3213&codigo_error=8");
		}
	break;
	case "catalogo_companias":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el nÃºmero de compaÃ±Ã­a
		$sql = "SELECT * FROM $tabla WHERE num_cia = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		// Si no existe compaía, crearla
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			header("location: ./mainframe.php?id_screen=3214&mensaje=Se+registro+compañía+con+exito");
		}
		else {
			header("location: ./mainframe.php?id_screen=3214&codigo_error=4");
		}
	break;
	case "catalogo_trabajadores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe compañía
		$sql = "SELECT * FROM companias WHERE num_cia = $_POST[campo1]";
		$result = $db->query($sql);
		$db->disconnect();	
		
		if ($result->numRows() > 0) {
			// Conectandose a la base de datos
			$db = DB::connect($dsn);
			if (DB::isError($db))
				die($db->getMessage());
			
			// Consultar si existe el número de empleado
			$sql = "SELECT * FROM $tabla WHERE num_empleado = $_POST[campo0]";
			$result = $db->query($sql);
			$db->disconnect();
			
			// Si no existe empelado, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->determinar_campos($ins->tabla_info());
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				header("location: ./mainframe.php?id_screen=3311&mensaje=Se+registro+trabajador+con+exito");
			}
			else {
				header("location: ./mainframe.php?id_screen=3311&codigo_error=14");
			}
		}
		else {
			header("location: ./mainframe.php?id_screen=3311&codigo_error=3");
		}
		break;
		
		
	
	/* ------------------------------ CATALOGOS EN MENU PANADERIAS -------------------------------------*/
	case "catalogo_expendios":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe el nÃºmero de compaÃ±Ã­a
		$sql = "SELECT num_cia FROM companias WHERE num_cia = $_POST[campo1]";
		$result = $db->query($sql);
		$db->disconnect();

		// Si existe compaÃ±Ã­a...
		if ($result->numRows() > 0) {
			// Consultar si existe el nÃºmero de expendio para la compaÃ±Ã­a
			$sql = "SELECT num_expendio FROM expendios WHERE num_cia = $_POST[campo1] AND num_expendio = $_POST[campo0]";
			$result = $db->query($sql);
			$db->disconnect();
			// Si no existe expendio, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->determinar_campos($ins->tabla_info());
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				header("location: ./mainframe.php?id_screen=1111&mensaje=Se+creo+expendio+con+exito'");
			}
			else {
				header("location: ./mainframe.php?id_screen=1111&codigo_error=6");
			}
		}
		else {
			header("location: ./mainframe.php?id_screen=1111&codigo_error=3");
		}
	break;
	
	case "mov_expendios":
		$db = new DBclass($dsn,$tabla,$_POST);
		$db->xinsertar();
		header("location: sub_expendios_M.php");
	break;
	case "produccion":
		$db = new DBclass($dsn,$tabla,$_POST);
		$db->xinsertar();
		// Determinar proximo turno
		if ($_SESSION['turno'] > 0 && $_SESSION['turno'] <= 3)
			$_SESSION['turno']++;
		else if ($_SESSION['turno'] == 4)
			$_SESSION['turno'] = 8;
		else if ($_SESSION['turno'] > 7 && $_SESSION['turno'] <= 9)
			$_SESSION['turno']++;
		// Redireccionar segun el turno
		if ($_SESSION['turno'] <= 9)
			header("location: consulta.php?tabla=control_produccion");
		else {
			unset($_SESSION['turno']);
			unset($_SESSION['compania']);
			unset($_SESSION['fecha']);
			header("location: sub_produccion_cap.php");
		}
	break;

	/* ----------------------------PANTALLAS Y MENUS---------------------------- */
	case "screens":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe la pantalla
		$sql = "SELECT idscreen FROM screens WHERE idscreen = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();

		// Si no existe pantalla, insertarla...
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
				
			header("location: ./screens.htm");
		}
		else {
			header("location: ./screens.htm?error=1");
		}
	break;
	case "menus":
		// Conectandose a la base de datos
				
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		
		// Consultar si existe la pantalla
		$sql = "SELECT idmenu FROM menus WHERE idmenu = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();

		// Si no existe pantalla, insertarla...
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->determinar_campos($ins->tabla_info());
			$ins->generar_script_insert(0);
			$ins->insertar();
				
			header("location: ./menus.htm");
		}
		else {
			header("location: ./menus.htm?error=1");
		}
	break;
	default:
			header("location: ./blank.php");
	break;
}
?>
