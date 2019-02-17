<?php
error_reporting(0);
header('Content-type:text/html;charset=utf-8');
$info = file_get_contents('https://www.pdflibr.com/');
$minPic = '#<source media="\(min-width:768px\)" type=([\s\S]*?) srcset="(.*?)">#';
$maxPic = '#<source media="\(max-width:768px\)" type=([\s\S]*?) srcset="(.*?)">#';
$imgPic = '#<img src="(.*?)">
                    </picture>#';
$h = '#<h3>(.*?)</h3>#';
$txt = '#<div class="number-list-info col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-3">([\s\S]*?)</div>#';
$href = '#<div class="sms-number-read col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-3">([\s\S]*?)</div>#';
$t = '#<a href="(.*?)" ([\s\S]*?)>#';
preg_match_all($minPic, $info, $minPicarr);
preg_match_all($maxPic, $info, $maxPicarr);
preg_match_all($imgPic, $info, $imgPicarr);
preg_match_all($h, $info, $harr);
preg_match_all($txt, $info, $txtarr);
preg_match_all($href, $info, $hrefarr);
$buttonarr = array();
foreach ($hrefarr[1] as $key=>$value){
    preg_match_all($t, $value, $tarr);
    $buttonarr[$key] = $tarr[1][0];
}