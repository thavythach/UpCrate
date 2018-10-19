<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../../../../favicon.ico">

    <!-- CSS -->
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <style>
       body { padding-top: 70px; }
    </style>

    <title>SQL Query Page</title>
  </head>
  <body>

<header>
      <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="#">TeamCorruptDisk</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="\~ubuntu">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="login.html">Login <span class="sr-only">(current)</span></a>
            </li>
          </ul>
          <form class="form-inline mt-2 mt-md-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
          </form>
        </div>
      </nav>
    </header>

<main role="main">
<div class="container">
	<form action="", method="post">
	<div class="form-group">
		<label for="SQL Query">SQL Query</label>
		<textarea class="form-control" rows="5" name="SQL" id="SQL"></textarea>
	</div>
<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Submit</button>

<h2 style="padding-top: 10px">Result:</h2>
<p>
    <?php

        //path to the SQLite database file
        $db_file = './myDB/airport.db';

        try {
            //open connection to the airport database file
            $db = new PDO('sqlite:' . $db_file);

            //set errormode to use exceptions
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 	    //return all passengers, and store the result set
            $query_str = $db->prepare(":SQL");
            $query_str->bindParam("SQL", $_POST['SQL']);
	    // prepare gives back an object, calling bindParam lets it identify the target parts
	    // of the statement to replace
	    $query_str->execute();
            //loop through each tuple in result set and print out the data
            //ssn will be shown in blue (see below)
            foreach($query_str as $tuple) {
                 echo "<font color='blue'>$tuple[ssn]</font> $tuple[f_name] $tuple[m_name] $tuple[l_name]<br/>\n";
            }

            //disconnect from db
            $db = null;
        }
        catch(PDOException $e) {
            die('Exception : '.$e->getMessage());
        }
    ?>

</p>
</div>
</main>

</body>
</form>
</html>
