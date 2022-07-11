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
require_once($path."includes/database.php");
require_once($path."includes/class.xml_tree_desc.php");
require_once($path."includes/class.xml_tree_ancs.php");

$id = 	intval(mysql_escape_string($_GET['id']));

header("Content-Type: text/xml");


echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n\n";
echo "<tree>\n\n";

$tree = new xml_tree_ancs;
$tree->show($id,1);
$tree = new xml_tree_desc;
$tree->show($id,1);

echo "</tree>\n";
?>
