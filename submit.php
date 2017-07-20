<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'private.php';

# decode JSON input
$data = file_get_contents("php://input");
if (empty($data))
    trigger_error("no data received", E_USER_ERROR);
$build = json_decode($data);
if (empty($build))
    trigger_error("invalid data received", E_USER_ERROR);

# figure out the branch this is built on
$sourcestamps = $build->buildset->sourcestamps;
if (count($sourcestamps) == 0) {
    trigger_error("no sourcestamps received", E_USER_ERROR);
} elseif (count($sourcestamps) > 1) {
    # our buildbot currently doesn't use multi-codebase builds, so this shouldn't happen.
    # if it did, we'd need a way to filter the relevant one (`codebase` GET param?)
    trigger_error("too many sourcestamps received", E_USER_ERROR);
} else {
    $branch = $sourcestamps[0]->branch;
    if (empty($branch) && empty($sourcestamps[0]->revision)) {
        # we only check out the latest branch if there's no revision specified
        # TODO: the documentation mentions a `changed` object...
        $branch = "master";
    }
}

# we don't care about builds that we can't link to a branch
if (!empty($branch)) {
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
                               $branch,
                               $build->url,
                               $complete,
                               $results);
    $stmt->execute();
    $stmt->close();
}

?>