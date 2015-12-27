<?php

echo "

  <li><a href='$path_web_root/SubTree/'>SubTree</a></li>
  <li><a href='$path_web_root/SiteMap/'>SiteMap</a> <a href='javascript:void(0)' onclick='toggle_nav_item($(this))'><span class='navigation_menu_toggle glyphicon glyphicon-plus-sign'></span></a>
    <ul id='sitemap' style='display:none'>
      <li><a href='$path_web_root/SiteMap/iter.php'>Iterator</a></li>
    </ul>
  </li>
  <li><a href='$path_web_root/SubTree/'>SubTree</a></li>
  <li><a href='$path_web_root/Profiles/'>Profiles</a></li>
  <li><a href='$path_web_root/Forum/'>Forum</a></li>
  <li><a href='$path_web_root/Forms/'>Forms</a></li>
  <li><a href='$path_web_root/Tables/'>Tables</a></li>
  <li><a href='$path_web_root/FancyBox/'>FancyBox</a></li>
  <li><a href='$path_web_root/Embed/'>Embed</a></li>



";

?>

<script>
  function toggle_nav_item(toggle){
    toggle.find(".glyphicon").toggleClass("glyphicon-plus-sign glyphicon-minus-sign");
    toggle.parent().find("ul").toggle("blind");
  }
</script>

<style>
  .navigation_menu_toggle {
    margin-top : 20px;
    margin-right : 10px;
    float : right;
  }
</style>
