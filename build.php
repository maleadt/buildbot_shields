<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'private.php';

require __DIR__ . '/vendor/autoload.php';

use PUGX\Poser\Render\SvgRender;
use PUGX\Poser\Poser;

$render = new SvgRender();
$poser = new Poser(array($render));

$colors = [
    "red" => "e05d44",
    "orange" => "fe7d37",
    "green" => "97CA00",
    "brightgreen" => "44cc11",
    "yellowgreen" => "a4a61d",
    "yellow" => "dfb317",
    "lightgrey" => "9f9f9f",
    "blue" => "007ec6",
    "pink" => "ff69b4"
];

if (!isset($_GET["builder"])) {
    echo $poser->generate('error', 'no builder', $colors["pink"], 'plastic');
    exit;
}
$builder = $_GET["builder"];

$branch = "master";
if (isset($_GET["branch"]))
    $branch = $_GET["branch"];

$mysqli = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

$stmt = $mysqli->prepare("SELECT complete, results FROM builds
                          WHERE builder = ? AND branch = ?");
$stmt->bind_param("ss", $builder, $branch);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo $poser->generate('error', 'no builds', $colors["red"], 'plastic');
} else {
    $stmt->bind_result($complete, $results);
    $stmt->fetch();
    if ($complete == 0) {
        $color = $colors["yellow"];
        $status = "in progress";        
    } else {
        switch ($results) {
            case 0:
            case 1:
                $color = $colors["green"];
                $status = "success";
                break;
            case 2:
            case 4:
                $color = $colors["red"];
                $status = "failed";
                break;
            case 6:
                $color = $colors["orange"];
                $status = "canceled";
                break;
            default:
                $color = $colors["pink"];
                $status = "unknown";
        }
    }
    echo $poser->generate('build', $status, $color, 'plastic');
}
$stmt->close();

?>