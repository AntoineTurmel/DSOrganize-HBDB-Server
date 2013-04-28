<?php
include("config.php");
if ($login_required == "true"){
  include ("logincheck.php");
}
include("pkggenerator.php");
$ver="v1.3";
// disable error reporting
error_reporting(E_ALL ^ E_NOTICE);

// Hack to get around PHP.INI setting register_globals=Off
foreach (array_keys($_GET) as $key) $$key=$_GET[$key]; 
foreach (array_keys($_POST) as $key) $$key=$_POST[$key];
$PHP_SELF = $_SERVER["PHP_SELF"];

function showlist() {
  global $dbtable, $search, $order, $what;
  if (!(
    (!$order) ||
    ($order == "id") ||
    ($order == "name") ||
    ($order == "description") ||
    ($order == "download_name") ||
    ($order == "download_link") ||
    ($order == "date") ||
    ($order == "version") ||
    ($order == "size") ||
    ($order == "category") ||
    ($order == "hit_counter")
  )) { die("DISABLED"); }
   
  $orderSort = $_GET['ordersort'];
  if ($order){
    $orderq="ORDER BY $order";
  }
  else{
    //default column  order
    $orderq = "ORDER BY name";
  }

  if ($search) $searchq="WHERE name like '%$search%' " .
                        "OR description like '%$search%'";
  if ($search) $sh="&search=$search";

  $query=mysql_query("SELECT id,name,description,download_name," .
  "download_link,date,version,size,category,hit_counter " . 
  "FROM $dbtable $searchq $orderq $orderSort");

  // Reverse ordersort to toggle column sorting
  if($orderSort == "DESC"){
    $orderSort = "&ordersort=ASC";
  }
  else {
    $orderSort = "&ordersort=DESC";
  }

  echo '<table class="hbList" cellpadding="0" cellspacing="0">
    <tr class="hbListHead">
    <td class="noBorder" ><b>&nbsp;</b></td>
    <td class="noBorder"><b>&nbsp;</b></td>
    <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=id'.$orderSort.$sh.'">ID:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=name'.$orderSort.$sh.'">Name:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=description'.$orderSort.$sh.'">Description:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=download_name'.$orderSort.$sh.'">Download&nbsp;Name:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=download_link'.$orderSort.$sh.'">Download&nbsp;Link:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=date'.$orderSort.$sh.'">Date:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=version'.$orderSort.$sh.'">Version:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=size'.$orderSort.$sh.'">Size:</a></b></td>
      <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=category'.$orderSort.$sh.'">Category:</a></b></td>
    <td class="noBorder"><b><a href="'.$_SERVER['SCRIPT_NAME'].'?what=&order=hit_counter'.$orderSort.$sh.'">Hits:</a></b></td>
    </tr>';
  while ($v=mysql_fetch_array($query)) {
  echo '  <tr >
    <td><a href="'.$PHP_SELF.'?what=edit&id='.$v[0].'"><b>Edit</b></a>&nbsp;</td>
    <td><a href="'.$PHP_SELF.'?what=del&id='.$v[0].'"><b>Delete</b></a>&nbsp;</td>
      <td>'.$v[0].'&nbsp;</td>
      <td>'.$v[1].'&nbsp;</td>
      <td>'.$v[2].'&nbsp;</td>
      <td><a href="./?ignorehit=true&req=PACKAGE&pkg='.$v[3].'">'.$v[3].'</a>&nbsp;</td>
      <td>'.$v[4].'&nbsp;</td>
      <td>'.$v[5].'&nbsp;</td>
      <td>'.$v[6].'&nbsp;</td>
      <td>'.$v[7].'&nbsp;</td>
    <td>'.$v[8].'&nbsp;</td>
    <td>'.$v[9].'&nbsp;</td>
    </tr>';
  } 
  echo "</table>";
}

function codeknapp($name) {?>
  <input type="submit" value="<? echo $name; ?>" name="send" ><?
}

function codelogo() {
  global $search, $ver;
?>

  <div style="height:100px;line-height:1.5em;margin-bottom:10px;">
  <form method="POST" action="<? $PHP_SELF ?>" style="float:right;padding-top:5px;padding-bottom:5px;">
  <input type="hidden" name="what" value="search">
  <input type="text" name="search" size="20" value="<? echo $search ?>"> <? codeknapp("Search"); ?>
  </form>
  <a href="<? echo $_SERVER['SCRIPT_NAME'] ?>" style="vertical-align:50%;" ><img align="left" class="logo" border="0" src="logo.png" width="100" height="100"><br></a>
  <b><span class="title">DSOrganize HBDB Server <? echo $ver; ?></b></span>
  <br>by GeekShadow and Scrawl
  <br><a href="<? echo $PHP_SELF ?>?what=add">Add a new entry</a> | 
  <a href="<? echo $_SERVER['SCRIPT_NAME'] ?>?what=motd">Edit Message of the day</a> | 
  <a href="<? echo $_SERVER['SCRIPT_NAME'] ?>?what=resethits">Reset hit counters</a>
  </div>
<? }

function codeedit($do="regnew") {
  global $id, $dbtable;
  if ($do=="update") {
    $query=mysql_query("SELECT id,name,description,download_name," . 
    "download_link,date,version,size,category,pkgscript,hit_counter " .
    "FROM $dbtable where id='$id'");
    $v=mysql_fetch_array($query);
    $id=$v[0];
    $name=$v[1];
    $description=$v[2];
    $download_name=$v[3];
    $download_link=$v[4];
    $date=$v[5];
    $version=$v[6];
    $size=$v[7];
    $category=$v[8];
    $pkgscript=$v[9];
    $hit_counter=$v[10];
  }

  // If form was a postback from "Generate PKG button", determine if the previous state was update or Add new
  if ($do=="genPkg"){
    global $dbtable, $name,$description,$download_name,$download_link,$date,$version,$size,$category,$pkgscript,$id,$hit_counter,$prevWhat;
    $pkgscript = generatePkg($download_link);
    $do="update";
  }
  if ($prevWhat=="regnew"){
    $do="regnew";
  } else if($prevWhat == "update"){
    $do="update";
  }
  ?>

  <form name="details" method="POST" action="<? echo $PHP_SELF ?>">
  <input type="hidden" name="what" value="<? echo $do ?>">
  <input type="hidden" name="prevWhat" value="<? echo $do ?>">
    <table border="0">
      <tr>
        <td>Name of Homebrew: </td>
        <td><input type="text" name="name" size="50" value="<? echo $name ?>" style="width:400px;"></td>
      </tr>
      <tr>
        <td>Description of Homebrew:<br/>(max. 2 lines)  </td>
        <td><textarea name="description" cols="50" rows="2" style="width:400px;"><? echo $description ?></textarea></td>
      </tr>
      <tr>
        <td>Download Link (http://):  </td>
        <td><input type="text" name="download_link" size="50" value="<? echo $download_link ?>" style="width:400px;">&nbsp;<a href="javascript:void(0)" onclick="javascript:pkgScriptHelp()">?</a> | <a href="javascript:void(0)" onclick="javascript:previewPkg()">Test PKG script</a></td>
      </tr>
      <tr>
        <td>Date (MM/DD/YY):  </td>
        <td><input type="text" name="date" size="10" value="<? echo date("m/d/y") ?>"></td>
      </tr>
      <tr>
        <td>Version:  </td>
        <td><input type="text" name="version" size="10" value="<? echo $version ?>"></td>
      </tr>
      <tr>
        <td>Size:  </td>
        <td><input type="text" name="size" size="10" value="<? echo $size ?>"></td>
      </tr>
      <tr>
        <td>Category:  </td>
        <td><input type="text" name="category" size="10" value="<? echo $category ?>"></td>
      </tr>
    <tr>
        <td>Hits:  </td>
        <td><input type="text" name="hit_counter" size="10" value="<? if ($do=="update"){ echo $hit_counter;} else{ echo "0";}?>"></td>
      </tr>
      <tr>
        <td>Custom PKG script:<br/>(optional)  </td>
        <td><textarea cols="50" rows="10" name="pkgscript" style="width:600px;"><? echo $pkgscript ?></textarea></td>
      </tr>

     <tr>
     <td>Customise PKG script:  </td>
     <td>
      <input type="submit" value="Create PKG script" name="send" onClick="javascript:flagPkg()">
      <input type="button" value="+ MakeDir" onClick="javascript:addmkdr()"/>
      <input type="button" value="+ ChangeDir" onClick="javascript:addchdr()"/>
      <input type="button" value="+ Download" onClick="javascript:adddown()"/>
      <input type="button" value="+ Echo" onClick="javascript:addecho()"/>
      <input type="button" value="+ Clean Screen" onClick="javascript:addcls()"/>
      <input type="button" value="+ Wait" onClick="javascript:addwait()"/>
     </td>
    </tr>
    <tr><td>&nbsp;</td><td></td></tr>
    <tr>
      <td></td>
      <td>
      <? 
      if ($do == "regnew") codeknapp("Add");
      if ($do == "update") codeknapp("Update"); 
      ?>
      <input type="button" value="Cancel" onClick="javascript:window.location = '<? echo $_SERVER['SCRIPT_NAME'] ?>'"/> 
      </td>
    </tr>
  </table>
  </form>
<? }

function codeadd() {
  global $dbtable, $name,$description,$download_name,$download_link,$date,$version,$size,$category,$pkgscript,$hit_counter;
  // Auto-generate $download_name based on $name
  $download_name = ereg_replace(" ", "", $name);
  if (! $query=mysql_query("INSERT INTO $dbtable (name,description," . 
      "download_name,download_link,date,version,size,category,pkgscript," .
      "hit_counter) VALUES('$name','$description','$download_name'," .
      "'$download_link','$date','$version','$size','$category'," .
      "'$pkgscript','$hit_counter')")) {
    print "Error when add a new entry... \n<BR>";
    die();
  }
}

function codeupdate() {
  global $dbtable, $name,$description,$download_name,$download_link,$date,$version,$size,$category,$pkgscript,$id,$hit_counter;
  // Auto-generate $download_name based on $name
  $download_name = ereg_replace(" ", "", $name);
  if (! $query=mysql_query("UPDATE $dbtable SET name='$name'," . 
      "description='$description',download_name='$download_name'," .
      "download_link='$download_link',date='$date',version='$version'," .
      "size='$size',category='$category',pkgscript='$pkgscript'," .
      "hit_counter='$hit_counter' where id=$id") ) {
    print "Error when update $id ... \n<BR>";
    die();
  }
}

function editMotd($do="updateMotd"){
  $myFile = "motd.txt";
  $fh = fopen($myFile, 'r');
  $theData = fread($fh, 500);
  fclose($fh);
  ?>
  <form name="details" method="POST" action="<? echo $_SERVER['SCRIPT_NAME'] ?>">
  <input type="hidden" name="what" value="<? echo $do ?>">
  <textarea cols="50" rows="10" name="motdString" style="width:600px;"><? echo $theData ?></textarea><br/>
  <input type="submit" value="Update" name="send" >
  <input type="button" value="Cancel" onClick="javascript:window.location = '<? echo $_SERVER['SCRIPT_NAME'] ?>'">
  </form>
  <?
}

function codeUpdateMotd(){
  global $motdString, $oldMotd;
  $myFile = "motd.txt";
  // Attempt to set file to write enabled
  $setWritePer = chmod($myFile, 0646);
  @$fh = fopen($myFile, 'w') or die("can't open file");
  $setWritePer = chmod($myFile, 0644);
  $stringData = $motdString;
  $fwritesuccess = fwrite($fh, $stringData);
  if($fwritesuccess){
    echo "<b>Success! Message of the day changed to:</b><br><br>";
    echo ereg_replace("\n", "<br>", $stringData);
  }
  else {
    echo "<b>Error! There was a problem writing to $myFile";
  }
  fclose($fh);
  ?>
  <br><br><input type="button" value="OK" onClick="javascript:window.location = '<? echo $_SERVER['SCRIPT_NAME'] ?>'">
  <?
}

function codedel() {
  global $dbtable, $id, $confirm;
  if ($confirm) {
    if (! $query=mysql_query("DELETE FROM $dbtable WHERE id=$id") ) {
      print "Error deleting row $id ... \n<BR>";
      die();
    }
  codelogo();
  showlist();
  }
  if (!$confirm) {
    codelogo();
    echo '<b>DELETE THIS ENTRY ?</b><br><br>Are you sure ? <a href="'.$PHP_SELF.'?what=del&id='.$id.'&confirm=y">Yes</a> <a href="javascript:back();">No</a>';  
  }
}

function resetHitCounters() {
  global $dbtable, $confirm;
  if ($confirm) {
    if (! $query=mysql_query("UPDATE $dbtable SET hit_counter='0'") ) {
      print "Error resetting hit counters... \n<BR>";
      die();
    }
  codelogo();
  showlist();
  }
  if (!$confirm) {
    codelogo();
    echo '<b>RESET ALL HIT COUNTERS ?</b><br><br>Are you sure ? <a href="'.$PHP_SELF.'?what=resethits&confirm=y">Yes</a> <a href="javascript:back();">No</a>';  
  }
}

switch ($what){

  case "add":
    include("header.php");
    codelogo();
    codeedit();
    include("footer.php");
    break;

  case "regnew":
    codeadd();
    header("location: $PHP_SELF");
    break;

  case "update":
    codeupdate();
    header("location: $PHP_SELF");
    break;

  case "edit":
    include("header.php");
    codelogo();
    codeedit("update");
    include("footer.php");
    break;
    
  case "show":
    include("header.php");
    codelogo();
    showlist();
    include("footer.php");
    break;

  case "search":
    include("header.php");
    codelogo();
    showlist();
    include("footer.php");
    break;
    
  case "genPkg":
    include("header.php");
    codelogo();
    codeedit("genPkg");
    include("footer.php");
    break;

  case "del":
    include("header.php");
    codedel();
    include("footer.php");
    break;
    
  case "motd":
    include("header.php");
    codelogo();
    editMotd();
    include("footer.php");
    break;

  case "updateMotd":
    include("header.php");
    codelogo();
    codeUpdateMotd();
    include("footer.php");
    break;
    
  case "resethits":
    include("header.php");
    resetHitCounters();
    include("footer.php");
    break;
    
  default:
    include("header.php");
    codelogo();
    showlist();
    include("footer.php");
    break;
}
?>
