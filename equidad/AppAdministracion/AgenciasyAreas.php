<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Escritorio.php'
paar correcta visualización
*/?>
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/ui/js/datepicker/jquery.ui.datepicker-es.js"></script><!-- Calensario	configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/jquery.maskedinput.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	
	<script type="text/javascript" src="js/VisorImagine.js"></script><!--Visor-->
	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->

	<script type="text/javascript">
        $(document).ready(function() {		
			tables();
			$('#filtroAgencia').change(function() {				
					CambiaFiltrosAgencia();		
			});
			
			$('#FiltroAreArea, #FiltroAreAgencia, #FiltroAreCorres').change(function() {				
					CambiaFiltrosArea();		
			});
			
			$('#FiltroDocDocumento, #FiltroDocArea, #FiltroDocPriori').change(function() {				
					CambiaFiltrosDocu();		
			});
		
		});
		
		function tables(){
			$("#GridAgencias").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'AdmAgencias' } 						  
							],
				colModel : [ { display : 'Id agencia', name : 'ofi.codigo', width : 50, sortable : true, align : 'center'},
							 { display : 'Agencia',	name : 'ofi.descrip', width : 200, sortable : true, align : 'left'}, 
							 { display : '', width : 20,	align : 'center'} 
						   ],
				buttons : [ { name : 'Nueva agencia', bclass : 'add', onpress : editaAgencia}, 
							{ separator : true} 
						  ],
				sortname : "ofi.descrip",
				sortorder : "asc",
				
				title : 'Agencias',
				width : 325,
				height :  ($(window).height()-300),
				resizable:false,
				rp:9999999
			});
			
			$("#GridAreas").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'AdmAreas' } 						  
							],
				colModel : [ { display : 'Area', name : 'area', width : 230, sortable : true, align : 'left'},
							 { display : 'Agencia',	name : 'ofi.descrip', width : 168, sortable : true, align : 'left'}, 
							 { display : 'Cor',	name : 'ofi.descrip', width : 20, sortable : true, align : 'left'}, 
							 { display : '', width : 20,	align : 'center'} 
						   ],
				buttons : [ { name : 'Nueva area', bclass : 'add', onpress : editaArea}, 
							{ separator : true} 
						  ],
				sortname : "ofi.descrip",
				sortorder : "asc",
				
				title : 'Areas',
				width : 505,
				height :  ($(window).height()-300),
				resizable:false,
				rp: 9999999
			});
			
			$("#GridDocumentos").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'AdmDocumentos' } 						  
							],
				colModel : [ { display : 'Documento', name : 'doc.tipo', width : 225, sortable : true, align : 'left'},
							 { display : 'Area',	name : 'are.area', width : 200, sortable : true, align : 'left'}, 
							 { display : 'Prioritario', width : 30,	align : 'center'} ,
							 { display : '', width : 20,	align : 'center'} 
						   ],
				buttons : [ { name : 'Nuevo documento', bclass : 'add', onpress : editaDocumento}, 
							{ separator : true} 
						  ],
				sortname : "are.area",
				sortorder : "asc",
				
				title : 'Documentos',
				width : 550,
				height :  ($(window).height()-300),
				resizable: false,
				rp: 9999999
			});
		}
		
		function editaAgencia(id, valor){
			var ingCodigo='<label><span>Codigo de la agencia:</span></label><input type="text" id="codigoAgencia" class="validate[required, custom[integer], minSize[1], maxSize[3]] text-input" style="text-transform:uppercase; width:40px">';
			$( "#dialog-modal" ).html('<form id="formID" class="formular"><fieldset>'+((id == 'Nueva agencia')?ingCodigo:'')+'<label><span>Nombre de la agencia:</span></label><input type="text" class="validate[required] text-input" id="nombreAgencia" value="'+((id == 'Nueva agencia')?'':valor)+'"/ style="text-transform:uppercase;"></fieldset></form>');					
			$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
			$("#formID").submit(function (){
				return false;
			});	
			$( "#dialog-modal" ).dialog({
				autoOpen: true,
				modal: true,
				width: 400,
				height: 250,
				buttons: {
					Aceptar: function() {
						if($('#formID').validationEngine('validate'))
							$.ajax({
								type: "POST",
								url: "config/ajax_querys.php",
								data: { op: "GuardaAgencia",  id: $("#codigoAgencia").val(), data:$("#nombreAgencia").val(), nuevo: id}
							}).done(function( data ) {
								var obj =$.parseJSON(data);	
								if(obj[0].value =='Duplicado')
									alert("El codigo o la agencia ya existe");
								else
									$("#dialog-modal" ).dialog( "destroy" );
								
								$("#GridAgencias").flexReload();
								$("#GridAreas").flexReload();
								$("#GridDocumentos").flexReload();
								
							});
					},					
					Cancelar: function() {
						$("#dialog-modal" ).dialog( "destroy" );
					}
				}
			});
		}
		
		function editaArea(accion,id, area, agencia, corres){
			$( "#nombreArea" ).val(area);
			$( "#agenciaArea" ).val(agencia);
			$( "#Corresp" ).val(corres);
			
			$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
			$('#formID').validationEngine('hide');
			$("#formID").submit(function (){return false;});	
			$( "#dialog-EditArea" ).dialog({
				autoOpen: true,
				modal: true,
				width: 400,
				height: 300,
				buttons: {
					Aceptar: function() {
						if($('#formID').validationEngine('validate'))
							$.ajax({
								type: "POST",
								url: "config/ajax_querys.php",
								data: { op: "GuardaArea",  accion: accion, id:((accion == 'Edita')?id:null) , area: $( "#nombreArea" ).val(), agencia: $( "#agenciaArea" ).val(), corres: $( "#Corresp" ).val(), }
							}).done(function( data ) {
								$("#GridAgencias").flexReload();
								$("#GridAreas").flexReload();
								$("#GridDocumentos").flexReload();
								$("#dialog-EditArea" ).dialog( "destroy" );								
							});
					},				
					Cancelar: function() {
						$("#dialog-EditArea" ).dialog( "destroy" );
					}
				}
			});
		}
		
		function editaDocumento(accion, id, doc, area, prio){
			$( "#nombreDocu" ).val(doc);
			$( "#areaDocu" ).val(area);
			$( "#Prioritario" ).val(prio);
			
			$("#formDocu").validationEngine('attach', {promptPosition : "topLeft"});	
			$('#formDocu').validationEngine('hide');
			$("#formDocu").submit(function (){return false;});	
			$( "#dialog-EditDocu" ).dialog({
				autoOpen: true,
				modal: true,
				width: 400,
				height: 300,
				buttons: {
					Aceptar: function() {
						if($('#formID').validationEngine('validate'))
							$.ajax({
								type: "POST",
								url: "config/ajax_querys.php",
								data: { op: "GuardaDocu",  accion: accion, id:((accion == 'Edita')?id:null) , doc: $( "#nombreDocu" ).val(), areaDocu: $( "#areaDocu" ).val(), Prioritario: $( "#Prioritario" ).val(), }
							}).done(function( data ) {
								//alert(data);
								$("#GridAgencias").flexReload();
								$("#GridAreas").flexReload();
								$("#GridDocumentos").flexReload();
								$("#dialog-EditDocu" ).dialog( "destroy" );								
							});
					},				
					Cancelar: function() {
						$("#dialog-EditDocu" ).dialog( "destroy" );
					}
				}
			});
		}
		
	function CambiaFiltrosAgencia(){
		$("#GridAgencias").flexOptions(
			{params:[{ name: 'consulta', value: 'AdmAgencias' }, 
				 { name:'Agencia', value:$('#filtroAgencia').val()}
				]
			}); 
		$("#GridAgencias").flexReload();
	}	
	
	function CambiaFiltrosArea(){
		$("#GridAreas").flexOptions(
			{params:[{ name: 'consulta', value: 'AdmAreas' }, 
				 { name:'Area', value:$('#FiltroAreArea').val()},
				 { name:'Agencia', value:$('#FiltroAreAgencia').val()},
				 { name:'Corres', value:$('#FiltroAreCorres').val()}
				]
			}); 
		$("#GridAreas").flexReload();
	}
	
	function CambiaFiltrosDocu(){
		$("#GridDocumentos").flexOptions(
			{params:[{ name: 'consulta', value: 'AdmDocumentos' }, 
				 { name:'Doc', value:$('#FiltroDocDocumento option:selected').text()},
				 { name:'Area', value:$('#FiltroDocArea').val()},
				 { name:'Prio', value:$('#FiltroDocPriori').val()}
				]
			}); 
		$("#GridDocumentos").flexReload();
	}		
	</script>
	
	<style>
		.filtros{
			color:#08298A;	
			display:inline;
		}
		.Detalles{
			color:blue;
		}
		.TableResult{
			color:black;
			border-spacing: 0px;
			border-color: #F1F1F1;
		}
		.TableResult td{
			padding:5px;
		}
		.TableResult th{
			text-align: left
		}
		.ui-autocomplete {
			max-height: 100px;
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
		}

	</style>
</head>
<body>	


<table align="center">
<tr align="center">
	<td colspan="3">
		<fieldset style="width:180px;" class='filtros'>
			<legend><b>Agencia: </b></legend>			
			<select id="filtroAgencia" ><option></option></select>
		</fieldset>
	</td>
</tr>
<tr align="center">
	<td colspan="3">
		<fieldset style="width:170px;" class='filtros'>
			<legend><b>Agencia: </b></legend>			
			<select id="FiltroAreAgencia"><option></option></select>
		</fieldset>
		<fieldset style="width:210px;" class='filtros'>
			<legend><b>Area: </b></legend>			
			<select id="FiltroAreArea"  style="width:200px"><option></option></select>
		</fieldset>

		<fieldset style="width:50px;" class='filtros'>
			<legend><b>Corres: </b></legend>			
			<select id="FiltroAreCorres"><option></option><option>SI</option><option>NO</option></select>
		</fieldset>
	</td>
</tr>
<tr align="center">
	<td colspan="3">
		<fieldset style="width:210px;" class='filtros'>
			<legend><b>Documento: </b></legend>			
			<select id="FiltroDocDocumento"  style="width:200px"><option></option></select>
		</fieldset>
		<fieldset style="width:210px;" class='filtros'>
			<legend><b>Area: </b></legend>			
			<select id="FiltroDocArea" style="width:200px"><option></option></select>
		</fieldset>
		<fieldset style="width:50px;" class='filtros'>
			<legend><b>Priori: </b></legend>			
			<select id="FiltroDocPriori"><option></option><option>SI</option><option>NO</option></select>
		</fieldset>
	</td>
</tr>
<td>
<table id="GridAgencias"></table>
</td>

<td>
<table id="GridAreas"></table>
</td>

<td>
<table id="GridDocumentos"></table>
</td>

</tr></table>

<div id="dialog-modal" title="Editar Agencia" style="display:none"></div>
<div id="dialog-EditArea" title="Editar Area" style="display:none">
	<form id="formID" class="formular">
		<fieldset>
			<label><span>Nombre del area:</span></label>
			<input type="text" class="validate[required] text-input" id="nombreArea" style="text-transform:uppercase;"/>
			
			<label><span>Agencia del area:</span></label>
			<select class="validate[required] text-input" id="agenciaArea"><option></option></select>
			
			<label><span>Es area de correspondencia:</span></label>
			<select id="Corresp" class="validate[required] text-input" ><option></option><option>SI</option><option>NO</option></select>
		</fieldset>
	</form>
</div>

<div id="dialog-EditDocu" title="Editar Area" style="display:none">
	<form id="formDocu" class="formular">
		<fieldset>
			<label><span>Documento:</span></label>
			<input type="text" class="validate[required] text-input" id="nombreDocu" style="text-transform:uppercase;"/>
			
			<label><span>Area del documento:</span></label>
			<select class="validate[required] text-input" id="areaDocu"><option></option></select>
			
			<label><span>Prioritario:</span></label>
			<select id="Prioritario" class="validate[required] text-input" ><option></option><option>SI</option><option>NO</option></select>
		</fieldset>
	</form>
</div>

<?=OpcionesSelect('agenciaArea', 'tblradofi ofi', ' ofi.codigo', 'ofi.descrip', "")?>
<?=OpcionesSelect('filtroAgencia', 'tblradofi ofi', ' ofi.codigo', 'ofi.descrip', "")?>
<?=OpcionesSelect('FiltroAreAgencia', 'tblradofi ofi', ' ofi.codigo', 'ofi.descrip', "")?>
<?=OpcionesSelect('FiltroDocDocumento', 'tbltiposdoccorresp doc', 'null', 'tipo', "GROUP BY tipo")?>
<?=OpcionesSelect('FiltroAreArea', 'tblareascorrespondencia are', ' are.areasid', 'are.area', "")?>
<?=OpcionesSelect('FiltroDocArea', 'tblareascorrespondencia are', ' are.areasid', 'are.area', "")?>
<?=OpcionesSelect('areaDocu', 'tblareascorrespondencia are', ' are.areasid', 'are.area', "")?>
</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.trim($row[1]).'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}
?>
