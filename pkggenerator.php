<?php

@$testPkg = $_GET['testPkg'];
@$displayClose = $_GET['displayClose'];

//Strip line breaks and carriage returns when testing
//PKG script from update/add screen
if($testPkg){
  echo ereg_replace("\n|\r|\r\n|\n\r", "", generatePkg($testPkg));
}
if($displayClose){
 echo '<div style="text-align:center;"><br><input type="button" value="Close window" onClick="javascript:window.close()"/></div>';
}

error_reporting(E_ALL ^ E_NOTICE); // disable error reporting

## Defines only available in PHP 5, created for PHP4
  if(!defined('PHP_URL_SCHEME')) define('PHP_URL_SCHEME', 1);
  if(!defined('PHP_URL_HOST')) define('PHP_URL_HOST', 2);
  if(!defined('PHP_URL_PORT')) define('PHP_URL_PORT', 3);
  if(!defined('PHP_URL_USER')) define('PHP_URL_USER', 4);
  if(!defined('PHP_URL_PASS')) define('PHP_URL_PASS', 5);
  if(!defined('PHP_URL_PATH')) define('PHP_URL_PATH', 6);
  if(!defined('PHP_URL_QUERY')) define('PHP_URL_QUERY', 7);                       
  if(!defined('PHP_URL_FRAGMENT')) define('PHP_URL_FRAGMENT', 8);   

  function parse_url_compat($url, $component=NULL){
    if(!$component) return parse_url($url);
    ## PHP 5
    if(phpversion() >= 5)
    return parse_url($url, $component);
    ## PHP 4
    $bits = parse_url($url);

    switch($component){
     case PHP_URL_SCHEME: return $bits['scheme'];
     case PHP_URL_HOST: return $bits['host'];
     case PHP_URL_PORT: return $bits['port'];
     case PHP_URL_USER: return $bits['user'];
     case PHP_URL_PASS: return $bits['pass'];
     case PHP_URL_PATH: return $bits['path'];
     case PHP_URL_QUERY: return $bits['query'];
     case PHP_URL_FRAGMENT: return $bits['fragment'];
    }
  }

//Scrawl - Modified file/directory parser which now exports DSO PKG files
function recur_dir($dir,$relativeDir) 
{
  global $pkgScript, $lastDir;
  @$dirlist = opendir($dir);
  if (!$dirlist){
    return "Error generating PKG: \r\nPath '".$dir."' does not exist";
    break;
  }
  while ($file = readdir ($dirlist))
  {
    if ($file != '.' && $file != '..')
    {
      $newpath = $dir.'/'.$file;
      $level = explode('/',$newpath);
      if (is_dir($newpath))
      {
        $pkgScript = $pkgScript."mkdr ".str_replace($_SERVER{'DOCUMENT_ROOT'}.$relativeDir,'', $newpath)."@\r\n"; //mkdr
        $mod_array[] = array(
          'level'=>count($level)-1,
          'path'=>$newpath,
          'name'=>end($level),
          'kind'=>'dir',
          'mod_time'=>filemtime($newpath),
          'content'=>recur_dir($newpath,$relativeDir));
      }
      else{
        if ($lastDir != $dir){
          $lastDir = $dir;
          $changeDir = str_replace($_SERVER{'DOCUMENT_ROOT'}.$relativeDir,'', $dir);
          if (!$changeDir){
            $changeDir = "/";
          }
          $pkgScript = $pkgScript."chdr ".$changeDir."@\r\n"; //chdr
        }
        $pkgScript = $pkgScript."down http://".str_replace($_SERVER{'DOCUMENT_ROOT'}, $_SERVER{'SERVER_NAME'}, $newpath)."@\r\n"; //down
        $mod_array[] = array(
          'level'=>count($level)-1,
          'path'=>$newpath,
          'name'=>end($level),
          'kind'=>'file',
          'mod_time'=>filemtime($newpath),
          'size'=>filesize($newpath));
      }
    }
  }
  closedir($dirlist); 
  //return $mod_array;
  return $pkgScript;
}

// Scrawl -  Crazy script which will handle the different types of paths.
// Eg http://host.com/homebrew.nds or http://host.com/homebrew/
// or /homebrew/, etc
function generatePkg($target){
  @$download = $target;
  @$download_parsed = parse_url($download);
  @$download_path = $download_parsed['path'];
  @$download_host = $download_parsed['host'];
  @$finalPkg = '';
  // If no host is provided, assume a local path is defined
  if (!$download_host){ 
    $download_host = $_SERVER{'SERVER_NAME'};
  }

  // Determine if URL provided is a Path or a file
  // External host and directory - Eg http://externalhost.com/hb_dir/
  if ($download{strlen($download)-1} == "/" && $download_host != $_SERVER{'SERVER_NAME'}){
    return "ERROR Directory defined for external host: \r\nDefining directories is only possible when they reside on a local server";
    break;
  }
  // Local directory - Eg /hb_dir/
  else if ($download{strlen($download)-1} == "/"){
    // Remove trailing slash
    $download_path = substr($download_path,0,strlen($download_path)-1);
    $finalPkg = recur_dir($_SERVER{'DOCUMENT_ROOT'}.$download_path,$download_path);
    }
  else if($download_host == $_SERVER{'SERVER_NAME'}){ // Local host and local file - Eg http://myhost.com/hb_dir/homebrew.nds
    $finalPkg =  "chdr /@down http://".$download_host.$download_path."@";
  }
  // External host and external file - Eg http://externalhost.com/hb_dir/homebrew.nds
  else {
    $finalPkg = "chdr /@down ".$download."@";
  }
  return $finalPkg;
}

?>
