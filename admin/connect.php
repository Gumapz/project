<!-- dbCon.php -->
<?php 
function connect($flag=TRUE){
	$servername = "sql3.freesqldatabase.com";
	$username = "sql3739572";
	$password = "vESzNNev4k";
	$dbName = "sql3739572";

	// Create connection
	if($flag){
		$conn = new mysqli($servername, $username, $password,$dbName);
	}else{
		$conn = new mysqli($servername, $username, $password);
	}
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: $conn->connect_error");
	} 
	//echo "Connected successfully";
	
	return $conn;
}

?>