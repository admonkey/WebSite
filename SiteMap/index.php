<?php

require_once('_resources/credentials.inc.php');
//$page_title = "Home Page";
require_once('_resources/header.inc.php');

// select file extension type for sitemap,
// $included_extensions = array("php", "html");

function get_page_title($page_file){
  // get first 10 lines of page
  $section = file_get_contents($page_file, NULL, NULL, 0, 200);
  // get into array
  $lines = explode("\n", $section);
  // find line containing $page_title
  foreach($lines as $line){
    if ( !(strpos($line, "page_title") === false) ){
      // extract & return value of page title
      $page_title_array = explode("\"",$line);
      return $page_title_array[1];
    }
  }
  return basename($page_file);
}

// but we're just using php for now because of header includes.
$included_extensions = array("php");

// select 2nd tier file extensions to exclude, such as file.inc.php or file.ajax.php
$excluded_extensions = array("inc","ajax","bounce");

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
$dir = realpath($path_real_root) . "/";

// sitemap.xml tag opening
$sitemap = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$list_of_anchors = "<ul>";

$navigation_menu = "";

function get_site_pages($main, $count=0){

    // re-declare global for use inside function
    global $sitemap;
    global $path_real_root;
    global $path_web_root;
    global $included_extensions;
    global $excluded_extensions;
    global $list_of_anchors;
    global $navigation_menu;
    
    // open the folder
    $dirHandle = opendir($main);
    // for each file in folder
    while($file = readdir($dirHandle)){
	
	// get site path relative to web root
	$basefile = substr($main.$file,strlen($path_real_root));
	
	// create link on gui for selection
	$extracted_page_title = get_page_title($path_real_root.$basefile);
	$print_anchor = "<a target='_blank' href='$path_web_root$basefile'>$extracted_page_title</a>";
	$open_checkbox = " <input name='exclude_file' type='checkbox' value='$basefile' ";
	$print_anchor = $print_anchor.$open_checkbox;
	$close_checkbox = "></input>";
	
	// if directory, then recurse
        if(is_dir($main.$file."/") && $file != '.' && $file != '..' && $file != '.git' && $file != '_resources'){
            

            
            // if manually excluded, then style strike-through, and go to next file
	    if (exclude_from_sitemap($basefile)){
	    
	      $list_of_anchors .= "<span class='excluded_from_sitemap'><li>$print_anchor $close_checkbox</li></span>\n";
	      continue;
	      
	    }
            $count++;
            //$list_of_anchors .= "<span><li><a target='_blank' href='$path_web_root$basefile'>$file</a></li><ul>";
            $list_of_anchors .= "<span><li>$print_anchor checked $close_checkbox</li></span>\n<ul>";
            $navigation_menu .= "<li><a href='$path_web_root$basefile'>$extracted_page_title</a>\n<ul style='display:none' class='sortable'>\n";
            // <a href='javascript:void(0)' onclick='toggle_nav_item($(this))'><span class='navigation_menu_toggle glyphicon glyphicon-plus-sign'></span></a>
            //
            $count = get_site_pages($main.$file."/",$count);
            $navigation_menu .= "</ul>\n</li>\n";
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
	      
	      // ignore index files
	      if ( !(strpos($file, "index") === false) ) continue;
	    
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
	      $webfile = "$path_web_root$basefile";
	      
	      // FIX: using invalid protocol relative "//" url,
	      // need dynamic sitemap to serve up "http://" or "https://"
	      $sitemap .= "\t\t<loc>//$_SERVER[SERVER_NAME]$webfile</loc>\n";
	      
	      // html navigation menu
	      $navigation_menu .= "<li><a href='$webfile'>$extracted_page_title</a></li>\n";
	      
	      // last modified
	      $modified = date("Y-m-d",filemtime($main.$file));
	      $sitemap .= "\t\t<lastmod>$modified</lastmod>\n";
	      
	      // done
	      $sitemap .= "\t</url>\n";
	      $list_of_anchors .= "<span><li>$print_anchor checked $close_checkbox</li></span>\n";
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
$number_of_files = get_site_pages($dir);
// close sitemap.xml tag *must come after call to get_site_pages() in main*
$sitemap .= "</urlset>\n";

// list of links to pages found
echo "
<div id='links_to_pages' class='well'>
  <h2><span id='number_of_files'>$number_of_files </span>Pages</h2>
    $list_of_anchors
  <div id='change_notifications'></div>
  <a id='regenerate_sitemap_xml' class='btn btn-primary' style='display:none' href=''>Regenerate sitemap.xml</a>
</div><!-- /#links_to_pages.well -->";

// preview html navigation menu
?>

<div class='well'>
  <div class='row'>


  <div id='nav_menu_current_col' class='col-sm-6'>
    <h2>Current</h2>
    
    <ul id='current_navigation_menu' class='sidebar-nav navigation-menu' style='background-color: black; position:relative;'>
      <?php require("dev.navigation-menu.inc.php"); ?>
    </ul>
  </div><!-- /#nav_menu_current_col -->


  <div id='nav_menu_preview_col' class='col-sm-6'>
    <h2>Preview</h2>

    <ul id='preview_navigation_menu' class='sortable sidebar-nav navigation-menu' style='background-color: black; position:relative;'>
      <?php echo "$navigation_menu"; ?>
    </ul>

    <a href='javascript:ajax_write_nav_menu()' class='btn btn-primary' style='margin:10px'>Write to File</a>

    <script>
      function ajax_write_nav_menu(){
	$("#preview_navigation_menu").find("ul, li").removeAttr('class');
	var navigation_menu_html = $("#preview_navigation_menu").html();
	$.post("write.navigation-menu.ajax.php", { navigation_menu_html:navigation_menu_html}, function(result){
	      alert(result);
	});
      }
      $(function(){
	$("#preview_navigation_menu").find("ul").each(function(){
	  var list_items = $(this).find("li");
	  if(list_items.length == 0)
	    $(this).remove();
	  else
	    $(this).parent().prepend("<a href='javascript:void(0)' onclick='toggle_nav_item($(this))'><span class='navigation_menu_toggle glyphicon glyphicon-plus-sign'></span></a>");
	});
	$( ".sortable" ).sortable();
	$( ".sortable" ).disableSelection();
      });
      
    </script>
  </div><!-- /#nav_menu_preview_col -->


  </div><!-- /.row -->
</div><!-- /.well -->

<?php


// print raw html navigation menu to screen
echo "<div class='well'><h2>navigation menu</h2>";
echo "<pre id='raw_navigation_menu'>" . htmlentities($navigation_menu) . "</pre>";
echo "</div><!-- /.well -->";

// print raw sitemap.xml to screen
echo "<div class='well'><h2>sitemap.xml</h2>";
echo "<pre id='raw_sitemap_xml'>" . htmlentities($sitemap) . "</pre>";
echo "</div><!-- /.well -->";


require_once('_resources/footer.inc.php');

?>

<script>
$(function(){
  $("input[name='exclude_file']").change(function(){
    var exclude_item = $(this);
    $.ajax({url: "sitemap.exclude.lst.update.ajax.php?exclude_file=" + exclude_item.val(), 
      statusCode: {
	200: function(result){
	  $("#change_notifications").append(result);
	  exclude_item.parents("span").toggleClass("excluded_from_sitemap");
	  $("#number_of_files").hide();
	  $("#raw_sitemap_xml").hide();
	  $("#regenerate_sitemap_xml").show();
	},
	403: function(){
	  $("#change_notifications").append("<p><label class='label label-danger'>ERROR: couldn't write to sitemap.exclude.lst - check permissions</label></p>");
	  exclude_item.prop("checked", !exclude_item.prop("checked"));
	}
      },
      cache: false
    });
  });
});
</script>

<style>
.excluded_from_sitemap a {
  text-decoration: line-through;
  color: red;
}
</style>
