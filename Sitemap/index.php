<?php

include_once('../_resources/credentials.php');
//$page_title = "Home Page";
require_once('../_resources/header.php');

echo "
  <h1>$section_title</h1>
  <div class='well'>
";

$path = realpath($path_real_relative_root);

$sitemap = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$exclude_list = file('sitemap.exclude.lst',FILE_IGNORE_NEW_LINES);
function exclude_from_sitemap($file){
  global $exclude_list;
  foreach ($exclude_list as $item)
    if ( $file == $item ) return true;
  return false;
}

// thanks to svens
// http://stackoverflow.com/questions/2528848/recursion-through-a-directory-tree-in-php
$dir = realpath($path_real_relative_root) . "/";
function recurseDirs($main, $count=0){
    global $sitemap;
    global $path_real_relative_root;
    global $path_web_relative_root;
    $extensions = array("php", "html");
    $dirHandle = opendir($main);
    while($file = readdir($dirHandle)){
	// if directory, then recurse
        if(is_dir($main.$file."/") && $file != '.' && $file != '..' && $file != '.git' && $file != '_resources'){
            //echo "Directory {$file}: <br />";
            $count = recurseDirs($main.$file."/",$count); // Correct call and fixed counting
        }
        // else check if valid file
        else{
	    $ext = pathinfo($main.$file, PATHINFO_EXTENSION);
	    if (in_array($ext,$extensions)){
	      // get site path relative to web root
	      $basefile = substr($main.$file,strlen($path_real_relative_root));
	      $print_anchor = "<a target='_blank' href='$path_web_relative_root$basefile'>$basefile</a><br/>\n";
	      if (exclude_from_sitemap($basefile)){
		echo "<span style='text-decoration: line-through;'>$print_anchor</span>";
		continue;
	      }
	      $count++;
	      //echo "$count: filename: $file in $main \n<br />";
	      $sitemap .= "\t<url>\n";
	      $webfile = "$path_web_relative_root$basefile";
	      // FIX: using invalid protocol relative url,
	      // need dynamic sitemap to serve up http or https
	      $sitemap .= "\t\t<loc>//$_SERVER[SERVER_NAME]$webfile</loc>\n";
	      $modified = date("Y-m-d",filemtime($pathfile));
	      $sitemap .= "\t\t<lastmod>$modified</lastmod>\n";
	      $sitemap .= "\t</url>\n";
	      
	      echo $print_anchor;
	    }
        }
    }
    return $count;
}
$number_of_files = recurseDirs($dir);
$sitemap .= "</urlset>\n";

echo $number_of_files." locations in sitemap.xml";

echo "<pre>" . htmlentities($sitemap) . "</pre>";

echo "
  </div><!-- /.well-->
";

require_once('../_resources/footer.php');

?>
