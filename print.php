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

header("Content-type: text/plain");

$sql = "SELECT A.*, B.id AS fid, B.first_name AS ffname, B.last_name AS flname, ";
$sql.= "C.id AS mid, C.first_name AS mfname, C.last_name AS mlname ";
$sql.= "FROM ".$prefix."_people A ";
$sql.= "LEFT JOIN ".$prefix."_people B on A.father = B.id ";
$sql.= "LEFT JOIN ".$prefix."_people C on A.mother = C.id ";
$sql.= "ORDER BY A.last_name, A.first_name, A.middle_name";

$res = mysql_query($sql);

echo $site_title." :: PHP Family Tree\n";
echo "Listed in alphabetical order.\n";
echo "\n-------------------------------------------------------------------------------\n\n";

while($row = mysql_fetch_array($res)) {
	echo strtoupper($row['last_name'].", ".$row['first_name']." ".$row['middle_name']." [".$row['id']."]\n\n");

	/* echo "ID:            ".$row['id']."\n";
	if($row['first_name'])
		echo "First Name:    ".$row['first_name']."\n";
	if($row['middle_name'])
		echo "Middle Name:   ".$row['middle_name']."\n";
	if($row['last_name'])
		echo "Last Name:     ".$row['last_name']."\n"; */
	if($row['former_last_name'])
		echo "Former Last Name:     ".$row['former_last_name']."\n";
	if($row['maiden_name'])
		echo "Maiden Name:   ".$row['maiden_name']."\n";
	if($row['gender'])
		echo "Gender:        ".$row['gender']."\n";
	if($row['birthdate'] != "0000-00-00")
		echo "Birthdate:     ".$row['birthdate']."\n";
	if($row['birthplace'])
		echo "Birth Place:   ".$row['birthplace']."\n";
	if($row['died'] != "0000-00-00")
		echo "Died:          ".$row['died']."\n";
	if($row['resting_place'])
		echo "Resting Place: ".$row['resting_place']."\n";


	if($row['father'] || $row['mother']) {
		echo "\nPARENTS:\n";
		if($row['father'])
			echo "Father:        ".$row['flname'].", ".$row['ffname']." [ID:".$row['fid']."]\n";
		if($row['mother'])
			echo "Mother:        ".$row['mlname'].", ".$row['mfname']." [ID:".$row['mid']."]\n";
	}

	echo "\n";


	// SIBLINGS
	// query database
	$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1, B.gender AS gender1, ";
	$sql.= "C.first_name AS fname2,C.last_name AS lname2, C.gender AS gender2 ";
	$sql.= "FROM ".$prefix."_siblings A ";
	$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
	$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
	$sql.= "WHERE (A.person1 = ".$row['id']." OR A.person2 = ".$row['id'].")";
	$res1 = mysql_query($sql);
	
	if(mysql_num_rows($res1))
		echo "SIBLINGS:\n";
		
	while(list($person1,$person2,$fname1,$lname1,$gender1,$fname2,$lname2,$gender2) = mysql_fetch_row($res1)) {
		// assign person who isn't the person
		if($person1 == $row['id']) {
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
			$sibling = "Sister ";
		else
			$sibling = "Unknown";
		
			
		echo $sibling.":       ".$sibling_lname.", ".$sibling_fname." [ID:".$sibling_id."]\n";
	}
	
	if(mysql_num_rows($res1))
		echo "\n";
	
	//SPOUSES
	// query database
	$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1,";
	$sql.= "C.first_name AS fname2,C.last_name AS lname2,A.married,A.divorced ";
	$sql.= "FROM ".$prefix."_spouses A ";
	$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
	$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
	$sql.= "WHERE (A.person1 = ".$row['id']." OR A.person2 = ".$row['id'].") ";
	$sql.= "ORDER BY married";
		
	$res2 = mysql_query($sql);

	if(mysql_num_rows($res2))
		echo "SPOUSES:\n";

	while(list($person1,$person2,$fname1,$lname1,$fname2,$lname2,$married,$divorced) = mysql_fetch_row($res2)) {
		if($person1 == $row['id']) {
			$spouse_id    = $person2;
			$spouse_fname = $fname2;
			$spouse_lname = $lname2;
		}
		else {
			$spouse_id    = $person1;
			$spouse_fname = $fname1;
			$spouse_lname = $lname1;
		}
		
		echo "               ".$spouse_lname.", ".$spouse_fname." [ID:".$spouse_id."] ";
	
		if($married == "0000-00-00")
			echo "unknown wedding date";
		else
			echo $married;
		if($divorced != "0000-00-00")
			echo " - ".$divorced;
								
		echo "\n";
	}

	if(mysql_num_rows($res2))
		echo "\n";

	// CHILDREN
	// query database
	$sql = "SELECT id,first_name,last_name,gender FROM ".$prefix."_people WHERE ";
	$sql.= "(mother = ".$row['id']." OR father = ".$row['id'].") ORDER BY birthdate";
	$res3 = mysql_query($sql);
	
	if(mysql_num_rows($res3))
		echo "CHILDREN:\n";

	while(list($child_id,$first_name,$last_name,$gender) = mysql_fetch_row($res3)) {
		// get son/daughter display
		if($gender == "M")
			$child = "Son     ";
		elseif($gender == "F")
			$child = "Daughter";
		else
			$child = "Unknown ";

			
		echo $child.":      ".$last_name.", ".$first_name." [ID:".$child_id."]\n";
	}

	if(mysql_num_rows($res3))
		echo "\n";

	if($row['about'])
		echo wordwrap($row['about'],80)."\n";

	echo "-------------------------------------------------------------------------------\n\n";
}
?>
