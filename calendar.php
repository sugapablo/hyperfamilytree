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

$m 	= intval(mysql_escape_string($_GET['m']));
$y 	= intval(mysql_escape_string($_GET['y']));
$list 	= intval($_GET['list']);

function textMonth($date) {
	switch($date) {
	case 1:
		$month = "Jan";
		break;
	case 2:
		$month = "Feb";
		break;
	case 3:
		$month = "Mar";
		break;
	case 4:
		$month = "Apr";
		break;
	case 5:
		$month = "May";
		break;
	case 6:
		$month = "Jun";
		break;
	case 7:
		$month = "Jul";
		break;
	case 8:
		$month = "Aug";
		break;
	case 9:
		$month = "Sep";
		break;
	case 10:
		$month = "Oct";
		break;
	case 11:
		$month = "Nov";
		break;
	case 12:
		$month = "Dec";
		break;
	}
	return $month;
}

function ical() {
	global $siteURL;
	global $siteURLpath;
	
	$string = " - <a href=\"webcal://".$siteURL."ical.php\">Export to iCal</a>";
	$string.= " - <a href=\"https://www.google.com/calendar/render?cid=".$siteURLpath."ical.php\">Export to Google Calendar</a>";
	
	return $string;
}
?>

<p class="center"><a href="javascript:history.go(-1);"><img src="images/b-back.png" alt="print"/></a> <a href="index.php"><img src="images/b-home.png" alt="print"/></a></p>

<table class="display">
	<tr>
		<td class="display">
<?php			
if($list) {
	echo "<div class=\"center\"><a href=\"calendar.php\">View Monthly</a>".ical()."</div><br/>";
	
	// GET BIRTHDAYS
	$sql = "SELECT id,first_name,last_name,birthdate FROM ".$prefix."_people WHERE (birthdate != '0000-00-00') ORDER BY birthdate";
	$res = mysql_query($sql);
	
	while(list($id,$first_name,$last_name,$odate) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		$title = htmlspecialchars($last_name).",&nbsp;".htmlspecialchars($first_name);
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Birthday|".$title."|".$p[0];
	}

	// GET DEATHS
	$sql = "SELECT id,first_name,last_name,died FROM ".$prefix."_people WHERE (died != '0000-00-00') ORDER BY died";
	$res = mysql_query($sql);

	while(list($id,$first_name,$last_name,$odate) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		$title = htmlspecialchars($last_name).",&nbsp;".htmlspecialchars($first_name);
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Died|".$title."|".$p[0];
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
			$title = htmlspecialchars($lname1).",&nbsp;".htmlspecialchars($fname1)." &amp; ".htmlspecialchars($fname2);
		else
			$title = htmlspecialchars($lname1).",&nbsp;".htmlspecialchars($fname1)." &amp; ".htmlspecialchars($lname2).",&nbsp;".htmlspecialchars($fname2);
		
		$array[$p[1].'-'.$p[2]][$i++] = $p[1].'-'.$p[2]."|Anniversary|".$title."|".$p[0];
	}


	ksort($array);

	echo "<table class=\"stats\">
		<tr>
			<td class=\"stats_head\" colspan=\"2\">Date</td>
			<td class=\"stats_head\">Event</td>
			<td class=\"stats_head\">Who</td>
			<td class=\"stats_head\">Year</td>
		</tr>";
	
	foreach($array as $key=>$value) {
		foreach($value as $record) {
			$part = explode("|",$record);
			$date = explode("-",$part[0]);
				
			if(substr($part[3],0,4) != "0000")
				$part[3] = substr($part[3],0,4);
			else
				$part[3] = "????";

			echo "	<tr>
					<td class=\"stats_body\">".textMonth(intval($date[0]))."</td>
					<td class=\"stats_body\">".intval($date[1])."</td>
					<td class=\"stats_body\">".$part[1]."</td>
					<td class=\"stats_body\">".$part[2]."</td>
					<td class=\"stats_body\">".$part[3]."</td>
				</tr>";
		}
	}

	echo "</table>";
		
}
else {
	echo "<div class=\"center\"><a href=\"calendar.php?list=1\">View Annual</a>".ical()."</div>";
	
	// Fix dates
	if(empty($m)) $m = date('n');
	if(empty($y)) $y = date('Y');

	$day = date('w',mktime(0,0,0,$m,1,$y));
	$days = date('t',mktime(0,0,0,$m,1,$y));
	$dateStr = date('F Y',mktime(0,0,0,$m,1,$y));
	$next = date('m-Y',mktime(0,0,0,($m + 1),1,$y));
	$prev = date('m-Y',mktime(0,0,0,($m - 1),1,$y));
	$next = str_replace("-","&amp;y=",$next);
	$prev = str_replace("-","&amp;y=",$prev);

	// GET BIRTHDAYS
	$sql = "SELECT id,first_name,last_name,birthdate,died FROM ".$prefix."_people WHERE (birthdate LIKE '%-".date('m',mktime(0,0,0,$m,1,$y))."-%') ORDER BY birthdate";
	$res = mysql_query($sql);

	while(list($id,$first_name,$last_name,$odate,$died) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		$title = htmlspecialchars($last_name).",&nbsp;".htmlspecialchars($first_name);
		if(substr($died,0,4) != "0000")
			$died = "-".substr($died,0,4);
		else
			$died = "";

		if(substr($p[0],0,4) != "0000")
			$p[0] = substr($p[0],0,4);
		else
			$p[0] = "????";
			
		if($t[intval($p[2])][0])
			array_push($t[intval($p[2])],$id."|Birthday:&nbsp;".$title." (".$p[0].$died.")");
		else
			$t[intval($p[2])][0] = $id."|Birthday:&nbsp;".$title." (".$p[0].$died.")";
	}

	// GET DEATHS
	$sql = "SELECT id,first_name,last_name,birthdate,died FROM ".$prefix."_people WHERE (died LIKE '%-".date('m',mktime(0,0,0,$m,1,$y))."-%') ORDER BY died";
	$res = mysql_query($sql);

	while(list($id,$first_name,$last_name,$birthdate,$odate) = mysql_fetch_row($res)) {
		if(substr($birthdate,0,4) != "0000")
			$birthdate = substr($birthdate,0,4)."-";
		else
			$birthdate = "";

		$p = explode("-",$odate);

		if(substr($p[0],0,4) != "0000")
			$p[0] = substr($p[0],0,4);
		else
			$p[0] = "????";

		$title = htmlspecialchars($last_name).",&nbsp;".htmlspecialchars($first_name);
		if($t[intval($p[2])][0])
			array_push($t[intval($p[2])],$id."|Died: ".$title." (".$birthdate.$p[0].")");
		else
			$t[intval($p[2])][0] = $id."|Died: ".$title." (".$birthdate.$p[0].")";
	}

	// GET ANNIVERSARIES
	$sql = "SELECT A.person1,A.person2,B.first_name AS fname1,B.last_name AS lname1,";
	$sql.= "C.first_name AS fname2,C.last_name AS lname2,A.married,A.divorced ";
	$sql.= "FROM ".$prefix."_spouses A ";
	$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
	$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
	$sql.= "WHERE (A.married LIKE '%-".date('m',mktime(0,0,0,$m,1,$y))."-%') ";
	$sql.= "ORDER BY married";
		
	$res = mysql_query($sql);

	while(list($person1,$person2,$fname1,$lname1,$fname2,$lname2,$odate,$divorced) = mysql_fetch_row($res)) {
		$p = explode("-",$odate);
		if($lname1 == $lname2)
			$title = htmlspecialchars($lname1).",&nbsp;".htmlspecialchars($fname1)." &amp; ".htmlspecialchars($fname2);
		else
			$title = htmlspecialchars($lname1).",&nbsp;".htmlspecialchars($fname1)." &amp; ".htmlspecialchars($lname2).",&nbsp;".htmlspecialchars($fname2);
			
		if($t[intval($p[2])][0])
			array_push($t[intval($p[2])],$id."|Anniversary: ".$title." (".$p[0].")");
		else
			$t[intval($p[2])][0] = $id."|Anniversary: ".$title." (".$p[0].")";
	}

	
	echo "<h2 class=\"cal\">".$dateStr."</h2>
		<p class=\"center\"><a href=\"calendar.php?m=".$prev."\">Previous Month</a> - <a href=\"calendar.php?m=".$next."\">Next Month</a></p>
		<table class=\"cal\">
			<tr>
				<td class=\"calh\">Sunday</td>
				<td class=\"calh\">Monday</td>
				<td class=\"calh\">Tuesday</td>
				<td class=\"calh\">Wednesday</td>
				<td class=\"calh\">Thursday</td>
				<td class=\"calh\">Friday</td>
				<td class=\"calh\">Saturday</td>
			</tr><tr>";
			
	for($i = 0;$i < $day;$i++) 
		echo "<td class=\"cal\">&nbsp;</td>";
	for($i = 1;$i <= $days;$i++) {
		echo "	<td class=\"cal\">
			<b>".$i."</b><br/>";
		if(count($t[$i])) {
			foreach($t[$i] as $item) {
				$part = explode("|",$item);

				echo "	<div class=\"smallcal\">";
				if($part[0])
					echo "		<a class=\"smallcal\" href=\"person.php?id=".$part[0]."\">";
				echo 		$part[1];
				if($part[0])
					echo "		</a>";
				echo "		<br/><br/>
					</div>";
			}
		}
		echo "	</td>";
		$day++;
		if($day == 7 && $i != $days) {
			echo "</tr><tr>";
			$day = 0;
		}
	}
	for($i = $day;$i < 7;$i++) 
		echo "<td class=\"cal\">&nbsp;</td>";
	echo "	</tr>
		</table>";
}

?>		
		</td>
	</tr>

</table>

<?php
require_once($path."includes/bottom.php");
?>
