<?php
$link = '187radio.net';

function getDataByLink($link){
    $content = file_get_contents('http://ahrefs.com/site-explorer/backlinks/subdomains/'.$link.'/not_sitewide-href-dofollow-all-all');
    $indexAll = preg_match('#id="filter-all-gov-edu-counter">(\d+)</span>#isU',$content,$tempAll) ? $tempAll[1] : 0 ;
    $indexGov = preg_match('#id="filter-gov-counter">(\d+)</span>#isU',$content,$tempGov) ? $tempGov[1] : 0 ;
    $indexEdu = preg_match('#id="filter-edu-counter">(\d+)</span>#isU',$content,$tempEdu) ? $tempEdu[1] : 0 ;

    return array($indexAll,$indexGov,$indexEdu);
}
