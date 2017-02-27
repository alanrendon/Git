<?php 
if (!$res && file_exists("../main.inc.php"))
	$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
	$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
	$res = @include '../../../main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../main.inc.php"))
	$res = @include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

class poliza_cierre
{
	var $db;	
	var $cuenta;
	var $haber;
	var $debe;	
	
	function __construct($db)
	{
		$this->db = $db;
	
		return 1;
	}


	function prearmar_poliza($ini, $fin, $anio){
		global $conf;
		/* $sql = "SELECT
					a.cuenta,
					Sum(a.haber) AS haber,
					Sum(a.debe) AS debe,
					c.natur
				FROM
					".MAIN_DB_PREFIX."contab_polizasdet AS a
				INNER JOIN ".MAIN_DB_PREFIX."contab_polizas AS b ON a.fk_poliza = b.rowid
				INNER JOIN ".MAIN_DB_PREFIX."contab_sat_ctas AS c ON c.codagr = a.cuenta
				WHERE
					a.cuenta >= ".$ini."
				AND a.cuenta <= ".$fin."
				AND b.anio <= ".$anio."
				GROUP BY
					a.cuenta;"; */
		$sql="SELECT d.rowid,d.cta as cuenta,d.descta,ifnull(c.debe,0) as debe,ifnull(c.haber,0) as haber
	FROM ".MAIN_DB_PREFIX."contab_cat_ctas d
 	LEFT JOIN (SELECT b.cuenta,sum(b.debe) as debe,sum(b.haber) as haber
	FROM ".MAIN_DB_PREFIX."contab_polizas a, ".MAIN_DB_PREFIX."contab_polizasdet b
			WHERE anio<=".$anio."
			AND entity=".$conf->entity." AND a.rowid=b.fk_poliza GROUP BY b.cuenta) c ON d.cta=c.cuenta
	WHERE entity=".$conf->entity." ORDER BY d.cta";
		
		//print $sql;
    	dol_syslog(get_class($this)."::Prearmar_poliza sql=".$sql, LOG_DEBUG);    	
    	
    	$result = $this->db->query($sql);
    	if ($result) {
    		while ($obj = $this->db->fetch_object($result)) {
    			$a=explode('.',$ini);
    			$b=explode('.',$fin);
    			
    			$c=explode('.',$obj->cuenta);
    			$d='';
    			if($c[0]>=$a[0] && $c[0]<=$b[0]){
    				if($c[0]==$a[0] && $c[0]==$b[0]){
    					if($c[1]>=$a[1] && $c[1]<=$b[1]){
    						if($c[1]==$a[1] && $c[1]==$b[1]){
    							//if($c[2]>=$a[2] && $c[2]<=$b[2]){
    								$d='Si';
    							/* }else{
    								$d='No';
    							} */
    						}else{
    							if($c[1]==$a[1]){
    								//if($c[2]>=$a[2]){
    									$d='Si';
    								/* }else{
    									$d='No';
    								} */
    							}else{
    								if($c[1]==$b[1]){
    									//if($c[2]<=$b[2]){
    										$d='Si';
    									/* }else{
    										$d='No';
    									} */
    								}else{
    									$d='Si';
    								}
    							}
    						}
    					}else{
    						$d='No';
    					}
    				}else{
    					if($c[0]==$a[0]){
    						if($c[1]>=$a[1]){
    							if($c[1]==$a[1]){
    								//if($c[2]>=$a[2]){
    									$d='Si';
    								/* }else{
    									$d='No';
    								} */
    							}else{
    								$d='Si';
    							}
    						}else{
    							$d='No';
    						}
    					}else{
    						if($c[0]==$b[0]){
    							if($c[1]<=$b[1]){
    								if($c[1]==$b[1]){
    									//if($c[2]<=$b[2]){
    										$d='Si';
    									/* }else{
    										$d='No';
    									} */
    								}else{
    									$d='Si';
    								}
    							}else{
    								$d='No';
    							}
    						}else{
    							$d='Si';
    						}
    					}
    				}
    			}else{
    				$d='No';
    			}
    			if($d=='Si' && ($obj->debe!=0 || $obj->haber!=0)){
    				$arr[] = $obj;
    			}
    		}
    	} else {
    		$this->error=$this->db->error();
    		dol_syslog(get_class($this).'::fetch_outof_period '.$this->error,LOG_ERR);
    		//$res = -1;
    	}
    	return $arr;      
	}


	public function generar_poliza($ini, $fin, $anio, $res,$cresult){
		global $conf;        
        /* $sql ='SELECT
					a.cuenta,
					Sum(a.haber) AS haber,
					Sum(a.debe) AS debe,
					c.natur
				FROM
					'.MAIN_DB_PREFIX.'contab_polizasdet AS a
				INNER JOIN '.MAIN_DB_PREFIX.'contab_polizas AS b ON a.fk_poliza = b.rowid
				INNER JOIN '.MAIN_DB_PREFIX.'contab_sat_ctas AS c ON c.codagr = a.cuenta
				WHERE
					a.cuenta >= '.$ini.'
					AND a.cuenta <= '.$fin.'
					AND b.anio <= '.$anio.'
				GROUP BY
					a.cuenta'; */
	        $sql="SELECT d.rowid,d.cta as cuenta,d.descta,ifnull(c.debe,0) as debe,ifnull(c.haber,0) as haber
		FROM ".MAIN_DB_PREFIX."contab_cat_ctas d
	 	LEFT JOIN (SELECT b.cuenta,sum(b.debe) as debe,sum(b.haber) as haber
		FROM ".MAIN_DB_PREFIX."contab_polizas a, ".MAIN_DB_PREFIX."contab_polizasdet b
				WHERE anio<=".$anio."
				AND entity=".$conf->entity." AND a.rowid=b.fk_poliza GROUP BY b.cuenta) c ON d.cta=c.cuenta
		WHERE entity=".$conf->entity."  ORDER BY d.cta";
        
        dol_syslog(get_class($this)."::Prearmar_poliza sql=".$sql, LOG_DEBUG);    	
   
    	$result = $this->db->query($sql);

    	if ($result) {

    		$cc2 = new Contabpolizasdet($this->db);
    		$i=0;
    		$totdebe=0;
    		$tothaber=0;
    		while ($obj = $this->db->fetch_object($result)) {
    			$a=explode('.',$ini);
    			$b=explode('.',$fin);
    			 
    			$c=explode('.',$obj->cuenta);
    			$d='';
    		if($c[0]>=$a[0] && $c[0]<=$b[0]){
    				if($c[0]==$a[0] && $c[0]==$b[0]){
    					if($c[1]>=$a[1] && $c[1]<=$b[1]){
    						if($c[1]==$a[1] && $c[1]==$b[1]){
    							//if($c[2]>=$a[2] && $c[2]<=$b[2]){
    								$d='Si';
    							/* }else{
    								$d='No';
    							} */
    						}else{
    							if($c[1]==$a[1]){
    								//if($c[2]>=$a[2]){
    									$d='Si';
    								/* }else{
    									$d='No';
    								} */
    							}else{
    								if($c[1]==$b[1]){
    									//if($c[2]<=$b[2]){
    										$d='Si';
    									/* }else{
    										$d='No';
    									} */
    								}else{
    									$d='Si';
    								}
    							}
    						}
    					}else{
    						$d='No';
    					}
    				}else{
    					if($c[0]==$a[0]){
    						if($c[1]>=$a[1]){
    							if($c[1]==$a[1]){
    								//if($c[2]>=$a[2]){
    									$d='Si';
    								/* }else{
    									$d='No';
    								} */
    							}else{
    								$d='Si';
    							}
    						}else{
    							$d='No';
    						}
    					}else{
    						if($c[0]==$b[0]){
    							if($c[1]<=$b[1]){
    								if($c[1]==$b[1]){
    									//if($c[2]<=$b[2]){
    										$d='Si';
    									/* }else{
    										$d='No';
    									} */
    								}else{
    									$d='Si';
    								}
    							}else{
    								$d='No';
    							}
    						}else{
    							$d='Si';
    						}
    					}
    				}
    			}else{
    				$d='No';
    			}
    			if($d=='Si' && ($obj->debe!=0 || $obj->haber!=0)){
	    			$i++;
	    			if(($obj->debe!=0 && $obj->haber!=0)){
	    				$cc2->asiento = $i;
	    				$cc2->cuenta = $obj->cuenta;
	    				$tothaber=$tothaber+$obj->debe;
	    				$cc2->debe = 0;
	    				$cc2->haber = $obj->debe;
	    				$cc2->desc = '';
	    				$cc2->uuid = '';
	    				$cc2->fk_poliza =$res;
	    				$cc2->create($user->id);
	    				$i++;
	    				$cc2->asiento = $i;
	    				$cc2->cuenta = $obj->cuenta;
	    				$totdebe=$totdebe+$obj->haber;
	    				$cc2->debe = $obj->haber;
	    				$cc2->haber = 0;
	    				$cc2->desc = '';
	    				$cc2->uuid = '';
	    				$cc2->fk_poliza =$res;
	    				$cc2->create($user->id);
	    			}else{
		    			$cc2->asiento = $i;
				    	$cc2->cuenta = $obj->cuenta;
				    	if($obj->debe != 0){
				    		$tothaber=$tothaber+$obj->debe;
				    		$cc2->debe = 0;
				    		$cc2->haber = $obj->debe;
				    	}else{
				    		$totdebe=$totdebe+$obj->haber;
				    		$cc2->debe = $obj->haber;
				    		$cc2->haber = 0;
				    	}
						$cc2->desc = '';
						$cc2->uuid = '';
						$cc2->fk_poliza =$res;
		               	$cc2->create($user->id);
	    			}
    			}
    		}
    		$i++;
    		if($totdebe>$tothaber){
    			$difer=$totdebe-$tothaber;
    			$tothaber+=$difer;
    			//haber
    			$cc2->asiento = $i;
    			$cc2->cuenta = $cresult;
    			$cc2->debe = 0;
    			$cc2->haber = $difer;
    			$cc2->desc = '';
    			$cc2->uuid = '';
    			$cc2->fk_poliza =$res;
    			$cc2->create($user->id);
			}else{
    			$difer=$tothaber-$totdebe;
    			$totdebe+=$difer;
    			//debe
    			$cc2->asiento = $i;
    			$cc2->cuenta = $cresult;
    			$cc2->debe = $difer;
    			$cc2->haber = 0;
    			$cc2->desc = '';
    			$cc2->uuid = '';
    			$cc2->fk_poliza =$res;
    			$cc2->create($user->id);
  			}
    	} else {
    		$this->error=$this->db->error();
    		dol_syslog(get_class($this).'::Prearmar_poliza '.$this->error,LOG_ERR);
    		$res = -1;
    	}
    	
        return 1;
    }

}

