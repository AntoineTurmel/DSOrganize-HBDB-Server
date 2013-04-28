<?php
  // This is the link to your MySQL db.
  $dbhost      =  "localhost";
  // This is the user name of your MySQL db.
  $dbuser      =  "user";
  // This is the password of your MySQL db.
  $dbpassword  =  "password";
  // This is the name of your MySQL db.
  $dbdatabase  =  "hbdb";
  // This is the name of the table where are stored hb.
  $dbtable     =  "hbdb";

  // Enable/disable user login for Admin panel.
  // Disable if your server environment doesn't support sessions
  $login_required   =  "true";
  // Admin panel user name
  $admin_user_name  =  "admin";
  // Admin panel user password
  $admin_password   =  "pass";

  // DO NOT CHANGE THIS!
  $db = mysql_connect("$dbhost", "$dbuser", "$dbpassword")
    or die ("Could not connect to database...");
  mysql_select_db("$dbdatabase", $db) or die ("Unable to open database...");    
?>
