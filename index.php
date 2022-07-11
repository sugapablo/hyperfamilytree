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
?>

<table class="logo">
	<tr>
		<td class="logo">
			<img src="images/logo.png" alt="logo"/><br/><br/>
			<a href="calendar.php"><img src="images/b-calendar.png" alt="calendar"/></a><br/><br/>
			<a href="print.php"><img src="images/b-print.png" alt="print"/></a>
		</td>
		<td class="logo">&nbsp;</td>
		<td class="logo">
		
			<table class="display">
				<tr>
					<td class="display">
					<?php echo get_people("person.php",0) ?>
					</td>
				</tr>
			
			</table>

			<div>&nbsp;</div>

			<table class="display">
				<tr>
					<td class="display">
					<?php echo stats(); ?>
					</td>
				</tr>	
			</table>
		
		</td>
	</tr>
</table>

<?php
require_once($path."includes/bottom.php");
?>
