<?php 
require '../main.inc.php';


include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/factory/class/factoryproccess.class.php');
dol_include_once('/factory/class/factorytools.class.php');


date_default_timezone_set('America/Mexico_City');
// Load traductions files requiredby by page
$langs->load("root");
$langs->load("other");


if(isset($_POST['funcion'])){
	$funcion= addslashes($_POST['funcion']);
	switch ($funcion) {
		case 'productsPropal':			
			productsPropal($db);
			break;
		case 'asignarProcesos':
			asignarProcesos($db);
			break;
		default:
			echo "Parametros incorrectos";
			break;
	}
}

print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	
	jQuery("#btnAdd").click(function() {

		var products=[];
		var fechas=[];
		var operadores=[];
		i=0;
		$(".check:checked").each(function() {
		    products[i] = $(this).val();
		    i++;
		});

		for (i=0; i<products.length ; i++) { 
			var cad=products[i];
			var dateTime=""+$("#dateStart"+cad).val()+" "+$("#dateStart"+cad+"hour").val()+":"+$("#dateStart"+cad+"min").val()+"";
			//fechas[i]=$("#dateStart"+cad).val();
			fechas[i]=dateTime;
			
			operadores[i]=$("#fk_operator"+cad).val();		
			if($("#fk_operator"+cad).val()==0){
				alert("Seleccione operador");
				return false;	
			}
		}

		datos={funcion:"asignarProcesos", fechas:fechas, productos:products, operadores:operadores,  idPropal:$("#fk_propal").val()};

			$.ajax({
				async:true,
				type: "POST",
				dataType: "html",
				contentType: "application/x-www-form-urlencoded",
				url:"treatment_propal.php",
				data:datos,
				success:function (data){					
					//alert(data);
					window.location = "'.DOL_URL_ROOT.'/factory/factoryTreatment_list.php";

													
				},
				error:function (data){
					alert("error "+data);
				}
			});	
			
			return false;
	});
});
</script>';

function asignarProcesos($db){
	$fechas=$_POST['fechas'];
	$process=$_POST['productos'];
	$operadores=$_POST['operadores'];
	//$id_propal=$_POST['idPropal'];

	$operator= new Factoryproccess($db);


	for ($i=0; $i<sizeof($process); $i++){

	   	$operator->addProcesos_treatment($process[$i],0,$fechas[$i]);
	}
}


function productsPropal($db){
	$idPropal=$_POST['idPropal'];

	$string='
	SELECT
		a.fk_propal,
		a.fk_product,
		c.ref,
		c.label,
		b.rowid as id_factory_proccess
	FROM
		llx_propaldet AS a
	LEFT JOIN llx_factory_proccess AS b ON a.fk_propal = b.fk_propal AND a.fk_product = b.fk_product
	INNER JOIN llx_product as c on a.fk_product=c.rowid
	WHERE
		a.fk_propal ='.$idPropal.'
	AND b.status=4';
	//print $string;
	$res=$db->query($string);
	$num=$db->num_rows($res);
	

	if($num>0){	
		//llxHeader('','Produccion','');
		print '<input type="hidden" id="fk_propal" value="'.$idPropal.'"/>';
		$form=new Form($db);
		print load_fiche_titre("Productos");
		print '<table class="border" width="100%" >'."\n";		
			print '<tr class="liste_titre"  >';
				print '<td class="liste_titre" width=100px >Ref</td>';			
				print '<td class="liste_titre" width=100px >Producto</td>';
				print '<td class="liste_titre" width=100px >Fecha Inicialización</td>';
				//print '<td class="liste_titre" width=100px >Operador</td>';
				print '<td align="center" class="liste_titre" width=100px  >Asignar</td>';
			print '</tr>';

			while ($data = $db->fetch_object($res)) {
				print '<tr>';
					print '<td>'.$data->ref.'</td>';			
					print '<td>'.$data->label.'</td>';		
					print '<td>';
						if($dateop==''){				
							date_default_timezone_set('America/Mexico_City');
							$dateop=strtotime( date('Y-m-d h:i'));
						}		
						print ($form->select_date($dateop,'dateStart'.$data->id_factory_proccess ,1, 1, 0, "", 1, 0, 1, 0, '', '', ''));
						
						//print '<input name="fk_operator" id="fk_operator'.$data->fk_product.'" style="display: none;" value="0">';
					print '</td>';		
					/*
						print '<td>';		
							print '<select name="fk_operator" id="fk_operator'.$data->fk_product.'" class="fk_operator">';
								$operator= new Factorytools($db);
								$list=$operator->get_operators();
								print  '<option value="0">..Seleccione</option>';
								foreach ($list as $dat2) {
									print '<option value="'.$dat2->rowid.'">'.$dat2->name.'</option>';	
								}
							print '</select>';
						print '</td>';
					*/
					print '</td>';	
					print '<td align="center"><input type="checkbox" class="check" id="'.$data->id_factory_proccess.'" value="'.$data->id_factory_proccess.'"/></td>';						
				print '</tr>';
			}
		
		print '<br/>';
		print '</table>'."\n";
		print '<div class="center"><button class="button" name="add"  id="btnAdd" >Asignar</button></div>';
		
	}else{
		echo "Todos los procesos han sido asignados";
	}
}
