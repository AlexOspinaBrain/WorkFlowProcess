
<?php
	require '/var/www/equidad/config/conexion.php';
	

EnvioAlertasResumenDiarioporVencer();
			
		
	function EnvioAlertasResumenDiarioporVencer(){
		$result = queryQR("select qq.id_proceso, qq.prv
		from(
?>