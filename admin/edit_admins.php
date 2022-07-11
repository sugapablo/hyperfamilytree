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

echo "<p class=\"center\"><a href=\"javascript:history.go(-1);\">Back</a> | <a href=\"index.php\">Admin Home</a></p>";

echo "<table class=\"display\"><tr><td class=\"display\">";

// Add record
if($task == "add_action") {
	if($_COOKIE['phpft_perms'] == "S") {
		post2mysql($prefix."_admin",$_POST);
		echo "<p class=\"center\"><b>Record added successfully.</b></p>";
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to maintain administrators.</div>";
}

// Update record
if($task == "edit_action") {
	if($_COOKIE['phpft_perms'] != "S") {
		post2mysql_update($prefix."_admin",$_POST,"id",$id);
		echo "<p class=\"center\"><b>Record updated successfully.</b></p>";
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to maintain administrators.</div>";
}

// Delete record
if($task == "del") {
	if($_COOKIE['phpft_perms'] != "S") {
		mysql_query("DELETE FROM ".$prefix."_admin WHERE (id = ".$id.") LIMIT 1");
		echo "<p class=\"center\"><b>Record deleted successfully.</b></p>";
	}
	else
		echo "<div class=\"center\">Sorry, you don't have permission to maintain administrators.</div>";
}

if($task == "add") {
	$sql = "SELECT id,first_name,last_name FROM ".$prefix."_people ORDER BY last_name,first_name";
	$res = mysql_query($sql);

	echo "	<p class=\"center\"><b>Add New Admin</b></p>
		<p class=\"center\">Note: \"Supreme\" administrators have the ability to add, edit and delete other administrator accounts, even yours.</p>
		
		</td></tr></table>

		<div>&nbsp;</div>

		<table class=\"display\"><tr><td class=\"display\">

		<form method=\"post\" action=\"edit_admins.php?task=add_action&amp;id=".$id."\" id=\"edit\">
			<table class=\"admin\">
				<tr>
					<td class=\"admin_head\">Username</td>
					<td class=\"admin_body\"><input type=\"text\" maxlength=\"12\" name=\"username\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Password</td>
					<td class=\"admin_body\"><input type=\"password\" maxlength=\"12\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Email</td>
					<td class=\"admin_body\"><input type=\"text\" maxlength=\"40\" name=\"email\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Family Member</td>
					<td class=\"admin_body\">
						<select name=\"person\">
							<option value=\"0\">Select</option>";
	while(list($id,$first_name,$last_name) = mysql_fetch_row($res)) {
		echo "<option value=\"".$id."\">".$last_name.", ".$first_name."</option>";
	}			
	echo "					</select>
					</td>
				</tr><tr>
					<td class=\"admin_head\">Permissions</td>
					<td class=\"admin_body\">
						<select name=\"permissions\">";
	foreach($permissions AS $key=>$value) {
		echo "<option value=\"".$key."\">".$value."</option>";
	}						
	echo "					</select>
					</td>
				</tr><tr>
					<td class=\"admin_head\">Supreme</td>
					<td class=\"admin_body\">
						<input type=\"checkbox\" name=\"supreme\" value=\"1\"/>
					</td>
				</tr><tr>
					<td class=\"admin_head\">&nbsp;</td>
					<td class=\"admin_body\"><input type=\"submit\" value=\"Submit\"/></td>
				</tr>
			</table>
		</form>";

}
elseif($task == "edit") {
	$sql = "SELECT username,password,email,permissions,supreme,person FROM ".$prefix."_admin WHERE (id = ".$id.") LIMIT 1";
	$res = mysql_query($sql);

	list($username,$password,$email,$perm,$supreme,$person) = mysql_fetch_row($res);

	$sql = "SELECT id,first_name,last_name FROM ".$prefix."_people ORDER BY last_name,first_name";
	$res = mysql_query($sql);
	
	if($supreme == 1) $supremeYN = " checked=\"checked\"";
	else $supremeYN = "";

	echo "	<p class=\"center\"><b>Edit Admin</b></p>
		<p class=\"center\">Note: \"Supreme\" administrators have the ability to add, edit and delete other administrator accounts, even yours.</p>

		</td></tr></table>

		<div>&nbsp;</div>

		<table class=\"display\"><tr><td class=\"display\">
		<form method=\"post\" action=\"edit_admins.php?task=edit_action&amp;id=".$id."\" id=\"edit\">
			<table class=\"admin\">
				<tr>
					<td class=\"admin_head\">Username</td>
					<td class=\"admin_body\"><input type=\"text\" maxlength=\"12\" name=\"username\" value=\"".$username."\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Password</td>
					<td class=\"admin_body\"><input type=\"password\" maxlength=\"12\" name=\"password\" value=\"".$password."\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Email</td>
					<td class=\"admin_body\"><input type=\"text\" maxlength=\"40\" name=\"email\" value=\"".$email."\"/></td>
				</tr><tr>
					<td class=\"admin_head\">Family Member</td>
					<td class=\"admin_body\">
						<select name=\"person\">
							<option value=\"0\">Select</option>";
	while(list($id,$first_name,$last_name) = mysql_fetch_row($res)) {
		if($id == $person) $selected = " selected=\"selected\"";
		else $selected = "";

		echo "<option value=\"".$id."\"".$selected.">".$last_name.", ".$first_name."</option>";
	}			
	echo "					</select>
					</td>
				</tr><tr>
					<td class=\"admin_head\">Permissions</td>
					<td class=\"admin_body\">
						<select name=\"permissions\">";
	foreach($permissions AS $key=>$value) {
		if($key == $perm) $selected = " selected=\"selected\"";
		else $selected = "";
		
		echo "<option value=\"".$key."\"".$selected.">".$value."</option>";
	}						
	echo "					</select>
					</td>
				</tr><tr>
					<td class=\"admin_head\">Supreme (automatic ADD/EDIT/DELETE)</td>
					<td class=\"admin_body\">
						<input type=\"checkbox\" name=\"supreme\" value=\"1\"".$supremeYN."/>
					</td>
				</tr><tr>
					<td class=\"admin_head\">&nbsp;</td>
					<td class=\"admin_body\"><input type=\"submit\" value=\"Submit\"/></td>
				</tr>
			</table>
		</form>";

}

else {
	$sql = "SELECT id,username,email,supreme,permissions,person FROM ".$prefix."_admin ORDER BY username";
	$res = mysql_query($sql);

	echo "	<p class=\"center\">
			<b>Maintain Admins</b>";
	
	if($_COOKIE['phpft_perms'] == "S") {
		echo "	<br/>
			<a href=\"edit_admins.php?task=add\">Add New Admin</a>";
	}
	
	echo "	</p>
		
		<table class=\"admin\">
			<tr>
				<td class=\"admin_head\">Username</td>
				<td class=\"admin_head\">Email</td>
				<td class=\"admin_head\">Permissions</td>
				<td class=\"admin_head\">&nbsp;</td>
			</tr>";

	while(list($id,$username,$email,$supreme,$perm,$person) = mysql_fetch_row($res)) {
		if($supreme)
			$perms = "SUPREME (ADD/EDIT/DELETE/MAINTAIN ADMINS)";
		else
			$perms = $permissions[$perm];
			
		echo "<tr>
			<td class=\"admin_body\"><a href=\"person.php?id=".$person."\">".$username."</a></td>
			<td class=\"admin_body\"><a href=\"mailto:".$email."\">".$email."</a></td>
			<td class=\"admin_body\">".$perms."</td>
			<td class=\"admin_body\">";
		if($_COOKIE['phpft_perms'] == "S") {
			echo "	<a href=\"edit_admins.php?id=".$id."&amp;task=edit\">Edit</a> | 
				<a href=\"edit_admins.php?id=".$id."&amp;task=del\">Delete</a>";
		}
		else 
			echo "Edit | Delete";		
		echo "	</td>
		      </tr>";
	
	}
	echo "		</table>";
}

echo "</td></tr></table>";


require_once("../includes/bottom.php");
?>
