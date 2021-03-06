<?php

	require('config.php');
	require('functions.php');
	require('settings.php');
	
	

	switch ($_POST["type"]) {
		case "event_move":
			$q_user = $_POST["user"];		
			$q_id = $_POST["id"];
			$q_date = $_POST["date"];
									
			$query = "UPDATE events SET date = '$q_date' WHERE id = '$q_id'";
				
			$sql = $db->query($query);
			
			feed_log($db, $q_user, "flyttade timmar","Timmar: $q_id till $q_date");
			
			break;
		case "event_add":
							
			$q_user = $_POST["user"];
			$q_date = $_POST["date"];
			$q_hours = $_POST["hours"];
			$q_cust = $_POST["customer"];
			
			$sql1 = $db->query("select c.ID, r.rate as rate from customers c JOIN rates r ON r.ID = c.rate WHERE c.ID = $q_cust");
			
			while ($rates = $sql1->fetch_assoc()) {
				$q_rate = $rates['rate'];
			} 
			
			//echo "User:" . $q_user . ", Rate:" . $q_rate;
			
			$query = "INSERT INTO events (user,date,hours,customer,rate) VALUES ('$q_user','$q_date','$q_hours','$q_cust','$q_rate')";
				
			$sql = $db->query($query);
			
			feed_log($db, $q_user, "lade till timmar","Timmar: $q_hours på kund $gCustomers[$q_cust]['name']");
			
			break;
		case "event_remove":
			$q_user = $_POST["user"];
			$q_id = $_POST["id"];
			
			$query = "DELETE FROM events WHERE id = $q_id";
			
			$sql = $db->query($query);
			
			feed_log($db, $q_user, "tog bort timmar","");
			
			break;
		case "user_add":
			$q_username = $_POST["username"];
			$q_name = $_POST["name"];
			$q_role = $_POST["role"];
			$q_email = $_POST["email"];
			$q_password = md5($_POST["password"]);
			$q_company = $_POST["company"];
			$q_color = $_POST["color"];
			
			
			$q_user = $_POST["user"];
			
			$query = "INSERT INTO users (username, name, role, email, password, company, color)
			VALUES ('$q_username','$q_name','$q_role','$q_email','$q_password', '$q_company', '$q_color')";
			$sql = $db->query($query);
						
			feed_log($db, $q_user, "lade till en användare","Användare: $q_name");
			
			
			break;
		case "user_update":
			$q_username = $_POST["username"];
			$q_password = $_POST["password"];
			$q_name = $_POST["name"];
			$q_email = $_POST["email"];
			$q_company = $_POST["company"];
			$q_color = $_POST["color"];
			$q_image = $_POST["image"];
			$q_description = $_POST["description"];
			
			$q_user = $_POST["user"];
			
			if ($q_password == "******") {
				$query = "UPDATE users SET username = '$q_username', name = '$q_name', email = '$q_email', company = '$q_company', color = '$q_color', description = '$q_description', profileImage = '$q_image' WHERE ID = '$q_user'";
			} else {
				$q_password = md5($q_password);
				$query = "UPDATE users SET username = '$q_username', password = '$q_password', name = '$q_name', email = '$q_email', company = '$q_company', color = '$q_color', description = '$q_description', profileImage = '$q_image' WHERE ID = '$q_user'";
			}
						
			$sql = $db->query($query);
			
			if ($db->error) {
				echo "MySQL error $db->error <br> Query:<br> $query" . $db->errno;
			}
			
			feed_log($db, $q_user, "uppdaterade sin användarprofil","");
			
			break;
		case "customer_update":
			$q_name = $_POST["name"];
			$q_address = $_POST["address"];
			$q_phone = $_POST["phone"];
			$q_email = $_POST["email"];
			$q_status = $_POST["status"];
			$q_comment = $_POST["comment"];
			$q_user = $_POST["user"];
			$q_id = $_POST["cID"];
			$q_rate = $_POST["rate"];
			$cStart = $_POST["cStart"];
			$cEnd = $_POST["cEnd"];
			
			$query = "UPDATE customers SET name = '$q_name', address = '$q_address', phone = '$q_phone', email = '$q_email', status = '$q_status', comment = '$q_comment', rate = '$q_rate' WHERE ID = '$q_id'";
			
			$sql = $db->query($query);
			
			$last_id = $db->insert_id;
			
			$query2 = "INSERT INTO contracts (customer, start, end) VALUES('$last_id', '$cStart', '$cEnd') ON DUPLICATE KEY UPDATE start = '$cStart', end = '$cEnd'";
			
			$sql = $db->query($query2);

			feed_log($db, $q_user, "ändrade en kund","Kund: $q_name");
			
			break;
		case "customer_add":
		
			$q_name = $_POST["name"];
			$q_address = $_POST["address"];
			$q_phone = $_POST["phone"];
			$q_email = $_POST["email"];
			$q_status = $_POST["status"];
			$q_comment = $_POST["comment"];
			$q_user = $_POST["user"];
			$q_rate = $_POST["rate"];
			$cStart = $_POST["cStart"];
			$cEnd = $_POST["cEnd"];
			
			$query = "INSERT INTO customers (name, address, phone, email, status, comment, rate) VALUES ('$q_name','$q_address','$q_phone', '$q_email','$q_status','$q_comment', '$q_rate')";
			
			$sql = $db->query($query);
			
			$query2 = "INSERT INTO contracts (customer, start, end) VALUES('$q_id', '$cStart', '$cEnd') ON DUPLICATE KEY UPDATE start = '$cStart', end = '$cEnd'";
			
			$sql = $db->query($query2);		
						
			feed_log($db, $q_user, "lade till en kund","Kund: $q_name");
			
			break;
			
		case "customer_remove":
			$q_user = $_POST["user"];
			$q_id = $_POST["cID"];
			
			$query = "DELETE FROM customers WHERE id = $q_id";
			
			$sql = $db->query($query);
			
			feed_log($db, $q_user, "tog bort en kund","Kund: $gCustomers[$q_id]");
			
			break;
			
		default:
			break;
	}
	
?>