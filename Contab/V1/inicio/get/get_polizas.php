<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class polizas extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function getPolizas(){
		$sql=" SELECT
	     poliza.rowid as polid,
	     poliza.entity,
	     poliza.tipo_pol,
	     poliza.cons,
	     poliza.anio,
	     poliza.mes,
	     poliza.fecha,
	     poliza.concepto,
	     poliza.comentario,
	     poliza.anombrede,
	     poliza.numcheque,
       poliza.societe_type,
      factCliente.facnumber,
      factProve.ref
	    FROM
	     ".PREFIX."contab_polizas  AS poliza
	    INNER JOIN ".PREFIX."facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
	    INNER JOIN ".PREFIX."facture AS factCliente ON factCliente.rowid=poliza.fk_facture
	    ORDER BY
	     poliza.fechahora DESC
	    LIMIT 10
	    ";
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
			 
		}
		return $rows;
	}
	
	public function getPolizasAsiento($polid){
		$sql=" SELECT cuenta,debe,haber,descripcion
		FROM ".PREFIX."contab_polizasdet, ".PREFIX."contab_cat_ctas
		WHERE fk_poliza=".$polid." AND cuenta=codagr";
		//print $sql;
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
	
}



$polizas = new Polizas();
$arreglo = $polizas->getPolizas();
/* print "<pre>";
print_r($arreglo);
print "</pre>"; */
$j=0;


while($j<count($arreglo)){
	$d='';
	$e='';
	$c='';
	$i='';
	$tip='';
	if($arreglo[$j]['tipo_pol']=='D'){$d=' SELECTED';$tip='Diario';}
	if($arreglo[$j]['tipo_pol']=='E'){$e=' SELECTED';$tip='Egreso';}
	if($arreglo[$j]['tipo_pol']=='C'){$c=' SELECTED';$tip='Cheque';}
	if($arreglo[$j]['tipo_pol']=='I'){$i=' SELECTED';$tip='Ingreso';}
  if ($arreglo[$j]['societe_type']==1) {
    $tipoDoctorelacionado=  $arreglo[$j]['facnumber'];
  }elseif ($arreglo[$j]['societe_type']==2) {
      $tipoDoctorelacionado=  $arreglo[$j]['ref'];
  }else{
    $tipoDoctorelacionado="No hay docto.";
  }
?>
<div class="col-md-12 col-xs-12 col-lg-12">
     <div class=" x_panel">
        <div class="x_title">
            <h2>Encabezado de la póliza <?=$tip." ".$arreglo[$j]['cons']?></h2>
             <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li><a class="edit-link" title="Editar"><i class="fa fa-wrench" style="color: green"></i></a>
                </li>
                <li><a class="download-link" title="Descargar"> <i class="fa fa-download" style="color: blue"></i></a>
                </li>
                <li><a class="recycle-link" title="Recurente"> <i class="fa fa-recycle" style="color: yellow"></i></a>
                </li>
                <li><a class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a>
                </li>
                <li><a class="close-link" title="Cerrar"><i class="fa fa-close"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
                <table class="table table-striped">
                      <tbody>
                        <tr>
                          <th scope="row"  WIDTH="160" >Concepto:</th>
                          <td><?php echo  ($arreglo[$j]['concepto']); ?></td>
                           <th scope="row"  WIDTH="30" >Fecha:</th>
                          <td WIDTH="30"><?php echo $arreglo[$j]['fecha']; ?></td>
                           <th scope="row"  WIDTH="30" >Documento Relacionado:</th>
                          <td WIDTH="30"><?php echo $tipoDoctorelacionado; ?></td>
                        </tr>
                        <tr>
                          <th scope="row"  WIDTH="50" >Cheque a Nombre: </th>
                          <td><?php echo $arreglo[$j]['anombrede']; ?></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                        </tr>
                        <tr>
                          <th scope="row"  WIDTH="50" >Num. Cheque: </th>
                          <td><?php echo $arreglo[$j]['numcheque']; ?></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                          <td WIDTH="90"></td>
                        </tr>
                      </tbody>
                </table>
                <br>
                <div class="ln_solid"></div>

            <ul class="nav left panel_toolbox" >
              <li><a class="add-link" title="Agregar">Agregar asiento <i class="fa fa-plus fa-lg" style="color: blue"></i></a>
              </li>
            </ul>
<?php
        $arrasiento = $polizas->getPolizasAsiento($arreglo[$j]['polid']);
        if (!empty( $arrasiento)) {
          ?>
          <table  class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                 <th>Asiento</th>
                  <th>Cuenta</th>
                  <th>Debe</th>
                  <th>Haber</th>
                  <th class="column-title no-link last"><span class="nobr">Acciones</span>
                </tr>
            </thead>
            <tbody> 
            <?php 
           
            //print_r($arrasiento);
           $m=0;
            while($m<count($arrasiento)){
            ?>               
                <tr>
                     <td><?=$arrasiento[$m]["descripcion"]?></td>
                     <td><?=$arrasiento[$m]["cuenta"]?></td>
                     <td><?=$arrasiento[$m]["debe"]?></td>
                     <td><?=$arrasiento[$m]["haber"]?></td>
                     <td class="last">
                        <ul class="nav left panel_toolbox" >
                          <li><a class="edit-link" title="Editar"><i class="fa fa-wrench" style="color: green"></i></a>
                          </li>
                          <li><a class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a>
                          </li>
                      </ul>
                     </td>
                </tr>
            <?php 
              $m++;
            }
            ?>
            </tbody>
        </table>

          <?php
        }
?>

        </div>
    </div>
</div>
<?php 
	$j++;
}
?>