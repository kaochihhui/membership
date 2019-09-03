<?php
include_once "mama_queries2.php";

$servername = "localhost";
$dbname = "diego_mama_diario";

$username = "diego";
$password = "diego";

$api_relative_path = "/MamaDiarioApp";
$site_relative_path = "/MamaDiarioWebsite";

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/local_session_data');

$api_absolute_path = $_SERVER['DOCUMENT_ROOT'] . $api_relative_path;
$site_absolute_path = $_SERVER['DOCUMENT_ROOT'] . $site_relative_path;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) { lg("Connection failed: " . mysqli_connect_errno() . " " . mysqli_connect_error()); die(); } 
else { lg("Connection Success"); }

function int_is_user_authorized($conn, $user_id, $user_pass)
{
	$rows = sqls_user_check($conn, $user_id, $user_pass);
	if (count($rows) == 0) { lg("User NOT authorized!"); };
	return count($rows);
}

?>