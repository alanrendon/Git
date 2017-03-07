<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/tipo_operacion_proveedor.class.php";
require_once "../class/operation_societe_fourn.class.php";

$register_operation_societe = new Operation_Societe_Fourn();
$arreglo                    = $register_operation_societe->get_operations_societe();
?>

<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
	<thead>
		<tr>
				 	<th>No.</th>
				 	<th>Proveedor</th>
				 	<th>RFC</th>
				 	<th>Tipo proveedor</th>
				 	<th>Tipo de operación</th>
				 	<th class="column-title no-link last"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($arreglo  as $key => $value): ?>
			<tr>
				<td><?php echo $key+1 ?></td>
				<td><?php echo $value->nom ?></td>
				<td><?php echo $value->siren ?></td>
				<td><?php echo $value->tokey.' - '.$value->toname ?></td>
				<td><?php echo $value->tskey.' - '.$value->tsname ?></td>
				<td>
					<ul class="nav panel_toolbox" style="min-width: 10px !important;">
						<li>
							<a id="<?php echo $value->rowid ?>" class="delete-link" title="Borrar">
								<i class="fa fa-trash" style="color: red"></i>
							</a>
						</li>
					</ul>
				</td>
			</tr>
	<?php endforeach ?>
    </tbody>
  </table>
