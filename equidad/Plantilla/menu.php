<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
 

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.metadata.js"></script>
<script type="text/javascript" src="js/jquery.hoverIntent.js"></script>
<script type="text/javascript" src="js/mbMenu.js"></script>

<link rel="stylesheet" type="text/css" href="css/menu.css" media="screen" />

<script type="text/javascript">
function IniciaMenu(){
	$(".myMenu").buildMenu({
		menuWidth:150,
		hasImages:false,
		opacity:.95,
		shadow:false,
		openOnClick:false
	});
}
function AgregaSubMenu(padre, jerarquia, nombre, url){
	if(!$("#menu_"+padre).length){
		$('body').append("<div id='menu_"+padre+"' class='mbmenu'></div>"); 
		$('#'+padre).removeClass("{menu: 'empty'}");
		$('#'+padre).addClass("{menu: 'menu_"+padre+"'}");
		
		$('#'+padre).removeAttr("href");
	}
	$('#menu_'+padre).append("<a href='principal?p="+jerarquia+"' id='"+jerarquia+"'>"+nombre+"</a>");  
}
</script>
</head>
<body>
<?php 
//echo "Usuario:" . $_SESSION['uscod'];
if (strpos ($_GET['p']."","4-1") === 0)
	echo MuestraMenuWF();
else
	echo MuestraMenu();
?>
<?php
$Pagina=MuestraPagina();
?>
<br>
</body>
</html>

<?php
function MuestraMenu(){
	$conect=new conexion();
	$consulta=queryQR("select men.* from adm_menu men, adm_usumenu usm where men.jerarquia_opcion=usm.jerarquia_opcion and usm.usuario_cod='".$_SESSION['uscod']."' and usm.jerarquia_opcion not like '4.1.%' and usm.jerarquia_opcion not like '4.2.%' and usm.jerarquia_opcion not like '4.3.%'  and usm.jerarquia_opcion not like '4.4.%' and usm.jerarquia_opcion not like '4.5.%' order by men.jerarquia_opcion");
	if($consulta->RecordCount() == 0){
		$consulta=queryQR("select * from adm_menu where jerarquia_opcion like '4%'");
	}
	while ($row = $consulta->FetchRow()){
		$aux=explode(".", $row['jerarquia_opcion']);	
		
		$salida="";		

		for($i=0; $i<sizeof($aux); $i++){
			if(strlen($aux[$i]) == 1)
				$salida.="0".$aux[$i];
			else
				$salida.=$aux[$i];
		}

		if($salida>0 && $salida<100)	
			$principal[$salida]=array('jerarquia_opcion' => $row['jerarquia_opcion'], 'opcion' => $row['opcion'], 'url_opcion' => $row['url_opcion'], 'imagen' => $row['imagen']);	
		else
			$submenus[$salida]=array('jerarquia_opcion' => $row['jerarquia_opcion'], 'opcion' => $row['opcion'], 'url_opcion' => $row['url_opcion'], 'imagen' => $row['imagen']);	
	}
	
	ksort($principal);
	ksort($submenus);
	
	$salida='<table  border="0" cellpadding="0" cellspacing="0" bgcolor="#00640A" class="container" align="center">
				<tr><td class="myMenu"><table class="rootVoices" cellspacing="2" cellpadding="0" border="0"><tr>';
	
	foreach($principal as $c=>$v){
		$salida.='<td id="'.$v['jerarquia_opcion'].'" class="rootVoice">'.(($v['imagen']!=null)?'<img src="'.$v['imagen'].'" width="20px" border="0"> ':'').$v['opcion'].'</td>';
	}
	
	$salida.='</tr></table></td></tr></table>';
	
	foreach($submenus as $c=>$v){
		$padre= str_replace(".", "-", substr($v['jerarquia_opcion'], 0, strrpos($v['jerarquia_opcion'], ".")));
		$opcion= str_replace(".", "-", $v['jerarquia_opcion']);
		$salida.='<script>AgregaSubMenu("'.$padre.'", "'.$opcion.'", "'.$v['jerarquia_opcion'].' '.$v['opcion'].'", "'.$v['url_opcion'].'")</script>';
	}
	$salida.='<script>IniciaMenu()</script>';

	$conect->cierracon();
	return $salida;
}
function MuestraPagina(){
	if($_REQUEST['p'] ==NULL)
		return;
		
	$conect=new conexion();
	$consulta=queryQR("select * from adm_usumenu where usuario_cod='".$_SESSION['uscod']."' and jerarquia_opcion='". str_replace ('-', '.', $_REQUEST['p'])."'");
	if( $consulta->FetchRow()){
		$consulta=queryQR("select url_opcion from adm_menu where jerarquia_opcion='". str_replace ('-', '.', $_REQUEST['p'])."'");
		$row=$consulta->FetchRow();
	}else{
		if($_REQUEST['p'] == '4-1')
			return 'config/salir.php';
		else			
			echo "<script>alert('Usted no tiene acceso a esta opci?n');</script>";
	}
	
	$conect->cierracon();
	return $row['url_opcion'];
}

function MuestraMenuWF(){
	$conect=new conexion();
	$submenus = array();
	$consulta=queryQR("select men.* from adm_menu men, adm_usumenu usm where men.jerarquia_opcion=usm.jerarquia_opcion and usm.usuario_cod='".$_SESSION['uscod']."' and usm.jerarquia_opcion like '4.1.%' and usm.jerarquia_opcion not like '4.1.2.%' order by men.jerarquia_opcion");

	while ($row = $consulta->FetchRow()){
		$opWF=substr($row['jerarquia_opcion'] ,4,strlen($row['jerarquia_opcion']));
		$aux=explode(".", $opWF);	
		
		$salida="";		

		for($i=0; $i<sizeof($aux); $i++){
			if(strlen($aux[$i]) == 1)
				$salida.="0".$aux[$i];
			else
				$salida.=$aux[$i];
		}

		if($salida>0 && $salida<100)	
			$principal[$salida]=array('jerarquia_opcion' => $opWF, 'opcion' => $row['opcion'], 'url_opcion' => $row['url_opcion'], 'imagen' => $row['imagen']);	
		else
			$submenus[$salida]=array('jerarquia_opcion' => $opWF, 'opcion' => $row['opcion'], 'url_opcion' => $row['url_opcion'], 'imagen' => $row['imagen']);	
	}
	
	ksort($principal);
	ksort($submenus);
	
	$salida='<table  border="0" cellpadding="0" cellspacing="0" bgcolor="#00640A" class="container" align="center">
				<tr><td class="myMenu"><table class="rootVoices" cellspacing="2" cellpadding="0" border="0"><tr>';
	
	foreach($principal as $c=>$v){
		$OpcionWF = str_replace(".", "-", $v['jerarquia_opcion']);
		$Click=(($v['url_opcion']!=null)? 'onclick="location.href=\'principal?p=4-1-'.$OpcionWF.'\'"' : "");
		$salida.='<td id="'.$v['jerarquia_opcion'].'" class="rootVoice {menu: \'empty\'} " '.$Click.'>'.(($v['imagen']!=null)?'<img src="'.$v['imagen'].'" width="20px" border="0"> ':'').$v['opcion'].'</td>';
	}
	
	$salida.='</tr></table></td></tr></table>';
	
	foreach($submenus as $c=>$v){
		$padre= str_replace(".", "-", substr($v['jerarquia_opcion'], 0, strrpos($v['jerarquia_opcion'], ".")));
		$opcion= str_replace(".", "-", $v['jerarquia_opcion']);
		$salida.='<script>AgregaSubMenu("'.$padre.'", "4-1-'.$opcion.'", "'.$v['opcion'].'", "'.$v['url_opcion'].'")</script>';
	}
	$salida.='<script>IniciaMenu()</script>';

	$conect->cierracon();
	return $salida;
}
?>
