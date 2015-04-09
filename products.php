<html lang="en">

<!--- filename: products.php, Joshua Sopczynski, cis355, 2015-03-30 --->

<head>

    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
    		<div class="row">
    			<h3>The Best Pet Store</h3>
    		</div>
			<div class="row">
				
				<!--- displays the product table ---> 
								
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>Id</th>
						  <th>Name</th>
		                  <th>Description</th>
		                  <th>Price</th>
		              
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					  
					  //displays the data from the database into the table
					  
					  session_start();
					  if ($_SESSION["id"] != "loggedIn")
						  header("Location: login.php");
					  
					   include 'database.php';
					   $pdo = Database::connect();
					   $sql = 'SELECT * FROM Products ORDER BY id ASC';
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
							   	echo '<td>'. $row['id'] . '</td>';
							   	echo '<td>'. $row['name'] . '</td>';
							   	echo '<td>'. $row['description'] . '</td>';
								echo '<td>'. $row['price'] . '</td>';
								
							   	echo '<td width=250>';
							   	echo '<a class="btn" href="readProd.php?id='.$row['id'].'">Read</a>';
							   	echo '&nbsp;';
								
								
					   }
					   Database::disconnect();
					  ?>
				      </tbody>
	            </table>
					
    	</div>
    </div> <!-- /container -->
	
	<?php
	//navigation buttons
	
	echo '<a class="btn" href="customers.php"">Customers Table</a>';
							   	echo '&nbsp;';
								
	echo '<a class="btn" href="index.php"">Home</a>';
							   	echo '&nbsp;';
	?>
	
	</body>
</html>