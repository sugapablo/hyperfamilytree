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

class xml_tree_desc {
	
	function child_node($id,$first_name,$last_name,$maiden_name,$gender) {
		if($maiden_name)
			$maiden_name = "(".$maiden_name.") ";
			
		$string.= "		<leaf id=\"".$id."\">".htmlspecialchars($first_name)." ".htmlspecialchars($maiden_name).htmlspecialchars($last_name)."</leaf>\n";
		$string.= $this->spouse_names($id);		

		return $string;
	}
	
	function get_children($id,$level) {
		global $prefix;
		$go = 0;
		
		$level++;
		
		$sql = "SELECT id,first_name,last_name,maiden_name,gender FROM ".$prefix."_people ";
		$sql.= "WHERE (father = ".$id." OR mother = ".$id.") ORDER BY birthdate";

		$res = mysql_query($sql) or die(mysql_error());
		

		while(list($child,$first_name,$last_name,$maiden_name,$gender) = mysql_fetch_row($res)) {
			if($level == 1)
				$class = "";
			elseif($level == 2)
				$class = "grand";
			elseif($level == 3)
				$class = "great-grand";
			else
				$class = "great(".($level - 2).")-grand";

			if($gender == "M")
				$class.= "son";
			elseif($gender == "F")
				$class.= "daughter";

			if($go) 
				$string.= "</branch>\n<branch id=\"".$class."\">\n";
			else
				$string = "<branch id=\"".$class."\">\n";

			$string.= $this->child_node($child,$first_name,$last_name,$maiden_name,$gender);
			$go++;
			$string.= $this->get_children($child,$level);
		}

		$string.= "	</branch>\n";

		if($go)
			return $string;
	}
		

	function show($id,$tree = 0) {
		global $prefix;

		$string = "";
		
		$res = mysql_query("SELECT first_name,last_name,maiden_name FROM ".$prefix."_people WHERE (id = ".$id.")");
		list($first_name,$last_name) = mysql_fetch_row($res);

		if($maiden_name)
			$maiden_name = "(".$maiden_name.") ";

		if(!$tree)
			$string.= "<branch id=\"person\">\n";
		$string.= "		<leaf id=\"".$id."\">".htmlspecialchars($first_name)." ".htmlspecialchars($maiden_name).htmlspecialchars($last_name)."</leaf>\n";
		$string.= $this->spouse_names($id);
		$string.= $this->get_children($id,0);
		$string.= "</branch>\n";
		echo $string;
	}

	function spouse_names($id) {
		global $prefix;
		// query database
		$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1,B.maiden_name AS mname1, ";
		$sql.= "C.first_name AS fname2,C.last_name AS lname2,C.maiden_name AS mname2 ";
		$sql.= "FROM ".$prefix."_spouses A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.") ";
		$sql.= "ORDER BY married";
	
		$res = mysql_query($sql) or die(mysql_error());
	
		$string = "";
		
		while(list($person1,$person2,$fname1,$lname1,$mname1,$fname2,$lname2,$mname2) = mysql_fetch_row($res)) {
			if($person1 == $id) {
				$spouse_id    = $person2;
				$spouse_fname = $fname2;
				$spouse_lname = $lname2;
				$spouse_mname = $mname2;
			}
			else {
				$spouse_id    = $person1;
				$spouse_fname = $fname1;
				$spouse_lname = $lname1;
				$spouse_mname = $mname1;
			}

			if($spouse_mname)
				$spouse_mname = "(".$spouse_mname.") ";

			$string.= "<spouse id=\"".$spouse_id."\">".htmlspecialchars($spouse_fname)." ".htmlspecialchars($spouse_mname).htmlspecialchars($spouse_lname)."</spouse>\n";
		}
		return $string;
	}
}
?>
