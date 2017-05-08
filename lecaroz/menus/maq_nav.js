stm_bm(["menu3238",430,"./menus/","blank.gif",0,"","",0,0,0,0,0,1,0,0,"","",0],this);
stm_bp("p0",[0,4,0,0,0,2,16,0,100,"",-2,"",-2,90,0,0,"#000000","transparent","",3,0,0,"#ffffff"]);
stm_ai("p0i0",[0,"Maquinaria  ","","",-1,-1,0,"","_self","","","bullet1.gif","bullet2.gif",16,16,0,"","",0,0,0,0,1,"#73a8b7",0,"#b3ccd3",0,"","",3,3,0,0,"#ffffff","#ffffff","#ffffff","#000000","bold 12pt Arial","bold 12pt Arial",0,0]);
stm_bp("p1",[1,4,0,0,0,3,16,0,70,"",-2,"",-2,60,0,0,"#000000","#ffffff","",3,1,1,"#73a8b7"]);
stm_aix("p1i0","p0i0",[0,"Consulta de Ordenes de Servicio","","",-1,-1,0,"./fac_ord_ser_mod.php","_self","","","search.gif","search.gif",16,16,0,"","",0,0,0,0,1,"#ebf8ff",0,"#acd2dd",0,"","",3,3,0,0,"#ffffff","#ffffff","#333333","#333333","12pt Arial","12pt Arial"]);
stm_aix("p1i1","p1i0",[0,"Orden de Servicio","","",-1,-1,0,"./fac_ord_ser_alta.php"]);
stm_aix("p1i2","p1i0",[0,"Listados","","",-1,-1,0,"./fac_maq_lis.php","mainFrame"]);
stm_aix("p1i3","p1i2",[0,"Consulta","","",-1,-1,0,"./fac_maq_con.php"]);
stm_aix("p1i4","p1i0",[0,"Alta","","",-1,-1,0,"./fac_maq_alta.php","mainFrame","","","insert.gif","insert.gif"]);
stm_ep();
stm_ep();
stm_em();

with(st_ms[st_cm-1])
{
	mcff="mainFrame";
	mcfn=1;
	mcfd=0;
	mcfx=0;
	mcfy=0;
}
