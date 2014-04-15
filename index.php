<html>
<head>
<title>craiglist</title>
 
<style type="text/css">
	body{
    	font-family: Georgia, "Times New Roman",Times, serif;
    	color: purple;
    	background-color: #d8da3d 
    }
    .listing,.searchquery{
    	width:400px;
    	font-size:16px;
    	margin-top:20px;
    	margin-left:100px;
        border-style:dotted;
        border-width:1px;
        border-color:black;
    }
    .listingtitle{
    	clear:both;
    }
    .listingimg{
    	max-width:100px;
    	max-height:100px;
    	margin-right:8px;
    	float:left;
    }
    .listingdesc{
    	font-size:12px;
    }
    .listingtags{
    	clear:left;
    	float:left;
    	font-size:12px;
    }
    .listingtag{
    	float:left;
    	margin-right:8px;
    }
    .listingfound{
    	float:right;
    	font-size:12px;
    }
</style>

<script>
function add_cat(cat){
    var mylist=document.getElementById("cat_"+cat);
    document.getElementById("cat_row_"+cat+"_"+ mylist.options[mylist.selectedIndex].text ).style.display = 'block';
}
</script>

</head>
<body>


<?php

$abr = array("sss","ata","baa","bar","bia","boo","bka","bfa","sya","zip","fua","foa","has","jwa","maa","rva","sga","tia","tla","waa","ppa","ara","sna","pta","haa","ema","moa","cla","cba","ela","gra","gms","hva","msa","pha","taa","vga");
$wle = array("everything","antiques","baby and kids","barter","bikes","boats","books","business","computer","free stuff","furniture","general","household","jewlery","materials","rv and camp","sports","tickets","tools","wanted","appliances","art and crafts","atv/utv/sno","autoparts","beauty and health","cds dvds vhs","cell phones","clothes and acc","colletibles","electronics","farm and garden","garage sale","heavy equipment","music instruments","photo equipment","toys and games","video games");

if (!isset($_SESSION)) {
    session_start();
}
$dbhost = '127.0.0.1';//:3307
$dbuser = 'root';
$dbpass = 'eimajimi';
define('dbname','craigslist');

// make a connection to mysql here
$db = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("I cannot connect to the database because: " . mysql_error());
mysql_select_db (dbname) or die ("I cannot select the database '$dbname' because: " . mysql_error());
//$db = new PDO('sqlite:/var/db/craigslist.db');
//$db=mysqli_connect("localhost","root","eimajimi","craigslist");

if($_GET['p']=='search'){//just look at the search stuff

    $result = mysql_query('SELECT * FROM queries');
    //$count = 0;
    while ($row = mysql_fetch_array($result)) {
        echo "<div class=\"searchquery\" id =\"query_".$count."\">";
        echo "<div class=\"searchquery_key\">".$row["key"]."</div>";
        echo "<div class=\"include_".$count."\"><input type=\"checkbox\" id=\"include_".$count."\" value=\"include\">include</div>";

        echo "<form id=\"searchquery_".$count."\" ><input type=\"text\" id=\"term_".$count."\" value=\"".$row["term"]."\" size=\"11px\">".''."<br>";

        $cats = split(',',$row["categories"]);

        echo "<br><select id=\"cat_".$row["key"]."\" onchange=\"add_cat(".$row["key"].")\">";
        echo "<option value=\"none\">select catergories</option>";
        for ($i=0; $i<count($wle); $i++){
            echo "<option value=\"".$wle[$i]."\">".$wle[$i]."</option>";
        }
        echo "</select><br>";
       
        echo "<table id=\"cat_table_".$row["key"]."\">";
        for ($i=0; $i<count($abr); $i++){
            echo "<tr id=\"cat_row_".$row["key"]."_".$wle[$i]."\"";
            if( in_array($abr[$i],$cats) != False ){
                echo " style = \"display:block\"><td>";
                //echo $wle[$i] . "&nbsp";
            }else{
                echo " style = \"display:none\"><td>";
            }
            echo $wle[$i]."</td></tr>";
        }
        echo "</table>";
        
        echo "<br><input type=\"text\" id=\"sub_".$row["key"]."\" value=\"".$row["subtown"]."\" size=\"4px\">sub-city";
        echo "</form></div>";
        //$count++;
        //echo $count($cats);
        //echo $row["categories"];
    }

}else{//show the listings

    $result = mysql_query('SELECT * FROM listings ORDER BY found DESC');
    $update_time = "";


    while ($row = mysql_fetch_array($result)) {

    	echo "<div class=\"listing\"><a href=".$row["url"]." ><div class=\"listingtitle\">";

    	if ($row["new"] == 1) 
    	{ 
    		echo "*"; 
    	}

    	echo $row["title"]."</div></a>";

    	if (strlen($row["imgurl"])){
    		echo "<img class=\"listingimg\" src=\"".$row["imgurl"]."\">";
    	}
     
    	echo "<div class=\"listingdesc\">".$row["text"]."</div>";

    	echo "<div style=\"clear:both;\"></div>";
    	echo "<div class=\"listingfound\">".$row["found"]."</div>";

    	$abrkey = array_search($row["tagcat"], $abr);
    	$wholecat = $wle[$abrkey];

    	echo "<div class=\"listingtags\"><div class=\"listingtag\">".$wholecat."</div><div class=\"listingtag\">".$row["tagterm"]."</div></div>";
    	echo "</div>";
    	echo "<div style=\"clear:both;\"></div>";

    	
    	$update_time = $row["last_update"];
    }

    //$result = $db->exec('UPDATE listings SET new = 0 WHERE new = 1');
    //$result = $db->exec('COMMIT');

    


    echo "<h4>Last Update:". $update_time ."</h4>";
}
?>

</body>
</html>
