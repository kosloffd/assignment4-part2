<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-Type: text/html');
include 'password.php';
?>

<!DOCTYPE html>
<title>Video Store</title>

<form action="StoreFront.php", method="POST">
	<fieldset>
		<label>Video Name:</label>
		<input name="name", type="text">
		<label>Category:</label>
		<input name="category", type="text">
		<label>Length:</label>
		<input name="length", type="number">
		<button type="submit">Add Video</submit>
	</fieldset>
</form>


<?php
$mysqli = new mysqli('oniddb.cws.oregonstate.edu', 'kosloffd-db', $pass, 'kosloffd-db');
if($mysqli->connect_errno)
{
	echo "Connection error" . $mysqli->connect_errno . " " . $mysqli->connect_error; 
}
else
{
	echo "Connected!";
}

					//Prepare statements on this same page that is like
					//"INSERT INTO movies (name, category, length, available) VALUES(?, ?, ?, 'true')									-Add video btn									--Add Video btn
						//Activated if (isset$_POST["name"]) || isset$_POST["category"]) || isset$_POST(["length"])) 

if(isset($_POST["name"]) || isset($_POST["category"]) || isset($_POST["length"]))
{
	
	//add more validation tests here, like if any of the other values are entered, but the name isn't echo an
	//error message. Also need to check if length is an integer. Also need to check if name is unique, but maybe later.

	$addName = $_POST["name"];
	$addCat = $_POST["category"];
	$addLength = $_POST["length"];
	if(!($stmt = $mysqli->prepare("INSERT INTO film (name, category, length, rented) VALUES (?, ?, ?, 'false')")))
	{
		echo "Couldn't prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->bind_param("ssi", $addName, $addCat, $addLength))
	{
		echo "Couldn't bind parameters: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->execute())
	{
		echo "Couldn't execute 'Add Video' statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
}
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
if(!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM film")))
{
	echo "Couldn't prepare statement: (" . $mysqli->errno.") " . $mysqli->error;
}
if(!$stmt->execute())
{
	echo "Couldn't execute return statement: (" . $mysqli->errno . ") " . $mysqli->error;
}

$dbName = NULL;
$dbCategory = NULL;
$dbLength = NULL;
$dbRented = NULL;

if(!$stmt->bind_result($dbName, $dbCategory, $dbLength, $dbRented))
{
	echo "Couldn't bind return results: (" . $mysqli->errno . ") " . $mysqli->error;
}

echo "<table>";
while($stmt->fetch())
{
	echo "<tr> <td>$dbName <td>$dbCategory <td>$dbLength <td>$dbRented";
}
	echo "</table>";
$stmt->close();
									//The filter button will then be pretty much a page refresh button  	 													--filter button
								//to create table, just use $mySqli->query to get data and echo the HTML table rows
								//You will need to get the values for the category (+ an "All Movies" value) from the 
								//server with GROUP BY category or DISTINCT

?>