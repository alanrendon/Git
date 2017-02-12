<?php





require('../../main.inc.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/bank.lib.php';

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/sociales/class/chargesociales.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/tva/class/tva.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/salaries/class/paymentsalary.class.php';

require_once DOL_DOCUMENT_ROOT.'/don/class/don.class.php';

require_once DOL_DOCUMENT_ROOT.'/expensereport/class/expensereport.class.php';

require_once DOL_DOCUMENT_ROOT.'/loan/class/loan.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';



$langs->load("banks");

$langs->load("categories");

$langs->load("bills");

$langs->load("companies");

$langs->load("salaries");

$langs->load("loan");

$langs->load("donations");

$langs->load("trips");



$id = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('account','int'));

$ref = GETPOST('ref','alpha');

$action=GETPOST('action','alpha');

$confirm=GETPOST('confirm','alpha');





$object = new Account($db);



$dateop=-1;



llxHeader('','Cuenta de Inversión');



$societestatic=new Societe($db);

$userstatic=new User($db);

$chargestatic=new ChargeSociales($db);

$loanstatic=new Loan($db);

$memberstatic=new Adherent($db);

$paymentstatic=new Paiement($db);

$paymentsupplierstatic=new PaiementFourn($db);

$paymentvatstatic=new TVA($db);

$paymentsalstatic=new PaymentSalary($db);

$donstatic=new Don($db);

$expensereportstatic=new ExpenseReport($db);

$bankstatic=new Account($db);

$banklinestatic=new AccountLine($db);

$factura_proveedor=new FactureFournisseur($db);



$form = new Form($db);



if ($id > 0 )

{

	$societestatic->fetch($id);

	$result=$object->fetch($id);

	$head=bank_prepare_head($object);

	dol_fiche_head($head,'tabinvcliente',$langs->trans("FinancialAccount"),0,'account');

	$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',

               'Agosto','Septiembre','Octubre','Noviembre','Diciembre');		

	$total_ganancia=0;

	$total_neto=0;

	$actuam_mes= date("m",dol_now());



	if ($action=="generar_reporte") {


		$fecha_inicio=GETPOST("fecha_inicio") ;
		$fecha_final=GETPOST("fecha_final");
		if( !empty( $fecha_inicio) && !empty( $fecha_final) ) {

			$fecha_inicio=date('Y-m-d', strtotime(str_replace('/', '-',  GETPOST("fecha_inicio"))));

			$fecha_final= date('Y-m-d', strtotime(str_replace('/', '-',  GETPOST("fecha_final"))));

			echo '<script>window.open("pdf_formatos.php?id='.$id.'&fecha_inicio='.$fecha_inicio.'&fecha_final='.$fecha_final.'");</script>';

		}else{

			$fecha_inicio="";

			$fecha_final= "";

			echo '<script>window.open("pdf_formatos.php?id='.$id.'");</script>';

		}

	}







	if ($action=="facturar") {

		$mes=GETPOST('mes_facturar','int');



		$anio_facturar=GETPOST('anio_facturar','int');

		

		$sql="SELECT a.rowid,a.fk_tercero,a.date_creation FROM llx_terceros_cuenta as a WHERE a.fk_cuenta=".$id;

		

		$fk_tercero = $db->query($sql);

		

		if ($fk_tercero){

			$num = $db->num_rows($fk_tercero);

			if ($num>0) {

				$fk_tercero = $db->fetch_object($fk_tercero);

				$societestatic->fetch($fk_tercero->fk_tercero);

				

				if (!empty($mes) && !empty($anio_facturar)) {

					

					$factura_proveedor->ref_supplier  		= $societestatic->nom." - ".$mes."/".$anio_facturar;

			        $factura_proveedor->socid         		= $fk_tercero->fk_tercero;

			        $factura_proveedor->libelle       		= "Factura Mensual";

			        $factura_proveedor->date          		= $datefacture;



			        $fecha = date("Y-m-d", strtotime(date("d-m-Y")." + 30 days"));



			        $factura_proveedor->date_echeance 		= $fecha;

			        $factura_proveedor->note_public   		= "Factura correspondiente al mes ".$meses[$mes]." del año ".$anio_facturar;

			        $factura_proveedor->note_private  		= "";

					$factura_proveedor->cond_reglement_id 	= 1;

			        $factura_proveedor->mode_reglement_id	= 0;

			        $factura_proveedor->fk_account        	= null;

			        $factura_proveedor->fk_project    		= null;

					$factura_proveedor->fk_incoterms 		= 0;

			        $factura_proveedor->location_incoterms  = "";





			        $inicio=date('Y', strtotime(str_replace('/', '-',$tercero_camps->date_creation)));

					$final= date("Y",dol_now());





					$sql_porcentage="SELECT a.rowid,a.fk_tercero,a.date_creation FROM llx_terceros_cuenta as a WHERE a.fk_cuenta=".$id;

					$resultado=$db->query($sql_porcentage);

					if ($resultado){

						$numero = $db->num_rows($resultado);

						if ($numero>0) {



							$tercero_camps = $db->fetch_object($result);

							$inicio=date('Y', strtotime(str_replace('/', '-',$tercero_camps->date_creation)));

							$final= $anio_facturar;

							$total_global2=0;

							for ($k=$inicio; $k <=$final ; $k++) {

								if ($k==$final) {

									$actuam_mes= $mes;

								}else{

									$actuam_mes= 12;

								}

								for ($j=1; $j <=$actuam_mes ; $j++) { 

									

									$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv,";

									$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type, b.fk_bordereau,";

									$sql.= " ba.rowid as bankid, ba.ref as bankref, ba.label as banklabel,YEAR(b.datev) as anio";

									$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";

									$sql.= ", ".MAIN_DB_PREFIX."bank as b";

									$sql.= " WHERE b.fk_account=".$object->id;

									$sql.= " AND b.fk_account = ba.rowid AND b.inv=1";

									$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).") AND MONTH(b.datev)=".$j." AND YEAR(b.datev)=".$k;

									$sql.= $sql_rech;

									$sql.= $db->order("b.datev", "ASC");  // We add date of creation to have correct order when everything is done the same day

									$sql.= $db->plimit($limitsql, 0);



									$result2 = $db->query($sql);

									if ($result2)

									{

										$num = $db->num_rows($result2);

										$i = 0; $total2 = 0; 

										if ($num>0) {

											while ($i < $num)

											{

												$objp = $db->fetch_object($result2);



												$total2 = price2num($total2 + $objp->amount,'MT');

												

												$i++;

												if ($i!=$num-1) {

													$total_global2=$total_global2+$total2;

												}

											}

											$total_ganancia2=$total2/100*$societestatic->array_options["options_inv002"];

											

											if ($total_ganancia2==0) {

												$total_neto2=0;

											}else{

												$total_neto2=$total_neto2+$total_ganancia2;

											}
										}else{

											//$total_neto2=$total_neto2+$total_ganancia2;

										}

									}

								}

							}

						}

					}

					

					$factura_proveedor->lines[0]=new FactureLigne($db);

					$factura_proveedor->lines[0]->pu_ht=$total_neto2;

					$factura_proveedor->lines[0]->description="Ganancia cuenta de inversión (".$meses[$mes]."/".$anio_facturar.")";

					$factura_proveedor->lines[0]->tva_tx=0;

					$factura_proveedor->lines[0]->localtax1_tx=0;

					$factura_proveedor->lines[0]->localtax2_tx=0;

					$factura_proveedor->lines[0]->qty=1;

					$factura_proveedor->lines[0]->fk_product="";



			       	$id_factura= $factura_proveedor->create($user);

			        if ($id_factura>0) {

			        	$factura_proveedor->validate($user,'','');

			        	$sql='INSERT INTO llx_corte_mensual (mes,anio,fk_cuenta,fk_factura) VALUES ( '.$mes.', '.$anio_facturar.' , '.$id.', '.$id_factura.');';

			        	$result2 = $db->query($sql);	

			        }

				}

			}

		}

	}









	$sql_porcentage="SELECT a.rowid,a.fk_tercero,a.date_creation FROM llx_terceros_cuenta as a WHERE a.fk_cuenta=".$id;

	

	$resultado=$db->query($sql_porcentage);

	if ($resultado){

		$numero = $db->num_rows($resultado);

		if ($numero>0) {

			$tercero_camps = $db->fetch_object($result);

			$societestatic->fetch($tercero_camps->fk_tercero);

			if ($societestatic->array_options["options_inv001"] && $societestatic->array_options["options_inv001"]==1) {

				

				if ($societestatic->array_options["options_inv002"] && is_numeric($societestatic->array_options["options_inv002"])  ) {

					

					$anio= GETPOST("anio");



					if (!empty($anio)) {

						$inicio=$anio;

						$final= $anio;

					}else{

						$inicio=date('Y', strtotime(str_replace('/', '-',$tercero_camps->date_creation)));

						$final= date("Y",dol_now());

					}



					

					$total_global=0;

					$tot=0;

					for ($k=$inicio; $k <=$final ; $k++) {

						if ($k==$final) {

							$actuam_mes= date("m",dol_now());

						}else{

							$actuam_mes= 12;

						}



						for ($j=1; $j <=$actuam_mes ; $j++) { 

							$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv,";

							$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type, b.fk_bordereau,";

							$sql.= " ba.rowid as bankid, ba.ref as bankref, ba.label as banklabel,YEAR(b.datev) as anio";

							$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";

							$sql.= ", ".MAIN_DB_PREFIX."bank as b";

							$sql.= " WHERE b.fk_account=".$object->id;

							$sql.= " AND b.fk_account = ba.rowid AND b.inv=1";

							$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).") AND MONTH(b.datev)=".$j." AND YEAR(b.datev)=".$k;

							$sql.= $db->plimit($limitsql, 0);




							$result = $db->query($sql);




							if ($result){

								$num = $db->num_rows($result);

								$i = 0; $total = 0;



								if ($num>0) {

									while ($i < $num)

									{

										$objp = $db->fetch_object($result);

										$total = price2num($total + $objp->amount,'MT');

										$i++;

										if ($i!=$num-1) {

											$total_global=$total_global+$total;

										}

									}

									



									$total_ganancia=$total/100*$societestatic->array_options["options_inv002"];
									
									

									if ($total_ganancia==0) {

										$total_neto=0;

									}else{

										$total_neto=$total_neto+$total_ganancia;

									}

									$tot+=$total;

								}else{

									//$total_neto=$total_neto+$total_ganancia;
								}
								$var_tot+=$total_neto;

							}

						}

					}

					

					print '

					<div class="fichecenter">

						<table class="nobordernopadding" width="100%">

							<tbody>

								<tr>

									<td class="nowrap borderright">

										<table>

											<tbody>

												<tr>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 20px;">

														Fecha inicial de la inversión: 

													</td>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">

														'.$tercero_camps->date_creation.'

													</td>

												</tr>

												<tr>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">

														Monto invertido: 

													</td>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">

														'.price($tot).'

													</td>

												</tr>

												<tr>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">

														Ganancia acumulada: 

													</td>

													<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">

														'.price($var_tot).'

													</td>

												</tr>

											



											</tbody>

										</table>

									</td>

									<td align="center" class="nowrap borderright">

										<form method="post" action="'.$_SERVER["PHP_SELF"].'">

										<input type="hidden" id="action" name="action" value="buscar">

										<input type="hidden" name="id" value="'.$id.'">

											<table>

												<tbody>

													<tr>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 20px;">

															Año de la Inversión: 

														</td>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 14px;">

															<input size="8" type="text" class="flat" name="anio" value="'.$anio.'" maxlength="12">

														</td>

													</tr>

													

													<tr>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;" colspan=2 align="center">

															<input type="submit" class="button" style="min-width:120px" name="refresh" value="Buscar">

														</td>

													</tr>

												</tbody>

											</table>

										</form>

									</td>

									<td align="center" class="nowrap borderright">

										<form method="post" action="'.$_SERVER["PHP_SELF"].'">

										<input type="hidden" id="action" name="action" value="generar_reporte">

										<input type="hidden" name="id" value="'.$id.'">

											<table>

												<tbody>

													<tr>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 20px;">

															Fecha de Inicio: 

														</td>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 14px;">';

															$form->select_date($fecha_inicio,'fecha_inicio',0,0,1,"form_date",1,1,0,0,'');

													print '

														</td>

													</tr>

													<tr>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 20px;">

															Fecha Final: 

														</td>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 14px;">';

													$form->select_date($fecha_final,'fecha_final',0,0,1,"form_date",1,1,0,0,'');

													print '

														</td>

													</tr>

													

													<tr>

														<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;" colspan=2 align="center">

															<input type="submit" class="button" style="min-width:120px" name="refresh" value="Generar Reporte">

														</td>

													</tr>

												</tbody>

											</table>

										</form>

									</td>

								</tr>



							</tbody>

						</table>

					</div>';

						

					print '</div>';

					

					$total_ganancia=0;

					$total_neto=0;

					



					for ($k= $inicio ; $k <=$final  ; $k++) {





						if ($k==$final) {

							$actuam_mes= date("m",dol_now());

						}else{

							$actuam_mes= 12;

						}



						for ($j=1; $j <=$actuam_mes ; $j++) { 

							

							$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv,";

							$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type, b.fk_bordereau,";

							$sql.= " ba.rowid as bankid, ba.ref as bankref, ba.label as banklabel,YEAR(b.datev) as anio";

							

							$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";

							$sql.= ", ".MAIN_DB_PREFIX."bank as b";

							

							$sql.= " WHERE b.fk_account=".$object->id;

							$sql.= " AND b.fk_account = ba.rowid AND b.inv=1";

							$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).") AND MONTH(b.datev)=".$j." AND YEAR(b.datev)=".$k;

							$sql.= $sql_rech;

							$sql.= $db->order("b.datev", "ASC"); 

							$sql.= $db->plimit($limitsql, 0);



							$result = $db->query($sql);

		
							//listado de cuentas

							if ($result)

							{

								

								$now=dol_now();

								$nows=dol_print_date($now,'%Y%m%d');

								$var=true;

								$num = $db->num_rows($result);

								

								if ($num>0) {

									$i = 0; $total = 0; $sep = -1; $total_deb=0; $total_cred=0;
									print '<table class="noborder" width="100%">';
									// Ligne de titre tableau des ecritures
									print '<tr class="liste_titre">';

									print '<td>Mes</td>';

									print '<td>'.$langs->trans("Date").'</td>';

									print '<td>'.$langs->trans("Value").'</td>';

									print '<td>'.$langs->trans("Type").'</td>';


									print '<td>'.$langs->trans("Description").'</td>';

									print '<td>'.$langs->trans("ThirdParty").'</td>';

									print '<td align="right">'.$langs->trans("Debit").'</td>';

									print '<td align="right">'.$langs->trans("Credit").'</td>';

									print '<td align="right" >'.$langs->trans("BankBalance").'</td>';

									print '<td align="right" >% de Ganancia</td>';

									print '<td align="center" >Generar Factura</td>';



									print '</tr>';

								

									$ban=1;



									while ($i < $num)

									{

										$objp = $db->fetch_object($result);

										$total = price2num($total + $objp->amount,'MT');

										if ($i >= ($viewline * (($totalPages-$page)-1)))

										{

											$var=!$var;

											$dos=dol_print_date($db->jdate($objp->do),'%Y%m%d');

											//print "dos=".$dos." nows=".$nows;

											if ($dos < $nows) $sep=0;		// 0 means there was at least one line before current date

										



											print '<tr '.$bc[$var].'>';



											if ($ban==1) {

												print '<td rowspan="'.($num).'" style="width:150px;">'.$meses[$j].' '.$objp->anio.'</td>';

												$ban=0;

											}



											print '<td class="nowrap">'.dol_print_date($db->jdate($objp->do),"day")."</td>\n";



											print '<td class="nowrap">'.dol_print_date($db->jdate($objp->dv),"day");

											print "</td>\n";



											// Payment type

											print '<td class="nowrap">';

											$label=($langs->trans("PaymentTypeShort".$objp->fk_type)!="PaymentTypeShort".$objp->fk_type)?$langs->trans("PaymentTypeShort".$objp->fk_type):$objp->fk_type;



											if ($objp->fk_type == 'SOLD') $label='&nbsp;';

											if ($objp->fk_type == 'CHQ' && $objp->fk_bordereau > 0) 
											{

												dol_include_once('/compta/paiement/cheque/class/remisecheque.class.php');

												$bordereaustatic = new RemiseCheque($db);

												$bordereaustatic->id = $objp->fk_bordereau;

												$label .= ' '.$bordereaustatic->getNomUrl(2);

											}

											print $label;

											print "</td>\n";

											// Description

											print '<td>';

											// Show generic description

											if (preg_match('/^\((.*)\)$/i',$objp->label,$reg))

											{

												// Generic description because between (). We show it after translating.

												print $langs->trans($reg[1]);

											}

											else

											{

												print dol_trunc($objp->label,60);

											}

											

											// Add links after description

											$links = $object->get_url($objp->rowid);

											foreach($links as $key=>$val)
											{

												if ($links[$key]['type']=='payment')

												{

													$paymentstatic->id=$links[$key]['url_id'];

													$paymentstatic->ref=$links[$key]['url_id'];

													print ' '.$paymentstatic->getNomUrl(2);

												}

												elseif ($links[$key]['type']=='payment_supplier')

												{

													$paymentsupplierstatic->id=$links[$key]['url_id'];

													$paymentsupplierstatic->ref=$links[$key]['url_id'];

													print ' '.$paymentsupplierstatic->getNomUrl(2);

												}

												elseif ($links[$key]['type']=='payment_sc')

												{

													print '<a href="'.DOL_URL_ROOT.'/compta/payment_sc/card.php?id='.$links[$key]['url_id'].'">';

													print ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

													//print $langs->trans("SocialContributionPayment");

													print '</a>';

												}

												elseif ($links[$key]['type']=='payment_vat')

												{

													$paymentvatstatic->id=$links[$key]['url_id'];

													$paymentvatstatic->ref=$links[$key]['url_id'];

													print ' '.$paymentvatstatic->getNomUrl(2);

												}

												elseif ($links[$key]['type']=='payment_salary')

												{

													$paymentsalstatic->id=$links[$key]['url_id'];

													$paymentsalstatic->ref=$links[$key]['url_id'];

													print ' '.$paymentsalstatic->getNomUrl(2);

												}

												elseif ($links[$key]['type']=='payment_loan')

												{

													print '<a href="'.DOL_URL_ROOT.'/loan/payment/card.php?id='.$links[$key]['url_id'].'">';

													print ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

													print '</a>';

												}

												elseif ($links[$key]['type']=='payment_donation')

												{

													print '<a href="'.DOL_URL_ROOT.'/don/payment/card.php?id='.$links[$key]['url_id'].'">';

													print ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

													print '</a>';

												}

												elseif ($links[$key]['type']=='payment_expensereport')

												{

													print '<a href="'.DOL_URL_ROOT.'/expensereport/payment/card.php?id='.$links[$key]['url_id'].'">';

													print ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

													print '</a>';

												}

												elseif ($links[$key]['type']=='banktransfert')

												{

													// Do not show link to transfer since there is no transfer card (avoid confusion). Can already be accessed from transaction detail.

													if ($objp->amount > 0)

													{

														$banklinestatic->fetch($links[$key]['url_id']);

														$bankstatic->id=$banklinestatic->fk_account;

														$bankstatic->label=$banklinestatic->bank_account_label;

														print ' ('.$langs->trans("TransferFrom").' ';

														print $bankstatic->getNomUrl(1,'transactions');

														print ' '.$langs->trans("toward").' ';

														$bankstatic->id=$objp->bankid;

														$bankstatic->label=$objp->bankref;

														print $bankstatic->getNomUrl(1,'');

														print ')';

													}

													else

													{

														$bankstatic->id=$objp->bankid;

														$bankstatic->label=$objp->bankref;

														print ' ('.$langs->trans("TransferFrom").' ';

														print $bankstatic->getNomUrl(1,'');

														print ' '.$langs->trans("toward").' ';

														$banklinestatic->fetch($links[$key]['url_id']);

														$bankstatic->id=$banklinestatic->fk_account;

														$bankstatic->label=$banklinestatic->bank_account_label;

														print $bankstatic->getNomUrl(1,'transactions');

														print ')';

													}

													//var_dump($links);

												}else

												{

													// Show link with label $links[$key]['label']

													if (! empty($objp->label) && ! empty($links[$key]['label'])) print ' - ';

													print '<a href="'.$links[$key]['url'].$links[$key]['url_id'].'">';

													if (preg_match('/^\((.*)\)$/i',$links[$key]['label'],$reg))

													{

														// Label generique car entre parentheses. On l'affiche en le traduisant

														if ($reg[1]=='paiement') $reg[1]='Payment';

														print ' '.$langs->trans($reg[1]);

													}

													else

													{

														print ' '.$links[$key]['label'];

													}

													print '</a>';

												}

											}

											print '</td>';
											
											print '<td>';
											foreach($links as $key=>$val)
											{
												if ($links[$key]['type']=='company')
												{
													$societestatic->id=$links[$key]['url_id'];
													$societestatic->name=$links[$key]['label'];
													print $societestatic->getNomUrl(1,'',16);
												}
												else if ($links[$key]['type']=='user')
												{
													$userstatic->id=$links[$key]['url_id'];
													$userstatic->lastname=$links[$key]['label'];
													print $userstatic->getNomUrl(1,'');
												}
												else if ($links[$key]['type']=='sc')
												{
													// sc=old value
													$chargestatic->id=$links[$key]['url_id'];
													if (preg_match('/^\((.*)\)$/i',$links[$key]['label'],$reg))
													{
														if ($reg[1]=='socialcontribution') $reg[1]='SocialContribution';
														$chargestatic->lib=$langs->trans($reg[1]);
													}
													else
													{
														$chargestatic->lib=$links[$key]['label'];
													}
													$chargestatic->ref=$chargestatic->lib;
													print $chargestatic->getNomUrl(1,16);
												}
												else if ($links[$key]['type']=='loan')
												{
													$loanstatic->id=$links[$key]['url_id'];
													if (preg_match('/^\((.*)\)$/i',$links[$key]['label'],$reg))
													{
														if ($reg[1]=='loan') $reg[1]='Loan';
														$loanstatic->label=$langs->trans($reg[1]);
													}
													else
													{
														$loanstatic->label=$links[$key]['label'];
													}
													$loanstatic->ref=$loanstatic->label;
													print $loanstatic->getLinkUrl(1,16);
												}
												else if ($links[$key]['type']=='member')
												{
													$memberstatic->id=$links[$key]['url_id'];
													$memberstatic->ref=$links[$key]['label'];
													print $memberstatic->getNomUrl(1,16,'card');
												}
											}
											print '</td>';



											// Amount

											if ($objp->amount < 0)

											{

												print '<td align="right" class="nowrap">'.price($objp->amount * -1).'</td><td>&nbsp;</td>'."\n";

												$total_deb +=$objp->amount;

											}

											else

											{

												print '<td>&nbsp;</td><td align="right" class="nowrap">&nbsp;'.price($objp->amount).'</td>'."\n";

												$total_cred +=$objp->amount;

											}



											// Balance

											if (! $mode_search)

											{

												if ($total >= 0)

												{

													print '<td align="right" class="nowrap">&nbsp;'.price($total).'</td>';

												}

												else

												{

													print '<td align="right" class="error nowrap">&nbsp;'.price($total).'</td>';

												}

											}else{
												print '<td align="right">-</td>';

											}

											if ($objp->amount < 0)

											{

												print '<td align="right" class="nowrap" style="color:red;">'.price(($objp->amount * -1)/100*$societestatic->array_options["options_inv002"]).'</td><td>&nbsp;</td>'."\n";

												$total_deb +=$objp->amount;

											}

											else{

												print '<td align="right" class="nowrap" >&nbsp;'.price($objp->amount/100*$societestatic->array_options["options_inv002"]).'</td>'."\n";

												$total_cred +=$objp->amount;

											}

											print "</tr>";

										}



										$i++;

									}

									print '<tr class="liste_total">';

	

									$total_ganancia=$total/100*$societestatic->array_options["options_inv002"];



									$total_neto=$total_neto+$total_ganancia;

									if ($total_cred==0) {

										$total_neto=0;

									}
									print '<td colspan="8">';

										print $langs->trans("CurrentBalance");

									print '</td>';
									
									
									print '<td align="right" class="nowrap"><b>'.price($total).'</b></td>';

									print '<td align="right" class="nowrap" style="'.((price($total/100*$societestatic->array_options["options_inv002"])>0)?"color:green;":"color:red;").'"><b>'.price($total/100*$societestatic->array_options["options_inv002"]).'</b></td>';



										$sab='

										SELECT a.fk_factura FROM llx_corte_mensual as a

											INNER JOIN llx_facture_fourn as b on a.fk_factura=b.rowid

										WHERE a.mes='.$j.' AND a.anio='.$k.' AND a.fk_cuenta='.$id;

										

										$bot=$db->query($sab);
										if ($bot && $total_cred>0) {



											$camp = $db->num_rows($bot);



											if ($camp>0) {
												$bot2 = $db->fetch_object($bot);	

												$fact=new FactureFournisseur($db);

												$fact->fetch($bot2->fk_factura);
												print '<td rowspan="'.($num).'" align="center" style="vertical-align: center;"  >';
													print $fact->getNomUrl(0);		
												print '</td>';

											}else{

												print '

												<td rowspan="'.($num).'" align="center" style="vertical-align: center;" >

													<form action="'.$_SERVER["PHP_SELF"].'" method="post">

														<input type="hidden" name="action"  value="facturar">

														<input type="hidden" name="id" value="'.$id.'">

														<input type="hidden" name="mes_facturar" value="'.$j.'">

														<input type="hidden" name="anio_facturar"value="'.$k.'">

														<input type="submit" class="button" name="refresh" value="Facturar">

													</form>

												</td>';

											}

										}else{

											print '<td align="center" >';
											 print '</td>';

										}



										$ban=0;

									print '</tr>';

									print '<tr class="pair">';

										print '<td align="left" colspan=9>Total Ganancia</td>';

										print '<td align="right"  style="'.(($total_neto>0)?"color:green;":"color:red;").'">'.price($total_neto).'</td><td></td>';

									print '</tr>';

									$db->free($result);

								}else{

									//$total_neto=$total_neto+$total_ganancia;

									if($total_neto>0){

										print '<table class="noborder" width="100%">';

										print '<tr class="liste_titre">';

											print '<td width="75%">Mes</td>';

											print '<td align="right" >% de Ganancia</td>';

											print '<td align="center" >Generar Factura</td>';

										print '</tr>';

										print '<tr class="impair">

												<td colspan=2>'.$meses[$j]." ".$k.'</td>

												<td ></td>

											   </tr>';

										print '<tr class="pair">';

											print '<td align="left">Total Ganancia</td>';

											print '<td align="right"  style="'.(($total_neto>0)?"color:green;":"color:red;").'">'.price($total_neto).'</td>';

											$sab='

											SELECT a.fk_factura FROM llx_corte_mensual as a

												INNER JOIN llx_facture_fourn as b on a.fk_factura=b.rowid

											WHERE a.mes='.$j.' AND a.anio='.$k.' AND a.fk_cuenta='.$id;

											

											$bot=$db->query($sab);

											
											
											if ($bot && $total_cred>0) {

												$camp = $db->num_rows($bot);

												if ($camp>0) {

													$bot2 = $db->fetch_object($bot);	

													$fact=new FactureFournisseur($db);

													$fact->fetch($bot2->fk_factura);

													print '<td rowspan="'.($num).'" align="center" style="vertical-align: center;"  >';
																print $fact->getNomUrl(0);		
													 print '</td>';
													
													 
												}else{

													print '

													<td rowspan="'.($num).'" align="center" style="vertical-align: center;" >

													<form action="'.$_SERVER["PHP_SELF"].'" method="post">

														<input type="hidden" name="action"  value="facturar">

														<input type="hidden" name="id" value="'.$id.'">

														<input type="hidden" name="mes_facturar" value="'.$j.'">

														<input type="hidden" name="anio_facturar"value="'.$k.'">

														<input type="submit" class="button" name="refresh" value="Facturar">

													</form>

													</td>';

												}

											}else{

												print '<td align="center" >';

																

												 print '</td>';

											}

										print '</tr>';



										print '</table>';

									}

									

								}

							}

							

						}

					}

					llxFooter();

				}else{

					print '

					<div class="error">Acceso denegado.<br>

					

						Su porcentage de inversión es nulo<br>

						Regrese a la configuracion del tercero y elija un porcentaje valido. 

					</div>';

				}

			}else{

				print '

					<div class="error">Acceso denegado.<br>

					

						Su cuenta no esta vinculada a ningun tercero.<br>

					</div>';

			}

		}else{

			print '

				<div class="error">Acceso denegado.<br>

				

					Su cuenta no esta vinculada a ningun tercero.<br>

				</div>';

		}

	}

}

else

{

	print $langs->trans("ErrorBankAccountNotFound");

}







$db->close();

