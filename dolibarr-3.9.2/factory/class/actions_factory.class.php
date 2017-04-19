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
		global $db,$langs,$conf;


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
		if (isset($_REQUEST["cargar_clave"]) && isset($_REQUEST["select_clave"])) {
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
            
            require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
            $object2 = new Product($db);
			$extrafields = new ExtraFields($db);

			// fetch optionals attributes and labels
			$extralabels=$extrafields->fetch_name_optionals_label($object2->table_element);



			print '<tr><td><br></td></tr></table>
			<br>
			<table class="border" width="100%">';

			print '<tr class="liste_titre nodrag nodrop">';
			print '<td ><span class="hideonsmartphone">Nombre del proyecto</span>';
			print '</td>';
			if (! empty($extrafields->attribute_label))
        	{
        		print '<td align="center" ><span class="hideonsmartphone">'.$extrafields->attribute_label["anc"].'</span>';
				print '</td>';
				print '<td align="center" ><span class="hideonsmartphone">'.$extrafields->attribute_label["lar"].'</span>';
				print '</td>';
				print '<td align="center" ><span class="hideonsmartphone">MP</span>';
				print '<td align="center" ><span class="hideonsmartphone">TRA</span>';
				print '</td>';
				print '<td align="center" ><span class="hideonsmartphone">HM</span>';
				print '</td>';
				print '<td align="center" ><span class="hideonsmartphone">HE</span>';
				print '</td>';
				print '<td align="center" ><span class="hideonsmartphone">HR</span>';
				print '</td>';
			}
			
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
				
				$object2->fetch($obj->rowid);
				$obj->total_prod = (empty($obj->total_prod )) ? 1 : $obj->total_prod;
				$typeselect="impair";
				print '<tr class="'.$typeselect.'">';
				print '<td >';
					//print $object2->getNomUrl(1);$_POST["select_clave"]
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&select_clave='.$_REQUEST["select_clave"].'&cargar_clave='.$_REQUEST['cargar_clave'].'&id_obj='.$object2->id.'" >'.$object2->ref.'</a>';
				print '</td>';
				if (! empty($extrafields->attribute_label))
	        	{
	        		print '<td align="center" ><span class="hideonsmartphone">'.number_format($object2->array_options["options_anc"],2).'</span>';
					print '</td>';
					print '<td align="center" ><span class="hideonsmartphone">'.number_format($object2->array_options["options_lar"],2).'</span>';
					print '</td>';
					$factory = new Factory($db);
	            	$factory->id =$obj->rowid;
	            	$factory->get_sousproduits_arbo();
	            	//$pr = $factory->get_arbo_each_prod();

	            	foreach ($pr as $key => $value) {
	            		if ($value["type"]==0) {
	            			$ar[0]=dol_trunc($value["label"],10);
	            		}
	            		if ($value["type"]==1) {
	            			$ar[1]=dol_trunc($value["label"],10);
	            		}
	            	}


					print '<td align="center" ><span class="hideonsmartphone">'.$ar[0].'</span>';
					print '</td>';
					print '<td align="center" ><span class="hideonsmartphone">'.$ar[1].'</span>';
					print '</td>';
					print '<td align="center" ><span class="hideonsmartphone">'.number_format($object2->array_options["options_hmcnc"],2).'</span>';
					print '</td>';
					print '<td align="center" ><span class="hideonsmartphone">'.number_format($object2->array_options["options_hecnc"],2).'</span>';
					print '</td>';
					print '<td align="center" ><span class="hideonsmartphone">'.number_format($object2->array_options["options_hr"],2).'</span>';
					print '</td>';
				}
				print '<td align="center">';
					print $form->load_tva("tva_".$obj->rowid, "16", $mysoc, '');
				print '</td>';
				print '<td align="center">';
					print price($object2->price);
					$sum+=$object2->price;
				print '</td>';
				print '<td align="right">';
					print '<input type="text" size="2" name="qty_'.$obj->rowid.'" id="qty_'.$obj->rowid.'" class="flat qty_in" value="'.$obj->total_prod.'">';
				print '</td>';
				print '<td align="center" >';
					print '<input type="checkbox" class="check_in" id="check_'.$obj->rowid.'" name="type_select[]" value="'.$obj->rowid.'" price="'.$object2->price.'" checked>';
				print '</td>';
				print '</tr>';
				$typeselect="pair";
			}
			print '<tr class="'.$typeselect.'">';
			print '<td colspan=8><span class="hideonsmartphone">Precio total</span>';
			print '</td>';
			print '<td align="right"><span class="hideonsmartphone" id="price_total">'.price($sum).'</span>';
			print '</td>';
			print '<td align="center" colspan=3>
						<input type="submit" class="button" value="Añadir" name="add_elements" id="add_elements">';
			print '</td>';
			print '</tr></table></form>';
			if ($_REQUEST["id_obj"]>0) {
            	$form2 = new Form($db);

			    $langs->load("products");
				$prod = new Product($db);
				$prod->fetch($_REQUEST["id_obj"]);
				$sql="SELECT a.tratamiento FROM llx_product as a WHERE a.rowid=".$_REQUEST["id_obj"];

    			$res=$db->query($sql);
				if ($res) {
			        $res=$db->fetch_object($res);
			        $prod->tratamiento=$res->tratamiento;
			    }

            	require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
            	$factory = new Factory($db);
            	$factory->id =$_REQUEST["id_obj"];

            	$prodsfather = $factory->getFather(); //Parent Products
                // pour connaitre les produits composant le produits
                $factory->get_sousproduits_arbo();
                // Number of subproducts
                $prods_arbo = $factory->get_arbo_each_prod();



                //
                $html= '<br>
                <form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
               		$html.= '<input type="hidden" name="id" value="'.$object->id.'">';
		            $html.= '<input type="hidden" name="act" value="update">';
		            
		            $html.= '<input type="hidden" name="select_clave" value="'.$_REQUEST["select_clave"].'">';
		            $html.= '<input type="hidden" name="cargar_clave" value="'.$_REQUEST["cargar_clave"].'">';
		            $html.= '<input type="hidden" name="id_obj" value="'.$prod->id.'">';
		            
		            $label2=$prod->ref.' - '.$prod->label;

		            $html.= '<table class="border allwidth">';
		            /*$html.= '<tr class="liste_titre nodrag nodrop">';
		            	$html.= '<td colspan=2><b>'.$prod->ref.' - '.$prod->label.'</b></td>';
		            $html.= '</tr >';*/
		            if (! empty($modCodeProduct->code_auto)) $tmpcode=$modCodeProduct->getNextValue($prod,$type);
		            $html.= '<td class="fieldrequired" width="20%">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref2" size="32" maxlength="128" value="'.$prod->ref.'">';
		            if ($refalreadyexists)
		            {
		                $html.= $langs->trans("RefAlreadyExists");
		            }
		            $html.= '</td></tr>';

		            // Label
		            $html.= '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3">
		                    <input name="label" size="40" maxlength="255" value="'.$prod->label.'">
		                </td></tr>';




		            // Other attributes
		            $parameters=array('colspan' => 3);
		              // Note that $action and $prod may have been modified by hook
		            $option_anc=0;
		            $options_lar=0;
		            $options_total_prod=0;

		            if ( ! empty($extrafields->attribute_label))
		            {
		                //$html.= $prod->showOptionals($extrafields,'edit',$parameters);
		                $html.= '
		                    <tr>
		                        <td>'.$extrafields->attribute_label["anc"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" name="options_anc" size="6" value="'.number_format($prod->array_options["options_anc"],2).'"> 
		                        </td>
		                    </tr>
		       
		                    <tr>
		                        <td>'.$extrafields->attribute_label["lar"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" name="options_lar" size="6" value="'.number_format($prod->array_options["options_lar"],2).'"> 
		                        </td>
		                    </tr>
		        
		                    <tr>
		                        <td>'.$extrafields->attribute_label["total_prod"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" name="options_total_prod" size="10" maxlength="10" value="'.$prod->array_options["options_total_prod"].'">
		                        </td>
		                    </tr>

			                <tr></tr>
		                    <tr>
		                        <td>'.$extrafields->attribute_label["hmcnc"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" id="options_hmcnc" name="options_hmcnc" size="10" maxlength="10" value="'.(empty($prod->array_options["options_hmcnc"])?0:number_format($prod->array_options["options_hmcnc"],2)).'">
		                        </td>
		                    </tr>
		                
		                   <tr>
		                        <td>'.$extrafields->attribute_label["hecnc"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" id="options_hecnc" name="options_hecnc" size="10" maxlength="10" value="'.(empty($prod->array_options["options_hecnc"])?0:number_format($prod->array_options["options_hecnc"],2)).'">
		                        </td>
		                    </tr>
		                
		                   <tr>
		                        <td>'.$extrafields->attribute_label["hr"].'</td>
		                        <td colspan="3">
		                            <input type="text" class="flat" id="options_hr" name="options_hr" size="10" maxlength="10" value="'.(empty($prod->array_options["options_hr"])?0:number_format($prod->array_options["options_hr"],2)).'">
		                        </td>
		                    </tr>
			                ';
		                $option_anc=$prod->array_options["options_anc"];
		                $options_lar=$prod->array_options["options_lar"];
		            }
		             $cad="";
			        if ($prod->tratamiento==1) {
			            $cad="checked";
			        }

			        $html.= '
			           <tr>
			                <td>Solo Tratamiento</td>
			                <td colspan="3">
			                    <input type="checkbox" name="tratamiento" '.$cad.'>
			                </td>
			            </tr>
			        ';
		            $html.= '</table>';
		            $html.= '<div class="center"><br>';
		            $html.= '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
		            $html.= '</div>';
	            $html.= '</form>';

	            //echo $html;

	            if ($option_anc>0 && $options_lar>0  && !empty($prod->ref) && !empty($prod->label) ) {
	                
	                $prodsfather = $factory->getFather(); //Parent Products

	                // pour connaitre les produits composant le produits
	                $factory->get_sousproduits_arbo();

	 
	                // Number of subproducts
	                $prods_arbo = $factory->get_arbo_each_prod();
	                $ban_trat=0;
	                foreach ($prods_arbo as $key => $value) {
	                    if ($value["type"]==0) {
	                        $ban_trat=1;
	                    }
	                }
	                $html.= "<br>";
	                if ($prod->status_buy==0 && $prod->tratamiento==0 ) {
	                    
	                    $html.=load_fiche_titre("Face 2 - Materia Prima",'','');
	                    $html.=dol_get_fiche_head();

	                        $html.= '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
	                            $html.= '<input type="hidden" name="act" value="add_line">';
		            			$html.= '<input type="hidden" name="id" value="'.$object->id.'">';
	         					$html.= '<input type="hidden" name="select_clave" value="'.$_REQUEST["select_clave"].'">';
								$html.= '<input type="hidden" name="cargar_clave" value="'.$_REQUEST["cargar_clave"].'">';
								$html.= '<input type="hidden" name="id_obj" value="'.$prod->id.'">';
	                            $html.= '<b>Producto:</b>'.$this->select_dol_products(GETPOST("id_line"), 'id_line',1," WHERE fk_product_type<>1 and rowid<>".$object->id." ");
	                            //print '<br><b>Cantidad: </b><input name="cantidad" size="10"  value="'.GETPOST("cantidad").'">';
	                            $html.= '<br><br><input type="submit" class="button" value="Cargar">';
	                        $html.= '</form>';    
	                    $html.=dol_get_fiche_end(); 
	                }

	                echo "<br>";
	                if ($prod->status_buy==0 && ($ban_trat==1 || $prod->tratamiento==1) ) {
	                    
	                    $html.=load_fiche_titre("Face 3 - Tratamiento",'','');
	                 	$html.=dol_get_fiche_head();
                        $html.= '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
							$html.= '<input type="hidden" name="act" value="add_line">';
							$html.= '<input type="hidden" name="id" value="'.$object->id.'">';
							$html.= '<input type="hidden" name="select_clave" value="'.$_REQUEST["select_clave"].'">';
							$html.= '<input type="hidden" name="cargar_clave" value="'.$_REQUEST["cargar_clave"].'">';
							$html.= '<input type="hidden" name="id_obj" value="'.$prod->id.'">';

                            $html.= '<b>Producto:</b>'.$this->select_dol_products(GETPOST("id_line2"), 'id_line2',1," WHERE fk_product_type=1 and rowid<>".$prod->id." ");
                            //print '<br><b>Cantidad: </b><input name="cantidad" size="10"  value="'.GETPOST("cantidad").'">';
                            $html.= '<br><br><input type="submit" class="button" value="Cargar">';
                        $html.= '</form>';   
                        $html.=dol_get_fiche_end(); 
	                 
	                }
	                //print_fiche_titre("Integración",'','');
		             $html.= '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		             $html.= '<input type="hidden" name="act" value="del_line">';
		             $html.= '<input type="hidden" name="id" value="'.$object->id.'">';
		             $html.= '<input type="hidden" name="select_clave" value="'.$_REQUEST["select_clave"].'">';
					 $html.= '<input type="hidden" name="cargar_clave" value="'.$_REQUEST["cargar_clave"].'">';
					 $html.= '<input type="hidden" name="id_obj" value="'.$prod->id.'">';
		             $html.= '<table class="border" >';
		                 $html.= '<tr class="liste_titre">';
		                 $html.= '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
		                 $html.= '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
		                 $html.= '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyNeed").'</td>';
		                // on affiche la colonne stock m�me si cette fonction n'est pas active
		                 $html.= '<td class="liste_titre" width=50px align="center">'.$langs->trans("Stock").'</td>'; 
		                if ($conf->stock->enabled)
		                {   // we display vwap titles
		                     $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
		                     $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
		                }
		                else
		                {   // we display price as latest purchasing unit price title
		                     $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitHA").'</td>';
		                     $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostHA").'</td>';
		                }
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("TheoreticalWeight").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellPriceUnit").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellPrice").'</td>';
		                 $html.= '<td class="liste_titre" width=100px align="center">Retirar</td>';

		                 $html.= '</tr>';
		                if (count($prods_arbo) > 0)
		                {
		                    $compositionpresente=1;
		                    //print '<b>'.$langs->trans("FactoryTableInfo").'</b><BR>';
		                    
		                        $mntTot=0;
		                        $pmpTot=0;
		                        $sumPriUnit=0;
		                        $sumPriTot=0;
		                        $i=0;
		                        foreach($prods_arbo as $value)
		                        {
		                            //var_dump($value);
		                            // verify if product have child then display it after the product name
		                            $tmpChildArbo=$factory->getChildsArbo($value['id']);
		                            $nbChildArbo="";
		                            if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";

		                             $html.= '<tr>';
		                             $html.= '<td align="left">'.$factory->getNomUrlFactory($value['id'], 1,'index').$nbChildArbo.'</td>';
		                             $html.= '<td align="left" title="'.$value['description'].'">';
		                             $html.= $value['label'].'</td>';
		                             $html.= '<td align="center">'.$value['nb'];
		                            if ($value['globalqty'] == 1)
		                                 $html.= "&nbsp;G";
		                             $html.= '</td>';

		                            $productstatic = new Product($db);
		                            $padre9= new factory($db);//soon
		                            $padre9->priceFather($value['id']);
		                            $price=$padre9->costo;      


		                            //$price=$value['price'];
		                            $pmp=$value['pmp'];
		                            if ($conf->stock->enabled)
		                            {   // we store vwap in variable pmp and display stock

		                                $productstatic->fetch($value['id']);

		                                if ($value['fk_product_type']==0)
		                                {   // if product

		                                    $productstatic->load_stock();

		                                     $html.= '<td align=center></td>';

		                                }
		                                else{
		                                     $html.= '<td></td>';
		                                } 
		                                    
		                            }
		                            else{
		                                 $html.= '<td></td>';
		                            }

		                            $precioUnit=0;              
		                            $factory->get_ref($value['id']);
		                            $factory->dataIntegrationProduct($id);//father
		                            $soon= new factory($db);//soon
		                            $soon->dataIntegrationProduct($value['id']);
		                            
		                            $resp=substr($factory->refFather, 0, 3);                
		                            if((strcmp($resp, 'TRA')==0 || strcmp($resp, 'tra')==0) || strcmp($ref, 'cxhcnc')==0 || strcmp($ref, 'cxhdm')==0 || strcmp($ref, 'cxhr') ==0 || $factory->type==1){
		                                $pesoTeori=0;
		                                $precioUnit=$value['price'];                
		                            }else{
		                                                
		                                $pesoTeori=($soon->esp*$factory->anc*$factory->lar*$soon->fac/ 1000000);//peso teorico= espesor * ancho * largo * factor / 1000000
		                            }       

		                                
		                            if((strcmp($resp, 'TRA')==0 || strcmp($resp, 'tra')==0) ){                  

		                                /*//////////////calculo de Costo Unitario Tratamiento
		                                Costo Unitario de trat.=costo tratamiento + UTILIDAD TRATAMIENTO    
		                                costo tratamiento=FACTOR * AREA CUADRADA
		                                AREA CUADRADA =ANCHO PULGADAS * LARGO PULGADAS
		                                ANCHO PULGADAS = ancho milimetros / 25.4
		                                LARGO PULGADAS = largo milimetros / 25.4
		                                UTILIDAD TRATAMIENTO = costo tratamiento *  %utilidad %  ejemplo  =Y24*Z24%*/

		                                $largo=($factory->lar/25.4);                
		                                $ancho=($factory->anc/25.4);                
		                                $area=$largo*$ancho;
		                                $costoTrat=($soon->fac*$area);          

		                                $utilidad=(($costoTrat*$soon->utilidad)/100);
		                                $precioUnit=$costoTrat+$utilidad;                   

		                            }else{
		                                if($factory->type==0){

		                                    /*///////////////////////////Calculo del costo unitario de material////////////
		                                    costo unitario de material =costo materia prima * utilidad de materia prima                                 
		                                    utilidad de materia prima = costo de materia prima  *  % %  ejemplo  =Q24*R24%      
		                                    peso teorico= espesor * ancho * largo * factor / 1000000
		                                    conversion MN = precio kg * tc  
		                                    costo materia prima = conversion MN * peso teorico      */

		                                    $valorTC=$factory->get_valorTC();
		                                //  echo "  - ".$valorTC;
		                                    $conversionMN=$price*$valorTC;/////////////////////////conversion MN/////////////////////       
		                                //  echo " ".$conversionMN;
		                                    $costMatPrima=($conversionMN*$pesoTeori);   ////////////Costo materia prima /////////////////////////           
		                                //  echo " ".$costMatPrima;
		                                    $utilidadMP=(($costMatPrima*$soon->utilidad)/100); ////////////////Utilidad de materia rima////////////////////
		                                //  echo " ".$utilidadMP;
		                                    $precioUnit=$costMatPrima+$utilidadMP;
		                                //  echo " ".$precioUnit;


		                                }
		                            }       
		                            
		                             $html.= '<td align="right">'.price($pmp).'</td>'; // display else vwap or else latest purchasing price
		                             $html.= '<td align="right">'.price($pmp*$value['nb']).'</td>'; // display total line
		                             $html.= '<td align="right">'.price($price).'</td>';
		                             $html.= '<td align="right">'.price($price*$value['nb']).'</td>';
		                             $html.= '<td align="right">'.price(($price-$pmp)*$value['nb']).'</td>'; 
		                             $html.= '<td align="right">'.number_format($pesoTeori,2).'</td>'; //peso teorico= espesor * ancho * largo * factor / 1000000
		                             $html.= '<td align="right">'.number_format($precioUnit,2).'</td>'; //precio unitario
		                             $html.= '<td align="right">'.number_format(($precioUnit*$value['nb']),2).'</td>'; //precio total
		                             $html.= '<td align="center"><input type="checkbox" name="prod_id_chk'.$i.'" value="'.$value['id'].'"></td>';
		                            
		                            $mntTot=$mntTot+$price*$value['nb'];
		                            $pmpTot=$pmpTot+$pmp*$value['nb']; // sub total calculation
		                            $sumPriUnit+=round($precioUnit,2);
		                            $sumPriTot+=round(($precioUnit*$value['nb']),2);
		                            
		                             $html.= '</tr>';

		                            //var_dump($value);
		                            // $html.= '<pre>'.$productstatic->ref.'</pre>';
		                            // $html.= $productstatic->getNomUrl(1).'<br>';
		                            // $html.= $value[0];  // This contains a tr line.
		                            $i++;
		                        }
		                         $html.= '<tr class="liste_total">';
		                         $html.= '<td colspan=5 align=right >'.$langs->trans("Total").'</td>';
		                         $html.= '<td align="right" >'.price($pmpTot).'</td>';
		                         $html.= '<td ></td>';
		                         $html.= '<td align="right" >'.price($mntTot).'</td>';
		                         $html.= '<td align="right" >'.price($mntTot-$pmpTot).'</td>';
		                         $html.= '<td colspan=3 ></td>';
		                         $html.= '<td align="center" ><input type="submit" class="button" value="Retirar"></td>';
		                        // $html.= '<td >'.round($sumPriUnit,2).'</td>';
		                        // $html.= '<td >'.round($sumPriTot,2).'</td>';
		                        
		                }else{
		                     $html.= '<tr >';
		                         $html.= '<td colspan=13 >Sin Resultados</td>';
		                     $html.= '</tr>';
		                }
		             $html.= '</table>';
		             $html.= '</form>';   
	            }
	            echo $this->formconfirm2("",$label2,$html,"");
	           
	            
            }
		}



	}


	 function formconfirm2($page, $title, $question, $action, $formquestion='', $selectedchoice="", $useajax=1, $height=670, $width=1000)
    {
        global $langs,$conf;
        global $useglobalvars;

        $more='';
        $formconfirm='';
        $inputok=array();
        $inputko=array();
        
        // Clean parameters
        $newselectedchoice=empty($selectedchoice)?"no":$selectedchoice;
 
        
		// JQUI method dialog is broken with jmobile, we use standard HTML.
		// Note: When using dol_use_jmobile or no js, you must also check code for button use a GET url with action=xxx and check that you also output the confirm code when action=xxx
		// See page product/card.php for example
        if (! empty($conf->dol_use_jmobile)) $useajax=0;
		if (empty($conf->use_javascript_ajax)) $useajax=0;
        
        if ($useajax)
        {
            $autoOpen=true;
            $dialogconfirm='dialog-confirm';
            $button='';
            if (! is_numeric($useajax))
            {
                $button=$useajax;
                $useajax=1;
                $autoOpen=false;
                $dialogconfirm.='-'.$button;
            }
            $pageyes=$page.(preg_match('/\?/',$page)?'&':'?').'action='.$action.'&confirm=yes';
            $pageno=($useajax == 2 ? $page.(preg_match('/\?/',$page)?'&':'?').'confirm=no':'');
            // Add input fields into list of fields to read during submit (inputok and inputko)
            if (is_array($formquestion))
            {
                foreach ($formquestion as $key => $input)
                {
                	//print "xx ".$key." rr ".is_array($input)."<br>\n";
                    if (is_array($input) && isset($input['name'])) array_push($inputok,$input['name']);
                    if (isset($input['inputko']) && $input['inputko'] == 1) array_push($inputko,$input['name']);
                }
            }
			// Show JQuery confirm box. Note that global var $useglobalvars is used inside this template
            $formconfirm.= '<div id="'.$dialogconfirm.'" title="'.dol_escape_htmltag($title).'" >';
            if (! empty($more)) {
            	$formconfirm.= '<div class="confirmquestions">'.$more.'</div>';
            }
            $formconfirm.= ($question ? '<div class="confirmmessage"> '.$question . '</div>': '');
            $formconfirm.= '</div>'."\n";

            

            $formconfirm.= "\n<!-- begin ajax form_confirm page=".$page." -->\n";
            $formconfirm.= '<script type="text/javascript">'."\n";
            $formconfirm.= 'jQuery(document).ready(function() {
            $(function() {
            	$( "#'.$dialogconfirm.'" ).dialog(
            	{
                    autoOpen: '.($autoOpen ? "true" : "false").',';
 
        			$formconfirm.='
                    resizable: false,
                    height: "'.$height.'",
                    width: "'.$width.'",
                    modal: true,
                    closeOnEscape: false,
                    buttons: {
                        
                        "Cerrar": function() {
                        	var options = "";
                         	var inputko = '.json_encode($inputko).';
                         	var pageno="'.dol_escape_js(! empty($pageno)?$pageno:'').'";
                         	if (inputko.length>0) {
                         		$.each(inputko, function(i, inputname) {
                         			var more = "";
                         			if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
                         			var inputvalue = $("#" + inputname + more).val();
                         			if (typeof inputvalue == "undefined") { inputvalue=""; }
                         			options += "&" + inputname + "=" + inputvalue;
                         		});
                         	}
                         	var urljump=pageno + (pageno.indexOf("?") < 0 ? "?" : "") + options;
                         	//alert(urljump);
            				if (pageno.length > 0) { location.href = urljump; }
                            $(this).dialog("close");
                        }
                    }
                }
                );

            	var button = "'.$button.'";
            	if (button.length > 0) {
                	$( "#" + button ).click(function() {
                		$("#'.$dialogconfirm.'").dialog("open");
        			});
                }
            });
            });
            </script>';
            $formconfirm.= "<!-- end ajax form_confirm -->\n";
        }

        return $formconfirm;
    }






	function select_dol_products($selected='', $htmlname='prod', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
	{
	
	    global $conf,$user,$langs,$db;


	    $out='';

	    // On recherche les utilisateurs
	    $sql = "SELECT DISTINCT u.rowid, u.ref, u.label,u.price_ttc,u.tva_tx,u.duration,u.fk_product_type";
	    /*if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
	    {
	        $sql.= ", e.label";
	    }*/
	    $sql.= " FROM ".MAIN_DB_PREFIX ."product as u  ";

	    if (!empty($exclude)) {
	        $sql.=$exclude;
	    }

	    $sql.= " ORDER BY u.ref ASC";

	    $resql=$db->query($sql);

	    if ($resql)
	    {

	        $num = $db->num_rows($resql);
	        $i = 0;
	        if ($num)
	        {

	            // Enhance with select2
	            $nodatarole='';
	            if ($conf->use_javascript_ajax)
	            {
	                include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	                $comboenhancement = ajax_combobox($htmlname);
	                $out.=$comboenhancement;
	                $nodatarole=($comboenhancement?' data-role="none"':'');
	            }

	            $out.= '<select class="flat minwidth200'.($morecss?' '.$morecss:'').'" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
	            if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
	            if ($show_every) $out.= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

	            $userstatic=new User($db);

	            while ($i < $num)
	            {
	                $obj = $db->fetch_object($resql);

	                $disableline='';
	                if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=($enableonlytext?$enableonlytext:'1');

	                if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
	                {
	                    $out.= '<option value="'.$obj->rowid.'"';
	                    if ($disableline) $out.= ' disabled';
	                    $out.= ' selected>';
	                }
	                else
	                {
	                    $out.= '<option value="'.$obj->rowid.'"';
	                    if ($disableline) $out.= ' disabled';
	                    $out.= '>';
	                }

	                $out.= $obj->ref." - ".$obj->label." - $".price($obj->price_ttc,0,'',0,0,0);

	                if (empty($obj->duration)) {
	                    if ($obj->tva_tx>0) {
	                        $out.=" ".$langs->trans("TTC");
	                    }else{
	                        $out.=" ".$langs->trans("HT");
	                    }
	                }else{
	                    $our_value=substr($obj->duration,0,dol_strlen($obj->duration)-1);
	                    $outdurationunit=substr($obj->duration,-1);
	                    
	                    $da=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
	                    if (isset($da[$outdurationunit]))
	                    {
	                        $out.= " - ".$our_value." ".$langs->trans($da[$outdurationunit]);
	                    }
	                }
	                


	                $out.= '</option>';

	                $i++;
	            }
	        }
	        else
	        {
	            $out.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" disabled>';
	            $out.= '<option value="">'.$langs->trans("None").'</option>';
	        }
	        $out.= '</select>';
	    }
	    else
	    {
	        dol_print_error($db);
	    }
	    return $out;
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
		global $db,$user;
		
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
		


		//add elements
		$action= $_REQUEST['act'];	
		if ($action == 'update' )
	    {

	    	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
			require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
			require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	    	$id_line=GETPOST("id_line");
	        $id_obj=GETPOST("id_obj");
	        $id=GETPOST("id");
	        $select_clave=GETPOST("select_clave");
	        $cargar_clave=GETPOST("cargar_clave");

	        if (GETPOST('cancel'))
	        {
	            $action = '';
	        }
	        else
	        {
	            if ($id_obj > 0)
	            {

	            	$object_2 = new Product($db);
	            	$object_2->fetch($id_obj);
	            	$object_2->oldcopy= clone $object_2;
	                $object_2->ref                    = GETPOST("ref2");
	                $object_2->tratamiento            = GETPOST('tratamiento');
	                if ($object_2->tratamiento=="on") {
	                    $object_2->tratamiento=1;
	                }else{
	                    $object_2->tratamiento=0;
	                }
	                $object_2->label                  = GETPOST('label');
	                $object_2->description            = dol_htmlcleanlastbr(GETPOST('desc'));
	                $object_2->url                    = GETPOST('url');
	                $object_2->note                   = dol_htmlcleanlastbr(GETPOST('note'));
	                $object_2->customcode             = GETPOST('customcode');
	                $object_2->country_id             = GETPOST('country_id');
	                $object_2->status                 = GETPOST('statut');
	                $object_2->status_buy             = GETPOST('statut_buy');
	                $object_2->status_batch           = GETPOST('status_batch');
	                // removed from update view so GETPOST always empty
	                /*
	                $object_2->seuil_stock_alerte     = GETPOST('seuil_stock_alerte');
	                $object_2->desiredstock           = GETPOST('desiredstock');
	                */
	                $object_2->duration_value         = GETPOST('duration_value');
	                $object_2->duration_unit          = GETPOST('duration_unit');

	                $object_2->canvas                 = GETPOST('canvas');
	                $object_2->weight                 = GETPOST('weight');
	                $object_2->weight_units           = GETPOST('weight_units');
	                $object_2->length                 = GETPOST('size');
	                $object_2->length_units           = GETPOST('size_units');
	                $object_2->surface                = GETPOST('surface');
	                $object_2->surface_units          = GETPOST('surface_units');
	                $object_2->volume                 = GETPOST('volume');
	                $object_2->volume_units           = GETPOST('volume_units');
	                $object_2->finished               = GETPOST('finished');

	                $units = GETPOST('units', 'int');

	                if ($units > 0) {
	                    $object_2->fk_unit = $units;
	                } else {
	                    $object_2->fk_unit = null;
	                }

	                $object_2->barcode_type           = GETPOST('fk_barcode_type');
	                $object_2->barcode                = GETPOST('barcode');
	                // Set barcode_type_xxx from barcode_type id
	                $stdobject=new GenericObject($db);
	                $stdobject->element='product';
	                $stdobject->barcode_type=GETPOST('fk_barcode_type');
	                $result=$stdobject->fetch_barcode();

	                if (empty($object_2->ref))
	                {
	                    $error++;
	                    $mesg='Incluya una referencia';
	                    setEventMessages($mesg, "", 'errors');
	                }
	                if (empty($object_2->label))
	                {
	                    $error++;
	                    $mesg='Incluya una etiqueta';
	                    setEventMessages($mesg, "", 'errors');
	                }
	                if ($result < 0)
	                {
	                    $error++;
	                    $mesg='Failed to get bar code type information ';
	                    setEventMessages($mesg.$stdobject->error, $mesg.$stdobject->errors, 'errors');
	                }
	                $object_2->barcode_type_code      = $stdobject->barcode_type_code;
	                $object_2->barcode_type_coder     = $stdobject->barcode_type_coder;
	                $object_2->barcode_type_label     = $stdobject->barcode_type_label;

	                $object_2->accountancy_code_sell  = GETPOST('accountancy_code_sell');
	                $object_2->accountancy_code_buy   = GETPOST('accountancy_code_buy');
	                $extrafields = new ExtraFields($db);
	                $extralabels=$extrafields->fetch_name_optionals_label($object_2->table_element);
	                $ret = $extrafields->setOptionalsFromPost($extralabels,$object_2);
	                if ($ret < 0) $error++;

	                if (! $error)
	                {
	                    if ($object_2->update($id_obj, $user) > 0)
	                    {
	                    	$sql="UPDATE llx_product as a SET a.tratamiento=".$object_2->tratamiento." WHERE a.rowid=".$id_obj;
                        $db->query($sql);
	                        setEventMessages("Elemento Actualizado", "");
	                        header("Location: ".$_SERVER["PHP_SELF"].'?id='.$id."&select_clave=".$select_clave."&cargar_clave=".$cargar_clave."&id_obj=".$id_obj);
	                    	exit;
	                    }
	                }
	            }

	        }
	    }
		if ($action=='add_line') {
			require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
	        $id_line=GETPOST("id_line");
	        if ($_REQUEST["id_line2"]>0) {
	            $id_line=GETPOST("id_line2");
	        }
	        $id_obj=GETPOST("id_obj");
	        $id=GETPOST("id");
	        $select_clave=GETPOST("select_clave");
	        $cargar_clave=GETPOST("cargar_clave");
	        $cantidad=1;

	        if ($id_line>0) {
	            if ($cantidad>0) {
	                $producto2= new Product($db);
	                $producto2->fetch($id_line);
	                $pmp2=$producto2->pmp;
	                $price2=$producto2->price;
	                $factory = new Factory($db);
	                $factory->id =$id_obj;

	                if($factory->add_component($id_obj,$id_line , $cantidad, $pmp2, $price2, 0)){
	                    

	                    $factory->change_defaultPrice($id);
	                    setEventMessages("Elemento cargado satisfactoriamente", "");
	                    header("Location: ".$_SERVER["PHP_SELF"].'?id='.$id."&select_clave=".$select_clave."&cargar_clave=".$cargar_clave."&id_obj=".$id_obj);
	                    exit;
	                    
	                }
	                else
	                {
	                    setEventMessages($langs->trans("ErrorAssociationIsFatherOfThis"), "", 'errors');
	                    $action = 'edit';
	                }
	            }else{
	                setEventMessages("Ingrese una cantidad valida", "", 'errors');
	                $action = 'edit';
	            }
	            
	        }else{
	            setEventMessages("Ingrese un elemento a la integración valido", "", 'errors');
	            $action = 'edit';
	        }
	        
	    }

	    if ($action=='del_line') {
	    	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
	    	$factory = new Factory($db);
	    	$id_line=GETPOST("id_line");
	        $id_obj=GETPOST("id_obj");
	        $id=GETPOST("id");
	        $select_clave=GETPOST("select_clave");
	        $cargar_clave=GETPOST("cargar_clave");
	        $factory->id =$id_obj;
	        $i=0;
	        foreach ($_POST as $key => $value) {
	            if ($_POST["prod_id_chk".$i]) {
	                if ($factory->del_component($id_obj, $_POST["prod_id_chk".$i]) > 0)
	                {
	                    $ban=1;
	                    $factory->change_defaultPrice($id);
	                    
	                }
	            }
	            $i++;
	        }
	        if ($ban==1) {
	            setEventMessages("Elemento eliminado satisfactoriamente","");
	            header("Location: ".$_SERVER["PHP_SELF"].'?action=edit&id='.$object->id);
	            header("Location: ".$_SERVER["PHP_SELF"].'?id='.$id."&select_clave=".$select_clave."&cargar_clave=".$cargar_clave."&id_obj=".$id_obj);
	            exit;
	        }else{
	            $mesg=$product->error;
	            setEventMessages($mesg, "", 'errors');
	            $action = 'edit';
	        }

	        
	    }
	}

	function validarStock($id){
		global $db;		
		require_once DOL_DOCUMENT_ROOT.'/factory/class/factory.class.php';
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