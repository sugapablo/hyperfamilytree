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

require_once("config.php");
require_once($path."includes/top.php");

$id 	= intval(mysql_escape_string($_GET['id']));
$photo 	= mysql_escape_string($_GET['photo']);

if($_GET['back'])
	$back = $_GET['back'];
else
	$back = "person.php";
?>

<p class="center"><a href="javascript:history.go(-1);"><img src="images/b-back.png" alt="print"/></a> <a href="index.php"><img src="images/b-home.png" alt="print"/></a></p>

<table class="display">
	<tr>
		<td class="display">
			<img class="center" src="photos/<?php echo $photo; ?>" alt="<?php echo $photo; ?>"/>
		</td>
	</tr>

</table>

<?php
require_once($path."includes/bottom.php");
?>
