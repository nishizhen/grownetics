<?php
require "vendor/autoload.php";

use GitlabChangelog\GitlabChangelog;

$changelog = new GitlabChangelog();
$changelog->url = "https://code.cropcircle.io/";
$changelog->repo = "grownetics/grownetics";
$changelog->token = "9azdMzLsBBERWKzDW69u";

$changelog->milestoneFilter = function($milestone) {
    $ignore = array("todo", "long running task", "team", "next release");
    return !in_array($milestone->title, $ignore);
};
$changelog->getLabels = function($issue) {
    $label = "Fixed";
    $map = array(
        "bug" => "Fixed",
        "enhancement" => "Improved",
        "feature" => "Added"
    );
    foreach($map as $k => $v) {
        if(strripos(implode(',', $issue->labels), $k) !== FALSE) {
            $label = $v;
            break;
        }
    }
    return array($label);
};

$changelog->debug = true;

$markdown = $changelog->markdown();

file_put_contents("changelog.md", $markdown);
?>