<?php
/*********************************************************************************************/
/* GENERADOR DE ARCHIVOS PCL                                                                 */
/*********************************************************************************************/

// SECUENCIAS DE ESCAPE
define ("ESC", "\x1B");	// Caracter de comienzo de un comando (ESC = 27 DEC = 1B HEX)
define ("BS", "\x08");	// Backspace
define ("HT", "\x09");	// Horizontal Tab
define ("LF", "\x0A");	// Line Feed
define ("FF", "\x0C");	// Form Feed
define ("CR", "\x0D");	// Carriage Return
define ("SP", "\x20");	// Space

// TAMAÑOS DE HOJA
define ("EXECUTIVE", 1);
define ("LETTER",    2);
define ("LEGAL",     3);
define ("FOLIO",     4);
define ("A4",        26);
define ("A5",        13);
define ("UNIVERSAL", 101);

// ORIENTACION
define ("PORTRAIT",          0);
define ("LANDSCAPE",         1);
define ("REVERSE_PORTRAIT",  2);
define ("REVERSE_LANDSCAPE", 3);

// MEDIA TYPE
define ("PLAIN_PAPER",   "Plain");
define ("BOND",          "Bond");
define ("TRANSPARENCY",  "Transparency");
define ("CARD_STOCK",    "Card Stock");
define ("LABELS",        "Labels");
define ("LETTERHEAD",    "Letterhead");
define ("PREPRINTED",    "Preprinted");
define ("COLORED_PAPER", "Color");
define ("ENVELOPE",      "Envelope");

// ORIGEN DE PAPEL
define ("EJECT PAGE",           0);
define ("MAIN_PAPER_SOURCE",    1);
define ("MANUAL_FEED",          2);
define ("MANUAL_ENVELOPE_FEED", 3);
define ("AUTO_SELECT",          7);

// ESTILOS
define ("UPRIGHT", 0);
define ("ITALIC",  1);

// PESO DEL TRAZO
define ("LIGHT",      -3);
define ("DEMI_LIGHT", -2);
define ("SEMI_LIGHT", -1);
define ("MEDIUM",      0);
define ("SEMI_BOLD",   1);
define ("DEMI_BOLD",   2);
define ("BOLD",        3);
define ("EXTRABOLD",   4);

// ENCABEZADOS
define ("HEADER", ESC . "%-12345X@PJL SET RESOLUTION = 720\r\n@PJL ENTER LANGUAGE = PCL\r\n");	// Cabecera del archivo
define ("DEFAULT_FONT", ESC . "(0N" . ESC . "(s1p10v0s0b4101T");			// Fuente por default
																			// R1 CG Times, conjunto de caracteres 0N ISO 8859-1 Latin 1 (ECMA-94) (4101)
																			// Espaciado Proporcional (1)
																			// Tamaño 10
																			// Estilo Normal (0)
																			// Peso Medio (0)
define ("ARIAL_FONT", ESC . "(0N" . ESC . "(s1p10v0s0b16602T");				// Fuente Arial
																			// R29 Arial, conjunto de caracteres 0N ISO 8859-1 Latin 1 (ECMA-94) (16602)
																			// Espaciado Proporcional (1)
																			// Tamaño 10
																			// Estilo Normal (0)
																			// Peso Medio (0)
define ('BARCODE_FONT', ESC . '(9Y' . ESC . '(s1p10v0s0b32772T');			// Fuente para Código de Barras
																			// R88 C39 Wide
define ('LETTER_GOTHIC_FONT', ESC . '(0N' . ESC . '(s0p14h0s0b4102T');		// Fuente Letter Gothic
define ('LETTER_GOTHIC_FONT_BD', ESC . '(0N' . ESC . '(s0p14h0s3b4102T');	// Fuente Letter Gothic Bd
define ('LETTER_GOTHIC_FONT_IT', ESC . '(0N' . ESC . '(s0p14h1s0b4102T');	// Fuente Letter Gothic Bd
																	
define ("RESET", ESC . "E");	// Restablece la impresora
define ("DEFAULT_UNIT_OF_MEASURE", ESC . "&u720D");
define ("FORM_FEED", ESC . "&l0H");	// Cargar forma (Salto de Página)

// UNIDADES Y RESOLUCIÓN
$RESOLUTION = 1 / 720;	// Puntos por pulgada (720 por default)
define ("DECIPOINT", 1 / 720);

/********************************************************************************************/
/* FUNCIONES PARA CONVERSIÓN DE UNIDADES                                                    */
/********************************************************************************************/
function mm2inchs($mm) {
	return $mm * 0.03937008;
}

function mm2decipoints($mm) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)))
		die("mm2decipoints(double \$mm) -- Parámetro '\$mm' fuera de rango");
	
	return floor(mm2inchs($mm) / DECIPOINT);
}

function mm2points($mm) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)))
		die("mm2points(double \$mm) -- Parámetro '\$mm' fuera de rango");
	
	return floor(mm2inchs($mm) / $GLOBALS['RESOLUTION']);
}


/********************************************************************************************/
/* FUNCIONES PARA EL MANEJO DE PAGINA                                                       */
/********************************************************************************************/

function SetUnitOfMeasure($unit) {
	if (!is_numeric($unit) || !is_int($unit) || $unit < 0)
		die("UnitOfMeasure(\$unit) -- Parámetro fuera de rango (\n$unit = 96, 100, 120, 144, 150, 160, 180, 200, 225, 240, 288, 300, 360, 400, 450, 480, 600, 720, 800, 900, 1200, 1440, 1800, 2400, 3600, 7200)");
	
	$GLOBALS['RESOLUTION'] = 1 / $unit;
	
	return ESC . "&u" . $unit . "D";
}

function SetPageSize($range) {
	if (!is_numeric($range) || !is_int($range) || $range < 0)
		die("SetPageSize(\$range) -- Parámetro fuera de rango (\$rango = 2 - LETTER, 3 - LEGAL)");
	
	return ESC . "&l" . $range . "A";
}

function SetUniversalWidth($width) {
	if (!is_numeric($width) || !is_int($width) || $width < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "&f" . mm2decipoints($width) . "G";
}

function SetUniversalHeight($height) {
	if (!is_numeric($height) || !is_int($height) || $height < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "&f" . mm2decipoints($height) . "F";
}

function SelectOrientation($range) {
	if (!is_numeric($range) || !is_int($range) || $range < 0 || $range > 3)
		die("Parámetro fuera de rango");
	
	return ESC . "&l" . $range . "O";
}

function SetLeftMargin($columns) {
	if (!is_numeric($columns) || !is_int($columns) || $columns < 0)
		die("SetLeftMargin(\$columns) -- Parámetro fuera de rango");
	
	return ESC . "&a" . $columns . "L";
}

function SetRightMargin($columns) {
	if (!is_numeric($columns) || !is_int($columns) || $columns < 0)
		die("SetRightMargin(\$columns) -- Parámetro fuera de rango");
	
	return ESC . "&a" . $columns . "M";
}

function SetTopMargin($lines) {
	if (!is_numeric($lines) || !is_int($lines) || $lines < 0)
		die("SetTopMargin(\$lines) -- Parámetro fuera de rango");
	
	return ESC . "&l" . $lines . "E";
}

function SetAlphanumericID($string) {
	if (!is_string($string))
		die("SetAlphanumericID(\$string) -- El parámetro '\$string' debe ser una cadena de texto");
	
	return ESC . "&n" . (strlen($string) + 1) . "Wd" . $string;
}

function SetPaperSource($source) {
	if (!is_numeric($source) || !is_int($source) || $source < 0)
		die("SetPaperSource(\$source) -- Parámetro fuera de rango");
	
	return ESC . "&l" . $source . "H";
}

/********************************************************************************************/
/* FUNCIONES PARA POSICIÓN DEL CURSOR                                                       */
/********************************************************************************************/

function MoveCursorC($columns, $relative = FALSE) {
	if (!is_numeric($columns) || !is_int($columns) || !is_bool($relative))
		die("Parámetro fuera de rango");
	
	if (!$relative && $columns < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "&a" . ($relative == TRUE ? ($columns >= 0 ? "+" : "-") : "") . abs($columns) . "C";
}

function MoveCursorR($rows, $relative = FALSE) {
	if (!is_numeric($rows) || !is_int($rows) || !is_bool($relative))
		die("Parámetro fuera de rango");
	
	if (!$relative && $rows < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "&a" . ($relative == TRUE ? ($rows >= 0 ? "+" : "-") : "") . abs($rows) . "R";
}

function MoveCursorH($mm, $relative = FALSE) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)) || !is_bool($relative))
		die("MoveCursorH(\$mm, \$relative) -- Parámetro fuera de rango");
	
	if (!$relative && $mm < 0)
		die("MoveCursorH(\$mm, \$relative) -- Parámetro fuera de rango");
	
	return ESC . "&a" . ($relative == TRUE ? ($mm >= 0 ? "+" : "-") : "") . abs(mm2decipoints($mm)) . "H";
}

function MoveCursorV($mm, $relative = FALSE) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)) || !is_bool($relative))
		die("MoveCursorV(\$mm, \$relative) -- Parámetro fuera de rango");
	
	if (!$relative && $mm < 0)
		die("MoveCursorV(\$mm, \$relative) -- Parámetro fuera de rango");
	
	return ESC . "&a" . ($relative == TRUE ? ($mm >= 0 ? "+" : "-") : "") . abs(mm2decipoints($mm * 0.9890)) . "V";
}

function MoveCursorX($mm, $relative = FALSE) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)) || !is_bool($relative))
		die("Parámetro fuera de rango");
	
	if (!$relative && $mm < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "*p" . ($relative == TRUE ? ($mm >= 0 ? "+" : "-") : "") . abs(mm2points($mm)) . "X";
}

function MoveCursorY($mm, $relative = FALSE) {
	if (!is_numeric($mm) || !(is_int($mm) || is_float($mm)) || !is_bool($relative))
		die("Parámetro fuera de rango");
	
	if (!$relative && $mm < 0)
		die("Parámetro fuera de rango");
	
	return ESC . "*p" . ($relative == TRUE ? ($mm >= 0 ? "+" : "-") : "") . abs(mm2points($mm * 0.9890)) . "Y";
}

/********************************************************************************************/
/* FUNCIONES PARA EL CONTROL DE FUENTES                                                     */
/********************************************************************************************/

function SetSymbolSet($symbolset) {
	if (!is_string($symbolset))
		die('SetSymbolSet(string $symbolset) -- Parámetro \'$symbolset\' no se encuentra dentro las opciones para el mismo');
	
	return ESC . '(' . $symbolset;
}

function SetFontTypeFace($typeface) {
	if (!is_numeric($typeface) || !is_int($typeface))
		die("SetFontTypeFace(int \$typeface) -- Parámetro '\$typeface' fuera de rango");
	
	return ESC . "(s" . $typeface . "T";
}

function SetFontPointSize($size) {
	if (!is_numeric($size) || !(is_int($size) || is_float($size)) || $size < 0.25 || $size > 999.75)
		die("SetFontPointSize(double \$size) -- Ha excedido el tamaño de la fuente (0.25 >= \$size <= 999.75)");
	
	return ESC . "(s" . $size . "V";
}

function SetFontPitch($pitch) {
	if (!is_numeric($pitch) || !(is_int($pitch) || is_float($pitch)) || $pitch < 0.25 || $pitch > 999.75)
		die("SetFontPitch(double \$pitch) -- Ha excedido el tamaño de la fuente (0.25 >= \$pitch <= 999.75)");
	
	return ESC . "(s" . $pitch . "H";
}

function SetFontStrokeWeight($weight) {
	if (!is_numeric($weight) || !is_int($weight) || $weight < -7 || $weight > 7)
		die("SetFontStrokeWeight(int \$weight) -- EL valor para el peso (\$weight) del trazo no se encuentra dentro del rango de -7 a 7");
	
	return ESC . "(s" . $weight . "B";
}

function SetFontStyle($style) {
	if (!is_numeric($style) || !is_int($style))
		die("SetFontStyle(\$style) -- El valor para el estilo (\$style) no se encuentra dentro de las opciones para el mismo");
	
	return ESC . "(s" . $style . "S";
}

/********************************************************************************************/
/* FUNCIONES PARA MACROS                                                                    */
/********************************************************************************************/

function SetMacroID($id) {
	if (!is_numeric($id) || !is_int($id))
		die("SetMacroID(\$id) -- El valor para el ID (\$id) debe ser un número entero");
	
	return ESC . "&f" . $id . "Y";
}

function MakeMacroIDPermanent() {
	return ESC . "&f10X";
}

function MakeMacroIDTemporary() {
	return ESC . "&f9X";
}

function DeleteMacroID($id) {
	return ESC . "&f" . $id . "y8X";
}

function StartMacroDefinition() {
	return ESC . "&f0X";
}

function EndMacroDefinition() {
	return ESC . "&f1X";
}

function CallMacro($id) {
	if (!is_numeric($id) || !is_int($id))
		die("CallMacro(\$id) -- El valor para el ID (\$id) debe ser un número entero");
	return ESC . "&f" . $id . "y3X";
}
?>