<?php

$dbequi  = pg_connect("host=localhost dbname=equidad user=postgres password=QulebrA");
$dbpru  = pg_connect("host=192.168.200.161 dbname=equidad user=postgres password=QulebrA");


$result=pg_query($dbequi,"select * from adm_usuario where upper(usuario_desc) in ('ADMSEG1501',
'MAGSEG1501',
'RUASEG1501',
'CLOPEZ',
'MVARGASG',
'CVEGA',
'KMARTINEZ',
'BETA02',
'MESCOBAR',
'MGARCIAGU',
'GURREA',
'HRODRIGUEZA',
'AVILLEGAS',
'MCGOMEZ',
'CSALAMANCA',
'CJARAMILLO',
'DBARONA',
'FLOPEZ',
'BCACHAYA',
'HRENGIFO',
'CLGONGORA',
'JBENAVIDES',
'JPIEDRAHITA',
'JFERRO',
'JLOAIZA',
'MPINEDA',
'JESPINOSA',
'MGONZALEZ',
'OCATANO',
'OLOPEZ',
'RARISTIZABAL',
'AVILLEGASG',
'DZUNIGA',
'DGARCIAC',
'JAZA',
'CTORO',
'WCASTANO',
'NREDONDO',
'MDIAZ',
'LCEDENO',
'JALANCHEROS',
'DMMORENO',
'AARIAS',
'DBARRETO',
'MRODRIGUEZV',
'RPEDREROS',
'MGIRALDO',
'LORTIZ',
'HMEJURA',
'EMAECHA',
'ATENASAG',
'DPOLO',
'SLAVICTORIA',
'FCALVO',
'MGARCIAO',
'MCARDENAS',
'BGIRALDO',
'SHERRERAG',
'DIHERNANDEZ',
'SEGFMA2101',
'CSALDARRIAGA',
'GVALENCIA',
'RMONTALVO',
'SCAMPO',
'SMARIA',
'OSANTAMARIA ',
'SLARA',
'CNIETO',
'CPRODRIGUEZ',
'OCARDOZO',
'PMORENO',
'ROCASE1101',
'VOROZCO',
'CACSEG',
'JOHEON5101',
'LAQUITE',
'MULSEG5101',
'ZOBANDOI',
'RMEDINA',
'GBLANCO',
'ASALCEDO',
'CSANCHEZ',
'JLONDONO',
'FPEREZ',
'CACEVEDO',
'JDONOSO',
'FGAVILAN',
'GMURILLAS',
'LALARCON',
'LRODRIGUEZP',
'ALUNA',
'MPENAGOS',
'SROCHA',
'ARAMIREZA') ");

//for ($i=3056;$i<3057;$i++){
for ($i=0;$i<pg_num_rows($result);$i++){ 
  $row = pg_fetch_array($result,$i); 

  pg_query($dbequi,"insert into adm_usumenu (usuario_cod,jerarquia_opcion) values ('".$row[usuario_cod]."','4.4')");
  pg_query($dbequi,"insert into adm_usumenu (usuario_cod,jerarquia_opcion) values ('".$row[usuario_cod]."','4.4.1')");
  pg_query($dbequi,"insert into adm_usumenu (usuario_cod,jerarquia_opcion) values ('".$row[usuario_cod]."','4.4.3')");
  pg_query($dbequi,"insert into adm_usumenu (usuario_cod,jerarquia_opcion) values ('".$row[usuario_cod]."','4.4.4')");

}

/*$result=pg_query($dbpru,"select * from wf_tipologia order by id_tipologia ");


for ($i=0;$i<pg_num_rows($result);$i++){
   $row = pg_fetch_array($result,$i);
   echo $row['id_tipologia']."\r";
   $resulteq=pg_query($dbequi,"select * from wf_tipologia where id_tipologia = " . $row['id_tipologia']);
   //if ($resulteq==true){

   $insst = false;//es update o es insert
   if (pg_num_rows($resulteq) != 0){
    if ($row['id_tipotramite']==null){
	   $uptipo = pg_query($dbequi,"update wf_tipologia set desc_tipologia = '".$row['desc_tipologia']."', eliminado_tipologia = '".$row['eliminado_tipologia']."', id_compania = ".$row['id_compania'].", 
	     id_proceso = ".$row['id_proceso'].", id_servicio = ".$row['id_servicio'].", id_agencia = '".$row['id_agencia']."', codigo_entidad = '".$row['codigo_entidad']."', desc_tipologiaalterna = '".$row['desc_tipologiaalterna']."' where id_tipologia = " . $row['id_tipologia']);
    }else{
     $uptipo = pg_query($dbequi,"update wf_tipologia set desc_tipologia = '".$row['desc_tipologia']."', eliminado_tipologia = '".$row['eliminado_tipologia']."', id_compania = ".$row['id_compania'].", 
       id_proceso = ".$row['id_proceso'].", id_servicio = ".$row['id_servicio'].", id_agencia = '".$row['id_agencia']."', codigo_entidad = '".$row['codigo_entidad']."', desc_tipologiaalterna = '".$row['desc_tipologiaalterna']."', id_tipotramite = '".$row['id_tipotramite']."'  where id_tipologia = " . $row['id_tipologia']);      
    }
   }else{
    $insst = true; //es insert
    if ($row['id_tipotramite']==null){
	   $uptipo=pg_query($dbequi,"insert into wf_tipologia (id_tipologia, desc_tipologia, eliminado_tipologia, id_compania, id_proceso, id_servicio, id_agencia, codigo_entidad, desc_tipologiaalterna) values (".$row['id_tipologia'].",
	     '".$row['desc_tipologia']."','".$row['eliminado_tipologia']."',".$row['id_compania'].",".$row['id_proceso'].",".$row['id_servicio'].",'".$row['id_agencia']."','".$row['codigo_entidad']."','".$row['desc_tipologiaalterna']."')");
    }else{
     $uptipo=pg_query($dbequi,"insert into wf_tipologia (id_tipologia, desc_tipologia, eliminado_tipologia, id_compania, id_proceso, id_servicio, id_agencia, codigo_entidad, desc_tipologiaalterna, id_tipotramite) values (".$row['id_tipologia'].",
       '".$row['desc_tipologia']."','".$row['eliminado_tipologia']."',".$row['id_compania'].",".$row['id_proceso'].",".$row['id_servicio'].",'".$row['id_agencia']."','".$row['codigo_entidad']."','".$row['desc_tipologiaalterna']."', '".$row['id_tipotramite']."')");      
    }
   }

  if ($uptipo==true){

    //elimina flujos para construirlos de nuevo
  	/*$rswf=pg_query($dbequi,"select * from wf_workflow where id_tipologia = " . $row['id_tipologia']);
  	if (pg_num_rows($rswf) != 0){
  	  for ($j=0;$j<pg_num_rows($rswf);$j++){
    		$row1 = pg_fetch_array($rswf,$j);

    		pg_query($dbequi,"delete from wf_flujo where id_workflow = " . $row1['id_workflow']);
    		pg_query($dbequi,"delete from wf_workflowusuarios where id_workflow = " . $row1['id_workflow']);
    		pg_query($dbequi,"delete from wf_workflow where id_tipologia = " . $row1['id_tipologia']);
  	  }
  	}
    */
    /*
    if ($insst == true){
        $rswf=pg_query($dbpru,"select * from wf_workflow where id_tipologia = " . $row['id_tipologia']);
        if (pg_num_rows($rswf) != 0){
          for ($j=0;$j<pg_num_rows($rswf);$j++){
                $row1 = pg_fetch_array($rswf,$j);

      		  pg_query($dbequi,"insert into wf_workflow (id_workflow,inicio_workflow,fin_workflow,id_tipologia,id_actividad) values  (".$row1['id_workflow'].",'".$row1['inicio_workflow']."','".$row1['fin_workflow']."',
      			".$row1['id_tipologia'].",".$row1['id_actividad'].")");
      	  }
        }

        if (pg_num_rows($rswf) != 0){
          for ($j=0;$j<pg_num_rows($rswf);$j++){
		        $row1 = pg_fetch_array($rswf,$j);
		        $rsfl=pg_query($dbpru,"select * from wf_flujo where id_workflow = " . $row1['id_workflow']);
		        if (pg_num_rows($rsfl) != 0){
		          for ($h=0;$h<pg_num_rows($rsfl);$h++){
		                $row2 = pg_fetch_array($rsfl,$h);

      				  pg_query($dbequi,"insert into wf_flujo (id_workflow,id_flujo) values (".$row2['id_workflow'].",".$row2['id_flujo'].")");

      			  }
			       }

                        $rsfl=pg_query($dbpru,"select * from wf_workflowusuarios where id_workflow = " . $row1['id_workflow']);
                        if (pg_num_rows($rsfl) != 0){
                          for ($h=0;$h<pg_num_rows($rsfl);$h++){
                                $row2 = pg_fetch_array($rsfl,$h);

                                pg_query($dbequi,"insert into wf_workflowusuarios (id_workflow,usuario_cod) values (".$row2['id_workflow'].",".$row2['usuario_cod'].")");

                          }
                        }
          }

        }

    }
  }

}

*/
?>
