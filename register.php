<?php

/**
 * Escapes HTML for output
 *
 */

session_start();

if (empty($_SESSION['csrf'])) {
  if (function_exists('random_bytes')) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  } else if (function_exists('mcrypt_create_iv')) {
    $_SESSION['csrf'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
  } else {
    $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
  }
}


function escape($html) {
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
?>

<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "accounts";
$dsn = "mysql:host=$host;dbname=$dbname";
$options = array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);
?>


<?php
if(isset($_POST["create"])){
	if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) die();
	try{
		$pw_hash = password_hash($password, PASSWORD_DEFAULT);
		$connection = new PDO($dsn, $username, $password, $options);

		$new_user = array(
			"full_name"     => $_POST['fname'],
            "email"     => $_POST['email'],
            "password"       => password_hash($_POST['password'], PASSWORD_DEFAULT)
        );

        $sql = sprintf(
                "INSERT INTO %s (%s) values (%s)",
                "users",
                implode(", ", array_keys($new_user)),
                ":" . implode(", :", array_keys($new_user))
        );

        $statement = $connection->prepare($sql);
        $statement->execute($new_user);
	} catch(PDOException $error) {
        echo $sql . "<br>" . $error->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
</head>
<body>
	  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
	  	<form class="p-5 rounded shadow" 
	  	      method="post" 
	  	      style="width: 30rem">
	  		<h1 class="text-center pb-5 display-4">CREATE</h1>
		  <div class="mb-3">
		    <label for="exampleInputEmail0" 
		           class="form-label">Full Name
		    </label>
		    <input type="text" 
		           name="fname" 
		           class="form-control" 
		           id="exampleInputEmail0" aria-describedby="emailHelp">
		  </div>
		  <div class="mb-3">
		    <label for="exampleInputEmail1" 
		           class="form-label">Email address
		    </label>
		    <input type="email" 
		           name="email"  
		           class="form-control" 
		           id="exampleInputEmail1" aria-describedby="emailHelp">
		  </div>
		  <div class="mb-3">
		    <label for="exampleInputPassword1" 
		           class="form-label">Password
		    </label>
		    <input type="password" 
		           class="form-control" 
		           name="password" 
		           id="exampleInputPassword1">
		  </div>
		  <button type="submit" 
		          class="btn btn-primary" name="create">Create
		  </button>
		  	<input name="csrf" type="hidden" value="<?php echo escape($_SESSION['csrf']); ?>">
		</form>
	  </div>
</body>
</html>