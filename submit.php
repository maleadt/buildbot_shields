<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'private.php';

$data = file_get_contents("php://input");
$build = json_decode($data);

$mysqli = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

$stmt = $mysqli->prepare("INSERT INTO builds (builder, branch, url, complete, results, time)
                          VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                          ON DUPLICATE KEY UPDATE
                              builder = VALUES(builder),
                              branch = VALUES(branch),
                              url = VALUES(url),
                              complete = VALUES(complete),
                              results = VALUES(results),
                              time = CURRENT_TIMESTAMP");
$complete = $build->complete ? 1 : 0;
$results = is_null($build->results) ? -1 : $build->results;
$stmt->bind_param("sssii", $build->builder->name,
                           $build->buildset->sourcestamps[0]->branch,
                           $build->url,
                           $complete,
                           $results);
$stmt->execute();
$stmt->close();

?>