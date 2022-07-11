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
header('Content-type: text/calendar');
header('Content-Disposition: attachment; filename="familytree.ics"');


require_once("config.php");
require_once($path."includes/database.php");



?>
BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:HyperFamilyTree
CALSCALE:GREGORIAN
METHOD:PUBLISH
<?php			
	
	// GET BIRTHDAYS
	$sql = "SELECT id,first_name,last_name,birthdate FROM ".$prefix."_people WHERE (birthdate != '0000-00-00') ORDER BY birthdate";
	$res = mysql_query($sql);
	
	while(list($id,$first_name,$last_name,$odate) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		$title = $last_name.", ".$first_name;
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Birthday|".$title."|".$p[0].
		"|".$id;
	}

	// GET DEATHS
	$sql = "SELECT id,first_name,last_name,died FROM ".$prefix."_people WHERE (died != '0000-00-00') ORDER BY died";
	$res = mysql_query($sql);

	while(list($id,$first_name,$last_name,$odate) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		$title = $last_name.", ".$first_name;
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Died|".$title."|".$p[0].
		"|".$id;
	}

	// GET ANNIVERSARIES
	$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1,";
	$sql.= "C.first_name AS fname2,C.last_name AS lname2,A.married,A.divorced ";
	$sql.= "FROM ".$prefix."_spouses A ";
	$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
	$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
	$sql.= "WHERE (A.married != '0000-00-00') ";
	$sql.= "ORDER BY married";

	$res = mysql_query($sql);

	while(list($person1,$person2,$fname1,$lname1,$fname2,$lname2,$odate,$divorced) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		if($lname1 == $lname2)
			$title = $lname1.", ".$fname1." & ".$fname2;
		else
			$title = $lname1.", ".$fname1." & ".$lname2.", ".$fname2;
		
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Anniversary|".$title."|".$p[0]."|0";
	}


	ksort($array);

	
	foreach($array as $key=>$value) {
		foreach($value as $record) {
			$part = explode("|",$record);
			$date = explode("-",$part[0]);
			
			if($date[0] != "00" && $date[1] != "00") {				
				echo "BEGIN:VEVENT\r\n";
				echo "DTSTART;VALUE=DATE:".$part[3].$date[0].$date[1]."\r\n";
				if($part[4])
					echo "URL;VALUE=URI:".$siteURLpath."person.php?id=".$part[4]."\r\n";
				else
					echo "URL;VALUE=URI:".$siteURLpath."\r\n";
				echo "RRULE:FREQ=YEARLY;INTERVAL=1\r\n";
				if($part[3] == "0000")
					$part[3] = "????";
				echo "SUMMARY:".$part[1].": ".str_replace(",","\,",$part[2])." (".$part[3].")\r\n";
				echo "END:VEVENT\r\n";
			}
		}
	}


		


?>
END:VCALENDAR