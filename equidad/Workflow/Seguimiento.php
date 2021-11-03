<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Workflow/DetallesTramite.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.droppable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/handlebars.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script type="text/javascript" src="js/FunctionsWorkflow.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script type="text/javascript" src="js/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/tinymce/tiny_mce.js"></script>
	<script src="js/jquery.editinplace.js" type="text/javascript"></script><!--Config mascaras inputs-->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->


	<script type="text/javascript">
        $(document).ready(function() {	
			MuestraCodeBar('<?=$_REQUEST['codebar']?>', '<?=$_SESSION['uscod']?>');
		});	
		
		function tables(){
			$("#GridTramites").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'TramitesPendientes' } ,
							  { name: 'usuario', value: '<?=$_SESSION['uscod']?>' }
							],
				colModel : [ 
							 { display: 'No Tramite', name : 'rad.id_radicacion', width : 50, sortable : true, align: 'center', }, 
							 { display : 'Fecha y hora limite',	name : 'his.fechahora_limite', width : 120, sortable : true, align : 'center'}, 
							 { display : 'Actividad pendiente', name : 'act.desc_actividad', width : 100, sortable : true, align : 'left'}, 
							 { display : 'Tiempo restante',	name : 'tiemporestante', width : 120, sortable : false, align : 'left'}, 
							 { display : 'Nombre reclamante',	name : 'rad.nombre', width : 300, sortable : true, align : 'left'},
							 { display : 'Proceso',	name : 'pro.proceso_desc', width : 200, sortable : true, align : 'left'},
							 { display : 'Servicio',	name : 'ser.desc_servicio', width : 100, sortable : true, align : 'left'},
							 { display : 'Aseguradora',	name : 'com.des_compania', width : 100, sortable : true, align : 'left'},
							 { display : 'Tipo tramite',	name : 'tit.desc_tipotramite', width : 100, sortable : true, align : 'left'},
							 { display : '',	name : 'semaforo', width : 2, sortable : true, align : 'left'}						  
						   ],
				sortname : "his.fechahora_limite",
				sortorder : "asc",				
				title : 'Seguimiento de tramites pendientes',
				width :(($(window).width() > 1300)?1300:($(window).width()-20)),
				height :  ($(window).height()-200),
				resizable:false,
				usepager : true,
				showToggleBtn : false,
				rp:50,
				onSuccess:Semaforo
			});			
		}
		
		function MuestraTramite(IdTramite){
			 location.href='<?=$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]?>'+'&Tramite='+IdTramite;			
		}
    </script>
	
	<style>			
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
		}
		input[type=text]{
		margin:0px 2px;
			display:inline;			
		}
		.DatosCliente{
			font-size: 12px;
			font-family: Verdana;
			border-color: #BDBDBD;
		}
		.DatosCliente td, th {
			padding: 3px;
		}
		
		.DatosCliente th{
			background-color: #EFF2FB;
		}
		table a{
			color: #0101DF;

			font-weight: bold;
			text-decoration:none;
		}
		table a:hover{
			color: #819FF7;
		}
		.TableRadicado th{
			text-align:left;
			padding-right: 10px ;
			color:#08088A;
		}
		.TableRadicado b{			
			padding: 3px 15px;
			color:#424242;
		}
		.Activo{ color: #00891B;}
		
		.Vencido{color: #BA0000;}
		
		.ui-widget-content a{
			color: #08088A;
			font-weight: bold;
			text-decoration:none;
		}
		.ui-widget-content a:hover{
			color: #819FF7;
		}
	</style>
</head>
<body>




<table align="center"><tr><td><table id="GridTramites"></table></td></tr></table>

<?=MuestraPostRadicacion($_REQUEST['Tramite'])?>


</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$result=queryQR("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = $result->FetchRow()){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	return $salida;
}

function MuestraPostRadicacion($Id){
	$salida ="";
	
	if( $Id == NULL){
		$salida.="<script>tables();</script>";
		return $salida;
	}else{
		MuestraDetalles($Id, $_SESSION['uscod'],$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"], 'Seguimiento');
	}
	return;
}
?>
