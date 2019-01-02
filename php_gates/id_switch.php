<?php
if(isset($_GET['id']) && $_GET['id'] == "companyname")
/* Change this to your payload if you are not using payload_changer.py, leave as is if you are using it and feed the name via the -p switch */  
  {
  header("Location: placeholder");
  }
else
  {
/* If you want to be craftier, use a de-armed version of your web page or payload, 404 is quick and dirty */    
  header("HTTP/1.0 404 Not Found");    
  die();
  }
?>
