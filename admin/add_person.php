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

echo "<p class=\"center\"><a href=\"index.php\">Admin Home</a></p>";

// Update record
if($task == "people_action") {
	post2mysql($prefix."_people",$_POST);
	$id = mysql_insert_id();
	echo "<p class=\"center\"><b>Record entered successfully.</b>   <a href=\"person.php?id=".$id."\">View Person's Record</a></p>";
	require_once("../includes/bottom.php");
	die();
}
	
// Grab drop down lists
$sql = "SELECT id, CONCAT(last_name,', ',' (',maiden_name,') ',first_name,' ',middle_name) AS name FROM ".$prefix."_people ";
$sql.= "ORDER BY last_name,first_name";
$res_mother = mysql_query($sql);

$sql = "SELECT id, CONCAT(last_name,', ',' (',maiden_name,') ',first_name,' ',middle_name) AS name FROM ".$prefix."_people ";
$sql.= "ORDER BY last_name,first_name";
$res_father = mysql_query($sql);

// Begin display
echo "<table class=\"display\"><tr><td class=\"display\">";

if(can_add()) {
	$form = new Xform;
	$form -> setForm("post","add_person.php?task=people_action","person","person_head","person_body");
	$form -> fText("First Name","first_name",30,20);
	$form -> fText("Middle Name","middle_name",30,20);
	$form -> fText("Last Name","last_name",30,30);
	$form -> fText("Former Last Name","former_last_name",30,30);
	$form -> fText("Maiden Name","maiden_name",30,30);
	$form -> fText("Gender","gender",1,1);
	$form -> fText("Birth Date (YYYY-MM-DD)","birthdate",10,10);
	$form -> fText("Birthplace","birthplace",30,40);
	$form -> fText("Died (YYYY-MM-DD)","died",10,10);
	$form -> fText("Resting Place","resting_place",30,64);
	$form -> fSelectDB("Father","father",$res_father);
	$form -> fSelectDB("Mother","mother",$res_mother);
	$form -> fTextarea("About The Person","about",30,10);
	$form -> fRadio("Adopted","yes","adopted","1",0,1,0);
	$form -> fRadio("Adopted","no","adopted","0",1,0,1);
	$form -> fSubmit("Submit");
	$form -> echoForm();
}
else 
	echo "<div class=\"center\">Sorry, you don't have permision to add new people.</div>";

echo "</td></tr></table>";

require_once("../includes/bottom.php");
?>
