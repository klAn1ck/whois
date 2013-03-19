<?php

$db = mysqli_connect('localhost', 'root', 'ghjcnjnfr21', 'parser_kl');

$p = mysqli_query($db, 'SELECT  * FROM `domains`');
$r = mysqli_fetch_assoc($p);

//phpinfo();die();
//echo `whois xado.com`;
//die();
//$timeNow = strtotime('now');

$dayAgo = 0;
while ($dayAgo <= 731) {
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
                $domainsData[$pageCurrent][$key]['href'] = $domainDataTemp[1];
                $domainsData[$pageCurrent][$key]['pr'] = $domainDataTemp[2];

                $whoisData = shell_exec('whois ' . $domainDataTemp[1]);
                $whoisExpiredStatus = preg_match(
                    '#Expiration\s+Date\:\s+(\d{2}-\S{3}-\d{4})#is',
                    $whoisData,
                    $whoistemp
                ) ? strtotime($whoistemp[1]) : 0;
                $expiredStatusFlag = $timeDayAgo >= $whoisExpiredStatus ? 1 : 0;

                echo $whoisExpiredStatus;
                die();
                $db
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


    die();
    $dayAgo++;
}