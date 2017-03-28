<?php
/* Copyright (C) 2014		charles-Fr Benke	<charles.fr@benke.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file       htdocs/customlink/class/actions_customlink.class.php
 * 	\ingroup    customlink
 * 	\brief      Fichier de la classe des actions/hooks des customlink
 */
 
class ActionsFactory // extends CommonObject 
{ 
 
		/** Overloading the doActions function : replacing the parent's function with the one below 
	 *  @param      parameters  meta datas of the hook (context, etc...) 
	 *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
	 *  @param      action             current action (if set). Generally create or edit or null 
	 *  @return       void 
	 */ 
	function printSearchForm($parameters, $object, $action) 
	{ 
		if(DOL_VERSION<'3.9'){
		global $conf,$langs;
		
		$langs->load("factory@factory");
		$title = img_object('','factory@factory').' '.$langs->trans("Factory");
		$ret='';
		$ret.='<div class="menu_titre">';
		$ret.='<a class="vsmenu" href="'.dol_buildpath('/factory/list.php',1).'">';
		$ret.=$title.'</a><br>';
		$ret.='</div>';
		$ret.='<form action="'.dol_buildpath('/factory/list.php',1).'" method="post">';
		$ret.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$ret.='<input type="text" class="flat" ';
		if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $ret.=' placeholder="'.$langs->trans("SearchOf").''.strip_tags($title).'"';
		else $ret.=' title="'.$langs->trans("SearchOf").''.strip_tags($title).'"';
		$ret.=' name="tag" size="10" />&nbsp;';
		$ret.='<input type="submit" class="button" value="'.$langs->trans("Go").'">';
		$ret.="</form>\n";
		$this->resprints=$ret;
		return 0;
		}
	}

	function addMoreActionsButtons($parameters, $object, $action) 
	{

		global $user;
		if (in_array('propalcard', explode(':', $parameters['context'])))
		{
			if ($object->statut==0) {
				print '<div class="inline-block divButAction"><a style="color:rgb(0,0,120) !important;" class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&saction=create">Previsualización</a></div>';
			}
			if ($user->rights->factory->add_factory && $object->statut>1) {

				print '<div class="inline-block divButAction"><a style="color:rgb(0,0,120) !important;" class="butActionDelete" href="
				../factory/factory_list.php">Listado de producción</a></div>';				
			}
		}
		
	}
	function formAddObjectLine($parameters, $object, $action) 
	{
		global $db;

		$saction=GETPOST("saction");
		if ($saction=="create") {
			print '<tr><td colspan=7><br></td></tr>';

			print '<tr style="border:1px solid #E0E0E0;">';
			print '<td colspan=7 valign="top" style="width:265px; border-top:1px solid #E0E0E0;">Inserte la clave especifica del proyecto</td>';
			print '<td  valign="top" style="border-top:1px solid #E0E0E0;">';
			$sql='SELECT
				SUBSTR(a.ref, 1, 8) as clave
			FROM
				llx_product AS a
			WHERE SUBSTR(a.ref, 1, 5) REGEXP "^[0-9]+$"
			AND (SUBSTR(a.ref, 6, 1) = "_" OR SUBSTR(a.ref, 6, 1) = "-")
			AND SUBSTR(a.ref, 7, 2) REGEXP "^[A-Z]"
			GROUP BY SUBSTR(a.ref, 1, 8)';
			$query=$db->query($sql);
			print '
			<select class="flat" id="select_clave" name="select_clave">';
				while ($obj = $db->fetch_object($query)) {
					print '<option value="'.$obj->clave.'">'.$obj->clave.'</option>';
				}
			print '
			</select>';
			print '</td>
			<td style="border-top:1px solid #E0E0E0;">
				<input type="submit" class="button" value="Cargar" name="cargar_clave" id="cargar_clave">
			</td>
			</tr>';
		}
		if (isset($_POST["cargar_clave"]) && isset($_POST["select_clave"])) {
			require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			$sql = 'SELECT 
					a.rowid,
					e.total_prod
				FROM 
					llx_product as a 
				LEFT JOIN 
					llx_product_extrafields AS e ON a.rowid = e.fk_object
				WHERE 
					a.ref 
				LIKE "%'.GETPOST("select_clave").'%"';
			$query=$db->query($sql);
			$n=$db->num_rows($query);
			print "\n".'<script type="text/javascript" language="javascript">'."\n";
            print 'jQuery(document).ready(function () {
                        jQuery(".check_in").click(function (){
                            var price=0;
                        	$( ".check_in:checked" ).each(function( index ) {
                        		var cant=parseInt($("#qty_"+$(this).val()).val() );
                        		
								price=parseFloat(price)+(parseFloat($(this).attr("price"))*cant);
								
							});
							$("#price_total").html(price);
                        });
                        jQuery(".qty_in").keyup(function (){
                            var price=0;
                        	$( ".check_in:checked" ).each(function( index ) {
                        		var cant=parseInt($("#qty_"+$(this).val()).val() );
                        		
                        		if (isNaN(cant)) {
                        			cant=0;
                        		}
								price=parseFloat(price)+(parseFloat($(this).attr("price"))*cant);
								
							});
							$("#price_total").html(price);
                        });
                        
                    })';
            print '</script>';



			print '<tr><td><br></td></tr>';

			print '<tr class="liste_titre nodrag nodrop">';
			print '<td colspan=5><span class="hideonsmartphone">Nombre del proyecto</span>';
			print '</td>';
			print '<td align="center"><span class="hideonsmartphone">IVA</span>';
			print '</td>';
			print '<td align="right"><span class="hideonsmartphone">P.U.</span>';
			print '</td>';
			print '<td align="center"><span class="hideonsmartphone">Cant.</span>';
			print '</td>';
			print '<td align="center" > <span class="hideonsmartphone">Selección</span>';
			print '</td>';
			print '</tr>';
			$sum=0;
			$form = new Form($db);
			while ($obj = $db->fetch_object($query)) {
				$object = new Product($db);
				$object->fetch($obj->rowid);
				$obj->total_prod = (empty($obj->total_prod )) ? 1 : $obj->total_prod;
				$typeselect="impair";
				print '<tr class="'.$typeselect.'">';
				print '<td colspan=5>';
					print $object->getNomUrl(1);
				print '</td>';
				print '<td align="center">';
					print $form->load_tva("tva_".$obj->rowid, "16", $mysoc, '');
				print '</td>';
				print '<td align="center">';
					print price($object->price);
					$sum+=$object->price;
				print '</td>';
				print '<td align="right">';
					print '<input type="text" size="2" name="qty_'.$obj->rowid.'" id="qty_'.$obj->rowid.'" class="flat qty_in" value="'.$obj->total_prod.'">';
				print '</td>';
				print '<td align="center" >';
					print '<input type="checkbox" class="check_in" id="check_'.$obj->rowid.'" name="type_select[]" value="'.$obj->rowid.'" price="'.$object->price.'" checked>';
				print '</td>';
				print '</tr>';
				$typeselect="pair";
			}
			print '<tr class="'.$typeselect.'">';
			print '<td colspan=6><span class="hideonsmartphone">Precio total</span>';
			print '</td>';
			print '<td align="right"><span class="hideonsmartphone" id="price_total">'.price($sum).'</span>';
			print '</td>';
			print '<td align="center" colspan=3>
						<input type="submit" class="button" value="Añadir" name="add_elements" id="add_elements">';
			print '</td>';
			print '</tr>';
		}

	}

	function doActions($parameters, $object, $action) 
	{
		

		$action= $_POST['action'];		

		if(strcmp($action, 'setstatut')==0){
			$this->validarStock($object->id);
		}		

		if(strcmp($action, 'builddoc')==0){
			$id=$_GET["id"];			
			print "<script type='text/javascript'>				
				window.location = '".DOL_URL_ROOT."/comm/pdf/index.php?id=".$id."';		
			</script>";
		}
		global $db;
		
		if (isset($_POST["add_elements"]) && isset($_POST["type_select"])) {
			require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			foreach ($_POST["type_select"] as $id ) {
				$producto = new Product($db);
				$producto->fetch($id);
				$cant=GETPOST("qty_".$id,"int");
				$iva=GETPOST("tva_".$id,"int");
				$price=$producto->price*$cant;
				$object->addline(
					"", 
					$producto->price, 
					$cant, 
					$iva, 
					0, 
					0, 
					$producto->id, 
					0, 
					'HT'
				);
			}
		}
		
	}

	function validarStock($id){
		global $db;		
		
		$fact=new Factory($db);
		$need= $fact->get_qty_propal($id);
		$pend= $fact->get_qty_propal_pen($id);		
		
		if($pend<=0){
			$string='UPDATE  llx_propal set fk_status_factory=1 where rowid='.$id;
			$query=$db->query($string);	
		}else{
			$string='UPDATE  llx_propal set fk_status_factory=0 where rowid='.$id;
			$query=$db->query($string);
		}
		
	}

	// function validarStock($id){
	// 	global $db;		

	// 	$sql1='SELECT	a.fk_product, a.qty
	// 	FROM	llx_propaldet AS a		
	// 	WHERE	a.fk_propal ='.$id;
	// 	$rq1 = $db->query($sql1);	
	// 	$band=0;
		
	// 	while ( $rs1 = $db->fetch_object($rq1) ) {
	// 		//Cantidad  solicitada
	// 		$sql2='SELECT
	// 				t.qty, t3.ref, t.fk_product_children
	// 				FROM
	// 					llx_product_factory AS t					
	// 				INNER JOIN llx_product AS t3 ON t.fk_product_children = t3.rowid
	// 				WHERE
	// 					t.fk_product_father ='.$rs1->fk_product;
																				
	// 		$rq2 = $db->query($sql2);	
	// 		while ($rs2= $db->fetch_object($rq2)) {
	// 			$resp=substr($rs2->ref, 0, 3);
	// 			if((strcmp($resp, 'TRA')==0 || strcmp($resp, 'tra')==0) || strcmp($rs2->ref, 'cxhcnc')==0 || strcmp($rs2->ref, 'cxhdm')==0 || strcmp($rs2->ref, 'cxhr') ==0){
	// 			}else{	
	// 				//Cantidad  existente	
	// 				$sql3='SELECT
	// 						SUM(c.reel) AS suma
	// 					FROM
	// 						llx_product_stock AS c
	// 					WHERE
	// 						c.fk_product ='.$rs2->fk_product_children;	

	// 				$rq3 = $db->query($sql3);	
	// 				$rs3= $db->fetch_object($rq3);					
					
	// 				$solic=$rs2->qty*$rs1->qty;				

	// 				if($solic > $rs3->suma){						
	// 					$band=1;						
	// 				}
	// 			}
	// 		}	
	// 	}			
		
	// 	if($band==0){
	// 		$string='UPDATE  llx_propal set fk_status_factory=1 where rowid='.$id;
	// 		$query=$db->query($string);	
	// 	}else{
	// 		$string='UPDATE  llx_propal set fk_status_factory=0 where rowid='.$id;
	// 		$query=$db->query($string);
	// 	}
		
	// }
}
?>