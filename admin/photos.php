<?php
/*
PHP Family Tree
Copyright (C) 2005 Russ Schneider

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Contact Author: russ@sugapablo.com :: homepage -> https://www.sugapablo.com
*/

$admin_header = ": Administration";
require_once("../config.php");
require_once($path."includes/admin_functions.php");
require_once($path."includes/database.php");

check_admin_install();
admin_login();

require_once($path."includes/top.php");

$task 	= $_GET['task'];
$id 	= intval(mysql_escape_string($_GET['id']));
$i	= 1;

echo "<p class=\"center\"><a href=\"index.php\">Admin Home</a></p>";

if($task == "upload") {
	if(can_add()) {
		$fileName 	= mysql_escape_string($_FILES['userfile']['name']);
		$caption 	= mysql_escape_string($_POST['caption']);
		$year 		= mysql_escape_string($_POST['year']);

		if(is_uploaded_file($_FILES['userfile']['tmp_name']) && in_array($_FILES['userfile']['type'],$photo_type)) {
			$copy_to = $path."photos/".$fileName;
			copy($_FILES['userfile']['tmp_name'], $copy_to);

			$sql = "INSERT INTO ".$prefix."_photos (filename,caption,year,person) VALUES (";
			$sql.= "'".$fileName."','".$caption."','".$year."',".$id.")";
			mysql_query($sql);
		}
		else
			echo "<p class=\"center\"><b>Upload Failed.</b></p>";
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to add photos.</div>";
}

// Delete photos
if($_GET['delphoto']) {
	if(can_delete()) {
		$delphoto = intval(mysql_escape_string($_GET['delphoto']));
	
		$sql = "SELECT filename FROM ".$prefix."_photos WHERE (id = ".$delphoto.") LIMIT 1";
		$res = mysql_query($sql);
		list($del_filename) = mysql_fetch_row($res);
	
		if(file_exists($path."photos/".$del_filename)) {
			unlink($path."photos/".$del_filename);
		}

		mysql_query("DELETE FROM ".$prefix."_photos WHERE (id = ".$delphoto.")");
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to delete photos.</div>";

}

// Grab record and drop down lists
$sql = "SELECT first_name,last_name FROM ".$prefix."_people WHERE (id = ".$id.") LIMIT 1";
$res = mysql_query($sql);
list($fname,$lname) = mysql_fetch_row($res);

$sql = "SELECT id,caption,year,filename FROM ".$prefix."_photos WHERE (person = ".$id.") ORDER BY year,filename";
$res = mysql_query($sql);

// Begin display
echo "<table class=\"display\"><tr><td class=\"display\">";

echo "	<p class=\"center\"><a href=\"person.php?id=".$id."\"><b>".$fname." ".$lname."</b></a></p>
		<table class=\"photos\">";

while(list($photoid,$caption,$year,$filename) = mysql_fetch_row($res)) {
	echo "<tr>
		<td class=\"photos_head\">Photo #".$i."</td>
		<td class=\"photos_body\"><a href=\"../photo.php?id=".$id."&amp;back=admin%2Fphotos.php&amp;photo=".$filename."\">".$filename."</a></td>
		<td class=\"photos_body\">".$caption."</td>
		<td class=\"photos_body\">".$year."</td>
		<td class=\"photos_body\"><a href=\"photos.php?id=".$id."&amp;delphoto=".$photoid."\">Delete</a></td>
	      </tr>";
	$i++;
}

echo "		</table>";
echo "		<div>&nbsp;</div>";

echo "</td></tr></table>";

echo "<div>&nbsp;</div>";

// Begin upload form
echo "<table class=\"display\"><tr><td class=\"display\">";

echo "	<p class=\"center\"><b>Upload Photos</b></p>
	<form enctype=\"multipart/form-data\" action=\"photos.php?id=".$id."&amp;task=upload\" method=\"post\">
	<table class=\"photos\">
		<tr>
			<td class=\"photos_head\">Select File to Upload</td>
			<td class=\"photos_body\"><input name=\"userfile\" type=\"file\"/></td>
		</tr><tr>
			<td class=\"photos_head\">Caption</td>
			<td class=\"photos_body\"><input name=\"caption\" type=\"text\" maxlength=\"255\" size=\"45\"/></td>
		</tr><tr>
			<td class=\"photos_head\">Year (YYYY)</td>
			<td class=\"photos_body\"><input name=\"year\" type=\"text\" maxlength=\"4\" size=\"4\"/></td>
		</tr><tr>
			<td class=\"photos_head\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\"/>&nbsp;</td>
			<td class=\"photos_body\"><input type=\"submit\" value=\"Upload\"/></td>
		</tr>
	</table>
	</form>";


if(count($photo_type)) {
	echo "<p class=\"center\">Acceptable photo file types:<br/>";
	foreach($photo_type AS $type) 
		echo $type."<br/>";
	echo "</p>";
}

echo "<div>&nbsp;</div>";

echo "</td></tr></table>";


require_once("../includes/bottom.php");
?>
