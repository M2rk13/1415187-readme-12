<?php

$site_name = 'ReadMe';
$connection = db_connect("database:3306", "root", "tiger", "readme");
$page_limit = 6;
$now_time = new DateTime('now');
$active_section = '';
