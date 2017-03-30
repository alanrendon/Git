<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
dol_include_once('cclinico/lib/mpdf/mpdf.php');
global $mysoc;


dol_include_once('/cclinico/class/antecedentes.class.php');
dol_include_once('/cclinico/class/pacientes.class.php');
dol_include_once('/cclinico/class/consultas.class.php');
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$id			= GETPOST('id','int');
$aid		= GETPOST('aid','int');
if ($user->rights->cclinico->crear){

	if ($id>0) {
		$form = new Form($db);	
		$consulta= new Consultas($db);
		$consulta->fetch($id);
		$medico=new User($db);
		$medico->fetch($consulta->fk_user_med);
		$paciente=new Pacientes($db);
		$paciente->fetch($consulta->fk_user_pacientes);


		$pdf=new mPDF('c','A4','','',5,5,13.5,8);

		//$pdf->debug = true;
		//$pdf->showImageErrors = true;


		$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
		{
			$urllogo= $conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini;
		}
		else
		{
			$urllogo='../theme/dolibarr_logo.png';
		}


		$html.= ' 

			<table class="noborder" style="font-size:13px;" >
			 	<tr >
				 	<td rowspan=3 style="width:200px;" ><img src="'.$urllogo.'" style="max-width:200px; height:50px;"></td>
				 	<td align="center" style="width:400px;" >
				 		<strong>'.(($medico->gender=='man')?'Dr.':'Dr(a).').$medico->firstname.' '.$medico->lastname.'</strong>
				 	</td>
				 	<td align="center"></td>
			 	</tr>
			 	<tr >
				 	<td align="center" >';
					 	if ($medico->array_options["options_cedula_prof"]) {
					 		$html.='Cédula Profesional: '.$medico->array_options["options_cedula_prof"];
					 	}
					$html.='
				 	</td>
				 	<td align="center"></td>
			 	</tr>
			 	<tr >
				 	<td align="center" >'.$medico->job.'</td>
				 	<td align="right" style="padding-left:10px;"><strong>Folio:</strong>'.str_replace('!', '',$consulta->Ref).'</td>
			 	</tr>
			</table>
			<div style="border:1px solid rgb(83,155,193);"></div>

		';
		$date = new DateTime($consulta->date_consultation);
		$html.= '
		<div  style="border:1px solid rgb(83,155,193);">
			<div  style="border:1px solid rgb(83,155,193); font-size:13px; ">
				<div >
					<div style="word-wrap: break-word; width:65%; float: left; " >
						Nombre del Paciente: <strong>'.$paciente->firstname.' '.$paciente->lastname.'</strong>
					</div>
					<div style="word-wrap: break-word; width:33%; float: left;  " >
						Fecha de consulta: <strong>'.dol_print_date($consulta->date_consultation,'%d/%m/%Y').'</strong>
					</div>
					<div style="word-wrap: break-word; width:100px; float: left; " >&nbsp;&nbsp;
					</div>
					<div style="word-wrap: break-word; width:95px; float: left; " >
						Edad: '.$paciente->edad.' Años
					</div>
					<div style="word-wrap: break-word; width:95px; float: left; " >
						Peso: <strong>'.$consulta->weight.' Kgs. </strong>
					</div>
					<div style="word-wrap: break-word; width:140px; float: left; " >
						Estatura: <strong>'.$paciente->estatura.' Mts.</strong> 
					</div>
					<div style="word-wrap: break-word; width:140px; float: left; " >
						Presión Arterial: <strong>'.$consulta->blood_pressure.'</strong>
					</div>
					<div style="word-wrap: break-word; width:140px; float: left; " >
						Temperatura: <strong>'.$consulta->temperature.'</strong>
					</div>';

					if ($consulta->array_options["options_temp_con"]) {
				 		$html.='
				 		<div style="word-wrap: break-word; width:140px; float: left; " >
							Temperatura: <strong>'.round($consulta->array_options["options_temp_con"],2).'</strong>
						</div>';
				 	}

				$html.= '
				</div>
			</div> 
		</div>
		<br>
		<div style=" height:155px; text-align : justify; font-size:13px;" >
			<div style="word-wrap: break-word; width:100%; float: left; " >
				Receta:
			</div>
			<div style="text-align: justify; height:102px; padding-left:10px; word-wrap: break-word; font-size:11px; width:100%; float: left; " ><pre>'.$consulta->treatments.'</pre></div>
		</div>';
		if ($conf->global->CONSULTA_REQUIRED) {
			$html.='
			<div style="word-wrap: break-word; width:100%; float: left; font-size:13px;" >
				Notas Médicas:
			</div>
			<div style="height:162px; text-align: justify; padding-left:10px; word-wrap: break-word; font-size:11px; width:65%; float: left; " ><pre>'.$consulta->comments.'</pre></div>
			<div style="word-wrap: break-word; width:100%; float: left; font-size:13px;" ></div>
			';
			$top=320;
		}else{
			$top=140;
		}


		$html.='
		<div style="height:185px;  width:29.5%; position: absolute;left: 540px; top: '.$top.'px; font-size:13px; " >
			<div style="text-align:center; font-family: Courier;  width:100%;" ><br><br><br><br><br><br>'.(($medico->gender=='man')?'Dr.':'Dr(a).').$medico->firstname.' '.$medico->lastname.'<br><br><br><br><br></div>
			<div style="width:100%; border-top:1px solid rgb(83,155,193); text-align:center; font-family: Courier;" >Firma</div>
		</div>
		<div style="border-top:2px solid rgb(83,155,193); text-align:center; font-family: Courier; font-size:11px;">
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

		//$img='<img src="'.$urllogo.'" style="width:200px; height:50px;">';
		//print $html; 
		$pdf->writeHTML($html);
		//$pdf->pdf->IncludeJs('print(TRUE)');
		
		
		$pdf->Output('Consulta-'.$paciente->firstname.' '.$paciente->lastname.'.pdf','I');
	}



 

	if ($aid>0) {
		$form = new Form($db);	
		$paciente=new Pacientes($db);
		$paciente->fetch($aid);
		$consulta= new Consultas($db);
		$extrafields = new ExtraFields($db);
		$extralabels=$extrafields->fetch_name_optionals_label($paciente->table_element);
		$medico=new User($db);
		$medico->fetch($paciente->fk_user);
		$antecedentes= new Antecedentes($db);
		$pdf=new mPDF('c','A4','','',5,5,13.5,8);
		$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
		{
			$urllogo= $conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini;
		}
		else
		{
			$urllogo='../theme/dolibarr_logo.png';
		}


		$html.= '
			<table class="noborder" >
			 	<tr >
				 	<td rowspan=4 style="width:200px;" ><img src="'.$urllogo.'" style="max-width:200px; height:50px;"></td>
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
				 	<td align="center" style="width:350px;"><strong>Expediente Médico</strong></td>
				 	<td align="right" style="padding-left:46px;"></td>
			 	</tr>

			</table>
			<div style="border:1px solid rgb(83,155,193);"></div>
		';
		//$date = new DateTime(date($paciente->birthday));

		$html.= '
		<div  style="border:1px solid rgb(83,155,193); font-size:13px;">
			<div >
				<div style="word-wrap: break-word; width:352px; float: left;" >
					Nombre del Paciente: <strong>'.$paciente->firstname.' '.$paciente->lastname.'</strong>
				</div>
				<div style="word-wrap: break-word; width:152px; float: left;" >
					'.(($paciente->sexo==1)?'Genero.:<strong>Femenino</strong>':'Genero:<strong>Masculino</strong>').'</strong>
				</div>
				<div style="word-wrap: break-word; width:205px; float: left;" >
					Fecha de Nacimiento: <strong>'.dol_print_date($paciente->birthday,'%d/%m/%Y').'</strong>
				</div>
				<div style="word-wrap: break-word; width:505px; float: left;" >
					Dirección: <strong>'.$paciente->address.'</strong>
				</div>
				<div style="word-wrap: break-word; width:240px; float: left;" >
					Ciudad: <strong>'.$paciente->town.', '.getState($paciente->fk_departement).'</strong>
				</div>
				<div style="word-wrap: break-word; width:70px; float: left;" >
					CP.: <strong>'.$paciente->zip.'</strong>
				</div>
				<div style="word-wrap: break-word; width:90px; float: left;" >
					Tel.:<strong>'.$paciente->phone_pro.'</strong>
				</div>
				<div style="word-wrap: break-word; width:90px; float: left;" >
					Movil:<strong>'.$paciente->phone_perso.'</strong>
				</div>
				<div style="word-wrap: break-word; width:470px; float: left;" >
					Email:<strong>'.$paciente->email.'</strong>
				</div><br><br>
				<div style="word-wrap: break-word; width:100px; float: left;" >
					Edad: <strong>'.$paciente->edad.' Años</strong>
				</div>';
				if ($paciente->array_options["options_sang_con"]) {
					$html.= '
					<div style="word-wrap: break-word; width:150px; float: left;" >
						Tipo de Sangre: <strong>'.$paciente->array_options["options_sang_con"].' </strong>
					</div>';
				}
				$html.='
		 		<div style="word-wrap: break-word; width:150px; float: left;" >
 					Estatura: <strong>'.$paciente->estatura.' Mts. </strong> 
 				</div>
 				';
 				if ($paciente->array_options["options_est_civ"]) {
					$html.= '
					<div style="word-wrap: break-word; width:140px; float: left;" >
						Estado Civil: <strong>'.($paciente->array_options["options_est_civ"]==1?"Casado":"Soltero").' </strong>
					</div>';
				}
			$html.= '
				<div style="word-wrap: break-word; width:200px; float: left;" >
 					Profesion: <strong>'.$paciente->poste.'</strong>
 				</div>
 				<div style="word-wrap: break-word; width:250px; float: left;" >
 					Médico de Seguimiento: <strong>'.$medico->firstname.' '.$medico->lastname.'</strong>
 				</div>';

	 			if ($paciente->array_options["options_nom_seg_con"]) {
			 		$html.='
			 		<div style="word-wrap: break-word; width:290px; float: left;" >
	 					Nombre del Seguro: <strong>'.$paciente->array_options["options_nom_seg_con"].' </strong> 
	 				</div>';
			 	}
			 	if ($paciente->array_options["options_num_seg_con"]) {
			 		$html.='
			 		<div style="word-wrap: break-word; width:200px; float: left;" >
	 					Núm. de Seguro: <strong>'.$paciente->array_options["options_num_seg_con"].' </strong> 
	 				</div>';
			 	}
			 	$i=0;
			 	foreach ($paciente->array_options as $key => $val) {
			 		if ($i>3) {
			 			$html.='
				 		<div style="word-wrap: break-word; width:250px; float: left;" >
		 					'.$extralabels[str_replace("options_","",$key)].': <strong>'.$val.' </strong> 
		 				</div>';
			 		}
			 		
	 				$i++;
			 	}
			 	

 			$html.='
			</div>
		</div>
		<div style=" height:185px; border:1px solid rgb(83,155,193); padding:5px; font-size:13px;" >';
		$html.= '<strong>Antecedentes:</strong><br>';
		$rest=$antecedentes->listar_antecedentes($aid);
		if ($rest) {
	        $i=count($rest);
	        foreach ($rest as $key) {
	        	$medico2=new User($db);
	        	$medico2->fetch($key->fk_user_create);
	        	$html.="&nbsp;&nbsp;".$key->fecha_creacion.' - '.$medico2->lastname.' '.$medico2->firstname.' : '.$key->titulo;
	        	 $html.='
				    <div style="width:100%;word-wrap: break-word; padding-left:6px;"><pre><strong>'.$key->descripcion.'</strong></pre></div><br>';
	        }
	    }
	    $html.='</div>';
	   
		$html.= '
		<div style="width:100%; font-size:13px;">
		';
		$html.= '
		<div  style="">
			<strong>Resumen de Consultas:</strong><br><br>';
			$rest=$consulta->listar_consultas($aid);
			if ($rest) {
		        $i=count($rest);
		        foreach ($rest as $key) {
		        	if ($key->statut==2 || $key->statut==3) {
		        		$html.='
		        		<div style="width:100%;">
				        	<div style="background:rgb(83,155,193);height:2px;  width:25% !important; float: left;"></div>
				        	<div style="padding-top:-9px; padding-left:5px; width:55% !important;  float: left; word-wrap: break-word;" >
				        		&nbsp;&nbsp;Fecha:
					        	<strong style="float: left;">'.date("Y/m/d H:s",strtotime($key->date_consultation)).'hrs.</strong>  - 
					        	<strong style="float: left;">'.str_replace('!', '',$key->Ref).'</strong> - Tipo de Consulta:<strong>';
								$resql1=$db->query("
								SELECT * FROM llx_c_tipo_consulta as a WHERE a.active=1 AND a.rowid=".$key->Type_consultation);
							    if ($resql1)
							    {
							        $num2 = $db->num_rows($resql1);
							        if ($num2)
							        {
							            $obj2 = $db->fetch_object($resql1);
							            $html.= $obj2->description;
							            
							        }
							    }
					        	$html.='</strong>

				        	</div>
				        	<div style="background:rgb(83,155,193);height:2px;  width:18% !important;float: left;  "></div>
			        	</div>
			        	';
			        	$medico3=new User($db);
			        	$medico3->fetch($key->fk_user_med);
			        	$html.='
			        	<div style="width:100%; font-size:13px; ">
			        		<div style="word-wrap: break-word; width:100%; float: left; " >
								Peso:<strong>'.$key->weight.' Kgs.</strong> - Presión Arterial:<strong> '.$key->blood_pressure.'</strong> - Médico que consulta: <strong>'.$medico3->firstname.' '.$medico3->lastname.'</strong>
							</div>

							<div style="word-wrap: break-word; width:14%; float: left; " >
								Motivos Consulta:
							</div>
							<div style="word-wrap: break-word; width:85%; float: left;font-size:13px; " ><strong> '.$consulta->listar_motivo_consulta($key->reason).'</strong></div>
							<div style="word-wrap: break-word; width:100%; float: left;  padding-left:13%; font-size:13px;" ><pre>'.$key->reason_detail.'</pre></div>


							<div style="word-wrap: break-word; width:14%; float: left; " >
								Diagnóstico:
							</div>
							<div style="word-wrap: break-word; width:85%; float: left; " >
								<strong> '.$consulta->listar_diagnosticos($key->diagnostics).'</strong>
							</div>
							<div style="word-wrap: break-word; width:100%; float: left;  padding-left:9%;font-size:12px; " ><pre>'.$key->diagnostics_detail.'</pre></div>


							<div style="word-wrap: break-word; width:100%; float: left; " >
								Detalles del tratamiento:
							</div>
							<div style="word-wrap: break-word; width:100%; float: left;  padding-left:17%; font-size:12px; " ><pre>'.$key->treatments.'</pre></div>


							<div style="word-wrap: break-word; width:100%; float: left; " >
								Otros comentarios:
							</div>
							<div style="word-wrap: break-word; width:100%; float: left;  padding-left:12%; font-size:13px;" ><pre>'.$key->comments.'</pre></div>
			        	</div>

			        	
						';
						$consulta2=new Consultas($db);
						$consulta2->fetch($key->rowid);
						$extrafields = new ExtraFields($db);
						$extralabels=$extrafields->fetch_name_optionals_label($consulta2->table_element);

						foreach ($extralabels as $key => $value) {
							$html.='
							<div style="word-wrap: break-word; width:13%; float: left; " >
								'.$value.':
							</div>
							<div style="word-wrap: break-word; width:86%; float: left;  " >
								'.$consulta2->array_options['options_'.$key].'
							</div>';
						}
						$html.='<br>';
		        	}
		        	
		        }
		    }
			$html.= '

		
		</div><br><br>
		<div style="border-top:2px solid rgb(83,155,193);font-size:13px; text-align:center; font-family: Courier;">
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
		//print $html;
		$modulepart='cclinico';
		$pdf->writeHTML($html);
		if (!file_exists($conf->$modulepart->dir_output.'/files/Paciente-'.$aid.'/documents')) {
		    dol_mkdir($conf->$modulepart->dir_output.'/files/Paciente-'.$aid.'/documents');
		}
		$pdf->Output($conf->$modulepart->dir_output.'/files/Paciente-'.$aid.'/documents/'.$paciente->firstname.' '.$paciente->lastname.'.pdf','F');
		$pdf->Output('Consulta-'.$paciente->firstname.' '.$paciente->lastname.'.pdf','I');
	}

}
$db->close();
