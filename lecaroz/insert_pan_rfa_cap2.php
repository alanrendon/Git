<?php
/* CAPTURA DE MOVIMIENTO DE GASTOS
 Tabla 'movimiento_gastos'
 Menu 'Panaderias->Gastos'*/
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
$letra='X';
$tabla = $_GET['tabla'];
$db1 = new Dbclass($dsn,$tabla,$_POST);
$ind=0;

for ($i=0; $i < $db1->numfilas; $i++){
	//Generación de arreglo con los indices de los blocs que se ocuparon en la captura de pasteles
	if($i == 0){
		$indicebloc[$ind] = $_POST['bloc'.$i];
		$ind++;
	}
	if($i > 0){
		if($indicebloc[$ind - 1] != $_POST['bloc'.$i]){
			$indicebloc[$ind] = $_POST['bloc'.$i];
			$ind++;
		}
	}
	//Termina arreglo
	
	$indice= (-1);
	$folios_usados=0;
	$num_folios=0;
	
	if($_POST['letra_folio'.$i]=="")
		$letra='X';
	else
		$letra=strtoupper($_POST['letra_folio'.$i]);

	$datos['num_cia'.$i]=		$_POST['num_cia'.$i];
	$datos['fecha'.$i]=			$_POST['fecha'.$i];
	$datos['letra_folio'.$i]=	$letra;
	$datos['num_remi'.$i]=		$_POST['num_remi'.$i];
	$datos['kilos'.$i]=			$_POST['kilos'.$i];
	$datos['precio_unidad'.$i]=	$_POST['precio_unidad'.$i];
	$datos['otros'.$i]=			$_POST['otros'.$i];
	$datos['base'.$i]=			$_POST['base'.$i];
	$datos['cuenta'.$i]=		$_POST['cuenta'.$i];
	$datos['resta'.$i]=			$_POST['resta'.$i];
	$datos['fecha_entrega'.$i]=	$_POST['fecha_entrega'.$i];
	$datos['idexpendio'.$i]=	$_POST['idexpendio'.$i];
	$datos['dev_base'.$i]=		$_POST['dev_base'.$i];
	$datos['total_factura'.$i]=	$_POST['total_factura'.$i];
	$datos['tipo'.$i] = 		$_POST['tipo'.$i];
	$datos['pastillaje'.$i]=	$_POST['pastillaje'.$i];
	$datos['otros_efectivos'.$i]=$_POST['otros_efectivos'.$i];
	
	if($_POST['resta_pagar'.$i] > 0)
		$datos['resta_pagar'.$i]=number_format($_POST['resta_pagar'.$i],2,'.','');
	else
		$datos['resta_pagar'.$i]=$_POST['resta_pagar'.$i];
		
		
//CONTROL AZUL
//DEVOLUCION DE BASE
	if($_POST['dev_base'.$i] !=""){
		$sql="INSERT INTO movimiento_gastos(codgastos,num_cia,fecha,importe,concepto,captura) VALUES (114,".$_POST['num_cia'.$i].",'".$_POST['fecha'.$i]."',".$_POST['dev_base'.$i].",'DEVOLUCION DE BASE','false')";
		ejecutar_script($sql,$dsn);
		if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn)){
			$sql="UPDATE total_panaderias set 
			efectivo= efectivo - ".number_format($_POST['dev_base'.$i],2,'.','').",
			gastos = gastos + ".number_format($_POST['dev_base'.$i],2,'.','')."
			WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";
			ejecutar_script($sql,$dsn);
		}
		else{
			$sql="INSERT INTO total_panaderias 
			(num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) 
			VALUES
			(".$_POST['num_cia0'].",
			'".$_POST['fecha0']."',
			0,
			0,
			0,
			0,
			".number_format($_POST['dev_base'.$i],2,'.','').",
			0,
			0,
			0,
			 -".number_format($_POST['dev_base'.$i],2,'.','').",
			false,
			false,
			false,
			false,
			true)";
			ejecutar_script($sql,$dsn);
		}
	}

//******************************************************************************************************************

//CONTROL AMARILLO CON DINERO A CUENTA
//NO ES EXPENDIO
	if(($_POST['idexpendio'.$i]=="") && ($_POST['cuenta'.$i]!="") && ($_POST["resta".$i]==0)){
		if($_POST['base'.$i]=="") $base=0; 
		else $base=$_POST['base'.$i];//El importe de la base se suma a otros de efectivos
		
		if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn)){
			$sql="UPDATE total_panaderias set 
			venta_puerta= venta_puerta +".number_format($_POST['cuenta'.$i],2,'.','')." - ".number_format($base,2,'.','').",
			efectivo= efectivo + ".number_format($_POST['cuenta'.$i],2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','').",
			otros = otros + ".number_format($base,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','').",
			pastillaje = pastillaje + ".number_format($_POST['pastillaje'.$i],2,'.','').",
			venta_pastel=venta_pastel + ".number_format($_POST['cuenta'.$i],2,'.','')." - ".number_format($base,2,'.','').",
			pas=true
			WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";
			ejecutar_script($sql,$dsn);
		}
		else{
			$sql="INSERT INTO total_panaderias 
			(num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) 
			VALUES
			(".$_POST['num_cia0'].", '".$_POST['fecha0']."', ".number_format($_POST['cuenta'.$i],2,'.','')." - ".number_format($base,2,'.','').",".number_format($_POST['pastillaje'.$i],2,'.','').",".number_format($base,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','').",0,0,0,".number_format($_POST['cuenta'.$i],2,'.','')." - ".number_format($base,2,'.','').",0,".number_format($_POST['cuenta'.$i],2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','').",false,false,false,false,true)";
			ejecutar_script($sql,$dsn);
		}
		
		// SI LA FACTURA YA SE MANDA PAGADA ENTONCES SE ACTUALIZA EL BLOC
		if(number_format($_POST['resta_pagar'.$i],2,'.','') == 0){
			if($_POST['tipo'.$i]==0){ //SI LA NOTA ES DE TIPO AMARILLO Y ESTA PAGADA ENTONCES PONE EL ESTADO DE PAGADA
				$datos['estado'.$i] = "1";			
/*				$sql="UPDATE bloc SET folios_usados= folios_usados + 1 where id=".$_POST['bloc'.$i];
				ejecutar_script($sql,$dsn);
				$folios_usados++;
				if($folios_usados >= $num_folios){
					$sql="UPDATE bloc SET estado=true where id=".$_POST['bloc'.$i];
					ejecutar_script($sql,$dsn);
				}
*/
			}
		}
		else{
			if($_POST['tipo'.$i]==0)
				$datos['estado'.$i] = "0";
		}
		
	}
//***********************************************************************************************************************************
	//CONTROL VERDE DE FACTURA
	//Actualiza el valor de Otros para los efectivos tomando en cuenta el valor de "resta"
	//Esta parte paga el resto de la factura, tomando en cuenta el valor de resta, y actualiza a la venta en puerta
	if(($_POST['idexpendio'.$i]=="") && ($_POST['cuenta'.$i]==0) && ($_POST["resta".$i]!="") && ($_POST['fecha_entrega'.$i]=="")){
		if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn)){
			//echo "<br>ACTUALIZO TOAL PANADERIAS";
			$sql="UPDATE total_panaderias set 
			venta_puerta= venta_puerta +".$_POST['resta'.$i].", 
			efectivo = efectivo + ".number_format($_POST['resta'.$i],2,'.','').",
			venta_pastel = venta_pastel + ".number_format($_POST['resta'.$i],2,'.','').",
			pas=true
			WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";
			ejecutar_script($sql,$dsn);
		}
		else{
			//echo "<br>INSERTO EN TOTAL PANADERIAS";
			$sql="INSERT INTO total_panaderias 
			(num_cia,
			fecha,
			venta_puerta,
			pastillaje,
			otros,
			abono,
			gastos,
			raya_pagada,
			venta_pastel,
			abono_pastel,
			efectivo,
			efe,
			exp,
			gas,
			pro,
			pas) 
			VALUES
			(".$_POST['num_cia0'].",
			 '".$_POST['fecha0']."',
			 ".number_format($_POST['resta'.$i],2,'.','')."
			 ,0,0,0,0,0,
			 ".number_format($_POST['resta'.$i],2,'.','').",
			 0,
			 ".number_format($_POST['resta'.$i],2,'.','').",
			 false,
			 false,
			 false,
			 false,
			 true)";
			ejecutar_script($sql,$dsn);
		}
		
		$nota=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia'.$i]." and num_remi=".$_POST['num_remi'.$i]." and letra_folio='".$letra."' and tipo=0",$dsn);
		
		if($nota[0]['resta_pagar'] == $_POST['resta'.$i]){ //LA RESTA ES IGUAL AL RESTA PAGAR DEL CONTROL AMARILLO
			
			//PONE EN ESTADO DE PAGADA A LA NOTA AMARILLA
			$sql="update venta_pastel set estado=1 where id=".$nota[0]['id'];
			ejecutar_script($sql,$dsn);
/*			
			if($_POST['tipo'.$i]==1){
				//ACTUALIZA EL BLOC
				$sql="UPDATE bloc SET folios_usados= folios_usados + 1 where id=".$_POST['bloc'.$i];
				ejecutar_script($sql,$dsn);
				$folios_usados++;
				if($folios_usados >=$num_folios){
					$sql="UPDATE bloc SET estado=true where id=".$_POST['bloc'.$i];
					ejecutar_script($sql,$dsn);
				}
			}*/
		}
		elseif($_POST['resta'.$i] < $nota[0]['resta_pagar']){
			$sql="UPDATE venta_pastel set resta_pagar = resta_pagar - ".$_POST['resta'.$i].", estado=0 where id=".$nota[0]['id'];
			ejecutar_script($sql,$dsn);
		}
	}
//***********************************************************************************************************************************
	//Actualiza el rezago para el expendio
	if(($_POST['idexpendio'.$i]!="") && ($_POST['cuenta'.$i]!="") && ($_POST['resta'.$i]==0) && ($_POST['fecha_entrega'.$i]!=""))
	{
		$abono_total=0;
		$abono_total_efectivo=0;
		if($_POST['base'.$i]=="") $base=0; else $base=number_format($_POST['base'.$i],2,'.','');
		$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'.$i],$_POST['idexpendio'.$i]),"","",$dsn);
		// [24-Mar-2008] Obtener devuelto de la nota
		$tmp = ejecutar_script("SELECT devolucion FROM mov_expendios WHERE num_cia = {$_POST['num_cia' . $i]} AND fecha = '{$_POST['fecha' . $i]}' AND num_expendio = {$_POST['idexpendio' . $i]}", $dsn);
		$dev = $tmp ? $tmp[0]['devolucion'] : 0;
		
		//MANDA NOTA DE PASTEL PAGADA
		if($_POST['cuenta'.$i] == $_POST['total_factura'.$i])
		{
			$pan_p_venta= number_format($_POST['cuenta'.$i],2,'.','') - number_format($base,2,'.','');
			$pan_porc = $pan_p_venta - $pan_p_venta * ($porcentaje[0]['porciento_ganancia']/100);
			
			$abono_total=$pan_porc - $dev;
			$abono_total_efectivo= number_format($_POST['cuenta'.$i],2,'.','');
			$abono_total_efectivo= $abono_total_efectivo - $abono_total_efectivo * ($porcentaje[0]['porciento_ganancia']/100) - $dev;
			$sql="UPDATE mov_expendios SET 
			pan_p_venta = pan_p_venta + ".number_format($_POST['cuenta'.$i],2,'.','')." - ".number_format($base,2,'.','').",
			pan_p_expendio = pan_p_expendio + ".number_format($pan_porc,2,'.','').",
			abono = abono + ".number_format(/*$pan_porc*/$abono_total,2,'.','')." 
			WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha ='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i]."";
			ejecutar_script($sql,$dsn);
			
			// [26-Mar-2008] Si 'devuelto' es mayor a cero, actualizar el rezago sumandole el devuelto
			if ($dev > 0) {
				$sql = "UPDATE mov_expendios SET rezago = rezago + $dev WHERE num_cia = {$_POST['num_cia' . $i]} AND fecha = '{$_POST['fecha'.$i]}' AND num_expendio = {$_POST['idexpendio' . $i]}";
				ejecutar_script($sql, $dsn);
			}
			
			//PONE EN ESTADO DE PAGADA A LA NOTA AMARILLA
			$datos['estado'.$i] = "1";
			
/*			if($_POST['tipo'.$i]==1){
				//ACTUALIZA EL BLOC
				$sql="UPDATE bloc SET folios_usados= folios_usados + 1 where id=".$_POST['bloc'.$i];
				ejecutar_script($sql,$dsn);
				$folios_usados++;
				if($folios_usados >=$num_folios){
					$sql="UPDATE bloc SET estado=true where id=".$_POST['bloc'.$i];
					ejecutar_script($sql,$dsn);
				}
			}*/
		}
		//REVISA SI DEJO DINERO A CUENTA O NO
		else{//si la factura es de pan
			$datos['estado'.$i]="0";
			if($_POST['otros'.$i]!="" and $_POST['cuenta'.$i]==0 and $_POST['pastillaje'.$i]=="" and $_POST['otros_efectivos'.$i]=="")
			{
				$pan_p_venta=number_format($_POST['otros'.$i],2,'.','');
				$pan_porc = $pan_p_venta - $pan_p_venta * ($porcentaje[0]['porciento_ganancia'] / 100);
				$abono_total=0/* - $dev*/;
				$abono_total_efectivo=0/* - $dev*/;
				$sql="UPDATE mov_expendios SET 
				pan_p_venta = pan_p_venta + ".number_format($_POST['otros'.$i],2,'.','').",
				pan_p_expendio = pan_p_expendio + ".number_format($pan_porc,2,'.','')."  
				WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha ='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i]."";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago=rezago + ".number_format($pan_porc,2,'.','')." WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha >='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
				ejecutar_script($sql,$dsn);
			}
			else if($_POST['cuenta'.$i] > 0 )//nota de pastel y deja algo pagado
			{
				$pan_p_venta = number_format($_POST['total_factura'.$i],2,'.','') - number_format($_POST["base".$i],2,'.','');
//					$pan_p_venta = number_format($_POST['cuenta'.$i],2,'.','') - number_format($_POST["base".$i],2,'.','');
//					$res1=number_format($_POST['cuenta'.$i],2,'.','') - number_format($base,2,'.','');
				$pan_porc = $pan_p_venta - $pan_p_venta * ($porcentaje[0]['porciento_ganancia'] / 100);
				$pan_rez= $pan_porc - number_format($_POST['cuenta'.$i],2,'.','') + number_format($_POST["base".$i],2,'.','');//nueva linea
				
				$abono_total= number_format($_POST['cuenta'.$i],2,'.','') - number_format($_POST['base'.$i],2,'.','');
				$abono_total= $abono_total - $abono_total * ($porcentaje[0]['porciento_ganancia'] / 100)/* - $dev*/;
				$abono_total_efectivo= number_format($_POST['cuenta'.$i],2,'.','');
				$abono_total_efectivo= $abono_total_efectivo - $abono_total_efectivo * ($porcentaje[0]['porciento_ganancia']/100)/* - $dev*/;
			
				$sql="UPDATE mov_expendios SET 
				pan_p_venta = pan_p_venta + ".number_format($pan_p_venta,2,'.','').",
				pan_p_expendio = pan_p_expendio + ".number_format($pan_porc,2,'.','').",
				abono = abono + ".number_format($abono_total,2,'.','')." 
				WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i]."";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago=rezago + ".number_format($pan_rez,2,'.','')." WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha >='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
				ejecutar_script($sql,$dsn);

			}
			//LA NOTA ES DE PASTEL Y NO DEJA NADA PAGADO
			else if($_POST['cuenta'.$i]==0 and $_POST['otros'.$i]=="")
			{
				$abono_total=0/* - $dev*/;
				$abono_total_efectivo=0/* - $dev*/;
				$pan_p_venta=number_format($_POST['total_factura'.$i],2,'.','') - number_format($_POST["base".$i],2,'.','');
				$pan_porc = $pan_p_venta - $pan_p_venta * ($porcentaje[0]['porciento_ganancia']/100);
				$sql="UPDATE mov_expendios SET 
				pan_p_venta = pan_p_venta + ".number_format($pan_p_venta,2,'.','').",
				pan_p_expendio = pan_p_expendio + ".number_format($pan_porc,2,'.','')."
				WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i]."";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago=rezago + ".number_format($pan_porc,2,'.','')." WHERE num_cia=".$_POST['num_cia'.$i]." AND fecha >='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
				ejecutar_script($sql,$dsn);
			}
		}
		if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn))
		{
			$sql="UPDATE total_panaderias set 
			abono_pastel = abono_pastel + ".number_format($abono_total,2,'.','').", 
			abono=abono + ".number_format($abono_total,2,'.','').",
			efectivo= efectivo + ".number_format($abono_total_efectivo,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','').",
			otros = otros + ".number_format($base,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','').",
			pastillaje = pastillaje + ".number_format($_POST['pastillaje'.$i],2,'.','').",
			pas=true
			WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";

			ejecutar_script($sql,$dsn);
		}
		else
		{
			$sql="INSERT INTO total_panaderias 
			(num_cia,
			fecha,
			venta_puerta,
			pastillaje,
			otros,
			abono,
			gastos,
			raya_pagada,
			venta_pastel,
			abono_pastel,
			efectivo,
			efe,
			exp,
			gas,
			pro,
			pas) 
			VALUES
			(".$_POST['num_cia0'].", 
			'".$_POST['fecha0']."',
			0,
			".number_format($_POST['pastillaje'.$i],2,'.','').",
			".number_format($base,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','').",
			".number_format($abono_total,2,'.','').",
			0,
			0,
			0,
			".number_format($pan_porc,2,'.','').",
			".number_format($abono_total_efectivo,2,'.','')." + ".number_format($_POST['otros_efectivos'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','').",
			false,
			false,
			false,
			false,
			true)";
			ejecutar_script($sql,$dsn);
		}
	}
//********************************************************************************************************
	//Actualiza el valor rezago para el expendio tomando en cuenta el valor de lo que faltaba de resta
	if(($_POST['idexpendio'.$i]!="") && ($_POST['cuenta'.$i]==0) && ($_POST['resta'.$i]!="") && ($_POST['fecha_entrega'.$i]==""))
	{
		$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'.$i],$_POST['idexpendio'.$i]),"","",$dsn);
		$factura_expendio=obtener_registro("venta_pastel",array("num_remi","letra_folio","idexpendio","num_cia"),array($_POST['num_remi'.$i],$letra,$_POST['idexpendio'.$i],$_POST['num_cia'.$i]),"","",$dsn);
		$abono=0;
//			echo "porcentaje ".$porcentaje[0]['porciento_ganancia']."<br>";
		
		// [24-Mar-2008] Obtener devuelto de la nota
		$tmp = ejecutar_script("SELECT devolucion FROM mov_expendios WHERE num_cia = {$_POST['num_cia' . $i]} AND fecha = '{$_POST['fecha' . $i]}' AND num_expendio = {$_POST['idexpendio' . $i]}", $dsn);
		$dev = $tmp ? $tmp[0]['devolucion'] : 0;

//MODIFICACION 14/05/2005
		if(number_format($factura_expendio[0]['cuenta'],2,'.','') == 0)
		{
//				echo "entre a descontar lo del expendio <br>";
			$abono_con_base= $_POST['resta'.$i] - $_POST['resta'.$i] *(number_format($porcentaje[0]['porciento_ganancia'],2,'.','')/100);
			$abono = number_format($factura_expendio[0]['total_factura'],2,'.','') - number_format($factura_expendio[0]['base'],2,'.','');
			$abono = $abono - $abono * (number_format($porcentaje[0]['porciento_ganancia'],2,'.','')/100)/* - $dev*/;
			$sql="
			UPDATE mov_expendios SET 
			abono = abono + ".number_format($abono,2,'.','')." 
			WHERE num_cia=".$_POST['num_cia'.$i]." AND 
			fecha='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
			ejecutar_script($sql,$dsn);
//echo $sql;
			$sql="
			UPDATE mov_expendios SET
			rezago = rezago - ".number_format($abono,2,'.','')."
			WHERE num_cia=".$_POST['num_cia'.$i]." AND 
			fecha >='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
			ejecutar_script($sql,$dsn);
//echo $sql;
		}
		else
		{
//				echo "no desconte lo del expendio <br>";
			$abono=number_format($_POST['resta'.$i],2,'.','')/* - $dev*/;
			$sql="
			UPDATE mov_expendios SET 
			abono = abono + ".$abono." 
			WHERE num_cia=".$_POST['num_cia'.$i]." AND 
			fecha='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
			ejecutar_script($sql,$dsn);
//echo $sql;
			$sql="
			UPDATE mov_expendios SET
			rezago = rezago - ".$abono."
			WHERE num_cia=".$_POST['num_cia'.$i]." AND 
			fecha >='".$_POST['fecha'.$i]."' AND num_expendio=".$_POST['idexpendio'.$i];
			ejecutar_script($sql,$dsn);
//echo $sql;	
		}
		$nota=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia'.$i]." and num_remi=".$_POST['num_remi'.$i]." and letra_folio='".$letra."' and tipo=0",$dsn);
		
		if($nota[0]['resta_pagar'] == $_POST['resta'.$i]){ //LA RESTA ES IGUAL AL RESTA PAGAR DEL CONTROL AMARILLO
			
			//PONE EN ESTADO DE PAGADA A LA NOTA AMARILLA
			$sql="update venta_pastel set estado=1 where id=".$nota[0]['id'];
			ejecutar_script($sql,$dsn);
			
/*			if($_POST['tipo'.$i]==1){
				//ACTUALIZA EL BLOC
				$sql="UPDATE bloc SET folios_usados= folios_usados + 1 where id=".$_POST['bloc'.$i];
				ejecutar_script($sql,$dsn);
				$folios_usados++;
				if($folios_usados >=$num_folios){
					$sql="UPDATE bloc SET estado=true where id=".$_POST['bloc'.$i];
					ejecutar_script($sql,$dsn);
				}
			}*/
		}
		elseif($_POST['resta'.$i] < $nota[0]['resta_pagar']){
			$sql="UPDATE venta_pastel set resta_pagar = resta_pagar - ".$_POST['resta'.$i].", estado=0 where id=".$nota[0]['id'];
			ejecutar_script($sql,$dsn);
		}

		$abono_base = $abono+$factura_expendio[0]['base'];
		
//			echo "abono $abono <br>";
		if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn))
		{
//				$sql="UPDATE total_panaderias set 
//				abono_pastel = abono_pastel + ".number_format($abono,2,'.','').", 
//				abono=abono + ".number_format($abono,2,'.','').",
//				otros= otros + ".number_format($factura_expendio[0]['base'],2,'.','').",
//				efectivo= efectivo + ".number_format($abono,2,'.','').", 
//				pas=true
//				WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";

			$sql="UPDATE total_panaderias set 
			abono_pastel = abono_pastel + ".number_format($abono,2,'.','').", 
			abono=abono + ".number_format($abono,2,'.','').",
			efectivo= efectivo + ".number_format($abono,2,'.','').", 
			pas=true
			WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";
//echo $sql;
			ejecutar_script($sql,$dsn);
		}
		else
		{
			$sql="INSERT INTO total_panaderias 
			(num_cia,
			fecha,
			venta_puerta,
			pastillaje,
			otros,
			abono,
			gastos,
			raya_pagada,
			venta_pastel,
			abono_pastel,
			efectivo,
			efe,
			exp,
			gas,
			pro,
			pas) 
			VALUES
			(".$_POST['num_cia0'].", 
			'".$_POST['fecha0']."',
			0,
			0,
			0,
			".number_format($abono,2,'.','').",
			0,
			0,
			0,
			".number_format($abono,2,'.','').",
			".number_format($abono,2,'.','').",
			false,false,false,false,true)";
			ejecutar_script($sql,$dsn);
//echo $sql;
		}
	}
	
	if($_POST['tipo'.$i]==0){
		if($_POST['total_factura'.$i]==0)
			$datos['estado'.$i] = 2;
	}
	
}
	
for($i = 0; $i <$ind; $i++){
	if($indicebloc[$i] != ""){
		$sql="UPDATE bloc SET folios_usados=0, estado=false WHERE id=".$indicebloc[$i];
		ejecutar_script($sql,$dsn);
		$bloc = ejecutar_script("select * from bloc where id=".$indicebloc[$i],$dsn);
		$folio = $bloc[0]['folio_inicio'];
		for($j=0;$j < $bloc[0]['num_folios'] ; $j++){
			$factura=ejecutar_script("select * from venta_pastel where letra_folio='".$bloc[0]['let_folio']."' and num_cia=".$bloc[0]['idcia']." and num_remi=".$folio." and tipo=0",$dsn);
			if($factura){
				if($factura[0]['estado']==1 or $factura[0]['estado']==2){
					$bloc=ejecutar_script("select * from bloc where id=".$indicebloc[$i],$dsn);
					$num_folios=$bloc[0]['folios_usados'];
					$num_folios++;
					$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$indicebloc[$i];
					ejecutar_script($sql,$dsn);
					if($num_folios >=$bloc[0]['num_folios'])
						ejecutar_script("UPDATE bloc SET estado=true WHERE id=".$indicebloc[$i],$dsn);					
				}
			}
			$folio++;
		}
	}
}

$db = new Dbclass($dsn,$tabla,$datos);	
$db->xinsertar();


//Modificación de los blocs

unset($_SESSION['fac_pas']);
header("location: ./pan_rfa_cap.php");

?>