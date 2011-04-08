<?php
/****************************************************************/
/*                                                              */
/* File Admin                                                   */
/* ----------                                                   */
/*                                                              */
/* File Admin will perform basic functions on files/directories.*/
/* Functions include, List, Open, View, Edit, Create, Upload,   */
/*   Rename and Move.                                           */
/*                                                              */
/*                                                              */
/* Written by Devin Smith, July 1st 2003                        */
/* I wrote this thing before I even knew php                    */
/*   so it'd pretty bad code. but it works.                     */
/* http://www.arzy.net                                          */
/*                                                              */
/****************************************************************/


/****************************************************************/
/* Config Section                                               */
/*                                                              */
/* $adminfile - THIS filename.                                  */
/* $sitetitle - The title at the top of all pages.              */
/* $sqlserver - mySQL server.                                   */
/* $sqluser - mySQL username.                                   */
/* $sqlpass - mySQL password.                                   */
/* $sqldb - mySQL database.                                     */
/* $default_perm - default unix permissions.                    */
/* $defaulttheme - default theme to display.                    */
/* $defaultlang - default language.                             */
/****************************************************************/

$adminfile = $_SERVER['PHP_SELF'];
$sitetitle = "Demo Browser";
$config['db']['server'] 		= 'localhost';
$config['db']['user'] 			= 'arzbeta_libra';
$config['db']['pass'] 			= 'l1br4';
$config['db']['db'] 			= 'arzbeta_libra';
$config['db']['pref'] 			= 'libra_';
$default_perm 					= '0777';  // change this to something like 0644 if you are runing suphp
$defaulttheme 					= 'classic';
$defaultlang 					= 'english';
$maxuploads 					= 5;
$config['enable_trash'] 		= false;  // TODO


$dbh=mysql_connect ($config['db']['server'], $config['db']['user'], $config['db']['pass']) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($config['db']['db']);

$yay = 0; $user = ''; $pass = ''; $sess = '';
$theme=''; $logged=false;

if ( isset($_REQUEST['login']) ) $user = $_REQUEST['login'];
elseif ( isset($_COOKIE['user']) ) $user = $_COOKIE['user'];

if ( isset($_REQUEST['encpas']) ) $pass = $_REQUEST['encpas'];
elseif ( isset($_COOKIE['pass']) ) $pass = $_COOKIE['pass'];

if ( isset($_REQUEST['randsess']) ) $sess = $_REQUEST['randsess'];
elseif ( isset($_COOKIE['sess']) ) $sess= $_COOKIE['sess'];

if ($user && $pass) {

	$mysql = mysql_query("SELECT pass, user, id, folder, http, spacelimit, language, theme, permbrowse, permupload, permcreate, permuser, permadmin, permdelete, permmove, permchmod, permget, permdeleteuser, permedituser, permmakeuser, permpass, permrename, permedit, permsub, formatperm, status, recycle, permprefs FROM ".$GLOBALS['config']['db']['pref']."users WHERE user='".mysql_escape_string($user)."'");
	list ($dbpass, $dbuser, $userid, $userdir, $http, $limit, $language, $theme, $permbrowse, $permupload, $permcreate, $permuser, $permadmin, $permdelete, $permmove, $permchmod, $permget, $permdeleteuser, $permedituser, $permmakeuser, $permpass, $permrename, $permedit, $permsub, $formatperm, $status, $recycle, $permprefs) = mysql_fetch_row($mysql);

        

	if ($userid && $pass == md5($dbpass.$sess)) {
		$yay = 1;
	}
}

if ($yay) {
	$user = $dbuser;
	$activesess = date("YmdHis");
	$mysql = mysql_query("UPDATE ".$GLOBALS['config']['db']['pref']."users SET currsess='$activesess' WHERE id='$userid'") or die (mysql_error());

	if (file_exists("themes/$theme/theme.php")) require_once("themes/$theme/theme.php");
	else {
		require_once("themes/$defaulttheme/theme.php");
		$theme = $defaulttheme;
	}

	if (file_exists("language/$language/lng.php")) require_once("language/$language/lng.php");
	else {
		$language = $defaultlang;
		require_once("language/$defaultlang/lng.php");
	}

	setcookie('user','',time()-60*60*24*1);
	setcookie('pass','',time()-60*60*24*1);
	setcookie('sess','',time()-60*60*24*1);

	setcookie('user',$dbuser,time()+60*60*24*1);
	setcookie('pass',$pass,time()+60*60*24*1);
	setcookie('sess',$sess,time()+60*60*24*1);

        $logged=true;

} else {    
	$er=false;
        if ( isset($_REQUEST['login']) || isset($_REQUEST['encpas']) ) $er = true;
	$theme=$defaulttheme;
        require_once("themes/$theme/theme.php");
        $logged=false;
	login($er);
}


if( isset($_REQUEST['d']) ) $d=$_REQUEST['d']; else $d=null;
if ($d) {
  while (preg_match('/\\\/',$d)) $d = preg_replace('/\\\/','/',$d);
  while (preg_match('/\/\//',$d)) $d = preg_replace('/\/\//','/',$d);
  while (preg_match('/\.\.\//',$d)) $d = preg_replace('/\.\.\//','/',$d);
  if ($d[strlen($d)-1] != '/') $d = $d.'/';
  if ($d == '/') $d = '';
}
if (!$userdir) $userdir = "./";
if ($permsub != 1) $d = "";

function login($fail = false) {
	setcookie('user','',time()-60*60*24*1);
	setcookie('pass','',time()-60*60*24*1);
	setcookie('sess','',time()-60*60*24*1);
	$user = '';
	$pass = '';
  global $configindex, $extraheaders, $REQUEST_URI, $sqlpref, $lastpage, $bgcolor1, $bgcolor2,$bgcolor3, $tbcolor1, $tbcolor2, $fail, $login, $password, $user, $pass;
    $randsess = md5(md5(rand(1,25419876)).md5(date("DMjygia")));
    $extraheaders = "<script language=javascript>\n"
                   ."  <!--\n"
                   .md5return()
                   ."  function encpass() {\n"
                   ."    if (md5_vm_test() && valid_js()) { \n"
                   ."      document.login.encpas.value = hex_md5(hex_md5(document.login.password.value) + document.login.randsess.value);\n"
                   ."      document.login.password.value = \"\";\n"
                   ."      document.login.submit();\n"
                   ."      return true;\n"
                   ."    } else {\n"
                   ."      document.login.onsubmit=function(){return false;}\n"
                   ."      return false;\n"
                   ."    }\n"
                   ."  }\n"
                   ."  -->\n"
                   ."</script>\n";
    page_header("Login",false);
    opentable("100%");
    if ($fail == TRUE)  echo "<font class=error>**ERROR: Incorrect login information.**<br><br>\n"; 
    echo "<form action=\"$REQUEST_URI\" method=\"post\" name=\"login\">\n"
        ."<table><tr>\n"
        ."<td valign=top>Username: </font>"
        ."<td><input type=\"text\" class=\"txtinput\" name=\"login\" size=\"18\" border=\"0\" class=\"text\">\n"
        ."<tr><td valign=top>Password: </font>\n"
        ."<td><input type=\"password\" class=\"txtinput\" name=\"password\" size=\"18\" border=\"0\" class=\"text\">\n"
        ."<input type=hidden name=\"lastpage\" value=\"$lastpage\">\n"
        ."<input type=hidden name=\"encpas\"><br>\n"
        ."<input type=hidden name=\"randsess\" value=\"$randsess\"><br>\n"
        ."<input type=hidden name=\"loging\" value=\"1\">\n"
        ."<tr><td colspan=2><input type=\"submit\" onclick=\"encpass();\" value=\"Log In\" border=\"0\" class=\"button\" name=\"standard\">\n"
        ."</td></tr></form><br>\n"
        ."<tr><td colspan=2><font class=note>**Note: Cookies and Javascript must be enabled to log in.**<br>\n"
        ."**Note: Passwords are case sensitive.**</font></table></form>\n";
	closetable();
    page_footer();
}


function alternatehome() {
  global $defaulttheme;
  require_once("themes/$defaulttheme/theme.php");
  page_header("Logout");
  echo "Please select an action.";
  page_footer();
}


function home() {
  global $nobar, $d, $bgcolor3, $tbcolor1, $tbcolor2, $tbcolor3, $tbcolor4, $userdir, $HTTP_HOST, $theme, $http, $extraheaders, $IMG_CHECK, $IMG_RENAME, $IMG_GET, $IMG_EDIT, $IMG_OPEN, $IMG_RENAME_NULL, $IMG_EDIT_NULL, $IMG_OPEN_NULL, $IMG_GET_NULL, $IMG_MIME_FOLDER, $IMG_MIME_BINARY, $IMG_MIME_AUDIO, $IMG_MIME_VIDEO, $IMG_MIME_IMAGE, $IMG_MIME_TEXT, $IMG_MIME_UNKNOWN, $permget, $permedit, $permrename, $permsub, $formatperm, $permmove, $permdelete, $permchmod;
  global $adminfile;
  $extraheaders = "<script language=javascript>\n"
                 ."function itemsel(item,ff,check,action,overcolor,outcolor,clickcolor) {\n"
                 ."  if (action == 1) {\n"
                 ."    item.bgColor=overcolor;\n"
                 ."  }\n"
                 ."  if (action == 2) {\n"
                 ."    if (document.getElementById(check).checked == false) item.bgColor=outcolor;\n"
                 ."    else item.bgColor=clickcolor;\n"
                 ."  }\n"
                 ."  if (action == 3) {\n"
                 ."    document.getElementById(check).checked = (document.getElementById(check).checked ? false : true);\n"
                 ."    item.bgColor=clickcolor;\n"
                 ."  }\n"
                 ."}\n"
                 ."function selectall() {\n"
                 ."  var holder;\n"
                 ."  for (x=1;x<document.bulk_submit.filetotal.value;x++) {\n"
                 ."    document.getElementById(\"filesel_\" + x).checked = (document.getElementById(\"filesel_\" + x).checked ? false : true);\n"
                 ."    document.getElementById(\"filebg_\" + x).bgColor = (document.getElementById(\"filesel_\" + x).checked ? \"$tbcolor3\" : document.getElementById(\"filecolor_\" + x).value);\n"
                 ."  }\n"
                 ."  for (x=1;x<document.bulk_submit.foldertotal.value;x++) {\n"
                 ."    document.getElementById(\"foldersel_\" + x).checked = (document.getElementById(\"foldersel_\" + x).checked ? false : true);\n"
                 ."    document.getElementById(\"folderbg_\" + x).bgColor = (document.getElementById(\"foldersel_\" + x).checked ? \"$tbcolor3\" : document.getElementById(\"foldercolor_\" + x).value);\n"
                 ."  }\n"

                 ."}\n"
                 ."</script>\n";
  page_header("Browse");
  $ud = "/".$d;
  echo "<tr><td>\n";
  opentitle("Browsing: $ud");
  echo "</tr></td><form name=bulk_submit action=\"?p=bulk_submit\" method=post>\n"
      ."<tr><td bgcolor=$bgcolor3><table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=100%>\n"
      ."<input type=hidden name=d value=\"$d\">\n";

  $count = "0";
  $a=1; $b=1; $content1 = ""; $content2 = "";
  $p=0; $tcoloring=''; $totalsize=0;

  $handle=opendir($http.$userdir.$d);
  while ($fileinfo = readdir($handle)) $filelist[] = $fileinfo;
  natcasesort($filelist);
  while (list ($key, $fileinfo) = each ($filelist)) {
    if (strlen($fileinfo)>40) $fileinfoa = substr($fileinfo,0,40)."...";
    else $fileinfoa = $fileinfo;
    if ($fileinfo[0] != "." && $fileinfo[0] != ".." ) {
      if (is_dir($http.$userdir.$d.$fileinfo) && is_readable($http.$userdir.$d.$fileinfo)) {
        if ($formatperm == 1) $perms = formatperms(@fileperms($http.$userdir.$d.$fileinfo));
        else $perms = substr(sprintf('%o', @fileperms($http.$userdir.$d.$fileinfo)), -4);

        if ($permrename == 1) $lnk_rename = "<a href=\"".$adminfile."?p=ren&file=".$fileinfo."&d=$d\"><img src=\"$IMG_RENAME\" border=0 onclick=\"itemsel(this,1,'foldersel_$a',3,'#CCCCFF','$tcoloring','#FFCC99');\"></a>\n";
        else $lnk_rename = "<img src=\"$IMG_RENAME_NULL\" border=0>\n";

        if ($permsub == 1) $lnk_open = "<a href=\"".$adminfile."?p=home&d=".$d.$fileinfo."/\">".$fileinfoa."</a>\n";
        else $lnk_open = $fileinfoa;

        $content1[$a] ="<td><input type=checkbox name=\"foldersel[$a]\" id=\"foldersel_$a\" value=\"".$fileinfo."\" onclick=\"itemsel(this,1,'foldersel_$a',3,'#CCCCFF','$tcoloring','#FFCC99');\"> \n"
                 ."<td valign=bottom><img src=\"$IMG_MIME_FOLDER\"> ".$lnk_open."</td>\n"
                 ."<td align=\"center\">\n"
                 ."<td align=\"center\">".$lnk_rename."\n"
                 ."<td> <td align=\"center\"> <td align=\"center\">$perms\n";
        $a++;
      } elseif (!is_dir($http.$userdir.$d.$fileinfo) && is_readable($http.$userdir.$d.$fileinfo)) {
        if ($formatperm == 1) $perms = formatperms(@fileperms($http.$userdir.$d.$fileinfo));
        else $perms = substr(sprintf('%o', @fileperms($http.$userdir.$d.$fileinfo)), -4);
        $size = filesize($http.$userdir.$d.$fileinfo);
        $totalsize = $totalsize + $size;
        $type = mime_content_type($http.$userdir.$d.$fileinfo);        
        if (substr($type,0,4) == "text") $mimeimage = "<img src=\"$IMG_MIME_TEXT\">";
        elseif (substr($type,0,5) == "image") $mimeimage = "<img src=\"$IMG_MIME_IMAGE\">";
        elseif (substr($type,0,11) == "application") $mimeimage = "<img src=\"$IMG_MIME_BINARY\">";
        elseif (substr($type,0,5) == "audio") $mimeimage = "<img src=\"$IMG_MIME_AUDIO\">";
        elseif (substr($type,0,5) == "video") $mimeimage = "<img src=\"$IMG_MIME_VIDEO\">";
        elseif (substr($type,0,5) == "model") $mimeimage = "<img src=\"$IMG_MIME_IMAGE\">";
        elseif (substr($type,0,7) == "message") $mimeimage = "<img src=\"$IMG_MIME_TEXT\">";
        elseif (substr($type,0,9) == "multipart") $mimeimage = "<img src=\"$IMG_MIME_TEXT\">";
        else $mimeimage = "<img src=\"$IMG_MIME_UNKNOWN\">";

        if ((substr($type,0,4) == "text" || $size == 0) && $permedit == 1) $edit = "<a href=\"".$adminfile."?p=edit&fename=".$fileinfo."&d=$d&next_action=0\"><img src=\"$IMG_EDIT\" border=0 onclick=\"itemsel(this,1,'filesel_$b',3,'#CCCCFF','$tcoloring','#FFCC99');\"></a>\n";
        elseif (substr($type,0,4) == "text" || $size == 0) $edit = "<a href=\"".$adminfile."?p=edit&fename=".$fileinfo."&d=$d&next_action=0\"><img src=\"$IMG_EDIT_NULL\" border=0>\n";
        else $edit = "";        
        if ($permrename == 1) $rename = "<a href=\"".$adminfile."?p=ren&file=".$fileinfo."&d=$d\"><img src=\"$IMG_RENAME\" border=0 onclick=\"itemsel(this,1,'filesel_$b',3,'#CCCCFF','$tcoloring','#FFCC99');\"></a>\n";
        else $rename = "<img src=\"$IMG_RENAME_NULL\" border=0>\n";
        if ($permget == 1) $get = "<a href=\"".$adminfile."?p=view&file=".$fileinfo."&d=$d\"><img src=\"$IMG_GET\" border=0 onclick=\"itemsel(this,1,'filesel_$b',3,'#CCCCFF','$tcoloring','#FFCC99');\"></a>\n";
        else $get = "<img src=\"$IMG_GET_NULL\" border=0>\n";
        if ($permget == 1) $filefile = "<a href=\"".$http.$userdir.$d.$fileinfo."\" onclick=\"itemsel(this,1,'filesel_$b',3,'#CCCCFF','$tcoloring','#FFCC99');\">".$fileinfoa."</a>\n";
        else $filefile = "$fileinfoa\n";

        $content2[$b] ="<td><input type=checkbox name=\"filesel[$b]\" id=\"filesel_$b\" value=\"".$fileinfo."\" onclick=\"itemsel(this,1,'filesel_$b',3,'#CCCCFF','$tcoloring','#FFCC99');\">\n"
                 ."<td>$mimeimage $filefile</td>\n"
                 ."<td align=\"center\" width=20> $edit\n"
                 ."<td align=\"center\" width=20> $rename\n"
                 ."<td align=\"center\" width=20> $get\n"
                 ."<td align=\"left\" nowrap>".getfilesize($size)."\n"
                 ."<td align=\"center\">$perms\n";
        $b++;
      } else {
        echo "<font class=error>Directory '$fileinfo' is unreadable.</font><br>\n";
      }
    $count++;
    }
  }
  @closedir($userdir.$d);
  $filetotal = $b;
  $foldertotal = $a;
  $pdir='';
  echo "<tr bgcolor=\"$tbcolor3\" width=20 class=titlebar1 height=25><td width=10 align=left valign=bottom><a href=\"javascript:selectall();\"><img src=\"$IMG_CHECK\" border=0></a> "
      ."<td class=theader width=420>Filename\n"
      ."<td align=\"center\" width=80 class=theader colspan=3>Actions<font size=1>\n"
      ."<td width=70 class=theader align=left>Size<font size=1>\n"
      ."<td align=\"center\" width=60 class=theader>Perms\n";
  if ($d) {
    $p=1;
    $tcoloring   = ($p % 2) ? $tbcolor1 : $tbcolor2;
    if (substr($d,0,strrpos(substr($d,0,-1),"/")) != "") $pdir = substr($d,0,strrpos(substr($d,0,-1),"/"))."/";
    echo "<tr height=20 id=p_sel bgcolor=".$tcoloring." width=100% height=22 onmouseover=\"itemsel(this,1,'p_sel',1,'$tbcolor4','$tcoloring','$tbcolor3');\" onmouseout=\"itemsel(this,1,'p_sel',2,'$tbcolor4','$tcoloring','$tbcolor3');\">\n"
        ."<td><td><img src=\"$IMG_MIME_FOLDER\"> <a href=\"".$adminfile."?p=home&d=".$pdir."\">../</a></td>\n"
        ."<input type=hidden name=\"p_sel\" value=\"$tcoloring\" id=\"p_sel\">\n"
        ."<td align=\"center\" width=20></a>\n"
        ."<td align=\"center\" width=20>\n"
        ."<td align=\"center\" width=20>\n"
        ."<td align=\"left\" nowrap>\n"
        ."<td align=\"center\">\n";
  }

  if ($content1) {
    for ($a=1; $a<count($content1)+1;$a++) {
      $tcoloring = (($a+$p) % 2) ? $tbcolor1 : $tbcolor2;
      echo "<tr height=20 id=\"folderbg_$a\" bgcolor=".$tcoloring." width=100% height=22 onmouseover=\"itemsel(this,1,'foldersel_$a',1,'$tbcolor4','$tcoloring','$tbcolor3');\" onmouseout=\"itemsel(this,1,'foldersel_$a',2,'$tbcolor4','$tcoloring','$tbcolor3');\" onclick=\"itemsel(this,1,'foldersel_$a',3,'$tbcolor4','$tcoloring','$tbcolor3');\">"
          ."<input type=hidden name=\"foldercolor_$a\" value=\"$tcoloring\" id=\"foldercolor_$a\">\n"
          .$content1[$a]
          ."</td></tr>\n";
    }
  }
  if ($content2) {
    for ($b=1; $b<count($content2)+1;$b++) {
      $tcoloring   = (($a++ + $p) % 2) ? $tbcolor1 : $tbcolor2;
      echo "<tr id=\"filebg_$b\" bgcolor=".$tcoloring." width=100% height=22 onmouseover=\"itemsel(this,1,'filesel_$b',1,'$tbcolor4','$tcoloring','$tbcolor3');\" onmouseout=\"itemsel(this,1,'filesel_$b',2,'$tbcolor4','$tcoloring','$tbcolor3');\" onclick=\"itemsel(this,1,'filesel_$b',3,'$tbcolor4','$tcoloring','$tbcolor3');\">"
          ."<input type=hidden name=\"filecolor_$b\" value=\"$tcoloring\" id=\"filecolor_$b\">\n"
          .$content2[$b]
          ."</td></tr>\n";
    }
  }
  if ($formatperm == 1) $perm = formatperms(@fileperms($userdir.$d));
  else $perm = substr(sprintf('%o', @fileperms($userdir.$d)), -4);
  echo "<tr bgcolor=\"$tbcolor3\" width=100% class=titlebar2 height=20><td class=titlebar2 colspan=5>\n"
      ."<img src=images/pixel.gif width=25 height=1>Files: $count\n"
      ."<input type=hidden name=filetotal value=\"$filetotal\">\n"
      ."<input type=hidden name=foldertotal value=\"$foldertotal\">\n"
      ."<td align=\"left\" class=titlebar2 nowrap>".getfilesize($totalsize)."\n"
      ."<td align=\"center\" class=titlebar2>".$perm."\n";
  if ($permmove == 1 || $permdelete == 1 || $permchmod == 1) {
    echo "<tr class=titlebar2><td colspan=2 class=titlebar2><img src=images/pixel.gif width=25 height=1>Bulk Actions: <select name=bulk_action class=\"action\"\">\n"
        ."<option value=\"\" class=action>[ Select Action ]</option>\n";
    if ($permdelete) echo "<option value=delete class=\"actiondelete\">Delete</option>\n";
    if ($permmove) echo "<option value=move class=\"actionmove\">Move</option>\n";
    if ($permchmod) echo "<option value=chmod class=\"actionchmod\">Change Permissions</option>\n";
    echo "</select>\n"
        ."<input type=submit value=\"  Go  \" class=button>\n";
  }
  echo "</td><td colspan=5 align=center>".showdiskspace()."</td></tr></form></table>";
  $nobar = 1;
  page_footer();
}


function listdir($dir, $level_count = 0) {
  global $content;
  if (!@($thisdir = opendir($dir))) { return; }
  while ($item = readdir($thisdir) ) {
    if (is_dir($dir."/".$item) && (substr($item, 0, 1) != '.')) {
      listdir($dir."/".$item, $level_count + 1);
    }
  }
  if ($level_count > 0) {
    $dir = ereg_replace("[/][/]", "/", $dir);
    $content[] = $dir;
  }
  return $content;
}


function logout() {
  global $logged;
  setcookie("user","",time()-60*60*24*1);
  setcookie("pass","",time()-60*60*24*1);
  $login=null;
  page_header("Logout",false);
  echo "Your are now logged out."
      ."<br><br>"
      ."<a href=?p=login>Click here to Log in again</a>";
  $logged=false;
  page_footer();
}


function getfilesize($size) {
  if ($size != 0) { 
    if ($size>=1099511627776) $size = round($size / 1024 / 1024 / 1024 / 1024, 2)." TB";
    elseif ($size>=1073741824) $size = round($size / 1024 / 1024 / 1024, 2)." GB";
    elseif ($size>=1048576) $size = round($size / 1024 / 1024, 2)." MB";
    elseif ($size>=1024) $size = round($size / 1024, 2)." KB";
    elseif ($size<1024) $size = round($size / 1024, 2)." B";
  }
  return $size;
}


if (!function_exists('mime_content_type')) {
   function mime_content_type($filename) {
        $i=strrpos($filename,"."); $m=strtolower(substr($filename,$i+1));
        switch($m){
            case "js": return "application/javascript";
            case "json": return "application/json";
            case "jpg": case "jpeg": case "jpe": return "image/jpg";
            case "png": case "gif": case "bmp": return "image/".$m;
            case "css": return "text/css";
            case "xml": return "application/xml";
            case "html": case "htm": case "php": return "text/html";
            default: return "";
        }
   }
}


function listdircontents($dir, $level_count = 0) {
  global $contenta, $contentb, $userdir;
    $dir = $userdir.$dir;
    if (!@($thisdir = opendir($userdir.$dir))) { return; }
    while ($item = readdir($thisdir) ) {
      if (is_dir($userdir.$dir."/".$item) && (substr($item, 0, 1) != '.')) {
        listdircontents($userdir.$dir."/".$item, $level_count + 1);
      }
    }
    if ($level_count > 0) {

      $dir = ereg_replace("[/][/]", "/", $dir);
      $handle=opendir($dir);
      while ($file = readdir($handle)) $filelist[] = $file;
      while (list ($key, $file) = each ($filelist)) { 
        if ($file != "." && $file != ".." && !is_dir($dir."/".$file)) {
          $contenta[] = $dir."/".$file;
        }
      }
      $contentb[] = $dir;
    }
}


function error_handler ($level, $message, $file, $line, $context) { 
  global $parseerror; 
  $parseerror = 1;
  if ($level == 2) echo "<font class=error>$message</font><br>\n";
  //if ($level == 2) echo "<b>Warning</b>: $message in $file on line $line\n";
} 


function permerror($error) { 
  page_header("Permission Error");
  opentitle("Permission Error");
  opentable("100%");
  echo "<br><font class=error>$error</font><br><br>\n";
  closetable();
  page_footer();
} 


function ismail($str) {
  if(eregi("^[\'+\\./0-9A-Z^_\`a-z{|}~\-]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+){1,5}$",$str) || !$str) return true;
  else return false;
}







function up() {
  global $d, $userdir, $maxuploads;
  page_header("Upload");
  opentitle("Upload");
  opentable("100%");
  echo "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"?p=upload\" METHOD=\"POST\">\n";
  for ($x=1;$x<=$maxuploads;$x++) {
    echo "File $x: <input type=\"File\" name=\"upfile[$x]\" size=\"20\" class=\"text\"><br>\n";
  }
  echo "<br><br>Destination:<br><select name=\"ndir\" size=1>\n";
  if (!$d) echo "<option value=\"/\">/</option>";
  else echo "<option value=\"".$d."\">".$d."</option>";
  $content = listdir($userdir.$d);
  asort($content);
  foreach ($content as $item) echo "<option value=\"".substr($item,strlen($userdir))."/\">".substr($item,strlen($userdir))."/</option>\n";
  echo "</select><br><br>"
      ."<input type=\"hidden\" name=d value=\"$d\">\n"
      ."<input type=\"submit\" value=\"Upload\" class=\"button\">\n"
      ."</form>\n";
  closetable();
  page_footer();
}


function upload($upfile, $ndir, $d) {
  global $userdir, $maxuploads, $default_perm;
  $x=0;
  page_header("Upload");
  opentitle("Upload");
  opentable("100%");
  for ($x=1;$x<=$maxuploads;$x++) {
    if($upfile['name'][$x]) {
      if (checkdiskspace(filesize($upfile['tmp_name'][$x]))) {
        if(copy($upfile['tmp_name'][$x],$userdir.$ndir.$upfile['name'][$x])) echo "<font class=ok>The file '/".$ndir.$upfile['name'][$x]."' uploaded successfully.</font><br>\n";
        else echo "<font class=error>File failed to upload '/".$ndir.$upfile['name'][$x]."'.</font><br>\n";
        @chmod($userdir.$ndir.$upfile['name'][$x], intval($default_perm,8));
      } else {
        echo "<font class=error>You do not have suficient disk space to upload the file(s).</font><br>\n";
        $space = 1;
        break;
      }
      $uploaded = 1;
    }
  }
  if (!$uploaded && !$space) echo "<font class=error>No files selected to upload.</font>\n";
  echo "<br><a href=\"javascript:history.back();\">Upload Again</a>\n"
      ."<br><a href=\"?d=$d\">Return</a>\n";
  closetable();
  page_footer();
}


function edit($fename) {
  global $userdir, $d, $next_action, $message;
  if ($fename && file_exists($userdir.$d.$fename)) {
    if ($next_action == 2) $sel2 = " checked";
    else $sel1 = " checked";
    page_header("Edit");
    opentitle("Edit");
    opentable("100%");
    if ($next_action == 1) echo "<font class=ok>The file '".$d.$fename."' was succesfully edited.</font><br>\n";
    else echo "Editing: '".$d.$fename."'<br>\n";
    echo "<form action=\"".$adminfile."?p=save\" method=\"post\">\n"
        ."<textarea cols=\"73\" rows=\"40\" name=\"ncontent\" wrap=off>\n";
    $handle = fopen ($userdir.$d.$fename, "r");
    $contents = "";
    while ($x<1) {
      $data = @fread ($handle, filesize ($userdir.$d.$fename));
      if (strlen($data) == 0) break;
      $contents .= $data;
    }
    fclose ($handle);
    echo  ereg_replace ("</textarea>","&lt;/textarea&gt;",$contents)
        ."</textarea>\n"
        ."<br>\n"
        ."<input type=\"hidden\" name=\"d\" value=\"".$d."\">\n"
        ."<input type=\"hidden\" name=\"fename\" value=\"".$fename."\">\n"
        ."<table cellpadding=0 cellpadding=0 width=400>\n"
        ."<tr><td align=left valign=bottom><table cellpadding=1 cellpadding=1>\n"
        ."<tr><td>After saving:\n"
        ."<tr><td><input type=\"radio\" name=\"next_action\" value=\"1\" id=\"act1\"$sel1><label for=\"act1\"> Continue Editing</label>\n"
        ."<input type=\"radio\" name=\"next_action\" value=\"2\" id=\"act2\"$sel2><label for=\"act2\"> Return Home</label>\n"
        ."</table>\n"
        ."<td align=right valign=bottom><input type=\"submit\" value=\"   Save   \" class=\"button\">\n"
        ."</table></form>\n";
  } else {
    page_header("Edit");
    opentitle("Edit");
    opentable("100%");
    echo "<font class=error>Error opening file.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
  }
  closetable();
  page_footer();
}


function save($ncontent, $fename, $d, $next_action) {
  global $userdir, $message;
  if ($fename) {
    $fp = fopen($userdir.$d.$fename, "w");
    if ($ncontent) {
      if(fwrite($fp, stripslashes($ncontent))) {
        $fp = null;
      } else {
        page_header("Edit");
        opentitle("Edit");
        opentable("100%");
        echo "<font class=error>There was a problem editing this file.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
        closetable();
        page_footer();
      }
    } else {
      // No content. asume correct modification.
    }
  } else {
    page_header("Edit");
    opentitle("Edit");
    opentable("100%");
    echo "<font class=error>Error saving file.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
  }
  if ($next_action == 1) header("Location: ?p=edit&fename=$fename&d=$d&next_action=$next_action");
  else header("Location: ?d=$d");
  die();
}


function cr() {
  global $d, $userdir,$content,$adminfile;
  page_header("Create");
  opentitle("Create");
  opentable("100%");
  if (!$content == "") { echo "<br><br>Please enter a filename.\n"; }
  echo "<form action=\"".$adminfile."?p=create\" method=\"post\">\n"
      ."Filename: <br><input type=\"text\" size=\"20\" name=\"nfname\" class=\"text\"><br><br>\n"
      ."Destination:<br><select name=ndir size=1>\n";
  if (!$d) echo "<option value=\"/\">/</option>";
  else echo "<option value=\"".$d."\">".$d."</option>";
  $content = listdir($userdir.$d);
  asort($content);
  foreach ($content as $item) echo "<option value=\"".substr($item,strlen($userdir))."/\">".substr($item,strlen($userdir))."/</option>\n";
  echo "</select><br><br>"
      ."<table cellpadding=0 cellspacing=0 width=100>\n"
      ."<tr><td>Directory <td><input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"1\" checked>\n"
      ."<tr><td>File <td><input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"0\">\n"
      ."</table><br><br>\n"
      ."<input type=\"hidden\" name=\"d\" value=\"$d\">\n"
      ."<input type=\"submit\" value=\"Create\" class=\"button\">\n"
      ."</form>\n";
  closetable();
  page_footer();
}


function create($nfname, $isfolder, $d, $ndir) {
  global $userdir, $default_perm;
  if (!$d) $dis = "/";
  page_header("Create");
  opentitle("Create");
  opentable("100%");
  if (!$nfname == "") {
    if (!file_exists($userdir.$d.$nfname)) {
      if ($isfolder == 1) {
        if(mkdir($userdir.$d.$nfname, $default_perm)) $ok = "Your directory, '".$dis.$d.$ndir.$nfname."', was succesfully created.\n";
        else $error = "The directory, '/".$d.$ndir.$nfname."', could not be created. Check to make sure the permisions on the directory is set to '0777'.\n";
      } else {
        if(fopen($userdir.$d.$nfname, "w")) {
          $ok = "Your file, '".$dis.$d.$ndir.$nfname."', was succesfully created.\n";
          @chmod($userdir.$d.$nfname, intval($default_perm,8));
        } else $error = "The file, '".$dis.$ndir.$nfname."', could not be created. Check to make sure the permisions on the directory is set to '0777'.\n";
      }
      if ($ok) echo "<font class=ok>$ok</font><br><a href=\"?d=$d\">Return</a>\n";
      if ($error) echo "<font class=error>$error</font><br><a href=\"javascript:history.back();\">Back</a>\n";
    } else {
      if (is_dir($userdir.d.$nfname)) echo "<font class=error>A directory by this name already exists. Please choose another.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
      else echo "<font class=error>A file/directory by this name already exists. Please choose another.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
    }
  } else {
    echo "<font class=error>Please enter a filename.</font><br><a href=\"javascript:history.back();\">Back</a>\n";
  }
  closetable();
  page_footer();
}


function ren($file) {
  global $d;
  if (!$file == "") {
    page_header("Rename");
    opentitle("Rename");
    opentable("100%");
    echo "<form action=\"".$adminfile."?p=rename\" method=\"post\">\n"
        ."Renaming '/".$d.$file."'\n"
        ."<br>\n"
        ."<input type=\"hidden\" name=\"rename\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"d\" value=\"".$d."\">\n"
        ."<input class=\"text\" type=\"text\" size=\"40\" width=\"40\" name=\"nrename\" value=\"".$file."\">\n"
        ."<input type=\"Submit\" value=\"Rename\" class=\"button\">\n";
    closetable();
    page_footer();
  } else {
    home();
  }
}


function renam($rename, $nrename, $d) {
  global $userdir;
  if ($rename && $nrename) {
    page_header("Rename");
    opentitle("Rename");
    opentable("100%");
    if(rename($userdir.$d.$rename,$userdir.$d.$nrename)) echo "<font class=ok>The file '".$d.$rename."' has been sucessfully changed to '".$d.$nrename."'.</font><br><a href=\"?d=$d\">Back</a>\n";
    else echo "<font class=error>There was a problem renaming '".$d.$rename."'</font><br><a href=\"javascript:history.back();\">Back</a>\n";
  } else {
    page_header("Rename");
    opentitle("Rename");
    opentable("100%");
    echo "<font class=error>Please enter a new file name.</font>\n"
        ."<br><a href=\"javascript:history.back();\">Back</a>\n";
  }
  closetable();
  page_footer();
}



function bulk_submit($bulk_action,$d) {
  global $_POST, $sqlpref, $d, $tbcolor1, $userdir, $tbcolor2;
  if (!$bulk_action) $error .= "Please select an action.<br>\n";
  if (!$_POST[filesel] && !$_POST[foldersel]) $error .= "Please select at least one file to perform an action on.<br>\n";
  if ($_POST[filesel] && $_POST[foldersel]) $delvar = "files/folders and all of their content";
  elseif ($_POST[filesel] && count($_POST[filesel]) > 1) $delvar = "files";
  elseif ($_POST[foldersel] && count($_POST[foldersel]) > 1) $delvar = "folders and all of their content";
  elseif ($_POST[filesel]) $delvar = "file";
  elseif ($_POST[foldersel]) $delvar = "folder and all of its contents";
  if (!$error && $bulk_action == "delete") {
    page_header("Delete");
    opentitle("Delete");
    opentable("100%");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=100%>\n"
        ."<form name=bulk_action action=\"?p=bulk_action\" method=post>\n"
        ."<tr><td><font class=error>Are you sure you want to delete the following $delvar?</font><br>\n"
        ."<tr><td bgcolor=$tbcolor1>\n";
    $a=0; $b=0;
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        echo "$file <input type=hidden name=filesel[$a] value=$file><br>\n";
        $a++;
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        echo "$file<input type=hidden name=foldersel[$b] value=$file><br>\n";
        $b++;
      }
    }
    echo "<tr><td align=center><br><a href=\"javascript:document.bulk_action.submit();\">Yes</a> | \n"
        ."<a href=\"?p=home\"> No </a>\n"
        ."<input type=hidden name=bulk_action value=\"$bulk_action\">\n"
        ."<input type=hidden name=d value=\"$d\">\n"
        ."</td></tr></form></table>\n";
    closetable();
    page_footer();
  } elseif (!$error && $bulk_action == "move") {
    page_header("Move");
    opentitle("Move");
    opentable("100%");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=100%>\n"
        ."<form name=bulk_action action=\"?p=bulk_action\" method=post>\n"
        ."<tr><td>Move $delvar:\n"
        ."<tr><td bgcolor=$tbcolor1>\n";

    $a=0; $b=0;
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        echo "$file <input type=hidden name=filesel[$a] value=$file><br>\n";
        $a++;
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        echo "$file<input type=hidden name=foldersel[$b] value=$file><br>\n";
        $b++;
      }
    }
    echo "<tr><td><select name=ndir size=1>\n"
        ."<option value=\"".substr($item,strlen($userdir.$d))."/\">".substr($item,strlen($userdir.$d))."/</option>";
    $content = listdir($userdir);
    asort($content);
    foreach ($content as $item) echo "<option value=\"".substr($item,strlen($userdir))."/\">".substr($item,strlen($userdir))."/</option>\n";
    echo "</select> "
        ."<input type=\"Submit\" value=\"Move\" class=\"button\">\n"
        ."<input type=hidden name=bulk_action value=\"$bulk_action\">\n"
        ."<input type=hidden name=d value=\"$d\">\n"
        ."</td></tr></form></table>\n";
    closetable();
    page_footer();
  } elseif (!$error && $bulk_action == "chmod") {
    page_header("Change Permissions");
    opentitle("Change Permissions");
    opentable("100%");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=100%>\n"
        ."<form name=bulk_action action=\"?p=bulk_action\" method=post>\n"
        ."<tr><td>Change Permissions of $delvar:\n"
        ."<tr><td bgcolor=$tbcolor1>\n";

    $a=0; $b=0;
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        echo "$file <input type=hidden name=filesel[$a] value=$file><br>\n";
        $a++;
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        echo "$file<input type=hidden name=foldersel[$b] value=$file><br>\n";
        $b++;
      }
    }
    
    if (is_array($_POST[filesel])) {
      $keys = array_keys($_POST[filesel]);
      $chval = substr(sprintf('%o', @fileperms($userdir.$d.$_POST[filesel][$keys{0}])), -4);
    } else {
      $keys = array_keys($_POST[foldersel]);
      $chval = substr(sprintf('%o', @fileperms($userdir.$d.$_POST[foldersel][$keys{0}])), -4);
    }
    echo "<tr><td><br><table cellpadding=0 cellspacing=0>\n"
/* Work in Progess
        ."<tr><td><table cellpadding=0 cellspacing=0 width=120 bgcolor=$tbcolor1>\n"
        ."<tr><td colspan=2>Owner:<tr><td>Read<td><input type=checkbox name=perms00 onMouseUp=\"chmodmake(400);\">\n"
        ."<tr><td>Write<td><input type=checkbox name=perms01 onMouseUp=\"chmodmake(200);\">\n"
        ."<tr><td>Execute<td><input type=checkbox name=perms02 onMouseUp=\"chmodmake(100);\"></table>\n"
        ."<td width=20><img src=images/pixel.gif width=20 height=1>\n"
        ."<td><table cellpadding=0 cellspacing=0 width=120 bgcolor=$tbcolor1>\n"
        ."<tr><td colspan=2>Group:<tr><td>Read<td><input type=checkbox name=perms10 onMouseUp=\"chmodmake(40);\">\n"
        ."<tr><td>Write<td><input type=checkbox name=perms11 onMouseUp=\"chmodmake(20);\">\n"
        ."<tr><td>Execute<td><input type=checkbox name=perms12 onMouseUp=\"chmodmake(10);\"></table>\n"
        ."<td width=20><img src=images/pixel.gif width=20 height=1>\n"
        ."<td><table cellpadding=0 cellspacing=0 width=120 bgcolor=$tbcolor1>\n"
        ."<tr><td colspan=2>Pubic:<tr><td>Read<td><input type=checkbox name=perms20 onMouseUp=\"chmodmake(4);\">\n"
        ."<tr><td>Write<td><input type=checkbox name=perms21 onMouseUp=\"chmodmake(2);\">\n"
        ."<tr><td>Execute<td><input type=checkbox name=perms22 onMouseUp=\"chmodmake(1);\"></table>\n"
        ."</table>\n"
*/
        ."<tr><td><input type=text width=20 size=20 name=ndir value=\"$chval\">\n"
        ."<br><br><input type=\"Submit\" value=\"Change\" class=\"button\">\n"
        ."<input type=hidden name=bulk_action value=\"$bulk_action\">\n"
        ."<input type=hidden name=d value=\"$d\">\n"
        ."</td></tr></form></table></table>\n";
    closetable();
    page_footer();
  } else {
    page_header("Action");
    opentitle("Action");
    opentable("100%");
    echo "<font class=error>$error</font>\n";
    closetable();
    page_footer();
  }
}


function bulk_action($bulk_action,$d,$ndir) {

  global $_POST, $sqlpref, $tbcolor1, $contenta, $contentb, $userdir;
  set_error_handler ('error_handler');
  if (!$bulk_action) $error .= "Please select an action.<br>\n";
  if (!$_POST[filesel] && !$_POST[foldersel]) $error .= "Please select at least one file to perform an action on.<br>\n";
  if (!$error && $bulk_action == "delete") {
    page_header("Delete");
    opentitle("Delete");
    opentable("100%");
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        if(@unlink($userdir.$d.$file)) echo "<font class=ok>$file has been sucessfully deleted.<br>\n";
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        listdircontents($userdir.$d);
        foreach ($contenta as $delitem) if(@unlink($userdir.$d.$delitem)) echo "<font class=ok>$delitem has been sucessfully deleted.<br>\n";
        foreach ($contentb as $delitem) if(@rmdir($userdir.$d.$delitem)) echo "<font class=ok>$delitem has been sucessfully deleted.<br>\n";
      }
    }
    if (!$parseerror) echo "<a href=\"?d=$d\">Back</a>\n";
    closetable();
    page_footer();
  } elseif (!$error && $bulk_action == "move") {
    page_header("Move");
    opentitle("Move");
    opentable("100%");
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        if(@rename($userdir.$d.$file, $userdir.$ndir.$file)) echo "<font class=ok>$file has been sucessfully moved.<br>\n";
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        if(@rename($userdir.$d.$file, $userdir.$ndir.$file)) echo "<font class=ok>$file has been sucessfully moved.<br>\n";
      }
    }
    if (!$parseerror) echo "<a href=\"?d=$d\">Back</a>\n";
    closetable();
    page_footer();
  } elseif (!$error && $bulk_action == "chmod") {
    page_header("Change Permissions");
    opentitle("Change Permissions");
    opentable("100%");
    if (is_array($_POST[filesel])) {
      foreach ($_POST[filesel] as $file) {
        if(@chmod($userdir.$d.$file, intval($ndir,8))) echo "<font class=ok>".$file."'s permissions have been sucessfully chnaged to $ndir.<br>\n";
      }
    }
    if (is_array($_POST[foldersel])) {
      foreach ($_POST[foldersel] as $file) {
        if(@chmod($userdir.$d.$file, $ndir)) echo "<font class=ok>".$file."'s permissions have been sucessfully chnaged.<br>\n";
      }
    }
    if (!$parseerror) echo "<a href=\"?d=$d\">Back</a>\n";
    closetable();
    page_footer();
  } else {
    page_header("Action");
    opentitle("Action");
    opentable("100%");
    echo "<font class=error>$error</font>\n";
    closetable();
    page_footer();
  }
}


function viewfile($file,$d) {
  global $userdir;
  $filep = $userdir.$d.$file;
  $file = basename($d.$file);
  $len = filesize($filep);
  //$type = mime_content_type($d.$file);
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Content-type: application/force-download");
  header("Content-Length: $len");
  header("Content-Disposition: inline; filename=$file");
  header("Accept-Ranges: $len"); 
  readfile($filep);
}


function getspaceusage($uid) {
  global $sqlpref, $totalbytes;
  $mysql = mysql_query("SELECT folder FROM ".$GLOBALS['config']['db']['pref']."users WHERE id='$uid'");
  list ($folder) = mysql_fetch_row($mysql);
  $totalbytes = "";
  dirusage($folder);
  return $totalbytes;
}


function dirusage($dir, $level_count = 0) {
  global $totalbytes;
  if (!@($thisdir = opendir($dir))) return;
  while ($item = readdir($thisdir)) if (is_dir("$dir/$item") && (substr($item, 0, 1) != '.'|'..')) dirusage("$dir/$item", $level_count + 1);
  if ($level_count >= 0) {
    $handle = opendir($dir);
    while ($file = readdir($handle)) if ($file != "." && $file != ".." && !is_dir($dir."/".$file)) $totalbytes = $totalbytes + filesize($dir."/".$file);
  }
}


function formatperms($perms) {
  if (($perms & 0xC000) == 0xC000) $info = 's';
  elseif (($perms & 0xA000) == 0xA000) $info = 'l';
  elseif (($perms & 0x8000) == 0x8000) $info = '-';
  elseif (($perms & 0x6000) == 0x6000) $info = 'b';
  elseif (($perms & 0x4000) == 0x4000) $info = 'd';
  elseif (($perms & 0x2000) == 0x2000) $info = 'c';
  elseif (($perms & 0x1000) == 0x1000) $info = 'p';
  else $info = 'u';
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
  return $info;
}


function showdiskspace() {
  global $userid, $limit;
  $width = 208;
  $used = getspaceusage($userid);
  $percent = round(($used  / $limit) * 100);
  if ($percent < 60) $barstyle = "barnormal";
  elseif ($percent < 80) $barstyle = "barwarning";
  else $barstyle = "barerror";
  $barwidth = round(($used  / $limit) * $width);
  $retval = "<table cellpadding=0 cellspacing=0>\n"
           ."<tr><td class=space style=\"COLOR: #FFFFFF\" align=center>Disk Usage: ".getfilesize($used)." / ".getfilesize($limit)." ($percent%)\n"
           ."<tr><td><table cellpadding=1 cellspacing=0 width=$width bgcolor=#000000 height=4>"
           ."<tr><td><table cellpadding=0 cellspacing=0 bgcolor=#555555 width=100% height=4>"
           ."<tr><td><table cellpadding=0 cellspacing=0 class=\"$barstyle\" width=\"".$barwidth."\" height=4 >"
           ."<tr><td class=space><img src=images/pixel.gif height=4 width=1></td></tr>"
           ."</table></td></tr></table></td></tr></table></td></tr></table>\n";
  return $retval;
}


function checkdiskspace($change) {
  global $userid, $limit;
  if ($limit > getspaceusage($userid)+$change) return TRUE;
  else return FALSE;
}












function super() {
  page_header("Admin Pannel");
  opentitle("Admin Pannel");
  opentable("100%");
  echo "<a href=?p=users>Users</a><br>\n";
  closetable();
  page_footer();
}


function usermod() {
  global $tbcolor1, $tbcolor2, $tbcolor3, $issuper, $sqlpref, $bgcolor3, $IMG_DELETE, $IMG_EDIT;
  page_header("Admin Pannel");
  opentitle("Admin Pannel");
  $result = mysql_query("SELECT id, user, folder, name, permadmin FROM ".$GLOBALS['config']['db']['pref']."users");
  while(list($uid, $uname, $d, $info, $permadmin) = mysql_fetch_row($result)) {
    if ($permadmin) $super = "Yes"; else $super = "No";
    $tcoloring = ($a % 2) ? $tbcolor1 : $tbcolor2;
    $content .= "<tr bgcolor=$tcoloring><td>$uname</a>"
               ."<td align=center>$d<td align=center>$super"
               ."<td align=center><a href=\"".$adminfile."?p=deluser&muid=$uid\"><img src=\"$IMG_DELETE\" border=0 alt=\"Delete\" label=\"Delete\" title\"Delete\"></a>\n"
               ."<td align=center><a href=\"".$adminfile."?p=eduser&muid=$uid\"><img src=\"$IMG_EDIT\" border=0 alt=\"Edit\" label=\"Edit\" title=\"Edit\"></a>\n";
    $a++;
  }
  echo "<tr><td bgcolor=$bgcolor3>\n"
      ."<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=100%>\n"
      ."<tr bgcolor=\"$tbcolor3\" width=20 class=titlebar1 height=25>\n"
      ."<td class=theader>Username<td class=theader>Home<td class=theader width=60 nowrap>Admin<td class=theader colspan=2 width=60 nowrap align=center>Actions\n"
      .$content
      ."</table>\n"
      ."<br><br><a href=\"".$adminfile."?p=newuser\">Create User</a><br>\n";
  page_footer();
}


function eduser($muid) {
  global $sqlpref, $extraheaders;
  $extraheaders = "<script language=\"javascript\">\n"
                 ."function checkall() {\n"
                 ."  document.user_edit.config_permbrowse.checked = true;\n"
                 ."  document.user_edit.config_permupload.checked = true;\n"
                 ."  document.user_edit.config_permcreate.checked = true;\n"
                 ."  document.user_edit.config_permuser.checked = true;\n"
                 ."  document.user_edit.config_permpass.checked = true;\n"
                 ."  document.user_edit.config_permdelete.checked = true;\n"
                 ."  document.user_edit.config_permmove.checked = true;\n"
                 ."  document.user_edit.config_permchmod.checked = true;\n"
                 ."  document.user_edit.config_permget.checked = true;\n"
                 ."  document.user_edit.config_permadmin.checked = true;\n"
                 ."  document.user_edit.config_permdeleteuser.checked = true;\n"
                 ."  document.user_edit.config_permedituser.checked = true;\n"
                 ."  document.user_edit.config_permmakeuser.checked = true;\n"
                 ."  document.user_edit.config_permedit.checked = true;\n"
                 ."  document.user_edit.config_permrename.checked = true;\n"
                 ."  document.user_edit.config_permsub.checked = true;\n"
                 ."  document.user_edit.config_permprefs.checked = true;\n"
                 ."  try {document.user_edit.config_permrecycle.checked = true;} catch (e) { }\n"
                 ."}\n"
                 ."function uncheckall() {\n"
                 ."  document.user_edit.config_permbrowse.checked = false;\n"
                 ."  document.user_edit.config_permupload.checked = false;\n"
                 ."  document.user_edit.config_permcreate.checked = false;\n"
                 ."  document.user_edit.config_permuser.checked = false;\n"
                 ."  document.user_edit.config_permpass.checked = false;\n"
                 ."  document.user_edit.config_permdelete.checked = false;\n"
                 ."  document.user_edit.config_permmove.checked = false;\n"
                 ."  document.user_edit.config_permchmod.checked = false;\n"
                 ."  document.user_edit.config_permget.checked = false;\n"
                 ."  document.user_edit.config_permadmin.checked = false;\n"
                 ."  document.user_edit.config_permdeleteuser.checked = false;\n"
                 ."  document.user_edit.config_permedituser.checked = false;\n"
                 ."  document.user_edit.config_permmakeuser.checked = false;\n"
                 ."  document.user_edit.config_permedit.checked = false;\n"
                 ."  document.user_edit.config_permrename.checked = false;\n"
                 ."  document.user_edit.config_permsub.checked = false;\n"
                 ."  document.user_edit.config_permprefs.checked = false;\n"
                 ."  try {document.user_edit.config_permrecycle.checked = false;} catch (e) { }\n"
                 ."}\n"
                 ."</script>\n";
  $result = mysql_query("SELECT id, user, email, name, folder, http, spacelimit, theme, language, permbrowse, permupload, permcreate, permuser, permadmin, permdelete, permmove, permchmod, permget, permdeleteuser, permedituser, permmakeuser, permpass, permedit, permrename, permsub, formatperm, status, recycle, permrecycle, permprefs FROM ".$GLOBALS['config']['db']['pref']."users WHERE id=$muid");
  list($uid, $uname, $email, $name, $folder, $http, $limit, $theme, $language, $permbrowse, $permupload, $permcreate, $permuser, $permadmin, $permdelete, $permmove, $permchmod, $permget, $permdeleteuser, $permedituser, $permmakeuser, $permpass, $permedit, $permrename, $permsub, $formatperm, $status, $recycle, $permrecycle, $permprefs) = mysql_fetch_row($result);
  page_header("Edit User $uname");
  opentitle("Edit User $uname");
  opentable("100%");
  echo "<table>\n"
      ."<form name=\"user_edit\" action=\"".$adminfile."?p=edituser\" method=\"post\">\n";
  if ($permbrowse == 1) $sel1 = " checked"; else $sel1 = "";
  if ($permupload == 1) $sel2 = " checked"; else $sel2 = "";
  if ($permcreate == 1) $sel3 = " checked"; else $sel3 = "";
  if ($permuser == 1) $sel4 = " checked"; else $sel4 = "";
  if ($permpass == 1) $sel5 = " checked"; else $sel5 = "";
  if ($permdelete == 1) $sel6 = " checked"; else $sel6 = "";
  if ($permmove == 1) $sel7 = " checked"; else $sel7 = "";
  if ($permchmod == 1) $sel8 = " checked"; else $sel8 = "";
  if ($permget == 1) $sel9 = " checked"; else $sel9 = "";
  if ($permadmin == 1) $sel10 = " checked"; else $sel10 = "";
  if ($permdeleteuser == 1) $sel11 = " checked"; else $sel11 = "";
  if ($permedituser == 1) $sel12 = " checked"; else $sel12 = "";
  if ($permmakeuser == 1) $sel13 = " checked"; else $sel13 = "";
  if ($permedit == 1) $sel14 = " checked"; else $sel14 = "";
  if ($permrename == 1) $sel15 = " checked"; else $sel15 = "";
  if ($permsub == 1) $sel16 = " checked"; else $sel16 = "";
  if ($permrecycle == 1) $sel17 = " checked"; else $sel17 = "";
  if ($permrecycle == 1) $sel17 = " checked"; else $sel17 = "";
  if ($permprefs == 1) $sel18 = " checked"; else $sel18 = "";


  if ($formatperm == 0) $perm1 = " checked";
  elseif ($formatperm == 1) $perm2 = " checked"; 

  if ($status == 0) $stat1 = " checked";
  elseif ($status == 1) $stat2 = " checked";

  if ($recycle == 0) $rec1 = " checked";
  elseif ($recycle == 1) $rec2 = " checked";

  echo "<tr><td>Username: <td><input type=\"text\" name=\"config_user\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$uname\">\n"
      ."<tr><td>Name: <td><input type=\"text\" name=\"config_name\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$name\">\n"
      ."<tr><td>Password: <td><input type=\"password\" name=\"config_pass\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"\">\n"
      ."<tr><td>Email: <td><input type=\"text\" name=\"config_email\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$email\">\n"
      ."<tr><td>Server Directory: <td><input type=\"text\" name=\"config_folder\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$folder\">\n"
      ."<tr><td>Http Directory: <td><input type=\"text\" name=\"config_http\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$http\"> (*)\n"
      ."<tr><td>Space Limit: <td><table cellpadding=0 cellspacing=0><td nowrap>".getspaceusage($uid)." (".getfilesize(getspaceusage($uid)).") / <td><input type=\"text\" name=\"config_limit\" size=\"15\" width=\"15\" border=\"0\" class=\"txtinput\" value=\"$limit\"> bytes</table>\n"
      ."<tr><td>Language: <td><select name=\"config_language\">\n";
  $handle = opendir("./language");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./language/".$file)) {
      @include("./language/".$file."/lng.def.php");
      if ($language == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$LNG_NAME</option>\n";
    }
  }
  closedir("./language");
  echo "</select><tr><td>Theme: <td><select name=\"config_theme\">\n";
  $handle = opendir("./themes");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./themes/".$file)) {
      @include("./themes/".$file."/theme.def.php");
      if ($theme == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$THEME_NAME</option>\n";
    }
  }
  closedir("./themes");
  echo "</select>\n"
      ."<tr><td>Account Status: <td>\n"
      ."<input type=radio name=\"config_status\" value=\"1\" id=\"stat1\"$stat2><label for=\"stat1\"> Active</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_status\" value=\"0\" id=\"stat2\"$stat1><label for=\"stat2\"> Suspend</label>\n";
  if ($GLOBALS['config']['enable_trash']) {
    echo "<tr><td>Trash Bin: <td>\n"
      ."<input type=radio name=\"config_recycle\" id=\"rec1\"$rec2><label for=\"rec1\"> On</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_recycle\" id=\"rec2\"$rec1><label for=\"rec2\"> Off</label>\n";
  }
  echo "<tr><td>Permission View: <td>\n"
      ."<input type=radio name=\"config_formatperms\" value=\"0\" id=\"perm1\"$perm1><label for=\"perm1\"> UNIX (0644)</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_formatperms\" value=\"1\" id=\"perm2\"$perm2><label for=\"perm2\"> Symbolic (-rw-r--r--)</label>\n"


      ."<tr><td valign=top>Privileges: <td><table cellpadding=1 cellspacing=1>\n"
      ."<td valign=top nowrap><input type=\"checkbox\" name=\"config_permbrowse\" id=\"config_permbrowse\" size=\"40\" border=\"0\" class=\"text\"$sel1><label for=\"config_permbrowse\"> Browse</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permupload\" id=\"config_permupload\" size=\"40\" border=\"0\" class=\"text\"$sel2><label for=\"config_permupload\"> Upload</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permcreate\" id=\"config_permcreate\" size=\"40\" border=\"0\" class=\"text\"$sel3><label for=\"config_permcreate\"> Create</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permpass\" id=\"config_permpass\" size=\"40\" border=\"0\" class=\"text\"$sel5><label for=\"config_permpass\"> Change Password</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permdelete\" id=\"config_permdelete\" size=\"40\" border=\"0\" class=\"text\"$sel6><label for=\"config_permdelete\"> Delete</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permmove\" id=\"config_permmove\" size=\"40\" border=\"0\" class=\"text\"$sel7><label for=\"config_permmove\"> Move</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permedit\" id=\"config_permedit\" size=\"40\" border=\"0\" class=\"text\"$sel14><label for=\"config_permedit\"> Edit</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permrename\" id=\"config_permrename\" size=\"40\" border=\"0\" class=\"text\"$sel15><label for=\"config_permrename\"> Rename</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permget\" id=\"config_permget\" size=\"40\" border=\"0\" class=\"text\"$sel9><label for=\"config_permget\"> Get</label>\n"
      ."<td valign=top nowrap><input type=\"checkbox\" name=\"config_permchmod\" id=\"config_permchmod\" size=\"40\" border=\"0\" class=\"text\"$sel8><label for=\"config_permchmod\"> Chmod</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permsub\" id=\"config_permsub\" size=\"40\" border=\"0\" class=\"text\"$sel16><label for=\"config_persub\"> Access Subdirectories</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permuser\" id=\"config_permuser\" size=\"40\" border=\"0\" class=\"text\"$sel4><label for=\"config_permuser\"> User CP</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permadmin\" id=\"config_permadmin\" size=\"40\" border=\"0\" class=\"text\"$sel10><label for=\"config_permadmin\"> Admin CP</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permdeleteuser\" id=\"config_permdeleteuser\" size=\"40\" border=\"0\" class=\"text\"$sel11><label for=\"config_permdeleteuser\"> Delete Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permedituser\" id=\"config_permedituser\" size=\"40\" border=\"0\" class=\"text\"$sel12><label for=\"config_permedituser\"> Edit Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permmakeuser\" id=\"config_permmakeuser\" size=\"40\" border=\"0\" class=\"text\"$sel13><label for=\"config_permmakeuser\"> Make Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permprefs\" id=\"config_permprefs\" size=\"40\" border=\"0\" class=\"text\"$sel18><label for=\"config_permprefs\"> Edit Prefs</label>\n";

  if ($GLOBALS['config']['enable_trash']) {
    echo "<br><input type=\"checkbox\" name=\"config_permrecycle\" id=\"config_permrecycle\" size=\"40\" border=\"0\" class=\"text\"$sel17><label for=\"config_permrecycle\"> Trash Bin</label>\n";
  }
  echo "</table>\n"
      ."<br><a href=\"javascript:checkall();\">Check All</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:uncheckall();\">Uncheck All</a>\n"
      ."<input type=hidden name=muid value=\"$muid\">\n"
      ."<tr><td colspan=\"2\"><br><input type=\"submit\" name=\"submitButtonName\" value=\"Save\" border=\"0\" class=\"button\">\n"
      ."</td></tr></form></table>\n";
  closetable();
  page_footer();
}


function edituser($muid) {
	if ($_REQUEST['config_status']) $_REQUEST['config_status'] = 1; else $_REQUEST['config_status'] = 0;
	if ($_REQUEST['config_recycle']) $_REQUEST['config_recycle'] = 1; else $_REQUEST['config_recycle'] = 0;
	if ($_REQUEST['config_permbrowse']) $_REQUEST['config_permbrowse'] = 1; else $_REQUEST['config_permbrowse'] = 0;
	if ($_REQUEST['config_permupload']) $_REQUEST['config_permupload'] = 1; else $_REQUEST['config_permupload'] = 0;
	if ($_REQUEST['config_permcreate']) $_REQUEST['config_permcreate'] = 1; else $_REQUEST['config_permcreate'] = 0;
	if ($_REQUEST['config_permpass']) $_REQUEST['config_permpass'] = 1; else $_REQUEST['config_permpass'] = 0;
	if ($_REQUEST['config_permdelete']) $_REQUEST['config_permdelete'] = 1; else $_REQUEST['config_permdelete'] = 0;
	if ($_REQUEST['config_permmove']) $_REQUEST['config_permmove'] = 1; else $_REQUEST['config_permmove'] = 0;
	if ($_REQUEST['config_permedit']) $_REQUEST['config_permedit'] = 1; else $_REQUEST['config_permedit'] = 0;
	if ($_REQUEST['config_permrename']) $_REQUEST['config_permrename'] = 1; else $_REQUEST['config_permrename'] = 0;
	if ($_REQUEST['config_permget']) $_REQUEST['config_permget'] = 1; else $_REQUEST['config_permget'] = 0;
	if ($_REQUEST['config_permchmod']) $_REQUEST['config_permchmod'] = 1; else $_REQUEST['config_permchmod'] = 0;
	if ($_REQUEST['config_permsub']) $_REQUEST['config_permsub'] = 1; else $_REQUEST['config_permsub'] = 0;
	if ($_REQUEST['config_permuser']) $_REQUEST['config_permuser'] = 1; else $_REQUEST['config_permuser'] = 0;
	if ($_REQUEST['config_permadmin']) $_REQUEST['config_permadmin'] = 1; else $_REQUEST['config_permadmin'] = 0;
	if ($_REQUEST['config_permdeleteuser']) $_REQUEST['config_permdeleteuser'] = 1; else $_REQUEST['config_permdeleteuser'] = 0;
	if ($_REQUEST['config_permedituser']) $_REQUEST['config_permedituser'] = 1; else $_REQUEST['config_permedituser'] = 0;
	if ($_REQUEST['config_permmakeuser']) $_REQUEST['config_permmakeuser'] = 1; else $_REQUEST['config_permmakeuser'] = 0;
	if ($_REQUEST['config_permuser']) $_REQUEST['config_permuser'] = 1; else $_REQUEST['config_permuser'] = 0;
	if ($_REQUEST['config_permrecycle']) $_REQUEST['config_permrecycle'] = 1; else $_REQUEST['config_permrecycle'] = 0;
	if ($_REQUEST['config_permprefs']) $_REQUEST['config_permprefs'] = 1; else $_REQUEST['config_permprefs'] = 0;

	page_header("Edit User $muname");
	opentitle("Edit User $muname");
	opentable("100%");

	$query = '
		UPDATE '.$GLOBALS['config']['db']['pref'].'users 
		SET user="'.$_REQUEST['config_user'].'", ';
	if ($_REQUEST['config_pass']) $query .= 'pass="'.md5($_REQUEST['config_pass']).'",';
	$query .= '
		email="'.$_REQUEST['config_email'].'",
		name="'.$_REQUEST['config_name'].'",
		folder="'.$_REQUEST['config_folder'].'",
		http="'.$_REQUEST['config_http'].'",
		spacelimit="'.$_REQUEST['config_limit'].'",
		theme="'.$_REQUEST['config_theme'].'",
		language="'.$_REQUEST['config_language'].'",
		permbrowse="'.$_REQUEST['config_permbrowse'].'",
		permupload="'.$_REQUEST['config_permupload'].'",
		permcreate="'.$_REQUEST['config_permcreate'].'",
		permuser="'.$_REQUEST['config_permuser'].'",
		permadmin="'.$_REQUEST['config_permadmin'].'",
		permdelete="'.$_REQUEST['config_permdelete'].'",
		permmove="'.$_REQUEST['config_permmove'].'",
		permchmod="'.$_REQUEST['config_permchmod'].'",
		permget="'.$_REQUEST['config_permget'].'",
		permdeleteuser="'.$_REQUEST['config_permdeleteuser'].'",
		permedituser="'.$_REQUEST['config_permedituser'].'",
		permmakeuser="'.$_REQUEST['config_permmakeuser'].'",
		permpass="'.$_REQUEST['config_permmakeuser'].'",
		permedit="'.$_REQUEST['config_permedit'].'",
		permrename="'.$_REQUEST['config_permrename'].'",
		permsub="'.$_REQUEST['config_permsub'].'",
		permrecycle="'.$_REQUEST['config_permrecycle'].'",
		permprefs="'.$_REQUEST['config_permprefs'].'",
		formatperm="'.$_REQUEST['config_formatperms'].'",
		status="'.$_REQUEST['config_status'].'",
		recycle="0"
		WHERE id='.$muid;
	$msql = mysql_query($query) or die(mysql_error());
	echo "User info succesfully updated<br>\n";
	closetable();
	page_footer();
}


function deluser($muid) {
  global $issuper, $userid;

  $mresult = mysql_query("SELECT user FROM ".$GLOBALS['config']['db']['pref']."users WHERE id=$muid");
  list ($muname) = mysql_fetch_row($mresult);
  page_header("Delete user $muname");
  opentitle("Edit User $muname");
  opentable("100%");
  if ($muid == $userid) {
    echo "<font class=error>**ERROR: You can not delete yourself!**</font><br>\n";
  } elseif ($msuper == "Code57") {
    echo "<font class=error>**ERROR: You can not delete an admin!**</font><br>\n";
  } else {
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**WARNING: This will permanatly delete $muname. This action is irreversable.**</font><br><br>\n"
        ."Are you sure you want to delete ".$muname."?<br><br>\n"
        ."<a href=\"".$adminfile."?p=deleteuser&muid=$muid\">Yes</a> | \n"
        ."<a href=\"".$adminfile."?p=home\"> No </a>\n"
        ."</table>\n";
  }
  closetable();
  page_footer();
}


function deleteuser($muid) {
  global $issuper, $userid;
  $mresult = mysql_query("SELECT user FROM ".$GLOBALS['config']['db']['pref']."users WHERE id=$muid");
  list ($uname) = mysql_fetch_row($mresult);
  page_header("Delete user $uname");
  opentitle("Delete User $uname");
  opentable("100%");
  if ($muid == $userid) {
    echo "<font class=error>**ERROR: You can not delete yourself!**</font><br>\n";
  } elseif ($msuper == "Code57") {
    echo "<font class=error>**ERROR: You can not delete an admin!**</font><br>\n";
  } else {
    $mysql = mysql_query("DELETE FROM ".$GLOBALS['config']['db']['pref']."users WHERE id=$muid");
    echo "User $uname successfully deleted<br>\n";
  }
  closetable();
  page_footer();
}


function newuser() {
  global $sqlpref, $extraheaders;
  $extraheaders = "<script language=\"javascript\">\n"
                 ."function checkall() {\n"
                 ."  document.user_edit.config_permbrowse.checked = true;\n"
                 ."  document.user_edit.config_permupload.checked = true;\n"
                 ."  document.user_edit.config_permcreate.checked = true;\n"
                 ."  document.user_edit.config_permuser.checked = true;\n"
                 ."  document.user_edit.config_permpass.checked = true;\n"
                 ."  document.user_edit.config_permdelete.checked = true;\n"
                 ."  document.user_edit.config_permmove.checked = true;\n"
                 ."  document.user_edit.config_permchmod.checked = true;\n"
                 ."  document.user_edit.config_permget.checked = true;\n"
                 ."  document.user_edit.config_permadmin.checked = true;\n"
                 ."  document.user_edit.config_permdeleteuser.checked = true;\n"
                 ."  document.user_edit.config_permedituser.checked = true;\n"
                 ."  document.user_edit.config_permmakeuser.checked = true;\n"
                 ."  document.user_edit.config_permedit.checked = true;\n"
                 ."  document.user_edit.config_permrename.checked = true;\n"
                 ."  document.user_edit.config_permsub.checked = true;\n"
                 ."  document.user_edit.config_permprefs.checked = true;\n"
                 ."  try {document.user_edit.config_permrecycle.checked = true;} catch (e) { }\n"
                 ."}\n"
                 ."function uncheckall() {\n"
                 ."  document.user_edit.config_permbrowse.checked = false;\n"
                 ."  document.user_edit.config_permupload.checked = false;\n"
                 ."  document.user_edit.config_permcreate.checked = false;\n"
                 ."  document.user_edit.config_permuser.checked = false;\n"
                 ."  document.user_edit.config_permpass.checked = false;\n"
                 ."  document.user_edit.config_permdelete.checked = false;\n"
                 ."  document.user_edit.config_permmove.checked = false;\n"
                 ."  document.user_edit.config_permchmod.checked = false;\n"
                 ."  document.user_edit.config_permget.checked = false;\n"
                 ."  document.user_edit.config_permadmin.checked = false;\n"
                 ."  document.user_edit.config_permdeleteuser.checked = false;\n"
                 ."  document.user_edit.config_permedituser.checked = false;\n"
                 ."  document.user_edit.config_permmakeuser.checked = false;\n"
                 ."  document.user_edit.config_permedit.checked = false;\n"
                 ."  document.user_edit.config_permrename.checked = false;\n"
                 ."  document.user_edit.config_permsub.checked = false;\n"
                 ."  document.user_edit.config_permprefs.checked = false;\n"
                 ."  try {document.user_edit.config_permrecycle.checked = false;} catch (e) { }\n"
                 ."}\n"
                 ."</script>\n";
  page_header("Create a new user");
  opentitle("Create a new user");
  opentable("100%");
  echo "<table>\n"
      ."<form name=\"user_edit\" action=\"".$adminfile."?p=saveuser\" method=\"post\">\n";

  echo "<tr><td>Username: <td><input type=\"text\" name=\"config_user\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$uname\">\n"
      ."<tr><td>Name: <td><input type=\"text\" name=\"config_name\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$name\">\n"
      ."<tr><td>Password: <td><input type=\"password\" name=\"config_pass\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"\">\n"
      ."<tr><td>Email: <td><input type=\"text\" name=\"config_email\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$email\">\n"
      ."<tr><td>Server Directory: <td><input type=\"text\" name=\"config_folder\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$folder\">\n"
      ."<tr><td>Http Directory: <td><input type=\"text\" name=\"config_http\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$http\"> (*)\n"
      ."<tr><td>Space Limit: <td><table cellpadding=0 cellspacing=0><td nowrap>".getspaceusage($uid)." (".getfilesize(getspaceusage($uid)).") / <td><input type=\"text\" name=\"config_limit\" size=\"15\" width=\"15\" border=\"0\" class=\"txtinput\" value=\"$limit\"> bytes</table>\n"
      ."<tr><td>Language: <td><select name=\"config_language\">\n";
  $handle = opendir("./language");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./language/".$file)) {
      @include("./language/".$file."/lng.def.php");
      if ($language == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$LNG_NAME</option>\n";
    }
  }
  closedir("./language");
  echo "</select><tr><td>Theme: <td><select name=\"config_theme\">\n";
  $handle = opendir("./themes");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./themes/".$file)) {
      @include("./themes/".$file."/theme.def.php");
      if ($theme == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$THEME_NAME</option>\n";
    }
  }
  closedir("./themes");
  echo "</select>\n"
      ."<tr><td>Account Status: <td>\n"
      ."<input type=radio name=\"config_status\" value=\"1\" id=\"stat1\"$stat2><label for=\"stat1\"> Active</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_status\" value=\"0\" id=\"stat2\"$stat1><label for=\"stat2\"> Suspend</label>\n";
  if ($GLOBALS['config']['enable_trash']) {
    echo "<tr><td>Trash Bin: <td>\n"
      ."<input type=radio name=\"config_recycle\" id=\"rec1\"$rec2><label for=\"rec1\"> On</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_recycle\" id=\"rec2\"$rec1><label for=\"rec2\"> Off</label>\n";
  }
  echo "<tr><td>Permission View: <td>\n"
      ."<input type=radio name=\"config_formatperms\" value=\"0\" id=\"perm1\"$perm1><label for=\"perm1\"> UNIX (0644)</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=radio name=\"config_formatperms\" value=\"1\" id=\"perm2\"$perm2><label for=\"perm2\"> Symbolic (-rw-r--r--)</label>\n"


      ."<tr><td valign=top>Privileges: <td><table cellpadding=1 cellspacing=1>\n"
      ."<td valign=top nowrap><input type=\"checkbox\" name=\"config_permbrowse\" id=\"config_permbrowse\" size=\"40\" border=\"0\" class=\"text\"$sel1><label for=\"config_permbrowse\"> Browse</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permupload\" id=\"config_permupload\" size=\"40\" border=\"0\" class=\"text\"$sel2><label for=\"config_permupload\"> Upload</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permcreate\" id=\"config_permcreate\" size=\"40\" border=\"0\" class=\"text\"$sel3><label for=\"config_permcreate\"> Create</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permpass\" id=\"config_permpass\" size=\"40\" border=\"0\" class=\"text\"$sel5><label for=\"config_permpass\"> Change Password</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permdelete\" id=\"config_permdelete\" size=\"40\" border=\"0\" class=\"text\"$sel6><label for=\"config_permdelete\"> Delete</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permmove\" id=\"config_permmove\" size=\"40\" border=\"0\" class=\"text\"$sel7><label for=\"config_permmove\"> Move</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permedit\" id=\"config_permedit\" size=\"40\" border=\"0\" class=\"text\"$sel14><label for=\"config_permedit\"> Edit</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permrename\" id=\"config_permrename\" size=\"40\" border=\"0\" class=\"text\"$sel15><label for=\"config_permrename\"> Rename</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permget\" id=\"config_permget\" size=\"40\" border=\"0\" class=\"text\"$sel9><label for=\"config_permget\"> Get</label>\n"
      ."<td valign=top nowrap><input type=\"checkbox\" name=\"config_permchmod\" id=\"config_permchmod\" size=\"40\" border=\"0\" class=\"text\"$sel8><label for=\"config_permchmod\"> Chmod</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permsub\" id=\"config_permsub\" size=\"40\" border=\"0\" class=\"text\"$sel16><label for=\"config_persub\"> Access Subdirectories</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permuser\" id=\"config_permuser\" size=\"40\" border=\"0\" class=\"text\"$sel4><label for=\"config_permuser\"> User CP</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permadmin\" id=\"config_permadmin\" size=\"40\" border=\"0\" class=\"text\"$sel10><label for=\"config_permadmin\"> Admin CP</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permdeleteuser\" id=\"config_permdeleteuser\" size=\"40\" border=\"0\" class=\"text\"$sel11><label for=\"config_permdeleteuser\"> Delete Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permedituser\" id=\"config_permedituser\" size=\"40\" border=\"0\" class=\"text\"$sel12><label for=\"config_permedituser\"> Edit Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permmakeuser\" id=\"config_permmakeuser\" size=\"40\" border=\"0\" class=\"text\"$sel13><label for=\"config_permmakeuser\"> Make Users</label>\n"
      ."<br><input type=\"checkbox\" name=\"config_permprefs\" id=\"config_permprefs\" size=\"40\" border=\"0\" class=\"text\"$sel18><label for=\"config_permprefs\"> Edit Prefs</label>\n";

  if ($GLOBALS['config']['enable_trash']) {
    echo "<br><input type=\"checkbox\" name=\"config_permrecycle\" id=\"config_permrecycle\" size=\"40\" border=\"0\" class=\"text\"$sel17><label for=\"config_permrecycle\"> Trash Bin</label>\n";
  }
  echo "</table>\n"
      ."<br><a href=\"javascript:checkall();\">Check All</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:uncheckall();\">Uncheck All</a>\n"
      ."<input type=hidden name=muid value=\"$muid\">\n"
      ."<tr><td colspan=\"2\"><br><input type=\"submit\" name=\"submitButtonName\" value=\"Save\" border=\"0\" class=\"button\">\n"
      ."</td></tr></form></table>\n";
  closetable();
  page_footer();
}



function saveuser() {
	if ($_REQUEST['config_status']) $_REQUEST['config_status'] = 1; else $_REQUEST['config_status'] = 0;
	if ($_REQUEST['config_recycle']) $_REQUEST['config_recycle'] = 1; else $_REQUEST['config_recycle'] = 0;
	if ($_REQUEST['config_permbrowse']) $_REQUEST['config_permbrowse'] = 1; else $_REQUEST['config_permbrowse'] = 0;
	if ($_REQUEST['config_permupload']) $_REQUEST['config_permupload'] = 1; else $_REQUEST['config_permupload'] = 0;
	if ($_REQUEST['config_permcreate']) $_REQUEST['config_permcreate'] = 1; else $_REQUEST['config_permcreate'] = 0;
	if ($_REQUEST['config_permpass']) $_REQUEST['config_permpass'] = 1; else $_REQUEST['config_permpass'] = 0;
	if ($_REQUEST['config_permdelete']) $_REQUEST['config_permdelete'] = 1; else $_REQUEST['config_permdelete'] = 0;
	if ($_REQUEST['config_permmove']) $_REQUEST['config_permmove'] = 1; else $_REQUEST['config_permmove'] = 0;
	if ($_REQUEST['config_permedit']) $_REQUEST['config_permedit'] = 1; else $_REQUEST['config_permedit'] = 0;
	if ($_REQUEST['config_permrename']) $_REQUEST['config_permrename'] = 1; else $_REQUEST['config_permrename'] = 0;
	if ($_REQUEST['config_permget']) $_REQUEST['config_permget'] = 1; else $_REQUEST['config_permget'] = 0;
	if ($_REQUEST['config_permchmod']) $_REQUEST['config_permchmod'] = 1; else $_REQUEST['config_permchmod'] = 0;
	if ($_REQUEST['config_permsub']) $_REQUEST['config_permsub'] = 1; else $_REQUEST['config_permsub'] = 0;
	if ($_REQUEST['config_permuser']) $_REQUEST['config_permuser'] = 1; else $_REQUEST['config_permuser'] = 0;
	if ($_REQUEST['config_permadmin']) $_REQUEST['config_permadmin'] = 1; else $_REQUEST['config_permadmin'] = 0;
	if ($_REQUEST['config_permdeleteuser']) $_REQUEST['config_permdeleteuser'] = 1; else $_REQUEST['config_permdeleteuser'] = 0;
	if ($_REQUEST['config_permedituser']) $_REQUEST['config_permedituser'] = 1; else $_REQUEST['config_permedituser'] = 0;
	if ($_REQUEST['config_permmakeuser']) $_REQUEST['config_permmakeuser'] = 1; else $_REQUEST['config_permmakeuser'] = 0;
	if ($_REQUEST['config_permuser']) $_REQUEST['config_permuser'] = 1; else $_REQUEST['config_permuser'] = 0;
	if ($_REQUEST['config_permrecycle']) $_REQUEST['config_permrecycle'] = 1; else $_REQUEST['config_permrecycle'] = 0;
	if ($_REQUEST['config_permprefs']) $_REQUEST['config_permprefs'] = 1; else $_REQUEST['config_permprefs'] = 0;

	page_header("New user");
	opentitle("New user");
	opentable("100%");

	$query = '
		INSERT INTO '.$GLOBALS['config']['db']['pref'].'users 
		(user, pass, email, name, folder, http, spacelimit, theme, language, permbrowse, permupload, permcreate, permuser, permadmin, permdelete, permmove, permchmod, permget, permdeleteuser, permedituser, permmakeuser, permpass, permedit, permrename, permsub, permrecycle, permprefs, formatperm, status, recycle)
		VALUES(
			"'.$_REQUEST['config_user'].'", 
			"'.md5($_REQUEST['config_pass']).'",
			"'.$_REQUEST['config_email'].'",
			"'.$_REQUEST['config_name'].'",
			"'.$_REQUEST['config_folder'].'",
			"'.$_REQUEST['config_http'].'",
			"'.$_REQUEST['config_limit'].'",
			"'.$_REQUEST['config_theme'].'",
			"'.$_REQUEST['config_language'].'",
			"'.$_REQUEST['config_permbrowse'].'",
			"'.$_REQUEST['config_permupload'].'",
			"'.$_REQUEST['config_permcreate'].'",
			"'.$_REQUEST['config_permuser'].'",
			"'.$_REQUEST['config_permadmin'].'",
			"'.$_REQUEST['config_permdelete'].'",
			"'.$_REQUEST['config_permmove'].'",
			"'.$_REQUEST['config_permchmod'].'",
			"'.$_REQUEST['config_permget'].'",
			"'.$_REQUEST['config_permdeleteuser'].'",
			"'.$_REQUEST['config_permedituser'].'",
			"'.$_REQUEST['config_permmakeuser'].'",
			"'.$_REQUEST['config_permmakeuser'].'",
			"'.$_REQUEST['config_permedit'].'",
			"'.$_REQUEST['config_permrename'].'",
			"'.$_REQUEST['config_permsub'].'",
			"'.$_REQUEST['config_permrecycle'].'",
			"'.$_REQUEST['config_permprefs'].'",
			"'.$_REQUEST['config_formatperms'].'",
			"'.$_REQUEST['config_status'].'",
			"0"
		);';
	$msql = mysql_query($query) or die(mysql_error());
	echo "New user succesfully created<br>\n";
	closetable();
	page_footer();
}











function user() {
  page_header("Control Pannel");
  opentitle("Control Pannel");
  opentable("100%");
  echo "<a href=\"?p=pass\">Change Password</a><br>\n"
      ."<a href=\"?p=prefs\">Change Preferences</a><br>\n";
  closetable();
  page_footer();
}


function pass() {
  global $content, $extraheaders;
  $extraheaders = "<script language=javascript>\n"
                 ."  <!--\n"
                   .md5return()
                 ."  function encpass() {\n"
                 ."    if (md5_vm_test() && valid_js()) { \n"
                 ."      var hasho;\n"
                 ."      var hash;\n"
                 ."      var hashb;\n"
                 ."      var passizeo;\n"
                 ."      var passize;\n"
                 ."      var passizeb;\n"
                 ."      var showpasso = \"\";\n"
                 ."      var showpass = \"\";\n"
                 ."      var showpassb = \"\";\n"
                 ."      hasho = hex_md5(document.prefmod.passwordo.value);\n"
                 ."      hash = hex_md5(document.prefmod.password.value);\n"
                 ."      hashb = hex_md5(document.prefmod.password2.value);\n"
                 ."      passizeo = document.prefmod.passwordo.value.length;\n"
                 ."      passize = document.prefmod.password.value.length;\n"
                 ."      passizeb = document.prefmod.password2.value.length;\n"
                 ."      for (x=0; x<passizeo; x++ ) {\n"
                 ."        showpasso = showpasso + \"x\";\n"
                 ."      }\n"
                 ."      for (x=0; x<passize; x++ ) {\n"
                 ."        showpass = showpass + \"x\";\n"
                 ."      }\n"
                 ."      for (x=0; x<passizeb; x++ ) {\n"
                 ."        showpassb = showpassb + \"x\";\n"
                 ."      }\n"
                 ."      document.prefmod.passwordo.value = showpasso;\n"
                 ."      document.prefmod.password.value = showpass;\n"
                 ."      document.prefmod.password2.value = showpassb;\n"
                 ."      document.prefmod.encpaso.value = hasho;\n"
                 ."      document.prefmod.encpas1.value = hash;\n"
                 ."      document.prefmod.encpas2.value = hashb;\n"
                 ."      return true;\n"
                 ."    } else {\n"
                 ."      form.onsubmit=function(){return false;};\n"
                 ."      return false;\n"
                 ."    }\n"
                 ."  } \n"
                 ."  -->\n"
                 ."</script>\n";
  page_header("Change Password");
  opentitle("Change Password");
  opentable("100%");
  echo "<font class=error>**WARNING: Do not lose your password. It will not be replaced.**</font><br><br>\n"
      ."<form action=\"?p=password\" method=\"post\" name=prefmod onsubmit=\"return encpass(1);\">\n"
      ."<table>\n"
      ."<tr><td>Old Password: <td><input type=password name=passwordo value=\"\">\n"
      ."<tr><td>New Password: <td><input type=password name=password value=\"\">\n"
      ."<tr><td>Confirm New: <td><input type=password name=password2 value=\"\">\n"
      ."<input type=hidden name=\"encpaso\">\n"
      ."<input type=hidden name=\"encpas1\">\n"
      ."<input type=hidden name=\"encpas2\">\n"
      ."<tr><td colspan=\"2\"><br><input type=\"submit\" name=\"secure\" value=\"Change\" border=\"0\" class=\"button\">\n"
      ."</table>\n"
      ."</form>\n";
  closetable();
  page_footer();
}


function password($oldpass, $newpass, $cnewpass) {

  global $uid, $user, $content, $sqlpref;
  $flag=0; $content="";
  $mresult = mysql_query("SELECT pass FROM ".$GLOBALS['config']['db']['pref']."users WHERE user='$user'");
  list ($password) = mysql_fetch_row($mresult);
  if ($oldpass != $password) $error .= "<font class=error>Incorrect Old Password.</font><br>\n";
  if ($newpass != $cnewpass) $error .= "<font class=error>Passwords do not match.</font><br>\n";
  if ($newpass == "d41d8cd98f00b204e9800998ecf8427e") $error .= "<font class=error>You must enter a password.</font><br>\n";
  if (!$error) {
    $msql = mysql_query("UPDATE ".$GLOBALS['config']['db']['pref']."users SET pass='$newpass' WHERE user='$user'") or die (mysql_error());
	setcookie('pass',md5($newpass.$_COOKIE['sess']),time()+60*60*24*1);
    page_header("Change Password");
    opentitle("Change Password");
    opentable("100%");
    echo "<font class=ok>Your password has been succesfully changed.</font><br>\n";
  } else {
    page_header("Change Password");
    opentitle("Change Password");
    opentable("100%");
    echo $error;
  }
  closetable();
  page_footer();
}



function prefs() {
  global $user, $d, $error, $sqlpref, $extraheaders, $config_email, $config_name, $config_theme, $config_language, $config_recycle, $config_formatperm;
  $result = mysql_query("SELECT id, user, email, name, theme, language, recycle, formatperm, permrecycle FROM ".$GLOBALS['config']['db']['pref']."users WHERE user='".$user."'");
  list($uid, $uname, $email, $name, $theme, $language, $recycle, $formatperm, $permrecycle) = mysql_fetch_row($result);
  if ($error){
    $email = $config_email;
    $name = $config_name;
    $theme = $config_theme;
    $language = $config_language;
    $recycle = $config_recycle;
    $formatperm = $config_formatperm;
  }
  page_header("User Preferences");
  opentitle("User Preferences");
  opentable("100%");
  echo "<table>\n"
      ."<form name=\"prefs\" action=\"?p=saveprefs\" method=\"post\">\n";
  if ($formatperm == '0') $perm1 = " checked";
  elseif ($formatperm == '1') $perm2 = " checked"; 
  if ($recycle == 0) $rec1 = " checked";
  elseif ($recycle == 1) $rec2 = " checked";
  echo "<tr><td>Username: <td><input disabled type=\"text\" name=\"config_user\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$uname\">\n";

  if($error && !$config_name) echo "<tr><td valign=bottom>Name: <td><font class=error>Invalid Name</font><br><table cellpadding=0 cellspacing=0 class=errorbox><tr><td><table cellpadding=1 cellspacing=1><tr><td><input type=\"text\" name=\"config_name\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$name\"></table></table>\n";
  else echo "<tr><td>Name: <td><input type=\"text\" name=\"config_name\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$name\">\n";
  if($error && !$config_email|!ismail($config_email)) echo"<tr><td valign=bottom>Email: <td><font class=error>Invalid Email</font><br><table cellpadding=0 cellspacing=0 class=errorbox><tr><td><table cellpadding=1 cellspacing=1><tr><td><input type=\"text\" name=\"config_email\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$email\"></table></table>\n";
  else echo"<tr><td>Email: <td><input type=\"text\" name=\"config_email\" size=\"40\" border=\"0\" class=\"txtinput\" value=\"$email\">\n";

  echo "<tr><td>Language: <td><select name=\"config_language\">\n";
  $handle = opendir("./language");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./language/".$file)) {
      @include("./language/".$file."/lng.def.php");
      if ($language == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$LNG_NAME</option>\n";
    }
  }
  closedir("./language");
  echo "</select><tr><td>Theme: <td><select name=\"config_theme\">\n";
  $handle = opendir("./themes");
  while ($file = readdir($handle)) $filelist[] = $file;
  natcasesort($filelist);
  foreach ($filelist as $file) {
    if ($file != "." && $file != ".." && is_dir("./themes/".$file)) {
      @include("./themes/".$file."/theme.def.php");
      if ($theme == $file) $isel = " selected"; else $isel = "";
      echo "<option value=\"$file\"$isel>$THEME_NAME</option>\n";
    }
  }
  closedir("./themes");
  echo "</select>\n";
  if ($permrecycle == 1 && $GLOBALS['config']['enable_trash']) {
    echo "<tr><td>Trash Bin: <td>\n"
        ."<input type=radio name=\"config_recycle\" id=\"rec1\"$rec2><label for=\"rec1\"> On</label>&nbsp;&nbsp;&nbsp;\n"
        ."<input type=radio name=\"config_recycle\" id=\"rec2\"$rec1><label for=\"rec2\"> Off</label>\n";
  }
  echo "<tr><td>Permission View: <td>\n"
      ."<input type=\"radio\" name=\"config_formatperm\" id=\"perm1\" value=\"0\" $perm1><label for=\"perm1\"> UNIX (0644)</label>&nbsp;&nbsp;&nbsp;\n"
      ."<input type=\"radio\" name=\"config_formatperm\" id=\"perm2\" value=\"1\" checked><label for=\"perm2\"> Symbolic (-rw-r--r--)</label>\n"
      ."<input type=hidden name=d value=\"$d\">\n"
      ."<tr><td colspan=\"2\"><br><input type=\"submit\" name=\"submitButtonName\" value=\"Save\" border=\"0\" class=\"button\">\n"
      ."</td></tr></form></table>\n";
  closetable();
  page_footer();
}


function preferences($config_email, $config_name, $config_theme, $config_language, $config_recycle, $config_formatperm) {
  global $d, $userid, $error;
  if (!$config_email || !ismail($config_email)) $error = TRUE;
  if (!$config_name) $error = TRUE;
  if (!$config_theme) $error = TRUE;
  if (!$config_language) $error = TRUE;
  if ($error) prefs();
  else {
    page_header("User Preferences");
    opentitle("User Preferences");
    opentable("100%");
    $msql = mysql_query("UPDATE ".$GLOBALS['config']['db']['pref']."users SET email='$config_email', name='$config_name', theme='$config_theme', language='$config_language', recycle='$config_recycle', formatperm='$config_formatperm' WHERE id='$userid'") or die(mysql_error());
    echo "<font class=ok>Your preferences have been sucessfully saved.</font><br><br><a href=\"?d=$d\">Return Home</a>\n";
    closetable();
    page_footer();
  }
}











/****************************************************************************/
/*                                                                          */
/* A JavaScript implementation of the RSA Data Security, Inc. MD5 Message   */
/* Digest Algorithm, as defined in RFC 1321.                                */
/* Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.                     */
/* Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet            */
/* Distributed under the BSD License                                        */
/* See http://pajhome.org.uk/crypt/md5 for more info.                       */
/*                                                                          */
/****************************************************************************/

function md5return() {
  return "
    var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
    var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

    function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}

    function md5_vm_test() {
      return hex_md5(\"abc\") == \"900150983cd24fb0d6963f7d28e17f72\";
    }


    function core_md5(x, len) {
      x[len >> 5] |= 0x80 << ((len) % 32);
      x[(((len + 64) >>> 9) << 4) + 14] = len;

      var a =  1732584193;
      var b = -271733879;
      var c = -1732584194;
      var d =  271733878;

      for(var i = 0; i < x.length; i += 16) {
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;

        a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
        d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
        c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
        b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
        a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
        d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
        c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
        b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
        a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
        d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
        c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
        b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
        a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
        d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
        c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
        b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

        a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
        d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
        c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
        b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
        a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
        d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
        c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
        b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
        a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
        d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
        c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
        b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
        a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
        d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
        c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
        b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

        a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
        d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
        c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
        b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
        a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
        d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
        c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
        b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
        a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
        d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
        c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
        b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
        a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
        d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
        c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
        b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

        a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
        d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
        c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
        b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
        a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
        d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
        c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
        b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
        a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
        d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
        c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
        b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
        a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
        d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
        c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
        b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

        a = safe_add(a, olda);
        b = safe_add(b, oldb);
        c = safe_add(c, oldc);
        d = safe_add(d, oldd);
      }
      return Array(a, b, c, d);

    }

    function md5_cmn(q, a, b, x, s, t) {
      return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
    }
    function md5_ff(a, b, c, d, x, s, t) {
      return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }
    function md5_gg(a, b, c, d, x, s, t) {
      return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }
    function md5_hh(a, b, c, d, x, s, t) {
      return md5_cmn(b ^ c ^ d, a, b, x, s, t);
    }
    function md5_ii(a, b, c, d, x, s, t) {
      return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
    }

    function safe_add(x, y) {
      var lsw = (x & 0xFFFF) + (y & 0xFFFF);
      var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
      return (msw << 16) | (lsw & 0xFFFF);
    }

    function bit_rol(num, cnt) {
      return (num << cnt) | (num >>> (32 - cnt));
    }

    function str2binl(str) {
      var bin = Array();
      var mask = (1 << chrsz) - 1;
      for(var i = 0; i < str.length * chrsz; i += chrsz)
        bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
      return bin;
    }

    function binl2hex(binarray) {
      var hex_tab = hexcase ? \"0123456789ABCDEF\" : \"0123456789abcdef\";
      var str = \"\";
      for(var i = 0; i < binarray.length * 4; i++) {
        str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
               hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
      }
      return str;
    }

    function valid_js() {      
      if (navigator.userAgent.indexOf(\"Mozilla/\") == 0) {
        return (parseInt(navigator.appVersion) >= 4);
      
      }else if( navigator.userAgent.indexOf(\"Opera/\") == 0) {
        return (parseInt(navigator.appVersion) >= 9);
      }
      return false;
    }
  ";
}




if ( isset($_REQUEST['p']) ) $p=$_REQUEST['p']; else $p=null;
switch($p) {
  case "logout":
    logout();
	break;
  case "up":
    if ($permupload == 1) up();
    else permerror("You do not currently have permission to upload.\n");
    break;
  case "upload":
    if ($permupload == 1) upload($_FILES['upfile'], $_REQUEST['ndir'], $d);
    else permerror("You do not currently have permission to upload.\n");
    break;
  case "edit":
    if ($permedit == 1) edit($_REQUEST['fename']);
    else permerror("You do not currently have permission to edit.\n");
    break;
  case "save":
    if ($permedit == 1) save($_REQUEST['ncontent'], $_REQUEST['fename'], $d, $_REQUEST['next_action']);
    else permerror("You do not currently have permission to edit.\n");
    break;
  case "cr":
    if ($permcreate == 1) cr();
    else permerror("You do not currently have permission to create.\n");
    break;
  case "create":
    if ($permcreate == 1) create($_REQUEST['nfname'], $_REQUEST['isfolder'], $d, $_REQUEST['ndir']);
    else permerror("You do not currently have permission to create.\n");
    break;
  case "ren":
    if ($permrename == 1) ren($_REQUEST['file']);
    else permerror("You do not currently have permission to rename.\n");
    break;
  case "rename":
    if ($permrename == 1) renam($_REQUEST['rename'], $_REQUEST['nrename'], $d);
    else permerror("You do not currently have permission to rename.\n");
    break;
  case "user":
    if ($permuser == 1) user();
    else permerror("You do not currently have permission to access the user control pannel.\n");
    break;
  case "pass":
    if ($permpass == 1) pass();
    else permerror("You do not currently have permission to change your password.\n");
    break;
  case "password":
    if ($permpass == 1) password($_REQUEST['encpaso'], $_REQUEST['encpas1'], $_REQUEST['encpas2']);
    else permerror("You do not currently have permission to change your password.\n");
    break;
  case "prefs":
    if ($permprefs == 1) prefs();
    else permerror("You do not currently have permission to change your preferences.\n");
    break;
  case "saveprefs":
    if ($permpass == 1) preferences($_REQUEST['config_email'], $_REQUEST['config_name'], $_REQUEST['config_theme'], $_REQUEST['config_language'], $_REQUEST['config_recycle'], $_REQUEST['config_formatperm']);
    else permerror("You do not currently have permission to change your preferences.\n");
    break;
  case "super":
    if ($permadmin == 1) super();
    else permerror("You do not currently have permission to access the admin pannel.\n");
    break;
  case "newuser":
    if ($permmakeuser == 1) newuser();
    else permerror("You do not currently have permission to create new users.\n");
    break;
  case "saveuser":
    if ($permmakeuser == 1) saveuser();
    else permerror("You do not currently have permission to create new users.\n");
    break;
  case "eduser":
    if ($permedituser == 1) eduser($_REQUEST['muid']);
    else permerror("You do not currently have permission to edit users.\n");
    break;
  case "edituser":
    if ($permedituser == 1) edituser($_REQUEST['muid']);
    else permerror("You do not currently have permission to edit users.\n");
    break;
  case "deluser":
    if ($permdeleteuser == 1) deluser($_REQUEST['muid']);
    else permerror("You do not currently have permission to delete users.\n");
    break;
  case "deleteuser":
    if ($permdeleteuser == 1) deleteuser($_REQUEST['muid']);
    else permerror("You do not currently have permission to delete users.\n");
    break;
  case "view":
    if ($permget == 1) viewfile($_REQUEST['file'],$d);
    break;
  case "printerror":
    printerror($error);
    break;
  case "bulk_submit":
    if ($permmove != 1 && $_REQUEST['bulk_action'] == "move") permerror("You do not currently have permission to move files/folders.\n");
    elseif ($permdelete != 1 && $_REQUEST['bulk_action'] == "delete") permerror("You do not currently have permission to delete files/folders.\n");
    elseif ($permchmod != 1 && $_REQUEST['bulk_action'] == "chmod") permerror("You do not currently have permission to change file/folders permissions.\n");
    else bulk_submit($_REQUEST['bulk_action'], $d);
    break;
  case "bulk_action":
    if ($permmove != 1 && $_REQUEST['bulk_action'] == "move") permerror("You do not currently have permission to move files/folders.\n");
    elseif ($permdelete != 1 && $_REQUEST['bulk_action'] == "delete") permerror("You do not currently have permission to delete files/folders.\n");
    elseif ($permchmod != 1 && $_REQUEST['bulk_action'] == "chmod") permerror("You do not currently have permission to change file/folders permissions.\n");
    else bulk_action($_REQUEST['bulk_action'], $d,$_REQUEST['ndir']);
    break;
  case "users":
    if ($permedituser == "1" || $permedituser == "1") usermod();
    else permerror("You do not currently have permission to modify users.\n");
    break;
  default:
  case "home":
    if ($permbrowse == 1) home();
    else permerror("You do not currently have permission to browse.\n");
    break;
}
?>