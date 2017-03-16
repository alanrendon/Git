<?php
$fechauno = '2010/01/01';
$fechados = '2010/01/31';

$fechaaamostar = $fechauno;
$cont = 0;
while(strtotime($fechados) >= strtotime($fechauno) || $cont < 31)
{
	if(strtotime($fechados) != strtotime($fechaaamostar))
	{
		echo "$fechaaamostar<br />";
		$fechaaamostar = date('Y/m/d', strtotime($fechaaamostar . " + 1 day"));
	}
	else
	{
		echo "$fechaaamostar<br />";
		break;
	}
	$cont++;
}
?>