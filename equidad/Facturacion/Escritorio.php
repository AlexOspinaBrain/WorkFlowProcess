
<div id="escritorioGen">
	<script>
		$(document).ready(function() {	
			$('#TblEscritorio > tbody > tr').each(function() {
				if($(this).index()%2 == 1)
					$(this).attr('class', 'alt');
			});
			$('#TblEscritorio > tbody > tr > td').each(function() {
				var texto=$(this).text();
				$(this).html("<a href='#'>"+ texto +"</a>");
			});

			if($('#TblEscritorio > tbody > tr').length == 0)
				$('#TblEscritorio > tbody').append("<tr><td colspan='2'>No hay actividades pendientes</td></tr>");


			$('#getPenEntregarFac td > a, #getPenFacturaCor td > a').attr("href", "principal.php?p=3-1");
			$('#getPenGenOrdenGiro td > a, #getPenCorOrdenGiro td > a').attr("href", "principal.php?p=3-3");
			$('#getPenCausarOrdenGiro td > a, #getPenCorCausacion td > a').attr("href", "principal.php?p=3-4");
			$('#getPenGenerarCP td > a, #getPenCorCP td > a').attr("href", "principal.php?p=3-5");
			$('#getCPAuditoria td > a').attr("href", "principal.php?p=3-6");
			$('#getCPCerrar td > a').attr("href", "principal.php?p=3-7");

			$('#getRecibirCP td > a').click(function() {
				$.ajax({
					url: "Facturacion/EscritorioCP.php",
					success	:function (data){
						$( "#escritorioGen" ).html(data);	
					}
				});		
			});

			$('#getPenRecibirFac td > a').click(function() {
				$.ajax({
					url: "Facturacion/RecibirFactura.php",
					success	:function (data){
						$( "#escritorioGen" ).html(data);	
					}
				});		
			});

			$('#getPenEnviaOrdenGiro td > a').click(function() {
				$.ajax({
					type: "POST",
					url: "Facturacion/EscritorioOrdenGiro.php",
					data: { estado: "Por enviar"},
					success	:function (data){
						$( "#escritorioGen" ).html(data);	
					}
				});		
			});

			$('#getPenRecibeOrdenGiro td > a').click(function() {
				$.ajax({
					type: "POST",
					url: "Facturacion/EscritorioOrdenGiro.php",
					data: { estado: "Por recibir"},
					success	:function (data){
						$( "#escritorioGen" ).html(data);	
					}
				});		
			});
		});
	</script>

	<style>
		#TblEscritorio a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#TblEscritorio tr:hover td a {color: white;text-decoration: underline}
		#TblEscritorio tr:hover td{background-color: #327E04;}
	</style>

	<br>
	<table class="TblGreen" align="center" id="TblEscritorio">
		<thead>
			<tr><th colspan="2" style="width:500px">Actividades pendientes</th></tr>
		</thead>
		<tbody>
		<?php
			$cantidad =  getPenFacturaCor();
			if($cantidad > 0)
				echo "<tr id='getPenFacturaCor'><td>Facturas pendientes por corregir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenEntregarFac();
			if($cantidad > 0)
				echo "<tr id='getPenEntregarFac'><td>Facturas pendientes por entregar</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenRecibirFac();
			if($cantidad > 0)
				echo "<tr id='getPenRecibirFac'><td>Facturas pendientes por recibir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenGenOrdenGiro();
			if($cantidad > 0)
				echo "<tr id='getPenGenOrdenGiro'><td>Facturas pendientes por generar orden de giro</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenCorOrdenGiro();
			if($cantidad > 0)
				echo "<tr id='getPenCorOrdenGiro'><td>Ordenes de giro pendientes por corregir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenEnviaOrdenGiro();
			if($cantidad > 0)
				echo "<tr id='getPenEnviaOrdenGiro'><td>Ordenes de giro pendientes por enviar</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenRecibeOrdenGiro();
			if($cantidad > 0)
				echo "<tr id='getPenRecibeOrdenGiro'><td>Ordenes de giro pendientes por recibir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenCausarOrdenGiro();
			if($cantidad > 0)
				echo "<tr id='getPenCausarOrdenGiro'><td>Ordenes de giro pendientes por causar</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenCorCausacion();
			if($cantidad > 0)
				echo "<tr id='getPenCorCausacion'><td>Causaciones pendientes por corregir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenGenerarCP();
			if($cantidad > 0)
				echo "<tr id='getPenGenerarCP'><td>Ordenes de giro pendientes por generar CP</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getRecibirCP();
			if($cantidad > 0)
				echo "<tr id='getRecibirCP'><td>Comprobantes de pago pendientes por recibir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getPenCorCP();
			if($cantidad > 0)
				echo "<tr id='getPenCorCP'><td>Comprobantes de pago pendientes por corregir</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getCPAuditoria();
			if($cantidad > 0)
				echo "<tr id='getCPAuditoria'><td>Comprobantes de pago por auditar</td><td align='center'>$cantidad</td></tr>";

			$cantidad =  getCPCerrar();
			if($cantidad > 0)
				echo "<tr id='getCPCerrar'><td>Comprobantes de pago por cerrar</td><td align='center'>$cantidad</td></tr>";
		?>
		</tbody>
	</table>


<?php
	function getPenEntregarFac(){
		$result = queryQR(" select count(*) from fac_radica rad join adm_usuario USING(usuario_cod)  where estado='Recibir en el área' 
			and area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." 
			and jerarquia_opcion='3.1')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenRecibirFac(){
		$result = queryQR("select count(*) from fac_radica rad join fac_historial his USING(id_radica) join adm_usuario usu on 
			usu.usuario_cod=his.usuario_cod where fecha_terminado is null and (rad.estado = 'Recibir en el área' or 
			rad.estado = 'Recibir corrección radicación') and usu.area=(select area from adm_usuario usu join adm_usumenu 
			USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." and (jerarquia_opcion='3.3' or jerarquia_opcion='3.1') limit 1)");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenGenOrdenGiro(){
		$result = queryQR("select count(*) from fac_radica rad join fac_historial his USING(id_radica) join adm_usuario usu on 
			usu.usuario_cod=his.usuario_cod where fecha_terminado is null and rad.estado = 'Generar orden de giro' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) 
			where usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.3')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenEnviaOrdenGiro(){
		$result = queryQR("select count(DISTINCT(id_ordengiro)) from fac_radica rad join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado = 'Enviar área contabilidad' and usu.area=(select area from adm_usuario usu 
			join adm_usumenu USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.3')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenRecibeOrdenGiro(){
		$result = queryQR("select count(DISTINCT(id_ordengiro)) from fac_radica rad join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			(rad.estado='Recibir contabilidad' or rad.estado='Recibir tesorería' or rad.estado='Recibir corrección orden de giro' 
			or rad.estado='Recibir corrección causación') and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and (jerarquia_opcion='3.3' or jerarquia_opcion='3.4' or jerarquia_opcion='3.5') limit 1)");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenCausarOrdenGiro(){
		$result = queryQR("select count(DISTINCT(id_ordengiro)) from fac_radica rad join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado='Causación' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.4')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenGenerarCP(){
		$result = queryQR("select count(DISTINCT(id_ordengiro)) from fac_radica rad join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado='Generar CP' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.5')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getRecibirCP(){
		$result = queryQR("select count(DISTINCT(id_comprobante)) from fac_ordengiro ord join fac_radica rad
			USING(id_ordengiro)  join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			(rad.estado='Recibir Auditoria' or rad.estado='Recibir para cierre' or rad.estado='Recibir corrección Comprobante de pago')  and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and (jerarquia_opcion='3.6' or jerarquia_opcion='3.7') limit 1)");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getCPAuditoria(){
		$result = queryQR("select count(DISTINCT(id_comprobante)) from fac_ordengiro ord join fac_radica rad
			USING(id_ordengiro)  join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado='Auditoria' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.6')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenCorCP(){
		$result = queryQR("select count(DISTINCT(id_comprobante)) from fac_ordengiro ord join fac_radica rad
			USING(id_ordengiro)  join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado='Corrección Comprobante de pago' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.5')");
		$row = $result->FetchRow();		
		return $row [0];
	}
	function getCPCerrar(){
		$result = queryQR("select count(DISTINCT(id_comprobante)) from fac_ordengiro ord join fac_radica rad
			USING(id_ordengiro)  join fac_historial his USING(id_radica) 
			join adm_usuario usu on usu.usuario_cod=his.usuario_cod where fecha_terminado is null and 
			rad.estado='Cerrar tramite' and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.7')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenFacturaCor(){
		$result = queryQR(" select count(*) from fac_radica rad join adm_usuario USING(usuario_cod)  where estado='Correción radicación' 
			and area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." 
			and jerarquia_opcion='3.1')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenCorOrdenGiro(){
		$result = queryQR("select count(*)
 							from fac_ordengiro ord left join
   	 						(select  DISTINCT on (id_ordengiro) id_ordengiro, estado, his.usuario_cod from fac_radica rad join(
        						select DISTINCT on (id_radica) id_radica, usuario_cod from fac_historial his order by id_radica, id_historial desc
    						) his USING (id_radica)
  						)rad USING(id_ordengiro) left join 
  						adm_usuario usu on usu.usuario_cod = rad.usuario_cod 
  						where estado='Corrección orden de giro' and 
  						usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.3')");
		$row = $result->FetchRow();		
		return $row [0];
	}

	function getPenCorCausacion(){
		$result = queryQR("select count(*)
 							from fac_ordengiro ord left join
   	 						(select  DISTINCT on (id_ordengiro) id_ordengiro, estado, his.usuario_cod from fac_radica rad join(
        						select DISTINCT on (id_radica) id_radica, usuario_cod from fac_historial his order by id_radica, id_historial desc
    						) his USING (id_radica)
  						)rad USING(id_ordengiro) left join 
  						adm_usuario usu on usu.usuario_cod = rad.usuario_cod 
  						where estado='Corrección causación' and 
  						usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.4')");
		$row = $result->FetchRow();		
		return $row [0];
	}
?>
</div>