<?php

if (isset($_GET['exclude_file'])) {

  // list of files that you don't want on sitemap
  $exclude_list = file('sitemap.exclude.lst',FILE_IGNORE_NEW_LINES);

  // iterate through list of excluded items
  foreach ($exclude_list as $item){
    // if removing file from list
    if ( $_GET["exclude_file"] == $item ){
      unset($exclude_list[$i]);
      $deleted_item = true;
      $return = "<span style='color:green'>included $_GET[exclude_file]</span><br/>";
      break;
    }
    $i++;
  }

  // if not deleting, then add to list
  if (!isset($deleted_item)){
    $exclude_list []= $_GET["exclude_file"];
    $return = "<span style='color:red'>excluded $_GET[exclude_file]</span><br/>";
  }

  // write to disk
  file_put_contents('sitemap.exclude.lst', implode("\n", $exclude_list));
  echo $return;
  
} else echo "no exclude_file";

?>
