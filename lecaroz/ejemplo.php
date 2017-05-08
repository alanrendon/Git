<?php
function mostrarTemplate($tema, $variables)
{
    //var_dump($variables);
    extract($variables);
    eval("?>".$tema."<?php");
}

$agenda = array(
    "0" => array("nombre"=>"Marcelo", "edad"=>"25", "domicilio"=>"VeraCRuz 342"),
    "1" => array("nombre"=>"Alejandra", "edad"=>"18", "domicilio"=>"Los Olmos 67"),
    "2" => array("nombre"=>"Micaela", "edad"=>"23", "domicilio"=>"Prof. Mariño 8"),
	"3" => array("nombre"=>"Helsing", "edad"=>"80", "domicilio"=>"IZTAPALACRA")
);
$tpl = implode("", file("ejemplo.html"));
foreach($agenda as $registro)
{
    mostrarTemplate($tpl, $registro);
}
?>