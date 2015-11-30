<?php

include_once('../_resources/credentials.php');
//$page_title = "Home Page";
require_once('../_resources/header.php');

echo "
  <h1>$section_title</h1>
  <div class='well'>
";

$path = realpath($path_real_relative_root);

// thanks to svens
// http://stackoverflow.com/questions/2528848/recursion-through-a-directory-tree-in-php
$dir = realpath($path_real_relative_root) . "/";
function recurseDirs($main, $count=0){
    $dirHandle = opendir($main);
    while($file = readdir($dirHandle)){
        if(is_dir($main.$file."/") && $file != '.' && $file != '..' && $file != '.git' && $file != '_resources'){
            echo "Directory {$file}: <br />";
            $count = recurseDirs($main.$file."/",$count); // Correct call and fixed counting
        }
        else{
	  if( $file != '.' && $file != '..' ){
            $count++;
            echo "$count: filename: $file in $main \n<br />";
          }
        }
    }
    return $count;
}
$number_of_files = recurseDirs($dir);

echo "
  </div><!-- /.well-->
";

require_once('../_resources/footer.php');

?>
