<?php
include("config.php");
include("pkggenerator.php");
error_reporting(E_ALL ^ E_NOTICE); // disable error reporting

// Hack to get around PHP.INI setting register_globals=Off
foreach (array_keys($_GET) as $key) $$key=$_GET[$key]; 
foreach (array_keys($_POST) as $key) $$key=$_POST[$key];
$PHP_SELF = $_SERVER["PHP_SELF"];

//This is the Message of the day
if ($_GET['req'] == 'MOTD')
{
  //echo "Welcome to your personal DSOrganize's Server !";
  $myFile = "motd.txt";
  $fh = fopen($myFile, 'r');
  $theData = fread($fh, 500);
  fclose($fh);
  echo ereg_replace("\n", "", $theData);
}

//Show the list of homebrews
elseif ($_GET['req'] == 'HBLIST' and $_GET['ver'] == '3') 
{
  global $dbtable, $order;
  if (!(
    (!$order) ||
    ($order == "name") ||
    ($order == "description") ||
    ($order == "download_name") ||
    ($order == "download_link") ||
    ($order == "date") ||
    ($order == "version") ||
    ($order == "size") ||
    ($order == "category")
  )) { die("DISABLED"); }

  $query=mysql_query("SELECT id,name,description,download_name,download_link," .
                      "date,version,size,category FROM $dbtable");
  while ($v=mysql_fetch_array($query)) {
    echo $v[1];
    echo "|" ;
    //echo $v[2];
    echo ereg_replace("\n", "", $v[2]);
    echo "|" ;
    echo $v[3];
    echo "|" ;
    echo $v[5];
    echo "|" ;
    echo $v[6];
    echo "|" ;
    echo $v[7];
    echo "|" ;
    echo $v[8];
    echo "@" ;
  }
}

//Download
elseif ($_GET['req'] == 'PACKAGE')
{
  $query=mysql_query("SELECT id,name,description,download_name,download_link, ".
                      "date,version,size,category,pkgscript FROM $dbtable");
  while ($v=mysql_fetch_array($query)) {
    $id = $v[0];
    $pkgscript = $v[9];
    $download_name = $v[3];
    $download_link = $v[4];
    // Output PKG script
    if ($_GET['req'] == 'PACKAGE' and $_GET['pkg'] == $v[3]){
      // Update hit counter
      // don't update hit counter when previewing script from homebrew list
      if (! $_GET['ignorehit']){
        if (! $hit_count_update=mysql_query("UPDATE $dbtable " .
                            "SET hit_counter=hit_counter+1 where id=$id") ) {
        // Don't print an error in case it screws over DSO
        /*print "Error when updating hit counter for $id ... \n<BR>";
        die(); */
        }
      }
      // Output default PKG script
      if (!$pkgscript){
        //$pkgscript = "chdr /@down $download_link@";
        $pkgscript = generatePkg($download_link);
      }
      // Output custom PKG script
      //Strip carriage returns and line breaks
      $pkgscript = ereg_replace("\n|\r|\r\n|\n\r", "", $pkgscript);
      echo $pkgscript;
    }
  }
}

else {
  header("Location: pkgmanager.php\n\n");
}
