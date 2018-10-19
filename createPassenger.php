

<?php
        // Make sure all elements are nonempty.
	$ssn = $_POST['passenger_ssn'];
	$f_name = $_POST['f_name'];
	$m_name = $_POST['m_name'];
	$l_name = $_POST['l_name'];
	$error = "Success!";

	if ( strlen($ssn) == 0 or strlen($f_name) == 0 or strlen($l_name) == 0){
		// tuple is empty.
		$error = "Social Security Number, First Name, or Last Name has nothing inside the fields.";
		header('Location: insertPass.php?error='.$error);
		exit();
	}

	// check m_name
	if ( strlen($m_name) == 0 ){
		$m_name = "";
	}

	$pattern = '/^[A-Za-z]+/';

	// check ssn
	if (!preg_match('(^\d{3}-?\d{2}-?\d{4}$|^XXX-XX-XXXX$)', $ssn, $matches)){
		$error = "Social Security Doesn't Match Pattern: (yyy-yy-yyyy).";
		header('Location: insertPass.php?error='.$error);
		exit();
	}

	if (!preg_match($pattern, $f_name)){
		$error = $f_name . " is not a valid first name.";
                header('Location: insertPass.php?error='.$error);
                exit();
	}

	if (strlen($m_name) >0){
		if(!preg_match($pattern, $m_name)){
                	$error = $m_name . " is not a valid middle name.";
                	header('Location: insertPass.php?error='.$error);
                	exit();
		}
        }

	if (!preg_match($pattern, $l_name)){
                $error = $l_name . " is not a valid last name.";
                header('Location: insertPass.php?error='.$error);
                exit();
        }

	// insert cool query stuff here rip
	$db_file = './myDB/airport.db';

	try {
		// open connection to the database
		$db = new PDO('sqlite:' . $db_file);

		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query_str = $db->prepare("INSERT into passengers (ssn, f_name, m_name, l_name) VALUES (:ssn, :f_name, :m_name, :l_name);");

		$query_str->bindParam("ssn", $ssn);
		$query_str->bindParam("f_name", $f_name);
		$query_str->bindParam("m_name", $m_name);
		$query_str->bindParam("l_name", $l_name);

		// execute the insertion
		$query_str->execute();
	} catch(PDOException $e) {
            $error = "Social Security Number Exists! Failed to add ssn!";
       	    header('Location: insertPass.php?error='.$error);
            exit();
	}

	header('Location: showPassengers.php?success='.$error);
	exit();

?>
