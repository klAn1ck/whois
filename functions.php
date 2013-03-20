<?php

mysql_connect('localhost', 'root', 'ghjcnjnfr21');

mysql_select_db('parser_kl');

$p = mysql_query('SELECT `domain_name` FROM `domains`');
//echo mysql_num_rows($q);
while($q = mysql_fetch_array($p)){
    $name = $q[0];
    $whoisData = shell_exec('whois '.$name);
    $whoisExpiredStatus = '-1';
    //die($whoisData);
    if(preg_match("#[^0-9]*Expir[^0-9]+\s+(.*)\n#isU",$whoisData,$whoistemp)){
//        print_r($whoistemp);
//        die('-');
        $whoisExpiredStatus = strtotime(trim($whoistemp[1]));
    }elseif(preg_match('#paid-till\:\s+\d{4}\.\d{2}\.\d{2})#is',$whoisData,$whoistemp)){
        $whoisExpiredStatus = strtotime($whoistemp[1]);
    }
    //die();
    echo $whoisExpiredStatus."<br />";
    flush();
    ob_flush();
}