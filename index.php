<html>
<head>
<title>craiglist</title>
 
<!---
its not getting the categories right when seding it to mysql

the pytonh script is breaking now, for whatever damn reason, som inicode crap
-->

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
function check_include_box(id){
    var form_enabled = document.getElementById("include_"+id);
    if(!form_enabled.checked){
        form_enabled.checked = true;
    }
}
function add_cat(id){
    var mylist=document.getElementById("cat_"+id);
    //if this is already visible, hide it.
    var element = document.getElementById("cat_row_"+id+"_"+ mylist.options[mylist.selectedIndex].text );
    if(element.style.display == 'none'){
        element.style.display = 'block';
    }else{
        element.style.display = 'none';
    }
    //set it back to the default value, helps if we want to remove the last category
    mylist.selectedIndex = 0;
    check_include_box(id);
}
function submit_forms(){
    s="";
    var my_forms = document.getElementById("forms_container");
    var blocks = my_forms.childNodes;
    var json = [];
    for(var i = 0; i < blocks.length; i++) {
        var id = blocks[i].getAttribute("id").split("_").pop();
        var form = document.getElementById("searchquery_"+id);
        var form_enabled = document.getElementById("include_"+id);
        var delete_form = document.getElementById("delete_"+id);

        if(form_enabled.checked){//only deal with the checked ones
            var entry = {
                type : "",
                id : -1,
                term : "",
                cat : "",
                sub : "",
                del : 0
            };
            var update_type = form.className;
            entry.type = update_type.split("_")[1];
            entry.id = id;
            for (var f = 0; f< form.elements.length; f++){
                //s+=form[f].id+":";
                switch(form[f].id){
                    case "term_"+id:
                       // s+=form[f].value;
                       entry.term = form[f].value;
                        break;
                    case "cat_"+id:
                        cat_array = [];
                        table = document.getElementById("cat_table_"+id);
                        for(var t = 0; t<table.rows.length; t++ ){
                            //s+=table.rows[t].style.display;
                            if(table.rows[t].style.display != 'none'){
                                cat_array.push(table.rows[t].cells[1].innerHTML);
                            }
                        }
                        //s+='fuck';
                        //s+=cat_array.toString();
                        entry.cat = cat_array.toString();
                        break;
                    case "sub_"+id:
                        entry.sub = form[f].value;
                        //s+=form[f].value;
                        break;
                    case "delete_"+id:
                        if(delete_form.checked){
                            entry.del = 1;
                        }
                        break;
                }
            }
            json[id] = entry;
            //s+=form_enabled.checked;
        }
        //s+="!!";
        //searchquery_
    }
    post_forms( JSON.stringify(json) );
    //alert(s);
}

function post_forms(data){
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            //alert('added search terms');
            alert(xmlhttp.responseText);
            location.reload();
        }
    }
    xmlhttp.open("GET","dbi.php?act=add&data="+data,true);
    xmlhttp.send();

}

</script>

</head>
<body>


<?php

$abr = array("sss","ata","baa","bar","bia","boo","bka","bfa","sya","zip","fua","foa","has","jwa","maa","rva","sga","tia","tla","waa","ppa","ara","sna","pta","haa","ema","moa","cla","cba","ela","gra","gms","hva","msa","pha","taa","vga");
$wle = array("everything","antiques","baby and kids","barter","bikes","boats","books","business","computer","free stuff","furniture","general","household","jewlery","materials","rv and camp","sports","tickets","tools","wanted","appliances","art and crafts","atv/utv/sno","autoparts","beauty and health","cds dvds vhs","cell phones","clothes and acc","colletibles","electronics","farm and garden","garage sale","heavy equipment","music instruments","photo equipment","toys and games","video games");

include "db.php";
/*$dbhost = '127.0.0.1';//:3307
$dbuser = 'root';
$dbpass = 'eimajimi';
define('dbname','craigslist');

// make a connection to mysql here
$db = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("I cannot connect to the database because: " . mysql_error());
mysql_select_db (dbname) or die ("I cannot select the database '$dbname' because: " . mysql_error());
*/
//$db = new PDO('sqlite:/var/db/craigslist.db');
//$db=mysqli_connect("localhost","root","eimajimi","craigslist");

function query_block($id,$term,$categories,$sub,$update = "update",$include = false){
    global $abr,$wle;

    echo "<div class=\"searchquery\" id =\"query_".$id."\">";
    echo "<div class=\"searchquery_key\">".$id."</div>";

    echo "<form id=\"searchquery_".$id."\" class=\"searchquery_".$update."\">";

    echo "<input type=\"checkbox\" id=\"include_".$id."\" value=\"include\" ".($include?'checked':'').">include<br>";
    echo "<input type=\"text\" id=\"term_".$id."\" value=\"".$term."\" size=\"11px\" onchange =\"check_include_box(".$id.")\">".''."<br>";

    $cats = split(',',$categories);

    echo "<br><select id=\"cat_".$id."\" onchange=\"add_cat(".$id.")\">";
    echo "<option value=\"none\">select catergories</option>";
    for ($i=0; $i<count($wle); $i++){
        echo "<option value=\"".$wle[$i]."\">".$wle[$i]."</option>";
    }
    echo "</select><br>";
   
    echo "<table id=\"cat_table_".$id."\">";
    for ($i=0; $i<count($abr); $i++){
        echo "<tr id=\"cat_row_".$id."_".$wle[$i]."\"";
        if( in_array($abr[$i],$cats) != False ){
            echo " style = \"display:block\"><td>";
        }else{
            echo " style = \"display:none\"><td>";
        }
        echo $wle[$i]."<td style = \"display:none\">".$abr[$i]."</td></td></tr>";
    }
    echo "</table>";
    
    echo "<br><input type=\"text\" id=\"sub_".$id."\" value=\"".$sub."\" size=\"4px\" onchange =\"check_include_box(".$id.")\">sub-city</br>";
    if($update == "update"){
        echo "<input type=\"checkbox\" id=\"delete_".$id."\" value=\"delete\" >delete";
    }
    echo "</form></div>";
}
function submit_button(){
    return "<button type=\"button\" onclick = \"submit_forms()\">Submit</button><br>";
}

if($_GET['p']=='search'){//just look at the search stuff

    echo submit_button();
    echo "<div id=\"forms_container\">";

    $result = mysql_query('SELECT * FROM queries');
    $count = 0;
    while ($row = mysql_fetch_array($result)) {
        query_block( $row["key"],$row["term"],$row["categories"],$row["subtown"] );
        if($row["key"]>$count){
            $count = $row["key"];
        }
        //$count++;
    }

    $n_add = ($_GET['n'] ? $_GET['n'] : 1 );
    for($i=0; $i<$n_add; $i++){
        query_block( $count+$i+1, '', 'sss', 'wch','new');
    }

    echo "</div>";
    echo submit_button();


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
