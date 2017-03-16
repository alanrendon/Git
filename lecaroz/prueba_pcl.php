<?php
include 'includes/pcl.inc.php';

$pcl = "";
$pcl .= HEADER;
$pcl .= SetPageSize(LETTER);
$pcl .= SetTopMargin(1);
$pcl .= SetLeftMargin(0);
$pcl .= /*LETTER_GOTHIC_FONT*/ARIAL_FONT;

$pcl .= MoveCursorV(50.00);
$pcl .= MoveCursorH(50.00);
//$pcl .= SetSymbolSet('15Y');
//$pcl .= SetFontTypeFace(0);
$pcl .= SetFontPointSize(8);
//$pcl .= '123456789';

//echo "mm = 25.39999918 ---> inchs = " . mm2inchs(25.39999918) . "<br><br>";

for ($i = 0; $i <= 195; $i++) {
	$pcl .= MoveCursorV(10.00);
	$pcl .= MoveCursorH($i);
	$pcl .= '.';
	if ($i % 10 == 0 || $i == 0) {
		$pcl .= MoveCursorH(-1.5, TRUE);
		$pcl .= "|";
		$pcl .= MoveCursorH(-2.00, TRUE);
		$pcl .= MoveCursorV(4.00, TRUE);
		$pcl .= "$i";
	}
	else if ($i % 5 == 0) {
		$pcl .= MoveCursorH(-1.5, TRUE);
		$pcl .= "|";
	}
	
	//echo "mm = $i ---> inchs = " . mm2inchs((float)$i) . " ---> decipoints = " . mm2decipoints((float)$i) . "<br>";
}
for ($i = 0; $i <= 270; $i++) {
	$pcl .= MoveCursorH(0.00);
	$pcl .= MoveCursorV((float)$i);
	$pcl .= "_";
	if ($i % 10 == 0) {
		$pcl .= "_";
		$pcl .= MoveCursorH(2.00, TRUE);
		$pcl .= MoveCursorV(2.00, TRUE);
		$pcl .= "$i mm";
	}
	else if ($i % 5 == 0)
		$pcl .= "_";
	
	//echo "mm = $i ---> inchs = " . mm2inchs((float)$i) . " ---> decipoints = " . mm2decipoints((float)$i) . "<br>";
}

//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(50.00);
//$pcl .= SetFontPitch(6);
//$pcl .= 'abcABC123 (6pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(60.00);
//$pcl .= SetFontPitch(8);
//$pcl .= 'abcABC123 (8pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(70.00);
//$pcl .= SetFontPitch(10);
//$pcl .= 'abcABC123 (10pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(80.00);
//$pcl .= SetFontPitch(12);
//$pcl .= 'abcABC123 (12pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(90.00);
//$pcl .= SetFontPitch(14);
//$pcl .= 'abcABC123 (14pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(100.00);
//$pcl .= SetFontPitch(16);
//$pcl .= 'abcABC123 (16pt)';

//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(50.00);
//$pcl .= SetFontPointSize(6);
//$pcl .= 'abcABC123 (6pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(60.00);
//$pcl .= SetFontPointSize(8);
//$pcl .= 'abcABC123 (8pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(70.00);
//$pcl .= SetFontPointSize(10);
//$pcl .= 'abcABC123 (10pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(80.00);
//$pcl .= SetFontPointSize(12);
//$pcl .= 'abcABC123 (12pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(90.00);
//$pcl .= SetFontPointSize(14);
//$pcl .= 'abcABC123 (14pt)';
//$pcl .= MoveCursorH(50.00);
//$pcl .= MoveCursorV(100.00);
//$pcl .= SetFontPointSize(16);
//$pcl .= 'abcABC123 (16pt)';


/*$pcl .= MoveCursorH(0.00);
$pcl .= MoveCursorV(0.00);
$pcl .= "# ORIGEN";
$pcl .= MoveCursorH(50.00);
$pcl .= MoveCursorV(50.00);
$pcl .= "PRUEBA DE POSICI\xF3N X=50mm Y=50mm";
$pcl .= MoveCursorH(50.00);
$pcl .= MoveCursorV(100.00);
$pcl .= "PRUEBA DE POSICI\xF3N X=50mm Y=100mm";
$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(240.00);
$pcl .= "PRUEBA DE POSICI\xF3N X=2mm Y=240mm";*/

//$pcl .= ESC . "&%STHPASSWORD$";

/*$pcl .= MoveCursorH(0.00);
$pcl .= MoveCursorV(30.00);
$pcl .= ESC . "&%STP12500$&%1B$(12500X$1200.00&%$";

$pcl .= MoveCursorH(110.00);
$pcl .= MoveCursorV(30.00);
$pcl .= ESC . "&%STP12500$&%1B$(12500X$1200.00&%$";*/

/*$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(0.00);
$pcl .= ESC . "&%SMD0002:511150729:01234567891;0000200$";

$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(5.00);
$pcl .= ESC . "&%SMD0002:511150729:01234567891;0000200$";

$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(10.00);
$pcl .= ESC . "&%SMD0002:511150729:01234567891;0000200$";

$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(244.00);
$pcl .= ESC . "&%SMD0002:511150729:01234567891;0000200$";

$pcl .= MoveCursorH(2.00);
$pcl .= MoveCursorV(150.00);
$pcl .= "ñÑáÁ";*/

$pcl .= RESET;

shell_exec("chmod 777 pcl");
$fp = fopen("pcl/prueba.pcl", "w");
fwrite($fp, $pcl);
fclose($fp);

shell_exec("lp -d S1855 pcl/prueba.pcl");
?>