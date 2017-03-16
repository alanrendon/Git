<?
// Script para respaldar base de datos
shell_exec("pg_dump lecaroz | gzip > /root/dump_lecaroz_" . date("d_M_Y") . ".sql.gz");
?>