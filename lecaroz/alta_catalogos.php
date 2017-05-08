<?php
// ALTA DE CATALOGOS
// Inserción de registros en catalogos

include './includes/class.session.inc.php';
include './includes/class.db2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass();
$session->validar_sesion();

$tabla = $_GET['tabla'];

switch ($tabla) {
	/* ----------------------------------CATALOGOS EN MENU BANCOS---------------------------------- */	
	case "catalogo_contadores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de contador
		$sql = "SELECT * FROM $tabla WHERE idcontador = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe contador, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Contador. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_con_altas.php?mensaje=Se+registro+contador+con+exito");
		}
		else {
			header("location: ./ban_con_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_administradores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de administrador
		$sql = "SELECT * FROM $tabla WHERE idadministrador = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe administrador, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Administrador. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_adm_altas.php?mensaje=Se+registro+administrador+con+exito");
		}
		else {
			header("location: ./ban_adm_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_auditores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de auditor
		$sql = "SELECT * FROM $tabla WHERE idauditor = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe auditor, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Auditor. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_aud_altas.php?mensaje=Se+registro+auditor+con+exito");
		}
		else {
			header("location: ./ban_aud_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_aseguradoras":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de aseguradora
		$sql = "SELECT * FROM $tabla WHERE idaseguradora = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe aseguradora, creala
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Aseguradora. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_ase_altas.php?mensaje=Se+registro+aseguradora+con+exito");
		}
		else {
			header("location: ./ban_ase_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_del_imss":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de delegacion
		$sql = "SELECT * FROM $tabla WHERE iddelimss = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe delegacion, crearla
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Delegacion del IMSS. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_dim_altas.php?mensaje=Se+registro+delegacion+con+exito");
		}
		else {
			header("location: ./ban_dim_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_subdel_imss":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de subdelegacion
		$sql = "SELECT * FROM $tabla WHERE idsubdelimss = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe subdelegacion, crearla
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Sub-Delegacion del IMSS. ID: $_POST[campo0]", $dsn);
			
			header("location: ./ban_sdi_altas.php?mensaje=Se+registro+subdelegacion+con+exito");
		}
		else {
			header("location: ./ban_sdi_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_sindicatos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de sindicato
		$sql = "SELECT * FROM $tabla WHERE idsindicato = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe sindicato, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Sindicato. ID: $_POST[campo0]",$dsn);
			
			header("location: ./ban_sin_altas.php?mensaje=Se+registro+sindicato+con+exito");
		}
		else {
			header("location: ./ban_sin_altas.php?codigo_error=1");
		}
	break;
	
	/* ----------------------------------CATALOGOS EN MENU FACTURAS Y PROVEEDORES---------------------------------- */	
	case "catalogo_proveedores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el número de proveedor
		$sql = "SELECT * FROM $tabla WHERE num_proveedor = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe proveedor, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Proveedor. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_pro_altas.php?mensaje=Se+registro+proveedor+con+exito");
		}
		else {
			header("location: ./fac_pro_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_puestos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de puesto
		$sql = "SELECT * FROM $tabla WHERE cod_puestos = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe puesto, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Puesto. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_pue_altas.php?mensaje=Se+creo+puesto+con+exito");
		}
		else {
			header("location: ./fac_pue_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_horarios":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}		
		
		// Consultar si existe el código de horario
		$sql = "SELECT * FROM $tabla WHERE cod_horario = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe horario, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Horario. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_hor_altas.php?mensaje=Se+creo+horario+con+exito");
		}
		else {
			header("location: ./fac_hor_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_turnos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
				
		// Consultar si existe el código de horario
		$sql = "SELECT * FROM catalogohorarios WHERE cod_horario = $_POST[campo1]";
		$result = $db->query($sql);
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}

		// Si existe horario...
		if ($result->numRows() > 0) {
			// Consultar si existe el turno
			$sql = "SELECT * FROM $tabla WHERE cod_turno = $_POST[campo0] AND cod_horario = $_POST[campo1]";
			$result = $db->query($sql);
			$db->disconnect();
			
			if (DB::isError($result)) {
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
				die($result->getMessage());
			}
			
			// Si no existe turno, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_campos($ins->tabla_info());
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				// Insertar registro de operacion de usuario en la tabla 'registro'
				$session->guardar_registro_acceso("Alta de Turno. ID: $_POST[campo0]", $dsn);
				
				header("location: ./fac_tur_altas.php?mensaje=Se+creo+turno+con+exito");
			}
			else {
				header("location: ./fac_tur_altas.php?codigo_error=2");
			}
		}
		else {
			header("location: ./fac_tur_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_companias":
		// Verificar si la compañía es dependiente
		if ($_POST['campo27'] == "TRUE") {
			$db = DB::connect($dsn);
			if (DB::isError($db)) {
				echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
				die($db->getMessage());
			}
			
			// Consultar si existe el número de compañía dependiente
			$sql = "SELECT * FROM $tabla WHERE num_cia = $_POST[homoclave]";
			$result = $db->query($sql);
			$db->disconnect();
			
			if (DB::isError($result)) {
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
				die($result->getMessage());
			}
			
			if ($result->numRows() == 0) {
				header("location: ./fac_cia_altas.php?codigo_error=2");
				die;
			}
		}

		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
				
		// Consultar si existe el número de compañía
		$sql = "SELECT * FROM $tabla WHERE num_cia = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}

		// Si no existe compañía, crearla
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Si depende de otra compañía, insertar registro de referencia en 'dependencia_cia'
			if ($_POST['campo27'] == "TRUE") {
				$datosdep['campo0'] = $_POST['homoclave'];
				$datosdep['campo1'] = $_POST['campo0'];
				$ref = new DBclass($dsn,"dependencia_cia",$datosdep);
				$ref->determinar_campos($ref->tabla_info());
				$ref->determinar_cols();
				$ref->determinar_rows();
				$ref->generar_script_insert(0);
				$ref->insertar();			
			}

			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de compañía. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_cia_altas.php?mensaje=Se+registro+compañía+con+exito");
		}
		else {
			header("location: ./fac_cia_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_trabajadores":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
				
		// Consultar si existe compañía
		$sql = "SELECT * FROM companias WHERE num_cia = $_POST[campo1]";
		$result = $db->query($sql);
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		if ($result->numRows() > 0) {
			// Consultar si existe el número de empleado
			$sql = "SELECT * FROM $tabla WHERE num_empleado = $_POST[campo0]";
			$result = $db->query($sql);
			$db->disconnect();
			
			if (DB::isError($result)) {
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos. alta_catalogos.<br>";
				die($result->getMessage());
			}
			
			// Si no existe empelado, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_campos($ins->tabla_info());
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->determinar_campos($ins->tabla_info());
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				// Insertar registro de operacion de usuario en la tabla 'registro'
				$session->guardar_registro_acceso("Alta de Trabajador. ID: $_POST[campo0]", $dsn);
				
				header("location: ./fac_tra_altas.php?mensaje=Se+registro+trabajador+con+exito");
			}
			else {
				header("location: ./fac_tra_altas.php?codigo_error=1");
			}
		}
		else {
			header("location: ./fac_tra_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_mat_primas":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de materia prima
		$sql = "SELECT * FROM $tabla WHERE codmp = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe materia prima, crearla
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Materia Prima. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_mat_altas.php?mensaje=Se+creo+materia+prima+con+exito");
		}
		else {
			header("location: ./fac_mat_altas.php?codigo_error=1");
		}
	break;
	case "catalogo_gastos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el código de gasto
		$sql = "SELECT * FROM $tabla WHERE codgastos = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe gasto, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Gasto. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_gas_altas.php?mensaje=Se+registro+gasto+con+exito");
		}
		else {
			header("location: ./fac_gas_altas.php?codigo_error=1");
		}
	break;
		
	
	/* ------------------------------ CATALOGOS EN MENU PANADERIAS -------------------------------------*/
	case "catalogo_expendios":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
				
		// Consultar si existe compañía
		$sql = "SELECT * FROM catalogo_companias WHERE num_cia = $_POST[campo0]";
		$result = $db->query($sql);
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		if ($result->numRows() > 0) {
			// Consultar si existe el número de expendio
			$sql = "SELECT * FROM $tabla WHERE num_expendio = $_POST[campo6] AND num_cia = $_POST[campo0]";
			$result = $db->query($sql);
			$db->disconnect();
			
			if (DB::isError($result)) {
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
				die($result->getMessage());
			}
			
			// Si no existe expendio, crearlo
			if ($result->numRows() == 0) {
				$ins = new DBclass($dsn,$tabla,$_POST);
				$ins->determinar_campos($ins->tabla_info());
				$ins->determinar_cols();
				$ins->determinar_rows();
				$ins->generar_script_insert(0);
				$ins->insertar();
				
				// Insertar registro de operacion de usuario en la tabla 'registro'
				$session->guardar_registro_acceso("Alta de Expendio. ID: $_POST[campo0]", $dsn);
				
				header("location: ./pan_exp_altas.php?mensaje=Se+registro+expendio+con+exito");
			}
			else {
				header("location: ./pan_exp_altas.php?codigo_error=1");
			}
		}
		else {
			header("location: ./pan_exp_altas.php?codigo_error=2");
		}
	break;
	case "catalogo_productos":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el cçodigo de producto
		$sql = "SELECT * FROM $tabla WHERE cod_producto = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}
		
		// Si no existe producto, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);

			$ins->determinar_campos($ins->tabla_info());

			$ins->determinar_cols();

			$ins->determinar_rows();

			$ins->generar_script_insert(0);

			$ins->insertar();
	
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Producto. ID: $_POST[campo0]", $dsn);
			
			header("location: ./pan_pts_altas.php?mensaje=Se+registro+producto+con+exito");
		}
		else {
			header("location: ./pan_pts_altas.php?codigo_error=1");
		}
	break;
//******************************
	case "catalogo_clientes":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_clientes.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe el cçodigo de producto
		$sql = "SELECT * FROM $tabla WHERE id = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_clietnes.<br>";
			die($result->getMessage());
		}
		
		// Si no existe producto, crearlo
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
			$ins->generar_script_insert(0);
			$ins->insertar();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Alta de Clientes. ID: $_POST[campo0]", $dsn);
			
			header("location: ./fac_clie_alta.php?mensaje=Se+registro+producto+con+exito");
		}
		else {
			header("location: ./fac_clie_alta.php?codigo_error=1");
		}
	break;

//******************************
	/* ----------------------------PANTALLAS Y MENUS---------------------------- */
	case "screens":
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
				
		// Consultar si existe la pantalla
		$sql = "SELECT idscreen FROM screens WHERE idscreen = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}

		// Si no existe pantalla, insertarla...
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
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
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. alta_catalogos.<br>";
			die($db->getMessage());
		}
		
		// Consultar si existe la pantalla
		$sql = "SELECT idmenu FROM menus WHERE idmenu = $_POST[campo0]";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $sql<br>Avisar al administrador. alta_catalogos.<br>";
			die($result->getMessage());
		}

		// Si no existe pantalla, insertarla...
		if ($result->numRows() == 0) {
			$ins = new DBclass($dsn,$tabla,$_POST);
			$ins->determinar_campos($ins->tabla_info());
			$ins->determinar_cols();
			$ins->determinar_rows();
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
