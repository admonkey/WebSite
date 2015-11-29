<?php

include_once('../_resources/credentials.php');
//$page_title = "Home Page";
require_once('../_resources/header.php');

echo "
  <h1>$section_title</h1>
  <div class='well'>
";

$path = realpath($path_real_relative_root);

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
$Regex = new RegexIterator($objects, '/^.+\.(php|html)$/i', RecursiveRegexIterator::GET_MATCH);
foreach($Regex as $name => $object){
    echo substr($name,strlen($path_real_relative_root)) . "<br/>\n";
}

echo "
  </div><!-- /.well-->
";

require_once('../_resources/footer.php');

?>
