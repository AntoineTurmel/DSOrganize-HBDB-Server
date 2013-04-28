<?php
session_start();

// Generate random user/pass if none defined
if(!isset($admin_user_name)){
  $admin_user_name = date('s')*rand(1000, 100000);
}
if(!isset($admin_password)){
	$admin_password = date('s')*rand(1000, 100000);
}

if (!isset($_SESSION['user'])) {
  
  if(isset($_POST['u_name'])) 
    $u_name = $_POST['u_name'];

  if(isset($_POST['u_password'])) 
    $u_password = $_POST['u_password'];

  if(!isset($u_name)) {
    ?>
    <html>
    <head>
      <title><?php echo $_SERVER["HTTP_HOST"]; ?> : Authentication Required</title>
      <link href="styles.css" rel="stylesheet" type="text/css">
    </head>
    <body>
      <font face=verdana size=2>
      <center>
      <?php
      $form_to = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
      echo $_SERVER["PHP_SELF"];
      if(isset($_SERVER["QUERY_STRING"]))
      $form_to = $form_to ."?". $_SERVER["QUERY_STRING"];
      ?>
      <form method=post action=<?php echo $form_to; ?>>
      <p>&nbsp;</p>
      <table border=0 width=350 class="loginbox">
      <tr>
      <td colspan="2">
         <font face=verdana size=2 class="loginBoxTitle"><b>Welcome to DSOrganize HBDB Extended</b><br><br></font>
      </td>
      </tr>
      <td width="100" ><font face=verdana size=2 ><b>User Name</b></font></td>
      <td><font face=verdana size=2><input type=text name=u_name size=20></font></td></tr>
      <tr>
      <td><font face=verdana size=2><B>Password</B></font></td>
      <td><font face=verdana size=2><input type=password name=u_password size=20></font></td>
      </tr>
      <tr>
      <td></td>
      <td><input type="submit" value="Login" ></td>
      </tr>
      </table>
      </form>
      </center>
      </font>
    </body>
    </html>
<?php
  exit;
  }
  else {
    function login_error($host,$php_self) {
      echo "<html><head>
      <title>$host :  Administration</title>
      </head><body>
      <center>
      <font face=verdana size=2>
      <center>";

      echo "Login failed: You are not authorized to access this part of the site.
      <b><a href=$php_self><br><br>Click here</a></b> to try again.
      </center>
      </font>
      </body>
      </html>";
      session_unregister("adb_password");
      session_unregister("user");
      exit;
      }

    $user_checked_passed = false;

    if(isset($_SESSION['adb_password'])) {
      $adb_session_password = $_SESSION['adb_password'];
      if($admin_password != $adb_session_password) 
        login_error($_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
      else {
        $user_checked_passed = true;
      }
    }

    if($user_checked_passed == false) {
      if(strlen($u_name)< 2) 
        login_error($_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
      if($admin_user_name != $u_name) //if username not correct
        login_error($_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
      if(isset($admin_password)) {
        if($admin_password == $u_password) {
          $_SESSION["adb_password"] = $admin_password;
          $_SESSION["user"] = $u_name;
        }
        else { //password incorrect
          login_error($_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
        }
      }
      else {
        login_error($_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
      }

    $page_location = $_SERVER["PHP_SELF"];
    if(isset($_SERVER["QUERY_STRING"]))
      $page_location = $page_location ."?". $_SERVER["QUERY_STRING"];

    header ("Location: ". $page_location);
    }
  }
}
?>
