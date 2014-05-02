<?php

include "db.php";


if($_GET['data'] && $_GET['act']=='add'){
	$data = json_decode($_GET['data']);
	//loop the forms
	//each for has an object that has onjects called type, term, cat, and sub
	$s='';
	for($i = 0; $i<count($data);$i++){
		if($data[$i]->type == 'update'){
			$term = mysql_real_escape_string( $data[$i]->term);
			$id = mysql_real_escape_string($data[$i]->id);
			mysql_query("UPDATE queries SET term ='$term' WHERE key='$id'");
			$s.= 'update';
		}else if($data[$i]->type == 'new'){
			$s.= 'new';
		}
		$s.=$data[$i]->term;
	}
	//print 'recieved';
	print $s;
}

//INSERT INTO `craigslist`.`queries` (`key`, `term`, `categories`, `subtown`) VALUES (NULL, 'test', 'sss', 'wch');
//UPDATE `craigslist`.`queries` SET `term` = 'ssssss' WHERE `queries`.`key` =4;
//DELETE FROM `craigslist`.`queries` WHERE `queries`.`key` = 5;

?>
