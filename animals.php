<?php

 session_start();
					  // if ($_SESSION["id"] != "loggedIn")
						  // header("Location: index.php");

# --------------------------------------------------------------------------- #
# program: animals.php
# author:  Joshua Sopczynski
# course:  cis355 winter 2015
# purpose: template for cis355
# --------------------------------------------------------------------------- #
# input:   $_POST, or nothing
#
# processing: this program is called by a browser and excecuted on a server.
#          a. display (echo) html head and link to bootstrap css
#          b. set connection variables and verify connection
#             if successful connection ...
#             c. create table, if necessary
#             d. initialize userSelection and $_POST variables 
#             e. determine what user clicked
#             f. call function based on what user clicked
#                1. if nothing selected (first time), 
#                   display database table contents, 
#                2. if insert selected, show a form 
#                   to insert a new record in database table,
#                3. if update selected, show a form 
#                   to update an existing record in database table,
#                4. if delete selected, show a form 
#                   to delete an existing record in database table,
#                   // currently no confirmation form is displayed on delete
#                   // selecting delete immediately deletes the record
#                5. if insert completed, insert a new record in database table, 
#                   then display database table contents,
#                6. if update completed, update right record in database table, 
#                   then display database table contents,
#                7. if delete completed, delete right record in database table, 
#                   then display database table contents.
#                   // this case never occurs because delete is immediate
# 
#                functions:
#
#                checkConnect
#                createTable
#                showList
#                populateTable
#                showInsertForm
#                showUpdateForm
#                deleteRecord
#                insertRecord
#                updateRecord
#
# output:  HTML, CSS, JavaScript code 
# --------------------------------------------------------------------------- #

//session_start();

// ---------- a. display (echo) html head and link to bootstrap css -----------
// moved to section "f" to solve Post/Redirect/Get problem
// displayHTMLHead();

// ---------- b. set connection variables and verify connection ---------------
$hostname="localhost";
$username="CIS355jasopczy";
$password="password";
$dbname="CIS355jasopczy";
$usertable="Animals";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection

// ---------- if successful connection...
if($mysqli)            
{
    // ---------- c. create table, if necessary -------------------------------
	createTable($mysqli); 
	
	// ---------- d. initialize userSelection and $_POST variables ------------
	$userSelection 		= 0;
	$firstCall 			= 1; // first time program is called
	$insertSelected 	= 2; // after user clicked insertSelected button on list 
	$updateSelected 	= 3; // after user clicked updateSelected button on list 
	$deleteSelected 	= 4; // after user clicked deleteSelected button on list 
	$insertCompleted 	= 5; // after user clicked insertSubmit button on form
	$updateCompleted 	= 6; // after user clicked updateSubmit button on form
	$deleteCompleted 	= 7; // after user clicked deleteSubmit button on form
	
    $id					= $_POST['id']; // if does not exist then value is ""
	$name 				= $_POST['name'];
	$animal			 	= $_POST['animal'];
	$breed		 		= $_POST['breed'];
	$color 				= $_POST['color'];
	//$email				= $_POST['email'];
	//$password_hash 		= $_POST['password_hash'];
	//$school       	 	= $_POST['school'];
	
	
	$userlocation       = $_SESSION['location'];
	
    // ---------- e. determine what user clicked ------------------------------
	// the $_POST['buttonName'] is the name of the button clicked in browser
	$userSelection = $firstCall; // assumes first call unless button was clicked
	if( isset( $_POST['insertSelected'] ) ) $userSelection = $insertSelected;
	if( isset( $_POST['updateSelected'] ) ) $userSelection = $updateSelected;
	if( isset( $_POST['deleteSelected'] ) ) $userSelection = $deleteSelected;
	if( isset( $_POST['insertCompleted'] ) ) $userSelection = $insertCompleted;
	if( isset( $_POST['updateCompleted'] ) ) $userSelection = $updateCompleted;
	if( isset( $_POST['deleteCompleted'] ) ) $userSelection = $deleteCompleted;
	
	// ---------- f. call function based on what user clicked -----------------
	switch( $userSelection ):
	    case $firstCall: 
			//print_r($_SESSION);
		    $msg = '';
			displayHTMLHead();
		    showList($mysqli, $msg);
			break;
		case $insertSelected:
			displayHTMLHead();
		    showInsertForm($mysqli);
			break;
		case $updateSelected :
			displayHTMLHead();
		    showUpdateForm($mysqli);
			break;
		case $deleteSelected:    
			// displayHTMLHead();		
			// showDeleteForm($mysqli); // currently no form is displayed
			deleteRecord($mysqli);   // delete is immediate (no confirmation)
			displayHTMLHead();
			$msg = 'record deleted';
			showList($mysqli, $msg);
			break;
		case $insertCompleted: // updated to do Post/Redirect/Get (PRG)
		    insertRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']); // redirect
			displayHTMLHead();
			$msg = 'record inserted';
			showList($mysqli, $msg);
			break;
		case $updateCompleted:
		    updateRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']);
			displayHTMLHead();
			$msg = 'record updated';
			showList($mysqli, $msg);
			break;
		case $deleteCompleted:        // this case never occurs (see above)
		    deleteRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']);
			displayHTMLHead();
			$msg = 'record deleted';  
			showList($mysqli, $msg);
			break;
	endswitch;

} // ---------- end if ---------- end main processing ----------

# ========== FUNCTIONS ========================================================

# ---------- checkConnect -----------------------------------------------------
function checkConnect($mysqli)
{
    if ($mysqli->connect_errno) {
        die('Unable to connect to database [' . $mysqli->connect_error. ']');
        exit();
    }
}
# ---------- createTable ------------------------------------------------------
function createTable($mysqli)
{
    global $usertable;
    if($result = $mysqli->query("select id from $usertable limit 1"))
    {
        $row = $result->fetch_object();
		$id = $row->id;
        $result->close();
    }
    if(!$id)
    {
	    $sql = "CREATE TABLE Animals
		       (id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY( id ),";
	    $sql .= "id INT,";
	   // $sql .= "role enum('Teacher', 'Student', 'Peer Reviewer'),";
	   // $sql .= "secondary_role enum('Teacher', 'Student', 'Peer Reviewer'),";
	    $sql .= "name VARCHAR(25),";
	    $sql .= "animal VARCHAR(25),";
	    $sql .= "breed VARCHAR(25),";
	    $sql .= "color VARCHAR(25),";
		//$sql .= "school VARCHAR(50),";
		// $sql .= "user_id INT,";
		//$sql .= "FOREIGN KEY (location_id) REFERENCES locations (location_id),";
        //$sql .= "FOREIGN KEY (user_id) REFERENCES users (user_id)";
	    $sql .= ")";

        if($stmt = $mysqli->prepare($sql))
        {
            $stmt->execute();
        }
    }
}

# ---------- showList ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"
function showList($mysqli, $msg) 
{
	global $usertable;
	
	// display current user and location_id
	
	echo "You are logged in as user: ".$_SESSION["user"]." (".$_SESSION["id"].") ".
	    " location: ".$_SESSION["location"]."<br>";
	
    // display html table column headings
	echo 	'<div class="col-md-12">
			<form action="animals.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Animals Table</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td>
			<td>Name</td><td>Animal</td><td>Breed</td>
			<td>Color</td>						
			<td></td></tr>';

	// get count of records in mysql table
	$countresult = $mysqli->query("SELECT COUNT(*) FROM $usertable");
	$countfetch  = $countresult->fetch_row();
	$countvalue  = $countfetch[0];
	$countresult->close();

	// if records > 0 in mysql table, then populate html table, 
	// else display "no records" message
	if( $countvalue > 0 )
	{
			populateTable($mysqli); // populate html table, from mysql table
	}
	else
	{
			echo '<br><p>No records in database table</p><br>';
	}
	
	// display html buttons 
	echo    '</table>
			<input type="hidden" id="hid" name="hid" value="">
			<input type="hidden" id="uid" name="uid" value="">
			<input type="submit" name="insertSelected" value="Add an Entry" 
			class="btn btn-primary"">
			</form></div>';

	// below: JavaScript functions at end of html body section
	// "hid" is id of item to be deleted
	// "uid" is id of item to be updated.
	// see also: populateTable function
	echo "<script>
			function setHid(num)
			{
				document.getElementById('hid').value = num;
		    }
		    function setUid(num)
			{
				document.getElementById('uid').value = num;
		    }
		 </script>";
}

# ---------- populateTable ----------------------------------------------------
// populate html table, from data in mysql table
function populateTable($mysqli)
{
	global $usertable;
	
	if($result = $mysqli->query("SELECT * FROM $usertable"))
	{
		while($row = $result->fetch_row())
		{
			echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' . 
			    $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] . 
				'</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' . 
				$row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9] ;
			
            if ($_SESSION["id"]==$row[9]) {			
			echo '</td><td><input name="deleteSelected" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' . 
				$row[0] .')" />' ;
			echo '<input style="margin-left: 10px;" type="submit" 
				name="updateSelected" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
			}
		}
	}
	$result->close();
}

# ---------- showInsertForm ---------------------------------------------------
function showInsertForm ($mysqli)
{
    global $userlocation;
	// display current user and location_id
	echo "You are logged in as user: ".$_SESSION["user"].
	    " location: ".$_SESSION["location"]."<br>";
		
    echo '<div class="col-md-4">
	<form name="basic" method="POST" action="animals.php" 
	    onSubmit="return validate();">
		<table class="table table-condensed" style="border: 1px solid #dddddd; 
		    border-radius: 5px; box-shadow: 2px 2px 10px;">
			<tr><td colspan="2" style="text-align: center; border-radius: 5px; 
			    color: white; background-color:#333333;">
			<h2>Animals Insert</h2></td></tr>
			
			<tr><td>Name: </td><td><input type="edit" name="name" value="" 
			size="30"></td></tr>
			<tr><td>Animal: </td><td><input type="edit" name="animal" value="" 
			size="20"></td></tr>
			<tr><td> Breed: </td><td><input type="edit" name="breed" 
			value="" size="30"></td></tr>
			<tr><td>Color: </td><td><input type="edit" name="color" value="" 
			size="20"></td></tr>';
			
/*			
		echo '<tr><td>Location ID: </td><td><textarea style="resize: none;" 
			name="location_id" cols="40" rows="3"></textarea></td></tr>';
*/	
            // echo '<tr><td>Location ID: </td><td>';
            // echo "<select class='form-control' name = 'location_id' id='location'>";
				// if($sql_statement = $mysqli->query("SELECT * FROM locations")){
                  // while($loc_row = $sql_statement->fetch_object()){
                    // if($loc_row->location_id == $userlocation){
                      // echo"<option value='".$loc_row->location_id. 
					  // "' selected='selected'>".$loc_row->name. "</option>";
                    // }
                    // else{
                      // echo "<option value='".$loc_row->location_id. 
					  // "' >".$loc_row->name. "</option>";
                    // }
                  // }
				  // $sql_statement->close();
				  // }
				 // else
				    // echo $mysqli->error;
                // echo "</select>";
	
			echo '</td></tr>
						    		    
				<tr><td><input type="submit" name="insertCompleted" 
			    class="btn btn-success" value="Add Entry"></td>
			    <td style="text-align: right;"><input type="reset" 
			    class="btn btn-danger" value="Reset Form"></td></tr>
		        
				</table><a href="animals.php" class="btn btn-primary">
		        Display Animals Table</a></form>
				</div>';
}

# ---------- showUpdateForm --------------------------------------------------
function showUpdateForm($mysqli) 
{
	$index = $_POST['uid'];  // "uid" is id of db record to be updated 
	global $usertable;
	
	if($result = $mysqli->query("SELECT * FROM $usertable WHERE id = $index"))
	{
		while($row = $result->fetch_row())
		{
		    // display current user and location_id
	        echo "You are logged in as user: ".$_SESSION["user"].
	              " location: ".$_SESSION["location"]."<br>";
			echo '	<br>
					<div class="col-md-4">
					<form name="basic" method="POST" action="animals.php">
						<table class="table table-condensed" 
						    style="border: 1px solid #dddddd; 
							border-radius: 5px; box-shadow: 2px 2px 10px;">
							<tr><td colspan="2" style="text-align: center; 
							border-radius: 5px; color: white; 
							background-color:#333333;">
							<h2>Animals Update Form</h2></td></tr>
							
							<tr><td>Name: </td><td><input type="edit" 
							name="name" value="' . $row[3] . '" size="30">
							</td></tr>
							<tr><td>Animal: </td><td><input type="edit" 
							name="animal" value="' . $row[4] . '" size="20">
							</td></tr>
							<tr><td>Breed: </td><td><input type="edit" 
							name="breed" value="' . $row[5] . '" size="30">
							</td></tr>
							<tr><td>Color: </td><td><input type="edit" 
							name="color" value="' . $row[6] . '" size="20">
							</td></tr>';
						
							
							//<tr><td>School: </td><td><textarea 							
							//style="resize: none;" name="school" cols="40" 							
							//rows="3">' . $row[8] . '</textarea></td></tr>	

            // echo '<tr><td>Location ID: </td><td>';
            // echo "<select class='form-control' name = 'location_id' id='location'>";
				// if($sql_statement = $mysqli->query("SELECT * FROM locations")){
                  // while($loc_row = $sql_statement->fetch_object()){
                    // if($loc_row->location_id === $row[8]){
                      // echo"<option value='".$loc_row->location_id. 
					  // "' selected='selected'>".$loc_row->name. "</option>";
                    // }
                    // else{
                      // echo "<option value='".$loc_row->location_id. 
					  // "' >".$loc_row->name. "</option>";
                    // }
                  // }
				  // $sql_statement->close();
				  // }
				 // else
				    // echo $mysqli->error;
                // echo "</select>";
							
				echo '</td></tr>
				    
							<tr><td><input type="submit" name="updateCompleted" 
							class="btn btn-primary" value="Update Entry"></td>
							<td style="text-align: right;"><input type="reset" 
							class="btn btn-danger" value="Reset Form"></td></tr>
						</table>
						<input type="hidden" name="uid" value="' . $row[0] . '">
					</form>
				</div>';
		}
		$result->close();
	}
}

# ---------- deleteRecord -----------------------------------------------------
function deleteRecord($mysqli)
{
	$index = $_POST['hid'];  // "hid" is id of db record to be deleted
	global $usertable;
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id=?"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- insertRecord -----------------------------------------------------
function insertRecord($mysqli)
{
    global $id, $name, $animal, $breed, $color;
	global $usertable;
    
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("INSERT INTO $usertable (id,name,animal,breed,color) VALUES (?, ?, ?, ?, ?)"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        $stmt->bind_param('issss', $id, $name, $animal, $breed, $color);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- updateRecord -----------------------------------------------------
function updateRecord($mysqli)
{
	global $id, $name, $animal, $breed, $color; 
	global $usertable;
	$index = $_POST['uid'];  // "uid" is id of db record to be updated 
    
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("UPDATE $usertable SET name=?, animal=?, 
	    breed=?, color=? WHERE id=?"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        $stmt->bind_param('ssssi', $name, $animal, $breed, $color, $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- displayHTMLHead -----------------------------------------------------
function displayHTMLHead()
{
echo '<!DOCTYPE html>
    <html> 
	<head>
	<title>Animals</title>
	<link rel="stylesheet" 	href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" 	href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/js/bootstrap.min.js">
	</script></head><body>';
	
echo '<div class="col-md-12" style="background-color: tan; border-bottom: 
    2px solid black; box-shadow: 3px 3px 5px #888888;">
	 <a href="../cis355/landing.php"><img src="" style="margin-top: 5px;"></a>';
if ($_SESSION["user"] != '')
{
	$user = $_SESSION['user'];
	echo '<p style="font-size:18px; float: right; margin-top: 40px; 
	    margin-right: 20px;">Welcome <b>' .	$user . '</b>!</p>';
}
else
{
	echo '<form class="navbar-form navbar-right" style="margin-top: 35px;" method="POST" 
	    action="../cis355/login.php">
		<input type="text" size="9" name="username" class="form-control" placeholder="Username">
		<input type="password" size="9" name="password" class="form-control" placeholder="Password">
		<button type="submit" name="loginSubmit" class="btn btn-success">Submit</button>
	    </form>';
}
echo '<br><br></div>';
}
?>