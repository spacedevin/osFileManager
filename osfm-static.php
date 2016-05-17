<?php
/****************************************************************/
/*                                                              */
/* osfm Static                                                  */
/* ----------                                                   */
/*                                                              */
/* This script will perform basic functions on files            */
/* Functions include, List, Open, View, Edit, Create, Upload,   */
/*   Rename and Move.                                           */
/*                                                              */
/* This version has had its SQL striped out of it.              */
/*                                                              */
/*                                                              */
/* Written by Devin Smith, July 1st 2003                        */
/* http://www.osfilemanager.com                                 */
/* http://www.arzy.net                                          */
/*                                                              */
/****************************************************************/


/****************************************************************/
/* Config Section                                               */
/*                                                              */
/* $adminfile - THIS filename.                                  */
/* $sitetitle - The title at the top of all pages.              */
/* $filefolder - The main folder to be browsing.                */
/* $user - The username required to login.                      */
/* $pass - The password required to login.                      */
/* $tbcolor1 - Color1 of TD on home.                            */
/* $tbcolor2 - Color2 of TD on home.                            */
/* $tbcolor3 - Top of home list.                                */
/* $bgcolor1 - Main color of page.                              */
/* $bgcolor2 - Color of page border.                            */
/* $bgcolor3 - Color of textarea abd button borders.            */
/* $txtcolor1 - Color of Normal text and hovered links.         */
/* $txtcolor2 - Color of links.                                 */
/****************************************************************/
$adminfile = $SCRIPT_NAME;
$tbcolor1c = "#bacaee";
$tbcolor2c = "#daeaff";
$tbcolor3c = "#7080dd";
$bgcolor1c = "#ffffff";
$bgcolor2c = "#a6a6a6";
$bgcolor3c = "#003399";
$txtcolor1c = "#000000";
$txtcolor2c = "#003399";
$filefolder = "./";
$sitetitle = 'OSFM Static';
$user = 'admin';
$pass = 'pass';



if (!$tbcolor1) $tbcolor1 = $tbcolor1c;
if (!$tbcolor2) $tbcolor2 = $tbcolor2c;
if (!$tbcolor3) $tbcolor3 = $tbcolor3c;
if (!$bgcolor1) $bgcolor1 = $bgcolor1c;
if (!$bgcolor2) $bgcolor2 = $bgcolor2c;
if (!$bgcolor3) $bgcolor3 = $bgcolor3c;
if (!$txtcolor1) $txtcolor1 = $txtcolor1c;
if (!$txtcolor2) $txtcolor2 = $txtcolor2c;

$op = $_REQUEST['op'];
$folder = $_REQUEST['folder'];
while (preg_match('/\.\.\//',$folder)) $folder = preg_replace('/\.\.\//','/',$folder);
while (preg_match('/\/\//',$folder)) $folder = preg_replace('/\/\//','/',$folder);

if ($folder == '') {
  $folder = $filefolder;
} elseif ($filefolder != '') {
  if (!ereg($filefolder,$folder)) {
    $folder = $filefolder;
  }  
}


/****************************************************************/
/* User identification                                          */
/*                                                              */
/* Looks for cookies. Yum.                                      */
/****************************************************************/

if ($_COOKIE['user'] != $user || $_COOKIE['pass'] != md5($pass)) {
	if ($_REQUEST['user'] == $user && $_REQUEST['pass'] == $pass) {
	    setcookie('user',$user,time()+60*60*24*1);
	    setcookie('pass',md5($pass),time()+60*60*24*1);
	} else {
		if ($_REQUEST['user'] == $user || $_REQUEST['pass']) $er = true;
		login($er);
	}
}



/****************************************************************/
/* function maintop()                                           */
/*                                                              */
/* Controls the style and look of the site.                     */
/* Recieves $title and displayes it in the title and top.       */
/****************************************************************/
function maintop($title,$showtop = true) {
  global $sitetitle, $lastsess, $login, $viewing, $iftop, $bgcolor1, $bgcolor2, $bgcolor3, $txtcolor1, $txtcolor2, $user, $pass, $password, $debug, $issuper;
  echo "<html>\n<head>\n"
      ."<title>$sitetitle :: $title</title>\n"
      ."</head>\n"
      ."<body bgcolor=\"#ffffff\">\n"
      ."<style>\n"
      ."td { font-size : 80%;font-family : tahoma;color: $txtcolor1;font-weight: 700;}\n"
      ."A:visited {color: \"$txtcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."A:hover {color: \"$txtcolor1\";font-weight: bold;text-decoration: underline;}\n"
      ."A:link {color: \"$txtcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."A:active {color: \"$bgcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."textarea {border: 1px solid $bgcolor3 ;color: black;background-color: white;}\n"
      ."input.button{border: 1px solid $bgcolor3;color: black;background-color: white;}\n"
      ."input.text{border: 1px solid $bgcolor3;color: black;background-color: white;}\n"
      ."BODY {color: $txtcolor1; FONT-SIZE: 10pt; FONT-FAMILY: Tahoma, Verdana, Arial, Helvetica, sans-serif; scrollbar-base-color: $bgcolor2; MARGIN: 0px 0px 10px; BACKGROUND-COLOR: $bgcolor1}\n"
      .".title {FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #000000; TEXT-ALIGN: center; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif}\n"
      .".copyright {FONT-SIZE: 8pt; COLOR: #000000; TEXT-ALIGN: left}\n"
      .".error {FONT-SIZE: 10pt; COLOR: #AA2222; TEXT-ALIGN: left}\n"
      ."</style>\n\n";

  if ($viewing == "") {
    echo "<table cellpadding=10 cellspacing=10 bgcolor=$bgcolor1 align=center><tr><td>\n"
        ."<table cellpadding=1 cellspacing=1 bgcolor=$bgcolor2><tr><td>\n"
        ."<table cellpadding=5 cellspacing=5 bgcolor=$bgcolor1><tr><td>\n";
  } else {
    echo "<table cellpadding=7 cellspacing=7 bgcolor=$bgcolor1><tr><td>\n";
  }

  echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
      ."<tr><td align=\"left\"><font face=\"Arial\" color=\"black\" size=\"4\">$sitetitle</font><font size=\"3\" color=\"black\"> :: $title</font></td>\n"
      ."<tr><td width=650 style=\"height: 1px;\" bgcolor=\"black\"></td></tr>\n";

  if ($showtop) {
    echo "<tr><td><font size=\"2\">\n"
        ."<a href=\"".$adminfile."?op=home\" $iftop>Home</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=up\" $iftop>Upload</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=cr\" $iftop>Create</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=logout\" $iftop>Logout</a>\n";

    echo "<tr><td width=650 style=\"height: 1px;\" bgcolor=\"black\"></td></tr>\n";
  }
  echo "</table><br>\n";
}


/****************************************************************/
/* function login()                                             */
/*                                                              */
/* Sets the cookies and alows user to log in.                   */
/* Recieves $pass as the user entered password.                 */
/****************************************************************/
function login($er=false) {
  global $op;
    setcookie("user","",time()-60*60*24*1);
    setcookie("pass","",time()-60*60*24*1);
    maintop("Login",false);

    if ($er) { 
		echo "<font class=error>**ERROR: Incorrect login information.**</font><br><br>\n"; 
	}

    echo "<form action=\"".$adminfile."?op=".$op."\" method=\"post\">\n"
        ."<table><tr>\n"
        ."<td><font size=\"2\">Username: </font>"
        ."<td><input type=\"text\" name=\"user\" size=\"18\" border=\"0\" class=\"text\" value=\"$user\">\n"
        ."<tr><td><font size=\"2\">Password: </font>\n"
        ."<td><input type=\"password\" name=\"pass\" size=\"18\" border=\"0\" class=\"text\" value=\"$pass\">\n"
        ."<tr><td colspan=\"2\"><input type=\"submit\" name=\"submitButtonName\" value=\"Login\" border=\"0\" class=\"button\">\n"
        ."</table>\n"
        ."</form>\n";
  mainbottom();

}


/****************************************************************/
/* function home()                                              */
/*                                                              */
/* Main function that displays contents of folders.             */
/****************************************************************/
function home() {
  global $folder, $tbcolor1, $tbcolor2, $tbcolor3, $filefolder, $HTTP_HOST;
  maintop("Home");
  echo "<font face=\"tahoma\" size=\"2\"><b>\n"
      ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=100%>\n";

  $content1 = "";
  $content2 = "";

  $count = "0";
  $style = opendir($folder);
  $a=1;
  $b=1;

  if ($folder) {
    if (ereg("/home/",$folder)) {
      $folderx = ereg_replace("$filefolder", "", $folder);
      $folderx = "http://".$HTTP_HOST."/".$folderx;
    } else {
      $folderx = $folder;
    } 
  }

  while($stylesheet = readdir($style)) {
    if (strlen($stylesheet)>40) { 
      $sstylesheet = substr($stylesheet,0,40)."...";
    } else {
      $sstylesheet = $stylesheet;
    }
    if ($stylesheet[0] != "." && $stylesheet[0] != ".." ) {
      if (is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) { 
        $content1[$a] ="<td>".$sstylesheet."</td>\n"
                 ."<td> "
                 //.disk_total_space($folder.$stylesheet)." Commented out due to certain problems
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=home&folder=".$folder.$stylesheet."/\">Open</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=ren&file=".$stylesheet."&folder=$folder\">Rename</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=del&dename=".$stylesheet."&folder=$folder\">Delete</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=mov&file=".$stylesheet."&folder=$folder\">Move</a>\n"
                 ."<td align=\"center\"> <tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";
        $a++;
      } elseif (!is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) { 
        $content2[$b] ="<td><a href=\"".$folderx.$stylesheet."\">".$sstylesheet."</a></td>\n"
                 ."<td align=\"left\"><img src=pixel.gif width=5 height=1>".filesize($folder.$stylesheet)
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=edit&fename=".$stylesheet."&folder=$folder\">Edit</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=ren&file=".$stylesheet."&folder=$folder\">Rename</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=del&dename=".$stylesheet."&folder=$folder\">Delete</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=mov&file=".$stylesheet."&folder=$folder\">Move</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=viewframe&file=".$stylesheet."&folder=$folder\">View</a>\n"
                 ."<tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";
        $b++;
      } else {
        echo "Directory is unreadable\n";
      }
    $count++;
    } 
  }
  closedir($style);

  echo "Browsing: $folder\n"
       ."<br>Number of Files: " . $count . "<br><br>";

  echo "<tr bgcolor=\"$tbcolor3\" width=100%>"
      ."<td width=300>Filename\n"
      ."<td width=65>Size\n"
      ."<td align=\"center\" width=44>Open\n"
      ."<td align=\"center\" width=58>Rename\n"
      ."<td align=\"center\" width=57>Delete\n"
      ."<td align=\"center\" width=40>Move\n"
      ."<td align=\"center\" width=44>View\n"
      ."<tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";

  for ($a=1; $a<count($content1)+1;$a++) {
    $tcoloring   = ($a % 2) ? $tbcolor1 : $tbcolor2;
    echo "<tr bgcolor=".$tcoloring." width=100%>";
    echo $content1[$a];
  }

  for ($b=1; $b<count($content2)+1;$b++) {
    $tcoloring   = ($a++ % 2) ? $tbcolor1 : $tbcolor2;
    echo "<tr bgcolor=".$tcoloring." width=100%>";
    echo $content2[$b];
  }

  echo"</table>";
  mainbottom();
}


/****************************************************************/
/* function up()                                                */
/*                                                              */
/* First step to Upload.                                        */
/* User enters a file and the submits it to upload()            */
/****************************************************************/
function up() {
  global $folder, $content, $filefolder;
  maintop("Upload");

  echo "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"".$adminfile."?op=upload\" METHOD=\"POST\">\n"
      ."<font face=\"tahoma\" size=\"2\"><b>File:</b></font><br><input type=\"File\" name=\"upfile\" size=\"20\" class=\"text\">\n"

      ."<br><br>Destination:<br><select name=\"ndir\" size=1>\n"
      ."<option value=\"".$filefolder."\">".$filefolder."</option>";
  listdir($filefolder);
  echo $content
      ."</select><br><br>"

      ."<input type=\"submit\" value=\"Upload\" class=\"button\">\n"
      ."</form>\n";

  mainbottom();
}


/****************************************************************/
/* function upload()                                            */
/*                                                              */
/* Sencond step in upload.                                      */
/* Saves the file to the disk.                                  */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function upload($upfile, $ndir) {

  global $folder;
  if (!$upfile) {
    error("Filesize too big or bytes=0");
  } elseif($upfile['name']) { 
    if(copy($upfile['tmp_name'],$ndir.$upfile['name'])) { 
      maintop("Upload");
      echo "The file ".$upfile['name'].$folder.$upfile_name." uploaded successfully.\n";
      mainbottom();
    } else {
      printerror("File $upfile failed to upload.");
    }
  } else {
    printerror("Please enter a filename.");
  }
}


/****************************************************************/
/* function del()                                               */
/*                                                              */
/* First step in delete.                                        */
/* Prompts the user for confirmation.                           */
/* Recieves $dename and ask for deletion confirmation.          */
/****************************************************************/
function del($dename) {
  global $folder;
    if (!$dename == "") {
    maintop("Delete");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**WARNING: This will permanatly delete ".$folder.$dename.". This action is irreversable.**</font><br><br>\n"
        ."Are you sure you want to delete ".$folder.$dename."?<br><br>\n"
        ."<a href=\"".$adminfile."?op=delete&dename=".$dename."&folder=$folder\">Yes</a> | \n"
        ."<a href=\"".$adminfile."?op=home\"> No </a>\n"
        ."</table>\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function delete()                                            */
/*                                                              */
/* Second step in delete.                                       */
/* Deletes the actual file from disk.                           */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function delete($dename) {
  global $folder;
  if (!$dename == "") {
    maintop("Delete");
    if (is_dir($folder.$dename)) {
      if(rmdir($folder.$dename)) {
        echo $dename." has been deleted.";
      } else {
        echo "There was a problem deleting this directory. ";
      }
    } else {
      if(unlink($folder.$dename)) {
        echo $dename." has been deleted.";
      } else {
        echo "There was a problem deleting this file. ";
      }
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function edit()                                              */
/*                                                              */
/* First step in edit.                                          */
/* Reads the file from disk and displays it to be edited.       */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function edit($fename) {
  global $folder;
  if (!$fename == "") {
    maintop("Edit");
    echo $folder.$fename;

    echo "<form action=\"".$adminfile."?op=save\" method=\"post\">\n"
        ."<textarea cols=\"73\" rows=\"40\" name=\"ncontent\">\n";

   $handle = fopen ($folder.$fename, "r");
   $contents = "";

    while ($x<1) {
      $data = @fread ($handle, filesize ($folder.$fename));
      if (strlen($data) == 0) {
        break;
      }
      $contents .= $data;
    }
    fclose ($handle);

    $replace1 = "</text";
    $replace2 = "area>";
    $replace3 = "< / text";
    $replace4 = "area>";
    $replacea = $replace1.$replace2;
    $replaceb = $replace3.$replace4;
    $contents = ereg_replace ($replacea,$replaceb,$contents);

    echo $contents;

    echo "</textarea>\n"
        ."<br><br>\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n"
        ."<input type=\"hidden\" name=\"fename\" value=\"".$fename."\">\n"
        ."<input type=\"submit\" value=\"Edit\" class=\"button\">\n"
        ."</form>\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function save()                                              */
/*                                                              */
/* Second step in edit.                                         */
/* Recieves $ncontent from edit() as the file content.          */
/* Recieves $fename from edit() as the file name to modify.     */
/****************************************************************/
function save($ncontent, $fename) {
  global $folder;
  if (!$fename == "") {
    maintop("Edit");
    $loc = $folder.$fename;
    $fp = fopen($loc, "w");

    $replace1 = "</text";
    $replace2 = "area>";
    $replace3 = "< / text";
    $replace4 = "area>";
    $replacea = $replace1.$replace2;
    $replaceb = $replace3.$replace4;
    $ncontent = ereg_replace ($replaceb,$replacea,$ncontent);

    $ydata = stripslashes($ncontent);

    if(fwrite($fp, $ydata)) {
      echo "The file <a href=\"".$adminfile."?op=viewframe&file=".$fename."&folder=".$folder."\">".$folder.$fename."</a> was succesfully edited\n";
      $fp = null;
    } else {
      echo "There was a problem editing this file\n";
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function cr()                                                */
/*                                                              */
/* First step in create.                                        */
/* Promts the user to a filename and file/directory switch.     */
/****************************************************************/
function cr() {
  global $folder, $content, $filefolder;
  maintop("Create");
  if (!$content == "") { echo "<br><br>Please enter a filename.\n"; }
  echo "<form action=\"".$adminfile."?op=create\" method=\"post\">\n"
      ."Filename: <br><input type=\"text\" size=\"20\" name=\"nfname\" class=\"text\"><br><br>\n"
   
      ."Destination:<br><select name=ndir size=1>\n"
      ."<option value=\"".$filefolder."\">".$filefolder."</option>";
  listdir($filefolder);
  echo $content
      ."</select><br><br>";


  echo "File <input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"0\" checked><br>\n"
      ."Directory <input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"1\"><br><br>\n"
      ."<input type=\"hidden\" name=\"folder\" value=\"$folder\">\n"
      ."<input type=\"submit\" value=\"Create\" class=\"button\">\n"
      ."</form>\n";
  mainbottom();
}


/****************************************************************/
/* function create()                                            */
/*                                                              */
/* Second step in create.                                       */
/* Creates the file/directoy on disk.                           */
/* Recieves $nfname from cr() as the filename.                  */
/* Recieves $infolder from cr() to determine file trpe.         */
/****************************************************************/
function create($nfname, $isfolder, $ndir) {
  global $folder;
  if (!$nfname == "") {
    maintop("Create");

    if ($isfolder == 1) {
      if(mkdir($ndir."/".$nfname, 0777)) {
        echo "Your directory, <a href=\"".$adminfile."?op=viewframe&file=".$nfname."&folder=$ndir\">".$ndir."/".$nfname."</a> was succesfully created.\n";
      } else {
        echo "The directory, ".$ndir."/".$nfname." could not be created. Check to make sure the permisions on the /files directory is set to 777\n";
      }
    } else {
      if(fopen($ndir."/".$nfname, "w")) {
        echo "Your file, <a href=\"".$adminfile."?op=viewframe&file=".$nfname."&folder=$ndir\">".$ndir.$nfname."</a> was succesfully created.\n";
      } else {
        echo "The file, ".$ndir."/".$nfname." could not be created. Check to make sure the permisions on the /files directory is set to 777\n";
      }
    }
    mainbottom();
  } else {
    cr();
  }
}


/****************************************************************/
/* function ren()                                               */
/*                                                              */
/* First step in rename.                                        */
/* Promts the user for new filename.                            */
/* Globals $file and $folder for filename.                      */
/****************************************************************/
function ren($file) {
  global $folder;
  if (!$file == "") {
    maintop("Rename");
    echo "<form action=\"".$adminfile."?op=rename\" method=\"post\">\n"
        ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."Renaming ".$folder.$file;

    echo "</table><br>\n"
        ."<input type=\"hidden\" name=\"rename\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n"
        ."New Name:<br><input class=\"text\" type=\"text\" size=\"20\" name=\"nrename\">\n"
        ."<input type=\"Submit\" value=\"Rename\" class=\"button\">\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function renam()                                             */
/*                                                              */
/* Second step in rename.                                       */
/* Rename the specified file.                                   */
/* Recieves $rename from ren() as the old  filename.            */
/* Recieves $nrename from ren() as the new filename.            */
/****************************************************************/
function renam($rename, $nrename, $folder) {
  global $folder;
  if (!$rename == "") {
    maintop("Rename");
    $loc1 = "$folder".$rename; 
    $loc2 = "$folder".$nrename;

    if(rename($loc1,$loc2)) {
      echo "The file ".$folder.$rename." has been changed to <a href=\"".$adminfile."?op=viewframe&file=".$nrename."&folder=$folder\">".$folder.$nrename."</a>\n";
    } else {
      echo "There was a problem renaming this file\n";
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function listdir()                                           */
/*                                                              */
/* Recursivly lists directories and sub-directories.            */
/* Recieves $dir as the directory to scan through.              */
/****************************************************************/
function listdir($dir, $level_count = 0) {
  global $content;
    if (!@($thisdir = opendir($dir))) { return; }
    while ($item = readdir($thisdir) ) {
      if (is_dir("$dir/$item") && (substr("$item", 0, 1) != '.')) {
        listdir("$dir/$item", $level_count + 1);
      }
    }
    if ($level_count > 0) {
      $dir = ereg_replace("[/][/]", "/", $dir);
      $content .= "<option value=\"".$dir."/\">".$dir."/</option>";
    }
}


/****************************************************************/
/* function mov()                                               */
/*                                                              */
/* First step in move.                                          */
/* Prompts the user for destination path.                       */
/* Recieves $file and sends to move().                          */
/****************************************************************/
function mov($file) {
  global $folder, $content, $filefolder;
  if (!$file == "") {
    maintop("Move");
    echo "<form action=\"".$adminfile."?op=move\" method=\"post\">\n"
        ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."Move ".$folder.$file." to:\n"
        ."<select name=ndir size=1>\n"
        ."<option value=\"".$filefolder."\">".$filefolder."</option>";
    listdir($filefolder);
    echo $content
        ."</select>"
        ."</table><br><input type=\"hidden\" name=\"file\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n" 
        ."<input type=\"Submit\" value=\"Move\" class=\"button\">\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function move()                                              */
/*                                                              */
/* Second step in move.                                         */
/* Moves the oldfile to the new one.                            */
/* Recieves $file and $ndir and creates $file.$ndir             */
/****************************************************************/
function move($file, $ndir, $folder) {
  global $folder;
  if (!$file == "") {
    maintop("Move");
    if (rename($folder.$file, $ndir.$file)) {
      echo $folder.$file." has been succesfully moved to ".$ndir.$file;
    } else {
      echo "There was an error moving ".$folder.$file;
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function viewframe()                                         */
/*                                                              */
/* First step in viewframe.                                     */
/* Takes the specified file and displays it in a frame.         */
/* Recieves $file and sends it to viewtop                       */
/****************************************************************/
function viewframe($file) {
  global $sitetitle, $folder, $HTTP_HOST, $filefolder;  
  if ($filefolder == "/") {
    $error="**ERROR: You selected to view $file but your home directory is /.**";
    printerror($error);
    die();
  } elseif (ereg("/home/",$folder)) {
    $folderx = ereg_replace("$filefolder", "", $folder);
    $folder = "http://".$HTTP_HOST."/".$folderx;
  }
  echo "<html>\n"
      ."<head>\n"
      ."<title>$sitetitle :: Viewing file - $file</title>\n"
      ."</head>\n\n"

      ."<frameset rows=\"85,*\" frameborder=\"no\">\n"
      ."<frame name=\"top\" noresize src=\"".$adminfile."?op=viewtop&file=$file\" scrolling=\"no\">\n"
      ."<frame name=\"content\" noresize src=\"".$folder.$file."\">\n"
      ."</frameset>\n\n"

      ."<body>\n"
      ."</body>\n"
      ."</html>\n";
}


/****************************************************************/
/* function viewtop()                                           */
/*                                                              */
/* Second step in viewframe.                                    */
/* Controls the top bar on the viewframe.                       */
/* Recieves $file from viewtop.                                 */
/****************************************************************/
function viewtop($file) {
  global $viewing, $iftop;
  $viewing = "yes";
  $iftop = "target=_top";
  maintop("Viewing file - $file");
}


/****************************************************************/
/* function logout()                                            */
/*                                                              */
/* Logs the user out and kills cookies                          */
/****************************************************************/
function logout() {
  global $login;
  setcookie("user","",time()-60*60*24*1);
  setcookie("pass","",time()-60*60*24*1);

  maintop("Logout",false);
  echo "Your are now logged out."
      ."<br><br>"
      ."<a href=".$adminfile."?op=home>Click here to Log in again</a>";
  mainbottom();
}


/****************************************************************/
/* function mainbottom()                                        */
/*                                                              */
/* Controls the bottom copyright.                               */
/****************************************************************/
function mainbottom() {
  echo "</table></table>\n"
      ."<table width=100%><tr><td align=right><font class=copyright>Copyright &copy 2003 - ".date('Y')." <a href=http://www.osfilemanager.com>osFileManager</a>, <a href=http://www.arzy.net>Arzy, LLC</a></font></table>\n"
      ."</table></table></body>\n"
      ."</html>\n";
  exit;
}


/****************************************************************/
/* function printerror()                                        */
/*                                                              */
/* Prints error onto screen                                     */
/* Recieves $error and prints it.                               */
/****************************************************************/
function printerror($error) {
  maintop("ERROR");
  echo "<font class=error>\n".$error."\n</font>";
  mainbottom();
}


/****************************************************************/
/* function switch()                                            */
/*                                                              */
/* Switches functions.                                          */
/* Recieves $op() and switches to it                            *.
/****************************************************************/
switch($op) {

    case "home":
	home();
	break;

    case "up":
	up();
	break;

    case "upload":
	upload($_FILES['upfile'], $_REQUEST['ndir']);
	break;

    case "del":
	del($_REQUEST['dename']);
	break;

    case "delete":
	delete($_REQUEST['dename']);
	break;

    case "edit":
	edit($_REQUEST['fename']);
	break;

    case "save":
	save($_REQUEST['ncontent'], $_REQUEST['fename']);
	break;

    case "cr":
	cr();
	break;

    case "create":
	create($_REQUEST['nfname'], $_REQUEST['isfolder'], $_REQUEST['ndir']);
	break;

    case "ren":
	ren($_REQUEST['file']);
	break;

    case "rename":
	renam($_REQUEST['rename'], $_REQUEST['nrename'], $folder);
	break;

    case "mov":
	mov($_REQUEST['file']);
	break;

    case "move":
	move($_REQUEST['file'], $_REQUEST['ndir'], $folder);
	break;

    case "viewframe":
	viewframe($_REQUEST['file']);
	break;

    case "viewtop":
	viewtop($_REQUEST['file']);
	break;

    case "printerror":
	printerror($error);
	break;

    case "logout":
	logout();
	break;

    default:
	home();
	break;
}
?>