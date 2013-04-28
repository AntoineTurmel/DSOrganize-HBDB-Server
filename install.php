<?php
  include("config.php");
  mysql_query("CREATE TABLE $dbtable(id INT(11) NOT NULL auto_increment, " .
    "name VARCHAR(100), description VARCHAR(100), download_name VARCHAR(100), " .
    "download_link VARCHAR(200), date VARCHAR(10), version VARCHAR(50), " .
    "size VARCHAR(50), category VARCHAR(50), pkgscript TEXT, " .
    "hit_counter INT(11), " . 
    "PRIMARY KEY  (`id`), UNIQUE id (id))") or die (mysql_error());
  echo("Table created !...<br /><br />");
  echo("You can delete install.php now !<br/><br/>");
  echo("<a href='./'>Proceed to admin interface</a>");
  echo ("</div>\n\n");
  mysql_close($db);
?>
