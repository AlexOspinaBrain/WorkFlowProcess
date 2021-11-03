<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Escritorio.php'
paar correcta visualización
*/?>
<?php
require_once ('/var/www/equidad/config/ValidaUsuario.php');
//require_once ('/var/www/equidad/config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>	
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

	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->


	<script type="text/javascript">
		var ActualTipologia={"actividades":[]};
		var ObjetoForm="";
        $(document).ready(function(){	
			$( "button" ).button();	
			
			ObjetoForm=$('#EditTipologia').html();
			$('#EditTipologia').remove();
			
			$( "#dialog-EditTipologia" ).dialog({
				autoOpen: false,
				modal: true,
				width: 1100,
				height: ($(window).height()-50),
				close:function(){
					$("#dialog-EditTipologia").html("");
				},
				open:function(){
					$('#dialog-EditTipologia').prepend($(ObjetoForm).show());
					$("#FormTipologia").validationEngine('attach', {promptPosition : "topLeft"});
					$('#ServicioTipologia').change(function (){
						CodigoSuper();
					});
				}
			});

			$('#filtroTipologia, #filtroCompania, #filtroAgencia, #filtroProceso, #filtroServicio, #filtroTipotra').change(function() {
				CambiaFiltros();				
			});
						
			$('#filtroCodTipologia').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});
			tables();
		});
		
		function CargaNuevaActividad(Action){
			$("#nuevaActividad").change(function(){
				arr = jQuery.grep(ActualTipologia.actividades, function(n, i){//busca si el item ya existe
					return n.id_actividad===$("#nuevaActividad").val();
				});
				
				if(Action!="Edita")
						EliminaActividad='<img class="delete" title="Eliminar tarea" src="images/delete.png" onClick="Eliminar(this)" />';
					else
						EliminaActividad='';
				
				if(arr.length==0){//Si no existe la ingresa
					ActualTipologia.actividades.push({"id_actividad":$("#nuevaActividad").val(), "desc_actividad":$("#nuevaActividad option:selected").text(), "flujos":[], "usuarios":[]})
					$( "#LineaProceso").append('<tr actividad="'+$("#nuevaActividad").val()+'"><td>'+EliminaActividad+$("#nuevaActividad option:selected").text()+"</td>"+
												"<td><input class='validate[required] radio' type='radio' name='Inicio' id='Inicio' value='2'></td>"+
												"<td><input class='validate[required] radio' type='radio' name='Fin' id='Fin' value='"+$("#nuevaActividad").val()+"'></td>"+
												'<td id="Flujo'+$("#nuevaActividad").val()+'"></td><td id="Usuarios'+$("#nuevaActividad").val()+'"></td></tr>');
					$( '#Flujo'+$("#nuevaActividad").val()).append('<br><br><label><span>Nuevo flujo:</span></label><select type="text" class="text-input InputFlujo"/>');
					$( '#Usuarios'+$("#nuevaActividad").val()).append('<br><br><label><span>Nuevo usuario:</span></label><input type="text" class="text-input" onFocus="NuevoUsuario('+$("#nuevaActividad").val()+', this)"/>');
					$( "#rowNuevaActividad").appendTo("#LineaProceso");
				}else{
					alert("Actividad ya ingresada");
				}
				$("#nuevaActividad").val("");
				
				$(".InputFlujo").html("<option></option>");
				for(i=0; i<ActualTipologia.actividades.length; i++){
					$(".InputFlujo").append("<option value='"+ActualTipologia.actividades[i].id_actividad+"'>"+ActualTipologia.actividades[i].desc_actividad+"</option>");
				}
				NuevoFlujo();				
			});
			
			
		}
		
		function EditaTipologia(Id){
			$( "#dialog-EditTipologia" ).dialog( "open" );
			$( "#dialog-EditTipologia" ).dialog({ 
				buttons: {
					Aceptar: function() {
						Guardar("Edita");
					},				
					Cancelar: function() {
						$("#dialog-EditTipologia" ).dialog( "close" );
					}
				} 
			});
			CargaTipologia("Edita", Id);
			CargaNuevaActividad("Edita");
		}		

		function CopiaTipologia(Id){
			$( "#dialog-EditTipologia" ).dialog( "open" );
			$( "#dialog-EditTipologia" ).dialog({ 
				buttons: {
					Aceptar: function() {
						Guardar("Copia");
					},				
					Cancelar: function() {
						$("#dialog-EditTipologia" ).dialog( "close" );
					}
				} 
			});
			CargaTipologia("Copia", Id);
			CargaNuevaActividad("Copia");
		}		
		
		function CargaTipologia(Action, Id){
			$.ajax({
				type: "POST",
				url: "Workflow/ajax_querys.php",
				data: { op: "copiaTipologia", term: Id }
			}).done(function( data ) {
				var obj =$.parseJSON(data);
				ActualTipologia=obj[0];
				
				$("#nombreTipologia").val(ActualTipologia.nombre);
				$("#nombreTipologiaalterna").val(ActualTipologia.alterna);
				$("#CompaniaTipologia").val(ActualTipologia.compania);
				$("#AgenciaTipologia").val(ActualTipologia.agencia);
				$("#ProcesoTipologia").val(ActualTipologia.proceso);
				$("#ServicioTipologia").val(ActualTipologia.servicio);
				$("#TipotramiteTipologia").val(ActualTipologia.tipotra);
				
				$("#CodigoSuperTipologia").val(ActualTipologia.codigosuper);
				
				if(ActualTipologia.habilitada=='f')
					$("#HabilitdaTipologia").attr('checked','checked');
							
				$.each(ActualTipologia.actividades, function(i,item){
					var id=item.id_actividad;
							
					if(ActualTipologia.inicio==item.id_actividad)
						var checkinicio='checked="checked"';
						
					if(ActualTipologia.fin==item.id_actividad)
						var checkfin='checked="checked"';
						
					if(Action!="Edita")
						EliminaActividad='<img class="delete" title="Eliminar tarea" src="images/delete.png" onClick="Eliminar(this)">';
					else
						EliminaActividad='';
					
					$( "#LineaProceso").append('<tr actividad="'+item.id_actividad+'"><td>'+EliminaActividad+item.desc_actividad+"</td>"+
												"<td><input class='validate[required] radio' type='radio' name='Inicio' id='Inicio' value='"+item.id_actividad+"' "+checkinicio+"></td>"+
												"<td><input class='validate[required] radio' type='radio' name='Fin' id='Fin' value='"+item.id_actividad+"' "+checkfin+"></td>"+
												'<td id="Flujo'+item.id_actividad+'"></td><td id="Usuarios'+item.id_actividad+'"></td></tr>');
					$( '#Flujo'+item.id_actividad).append('<br><br><label><span>Nuevo flujo:</span></label><select type="text" class="text-input InputFlujo" onClick="NuevoUsuario('+item.id_actividad+', this)"/>');
					$( '#Usuarios'+item.id_actividad).append('<br><br><label><span>Nuevo usuario:</span></label><input type="text" class="text-input" onFocus="NuevoUsuario('+item.id_actividad+', this)"/>');
					$( "#rowNuevaActividad").appendTo("#LineaProceso");
						
					$.each(item.usuarios, function(i,item){
						$( "#Usuarios"+id).prepend("<div actividad='"+id+"' usuario='"+item.id_usuario+"'><img class='delete' title='Eliminar flujo' src='images/delete.png' onClick='Eliminar(this)' />"+item.desc_usuario+"</div>");
					});
							
					$.each(item.flujos, function(i,item){
						$( "#Flujo"+id).prepend("<div actividad='"+id+"' flujo='"+item.id_actividad+"'><img class='delete' title='Eliminar flujo' src='images/delete.png' onClick='Eliminar(this)' />"+item.desc_actividad+"</div>");
					});
				});
				
				$(".InputFlujo").html("<option></option>");
				for(i=0; i<ActualTipologia.actividades.length; i++){
					$(".InputFlujo").append("<option value='"+ActualTipologia.actividades[i].id_actividad+"'>"+ActualTipologia.actividades[i].desc_actividad+"</option>");
				}
				NuevoFlujo();
				CodigoSuper();
			});	
			
			
		}

		function CambiaFiltros(){
			$("#Gridtipologias").flexOptions(
				{params:[{ name: 'consulta', value: 'Admtipologias' } ,
					{ name: 'filtroTipologia', value: $('#filtroTipologia').val()}, 
					{ name: 'filtroCompania', value: $('#filtroCompania').val()}, 		
					{ name: 'filtroAgencia', value: $('#filtroAgencia').val()}, 		
					{ name: 'filtroProceso', value: $('#filtroProceso').val()}, 		
					{ name: 'filtroServicio', value: $('#filtroServicio').val()}, 		
					{ name: 'filtroCodTipologia', value: $('#filtroCodTipologia').val()},
					{ name: 'filtroTipotra', value: $('#filtroTipotra').val()}
				]} 
			); 
			$('#Gridtipologias') .flexOptions({ newp: 1 }).flexReload();
		}	
		
		function tables(){
			$("#Gridtipologias").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'Admtipologias' } 						  
							],
				colModel : [ { display : '', width : 30, align : 'center'},
							 { display : '', width : 30, align : 'center'},
							 { display : 'Codigo',	name : 'tip.id_tipologia', width : 40, sortable : true, align : 'center'}, 
							 { display : 'Nombre Tipología',	name : 'tip.desc_tipologia', width : 400, sortable : true, align : 'left'}, 
							 { display : 'Compañia', name : 'com.des_compania', width : 120, sortable : true, align : 'left'}, 
							 { display : 'Agencia', name : 'age.descrip', width : 120, sortable : true, align : 'left'}, 
							 { display : 'Proceso', name : 'pro.proceso_desc', width : 180, sortable : true, align : 'left'}, 
							 { display : 'Servicio', name : 'ser.desc_servicio', width : 80, sortable : true, align : 'left'}, 
							 { display : 'Codigo super', name : 'tip.codigo_entidad', width : 60, sortable : true, align : 'center'},
							{ display : 'Tipo Tramite', name : 'tip.id_tipotramite', width : 60, sortable : true, align : 'center'}
						   ],
				buttons : [ { name : 'Nueva tipología', bclass : 'add', onpress : nuevaTipologia}, 
							{ separator : true} 
						  ],
				sortname : "tip.id_tipologia",
				sortorder : "desc",				
				title : 'Tipologías',
				width :1190,
				height :  ($(window).height()-300),
				resizable:false,
				usepager : true,
				rp:50
			});
		}
		
		function nuevaTipologia(){
			$( "#dialog-EditTipologia" ).dialog( "open" );
			$( "#dialog-EditTipologia" ).dialog({ 
				buttons: {
					Aceptar: function() {
						Guardar("Nueva");
					},				
					Cancelar: function() {
						$("#dialog-EditTipologia" ).dialog( "close" );
					}
				} 
			});		
			ActualTipologia={"actividades":[]};
			CargaNuevaActividad("Copia");
		}
		
		function NuevoFlujo(){
			$(".InputFlujo").change(function(){
				if($(this).val() != ''){
					id=$(this).parent().parent().attr("actividad")
					input=$(this);
					$.each(ActualTipologia.actividades, function(k,v){
						if(v.id_actividad == id)
							pos = k;
					});
						
					arr = jQuery.grep(ActualTipologia.actividades[pos].flujos, function(n, i){//busca si el item ya existe
						return n.id_actividad===$(input).val();
					});

					if(arr.length==0){//Si no existe la ingresa
						ActualTipologia.actividades[pos].flujos.push({"id_actividad":$(input).val(), "desc_actividad":$(input).find('option').filter('[selected]').text()})
						$( "#Flujo"+id).prepend("<div actividad='"+id+"' flujo='"+$(input).val()+"'><img class='delete' title='Eliminar flujo' src='images/delete.png' onClick='Eliminar(this)' />"+$(input).find('option').filter('[selected]').text()+"</div>");
					}else{
						alert("Actividad ya ingresada");
					}
					$(input).val('');
				}
			});
		}
		
		function NuevoUsuario(id, input){		
			$(input).autocomplete({
				source: "Workflow/ajax_querys.php?op=buscaNombre",
				minLength: 1,
				delay: 20,
				select: function( event, ui ) {//Al seleccionar una opcion	
					$.each(ActualTipologia.actividades, function(k,v){
						if(v.id_actividad == id)
							pos = k;
					});
					
					arr = jQuery.grep(ActualTipologia.actividades[pos].usuarios, function(n, i){//busca si el item ya existe
						return n.id_usuario===ui.item.id;
					});

					if(arr.length==0){//Si no existe la ingresa
						
						$.each(ActualTipologia.actividades[pos].usuarios, function(k,v){//si se existe la opcion todos la elimina al ingresar nuevo
							if(v.id_usuario == "0"){
								ActualTipologia.actividades[pos].usuarios.splice(k,1);
								$("div [actividad='"+id+"'][usuario='0']").remove();
							}
						});
						
						if(ui.item.id=="0"){//elimina todos los anteriores cuando ingresa TODOS
							ActualTipologia.actividades[pos].usuarios.splice(0,ActualTipologia.actividades[pos].usuarios.length);
							$("div [actividad='"+id+"']").remove();
						}
											
						ActualTipologia.actividades[pos].usuarios.push({"id_usuario":ui.item.id, "desc_usuario":ui.item.value})
						$( "#Usuarios"+id).prepend("<div actividad='"+id+"' usuario='"+ui.item.id+"'><img class='delete' title='Eliminar usuario' src='images/delete.png' onClick='Eliminar(this)' />"+ui.item.value+"</div>");
					}else{
						alert("Usuario ya ingresado");
					}
					
					
					$( this ).focus();					
					setTimeout('$( "#LineaProceso input[type=text]").val("");',50)
				}
			});
		}
		
		function Eliminar(Elemento){
			if($(Elemento).parent().attr('actividad') != null && $(Elemento).parent().attr('flujo') != null){
				var actividad=$(Elemento).parent().attr('actividad');
				var flujo=$(Elemento).parent().attr('flujo');
				
				$.each(ActualTipologia.actividades, function(k,v){
					if(v.id_actividad == actividad)
						pos = k;
				});
				
				$.each(ActualTipologia.actividades[pos].flujos, function(k,v){//si se existe la opcion todos la elimina al ingresar nuevo
					if(v.id_actividad == flujo)
						pos2 = k;
				});
				
				ActualTipologia.actividades[pos].flujos.splice(pos2,1);	
				$(Elemento).parent().remove();
				
			}
			
			if($(Elemento).parent().attr('actividad') != null && $(Elemento).parent().attr('usuario') != null){
				$.each(ActualTipologia.actividades, function(k,v){
					if(v.id_actividad == $(Elemento).parent().attr('actividad'))
						pos = k;
				});
				
				$.each(ActualTipologia.actividades[pos].usuarios, function(k,v){//si se existe la opcion todos la elimina al ingresar nuevo
					if(v.id_usuario == $(Elemento).parent().attr('usuario'))
						pos2 = k;	
				});
				ActualTipologia.actividades[pos].usuarios.splice(pos2,1);	
				$(Elemento).parent().remove();
			}
			
			if($(Elemento).parent().parent().attr('actividad') != null && $(Elemento).parent().parent().attr('usuario') == null && $(Elemento).parent().parent().attr('flujo') == null){
				$.each(ActualTipologia.actividades, function(k,v){
					if(v.id_actividad == $(Elemento).parent().parent().attr('actividad'))
						pos = k;
				});

				ActualTipologia.actividades.splice(pos,1);	
				$(Elemento).parent().parent().remove();

				$.each(ActualTipologia.actividades, function(k,v){
					$.each(ActualTipologia.actividades[k].flujos, function(x,y){//si se existe la opcion todos la elimina al ingresar nuevo
						if(y.id_actividad == $(Elemento).parent().parent().attr('actividad'))
							ActualTipologia.actividades[k].flujos.splice(x,1);	
					});
				});
				
				$("div [flujo='"+$(Elemento).parent().parent().attr('actividad')+"']").remove();				
			}
			
			$(".InputFlujo").html("<option></option>");
				for(i=0; i<ActualTipologia.actividades.length; i++){
					$(".InputFlujo").append("<option value='"+ActualTipologia.actividades[i].id_actividad+"'>"+ActualTipologia.actividades[i].desc_actividad+"</option>");
				}
		}
		
		function Guardar(Action){	
		//alert();
			arr = jQuery.grep(ActualTipologia.actividades, function(n, i){//busca si el item ya existe
				return 1;
			});
			if(arr.length == 0)
				alert("Ingrese al menos una actividad");
		
			if($('#FormTipologia').validationEngine('validate') && arr.length > 0){
				if(!confirm("Esta seguro de guardar los cambios?"))
					return;
				ActualTipologia["nombre"] = $("#nombreTipologia").val();
				ActualTipologia["alterna"] = $("#nombreTipologiaalterna").val();
				ActualTipologia["codigosuper"] = $("#CodigoSuperTipologia").val();
				ActualTipologia["compania"] = $("#CompaniaTipologia").val();
				ActualTipologia["agencia"] = $("#AgenciaTipologia").val();
				ActualTipologia["proceso"] = $("#ProcesoTipologia").val();
				ActualTipologia["servicio"] = $("#ServicioTipologia").val();
				ActualTipologia["tptramite"] = $("#TipotramiteTipologia").val();
				
				ActualTipologia["habilitada"] = $("#HabilitdaTipologia").is(':checked');
				ActualTipologia["inicio"] = $("#Inicio:checked").val();
				ActualTipologia["fin"] = $("#Fin:checked").val();

				
				if(Action=="Nueva" || Action=="Copia")
					opcion="GuardaTipologia";
				else
					opcion="EditaTipologia";
					
				$.ajax({
					type: "POST",
					url: "Workflow/ajax_querys.php",
					data: { op: opcion,  Tipologia: ActualTipologia }
				}).done(function( data ) {
					//var obj =$.parseJSON(data);	
					//if(data == '[{"termino":"si"}]'){
						alert("tipologia guardada con exito");
						$("#dialog-EditTipologia" ).dialog( "close" );
					//}else{
					//	alert(data+'ee');
					//}
					
					$('#Gridtipologias') .flexOptions({ newp: 1 }).flexReload();
				});
				
			}
		}
		
		function CodigoSuper(){
			if($("#ServicioTipologia").val() != '1')
				$("#CodigoSuperTipologia").removeClass("validate[required]");
			else
				$("#CodigoSuperTipologia").addClass("validate[required]");
		}
	</script>
	<style>
	
	.editorTip{
			font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
			border-collapse:collapse;
		}
		
		.editorTip td, .editorTip th {
			font-size:1.2em;
			border:1px solid #98bf21;
			padding:3px 7px 2px 7px;
			
		}
		
		.editorTip th {
			font-size:0.9em;
			text-align:left;
			padding-top:5px;
			padding-bottom:4px;
			background-color:#A7C942;
			color:#fff;
			text-align:center;
		}
		
		.editorTip tr.alt td {
			color:#000;
			background-color:#EAF2D3;
		}
		
		.filtros{
			color:#08298A;	
			display:inline;
		}

		.ui-autocomplete {
			max-height: 100px;
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
		}

		
		#LineaProceso td{
			padding:5px;
			font-weight: bold;
			vertical-align: top;
			color:#08298A;
		}
		
		#LineaProceso td[id^="Usuarios"],
		#LineaProceso td[id^="Flujo"]{
			vertical-align: bottom;
		}

		.delete{
			padding: 0 10px;
			cursor: pointer;
		}		
	</style>
</head>
<body>	


<table align="center">
<tr>
	<td align='left'>
		<fieldset style="width:60px;" class='filtros'>
			<legend><b>Codigo: </b></legend>			
			<input id="filtroCodTipologia" style="width:50px"/>
		</fieldset>
		
		<fieldset style="width:320px;" class='filtros'>
			<legend><b>Nombre Sub-tipología: </b></legend>			
			<select id="filtroTipologia" style="width:300px"><option></option></select>
		</fieldset>
	
		<fieldset style="width:160px;" class='filtros'>
			<legend><b>Compañia: </b></legend>			
			<select id="filtroCompania" style="width:150px"><option></option></select>
		</fieldset>
	
		<fieldset style="width:160px;" class='filtros'>
			<legend><b>Agencia: </b></legend>			
			<select id="filtroAgencia" style="width:150px"><option></option></select>
		</fieldset>
	
		<fieldset style="width:160px;" class='filtros'>
			<legend><b>Proceso: </b></legend>			
			<select id="filtroProceso" style="width:150px"><option></option></select>
		</fieldset>
		
		<fieldset style="width:160px;" class='filtros'>
			<legend><b>Servicio: </b></legend>			
			<select id="filtroServicio" style="width:150px"><option></option></select>
		</fieldset>
		<fieldset style="width:160px;" class='filtros'>
			<legend><b>Tipo Tramite: </b></legend>			
			<select id="filtroTipotra" style="width:150px"><option></option></select>
		</fieldset>		
	</td>
</tr>
</table>

<table align="center"><tr><td><table id="Gridtipologias"></table></td></tr></table>


<div id="EditTipologia" title="Editar Tipología" style="display:none">
	<form id="FormTipologia" class="formular" style="padding: 20px;">
		<table align="center"><tr><td colspan="3">
			<label><span>Nombre de la Sub-tipología:</span></label>
			<input type="text" class="validate[required] text-input" id="nombreTipologia" style="width:700px; text-transform:uppercase;"/>
			</td>
			<td>
				<label><span>Codigo super:</span></label>
				<select class="validate[required] text-input" id="CodigoSuperTipologia" style="width:90px"><option></option></select>
			</td>
			<td>
			<label><span>Sub-Tipología habilitada:</span></label>
			<input type="checkbox"  id="HabilitdaTipologia" />
			</td>			
			</tr>
			
			<tr><td colspan="3">
			
			<label><span>Nombre de la tipología:</span></label>
			<input type="text" class="validate[required] text-input" id="nombreTipologiaalterna" style="width:700px; text-transform:uppercase;"/>

			</td></tr>


			<tr><td>
				<label><span>Compañia:</span></label>
			<select class="validate[required] text-input" id="CompaniaTipologia"><option></option></select>
			</td>
			<td>
				<label><span>Agencia:</span></label>
			<select class="validate[required] text-input" id="AgenciaTipologia"><option></option></select>
			</td>
			<td>
				<label><span>Proceso:</span></label>
			<select class="validate[required] text-input" id="ProcesoTipologia"><option></option></select>
			</td>
			<td colspan="2">
				<label><span>Servicio:</span></label>
			<select class="validate[required] text-input" id="ServicioTipologia"><option></option></select>
			</td>
			
			</tr>
			<tr> <td colspan="4">
				<label><span>Tipo Tramite:</span></label>
				<select class="text-input" id="TipotramiteTipologia"><option></option></select> 
			</td> </tr>
			<tr>
				<table id="LineaProceso" border='1' class="editorTip" align="center">
					<tr><th>Actividades</th><th>Ini</th><th>Fin</th><th>Flujo siguiente</th><th>Usuarios</th></tr>
					<tr id="rowNuevaActividad">
						<td><label><span>Nueva Actividad:</span></label>
						<select type="text" class="text-input" id="nuevaActividad"><option></option></select></td>
						<td></td>
						<td></td>
						<td style="min-width:200px"></td>
						<td style="min-width:200px"></td>
					</tr>
				</table>
			</tr>
			</table>
	</form>
</div>

<div id="dialog-EditTipologia" title="Editar Tipología" style="display:none"></div>

<?= OpcionesSelect('CompaniaTipologia', 'wf_compania com', 'com.id_compania', 'com.des_compania', "")?>
<?= OpcionesSelect('filtroCompania', 'wf_compania com', 'com.id_compania', 'com.des_compania', "")?>
<?= OpcionesSelect('AgenciaTipologia', 'tblradofi', 'codigo', 'descrip', " where codigo!='094' and codigo!='999'")?>
<?= OpcionesSelect('filtroAgencia', 'tblradofi', 'codigo', 'descrip', " where codigo!='0' and codigo!='094' and codigo!='999'")?>
<?= OpcionesSelect('ProcesoTipologia', 'wf_proceso', 'id_proceso', 'proceso_desc', "")?>
<?= OpcionesSelect('filtroProceso', 'wf_proceso', 'id_proceso', 'proceso_desc', "")?>
<?= OpcionesSelect('ServicioTipologia', 'wf_servicio', 'id_servicio', 'desc_servicio', "")?>
<?= OpcionesSelect('filtroServicio', 'wf_servicio', 'id_servicio', 'desc_servicio', "")?>
<?//= OpcionesSelect('filtroTipologia', 'wf_tipologia', 'distinct(desc_tipologia)', 'desc_tipologia', " ")?>
<?= OpcionesSelect('filtroTipologia', 'wf_tipologia', 'distinct(desc_tipologia)', 'desc_tipologia', " where eliminado_tipologia = false")?>
<?= OpcionesSelect('nuevaActividad', 'wf_actividad', 'id_actividad', 'desc_actividad', "")?>
<?= OpcionesSelect('CodigoSuperTipologia', 'wf_superfinanciera', 'cod_super', 'cod_super', "")?>
<?= OpcionesSelect('TipotramiteTipologia', 'wf_tipotramite', 'id_tipotramite', 'desc_tipotramite',  " where id_tipotramite = 3 ")?>
<?= OpcionesSelect('filtroTipotra', 'wf_tipotramite', 'id_tipotramite', 'desc_tipotramite',  " where 
	id_tipotramite = 3 ")?>


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
?>
