stm_bm(["menu1dd6",430,"./menus/","blank.gif",0,"","",0,0,0,0,0,1,0,0,"","",0],this);
stm_bp("p0",[0,4,0,0,0,2,16,0,100,"",-2,"",-2,90,0,0,"#000000","transparent","",3,0,0,"#ffffff"]);
stm_ai("p0i0",[0,"Catalogos  ","","",-1,-1,0,"","_self","","","bullet1.gif","bullet2.gif",16,16,0,"","",0,0,0,0,1,"#73a8b7",0,"#b3ccd3",0,"","",3,3,0,0,"#ffffff","#ffffff","#ffffff","#000000","bold 12pt Arial","bold 12pt Arial",0,0]);
stm_bp("p1",[1,4,0,0,0,3,16,0,80,"",-2,"",-2,60,0,0,"#000000","#ffffff","",3,1,1,"#73a8b7"]);
stm_aix("p1i0","p0i0",[0,"Arrendadores","","",-1,-1,0,"CatalogoArrendadores.php","mainFrame","","","search.gif","search.gif",16,16,0,"","",0,0,0,0,1,"#ebf8ff",0,"#acd2dd",0,"","",3,3,0,0,"#ffffff","#ffffff","#333333","#333333","12pt Arial","12pt Arial"]);
stm_aix("p1i1","p1i0",[0,"Locales","","",-1,-1,0,"CatalogoLocales.php","mainFrame","","","search.gif","search.gif",-1,-1]);
stm_aix("p1i2","p1i1",[0,"Arrendatarios","","",-1,-1,0,"CatalogoArrendatarios.php"]);
stm_ep();
stm_ai("p0i1",[6,1,"#cccccc","",-1,-1,0]);
stm_aix("p0i2","p0i0",[0,"Rentas "]);
stm_bpx("p2","p1",[]);
stm_aix("p2i0","p1i0",[0,"Recibos automáticos","","",-1,-1,0,"RentasFacturasAutomatico.php","mainFrame","","","insert2.gif","insert2.gif"]);
stm_aix("p2i1","p1i0",[0,"Recibo manual","","",-1,-1,0,"RentasFacturasManual.php","mainFrame","","","insert.gif","insert.gif"]);
stm_aix("p2i2","p1i0",[0,"Consulta de recibos","","",-1,-1,0,"RentasConsulta.php"]);
stm_aix("p2i3","p1i0",[0,"Rentas pagadas","","",-1,-1,0,"RentasPagadas.php"]);
stm_aix("p2i4","p1i0",[0,"Contratos de arrendamiento vencidos","","",-1,-1,0,"RentasArrendatariosVencimiento.php"]);
stm_ep();
stm_aix("p0i3","p0i1",[]);
stm_aix("p0i4","p0i0",[0,"Cartas  ","","",-1,-1,0,"",""]);
stm_bpx("p3","p1",[1,4,0,0,0,3,0]);
stm_aix("p3i0","p1i0",[0,"Escribir Carta","","",-1,-1,0,"./ban_gen_car.php","mainFrame","","","","",0,0]);
stm_aix("p3i1","p3i0",[0,"Escribir Memo","","",-1,-1,0,"./ban_gen_mem.php"]);
stm_ep();
stm_aix("p0i5","p0i1",[]);
stm_aix("p0i6","p0i0",[0,"Arrendamientos  "]);
stm_bpx("p4","p3",[]);
stm_aix("p4i0","p3i0",[0,"Catálogo de arrendamientos","","",-1,-1,0,"ArrendamientosCatalogo.php"]);
stm_aix("p4i1","p3i0",[0,"Arrendamientos vencidos","","",-1,-1,0,"ArrendamientosVencidos.php"]);
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
