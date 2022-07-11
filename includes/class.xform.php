<?php
/*
-------------------------------------------------------------------------------
Class Xform
-------------------------------------------------------------------------------
Functions designed to make creating XHTML forms a lot simpler and quicker. 
Saves a lot of typing.

Example:
include("Xform.class.php");

$form = new Xform;
$form-> setForm("get","formtest.php","form","head","body");
$form-> fText("Name","name");
$form-> fText("Email","email");
$form-> fPassword("Password","pass");
$form-> fHidden("Note","this is hidden");
$form-> fSelect("Number","number",$array,"2");
$form-> fRadio("Notify","Yes","notify","1",1,1);
$form-> fRadio("Notify","No","notify","1",0,0,1);
$form-> fSubmit("Submit");
$form-> echoForm();

-------------------------------------------------------------------------------
Copyright (c) 2005 Sugapablo
	
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

If you have any questions or comments, please email:

Sugapablo - russ@sugapablo.com - https://www.sugapablo.com

*/
class Xform {
	var $method;
	var $action;
	var $form_string;
	var $hidden_string;
	var $table_class;
	var $td_left_class;
	var $td_right_class;
	var $form_id;
		
	function setForm($method,$action,$class1,$class2,$class3,$id = 0) {
		$this->method = $method;
		$this->action = $action;
		$this->table_class = $class1;
		$this->td_left_class = $class2;
		$this->td_right_class = $class3;
		$this->form_id = $id;
	}
	
	function fText($head,$name,$size = 0,$max = 0,$value = 0) {
		$this->form_string.= "\t<tr>\n\t\t<td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\"><input type=\"text\" name=\"".$name."\"";
		if($size)
			$this->form_string.= " size=\"".$size."\"";
		if($max)
			$this->form_string.= " maxlength=\"".$max."\"";
		if($value)
			$this->form_string.= " value=\"".$value."\"";
		$this->form_string.= "/></td>\n\t</tr>\n";
	}
	
	function fHidden($name,$value = 0) {
		$this->hidden_string.= "\t<input type=\"hidden\" name=\"".$name."\" value=\"".$value."\"/>\n";
	}
	
	function fPassword($head,$name,$size = 0,$max = 0,$value = 0) {
		$this->form_string.= "\t<tr>\n\t\t<td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\"><input type=\"password\" name=\"".$name."\"";
		if($size)
			$this->form_string.= " size=\"".$size."\"";
		if($max)
			$this->form_string.= " maxlength=\"".$max."\"";
		if($value)
			$this->form_string.= " value=\"".$value."\"";
		$this->form_string.= "/></td>\n\t</tr>\n";
	}

	
	function fSelect($head,$name,$array,$selected = 0,$script = "") {
		$this->form_string.=  "\t<tr>\n\t\t<td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\"><select name=\"".$name."\" ".$script.">\n";
		$this->form_string.= "<option value=\"\">Select</option>";
		foreach($array as $key => $value) {
			$this->form_string.= "<option value=\"".$key."\"";
			if($key == $selected)
				$this->form_string.=  " selected=\"selected\"";
			$this->form_string.= ">".$value."</option>\n";
		}
		$this->form_string.= "</select></td></tr>\n";
	}

	function fSelectDB($head,$name,$res,$selected = 0,$script = "") {
		$this->form_string.=  "\t<tr>\n\t\t<td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\"><select name=\"".$name."\" ".$script.">\n";
		$this->form_string.= "<option value=\"\">Select</option>";
		while(list($key,$value) = mysql_fetch_row($res)) {
			$this->form_string.= "<option value=\"".$key."\"";
			if($key == $selected)
				$this->form_string.=  " selected=\"selected\"";
			$this->form_string.= ">".$value."</option>\n";
		}
		$this->form_string.= "</select></td></tr>\n";
	}
	
	function fTextarea($head,$name,$cols,$rows,$value = 0) {
		$this->form_string.= "\t<tr>\n\t\t<td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\"><textarea name=\"".$name."\" cols=\"".$cols."\" rows=\"".$rows."\">";
		if($value)
			$this->form_string.= $value;
		$this->form_string.= "</textarea></td></tr>\n";
	}
	
	function fRadio($head,$choice,$name,$value,$checked = 0,$open = 0,$close = 0) {
		if($open)
			$this->form_string.= "<tr><td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\">";
		$this->form_string.= $choice." <input type=\"radio\" name=\"".$name."\" value=\"".$value."\"";
		if($checked)
			$this->form_string.= " checked=\"checked\"";
		$this->form_string.= "/><br/>";
		if($close)
			$this->form_string.= "</td></tr>";
	}
	
	function fCheckbox($head,$choice,$name,$value,$checked = 0,$open = 0,$close = 0) {
		if($open)
			$this->form_string.= "<tr><td class=\"".$this->td_left_class."\">".$head."</td><td class=\"".$this->td_right_class."\">";
		$this->form_string.= $choice." <input type=\"checkbox\" name=\"".$name."\" value=\"".$value."\"";
		if($checked)
			$this->form_string.= " checked=\"checked\"";
		$this->form_string.= "/><br/>";
		if($close)
			$this->form_string.= "</td></tr>";
	}

	function fSubmit($value) {
		$this->form_string.= "<tr><td class=\"".$this->td_left_class."\">&nbsp;</td><td class=\"".$this->td_right_class."\">";
		$this->form_string.= "<input type=\"submit\" value=\"".$value."\"/></td></tr>";
	}

	function addText($string) {
		$this->form_string.= "<tr><td colspan=\"2\">".$string."</td></tr>";
	}

	function echoForm($onSubmit = 0) {
		echo "<form method=\"".$this->method."\" action=\"".$this->action."\"";
	       	if($this->form_id)
			echo " name=\"".$this->form_id."\"";
		if($onSubmit)
			echo " onSubmit=\"".$onSubmit."\"";
		echo ">\n";
		echo "<div>".$this->hidden_string."</div>\n";
		echo "<table class=\"".$this->table_class."\">\n";
		echo $this->form_string;
		echo "\n</table>\n</form>";
	}
}
?>
