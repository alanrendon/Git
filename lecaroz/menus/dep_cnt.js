stm_bm(["menu76c9",430,"./menus/","blank.gif",2,"","",0,0,0,0,0,1,1,1,"","",0],this);
stm_bp("p0",[0,4,0,0,0,2,16,0,100,"",-2,"",-2,90,0,0,"#000000","transparent","",3,0,0,"#ffffff"]);
stm_ai("p0i0",[0,"Inventarios  ","","",-1,-1,0,"","_self","","","bullet1.gif","bullet2.gif",16,16,0,"","",0,0,0,0,1,"#73a8b7",0,"#b3ccd3",0,"","",3,3,0,0,"#ffffff","#ffffff","#ffffff","#000000","bold 12pt Arial","bold 12pt Arial",0,0]);
stm_bp("p1",[1,4,0,0,0,3,16,0,80,"progid:DXImageTransform.Microsoft.Wipe(GradientSize=1.0,wipeStyle=1,motion=forward,enabled=0,Duration=0.50)",5,"progid:DXImageTransform.Microsoft.Wipe(GradientSize=1.0,wipeStyle=1,motion=reverse,enabled=0,Duration=0.50)",4,60,0,0,"#000000","#ffffff","",3,1,1,"#73a8b7"]);
stm_aix("p1i0","p0i0",[0,"Existencias fin de mes","","",-1,-1,0,"./ped_inv_con.php","mainFrame","","","search.gif","search.gif",16,16,0,"","",0,0,0,0,1,"#ebf8ff",0,"#acd2dd",0,"","",3,3,0,0,"#ffffff","#ffffff","#333333","#333333","12pt Arial","12pt Arial"]);
stm_ep();
stm_ai("p0i1",[6,1,"#cccccc","",0,0,0]);
stm_aix("p0i2","p0i0",[0,"Cat�logos  "]);
stm_bpx("p2","p1",[]);
stm_aix("p2i0","p1i0",[0,"Cat�logo de Compa��as","","",-1,-1,0,"./fac_cia_con.php"]);
stm_aix("p2i1","p1i0",[0,"Cat�logo de Proveedores","","",-1,-1,0,"./fac_prov_con.php"]);
stm_aix("p2i2","p1i0",[0,"Cat�logo de Materia Prima","","",-1,-1,0,"./fac_mp_con.php"]);
stm_aix("p2i3","p1i0",[0,"Cat�logo de Precios por Proveedor","","",-1,-1,0,"./fac_dmp_con.php"]);
stm_ep();
stm_aix("p0i3","p0i1",[]);
stm_aix("p0i4","p0i0",[0,"Listados  "]);
stm_bpx("p3","p1",[]);
stm_aix("p3i0","p1i0",[0,"Compras Anuales por Proveedor","","",-1,-1,0,"./fac_pro_anu.php"]);
stm_aix("p3i1","p1i0",[0,"Consumos Anuales por Producto","","",-1,-1,0,"./fac_con_anu.php"]);
stm_aix("p3i2","p1i0",[0,"Consumos Anuales por Compa��a","","",-1,-1,0,"./fac_con_cia.php"]);
stm_aix("p3i3","p1i0",[0,"Promedios de Consumo","","",-1,-1,0,"./fac_pro_con.php"]);
stm_aix("p3i4","p1i0",[0,"Cat�logo de Costos","","",-1,-1,0,"./fac_costos_con.php"]);
stm_ep();
stm_ep();
stm_em();
