stm_bm(["menu41c7",430,"./menus/","blank.gif",2,"","",0,0,0,0,0,1,0,0,"","",0],this);
stm_bp("p0",[0,4,0,0,0,2,16,0,100,"",-2,"",-2,90,0,0,"#000000","transparent","",3,0,0,"#ffffff"]);
stm_ai("p0i0",[0,"Catálogos  ","","",-1,-1,0,"","_self","","","bullet1.gif","bullet2.gif",16,16,0,"","",0,0,0,0,1,"#73a8b7",0,"#b3ccd3",0,"","",3,3,0,0,"#ffffff","#ffffff","#ffffff","#000000","bold 12pt Arial","bold 12pt Arial",0,0]);
stm_bp("p1",[1,4,0,0,0,3,16,0,80,"",-2,"",-2,10,0,0,"#000000","#ffffff","",3,1,1,"#73a8b7"]);
stm_aix("p1i0","p0i0",[0,"Catálogo de Cuentas","","",-1,-1,0,"ban_cue_con.php","mainFrame","","","search.gif","search.gif",16,16,0,"","",0,0,0,0,1,"#ebf8ff",0,"#acd2dd",0,"","",3,3,0,0,"#ffffff","#ffffff","#333333","#333333","12pt Arial","12pt Arial"]);
stm_ep();
stm_ai("p0i1",[6,1,"#cccccc","",0,0,0]);
stm_aix("p0i2","p0i0",[0,"Faltantes  ","","",-1,-1,0,"","mainFrame"]);
stm_bpx("p2","p1",[]);
stm_aix("p2i0","p1i0",[0,"Consulta de Faltantes","","",-1,-1,0,"ban_fal_lis.php"]);
stm_ep();
stm_aix("p0i3","p0i1",[]);
stm_aix("p0i4","p0i0",[0,"Efectivos  ","","",-1,-1,0,"ban_efe_con.php","_self","","","bullet1.gif","bullet2.gif",-1,-1]);
stm_bpx("p3","p1",[]);
stm_aix("p3i0","p1i0",[0,"Listado","","",-1,-1,0,"ban_efe_con.php"]);
stm_ep();
stm_aix("p0i5","p0i1",[]);
stm_aix("p0i6","p0i4",[0,"Cometra  ","","",-1,-1,0,""]);
stm_bpx("p4","p1",[1,4,0,0,0,3,0]);
stm_aix("p4i0","p1i0",[0,"Comprobantes de billetes falsos","","",-1,-1,0,"BilletesFalsos.php","mainFrame","","","","",0,0]);
stm_aix("p4i1","p4i0",[0,"Consulta de comprobantes de billetes falsos","","",-1,-1,0,"BilletesFalsosConsulta.php"]);
stm_ep();
stm_aix("p0i7","p0i1",[]);
stm_aix("p0i8","p0i6",[0,"Cartas  "]);
stm_bpx("p5","p1",[]);
stm_aix("p5i0","p1i0",[0,"Carta de solicitud de video a Cometra","","",-1,-1,0,"CometraCartaSolicitudVideo.php","mainFrame","","","insert2.gif","insert2.gif",-1,-1]);
stm_ep();
stm_aix("p0i9","p0i1",[]);
stm_aix("p0i10","p0i6",[0,"Infonavit  "]);
stm_bpx("p6","p1",[]);
stm_aix("p6i0","p1i0",[0,"Impresión de recibos","","",-1,-1,0,"InfonavitImprimirRecibos.php","mainFrame","","","insert2.gif","insert2.gif"]);
stm_aix("p6i1","p1i0",[0,"Consulta de pagos pendientes","","",-1,-1,0,"fac_inf_pen_con.php"]);
stm_ep();
stm_aix("p0i11","p0i1",[]);
stm_aix("p0i12","p0i0",[0,"Prestamos<BR>Personales  "]);
stm_bpx("p7","p1",[1,4,0,0,0,3,16,0,80,"",-2,"",-2,60]);
stm_aix("p7i0","p1i0",[0,"Consulta de prestamos","","",-1,-1,0,"PrestamosEmpleadosConsulta.php"]);
stm_aix("p7i1","p1i0",[0,"Consulta de trabajadores","","",-1,-1,0,"TrabajadoresConsultaSimple.php"]);
stm_aix("p7i2","p1i0",[0,"Memorandums de Prestamos Pendientes","","",-1,-1,0,"./ban_pre_mem.php"]);
stm_ep();
stm_ep();
stm_em();
