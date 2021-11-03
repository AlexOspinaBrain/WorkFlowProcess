<?php	
session_start(); 
session_destroy();
unset($_SESSION['uscod']);
echo "<script>location.href='./default.php';</script>";
exit;