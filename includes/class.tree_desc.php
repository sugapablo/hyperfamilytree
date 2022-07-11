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

class tree_desc {
	var $treelinks;

	function get_siblings($id) {
		global $prefix;

		// query database
		$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1, B.gender AS gender1, ";
		$sql.= "C.first_name AS fname2,C.last_name AS lname2, C.gender AS gender2 ";
		$sql.= "FROM ".$prefix."_siblings A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.")";
		
		$res = mysql_query($sql);

		while(list($person1,$person2,$fname1,$lname1,$gender1,$fname2,$lname2,$gender2) = mysql_fetch_row($res)) {
			// assign person who isn't the person
			if($person1 == $id) {
				$sibling_id     = $person2;
				$sibling_fname  = $fname2;
				$sibling_lname  = $lname2;
				$sibling_gender = $gender2;
			}
			else {
				$sibling_id     = $person1;
				$sibling_fname  = $fname1;
				$sibling_lname  = $lname1;
				$sibling_gender = $gender1;
			}

			// get brother/sister display
			if($sibling_gender == "M")
				$sibling = "Brother";
			elseif($sibling_gender == "F")
				$sibling = "Sister";
			else
				$sibling = "Unknown";
			
			$string.= "</td><td class=\"tree2\">
					<div class=\"tree\">
						<p class=\"tree_middle\">
							<a href=\"person.php?id=".$id."\">".$sibling_fname."&nbsp;".$maiden_name.$sibling_lname."</a>
						</p>";
			$string.= $this->spouse_names($sibling_id);
			$string.= "		</div>";
			
		}
			
		return $string;
	}

	function child_node($id,$first_name,$last_name,$maiden_name,$gender,$level) {
		if($level == 1)
			$child = "";
		elseif($level == 2)
			$child = "grand";
		elseif($level == 3)
			$child = "great-grand";
		else
			$child = "great(".($level - 2).")-grand";
			
		if($gender == "M")
			$child.= "son:";
		elseif($gender == "F")
			$child.= "daughter:";

		if($maiden_name)
			$maiden_name = "(".$maiden_name.")&nbsp;";
			
		$string = "<div class=\"treeline\">|</div>
				<div class=\"tree\">
					<p class=\"treechild\">".$child."</p>
					<p class=\"tree\">
						<a href=\"person.php?id=".$id."\">".$first_name."&nbsp;".$maiden_name.$last_name."</a>";
		if($this->treelinks) 
				$string.= "	&nbsp;<span class=\"treelink\">&nbsp;<a href=\"tree.php?id=".$id."\">tree</a>&nbsp;</span>";
		$string.= "		</p>";
		$string.= $this->spouse_names($id);		
		$string.= "	</div>";

		return $string;
	}
	
	function get_children($id,$level) {
		global $prefix;
		$go = 0;
		$level++;
		
		$sql = "SELECT id,first_name,last_name,maiden_name,gender FROM ".$prefix."_people ";
		$sql.= "WHERE (father = ".$id." OR mother = ".$id.") ORDER BY birthdate";

		$res = mysql_query($sql) or die(mysql_error());

		$string = "<table class=\"tree\">
				<tr>
				<td class=\"tree\">";

		while(list($child,$first_name,$last_name,$maiden_name,$gender) = mysql_fetch_row($res)) {
			if($go) 
				$string.= "</td><td class=\"tree\">";

			$string.= $this->child_node($child,$first_name,$last_name,$maiden_name,$gender,$level);
			$go++;
			$string.= $this->get_children($child,$level);
		}

		$string.= "	</td>
				</tr>
		           </table>";

		if($go)
			return $string;
	}
		

	function show($id,$tree = 0) {
		global $prefix;

		$this->treelinks = $tree;
		
		$string = "";
		
		$res = mysql_query("SELECT first_name,last_name,maiden_name FROM ".$prefix."_people WHERE (id = ".$id.")");
		list($first_name,$last_name) = mysql_fetch_row($res);

		if($maiden_name)
			$maiden_name = "(".$maiden_name.")&nbsp;";

		if(!$tree) {
			$string.= "<table class=\"tree\">
					<tr>";
			$string.= " 	<td class=\"tree\">";
			$class = "tree";
		}
		else
			$class = "tree_middle";

			
		$string.= "		<div class=\"tree\">
						<p class=\"".$class."\">
							<a href=\"person.php?id=".$id."\">".$first_name."&nbsp;".$maiden_name.$last_name."</a>";
		$string.= "			</p>";
		$string.= $this->spouse_names($id);
		$string.= "		</div>";
		
		$string.= $this->get_children($id,0);

		if($_GET['test']) {
			$string.= $this->get_siblings($id);
		}

		$string.= "	</td>";
		$string.= "	</tr>
		           </table>";
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
				$spouse_mname = "(".$spouse_mname.")&nbsp;";

			$string.= "<p class=\"treespouse\">
					<a href=\"person.php?id=".$spouse_id."\">".$spouse_fname."&nbsp;".$spouse_mname.$spouse_lname."</a>";
			if($this->treelinks) 
				$string.= "	&nbsp;<span class=\"treelink\">&nbsp;<a href=\"tree.php?id=".$spouse_id."\">tree</a>&nbsp;</span>";
			$string.= "</p>";
		}
		return $string;
	}
}
?>
