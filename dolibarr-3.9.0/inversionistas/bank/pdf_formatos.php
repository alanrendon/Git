<?php





require '../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

dol_include_once('inversionistas/lib/mpdf/mpdf.php');

require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

global $mysoc;





$langs->load("banks");

$langs->load("categories");

$langs->load("bills");

$langs->load("companies");

$langs->load("salaries");

$langs->load("loan");

$langs->load("donations");

$langs->load("trips");



$mesg=''; $error=0; $errors=array();



$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');

$id			= GETPOST('id','int');

$aid		= GETPOST('aid','int');



if ($id>0) {

	$form = new Form($db);	

	

	$object = new Account($db);

	$result=$object->fetch($id);





	$pdf=new mPDF('c','A4','','',5,5,13.5,8);

	$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;

	if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))

	{

		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_mini);

	}

	else

	{

		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';

	}

	



	$urllogo= $_SERVER['DOCUMENT_ROOT'].$urllogo;

	$html.= '

		<table width="100%" >

		 	<tr >

			 	<td rowspan=4><img src="'.$urllogo.'" style="width:200px; height:50px;"></td>

			 	<td align="center" style="width:350px;">

			 		<strong>'.(empty($conf->global->MAIN_INFO_SOCIETE_NOM)?'':$conf->global->MAIN_INFO_SOCIETE_NOM).'</strong>

			 	</td>

			 	<td align="center"></td>

		 	</tr>

		 	<tr >

			 	<td align="center" style="width:410px;">'

			 	.(empty($conf->global->MAIN_INFO_SIREN)?'':'RFC.: '.$conf->global->MAIN_INFO_SIREN).

			 	'	

			 	</td>

			 	<td align="center"></td>

		 	</tr>

		 	<tr >

			 	<td align="center" ><br></td>

			 	<td align="right" ></td>

		 	</tr>

		 	<tr >

			 	<td align="center" style="width:350px;"></td>

			 	<td align="right" style="padding-left:46px;"></td>

		 	</tr>



		</table>

		

	';

	





	$sql_porcentage="SELECT a.rowid,a.fk_tercero,a.date_creation FROM llx_terceros_cuenta as a WHERE a.fk_cuenta=".$id;



	$societestatic=new Societe($db);

	$resultado=$db->query($sql_porcentage);

	if ($resultado){



		$numero = $db->num_rows($resultado);

		if ($numero>0) {

			$tercero_camps = $db->fetch_object($result);

			$societestatic->fetch($tercero_camps->fk_tercero);

			if ($societestatic->array_options["options_inv001"] && $societestatic->array_options["options_inv001"]==1) {

				

				if ($societestatic->array_options["options_inv002"] && is_numeric($societestatic->array_options["options_inv002"])  ) {

					

					//$anio= GETPOST("anio");



					

					$inicio=date('Y', strtotime(str_replace('/', '-',$tercero_camps->date_creation)));

					$final= date("Y",dol_now());

					$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',

               						'Agosto','Septiembre','Octubre','Noviembre','Diciembre');	



					

					$total_global=0;

					$total_ganancia=0;

					$total_neto=0;





					$fecha_inicio= GETPOST("fecha_inicio");

					$fecha_final= GETPOST("fecha_final");

					$var=1;

					$var2="";

					



					if (!empty($fecha_inicio) && !empty($fecha_final) ) {



						$array1=explode("-", $fecha_inicio);

						$array2=explode("-", $fecha_final);

						$inicio=$array1[0];

						$final=$array2[0];

						$var=$array1[1];

						$var2=$array2[1];

						

					}





					for ($k= $inicio ; $k <=$final  ; $k++) {


						
						

							if ($k==$final) {

								$actuam_mes= date("m",dol_now());

							}else{

								$actuam_mes= 12;

							}

						


						
						for ($j=$var; $j <=$actuam_mes ; $j++) { 

							

							$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv,";

							$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type, b.fk_bordereau,";

							$sql.= " ba.rowid as bankid, ba.ref as bankref, ba.label as banklabel,YEAR(b.datev) as anio";

							

							$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";

							$sql.= ", ".MAIN_DB_PREFIX."bank as b";

							

							$sql.= " WHERE b.fk_account=".$object->id;

							$sql.= " AND b.fk_account = ba.rowid";

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

								//$var3=true;

								$num = $db->num_rows($result);

								$i = 0; $total = 0; $sep = -1; $total_deb=0; $total_cred=0;

								if ($num>0) {

									$html.= '<table   width="100%">';

									$html.= '<tr >';

									$html.= '<td style="width:100px; background:#ececed;" >Mes</td>';

									$html.= '<td style="width:100px; background:#ececed;" >'.$langs->trans("Type").'</td>';

									$html.= '<td style="width:90px; background:#ececed;" >'.$langs->trans("Description").'</td>';

									$html.= '<td style="width:90px; background:#ececed;"  align="right">Debe</td>';

									$html.= '<td style="width:90px; background:#ececed;"  align="right">Haber</td>';

									$html.= '<td style="width:90px; background:#ececed;"  align="right" ></td>';

									$html.= '<td style="width:100px; background:#ececed;"  align="right" >% de Ganancia</td>';



									$html.= '</tr>';

								

									$ban=1;



									while ($i < $num)

									{

										$objp = $db->fetch_object($result);

										$total = price2num($total + $objp->amount,'MT');

										if ($i >= ($viewline * (($totalPages-$page)-1)))

										{

											$var3=!$var3;

											$dos=dol_print_date($db->jdate($objp->do),'%Y%m%d');

											//$html.= "dos=".$dos." nows=".$nows;

											if ($dos < $nows) $sep=0;		// 0 means there was at least one line before current date

											if ($dos > $nows && ! $sep){

												$sep = 1 ;

												$html.= '<tr class="liste_total">



												<td colspan="8" >';

												$html.= $langs->trans("CurrentBalance");

												$html.= '</td>';

												$html.= '<td align="right" class="nowrap"><b>'.price($total - $objp->amount).'</b></td>';

												$html.= "<td style='background:#fafafa;' >&nbsp;</td>";

												$html.= '</tr>';

											}



											$html.= '<tr '.$bc[$var3].'>';



											if ($ban==1) {

												$html.= '<td  rowspan="'.($num).'" style="width:150px; background:#fafafa;">'.$meses[$j].' '.$objp->anio.'</td>';

												$ban=0;

											}





											// Payment type

											$html.= '<td class="nowrap" style="width:150px; background:#fafafa">';

											$label=($langs->trans("PaymentTypeShort".$objp->fk_type)!="PaymentTypeShort".$objp->fk_type)?$langs->trans("PaymentTypeShort".$objp->fk_type):$objp->fk_type;



											if ($objp->fk_type == 'SOLD') $label='&nbsp;';

											if ($objp->fk_type == 'CHQ' && $objp->fk_bordereau > 0) 

											{

												dol_include_once('/compta/paiement/cheque/class/remisecheque.class.php');

												$bordereaustatic = new RemiseCheque($db);

												$bordereaustatic->id = $objp->fk_bordereau;

												$label .= ' '.$bordereaustatic->getNomUrl(2);

											}

											$html.= $label;

											$html.= "</td>\n";



											

											// Description

											$html.= '<td style="width:150px !important; background:#fafafa;  text-overflow: ellipsis;">';
												if (preg_match('/^\((.*)\)$/i',$objp->label,$reg))
												{
													$html.= $langs->trans($reg[1]);

												}
												else

												{

													$html.= dol_trunc($objp->label,60);

												}

												

												// Add links after description

												$links = $object->get_url($objp->rowid);

												foreach($links as $key=>$val3)

												{

													if ($links[$key]['type']=='payment')

													{

														$paymentstatic->id=$links[$key]['url_id'];

														$paymentstatic->ref=$links[$key]['url_id'];

														$html.= ' '.$paymentstatic->getNomUrl(2);

													}

													/*elseif ($links[$key]['type']=='payment_supplier')

													{

														$paymentsupplierstatic->id=$links[$key]['url_id'];

														$paymentsupplierstatic->ref=$links[$key]['url_id'];

														$html.= ' '.$paymentsupplierstatic->getNomUrl(2);

													}*/

													elseif ($links[$key]['type']=='payment_sc')

													{

														$html.= '<a href="'.DOL_URL_ROOT.'/compta/payment_sc/card.php?id='.$links[$key]['url_id'].'">';

														$html.= ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

														//$html.= $langs->trans("SocialContributionPayment");

														$html.= '</a>';

													}

													elseif ($links[$key]['type']=='payment_vat')

													{

														$paymentvatstatic->id=$links[$key]['url_id'];

														$paymentvatstatic->ref=$links[$key]['url_id'];

														$html.= ' '.$paymentvatstatic->getNomUrl(2);

													}

													elseif ($links[$key]['type']=='payment_salary')

													{

														$paymentsalstatic->id=$links[$key]['url_id'];

														$paymentsalstatic->ref=$links[$key]['url_id'];

														$html.= ' '.$paymentsalstatic->getNomUrl(2);

													}

													elseif ($links[$key]['type']=='payment_loan')

													{

														$html.= '<a href="'.DOL_URL_ROOT.'/loan/payment/card.php?id='.$links[$key]['url_id'].'">';

														$html.= ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

														$html.= '</a>';

													}

													elseif ($links[$key]['type']=='payment_donation')

													{

														$html.= '<a href="'.DOL_URL_ROOT.'/don/payment/card.php?id='.$links[$key]['url_id'].'">';

														$html.= ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

														$html.= '</a>';

													}

													elseif ($links[$key]['type']=='payment_expensereport')

													{

														$html.= '<a href="'.DOL_URL_ROOT.'/expensereport/payment/card.php?id='.$links[$key]['url_id'].'">';

														$html.= ' '.img_object($langs->trans('ShowPayment'),'payment').' ';

														$html.= '</a>';

													}

													elseif ($links[$key]['type']=='banktransfert')

													{

														// Do not show link to transfer since there is no transfer card (avoid confusion). Can already be accessed from transaction detail.

														if ($objp->amount > 0)

														{

															$banklinestatic->fetch($links[$key]['url_id']);

															$bankstatic->id=$banklinestatic->fk_account;

															$bankstatic->label=$banklinestatic->bank_account_label;

															$html.= ' ('.$langs->trans("TransferFrom").' ';

															$html.= $bankstatic->getNomUrl(1,'transactions');

															$html.= ' '.$langs->trans("toward").' ';

															$bankstatic->id=$objp->bankid;

															$bankstatic->label=$objp->bankref;

															$html.= $bankstatic->getNomUrl(1,'');

															$html.= ')';

														}

														else

														{

															$bankstatic->id=$objp->bankid;

															$bankstatic->label=$objp->bankref;

															$html.= ' ('.$langs->trans("TransferFrom").' ';

															$html.= $bankstatic->getNomUrl(1,'');

															$html.= ' '.$langs->trans("toward").' ';

															$banklinestatic->fetch($links[$key]['url_id']);

															$bankstatic->id=$banklinestatic->fk_account;

															$bankstatic->label=$banklinestatic->bank_account_label;

															$html.= $bankstatic->getNomUrl(1,'transactions');

															$html.= ')';

														}

														//var_dump($links);

													}else

													{

														// Show link with label $links[$key]['label']

														if (! empty($objp->label) && ! empty($links[$key]['label'])) $html.= ' - ';

														$html.= '<a href="'.$links[$key]['url'].$links[$key]['url_id'].'">';

														if (preg_match('/^\((.*)\)$/i',$links[$key]['label'],$reg))

														{

															// Label generique car entre parentheses. On l'affiche en le traduisant

															if ($reg[1]=='paiement') $reg[1]='Payment';

															$html.= ' '.$langs->trans($reg[1]);

														}

														else

														{

															$html.= ' '.$links[$key]['label'];

														}

														$html.= '</a>';

													}

												}

											$html.= '</td>';



										



											// Amount

											if ($objp->amount < 0)

											{

												$html.= '<td align="right" class="nowrap" style="width:150px; background:#fafafa; word-wrap: break-word;">'.price($objp->amount * -1).'</td><td style="background:#fafafa;">&nbsp;</td>'."\n";

												$total_deb +=$objp->amount;

											}

											else

											{

												$html.= '<td style="width:150px; background:#fafafa;">&nbsp;</td><td align="right" class="nowrap" style="width:150px; background:#fafafa; word-wrap: break-word;">&nbsp;'.price($objp->amount).'</td>'."\n";

												$total_cred +=$objp->amount;

											}



											// Balance

	

											if ($total >= 0)

											{

												$html.= '<td align="right" style="width:150px; background:#fafafa; word-wrap: break-word;" class="nowrap">&nbsp;'.price($total).'</td>';

											}

											else

											{

												$html.= '<td align="right" style="width:150px; background:#fafafa; word-wrap: break-word;" class="nowrap">&nbsp;'.price($total).'</td>';

											}

											



											if ($objp->amount < 0){

												$total_deb +=$objp->amount;
												$html.= '<td align="right" style="width:150px; background:#fafafa; word-wrap: break-word;" class="nowrap" ></td>'."\n";
											}

											else

											{

												$html.= '<td align="right" style="width:150px; background:#fafafa; word-wrap: break-word;" class="nowrap" >&nbsp;'.price($objp->amount/100*$societestatic->array_options["options_inv002"]).'</td>'."\n";

												$total_cred +=$objp->amount;
											}

											$html.= "</tr>";

										}

										$i++;

									}
									//Real account situation

									$html.= '<tr class="liste_total"><td align="left" colspan="5" style="width:150px; background:#fcfcfc;">';

									if ($sep > 0) $html.= '&nbsp;';	// If we had at least one line in future

									else $html.= $langs->trans("CurrentBalance");

									$html.= ' '.$object->currency_code.'</td>';



									$html.= '<td align="right" class="nowrap" syle="background:#fcfcfc word-wrap: break-word;"><b>'.price($total).'</b></td>';

									$html.= '<td align="right" class="nowrap" style=" background:#fcfcfc;'.((price($total/100*$societestatic->array_options["options_inv002"])>0)?"color:green;":"color:red;").'"><b>'.price($total/100*$societestatic->array_options["options_inv002"]).'</b></td>';



									

									$total_ganancia=$total/100*$societestatic->array_options["options_inv002"];

									$total_neto=$total_neto+$total_ganancia;

									if ($total_cred==0) {

										$total_neto=0;

									}



									$html.= '</tr>';

									$html.= '<tr class="liste_total"><td align="left" colspan="6" style="width:150px; background:#fcfcfc">Total Ganancia</td>';



									$html.= '<td align="right" class="nowrap" style="background:#fcfcfc; '.(($total_neto>0)?"color:green;":"color:red;").'"><b>'.price($total_neto).'</b></td>';

									$html.= '</tr></table><br><br>';

									$db->free($result);

								}else{

									//$total_neto=$total_neto+$total_ganancia;

									if ($total_neto!=0) {

										$html.= '<table  class="noborder" style="font-size:13px;" width="100%" >';



										// Ligne de titre tableau des ecritures
										$html.= '<tr >';

											$html.= '<td style="width:70%; background:#ececed;" >Mes</td>';

											$html.= '<td style="width:29%; border-top:1px solid black !important; background:#ececed;"  align="right" >% de Ganancia</td>';

										$html.= '</tr>';

										$html.= '<tr class="liste_titre">';

											$html.= '<td  align="left" style="width:150px; background:#fcfcfc;">'.$meses[$j]." ".$k.'</td>';
										$html.= '</tr>';

										$html.= '<tr >';

											$html.= '<td align="left" style="width:150px; background:#fcfcfc;">Total Ganancia</td>';

											$html.= '<td align="right"   style="background:#fcfcfc; '.(($total_neto>0)?"color:green;":"color:red;").'">'.price($total_neto).'</td>';

										$html.= '</tr>';

									



										$html.= '</table><br><br>';

									}

								}

							}

						}
						$var2="";

					}

				}else{

					$html.= '

					<div class="error">Acceso denegado.<br>
						Su porcentage de inversión es nulo<br>
						Regrese a la configuracion del tercero y elija un porcentaje valido. 

					</div>';

				}

			}else{

				$html.= '

					<div class="error">Acceso denegado.<br>

						Su cuenta no esta vinculada a ningun tercero.<br>

					</div>';

			}

		}else{

			$html.= '

				<div class="error">Acceso denegado.<br>

				

					Su cuenta no esta vinculada a ningun tercero.<br>

				</div>';

		}

	}





	$html.= '

	<div style="border-top:2px solid rgb(83,155,193); text-align:center; font-family: Courier; font-size:13px;">

		'.(empty($conf->global->MAIN_INFO_SOCIETE_ADDRESS)?'':$conf->global->MAIN_INFO_SOCIETE_ADDRESS) .

		  (empty($conf->global->MAIN_INFO_SOCIETE_TOWN)?'': ', '.$conf->global->MAIN_INFO_SOCIETE_TOWN).

		  (empty($conf->global->MAIN_INFO_SOCIETE_STATE)?'': ', '.getState($conf->global->MAIN_INFO_SOCIETE_STATE)).

		  (empty($mysoc->country_code)?'': ', '.str_replace('MX - ', '',getCountry($mysoc->country_code,1))).

		  '<br>

		  '.(empty($conf->global->MAIN_INFO_SOCIETE_TEL)?'': dol_print_phone($conf->global->MAIN_INFO_SOCIETE_TEL,$mysoc->country_code)).

		  (empty($conf->global->MAIN_INFO_SOCIETE_MAIL)?'': ', '.$conf->global->MAIN_INFO_SOCIETE_MAIL).

		  (empty($conf->global->MAIN_INFO_SOCIETE_WEB)?'': ', '.$conf->global->MAIN_INFO_SOCIETE_WEB).

		  '

	</div>

	';

	$pdf->writeHTML($html);

	//$pdf->pdf->IncludeJs('print(TRUE)');

	$pdf->Output('Expediente.pdf',"I");

	

}





$db->close();

