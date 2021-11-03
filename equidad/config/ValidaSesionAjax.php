<?php	
session_start(); 
if (isset($_SESSION['uscod']))
	echo 'true';	
else
	echo 'false';	
