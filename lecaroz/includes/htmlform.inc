<?php
class htmlform{
function creatabla($numrows, $numcols, $titulos){
	$num=0;
	echo "<TABLE border\"1\">";
	for($k=0;$k<$numcols;$k++){
		echo "<th>".$titulos['nombre'.$k]."</th>";
	}
	for($i=0;$i<$numrows;$i++)
	{echo"<tr>";
		for($j=0;$j<$numcols;$j++){
		   	//$campos[$num]="$nombre".$num;
			echo "<td><input type=text name=".$titulos['nomcampo'].$num." size=\"10\"></td>";
			$num ++;
		}
	echo"</tr>";

	}
	echo "</table>";
}




}
?>
