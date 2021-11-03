 /*
 ***Requiere incluir en la principal los archivos ************************************************
	<script type="text/javascript" src="js/jquery.min.js"></script>	
    <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->
	
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
 */
var jcrop_api=null, obj, muestra, paginas, pagina=1, rotar=0;
var SrTramite=null, TipoConsulta=null;

 $(document).ready(function() {	 
 
	$('body').prepend('<div id="VisorImagine" title="Visor Imagine Technologies" style="display:none">'+
					'<table align="center" border="0" id="TblZoom">'+
					'<tr><td id="celdaiz" align="center"></td>'+
					'<td id="celdadersup" width="200px" align="center" style="vertical-align: top"></td></tr>'+
					'</table>'+
					'</div>');
	$( "#VisorImagine" ).dialog({
		autoOpen: false,
		width:$(window).width()-20,
		height: $(window).height()-20,
		modal: true
	});	
	$('#celdadersup').append('<div id="herramientas" style="padding:10px"><a href="#" title="Girar izquierda"><img src="images/girarizquierda.png" width="15px" border="0"/></a><a href="#" title="Girar derecha"><img src="images/girarderecha.png" width="15px" border="0"/></a><a href="#" title="Imprimir"><img src="images/print.png" width="15px" border="0"/></a><a href="#" title="Descargar"><img src="images/descargar.png" width="15px" border="0"/></a></div>');
	$("#herramientas>a").button().click(Herramientas);
	$('#TblZoom').width($(window).width()-50);
	$('#TblZoom').height($(window).height()-60);

	
 });

 function MuestraVisor(NumTramite, ConsultaImagen, Usuario, HV){
	if(ConsultaImagen == null)
		ConsultaImagen="ImagenesTramite";
	
	$('#listado').remove();
	$.ajax({
		type: "POST",
		url: "config/BuscaImagenes.php",
		data: { op: ConsultaImagen, term: NumTramite, usuario:Usuario, consultaHV:HV}
	}).done(function( data ) {	
		obj =$.parseJSON(data);	
		var opciones="";
		for(i=0; i<obj.length; i++)
			opciones+="<a href='#' id='imagen"+i+"' style='margin:3px'> "+obj[i].id+"</a><br>";
				
		$('#celdadersup').append("<div id='listado' style='position:relative;text-align:left'>"+opciones+"</div>");		
		$('#listado').css('overflowY', 'auto');
		$("#listado>a").button().click(CambiaImagen);
		$("#listado>a").first().click();
	});
	
	$("#VisorImagine" ).dialog("open");
	 SrTramite=NumTramite;
	 TipoConsulta=ConsultaImagen;
	
 }
 
function initJcrop(vec){
	$('#target').Jcrop({
		bgFade:     true,
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1.5,
		setSelect:vec
	},function(){
		jcrop_api = this;
	});
}

function showPreview(coords){
	var rx = $('#DivZoom').width() / coords.w;
	var ry = $('#DivZoom').height() / coords.h;

	$('#preview').css({
		width: Math.round(rx * $('#target').width()) + 'px',
		height: Math.round(ry * $('#target').height()) + 'px',
		marginLeft: '-' + Math.round(rx * coords.x) + 'px',
		marginTop: '-' + Math.round(ry * coords.y) + 'px'
	});
};

function CambiaImagen() {
	if(this.id != null){
		$("#EnVista").remove();
		$(this).html('<span class="ui-button-text"><img id="EnVista" src="images/next.gif" border=0>'+$(this).text()+'</span>');
		muestra=this.id.replace("imagen","");
		rotar=0;
		pagina=1;
		$("#paginador").remove();
		LeerNumeroPaginas();
	}
	
	if(jcrop_api !=null)
		jcrop_api.destroy();
	$('#target').remove();
	$('#DivZoom').remove();
	$('#celdaiz').prepend('<div id="DivZoom" style="overflow:hidden; border:2px dashed gray"><img src="config/ImageMagick.php?img='+obj[muestra].value+'&rot='+rotar+'&pag='+(pagina-1)+'" id="preview" class="jcrop-preview" width="10px"/></div>');
	$('#celdadersup').prepend('<img src="config/ImageMagick.php?img='+obj[muestra].value+'&rot='+rotar+'&pag='+(pagina-1)+'" id="target" width="200px" style=""/>');
	$('#DivZoom').width($(window).width()-270);
	$('#target').load(function(){		
		if($('#target').height()>200){
			$('#target').height(200);
			$('#target').removeAttr('width');
		}
		$("#listado").height($(window).height()-($('#target').height()+140));
		$('#DivZoom').height($('#DivZoom').width()*0.7);
		if(($('#DivZoom').height()+50) > $(window).height()){
			$('#DivZoom').height($(window).height()-80);
			$('#DivZoom').width($('#DivZoom').height()*1.5);
		}
		setTimeout("initJcrop([0, 0, 150])", 1);
	});
	
}

function Herramientas() {
	if($(this).attr('title') == 'Imprimir'){
		jcrop_api.animateTo([0,0,200]);
		$('#preview').jqprint();
		
	}
	
	if($(this).attr('title') == 'Descargar'){
		window.location.href=('config/ImageMagick.php?down=true&sr_tramite='+obj[muestra].sr+'&sr_todo_tramite='+SrTramite+'&tipo_consulta='+TipoConsulta+'&img='+obj[muestra].value+'&nombre='+obj[muestra].id);
	}
	
	if($(this).attr('title') == 'Girar izquierda'){
		rotar-=90;
		if(rotar==-360)
		rotar=0;
		CambiaImagen();
	}
	
	if($(this).attr('title') == 'Girar derecha'){
		rotar+=90;
		if(rotar==360)
		rotar=0;
		CambiaImagen();
	}
	
	if($(this).attr('title') == 'Siguiente'){
		if((pagina+1) <= paginas){
			pagina++;
			CambiaImagen();
			$('#pagin').val(pagina);
		}
	}
	
	if($(this).attr('title') == 'Anterior'){
		if((pagina-1) >=1){
			pagina--;
			CambiaImagen();
			$('#pagin').val(pagina);
		}
	}
}

function LeerNumeroPaginas(){
	$.ajax({
		type: "POST",
		url: 'config/ImageMagick.php?img='+obj[muestra].value+'&pag='+(pagina-1),
		data: { op: "MuestraPaginas"}
	}).done(function( data ) {	
		if(data>1){
			paginas=data;			
			$("#herramientas").append('<div id="paginador"><a href="#" title="Anterior"><img src="images/prev.png" width="15px" border="0"/></a><a href="#" title="Siguiente"><img src="images/next.png" width="15px" border="0"/></a><input type="text" id="pagin" size="2" value="1"> / '+paginas+'</div>');
			$("#paginador>a").button().click(Herramientas);
			$("#pagin").keypress(function(event) {
				if ( event.which == 13 ) {
					if(($('#pagin').val() >=1) && ($('#pagin').val() <=  parseInt(paginas))){
						pagina=$('#pagin').val();
						CambiaImagen();
						$('#pagin').val(pagina);
					}else{
						pagina=1;
						CambiaImagen();
						$('#pagin').val(pagina);
					}
				}
			});
		}
	});
}
