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
require_once($path."includes/class.xform.php");

$task 	= $_GET['task'];
$id 	= intval(mysql_escape_string($_GET['id']));

echo "<p class=\"center\"><a href=\"index.php\">Admin Home</a> | <a href=\"person.php?id=".$id."\">Back to Person's View</a></p>";

// Update record
if($task == "people_action") {
	if(can_edit()) {
		post2mysql_update($prefix."_people",$_POST,"id",$id);
		echo "<p class=\"center\"><b>Record updated successfully.</b></p>";
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to edit people.</div>";
}
	
// Grab record and drop down lists
$sql = "SELECT * FROM ".$prefix."_people WHERE (id = ".$id.") LIMIT 1";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$sql = "SELECT id, CONCAT(last_name,', ',' (',maiden_name,') ',first_name,' ',middle_name) AS name FROM ".$prefix."_people ";
$sql.= "ORDER BY last_name,first_name";
$res_mother = mysql_query($sql);

$sql = "SELECT id, CONCAT(last_name,', ',' (',maiden_name,') ',first_name,' ',middle_name) AS name FROM ".$prefix."_people ";
$sql.= "ORDER BY last_name,first_name";
$res_father = mysql_query($sql);

// Begin display
echo "<table class=\"display\"><tr><td class=\"display\">";

$form = new Xform;
$form -> setForm("post","edit_person.php?task=people_action&amp;id=".$id,"person","person_head","person_body");
$form -> fText("First Name","first_name",30,20,$row['first_name']);
$form -> fText("Middle Name","middle_name",30,20,$row['middle_name']);
$form -> fText("Last Name","last_name",30,30,$row['last_name']);
$form -> fText("Former Last Name","former_last_name",30,30,$row['former_last_name']);
$form -> fText("Maiden Name","maiden_name",30,30,$row['maiden_name']);
$form -> fText("Gender","gender",1,1,$row['gender']);
$form -> fText("Birth Date (YYYY-MM-DD)","birthdate",10,10,$row['birthdate']);
$form -> fText("Birthplace","birthplace",30,40,$row['birthplace']);
$form -> fText("Died (YYYY-MM-DD)","died",10,10,$row['died']);
$form -> fText("Resting Place","resting_place",30,64,$row['resting_place']);
$form -> fSelectDB("Father","father",$res_father,$row['father']);
$form -> fSelectDB("Mother","mother",$res_mother,$row['mother']);
$form -> fTextarea("About The Person","about",30,10,$row['about']);
$form -> fRadio("Adopted","yes","adopted","1",0,1,0);
$form -> fRadio("Adopted","no","adopted","0",1,0,1);
$form -> fSubmit("Submit");
$form -> echoForm();

echo "</td></tr></table>";

require_once("../includes/bottom.php");
?>
