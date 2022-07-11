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

class xml_tree_ancs {
	
	function parentNode($id,$first_name,$last_name,$maiden_name,$gender) {
		if($maiden_name)
			$maiden_name = "(".htmlspecialchars($maiden_name).") ";
			
		$string = "<leaf id=\"".$id."\">".htmlspecialchars($first_name)." ".htmlspecialchars($maiden_name).htmlspecialchars($last_name)."</leaf>\n";

		return $string;
	}
	
	function get_parents($id,$level) {
		global $prefix;
		$go = 0;

		$level++;
		

		
		$sql = "SELECT B.id AS fid, B.first_name AS ffname, B.last_name AS flname, B.maiden_name AS fmaiden_name, B.gender AS fgender, ";
		$sql.= "C.id AS mid, C.first_name AS mfname, C.last_name AS mlname, C.maiden_name AS mmaiden_name, C.gender AS mgender ";
		$sql.= "FROM ".$prefix."_people A ";
		$sql.= "LEFT JOIN ".$prefix."_people B on A.father = B.id ";
		$sql.= "LEFT JOIN ".$prefix."_people C on A.mother = C.id ";
		$sql.= "WHERE (A.id = ".$id.") LIMIT 1";
		$res = mysql_query($sql) or die(mysql_error());


		while(list($fparent,$ffirst_name,$flast_name,$fmaiden_name,$fgender,$mparent,$mfirst_name,$mlast_name,$mmaiden_name,$mgender) = mysql_fetch_row($res)) {
			if($level == 1)
				$class = "";
			elseif($level == 2)
				$class = "grand";
			elseif($level == 3)
				$class = "great-grand";
			else
				$class = "great(".($level - 2).")-grand";
			
			if($fparent) {
				if($fgender == "M")
					$class.= "father";
				elseif($fgender == "F")
					$class.= "mother";

				if($go) 
					$string.= "</branch><branch id=\"".$class."\">\n";
				else
					$string = "<branch id=\"".$class."\">\n";

				$string.= $this->get_parents($fparent,$level);
				$string.= $this->parentNode($fparent,$ffirst_name,$flast_name,$fmaiden_name,$fgender);
				$go++;
			}

			if($level == 1)
				$class = "";
			elseif($level == 2)
				$class = "grand";
			elseif($level == 3)
				$class = "great-grand";
			else
				$class = "great(".($level - 2).")-grand";

		
			if($mparent) {
				if($mgender == "M")
					$class.= "father";
				elseif($mgender == "F")
					$class.= "mother";

				if($go) 
					$string.= "</branch><branch id=\"".$class."\">\n";
				else
					$string = "<branch id=\"".$class."\">\n";

				$string.= $this->get_parents($mparent,$level);
				$string.= $this->parentNode($mparent,$mfirst_name,$mlast_name,$mmaiden_name,$mgender);
				$go++;
			}
		}

		$string.= "	</branch>\n";

		if($go)
			return $string;
	}
		

	function show($id,$tree = 0) {
		global $prefix;
		
		$res = mysql_query("SELECT first_name,last_name,maiden_name FROM ".$prefix."_people WHERE (id = ".$id.")");
		list($first_name,$last_name) = mysql_fetch_row($res);

		if($maiden_name)
			$maiden_name = "(".$maiden_name.") ";

		$string = "<branch id=\"person\">\n";
		$string.= $this->get_parents($id,0);
		if(!$tree) {
			$string.= "		<leaf nid=\"".$id."\">".htmlspecialchars($first_name)." ".htmlspecialchars($maiden_name).htmlspecialchars($last_name)."</leaf>";
			$string.= "</branch>";
		}
		echo $string;
	}
}
?>
