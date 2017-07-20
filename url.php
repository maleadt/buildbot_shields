<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'private.php';

if (!isset($_GET["builder"]))
    trigger_error("no builder specified", E_USER_ERROR);
$builder = $_GET["builder"];

$branch = "master";
if (isset($_GET["branch"]))
    $branch = $_GET["branch"];

$mysqli = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

$stmt = $mysqli->prepare("SELECT url FROM builds
                          WHERE builder = ? AND branch = ?");
$stmt->bind_param("ss", $builder, $branch);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    trigger_error("no builds found", E_USER_ERROR);
} else {
    $stmt->bind_result($url);
    $stmt->fetch();
    header('Location: '.$url);
}
$stmt->close();

?>
