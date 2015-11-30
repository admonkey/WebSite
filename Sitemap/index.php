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

function recurseDirs($main, $count=0){

    // re-declare global for use inside function
    global $sitemap;
    global $path_real_relative_root;
    global $path_web_relative_root;
    global $included_extensions;
    global $excluded_extensions;
    
    // open the folder
    $dirHandle = opendir($main);
    
    // for each file in folder
    while($file = readdir($dirHandle)){
    
	// if directory, then recurse
        if(is_dir($main.$file."/") && $file != '.' && $file != '..' && $file != '.git' && $file != '_resources'){
        
            //echo "Directory {$file}: <br />";
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
	    
	      // get site path relative to web root
	      $basefile = substr($main.$file,strlen($path_real_relative_root));
	      
	      // create link on gui for selection
	      $print_anchor = "<a target='_blank' href='$path_web_relative_root$basefile'>$basefile</a><br/>\n";
	      
	      // if manually excluded, then style strike-through, and go to next file
	      if (exclude_from_sitemap($basefile)){
	      
		echo "<span style='text-decoration: line-through;'>$print_anchor</span>";
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
	      $modified = date("Y-m-d",filemtime($pathfile));
	      $sitemap .= "\t\t<lastmod>$modified</lastmod>\n";
	      
	      // done
	      $sitemap .= "\t</url>\n";
	      echo $print_anchor;
	    }
        }
    }
    return $count;
}

// begin page html
echo "

  <h1>$section_title</h1>
  <div class='well'>
  
";

// filter information
echo "<h2>Filtered Extensions</h2>
  <ul id='filtered_extensions'>";
foreach ($included_extensions as $in_ext){
  echo "<li>including filename.$in_ext</li>";
  foreach ($excluded_extensions as $ex_ext)
    echo "<li>excluding filename.$ex_ext.$in_ext</li>";
}
echo "</ul>";

// main function call
$number_of_files = recurseDirs($dir);

// close sitemap.xml tag *must come after call to recurseDirs() in main*
$sitemap .= "</urlset>\n";

// count of files in sitemap
echo $number_of_files." locations in sitemap.xml";

// print raw sitemap.xml to screen
echo "<pre>" . htmlentities($sitemap) . "</pre>";

// end page html
echo "
  </div><!-- /.well-->
";

require_once('../_resources/footer.php');

?>
