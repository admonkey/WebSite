<?php

include_once('../_resources/credentials.php');
//$page_title = "Home Page";
require_once('../_resources/header.php');

echo "<h1>$section_title</h1>";

$Directory = new RecursiveDirectoryIterator('/var/www/html/WebSite/');
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

?>

<div class="well">

<?php var_dump($Regex); ?>

</div><!-- /.well-->

<?php require_once('../_resources/footer.php');?>
