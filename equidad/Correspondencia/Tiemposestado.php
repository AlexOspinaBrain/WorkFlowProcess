<?php
	require_once ('config/ValidaUsuario.php');
	require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script src="js/ui/js/jquery.ui.core.js"></script>
	<script src="js/ui/js/jquery.ui.widget.js"></script>
	<script src="js/ui/js/jquery.ui.mouse.js"></script>
	<script src="js/ui/js/jquery.ui.draggable.js"></script>
	<script src="js/ui/js/jquery.ui.position.js"></script>
	<script src="js/ui/js/jquery.ui.resizable.js"></script>
	<script src="js/ui/js/jquery.ui.dialog.js"></script>
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script>
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/jquery.maskMoney.js" type="text/javascript"></script>
	<script src="js/handlebars.js" type="text/javascript"></script>
	<script src="js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			var auxi;
			var lastsel;
			$("#ListCP").jqGrid({
				url:'Correspondencia/json.php',
				datatype: "json",
				postData: {
				 	query: 'consuli',
				 	usuario_ord: true,
				    columns:['nom_estado', 'tiempo_estado'],
				    
				},
				height: ($(window).height()-710),

				colNames:['Estado','Tiempo'],
				colModel:[
					{name:'nom_estado',index:'nom_estado', width:250, align:"center", editable:false},
					{name:'tiempo_estado',index:'tiempo_estado', width:60, align:"center", editrules:{required:true,integer:true,maxValue:365,minValue:1}, editable:true},
				],
				scroll:1,
				rowNum:10,
				pager: '#PagerListCP',
				sortname: 'id_estados',
				viewrecords: true,
				sortorder: "asc",
				caption:"Estados de Actividades",
				forceFit : true,
				//cellEdit: true,
				//cellurl : "Correspondencia/editesta.php",
				altRows:true,
				altclass:'altClassrow',

			onSelectRow: function(id){
				if(id && id!==lastsel){
					jQuery('#ListCP').jqGrid('saveRow',lastsel);
					jQuery('#ListCP').jqGrid('restoreRow',lastsel);
					jQuery('#ListCP').jqGrid('editRow',id,true);
					lastsel=id;
				}
			},
			editurl: "Correspondencia/editesta.php",
			});
			$("#ListCP").jqGrid('navGrid','#PagerListCP',{del:false,add:false,edit:false});

			$(document).click(function(event){
				var gr = jQuery("#ListCP").jqGrid('getGridParam','selrow');
				if(($(event.target).attr("role") !== "textbox") && ($(event.target).attr("role") !== "gridcell")){
					if((gr != null) && (auxi != gr)){
						jQuery('#ListCP').jqGrid('saveRow',gr);
						auxi = gr;
					}
				}
			});
		});

	</script>

	<style>	
	    .altClassrow td{
			background-color: #EAF2D3;
		}
		
		.ui-state-hover td{
			background-color: #327E04;
		}
		
		#ListRadica .ui-state-hover a, #ListCP .ui-state-hover a{
			color: white;
		}
		.ui-state-highlight td{
			background-color: #FFFFAB;
		}
		
		#ListRadica a, #ListCP a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListRadica a:hover, #ListCP a:hover{text-decoration: underline}
	</style>

</head>

<body>
	<table align="center">
		<tr>
			<td style="vertical-align: bottom;">
				<table id="ListCP" align="center"></table>
				<div id="PagerListCP"></div>
			</td>
		</tr>
	</table>
	<center>
	  <legend><b><span style="color:red">*</span>Para guardar cambios presione ENTER o cambie de fila</b></legend>
	</center>
</body>
</html>