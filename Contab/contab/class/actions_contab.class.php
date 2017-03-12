<?php

class ActionsContab
{

	/**
	 *	Constructor
	 *
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 *
	 */
	function printTopRightMenu($parameters=false)
	{
		echo $this->getTopRightMenu();
	}

	/**
	 *
	 */
	/**
	 * 	Show entity info
	 */
	private function getTopRightMenu()
	{
		global $conf,$user,$langs,$db;
		if($user->rights->contab->acceso){
			$sql='SELECT url
            FROM '.MAIN_DB_PREFIX.'contab_url
            WHERE entity='.$conf->entity;
            $rqs=$db->query($sql);
            $nrw=$db->num_rows($rqs);
            if($nrw>0){
                $rs=$db->fetch_object($rqs);
               
                //Consulta para obtener clave única
                $key = $this->contab_dol_login();
                
                //Creación de la URL
                $url=$rs->url.'?key='.$key;
                $out='';

                if (1)
                {
                    $form=new Form($this->db);

                    //$this->getInfo($conf->entity);
                    
                    $text = img_picto('', 'icon@contab','id="switchconta" onclick="window.open(\''.$url.'\',\'_blank\')" class="entity linkobject" style="width: 160%;"');

                    $htmltext ='<u>'.$langs->trans("Entity").'</u>'."\n";
                    $htmltext.='<br><b>Contab</b>: '.$_SESSION['dol_login']."\n";
                    $htmltext.='<br><b>Direccion: </b>: Contab PRO- Acceso directo'."\n";

                    $out.= $form->textwithtooltip('',$htmltext,2,1,$text,'block_elem',2);

                    $out.= '<div id="dialog-switchentity" class="hideobject" title="'.$langs->trans('Acceso Contab').'">'."\n";
                    //$out.= '<br>'.$langs->trans('SelectAnEntity').' ';
                    //$out.= ajax_combobox('entity');
                    //$out.= $this->select_entities($conf->entity)."\n";
                    $out.= '</div>'."\n";
                }

                $this->resprints = $out;
			}
		}
		
	}
    
    function contab_dol_login(){
        global $conf,$user,$langs,$db;
        
        $sql   ='SELECT * FROM `llx_contab_dol_login` WHERE `dol_login` = "'.$_SESSION['dol_login'].'" LIMIT 1;';
        $rqs   =$db->query($sql);
        
        if(isset($_SESSION['dol_login_cont'])){
            $_SESSION['dol_login_cont']= ((int)$_SESSION['dol_login_cont'])+1;
            $row=$db->fetch_object($rqs);
            $key=$row->login_key;
        }else{
            $_SESSION['dol_login_cont'] = 1;
            $key   =$this->contab_login_key();
        }
        
        
       
        
        if( $_SESSION['dol_login_cont']==1){
            if($db->num_rows($rqs)>0){
            $sql = 'UPDATE `llx_contab_dol_login` SET `login_key`="'.$key.'" WHERE `dol_login`="'.$_SESSION['dol_login'].'" LIMIT 1;';
            }else{
                $sql = 'INSERT INTO 
                            `llx_contab_dol_login`
                                ( 
                                `dol_login`, 
                                `login_key`
                                ) 
                                VALUES 
                                (
                                    "'.$_SESSION['dol_login'].'",
                                    "'.$key.'"
                                );';  
            }
            $rqs=$db->query($sql);
        }
        
        return $key;
    }
    
    function contab_login_key(){
        
        
        $d      =date ("d");
        $m      =date ("m");
        $y      =date ("Y");
        $t      =time();
        $dmt    =$d+$m+$y+$t;    
        $ran    =rand(0,10000000);
        $dmtran =$dmt+$ran;
        $un     =uniqid($_SESSION['dol_login'], true);
        $dmtun  =$dmt.$un;
        $mdun   =md5($dmtran.$un);
        
        return $mdun;
    }


}
?>
