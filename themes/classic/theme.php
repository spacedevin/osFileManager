<?php

$tbcolor1 = "#E5E5E5";
$tbcolor2 = "#EEEEEE";
$tbcolor3 = "#FFCC99";
$tbcolor4 = "#CCCCFF";
$bgcolor1 = "#E8ECF5";
$bgcolor2 = "#a6a6a6";
$bgcolor3 = "#ffffff";
$txtcolor1 = "#000000";
$txtcolor2 = "#003399";

// Image Map
$IMG_CHECK = "themes/$theme/images/icons/accept.png";
$IMG_RENAME = "themes/$theme/images/icons/font.png";
$IMG_GET = "themes/$theme/images/icons/drive_go.png";
$IMG_EDIT = "themes/$theme/images/icons/page_edit.png";
$IMG_DELETE = "themes/$theme/images/icons/delete.png";
$IMG_MOVE = "themes/$theme/images/icons/folder_go.png";
$IMG_CHMOD = "themes/$theme/images/icons/lock.png";
$IMG_ACTION = "themes/$theme/images/icons/bullet_arrow_down.png";

$IMG_MIME_FOLDER = "themes/$theme/images/mime/folder.png";
$IMG_MIME_BINARY = "themes/$theme/images/mime/page_white_gear.png";
$IMG_MIME_AUDIO = "themes/$theme/images/mime/page_white_cd.png";
$IMG_MIME_VIDEO = "themes/$theme/images/mime/page_white_camera.png";
$IMG_MIME_IMAGE = "themes/$theme/images/mime/page_white_picture.png";
$IMG_MIME_TEXT = "themes/$theme/images/mime/page_white_text.png";
$IMG_MIME_UNKNOWN = "themes/$theme/images/mime/page_white.png";


function page_header($title,$show = true) {
  global $HEADER_CHARACTERSET, $permmakeuser, $permedituser, $permdeleteuser, $permbrowse, $permupload, $permcreate, $permuser, $permadmin, $d, $darkgrey, $lkcolor1, $lkcolor2, $lkcolor3, $lkcolor4, $background, $lightgrey, $incolor2, $incolor1, $black, $white, $user, $pass, $extraheaders, $sitetitle, $bgcolor1, $bgcolor2, $bgcolor3, $txtcolor1, $txtcolor2, $tbcolor4, $IMG_ACTION, $IMG_CHMOD, $IMG_DELETE, $IMG_MOVE;
  global $extraheaders, $sitetitle, $lastsess, $login, $viewing, $iftop, $bgcolor1, $bgcolor2, $bgcolor3, $txtcolor1, $txtcolor2, $user, $pass, $password, $debug, $issuper;
  global $adminfile;

  echo "<html>\n<head>\n"
      ."<title>$sitetitle :: $title �   Powerd by Arz FileManager 2.0 (Libra)</title>\n"
      ."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$HEADER_CHARACTERSET\">\n"
      ."</head>\n"
      ."<body bgcolor=\"#ffffff\">\n"
      ."<style>\n"
      ."td { font-size : 80%;font-family : tahoma;color: $txtcolor1;}\n"
      ."A:visited {color: \"$txtcolor2\";text-decoration: underline;}\n"
      ."A:hover {color: \"$txtcolor1\";text-decoration: underline;}\n"
      ."A:link {color: \"$txtcolor2\";text-decoration: underline;}\n"
      ."A:active {color: \"$bgcolor2\";text-decoration: underline;}\n"
      ."BODY {color: $txtcolor1; FONT-SIZE: 10pt; FONT-FAMILY: Verdana, Tahoma, Arial, Helvetica, sans-serif; MARGIN: 0px 0px 10px; BACKGROUND-COLOR: $bgcolor1;\n"
      ."                      SCROLLBAR-BASE-COLOR: #5B628C; \n"
      ."                      MARGIN: 0px; SCROLLBAR-HIGHLIGHT-COLOR: #e0e5ff; \n"
      ."                      SCROLLBAR-SHADOW-COLOR: #e0e5ff; SCROLLBAR-3DLIGHT-COLOR: #5B628C; \n"
      ."                      SCROLLBAR-ARROW-COLOR: #e0e5ff; SCROLLBAR-TRACK-COLOR: #e0e5ff; \n"
      ."                      SCROLLBAR-DARKSHADOW-COLOR: #e0e5ff;}\n"
      ."SELECT,TEXTAREA      {FONT-SIZE: 8pt; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; \n"
      ."                      BORDER-RIGHT: 1px solid; BORDER-TOP: 1px solid; BORDER-LEFT: 1px solid; \n"
      ."                      BORDER-BOTTOM: 1px solid; BACKGROUND: #EFF1F3; COLOR: #000000; spacing: 0; \n"
      ."                      BORDER-RIGHT-COLOR: #000000; BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; \n"
      ."                      BORDER-BOTTOM-COLOR: #000000; SCROLLBAR-BASE-COLOR: #5B628C; \n"
      ."                      MARGIN: 0px; SCROLLBAR-HIGHLIGHT-COLOR: #e0e5ff; \n"
      ."                      SCROLLBAR-SHADOW-COLOR: #e0e5ff; SCROLLBAR-3DLIGHT-COLOR: #5B628C; \n"
      ."                      SCROLLBAR-ARROW-COLOR: #e0e5ff; SCROLLBAR-TRACK-COLOR: #e0e5ff; \n"
      ."                      SCROLLBAR-DARKSHADOW-COLOR: #e0e5ff;}\n"
      ."INPUT                {border: 1px solid #000000; spacing: 0; MARGIN: 0px 0px 0px 0px; margin-right: 0px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px;}\n"

      ."TEXTAREA             {FONT-FAMILY: Lucida Console, Courier New, Courier;}\n"
      .".title {FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #000000; TEXT-ALIGN: center; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif}\n"
      .".subtitle {FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #FFFFFF; TEXT-ALIGN: center; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif}\n"
      .".theader {FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #FFFFFF; TEXT-ALIGN: center; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif}\n"
      .".copyright {FONT-SIZE: 8pt; COLOR: #000000; TEXT-ALIGN: left}\n"
      .".error {FONT-SIZE: 10pt; COLOR: #AA2222; TEXT-ALIGN: left; FONT-WEIGHT: bold;}\n"
      .".errorbox {BACKGROUND: #AA2222; }\n"
      .".ok {FONT-SIZE: 10pt; COLOR: #22AA22; TEXT-ALIGN: left; FONT-WEIGHT: bold; }\n"
      .".space {FONT-SIZE: 7pt; COLOR: #000000;}\n"
      .".barnormal {BACKGROUND-COLOR: #0bb60f;}\n"
      .".barwarning {BACKGROUND-COLOR: #f6ed00;}\n"
      .".barerror {BACKGROUND-COLOR: #d70000;}\n"
      .".txtinput {background: rgb(255, 255, 255) url('themes/classic/images/txtbar.jpg'); FONT-SIZE: 8pt; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; \n"
      ."                      BORDER-RIGHT: 1px solid; BORDER-TOP: 1px solid; BORDER-LEFT: 1px solid; \n"
      ."                      BORDER-BOTTOM: 1px solid; COLOR: #000000; spacing: 0; \n"
      ."                      BORDER-RIGHT-COLOR: #000000; BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; \n"
      ."                      BORDER-BOTTOM-COLOR: #000000; MARGIN: 0px; }\n"
      .".button {BACKGROUND: #EFF1F3; FONT-SIZE: 8pt; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; \n"
      ."                      BORDER-RIGHT: 1px solid; BORDER-TOP: 1px solid; BORDER-LEFT: 1px solid; \n"
      ."                      BORDER-BOTTOM: 1px solid; COLOR: #000000; spacing: 0; \n"
      ."                      BORDER-RIGHT-COLOR: #000000; BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; \n"
      ."                      BORDER-BOTTOM-COLOR: #000000; MARGIN: 0px; }\n"
      .".titlebar1         { COLOR: #FFFFFF; background: url(\"themes/classic/images/titlebar1.jpg\") repeat-x top left; }\n"
      .".titlebar2         { COLOR: #FFFFFF; background: url(\"themes/classic/images/titlebar2.jpg\") repeat-x top left; }\n"
      .".action            { background: rgb(255, 255, 255) url(\"".$IMG_ACTION."\") no-repeat scroll 0px 0px; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; padding-left: 18px; height: 15px; } \n"
      .".actiondelete      { background: rgb(255, 255, 255) url(\"".$IMG_DELETE."\") no-repeat scroll 0px 0px; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; padding-left: 18px; height: 15px; }\n"
      .".actionmove        { background: rgb(255, 255, 255) url(\"".$IMG_MOVE."\") no-repeat scroll 0px 0px; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; padding-left: 18px; height: 15px; }\n"
      .".actionchmod       { background: rgb(255, 255, 255) url(\"".$IMG_CHMOD."\") no-repeat scroll 0px 0px; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; padding-left: 18px; height: 15px; }\n"
      ."</style>\n\n"
      .$extraheaders;

  echo "<table cellpadding=2 cellspacing=2 bgcolor=$bgcolor1 align=center><tr><td>\n"
      ."<table cellpadding=1 cellspacing=1 bgcolor=$bgcolor2><tr><td>\n"
      ."<table cellpadding=0 cellspacing=0 bgcolor=$bgcolor1><tr><td>\n"
      ."<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
      ."<tr><td align=\"left\" height=120 width=648 background=themes/classic/images/logo.jpg valign=bottom align=left>\n";

  if ($show) {
    if ($permbrowse == 1) echo "<a href=\"".$adminfile."?p=home&d=$d\" $iftop class=theader><font class=theader>Browse</font></a>\n";
    if ($permupload == 1) echo "<img src=pixel.gif width=7 height=1><a href=\"?p=up&d=$d\" $iftop class=theader><font class=theader>Upload</font></a>\n";
    if ($permcreate == 1) echo "<img src=pixel.gif width=7 height=1><a href=\"?p=cr&d=$d\" $iftop class=theader><font class=theader>Create</font></a>\n";
    if ($permuser == 1) echo "<img src=pixel.gif width=7 height=1><a href=\"?p=user&d=$d\" $iftop class=theader><font class=theader>User CP</font></a>\n";
    if ($permadmin == 1 || $permmakeuser == 1 || $permedituser == 1 || $permdeleteuser == 1) echo "<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?p=super\" $iftop class=theader><font class=theader>Admin CP</font></a>\n";
    echo "<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?p=logout\" $iftop class=theader><font class=theader>Logout</font></a>\n";
  }
  echo "</table>\n";
}


function page_footer() {
  global $nobar, $user;
  /* keep this footer or you are just a big meaning :( */
  if ($nobar != 1) echo "<tr class=titlebar2 align=right><td class=titlebar2 align=right height=15>\n"; if ($user) showdiskspace();
  echo "</table></table><tr><td align=right><font class=copyright>Powerd by <a href=http://www.osfilemanager.com/>osFileManager</a><br>&copy; 2003-".date("Y")." <a href=http://www.arzy.net/>Arzy LLC</a></font></table>\n"
      ."</body>\n"
      ."</html>\n";
  die();
}

function opentitle($title) {
  global $tbcolor4;
  echo "<table cellpadding=1 cellspacing=0 height=20 bgcolor=$tbcolor4 width=100% class=titlebar2 height=20>\n"
      ."<tr><td width=20><img src=images/pixel.gif width=20 height=1> <td width=100%>\n"
      ."<font class=subtitle>$title</font>\n"
      ."</table>\n";  
}


function opensubtitle($title) {
  global $bgcolor3, $black;
  echo "<table cellpadding=1 cellspacing=0 height=20 bgcolor=$bgcolor3>\n"
      ."<tr><td colspan=2 bgcolor=$black> </td></tr>\n"
      ."<tr><td width=16> <td width=100%>\n"
      ."<font class=subtitle2>$title</font>"
      ."<tr><td colspan=2 bgcolor=$black> </td></tr>\n"
      ."</table>\n";
}

function opentable($width) {
  global $bgcolor3, $white;
  echo "<table cellpadding=1 cellspacing=1 width=$width bgcolor=$bgcolor3><tr><td>\n"
      ."<table cellpadding=0 cellspacing=0 width=$width bgcolor=$bgcolor3><tr><td>\n";
}

function closetable() {
  echo "</table></table>\n";
}
?>