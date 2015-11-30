<?php

include_once('../_resources/credentials.php');
$page_title = "Iterator";
require_once('../_resources/header.php');

echo "
  <h1>$page_title</h1>
  <div class='well'>
";

$path = realpath($path_real_relative_root);

// thanks to salathe
// http://stackoverflow.com/questions/3321547/how-to-use-regexiterator-in-php

abstract class FilesystemRegexFilter extends RecursiveRegexIterator {
    protected $regex;
    public function __construct(RecursiveIterator $it, $regex) {
        $this->regex = $regex;
        parent::__construct($it, $regex);
    }
}

class FilenameFilter extends FilesystemRegexFilter {
    // Filter files against the regex
    public function accept() {
        return ( ! $this->isFile() || preg_match($this->regex, $this->getFilename()));
    }
}

class DirnameFilter extends FilesystemRegexFilter {
    // Filter directories against the regex
    public function accept() {
        return ( ! $this->isDir() || preg_match($this->regex, $this->getFilename()));
    }
}

$exclude_list = file('sitemap.exclude.lst',FILE_IGNORE_NEW_LINES);
function exclude_from_sitemap($file){
  global $exclude_list;
  foreach ($exclude_list as $item)
    if ( $file == $item ) return true;
  return false;
}

$directory = new RecursiveDirectoryIterator($path);
// Filter out _resources and hidden folders
$filter = new DirnameFilter($directory, '/^(?!(\.|_resources))/');
// Filter PHP/HTML files 
$filter = new FilenameFilter($filter, '/\.(?:php|html)$/');

$sitemap = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
foreach(new RecursiveIteratorIterator($filter) as $pathfile) {
  $basefile = substr($pathfile,strlen($path_real_relative_root));
  $print_anchor = "<a target='_blank' href='$path_web_relative_root$basefile'>$basefile</a><br/>\n";
  if (exclude_from_sitemap($basefile)){
    echo "<span style='text-decoration: line-through;'>$print_anchor</span>";
    continue;
  }
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
$sitemap .= "</urlset>\n";

echo "<pre>" . htmlentities($sitemap) . "</pre>";

//file_put_contents('sitemap.xml',$sitemap);

echo "
  </div><!-- /.well-->
";

require_once('../_resources/footer.php');

?>
