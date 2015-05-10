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

<!--Form with no elements to POST data from buttons-->
<form action="StoreFront.php", id="main" method="POST"></form>

<?php
$mysqli = new mysqli('oniddb.cws.oregonstate.edu', 'kosloffd-db', $pass, 'kosloffd-db');
if(!$mysqli || $mysqli->connect_errno)
{
	echo "Connection error" . $mysqli -> connect_errno . " " . $mysqli -> connect_error; 
}

//If there is a POST to the page with 
if(isset($_POST["name"]) || isset($_POST["category"]) || isset($_POST["length"]))
{
	//Ensure the required Name field is filled
	if($_POST["name"] == "")
	{
		echo "<h3>You must enter a video name.</h3><br>";
		$dataValidated = false;
	}
	
	//Ensure the Length is a number if the the field is populated
	if((!$_POST["length"] == "") && !is_numeric($_POST["length"]))
	{
		echo "<h3>You must enter a number for the length.</h3><br>";
		$dataValidated = false;
	}
	else if(intval($_POST["length"]) < 0)
	{
		echo "<h3>You must enter a positive number for the length.</h3><br>";
		$dataValidated = false;
	}

	if($dataValidated != false)
	{
		$dataValidated = true;
		//Check for a duplicate title in the database
		if(!($stmt = $mysqli->prepare("SELECT name FROM film")))
		{
			echo "Couldn't prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if(!$stmt->execute())
		{
			echo "Couldn't execute return statement: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		$dbName = NULL;
		if(!$stmt->bind_result($dbName))
		{
			echo "Couldn't bind return results: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		while($stmt->fetch())
		{
			if($dbName == $_POST["name"])
			{
				echo "That Video can't be added: the name already exists.";
				$dataValidated = false;
			}
		}
		$stmt -> close();
		
		if($dataValidated == true)
		{
			$addName = $_POST["name"];
			$addCat = $_POST["category"];
			$addLength = $_POST["length"];
			if(!($stmt = $mysqli->prepare("INSERT INTO film (name, category, length, rented) VALUES (?, ?, ?, false)")))
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
	}
}

if(isset($_POST["deleteAll"]) && $_POST["deleteAll"] == true)
{
	if (!$mysqli->query("DELETE FROM film"))
	{
    echo "Delete All failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
}

//Checkout a movie from the list
if(isset($_POST["checkoutMovie"]))
{
	$movieID = $_POST["checkoutMovie"];
	if(!($stmt = $mysqli->prepare("UPDATE film SET rented = 1 WHERE id = ?")))
	{
		echo "Couldn't prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->bind_param("i", $movieID))
	{
		echo "Couldn't bind parameters: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->execute())
	{
		echo "Couldn't execute 'Checkout Video' statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
}

//Return a movie
if(isset($_POST["returnMovie"]))
{
	$movieID = $_POST["returnMovie"];
	if(!($stmt = $mysqli->prepare("UPDATE film SET rented = 0 WHERE id = ?")))
	{
		echo "Couldn't prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->bind_param("i", $movieID))
	{
		echo "Couldn't bind parameters: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->execute())
	{
		echo "Couldn't execute 'Return Video' statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
}

//Delete a movie
if(isset($_POST["deleteMovie"]))
{
	$movieID = $_POST["deleteMovie"];
	if(!($stmt = $mysqli->prepare("DELETE FROM film WHERE id = ?")))
	{
		echo "Couldn't prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->bind_param("i", $movieID))
	{
		echo "Couldn't bind parameters: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(!$stmt->execute())
	{
		echo "Couldn't execute 'Delete Video' statement: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
}

								//Prepare a statement to create the table of movies WITH FILTER and refresh the page							--create table
								//It will be like: echo <table>; while() echo <tr>; while ...->fetch echo <td>$value;  
									//Should activate EVERY TIME, that way the page can just refresh and it will update the displayed table
if(!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM film")))
{
	echo "Couldn't prepare statement: (" . $mysqli->errno.") " . $mysqli->error;
}
if(!$stmt->execute())
{
	echo "Couldn't execute return statement: (" . $mysqli->errno . ") " . $mysqli->error;
}

$movieID = NULL;
$dbName = NULL;
$dbCategory = NULL;
$dbLength = NULL;
$dbRented = NULL;

echo "<table>";
echo "<tr> <td> <td>Title <td>Category <td>Length <td>Availability";
if(!$stmt->bind_result($movieID, $dbName, $dbCategory, $dbLength, $dbRented))
{
	echo "Couldn't bind return results: (" . $mysqli->errno . ") " . $mysqli->error;
}
while($stmt->fetch())
{
	if($dbRented == 0){$dbRented = "Available";}
	else{$dbRented = "Checked Out";}
	echo "<tr><td><button form=\"main\" name=\"deleteMovie\" value=\"$movieID\">Delete</button>
	 <td>$dbName <td>$dbCategory <td>$dbLength min <td>$dbRented";
	if($dbRented == "Available")
		{echo "<td><button form=\"main\" name=\"checkoutMovie\" value=\"$movieID\">Checkout Movie</button>";}
	else
		{echo "<td><button form=\"main\" name=\"returnMovie\" value=\"$movieID\">Return Movie</button>";}
}
if($movieID == NULL) {echo "<tr><td><td>There are no titles to display";}
//Show the Delete All button only if there is data in the table
echo "</table>";
if(!($movieID == NULL)) {echo "<button form=\"main\" name=\"deleteAll\" value=\"true\">Delete All Movies</button>";}

$stmt->close();
									//The filter button will then be pretty much a page refresh button  	 													--filter button
								//to create table, just use $mySqli->query to get data and echo the HTML table rows
								//You will need to get the values for the category (+ an "All Movies" value) from the 
								//server with GROUP BY category or DISTINCT

?>