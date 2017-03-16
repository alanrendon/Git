<?php
if (isset($_POST['textfield'])) {
	print_r($_POST);
	die;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<form name="form1" method="post" action="">
  <input name="textfield[]" type="text" id="textfield0">
  <input name="textfield[]" type="text" id="textfield1">
  <input name="textfield[]" type="text" id="textfield2">
  <input name="textfield[]" type="text" id="textfield3" style="display:none;">
  <input name="textfield[]" type="text" id="textfield4">
  <input type="submit" name="Submit" value="Submit">
  <input type="button" name="Submit2" value="Button" onClick="listar(this.form)">
</form>
<script language="javascript" type="text/javascript">
<!--
function listar(form) {
	//document.write(document.getElementById("textfield4").value);
	for (i = 0; i < form.textfield1.length; i++) {
		write(document.getElementById("textfield" + i).value);
	}
}
-->
</script>
</body>
</html>
