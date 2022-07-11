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

$task = $_GET['task'];

if($task == "show") {

}
else {
	if($_COOKIE['phpft_perms'] == "S")
		$perms = "SUPREME (ADD/EDIT/DELETE/MAINTAIN ADMINS)";
	else
		$perms = $permissions[$_COOKIE['phpft_perms']];

	echo "<table class=\"display\">
		<tr>
			<td class=\"display\">".get_people("person.php",0)."</td>
		</tr>
	      </table>
	      
	      <div>&nbsp;</div>
	      
	      <table class=\"display\">
		<tr>
			<td class=\"display\">
				<p class=\"center\">
					<b>Admin Menu</b>
					<br/>
					Welcome, ".$_COOKIE['phpft_admin'].".  Your permissions are: ".$perms.".			
				</p>
			</td>
		</tr>
		<tr>
			<td class=\"display\">
				<div class=\"center\">
						<a href=\"../index.php\">Site Home</a>
				</div>
				<br/>
				<div class=\"center\">
						<a href=\"add_person.php\">Add New Person to Database</a>
				</div>
				<br/>
				<div class=\"center\">
						<a href=\"edit_admins.php\">View/Maintain Administrators</a>
				</div>
			</td>
		</tr>
	      </table>";
}
require_once("../includes/bottom.php");
?>
