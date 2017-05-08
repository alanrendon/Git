<?php
class sessionclass {
	function sessionclass() {
		session_start();
	}
	
	function validar_sesion($authorized) {
		if ($_SESSION['authorized'] != TRUE) {
			header("location: session_error.php?error=1");
			exit();
		}
	}
}
?>