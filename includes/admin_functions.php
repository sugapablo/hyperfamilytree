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

	//Admin persmissions:
	$permissions[1] = "ADD";
	$permissions[2] = "EDIT";
	$permissions[3] = "ADD/EDIT";
	$permissions[4] = "DELETE";
	$permissions[5] = "ADD/EDIT";
	$permissions[6] = "EDIT/DELETE";
	$permissions[7] = "ADD/EDIT/DELETE";

	function can_add() {
		if($_COOKIE['phpft_perms'] == "1" || $_COOKIE['phpft_perms'] == "3" || $_COOKIE['phpft_perms'] == "5" || $_COOKIE['phpft_perms'] == "7" || $_COOKIE['phpft_perms'] == "S")
			return true;
		else
			return false;
	}

	function can_edit() {
		if($_COOKIE['phpft_perms'] == "2" || $_COOKIE['phpft_perms'] == "3" || $_COOKIE['phpft_perms'] == "5"  || $_COOKIE['phpft_perms'] == "6" || $_COOKIE['phpft_perms'] == "7" || $_COOKIE['phpft_perms'] == "S")
			return true;
		else
			return false;
	}

	function can_delete() {
		if($_COOKIE['phpft_perms'] == "4" || $_COOKIE['phpft_perms'] == "6" || $_COOKIE['phpft_perms'] == "7" || $_COOKIE['phpft_perms'] == "S")
			return true;
		else
			return false;
	}

	
	function post2mysql($table,$postArray) {
		$sql = "INSERT INTO ".$table." (";
		$columns = "";
		$values = "";
		foreach($postArray as $key => $value) {
			$columns.= $key.",";
			$values.= "'".mysql_escape_string($value)."',";
		}
		$sql.= substr_replace($columns,'',-1,1).") VALUES (";
		$sql.= substr_replace($values,'',-1,1).")";
		mysql_query($sql);
	}

	function post2mysql_update($table,$postArray,$idname,$id) {
		$sql = "UPDATE ".$table." SET ";
		foreach($postArray as $key => $value) {
			$sql.= $key." = '".mysql_escape_string($value)."',";
		}
		$sql = substr_replace($sql,' ',-1,1);
		$sql.= "WHERE ".$idname." = '".$id."'";
		mysql_query($sql);
	}

	function admin_login() {
		global $prefix,$path,$siteURLpath;
		
		if($_COOKIE['phpft_admin']) 
			return $_COOKIE['phpft_perms'];
		elseif($_POST['username'] && $_POST['password']) {
			$user = mysql_escape_string($_POST['username']);
			$pass = mysql_escape_string($_POST['password']);
			
			$sql = "SELECT username,password,supreme,permissions FROM ".$prefix."_admin ";
			$sql.= "WHERE (username = '".$user."' AND password = '".$pass."') LIMIT 1";
			$res = mysql_query($sql);

			$string = "<table class=\"display\">
				<tr>
					<td class=\"display\">";

			if(mysql_num_rows($res)) {
				list($username,$password,$supreme,$perm) = mysql_fetch_row($res);
				if($supreme)
					$perm = "S";
				setcookie("phpft_admin",$username);
				setcookie("phpft_perms",$perm);
				$string.= "<p class=\"center\">Login successful.  Click <a href=\"index.php\">here</a> to continue.</p>";
			}
			else
				$string.= "<p class=\"center\">Login failed.  Incorrect username and/or password.</p>";

			$string.= "</td></tr></table>";
				
			require_once($path."includes/top.php");

			echo $string;

			require_once("../includes/bottom.php");
			die();
		}
		else {
			$string = "<table class=\"display\">
				<tr>
					<td class=\"display\">
						<p class=\"center\"><b>PHP Family Tree: Admin Login</b></p>
							<form method=\"post\" action=\"index.php\" id=\"admin_login\">
							<table class=\"admin\">
								<tr>
									<td class=\"admin_head\">Username</td>
									<td class=\"admin_body\"><input type=\"text\" maxlength=\"12\" name=\"username\" value=\"".$_POST['username']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\">Password</td>
									<td class=\"admin_body\"><input type=\"password\" maxlength=\"12\" name=\"password\" value=\"".$_POST['password']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\">&nbsp;</td>
									<td class=\"admin_body\"><input type=\"submit\" value=\"Submit\"/></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			      </table>";

			require_once($path."includes/top.php");

			echo $string;

			require_once("../includes/bottom.php");
			die();
		}
	}	


	function check_admin_install() {
		global $prefix,$path,$siteURLpath;

		$sql = "SELECT username,password FROM ".$prefix."_admin";
		$res = mysql_query($sql) or die(mysql_error());

		if(mysql_num_rows($res)) 
			return true;
		elseif($_POST['username'] && $_POST['password'] == $_POST['confirm'] && $_POST['email']) {
			unset($_POST['confirm']);
			post2mysql($prefix."_admin",$_POST);
			return true;
		}
		else {
			require_once($path."includes/top.php");
			echo "<table class=\"display\">
				<tr>
					<td class=\"display\">
						<p class=\"center\"><b>PHP Family Tree: Installation</b></p>";

			if($_POST['password'] != $_POST['confirm'])
				echo "<p class=\"center\"><b>Password and confirm password did not match.</b></p>";
						
			echo "			<p class=\"center\">Please set the administrator's username &amp; password.</p>
						
						<form method=\"post\" action=\"index.php?task=install_admin\" id=\"admin_install\">
							<table class=\"admin\">
								<tr>
									<td class=\"admin_head\">Username</td>
									<td class=\"admin_body\"><input type=\"text\" maxlength=\"12\" name=\"username\" value=\"".$_POST['username']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\">Password</td>
									<td class=\"admin_body\"><input type=\"password\" maxlength=\"12\" name=\"password\" value=\"".$_POST['password']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\">Confirm Password</td>
									<td class=\"admin_body\"><input type=\"password\" maxlength=\"12\" name=\"confirm\" value=\"".$_POST['confirm']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\">Email</td>
									<td class=\"admin_body\"><input type=\"text\" maxlength=\"40\" name=\"email\" value=\"".$_POST['email']."\"/></td>
								</tr><tr>
									<td class=\"admin_head\"><input type=\"hidden\" name=\"supreme\" value=\"1\"/>&nbsp;</td>
									<td class=\"admin_body\"><input type=\"submit\" value=\"Submit\"/></td>
								</tr>
							</table>
						</form>
						<p class=\"center\"><i>All fields required.</i></p>
					</td>
				</tr>
			      </table>";			
			
			require_once("../includes/bottom.php");
			die();
		}
	}	
?>
