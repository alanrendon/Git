<?php 
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/tipo_operacion_proveedor.class.php";

$operacion = new Tipo_Operacion();
$claves_datos = array();

if (isset($_POST['type'])  && $_POST['type'] == 'get_list_typesociete' && isset($_POST['idp']) ) {


$operations= $operacion->get_list_typesociete();
$operation_societe= $operacion->get_info_proveedor((int)$_POST['idp']);

if(sizeof($operation_societe)>0)
	$claves_datos= $operacion->get_operaciones_registradas_proveedor($operation_societe->siren);
?>

<br>
	<div class="col-md-6">
	<label> Tipo de proveedor</label>
		<select name="typeoperation" id="id_typeoperation" class="select2_single" onchange="get_list_typeoperation()">
			<?php foreach ($operations as  $value): ?>
				<?php if (sizeof($claves_datos)>0): ?>
					<?php if ($claves_datos->key_type_operation == $value->rowid ): ?>
							<option value="<?php echo $value->rowid; ?>" selected> <?php echo $value->name;?></option>
					<?php else: ?>	
							<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
					<?php endif ?>
				<?php else: ?>
						<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
				<?php endif ?>
				
			<?php endforeach ?>
		</select>
	</div>
	<div class="col-md-6" id="get_list_typeoperation">
		
	</div>
	<br>
<?php
}else if(isset($_POST['type']) && isset($_POST['fk_type_societe']) && isset($_POST['idp'])){

	if(isset($_POST['primera'])){
		$operations= $operacion->get_list_typesociete();
		$operation_societe= $operacion->get_info_proveedor((int)$_POST['idp']);

		if(sizeof($operation_societe)>0)
			$claves_datos= $operacion->get_operaciones_registradas_proveedor($operation_societe->siren);
	}


	$fk = (int)$_POST['fk_type_societe'];
	$operations= $operacion->get_list_typeoperation($fk);
?>
	<label> Tipo de operación </label>
		<select name="operation_societe" id="operation_societe" class="select2_single">
			<?php foreach ($operations as  $value): ?>
				<?php if (sizeof($claves_datos)>0): ?>
					<?php if ($claves_datos->key_type_societe == $value->rowid ): ?>
							<option value="<?php echo $value->rowid; ?>" selected> <?php echo $value->name;?></option>
					<?php else: ?>	
							<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
					<?php endif ?>
				<?php else: ?>
						<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
				<?php endif ?>
			<?php endforeach ?>
		</select>
	<?php if ($fk == 2): ?>
		<div class="row">
			<div class="form-group">
				<br>
				<div class="col-sm-12">
					<label> RFC</label>
					<input type="text" name="rfc_ext" id="rfc_ext" class="form-control" required="">
					<label> No. ID fiscal  </label>
					<input type="text" name="id_ext" id="id_ext" class="form-control" required="">
					<label> Nombre del extranjero </label>
					<input type="text" name="nombre_ext" id="nombre_ext" class="form-control" required="">
				</div>
			</div>
		</div>
	<?php endif ?>
<?php

}else if (isset($_POST['type'])  && $_POST['type'] == 'get_list_typesociete' && !isset($_POST['idp']) ) {

$operations= $operacion->get_list_typesociete();
?>

	<div class="col-md-4">
		<label> Tipo de proveedor</label>
		<select name="typeoperation" id="id_typeoperation" class="select2_single" onchange="get_list_typeoperation()">
			<?php foreach ($operations as  $value): ?>
				<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
			<?php endforeach ?>
		</select>
	</div>
	<div class="col-md-4" id="get_list_typeoperation">
		
	</div>
<?php
}else if(isset($_POST['type']) && isset($_POST['fk_type_societe']) && !isset($_POST['idp'])){
	$fk = (int)$_POST['fk_type_societe'];
	$operations= $operacion->get_list_typeoperation($fk);
?>
	<label> Tipo de operación</label>

	<select name="operation_societe" id="operation_societe" class="select2_single">
		<?php foreach ($operations as  $value): ?>
			<option value="<?php echo $value->rowid; ?>"> <?php echo $value->name;?></option>
		<?php endforeach ?>
	</select>
<?php
}
?>