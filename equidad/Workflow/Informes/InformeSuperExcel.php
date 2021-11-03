<?php
header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=InformeExcel.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $_REQUEST['datos_a_enviar'];
?>