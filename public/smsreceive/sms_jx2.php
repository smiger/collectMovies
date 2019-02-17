<?php
error_reporting(0);
header('Content-type:text/html;charset=utf-8');
$id = $_GET['id'];
$page = $_GET['page'];
$info = file_get_contents('https://www.pdflibr.com/SMSContent/'.$id.'?page='.$page);
$section = '#<section class="container-fluid sms_content">([\s\S]*?)</section>#';

preg_match_all($section, $info, $sectionarr);

