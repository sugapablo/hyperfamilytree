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

	function date_check($date) {
		if(substr($date,0,4) == "0000")
			$date = substr_replace($date,"????",0,4);
		return $date;
	}

	function person_display($id,$admin = 0) {
		if(show_person($id)) {
			echo "<table class=\"display\">
				<tr>";
			
			echo "<td class=\"display\">";

			echo show_person($id,$admin);
	
			if(show_siblings($id) || show_spouses($id) || show_children($id) || $admin) {
				echo "		</td><td class=\"display\">&nbsp;</td>";
					
				echo "		<td class=\"display\">";
			}
	
			if(show_siblings($id) || $admin) {
				echo "<div>siblings</div>";
				echo show_siblings($id,$admin);
			}

			if(show_spouses($id) || $admin) {
				echo "<div>spouses</div>";
				echo show_spouses($id,$admin);
			}

			if(show_children($id)) {
				echo "<div>children</div>";
				echo show_children($id,$admin);
			}

			echo "		</td>
				</tr>";
				
			if(!$admin) {
				echo "<tr>
					<td class=\"display.php\" colspan=\"3\">
						<div class=\"center\"><a href=\"tree.php?id=".$id."\"><b>show tree</b></a></div>
				      	</td>
				      </tr>";
			}

			echo "</table>";
		
			if(show_photos($id) || $admin) {
				echo "<div>&nbsp;</div>";
				echo "<table class=\"display\">";
				echo "<tr><td colspan=\"3\">";
				echo "<div>photographs</div>";
				echo show_photos($id,$admin);
				echo "</td></tr>";
				echo "</table>";
			}
		
			if(show_about($id)) {
				echo "<div>&nbsp;</div>";
				echo "<table class=\"display\">
					<tr>
						<td class=\"display\">";
				echo show_about($id);
				echo "		</td>
					</tr>
				      </table>";
				echo "<div>&nbsp;</div>";
			}
		}
	}

	function show_person($id,$admin = 0) {
		global $prefix;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));

		// query database
		$sql = "SELECT A.*, B.first_name AS ffname, B.last_name AS flname, ";
		$sql.= "C.first_name AS mfname, C.last_name AS mlname ";
		$sql.= "FROM ".$prefix."_people A ";
		$sql.= "LEFT JOIN ".$prefix."_people B on A.father = B.id ";
		$sql.= "LEFT JOIN ".$prefix."_people C on A.mother = C.id ";
		$sql.= "WHERE (A.id = ".$id.") LIMIT 1";
		$res = mysql_query($sql) or die(mysql_error());

		$row = mysql_fetch_array($res);
		
		// Begin table display
		$string = "<table class=\"person\">";
		if($row['first_name'] != "") 
			$string.= "<tr><td class=\"person_head\">First Name</td><td class=\"person_body\">".$row['first_name']."</td></tr>";
		if($row['middle_name'] != "") 
			$string.= "<tr><td class=\"person_head\">Middle Name</td><td class=\"person_body\">".$row['middle_name']."</td></tr>";
		if($row['last_name'] != "") 
			$string.= "<tr><td class=\"person_head\">Last Name</td><td class=\"person_body\">".$row['last_name']."</td></tr>";
		if($row['former_last_name'] != "") 
			$string.= "<tr><td class=\"person_head\">Former Last Name</td><td class=\"person_body\">".$row['former_last_name']."</td></tr>";
		if($row['maiden_name'] != "") 
			$string.= "<tr><td class=\"person_head\">Maiden Name</td><td class=\"person_body\">".$row['maiden_name']."</td></tr>";
		if($row['gender'] != "") 
			$string.= "<tr><td class=\"person_head\">Gender</td><td class=\"person_body\">".$row['gender']."</td></tr>";
		if($row['birthdate'] != "0000-00-00") 
			$string.= "<tr><td class=\"person_head\">Birthdate</td><td class=\"person_body\">".date_check($row['birthdate'])."</td></tr>";
		if($row['birthplace'] != "") 
			$string.= "<tr><td class=\"person_head\">Birthplace</td><td class=\"person_body\">".$row['birthplace']."</td></tr>";
		if($row['died'] != "0000-00-00")
			$string.= "<tr><td class=\"person_head\">Died</td><td class=\"person_body\">".date_check($row['died'])."</td></tr>";
		if($row['resting_place'] != "") 
			$string.= "<tr><td class=\"person_head\">Resting Place</td><td class=\"person_body\">".$row['resting_place']."</td></tr>";


		if($admin)
			$string.= "<tr><td class=\"person_head\"><b>EDIT -&gt;</b></td><td class=\"person_body\"><a href=\"edit_person.php?id=".$id."\">Edit Person</a></td></tr>";


		$string.= "</table>";
		$string.= "<div>&nbsp;</div>";

		if($row['mother'] || $row['father']) {
			$string.= "<div>parents</div>";
			$string.= "<table class=\"person\">";
		}
		
		if($row['father']) {
			$string.= "<tr>
					<td class=\"person_head\">Father</td>
					<td class=\"person_body\">
						<a href=\"person.php?id=".$row['father']."\">".$row['ffname']."&nbsp;".$row['flname']."</a>
					</td>
				   </tr>";
		}
		if($row['mother']) {
			$string.= "<tr>
					<td class=\"person_head\">Mother</td>
					<td class=\"person_body\">
						<a href=\"person.php?id=".$row['mother']."\">".$row['mfname']."&nbsp;".$row['mlname']."</a>
					</td>
				   </tr>";
		}
		if(($row['mother'] || $row['father']) && !$admin) {
			$string.= "<tr>
					<td class=\"person_head\"><b>-&gt;</b></td>
					<td class=\"show\">
						<a href=\"ancestors.php?id=".$id."\"><b>show all ancestors</b></a>
					</td>
				   </tr>";
		}
			   
		if($row['adopted'])
			$string.= "<tr><td class=\"person_head\">Adopted</td><td class=\"person_body\">Yes</td></tr>";
			
		if($row['mother'] || $row['father']) {
			$string.= "</table>";
			$string.= "<div>&nbsp;</div>";
		}
		

		if(mysql_num_rows($res)) 
			return $string;
		else
			return false;
	}

	function show_children($id,$admin = 0) {
		global $prefix,$siteURLpath;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));
		
		// query database
		$sql = "SELECT id,first_name,last_name,gender FROM ".$prefix."_people WHERE ";
		$sql.= "(mother = ".$id." OR father = ".$id.") ORDER BY birthdate";
		$res = mysql_query($sql);

		
		// begin table display
		$string = "<table class=\"children\">";

		while(list($child_id,$first_name,$last_name,$gender) = mysql_fetch_row($res)) {
			// get son/daughter display
			if($gender == "M")
				$child = "Son";
			elseif($gender == "F")
				$child = "Daughter";
			else
				$child = "Unknown";

			$string.= "<tr>
					<td class=\"children_head\">".$child."</td>
					<td class=\"children_body\">
						<a href=\"person.php?id=".$child_id."\">".$first_name."</a>
					</td>
				   </tr>";
		}

		if(!$admin) {
			$string.= "<tr>
					<td class=\"children_head\"><b>-&gt;</b></td>
					<td class=\"show\"><b><a href=\"descendants.php?id=".$id."\">show all descendants</a></b></td>
			 	 </tr>";

		}					
			
		$string.= "</table>";
		$string.= "<div>&nbsp;</div>";

		if(mysql_num_rows($res)) 
			return $string;
		else
			return false;
	}

	function show_siblings($id,$admin = 0) {
		global $prefix;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));
		
		// query database
		$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1, B.gender AS gender1, ";
		$sql.= "C.first_name AS fname2,C.last_name AS lname2, C.gender AS gender2 ";
		$sql.= "FROM ".$prefix."_siblings A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.")";
		
		$res = mysql_query($sql);

		// begin table display
		$string = "<table class=\"siblings\">";
		
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
			
			$string.= "<tr>
					<td class=\"siblings_head\">".$sibling."</td>
					<td class=\"siblings_body\">
						<a href=\"person.php?id=".$sibling_id."\">".$sibling_fname."</a>
					</td>
				   </tr>";
		}

		if($admin)
			$string.= "<tr><td class=\"siblings_head\"><b>EDIT -&gt;</b></td><td class=\"siblings_body\"><a href=\"siblings.php?id=".$id."\">Edit Sibling Links</a></td></tr>";

			
		$string.= "</table>";
		$string.= "<div>&nbsp;</div>";

		if(mysql_num_rows($res)) 
			return $string;
		elseif($admin)
			return "<table class=\"siblings\"><tr><td class=\"siblings_head\"><b>EDIT -&gt;</b></td><td class=\"siblings_body\"><a href=\"siblings.php?id=".$id."\">Edit Sibling Links</a></td></tr></table><div>&nbsp;</div>";
		else
			return false;
	}

	function show_spouses($id,$admin = 0) {
		global $prefix;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));
		
		// query database
		$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1,";
		$sql.= "C.first_name AS fname2,C.last_name AS lname2,A.married,A.divorced ";
		$sql.= "FROM ".$prefix."_spouses A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.") ";
		$sql.= "ORDER BY married";
		
		$res = mysql_query($sql);

		// begin table display
		$string = "<table class=\"spouses\">";
		
		while(list($person1,$person2,$fname1,$lname1,$fname2,$lname2,$married,$divorced) = mysql_fetch_row($res)) {
			if($person1 == $id) {
				$spouse_id    = $person2;
				$spouse_fname = $fname2;
				$spouse_lname = $lname2;
			}
			else {
				$spouse_id    = $person1;
				$spouse_fname = $fname1;
				$spouse_lname = $lname1;
			}
			$string.= "<tr>
					<td class=\"spouses_head\">Married</td>
					<td class=\"spouses_body\">
						<a href=\"person.php?id=".$spouse_id."\">".$spouse_fname."&nbsp;".$spouse_lname."</a>
					</td>
					<td class=\"spouses_body\">";

			if($married == "0000-00-00")
				$string.= "unknown wedding date";
			else
				$string.= date_check($married);

			if($divorced != "0000-00-00")
				$string.= "&nbsp;-&nbsp;".date_check($divorced);
			
						
			$string.= "	</td>
				   </tr>";
		}

		if($admin)
			$string.= "<tr><td class=\"spouses_head\"><b>EDIT -&gt;</b></td><td colspan=\"2\" class=\"spouses_body\"><a href=\"spouses.php?id=".$id."\">Edit Spouse Links</a></td></tr>";
			
		$string.= "</table>";
		$string.= "<div>&nbsp;</div>";

		if(mysql_num_rows($res)) 
			return $string;
		elseif($admin)
			return "<table class=\"spouses\"><tr><td class=\"spouses_head\"><b>EDIT -&gt;</b></td><td colspan=\"2\" class=\"spouses_body\"><a href=\"spouses.php?id=".$id."\">Edit Spouse Links</a></td></tr></table><div>&nbsp;</div>";
		else
			return false;
	}

	function show_photos($id,$admin = 0) {
		global $prefix;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));
		$i 	= 1;
		
		// query database
		$sql = "SELECT filename,caption,year FROM ".$prefix."_photos WHERE (person = ".$id.") ORDER BY year,filename";
		$res = mysql_query($sql);

		// begin table display
		$string = "<table class=\"photos\">";
		
		while(list($filename,$caption,$year) = mysql_fetch_row($res)) {
			if($admin)
				$url = "../photo.php?id=".$id."&amp;photo=".$filename."&amp;back=admin%2Fperson.php";
			else
				$url = "photo.php?id=".$id."&amp;photo=".$filename;

			$string.= "<tr>
					<td class=\"photos_head\">Photo #".$i."</td>
					<td class=\"photos_body\"><a href=\"".$url."\">".$caption."</a></td>
					<td class=\"photos_body\">".$year."</td>
				   </tr>";
			$i++;
		}

		if($admin) {
			$string.= "<tr>
				<td class=\"photos_head\"><b>EDIT -&gt;</b></td>
				<td class=\"photos_body\" colspan=\"3\">
					<a href=\"photos.php?id=".$id."\">Edit Photos</a>
				</td>
			      </tr>";					
		}

		$string.= "</table>";
		$string.= "<div>&nbsp;</div>";

		if(mysql_num_rows($res)) 
			return $string;
		elseif($admin) {
			echo "<table class=\"photos\"><tr>
				<td class=\"photos_head\"><b>EDIT -&gt;</b></td>
				<td class=\"photos_body\" colspan=\"3\">
					<a href=\"photos.php?id=".$id."\">Edit Photos</a>
				</td>
			      </tr></table>";		
		}
		else
			return false;
	}

	function show_about($id) {
		global $prefix;
		
		// clean variables
		$id 	= intval(mysql_escape_string($id));
		
		// query database
		$sql = "SELECT first_name,about FROM ".$prefix."_people WHERE (id = ".$id.")";	
		$res = mysql_query($sql);

		list($first_name,$about) = mysql_fetch_row($res);
		
		// begin display
		$string = "<div class=\"about\"><b>About ".$first_name.":</b><br/>".nl2br(htmlspecialchars($about))."</div>";
		
		if($about) 
			return $string;
		else
			return false;
	}

	function get_people($action,$task) {
		global $prefix;
		
		$sql = "SELECT id,first_name,last_name,middle_name,maiden_name FROM ".$prefix."_people ORDER BY last_name,first_name";
		$res = mysql_query($sql);

		$string = "<form method=\"get\" action=\"".$action."\" id=\"get_people\">";
		$string.= "	<div>";
		$string.= "		Select Person: <select name=\"id\">
					<option value=\"0\">Select</option>";

		while(list($id,$first_name,$last_name,$middle_name,$maiden_name) = mysql_fetch_row($res)) {
			if($maiden_name)
				$maiden_name = " (".$maiden_name.")";
			$string .="<option value=\"".$id."\">".$last_name.$maiden_name.", ".$first_name." ".$middle_name."</option>";
		}
		
		$string.= "				</select>";
	
		if($task) 
			$string.= "<input type=\"hidden\" name=\"task\" value=\"".$task."\"/>";
		
		$string.= "		<input type=\"submit\" value=\"View\"/>";
		$string.= "	</div>";
		$string.= "</form>";

		return $string;
	}

	function stats() {
		global $prefix;
		
		$sql = "SELECT COUNT(id) FROM ".$prefix."_people";
		$res = mysql_query($sql);
		list($people) = mysql_fetch_row($res);

		$sql = "SELECT COUNT(id) FROM ".$prefix."_people WHERE (gender = 'M')";
		$res = mysql_query($sql);
		list($males) = mysql_fetch_row($res);
		
		$sql = "SELECT COUNT(id) FROM ".$prefix."_people WHERE (gender = 'F')";
		$res = mysql_query($sql);
		list($females) = mysql_fetch_row($res);
		
		$sql = "SELECT COUNT(DISTINCT(last_name)) FROM ".$prefix."_people";
		$res = mysql_query($sql);
		list($names) = mysql_fetch_row($res);
		
		$sql = "SELECT id,first_name,last_name,birthdate FROM ".$prefix."_people WHERE (birthdate NOT LIKE '0000%') ORDER BY birthdate LIMIT 1";
		$res = mysql_query($sql);
		list($id1,$first_name1,$last_name1,$oldest) = mysql_fetch_row($res);
		
		$sql = "SELECT id,first_name,last_name,birthdate FROM ".$prefix."_people WHERE (birthdate NOT LIKE '0000%') ORDER BY birthdate DESC LIMIT 1";
		$res = mysql_query($sql);
		list($id2,$first_name2,$last_name2,$youngest) = mysql_fetch_row($res);

		$sql = "SELECT COUNT(id) FROM ".$prefix."_photos";
		$res = mysql_query($sql);
		list($photos) = mysql_fetch_row($res);
		
		$string = "<p class=\"center\"><b>Database Statistics:</b></p>";

		if($people) {
			$string.= "<table class=\"stats\">
					<tr>
						<td class=\"stats_head\">People</td>
						<td class=\"stats_body\">".$people."</td>
					</tr><tr>
						<td class=\"stats_head\">Men</td>
						<td class=\"stats_body\">".$males."</td>
					</tr><tr>
						<td class=\"stats_head\">Women</td>
						<td class=\"stats_body\">".$females."</td>
					</tr><tr>
						<td class=\"stats_head\">Last Names</td>
						<td class=\"stats_body\">".$names."</td>
					</tr><tr>
						<td class=\"stats_head\">Oldest Birthdate</td>
						<td class=\"stats_body\">
							<a href=\"person.php?id=".$id1."\">".$first_name1."&nbsp;".$last_name1."</a>: ".$oldest."
						</td>
					</tr><tr>
						<td class=\"stats_head\">Youngest Birthdate</td>
						<td class=\"stats_body\">
							<a href=\"person.php?id=".$id2."\">".$first_name2."&nbsp;".$last_name2."</a>: ".$youngest."
						</td>
					</tr><tr>
						<td class=\"stats_head\">Photos</td>
						<td class=\"stats_body\">".$photos."</td>
					</tr>
			            </table>";
		}
		else
			echo "<p class=\"center\">Database is currently empty.</p>";

		return $string;
	}	
?>
