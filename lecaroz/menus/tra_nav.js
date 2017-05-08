stm_bm(["menu0d9e",430,"./menus/","blank.gif",0,"","",0,0,0,0,0,1,0,0,"","",0],this);
stm_bp("p0",[0,4,0,0,0,2,16,0,100,"",-2,"",-2,90,0,0,"#000000","transparent","",3,0,0,"#ffffff"]);
stm_ai("p0i0",[0,"Trabajadores  ","","",-1,-1,0,"","_self","","","bullet1.gif","bullet2.gif",16,16,0,"","",0,0,0,0,1,"#73a8b7",0,"#b3ccd3",0,"","",3,3,0,0,"#ffffff","#ffffff","#ffffff","#000000","bold 12pt Arial","bold 12pt Arial",0,0]);
stm_bp("p1",[1,4,0,0,0,3,16,0,70,"",-2,"",-2,60,0,0,"#000000","#ffffff","",3,1,1,"#73a8b7"]);
stm_aix("p1i0","p0i0",[0,"Alta","","",-1,-1,0,"TrabajadoresAlta.php","mainFrame","","","insert.gif","insert.gif",16,16,0,"","",0,0,0,0,1,"#ebf8ff",0,"#acd2dd",0,"","",3,3,0,0,"#ffffff","#ffffff","#333333","#333333","12pt Arial","12pt Arial"]);
stm_aix("p1i1","p1i0",[0,"Consultas","","",-1,-1,0,"TrabajadoresConsulta.php","mainFrame","","","search.gif","search.gif"]);
stm_ep();
stm_ai("p0i1",[6,1,"#999999","",0,0,0]);
stm_aix("p0i2","p0i0",[0,"IMSS  "]);
stm_bpx("p2","p1",[]);
stm_aix("p2i0","p1i0",[0,"Impresión de Carta de Alta/Baja del IMSS","","",-1,-1,0,"./fac_imp_mov.php","mainFrame","","","insert2.gif","insert2.gif"]);
stm_aix("p2i1","p2i0",[0,"Impresión de Aviso de Atraso de Alta/Baja del IMSS","","",-1,-1,0,"./fac_imp_avi.php"]);
stm_aix("p2i2","p1i0",[0,"Cambio de Estado de Trabajador","","",-1,-1,0,"./fac_act_est.php"]);
stm_aix("p2i3","p2i0",[0,"Carta alta/baja IMSS","","",-1,-1,0,"./fac_carta_imss.php"]);
stm_aix("p2i4","p0i1",[6,1,"#73a8b7"]);
stm_aix("p2i5","p2i0",[0,"Escanear Documentos","","",-1,-1,0,"fac_doc_emp_alta.php"]);
stm_aix("p2i6","p1i1",[0,"Consultar Documentos","","",-1,-1,0,"fac_doc_emp_con.php","_self"]);
stm_ep();
stm_aix("p0i3","p0i1",[]);
stm_aix("p0i4","p0i0",[0,"Nomina  "]);
stm_bpx("p3","p1",[]);
stm_aix("p3i0","p2i0",[0,"Registro de Nómina Recibida","","",-1,-1,0,"./fac_nom_cap.php"]);
stm_aix("p3i1","p1i1",[0,"Reportes de Nómina","","",-1,-1,0,"./fac_nom_con.php"]);
stm_aix("p3i2","p2i0",[0,"Formato para Nómina","","",-1,-1,0,"./fac_tra_baj_fic.php"]);
stm_aix("p3i3","p2i4",[]);
stm_aix("p3i4","p1i0",[0,"Reporte de Nónima","","",-1,-1,0,"ReporteNomina.php","mainFrame","","","","",0,0]);
stm_aix("p3i5","p3i4",[0,"Consulta","","",-1,-1,0,"ReporteNominaConsulta.php"]);
stm_aix("p3i6","p2i4",[]);
stm_aix("p3i7","p3i4",[0,"Reporte de Aguinaldos","","",-1,-1,0,"ReporteAguinaldo.php"]);
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
