<?php

include_once('../_resources/credentials.php');
//$page_title = "Home Page";
require_once('../_resources/header.php');

// select file extension type for sitemap,
// $included_extensions = array("php", "html");

// but we're just using php for now because of header includes.
$included_extensions = array("php");

// select 2nd tier file extensions to exclude, such as file.inc.php or file.ajax.php
$excluded_extensions = array("inc","ajax");

// list of files that you don't want on sitemap
$exclude_list = file('sitemap.exclude.lst',FILE_IGNORE_NEW_LINES);
function exclude_from_sitemap($file){
  global $exclude_list;
  foreach ($exclude_list as $item)
    if ( $file == $item ) return true;
  return false;
}

// thanks to svens
// http://stackoverflow.com/questions/2528848/recursion-through-a-directory-tree-in-php

// search entire site for files starting at web root
$dir = realpath($path_real_relative_root) . "/";

// sitemap.xml tag opening
$sitemap = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$list_of_anchors = "<ul>";

function recurseDirs($main, $count=0){

    // re-declare global for use inside function
    global $sitemap;
    global $path_real_relative_root;
    global $path_web_relative_root;
    global $included_extensions;
    global $excluded_extensions;
    global $list_of_anchors;
    
    // open the folder
    $dirHandle = opendir($main);
    // for each file in folder
    while($file = readdir($dirHandle)){
	
	// get site path relative to web root
	$basefile = substr($main.$file,strlen($path_real_relative_root));
	
	// if directory, then recurse
        if(is_dir($main.$file."/") && $file != '.' && $file != '..' && $file != '.git' && $file != '_resources'){
            //echo "Directory {$file}: <br />";
            $list_of_anchors .= "<li><a target='_blank' href='$path_web_relative_root$basefile'>$file</a></li><ul>";
            $count = recurseDirs($main.$file."/",$count);
        }
        // else check if valid file
        else{
        
	    // check for matching file extension
	    $ext = pathinfo($main.$file, PATHINFO_EXTENSION);
	    if (in_array($ext,$included_extensions)){
	    
	      // tier 2 wipe for excluded extensions
	      $ext = pathinfo(substr( $main.$file, 0, ( (strlen($main.$file))-(strlen($ext)+1) ) ), PATHINFO_EXTENSION);
	      if (in_array($ext,$excluded_extensions)) {
		continue;
	      }
	    
	      // create link on gui for selection
	      $print_anchor = "<a target='_blank' href='$path_web_relative_root$basefile'>$file</a>";
	      $open_checkbox = " <input name='exclude_file' type='checkbox' value='$basefile' ";
	      $print_anchor = $print_anchor.$open_checkbox;
	      $close_checkbox = "></input>";
	      
	      // if manually excluded, then style strike-through, and go to next file
	      if (exclude_from_sitemap($basefile)){
	      
		$list_of_anchors .= "<span class='excluded_from_sitemap'><li>$print_anchor $close_checkbox</li></span>\n";
		continue;
		
	      }
	      
	      // increment number of locations in sitemap
	      $count++;
	      //echo "$count: filename: $file in $main \n<br />";
	      
	      // sitemap xml string
	      $sitemap .= "\t<url>\n";
	      
	      // url relative to site root
	      $webfile = "$path_web_relative_root$basefile";
	      
	      // FIX: using invalid protocol relative "//" url,
	      // need dynamic sitemap to serve up "http://" or "https://"
	      $sitemap .= "\t\t<loc>//$_SERVER[SERVER_NAME]$webfile</loc>\n";
	      
	      // last modified
	      $modified = date("Y-m-d",filemtime($main.$file));
	      $sitemap .= "\t\t<lastmod>$modified</lastmod>\n";
	      
	      // done
	      $sitemap .= "\t</url>\n";
	      $list_of_anchors .= "<li>$print_anchor checked $close_checkbox</li>\n";
	    }
        }
    }
    $list_of_anchors .= "</ul>";
    return $count;
}

// begin page html
echo "<h1>$section_title</h1>";

// filter information
echo "
<div id='sitemap_filters_div' class='well'>
  <h2>Filtered Filename Extensions</h2>
    <ul id='filtered_extensions'>";
foreach ($included_extensions as $in_ext){
  echo "<li>including <code style='color:green;'>filename.$in_ext</code></li>";
  foreach ($excluded_extensions as $ex_ext)
    echo "<li>excluding <code>filename.$ex_ext.$in_ext</code></li>";
}
echo "</ul></div><!-- /#sitemap_filters_div.well -->";

// main function call
$number_of_files = recurseDirs($dir);
// close sitemap.xml tag *must come after call to recurseDirs() in main*
$sitemap .= "</urlset>\n";

// list of links to pages found
echo "
<div id='links_to_pages' class='well'>
  <h2>$number_of_files Pages</h2>
  <form method='get' role='form'>
    $list_of_anchors
    <button class='btn btn-primary'>Submit</button>
  </form>
</div><!-- /#links_to_pages.well -->";

//foreach ($_GET["exclude_file"] as $line)
//  echo "$line<br/>";

// print raw sitemap.xml to screen
echo "<div id='raw_sitemap_xml' class='well'><h2>sitemap.xml</h2>";
echo "<pre>" . htmlentities($sitemap) . "</pre>";
echo "</div><!-- /#raw_sitemap_xml.well -->";

require_once('../_resources/footer.php');

?>

<script>
$(function(){
  $("input[name='exclude_file']").change(function(){
    alert($(this).val());
  });
});
</script>

<style>
.excluded_from_sitemap a {
  text-decoration: line-through;
  color: red;
}
</style>
