<?php
echo shell_exec('whois xado.name');
die();
set_time_limit(0);
$db = mysqli_connect('localhost', 'root', 'ghjcnjnfr21', 'parser_kl');

function getDataByLink($link){
    $content = file_get_contents('http://ahrefs.com/site-explorer/backlinks/subdomains/'.$link.'/not_sitewide-href-dofollow-all-all');
    $indexAll = preg_match('#id="filter-all-gov-edu-counter">(\d+)</span>#isU',$content,$tempAll) ? $tempAll[1] : 0 ;
    $indexGov = preg_match('#id="filter-gov-counter">(\d+)</span>#isU',$content,$tempGov) ? $tempGov[1] : 0 ;
    $indexEdu = preg_match('#id="filter-edu-counter">(\d+)</span>#isU',$content,$tempEdu) ? $tempEdu[1] : 0 ;

    return array($indexAll,$indexGov,$indexEdu);
}


//$p = mysqli_query($db, 'SELECT  * FROM `domains`');
//$r = mysqli_fetch_assoc($p);

//phpinfo();die();
//echo `whois xado.com`;
//die();
//$timeNow = strtotime('now');

$dayAgo = 0;
while ($dayAgo <= 1){//731) {
    $pageCurrent = 1;
    $timeDayAgo = strtotime('-' . $dayAgo . ' day');
    $dateLinkText = date('Y-m-d', $timeDayAgo);
    $domainsData = array();

    do {
        // prepare POST for file_get_contents function
        $postData = http_build_query(
            array(
                'page' => $pageCurrent,
                'EXP_DATE' => $dateLinkText, //'2012-10-23',
                'list_type' => 'PR',
                'tld' => ''
            )
        );

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postData
            )
        );

        // get the content of the current page
        $mainLink = 'http://www.odditysoftware.com/__live/_post/domains.php';
        $context = stream_context_create($opts);
        $content = file_get_contents($mainLink, false, $context);

        // parse a links with PR
        if (preg_match_all(
            '#<tr>\s+<td\s+class=\'ui-widget-content\'><a\s+target="_blank"\s+href=\'/domain-tools/register/([^\']+)\'>.+src="/_images/pr/PageRank(\d+)\.jpg".+</tr>#isU',
            $content,
            $domainsDataTemp,
            PREG_SET_ORDER)) {
            foreach ($domainsDataTemp as $key => $domainDataTemp) {
                $domainsData = array();
                $domainsData['href'] = $domainDataTemp[1];
                $domainsData['pr'] = $domainDataTemp[2];
                $domainsData['backlinks'] = getDataByLink($domainsData['href']);

                echo $domainsData['href'];
                flush();
                ob_flush();
                flush();

                $whoisData = shell_exec('whois ' . $domainDataTemp[1]);
                $whoisExpiredStatus = 0;
                if(preg_match('#^[^\d]*Expir[^\d]+\:\s+(.*)$#is',$whoisData,$whoistemp)){
                    $whoisExpiredStatus = strtotime(trim($whoistemp[1]));
                }elseif(preg_match('#paid-till\:\s+\d{4}\.\d{2}\.\d{2})#is',$whoisData,$whoistemp)){
                    $whoisExpiredStatus = strtotime($whoistemp[1]);
                }

                $expiredStatusFlag = $timeDayAgo >= $whoisExpiredStatus ? 1 : 0;
                if($expiredStatusFlag){
                    echo ' yes';
                    flush();
                    ob_flush();
                    flush();
                    $t = mysqli_query($db,'INSERT INTO `domains` SET
                                            `domain_name`="'.$domainsData['href'].'",
                                            `PR`="'.$domainsData['pr'].'",
                                            `date`="'.date('Y-m-d',$timeDayAgo).'",
                                            `backlinks_all`="'.$domainsData['backlinks'][0].'",
                                            `backlinks_gov`="'.$domainsData['backlinks'][1].'",
                                            `backlinks_edu`="'.$domainsData['backlinks'][2].'"');
                }
                echo "<br />";
                flush();
                ob_flush();
                flush();
                //die();

            }
        }

        # get the count of pages
        $pageCount = !isset($pageCount) ? (preg_match(
            '#<span\s+class="pages">[^<]+<b>\d+</b>[^<]+<b>(\d+)</b></span>#is',
            $content,
            $pageArr
        ) ? $pageArr[1] : 1) : $pageCount;
        ++$pageCurrent;
    } while ($pageCurrent <= $pageCount);


    //die();
    $dayAgo++;
}