<?php
require_once $url[0]."conex/conexion.php";
require_once "asiento.class.php";

class PolizaGenerator extends conexion {
	var $facid;
	var $tipo_fac;
	var $tipo_pol;
	var $cons;
	var $anio;
	var $mes;
	var $fecha;
	var $concepto;
	var $comentario;
	var $anombrede;
	var $numcheque;
	var $fk_facture;
	var $ant_ctes;
	var $fechahora;
	var $societe_type;
	var $debe_total;
	var $haber_total;
	
	public function __construct() {
		parent::__construct();
	}
	
	function Simulacion_Polizas_Clientes($facid) {
		$error = 0;
		$tipo_fac = 1;
	
		//Tipo factura = 1 ==> es una factura de cliente y 2 ==> Factura Proveedor
		if ($this->facid > 0 && $this->tipo_fac == $this::FACTURA_TIPO_CLIENTE) {
	
			$pol = new PolizaGenerator();
	
			$sql="SELECT f.rowid, s.nom, f.facnumber, f.datef, f.type, f.fk_cond_reglement, b.dateo, pf.amount, pa.code, pa.libelle, pai.rowid as paimid
		FROM ".PREFIX."facture as f
		     LEFT JOIN ".PREFIX."paiement_facture as pf ON f.rowid = pf.fk_facture
		     INNER JOIN ".PREFIX."societe as s ON f.fk_soc = s.rowid
		     LEFT JOIN ".PREFIX."paiement as pai ON pf.fk_paiement = pai.rowid
		     LEFT JOIN ".PREFIX."bank as b on pai.fk_bank = b.rowid
		     LEFT JOIN ".PREFIX."c_paiement pa ON pai.fk_paiement = pa.id
		     LEFT JOIN (Select a.*,debe From ".PREFIX."contab_polizas a,".PREFIX."contab_polizasdet
		               Where societe_type = 1  AND tipo_pol='I' AND a.rowid=fk_poliza AND debe!=0 ) as cp ON f.rowid = cp.fk_facture AND cp.debe=pf.amount
		WHERE f.entity = ".$conf->entity." AND cp.rowid is NULL AND fk_cond_reglement!=0 AND f.rowid = ".$facid." AND (f.fk_statut=1 || f.fk_statut=2)  ORDER BY f.facnumber";
			//print $sql;
			if ($res = $this->db->query($sql)) {
				if ($obj = $this->db->fetch_object($res)) {
					$fac = new FacPolizaPendiente();
					$fac->fetch($facid);
	
					//Saber si es una factura Estandar, NC o cualquier otra?
					if ($obj->type == $fac::TYPE_STANDARD) {
	
						dol_syslog("Tipo de Factura: Estandard");
	
						//Saber si es una compra al contado o a crédito
						$cond_pago = 1;
						$payment = new Contabpaymentterm($this->db);
						$payment->fetch_by_cond_reglement($obj->fk_cond_reglement);
						//$payment->fetch($obj->fk_cond_reglement);
						if ($payment->cond_pago) {
							$cond_pago = $payment->cond_pago;
						}
	
						foreach ($fac->lines as $i => $line) {
							if ($line->desc == "(DEPOSIT)") {
								dol_syslog("Hay un pago anticipado que se debe de resolver primero.");
								//print "1DEPOSIT::Hay un pago anticipado que se debe de resolver primero.<br>";
								//$pol->Cliente_Saldar_Pago_Anticipado2($obj->rowid, $user, $conf);
								$pol->Cliente_Simula_Saldar_Pago_Anticipado2($obj->rowid, $user, $conf);
							}
						}
	
						if ($cond_pago == $payment::PAGO_AL_CONTADO) {
							//No se realiza el registro directamente por el o los pagos realizados.
							while ($obj) {
								dol_syslog("Se genera la poliza por el pago al contado por el monto de esta transacción: paimid=".$obj->paimid);
								//print "2PAGO_AL_CONTADO::Se genera la poliza por el pago al contado por el monto de esta transaccion: paimid=".$obj->paimid."<br>";
								//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
								$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
								$obj = $this->db->fetch_object($res);
							}
						} else if ($cond_pago == $payment::PAGO_A_CREDITO) {
							dol_syslog("Es una poliza a Clientes, Venta a Crédito - facid=".$obj->rowid);
							//var_dump($conf);
							//var_dump($user);
							//como es a credito, primero se guarda la poliza por la compra a credito.
							dol_syslog("Se genera la póliza por venta a crédito");
							//print "3PAGO_A_CREDITO::Genera la poliza por venta a credito.<br>";
							//$pol->Venta_a_Credito2($obj->rowid, $user, $conf);
							$sqlm="SELECT count(*) as exist
								FROM ".MAIN_DB_PREFIX."contab_polizas
								WHERE fk_facture=".$this->facid." AND societe_type=1 AND tipo_pol='D'";
							$rds=$this->db->query($sqlm);
							$fds=$this->db->fetch_object($rds);
							if($fds->exist==0){
								$pol->Simula_Venta_a_Credito2($obj->rowid, $user, $conf);
							}
							dol_syslog("Se terminó de generar la póliza por la venta a crédito");
							//Ahora se genera la poliza por el pago de la venta a credito.
							//Puede haber varios pagos, así que hay que ver esto.
							while ($obj) {
								dol_syslog("Se genera la póliza por el pago de la compra que se había realizado a credito para la trans:".$obj->paimid);
								//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
								$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
								//print "4PAGO_A_CREDITO::Se genera la poliza por el pago de la compra que se habia realizado a credito para la trans:".$obj->paimid."<br>";
								dol_syslog("Se busca si hay mas pagos realizados, para hacer la póliza correspondiente");
								$obj = $this->db->fetch_object($res);
							}
						} else if ($cond_pago ==  $payment::PAGO_EN_PARTES) {
							dol_syslog("Es una poliza a Clientes, Venta 50/50 - facid=".$obj->rowid);
							//Se genera la póliza de diario por el 50% de la venta a credito
							//print "5PAGO_EN_PARTES::Una poliza a Clientes, Venta 50/50 - facid=".$obj->rowid."<br>";
								
							$pol->Simula_Venta_a_Credito2($obj->rowid, $user, $conf);
	
							//$pol->Venta_a_Credito2($obj->rowid, $user, $conf);
							while ($obj) {
							//Se genera la póliza del ingreso por el 50% del primer pago
								//print "6PAGO_EN_PARTES::Se genera la poliza del ingreso por el 50% del primer pago<br>";
								$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
								//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
								//Se genera la póliza del ingreso por el 50% que había sido a crédito.
								//print "7PAGO_EN_PARTES::Se genera la poliza del ingreso por el 50% que habia sido a credito.<br>";
								//$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
								$obj = $this->db->fetch_object($res);
								//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
							}
							}
							} else if ($obj->type == $fac::TYPE_DEPOSIT) {
							//Debe ser a fuerzas al contado por que es pago anticipado
								dol_syslog("Tipo de Factura: Pago Anticipado (DEPOSIT)");
						$cond_pago = 1;
								$payment = new Contabpaymentterm($this->db);
								$payment->fetch_by_cond_reglement($obj->fk_cond_reglement);
								//$payment->fetch($obj->fk_cond_reglement);
								if ($payment->cond_pago) {
								$cond_pago = $payment->cond_pago;
								}
	
								foreach ($fac->lines as $i => $line) {
								if ($line->desc == "(DEPOSIT)") {
										dol_syslog("Hay un pago anticipado que se debe de resolver primero.");
												//print "8TYPE_DEPOSIT::Hay un pago anticipado que se debe de resolver primero.<br>";
												//$pol->Cliente_Saldar_Pago_Anticipado2($obj->rowid, $user, $conf);
												$pol->Cliente_Simula_Saldar_Pago_Anticipado2($obj->rowid, $user, $conf);
										}
										}
	
										if ($cond_pago == $payment::PAGO_AL_CONTADO) {
											//No se realiza el registro directamente por el o los pagos realizados.
											while ($obj) {
												dol_syslog("Se genera la póliza por el pago al contado por el monto de esta transacción: paimid=".$obj->paimid);
												//print "9PAGO_AL_CONTADO::Se genera la poliza por el pago al contado por el monto de esta transaccion: paimid=".$obj->paimid."<br>";
												$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
												//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
												$obj = $this->db->fetch_object($res);
											}
										} else {
											//var_dump("Error");
											$error ++;
										}
							} else if ($obj->type == $fac::TYPE_CREDIT_NOTE) {
								dol_syslog("Tipo de Factura: Estandard");
	
								//Saber si es una compra al contado o a crédito
								$cond_pago = 1;
								$payment = new Contabpaymentterm($this->db);
								$payment->fetch_by_cond_reglement($obj->fk_cond_reglement);
								//$payment->fetch($obj->fk_cond_reglement);
								if ($payment->cond_pago) {
									$cond_pago = $payment->cond_pago;
								}
									
								if ($cond_pago == $payment::PAGO_AL_CONTADO) {
									//No se realiza el registro directamente por el o los pagos realizados.
									while ($obj) {
										dol_syslog("Se genera la póliza por el pago al contado por el monto de esta transacción: paimid=".$obj->paimid);
										//print "10PAGO_AL_CONTADO::Se genera la poliza por el pago al contado por el monto de esta transaccion: paimid=".$obj->paimid."<br>";
										$pol->Simulacion_de_Factura2($obj->paimid, $user, $conf);
										//$pol->Pago_de_Factura2($obj->paimid, $user, $conf);
										$obj = $this->db->fetch_object($res);
									}
								}
							}
	}
	}
	}
	return $error * -1;
	}
	
	public function Simula_Venta_a_Credito2($facid) {
		require_once "fact_clie_pendientes.class.php";
		$fac = new FacPolizaPendiente();
		$fact=$fac->fetch_facture($facid);
		//print_r($fact);
		//print "<---";
		$facref = $fact[0]['facnumber'];
		$socid = $fact[0]['fk_soc'];
		//print $facref."<---";
		require_once "contabcatctas.class.php";
		require_once "contabrelctas.class.php";
		$rel = new Contabrelctas($this->db);
		$cat = new Contabcatctas($this->db);
		
		$mismo_iva = true;
		$sqlr="SELECT fk_facture, tva_tx FROM ".PREFIX."facturedet WHERE fk_facture=".$facid." GROUP BY tva_tx";
		//print $sqlr;
		$query=$this->db->query($sqlr);
		$nrr=mysqli_num_rows($query);
		if($nrr==1){
			@$rsl=$query->fetch_object($query);
			@$tva_tx = $rsl->tva_tx;
		}else{
			$mismo_iva = false;
		} 
			
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_payment_term,";
		$sql.= " t.cond_pago";
		$sql.= " FROM ".PREFIX."contab_payment_term as t";
		$sql.= " WHERE t.fk_payment_term = ".$fact[0]['fk_cond_reglement'];
		$sql.= " AND entity = ".ENTITY;
		//print $sql;
		$query=$this->db->query($sql);
		$rsl=$query->fetch_object();
		$cond_pago = $rsl->cond_pago;
		/* $payment = new Contabpaymentterm($this->db);
		$payment->fetch_by_cond_reglement($fac->cond_reglement_id);
		//$payment->fetch($fac->fk_cond_reglement);
		if ($payment->cond_pago) {
			$cond_pago = $payment->cond_pago;
		} */
			
		//Que tipo de poliza se trata?
// 		dol_syslog("VENTA A CREDITO");
// 		dol_syslog("Tipo Factura=".$fac->type.", cond_reglement_id=".$fac->cond_reglement_id.", cond_pago=".$cond_pago);
		$tipo_pol = "";
		$concepto = "";
			
		if ($fact[0]['type'] == 0/* $fac::TYPE_STANDARD */) {
			if ($cond_pago == 2/* Contabpaymentterm::PAGO_A_CREDITO */) {
				//la Venta es a Credito
				// 2. El cliente paga a 30 dias.
				// 3. Paga a 30 dias Final del Mes.
				// 4. Paga a 60 dias.
				// 5. Paga a 60 dias Final del Mes.
				$tp = 'D';//$this::POLIZA_DE_DIARIO;
				$concepto = "Venta al Cliente a Credito, Segun Factura ".$fact[0]["facnumber"];
			} else if($cond_pago == 4/* Contabpaymentterm::PAGO_EN_PARTES */) {
				//La venta es 50% a credito y 50% al contado.
				//8. El cliente paga la mitad ahorita y la mitad después.
				$tp = 'D';//$this::POLIZA_DE_DIARIO;
				$concepto = "Venta al Cliente, 50% a Credito y 50% al Contado, Segun Factura ".$fact[0]['facnumber'];
					
				if (!$mismo_iva) {
					$ret = -1;
					return $ret;
				}
			}
		}
		print "<table border='1' style='width:100%;'>";
		print "<tr class='liste_titre'><td colspan='3'><strong>Tipo Poliza:</strong> Diario</td></tr>";
		print "<tr><td colspan='3'><strong>Concepto:</strong> ".$concepto."</td></tr>";
	
		$exists = $this->fetch_by_factura_Y_TipoPoliza($facid, $tp, 1);
			//print ">".$exists;
		if($fact[0]['type'] == 0/* $fac::TYPE_STANDARD  */&& ($cond_pago == 2/* Contabpaymentterm::PAGO_A_CREDITO */  || $cond_pago == 4/* Contabpaymentterm::PAGO_EN_PARTES */ )) {
	
			if ($exists) {
	
			} else {
	
				$cons1= $this->fetch_last_by_tipo_pol($tp);
				if($cons1>=0){
					$cons = $cons1 + 1;
				}
					
				$this->initAsSpecimen();
					
				$this->anio = date("Y", $fact[0]['datef']);
				$this->mes = date("m", $fact[0]['datef']);
				$this->fecha = date("Y-m-d",$fact[0]['datef']);
				$this->concepto = $concepto;
				$comentario = "Factura a Cliente con fecha del ".date("Y-m-d", $fac->date);
	
				print "<tr><td colspan='3'><strong>Comentario:</strong> ".$comentario."</td></tr>";
				print "<tr><td width='80%'><strong>Cuenta</strong></td><td align='right'><strong>Debe</strong></td><td align='right'><strong>Haber</strong></td></tr>";
	
				$this->tipo_pol = $tp;
				$this->cons = $cons;
				$this->fk_facture = $facid;
				$this->societe_type = 1;
					
				//$this->create($user);
				$polid = $this->id;
					
				/* $dscto_ht = 0;
				 $dscto_tva = 0;
				 $dscto_ttc = 0;
				 // Se busca el descuento que fue aplicado a la factura
				 $sql = 'SELECT SUM(amount_ht) amount_ht, SUM(amount_tva) amount_tva, SUM(amount_ttc) amount_ttc ';
				 $sql.= 'FROM llx_societe_remise_except sr ';
				 $sql.= 'INNER JOIN llx_facturedet fd ';
				 $sql.= 'ON sr.fk_facture_line = fd.rowid AND fd.fk_facture = '.$facid;
	
				 dol_syslog("Venta a Credito - sql:".$sql);
	
				 if ($res = $this->db->query($sql)) {
				 if ($row = $this->db->fetch_object($res)) {
				 $dscto_ht = $row->amount_ht;
				 $dscto_tva = $row->amount_tva;
				 $dscto_ttc = $row->amount_ttc;
				 }
				 } */
					
				//Ahora se crearán los asientos contables para la póliza.
				$ln = array();
// 				$faclines=$fac->fetch_facture_lines($facid);
// 				print_r($faclines);
				foreach ($fac->lines as $j => $line) {
					//dol_syslog("j=$j, remise_excent=".$fac->lines[$j]->fk_remise_except);
					//if (! $fac->lines[$j]->fk_remise_except > 0) {
					if(1){//if ($fac->lines[$j]->product_type == 0) {
							
						//dol_syslog("Se trata de un Producto!!!");
							
						//Es un producto
						//Analizando si hay descuento sobre compras
	
						$sub_total = $line->subprice * $line->qty;
						$descto = ($sub_total * $line->remise_percent / 100); //+ $dscto_ht;
						$total = $sub_total - $descto;
						$iva = $total * $line->tva_tx / 100;
							
						dol_syslog("Line id = ".$line->rowid." - ".$fac->lines[$j]->rowid." subtotal=$sub_total, descto=$descto, total=$total, iva=$iva");
							
						/* $dscto_ht = 0;
						 $dscto_tva = 0;
						$dscto_ttc = 0; */
	
						$cuenta = "";
						$codagr = "";
						//Es poliza de Ingreso cuando se hace una venta al contado o con cheque al contado.
						//Es poliza de Egresos cuando se hace un pago con Cheque o se emite un cheque.
						//Es de Diario cuando se trata de cualquier otro tipo de poliza.
						if($cond_pago == Contabpaymentterm::PAGO_A_CREDITO) {
							dol_syslog("PAGO A CREDITO");
							//La venta es 100% a credito.TYPE_STANDARD
							//2 al 5. El cliente compra todo a crédito, 30, 60 días.
	
							//la Venta es 100% a Crédito
							// Se registra una Venta a Credito y 10% de Desc.
	
							// Póliza de Diario
							// Clientes				104.40
							// Descto s/vtas		 10.00
							// 		Ventas					100.00
							//		IVA x Trasladar			 14.40
	
							// Es una póliza de Diario (se trata de una venta a crédito.
							if ($cuenta = $this->Get_Cliente_Proveedor_Cta($socid)) {
								//Se recibe el pago contra el cliente (NO Caja, No Bancos)
								//No se hace nada por que lo único que necesito ya está en la instrucción del IF (el número de cuenta).
								//El cliente tiene definida una cuenta para referenciar acientos contables
								$codagr = "";
								// print "	ESTE ES EL NUMERO DE CUENTA:".$cuenta;
							} else {
								//El cliente no tiene una cuenta asignada, por lo que se toma la que esté con el codagr preseleccionado.
								$cuenta = $this->Get_Cliente_Cuenta($socid, $conf);
							}
							$debe = $total + $iva;
							$haber = 0;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
	
							//Analizando si hay descuento sobre la venta
							// Se registra el Descuento sobre Ventas
							if ($line->remise_percent > 0 || $descto > 0) {
								if ($line->tva_tx > 0) {
									$rel->fetch_by_code("DEV_VTA_TASA_GRAL");
									//$codagr = "402.01"; //Dscto a Tasa General
								} else {
									$rel->fetch_by_code("DEV_VTA_TASA_0");
									//$codagr = "402.02"; //Dscto a Tasa 0%
								}
								$cat->fetch($rel->fk_cat_cta);
								$cuenta = $cat->cta;
								$debe = $descto;
								$haber = 0;
								$cat2 = new Contabcatctas($this->db);
								$cat2->fetch_by_Cta($cuenta);
								print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
								//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
							}
	
							// Se registra la Ventas
							if ($line->tva_tx > 0) {
								$rel->fetch_by_code("VENTAS_TASA_GRAL");
								//$codagr = "401.01"; //Venta a Tasa General
							} else {
								$rel->fetch_by_code("VENTAS_TASA_0");
								//$codagr = "401.04"; //Venta a Tasa 0%
							}
							$cat->fetch($rel->fk_cat_cta);
							$cuenta = $cat->cta;
							$debe = 0;
							$haber = $sub_total;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
	
							//Se registra el IVA Trasladado No Cobrado por que fue a Crédito
							$rel->fetch_by_code("IVA_TRAS_NO_COBRADO");
							/* $codagr = "209.01"; //Iva Trasladado No Cobrado*/
							$cat->fetch($rel->fk_cat_cta);
							$cuenta = $cat->cta;
							$debe = 0;
							$haber = $iva;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
								
						} else if($cond_pago == Contabpaymentterm::PAGO_EN_PARTES) {
							dol_syslog("PAGO EN PARTES");
							//la Venta es 50% al Contado y 50% a crédito
							// Se registra una Venta 50% al Contado y 50% a Credito y 10% de Desc.
	
							// Póliza de Diario
							// Clientes				52.20
							// Descto s/vtas		 5.00
							// 		Ventas					50.00
							//		IVA x Trasladar			 7.20
	
							//Se registra el ingreso a Clientes
							if ($cuenta = $this->Get_Cliente_Proveedor_Cta($socid)) {
								//Se recibe el pago contra el cliente (NO Caja, No Bancos)
								//No se hace nada por que lo único que necesito ya está en la instrucción del IF (el número de cuenta).
								//El cliente tiene definida una cuenta para referenciar acientos contables
								$codagr = "";
								// print "	ESTE ES EL NUMERO DE CUENTA:".$cuenta;
							} else {
								//El cliente no tiene una cuenta asignada, por lo que se toma la que esté con el codagr preseleccionado.
								$cuenta = $this->Get_Cliente_Cuenta($socid, $conf);
							}
							//Se divide ya que es la mitad a credito y la mitad a contado, cada uno en Póliza separada
							$debe = ($total + $iva) / 2;
							$haber = 0;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
	
							// Se registra el Descuento sobre Ventas
							if ($line->remise_percent > 0) {
								if ($line->tva_tx > 0) {
									$rel->fetch_by_code("DEV_VTA_TASA_GRAL");
									//$codagr = "402.01"; //Dscto a Tasa General
								} else {
									$rel->fetch_by_code("DEV_VTA_TASA_0");
									//$codagr = "402.02"; //Dscto a Tasa 0%
								}
								$cat->fetch($rel->fk_cat_cta);
								$cuenta = $cat->cta;
								$debe = $descto / 2;
								$haber = 0;
								$cat2 = new Contabcatctas($this->db);
								$cat2->fetch_by_Cta($cuenta);
								print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
								//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
							}
	
							// Se registra la Ventas
							if ($line->tva_tx > 0) {
								$rel->fetch_by_code("VENTAS_TASA_GRAL");
								//$codagr = "401.01"; //Venta a Tasa General
							} else {
								$rel->fetch_by_code("VENTAS_TASA_0");
								//$codagr = "401.04"; //Venta a Tasa 0%
							}
							$cat->fetch($rel->fk_cat_cta);
							$cuenta = $cat->cta;
							$debe = 0;
							//Se divide ya que es la mitad a credito y la mitad a contado, cada uno en Póliza separada
							$haber = $sub_total / 2;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
	
							//Se registra el IVA
							$rel->fetch_by_code("IVA_TRAS_NO_COBRADO");
							//$codagr = "209.01"; //Iva Trasladado No Cobrado  por ser Venta a Crédito 50%
							$cat->fetch($rel->fk_cat_cta);
							$cuenta = $cat->cta;
							$debe = 0;
							//Se divide ya que es la mitad a credito y la mitad a contado, cada uno en Póliza separada
							$haber = $iva / 2; //(($line->subprice * $line->qty) - $debe_dscto) * $line->tva_tx / 100;
							$cat2 = new Contabcatctas($this->db);
							$cat2->fetch_by_Cta($cuenta);
							print "<tr><td>".$cuenta." ".$cat2->descta."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($debe,2)."</td><td align='right'>".$langs->getCurrencySymbol($conf->currency)." ".number_format($haber,2)."</td></tr>";
							//$ln = $this->Cliente_Proveedor_Almacena_Poliza_Det($user, $polid, $ln, $tp, $cuenta, $debe, $haber);
						}
					} else if ($fac->lines[$j]->product_type == 1) {
						//Se trata de una venta a credito de Servicio
						/*
						* TODO: Realizar la parte de Servicios, específicamente cuando haya retenciones por parte de nuestros
						* 		Clientes cuando les hagamos algun trabajo o servicio y nos tengan que hacer las retenciones
						* 		correspondientes de IVA y de ISR.
						*
						* 		También puede haber venta de servicios a clientes pero que sean de tipo Instalación, Capacitación
						* 		y no necesariamnte se tiene que cobrar o retener IVA e ISR.
						*
						*/
						}
						//} else { dol_syslog("Esta línea no se debe procesar"); }
				}
				print "</table><br>";
				$jj = 0;
						while ($jj < sizeof($ln[$tp])) {
						//$this->Cliente_Proveedor_Crea_Poliza_Det_From_Array($user, $ln[$tp][$jj]);
							$jj++;
						}
						}
						}
			}
			
			function fetch_by_factura_Y_TipoPoliza($facid, $tp, $soc_type)
			{
				$sql = "Select * From ".PREFIX."contab_polizas ";
				$sql.= " Where fk_facture = ".$facid." And tipo_pol = '".$tp."' AND societe_type = ".$soc_type;
				$sql.= " AND entity = ".ENTITY;
				$sql.= " Limit 1;";
				$resql=$this->db->query($sql);
				if ($resql)
				{
					//$this->initAsSpecimen();
			
					if (mysqli_num_rows($resql))
					{
						return 1;
					} else {
						return 0;
					}
				}
				else
				{
					return -1;
				}
			}
			
			function fetch_last_by_tipo_pol($tp)
			{
				$sql = "Select * From ".MAIN_DB_PREFIX."contab_polizas ";
				$sql.= " Where tipo_pol = '".$tp."'";
				$sql.= " AND entity = ".ENTITY;
				$sql.= " Order by cons DESC Limit 1;";
			
				$resql=$this->db->query($sql);
				if ($resql)
				{
					if (mysql_num_rows($resql))
					{
						$obj = $resql->fetch_object($resql);
			 
						return $obj->cons;
					} else {
						return 0;
					}
				}
				else
				{
					return -1;
				}
			}
			
			function initAsSpecimen()
			{
				$this->id=0;
				$this->tipo_pol='';
				$this->cons='';
				$this->anio='';
				$this->mes='';
				$this->fecha='';
				$this->concepto='';
				$this->comentario='';
				$this->anombrede='';
				$this->numcheque='';
				$this->fk_facture='';
				$this->ant_ctes='';
				$this->fechahora='';
				$this->societe_type='';
			
				$this->debe_total = 0;
				$this->haber_total = 0;
			}
			
			
}


?>