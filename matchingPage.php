<?php
	require_once("support.php");

	if (session_id() == NULL) {
		session_start();
	}

	$host = "localhost";
	$user = "dbuser";
	$dbpassword = "goodbyeWorld";
	$database = "projectship";
	$table = "userprofiles";

	$currUser = $_SESSION['currUser'];

	/* Connecting to the database */		
	$db_connection = new mysqli($host, $user, $dbpassword, $database);
	if ($db_connection->connect_error) {
		die($db_connection->connect_error);
	}

	$profilesArray = [];
	$usersArray = [];

	$query = "select * from $table where email != \"$currUser\"";
	$result = $db_connection->query($query);
	if ($result) {
		$num_rows = $result->num_rows;
		if ($num_rows === 0) {
			echo "Empty Table<br>";
		} else {
			for ($row_index = 0; $row_index < $num_rows; $row_index++) {
				$result->data_seek($row_index);
				$row = $result->fetch_array(MYSQLI_ASSOC);

				$dbEmail = $row['email'];

				$usersArray[$row_index] = $dbEmail;

				$firstName = $row['firstname'];
				$lastName = $row['lastname'];
				$gender = $row['gender'];
				$major = $row['major'];
				$grade = $row['grade'];
				$numPartners = $row['numpartners'];
				$comments = $row['comments'];

				$profilesArray[$dbEmail] = "<h1>Name: ".$firstName." ".$lastName."</h1> Gender: ".$gender."<br> Major: ".$major."<br> Grade: ".$grade."<br> About: ".$comments."<br>";
			}
		}
		$result->close();
	}  else {
		die("Retrieval failed: ". $db_connection->error);
	}

	$currProfile = $usersArray[0];

	$userEmailsJSON = json_encode($usersArray);
	$profilesJSON = json_encode($profilesArray);

	//print_r($profilesArray);



	$body = <<<BODY
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
		.card {
		  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
		  max-width: 500px;
		  margin: auto;
		  text-align: center;
		  font-family: arial;
		  background-color: white;
		}

		button {
		  border: none;
		  outline: 0;
		  display: inline-block;
		  padding: 8px;
		  color: white;
		  background-color: #000;
		  text-align: center;
		  cursor: pointer;
		  width: 100%;
		  font-size: 18px;
		}

		button:hover, a:hover {
		  opacity: 0.7;
		}
		</style>

		<center><h1> Here are the "Matched" friends! </h1><br>

		<input type="button" id="prev" value="Previous Match">
		<input type="button" id="next" value="Next Match">
		<br><br>

		<div class="card">
			<div id="profile">
			$profilesArray[$currProfile]
			</div>
		 	<p><button>Contact</button></p>
		</div>
		

		

		<input type="button" onclick="location.href='homePage.php';" value="Home Page" /> </center>

		<script>
			"use strict";

			var profiles = $profilesJSON;
			var emails = $userEmailsJSON;
			var profileIndex = 0;

			console.log(profiles);
			console.log(emails);


			document.getElementById("next").addEventListener("click", function() {
				if (profileIndex < emails.length - 1) {
					profileIndex++;
					document.getElementById("profile").innerHTML = profiles[emails[profileIndex]];
					
				}
			});

			document.getElementById("prev").addEventListener("click", function() {
				if (profileIndex > 0) {
					profileIndex--;
					document.getElementById("profile").innerHTML = profiles[emails[profileIndex]];
				}
			});



		</script>



BODY;
	

	$db_connection->close();

	$page = generatePage($body, "Matching");
	echo $page;
?>