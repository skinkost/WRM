<?php
	require('config.php');
	
	//echo "Start: " . $_POST['start'] . " End: " . $_POST['end'];
	
	$startdate = $_POST['start'];
	$enddate = $_POST['end'];
	
	try {
	
		if ($_POST["users"] != '') {
			$user = $_POST["users"];
			$query = 
			"SELECT e.ID AS ID,e.user AS user,e.date AS date,e.hours AS hours,e.customer AS customer,c.ID AS cId,c.name AS cName,u.username AS username,u.color AS color, u.ID
			 FROM events e LEFT JOIN customers c ON (e.customer = c.ID)
			 LEFT JOIN users u ON (e.user = u.ID) 
			 WHERE u.ID IN ($user) AND e.date >= '$startdate' AND e.date <= '$enddate'";
		} else {
			$query = 
			"SELECT e.ID AS ID,e.user AS user,e.date AS date,e.hours AS hours,e.customer AS customer,c.ID AS cId,c.name AS cName,u.username AS username,u.color AS color
			 FROM events e 
			 LEFT JOIN customers c ON (e.customer = c.ID) 
			 LEFT JOIN users u ON (e.user = u.ID) 
			 WHERE e.date >= '$startdate' AND e.date <= '$enddate'";
		}
	
		$sql = $db->query($query);
	   
		// Returning array
		$events = array();
		
		// Fetch results
		while ($row = $sql->fetch_assoc()) {
			$e = array();
			$e['title'] = $row['cName'] . "\n " . $row['hours'] . " h";
			$e['id'] = $row['ID'];
			$e['resourceId'] = $row['user'];
			$e['user'] = $row['user'];
			$e['date'] = $row['date'];
			$e['hours'] = $row['hours'];
			$e['customer'] = $row['cId'];
			$e['color'] = $row['color'];
			$e['rTop'] = $row['cName'];
			$e['rBottom'] = $row['hours'] . " h";
			
			// Merge the event array into the return array
			array_push($events, $e);
		}

		// Output json for our calendar
		echo json_encode($events);
		//echo $events;
		
		exit();

	} catch (PDOException $e){
		echo $e->getMessage();
	}
?>
