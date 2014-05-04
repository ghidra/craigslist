<?php

include "db.php";


if($_GET['data'] && $_GET['act']=='add'){
	$data = json_decode($_GET['data']);
	//loop the forms
	//each for has an object that has onjects called type, term, cat, and sub
	$s='';
	$update_query='';
	$new_query='';
	$remove_query='';

	for($i = 0; $i<count($data);$i++){
		$id = intval(mysql_real_escape_string($data[$i]->id));
		$term = mysql_real_escape_string( $data[$i]->term);
		$cat = mysql_real_escape_string( $data[$i]->cat);
		$sub = mysql_real_escape_string( $data[$i]->sub);
		
		if($data[$i]->type == 'update'){
			$update_query.="UPDATE queries SET `term`='$term',`categories`='$cat',`subtown`='$sub' WHERE `key`=$id;";
			$s.= 'update';
		}else if($data[$i]->type == 'new'){
			$new_query.="INSERT INTO queries (`term`,`categories`,`subtown`) VALUES ('$term','$cat','$sub');";
			$s.= 'new';
		}
		if($data[$i]->del > 0 ){
			//$remove_query.="DELETE FROM queries WHERE `key`='$id'";
			$remove_query.="'".$id."',";
			$s.= 'remove';
		}
		//$s.=$data[$i]->term;
	}
	if(strlen($update_query)>0){
		$s.= mysql_query($update_query)or die ("I cannot connect to the database because: " . mysql_error());
	}
	if(strlen($new_query)>0){
		$s.= mysql_query($new_query)or die ("I cannot connect to the database because: " . mysql_error());
	}
	if(strlen($remove_query)>0){
		$remove_query = rtrim($remove_query, ",");
		$s.= mysql_query("DELETE FROM queries WHERE `key` in( $remove_query )")or die ("I cannot connect to the database because: " . mysql_error());
	}
	print $s;
	//print $update_query;
	//print 'recieved';
	
}

//if($_GET['data'] && $_GET['act']=='subtract'){
	//"DELETE FROM _users WHERE USER in(`name1`,`name2`,`name3`);";
//}

//INSERT INTO `craigslist`.`queries` (`key`, `term`, `categories`, `subtown`) VALUES (NULL, 'test', 'sss', 'wch');
//UPDATE `craigslist`.`queries` SET `term` = 'ssssss' WHERE `queries`.`key` =4;
//DELETE FROM `craigslist`.`queries` WHERE `queries`.`key` = 5;

?>
