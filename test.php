<?php
$prefix = "phpft";

require_once("config.php");
require_once("includes/database.php");

class relation {
	var $person1;
	var $person2;
	var $personArray;
	var $everyone;
	var $x;

	function get_children($id) {
		global $prefix;
		
		// query database
		$sql = "SELECT id FROM ".$prefix."_people WHERE ";
		$sql.= "(mother = ".$id." OR father = ".$id.") ORDER BY birthdate";
		
		$res = mysql_query($sql) or die(mysql_error());
		
		while(list($id1) = mysql_fetch_row($res)) {
			$this->get_connections($id,$id1);
		}
	}

	function get_parents($id) {
		global $prefix;
		
		// query database
		$sql = "SELECT B.id AS fid, C.id AS mid ";
		$sql.= "FROM ".$prefix."_people A ";
		$sql.= "LEFT JOIN ".$prefix."_people B on A.father = B.id ";
		$sql.= "LEFT JOIN ".$prefix."_people C on A.mother = C.id ";
		$sql.= "WHERE (A.id = ".$id.") LIMIT 1";
		
		//echo $sql."<br/>";
		$res = mysql_query($sql) or die(mysql_error());
		
		list($id1,$id2) = mysql_fetch_row($res);
		
		if($id1) {
			$this->get_connections($id,$id1);
		}
		if($id2) {
			$this->get_connections($id,$id1);
		}
	}

	function get_siblings($id) {
		global $prefix;
		
		// query database
		$sql = "SELECT B.id AS id1, C.id AS id2 ";
		$sql.= "FROM ".$prefix."_siblings A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.") ";
		
		//echo $sql."<br/>";
		$res = mysql_query($sql) or die(mysql_error());

		while(list($id1,$id2) = mysql_fetch_row($res)) {
			if($id = $id1) 
				$this->get_connections($id,$id2);
			elseif($id = $id2)
				$this->get_connections($id,$id1);
		}
	}

	function get_spouses($id) {
		global $prefix;
		
		// query database
		$sql = "SELECT B.id AS id1, C.id AS id2 ";
		$sql.= "FROM ".$prefix."_spouses A ";
		$sql.= "INNER JOIN ".$prefix."_people B ON A.person1 = B.id ";
		$sql.= "INNER JOIN ".$prefix."_people C ON A.person2 = C.id ";
		$sql.= "WHERE (A.person1 = ".$id." OR A.person2 = ".$id.") ";
		
		//echo $sql."<br/>";
		$res = mysql_query($sql);

		while(list($id1,$id2) = mysql_fetch_row($res)) {
			if($id = $id1) 
				$this->get_connections($id,$id2);
			elseif($id = $id2)
				$this->get_connections($id,$id1);
		}
	}

	function get_connections($i,$id) {
		$this->personArray[$i] = $id;	
		
		if($id != $this->person2 && !in_array($id,$this->everyone)) {
			$this->everyone[$this->x++] = $id;		
			$this->get_children($id);
			$this->get_parents($id);
			$this->get_siblings($id);
			$this->get_spouses($id);
		}
		else {
			return $this->map_points($i);
		}
	}

	function map_points($id) {
		echo "<pre>";
		print_r($this->personArray);
		echo "</pre>";
		die;
	}
	
	function show() {
		$this->everyone[0] = 0;
		$this->person1 = intval(mysql_escape_string($this->person1));
		echo $this->get_connections(0,$this->person1);
	}
}

$map = new relation;
$map->person1 = 1;
$map->person2 = 10;
$map->show();

?>
