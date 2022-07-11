<?php 
/*
HyperFamilyTree
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

require_once($path."includes/database.php");
require_once($path."includes/functions.php");

if($site_title)
	$site_title.= " :: ";
	
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"https://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
	<title>".$site_title."HyperFamilyTree :: Version 0.87 BETA</title>
	<link rel=\"stylesheet\" media=\"screen\" href=\"".$siteURLpath."includes/css/".$stylesheet."\" title=\"main\"/>
</head>
<body>";
?>

<div class="header">
	<span class="header"><?php echo $site_title; ?>HyperFamilyTree<?php echo $admin_header;?></span>
</div>

<div>&nbsp;</div>


