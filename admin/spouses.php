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
$delid 	= intval(mysql_escape_string($_GET['delid']));

echo "<p class=\"center\"><a href=\"index.php\">Admin Home</a></p>";

// Update record
if($task == "spouse_action") {
	if(can_edit()) 
		post2mysql($prefix."_spouses",$_POST);
	else
		echo "<div class=\"center\">Sorry, you don't have permission to edit links.</div>";
}

// Delete link
if($delid) {
	if(can_delete())
		mysql_query("DELETE FROM ".$prefix."_spouses WHERE (id = ".$delid.")");
	else
		echo "<div class=\"center\">Sorry, you don't have permission to delete links.</div>";
}
	
// Grab record and drop down lists
$sql = "SELECT first_name,last_name FROM ".$prefix."_people WHERE (id = ".$id.") LIMIT 1";
$res = mysql_query($sql);
list($fname,$lname) = mysql_fetch_row($res);

$sql = "SELECT A.id,A.person1,A.person2,A.married,A.divorced, ";
$sql.= "B.first_name AS fname1,B.last_name AS lname1, B.gender AS gender1, ";
$sql.= "C.first_name AS fname2,C.last_name AS lname2, C.gender AS gender2 ";
$sql.= "FROM ".$prefix."_spouses A ";
$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.")";
$res = mysql_query($sql);

// Grab drop down lists
$sql = "SELECT id, CONCAT(last_name,', ',first_name) AS name FROM phpft_people ";
$sql.= "ORDER BY last_name,first_name";
$res_spouse = mysql_query($sql);

// Begin display
echo "<table class=\"display\"><tr><td class=\"display\">

	<p class=\"center\"><a href=\"person.php?id=".$id."\"><b>".$fname." ".$lname."</b></a></p>
		<table class=\"spouses\">";

while(list($rowid,$person1,$person2,$married,$divorced,$fname1,$lname1,$gender1,$fname2,$lname2,$gender2) = mysql_fetch_row($res)) {
	// assign person who isn't the person
	if($person1 == $id) {
		$spouse_id     = $person2;
		$spouse_fname  = $fname2;
		$spouse_lname  = $lname2;
	}
	else {
		$spouse_id     = $person1;
		$spouse_fname  = $fname1;
		$spouse_lname  = $lname1;
	}
	
	echo "<tr>
			<td class=\"spouses_head\"><a href=\"person.php?id=".$spouse_id."\">".$spouse_fname."&nbsp;".$spouse_lname."</a></td>
			<td class=\"spouses_body\">married: ".$married."</td>
			<td class=\"spouses_body\">divorced: ".$divorced."</td>
			<td class=\"spouses_body\">
				<a href=\"spouses.php?id=".$id."&amp;delid=".$rowid."\">Delete This Link</a>
			</td>
		   </tr>";
}
			
echo "</table>";
echo "<div>&nbsp;</div>";

echo "</td></tr></table>";

echo "<div>&nbsp;</div>";

echo "<table class=\"display\"><tr><td class=\"display\">";
$form = new Xform;
$form -> setForm("post","spouses.php?task=spouse_action&amp;id=".$id,"spouses","spouses_head","spouses_body");
$form -> fSelectDB("Link New Spouse","person2",$res_spouse);
$form -> fText("Marriage Date (YYYY-MM-DD)","married",10,10);
$form -> fText("Divorce Date (YYYY-MM-DD)","divorced",10,10);
$form -> fHidden("person1",$id);
$form -> fSubmit("Submit");
$form -> echoForm();

echo "</td></tr></table>";

require_once("../includes/bottom.php");
?>
