<?php

// write naviagtion menu to file
if (file_put_contents("navigation-menu.inc.php",$_POST["navigation_menu_html"]))
  echo "wrote to file";
else
  echo "error: check permissions on navigation-menu.inc.php";

?>