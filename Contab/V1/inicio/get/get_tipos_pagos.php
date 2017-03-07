<?php 
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/tipos_pagos.class.php";

$funcion=$_GET['fun'];
switch ($funcion) {
	case 'get_tipos_pagos_credito':
		get_tipos_pagos_credito();
		break;	
	case 'get_tipos_pagos':
		get_tipos_pagos();
		break;	
	case 'get_condiciones_asignadas':
		get_condiciones_asignadas();
		break;
	default:
		echo "Error 0001";
		break;
}

function get_tipos_pagos_credito(){
	$pag     = new tipos_pagos(); 
	$arreglo = $pag->get_tiposPagos();	

	print '<label class="">Crédito</label>
			<select class="select2_multiple form-control select2-hidden-accessible" name="datos[]" multiple>
			';

	foreach ($arreglo as $value) {
		print '<option value="'.$value->rowid.'" >'.$value->libelle.'</option>' ;		
	}

	print '</select>';
}

function get_tipos_pagos(){

	print '<label class="">Pagos</label>
			<select class="select2_single form-control select2-hidden-accessible" name="type" >';				

				print '<option value="1" >Contado</option>' ;	
				print '<option value="2" >Crédito</option>' ;	
				print '<option value="3" >Anticipo</option>' ;	
				print '<option value="4" >50/50</option>' ;	
			print '</select>';
}

function get_condiciones_asignadas(){
	$res = '<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>No.</th>
					<th>Creditos</th>
					<th>Pagos</th>
					<th></th>
				</tr>
			</thead>
			<tbody>';

        $contador = 1;
        $pag = new tipos_pagos(); 
		$arreglo = $pag->get_condiciones_pagos_asignadas();	
		
        foreach ( $arreglo as $value ) {                
                $res .= '<tr>';
                $res .= '<td>'.$contador.'</td>';
                $res .= '<td>'.$value->libelle.'</td>';
                $res .= '<td>'.$value->cond_pago.'</td>';
                $res .= '<td align="center">';
                $res .= '<ul class="nav panel_toolbox" style="min-width: 10px !important;"><li><a id="'.$value->rowid.'" class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a></li></ul>';
                $res .= '</td>';
                $res .= '</tr>';
                $contador++;
            }

            $res .= '</tbody>
                    </table>';

            print $res;
}

