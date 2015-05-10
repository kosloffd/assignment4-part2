<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-Type: text/html');
?>

<!DOCTYPE html>
<title>Video Store</title>

<?php
//HTML form, 
		//submit button connects by sending POST or GET data to videoManagement.php or same page with title names and everything

//Create Db at phpMyAdmin on server
		//Any reason NOT to?
//Prepare statements on this same page that is like
//"INSERT INTO movies (name, category, length, available) VALUES(?, ?, ?, 'true')									-Add video btn									--Add Video btn
	//Activated if (isset$_POST["name"]) || isset$_POST["category"]) || isset$_POST(["length"])) 
	//-decision here:check at this point for invalid values?
//Prepare another statement ("DROP TABLE movies")																									--Delete all videos btn
	//Activated if (isset$_POST["deleteAll"]) && $_POST["deleteAll"]) = "true")
//Prepare a statement "DELETE FROM movies WHERE id = ?"																						--Delete video btn
	//Activated if (isset$_POST["delete"]) -it switches in the "delete" key value 
//Prepare a statement "UPDATE movies SET available = ? WHERE id = ?"															--Checkout btn
	//Activate if (isset$_POST["checkoutMovie"])

//Prepare a statement to create the table of movies WITH FILTER and refresh the page							--create table
//It will be like: echo <table>; while() echo <tr>; while ...->fetch echo <td>$value;  
	//Should activate EVERY TIME, that way the page can just refresh and it will update the displayed table
	//The filter button will then be pretty much a page refresh button  	 													--filter button
//to create table, just use $mySqli->query to get data and echo the HTML table rows
//You will need to get the values for the category (+ an "All Movies" value) from the 
//server with GROUP BY category

?>


