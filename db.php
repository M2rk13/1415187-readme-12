<?php

$add_text_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0";
$add_quote_post_query = $add_text_post_query . ", quote_author = ?";
$add_photo_post_query = $add_text_post_query . ", img_url = ?";
$add_link_post_query = $add_text_post_query;
$add_video_post_query = $add_text_post_query . ", youtube_url = ?";

function db_connect($host, $user, $pass, $db) {
    $con = mysqli_connect($host, $user, $pass, $db);
    if ($con == false) {
        $error = mysqli_connect_error();
        print($error);
        http_response_code(500);
        exit();
    }
    mysqli_set_charset($con, "utf8");
    return $con;
}