<?php

echo "

  <li><a href='$path_web_root/SubTree/'>SubTree</a></li>
  <li><a href='$path_web_root/SiteMap/'>SiteMap</a> <a href='javascript:$(\"#sitemap\").show()'>Expand</a>
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
