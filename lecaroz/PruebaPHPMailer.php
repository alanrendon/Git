<?php
include('includes/phpmailer/class.phpmailer.php');

$mail = new PHPMailer ();

$mail->IsSMTP();
$mail->Host = 'mail.lecaroz.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = 'carlos.candelario+lecaroz.com';
$mail->Password = 'kilian88';

$mail->From = "carlos.candelario@lecaroz.com";
$mail->FromName = "Carlos";
$mail->AddBCC("p_master5@hotmail.com");
$mail->AddBCC('carlos.candelario@live.com.mx');
$mail->Subject = "Prueba";
$mail->Body = "Prueba";
$mail->IsHTML (true);

$mail->AddAttachment('facturas/comprobantes_pdf/28-1.pdf');
$mail->AddAttachment('facturas/comprobantes_xml/28-1.xml');

if(!$mail->Send()) {
	echo 'Error: ' . $mail->ErrorInfo;
}
else {
	echo 'Mail enviado!';
}
?>
