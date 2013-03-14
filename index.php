<?php

phpinfo();die();
echo `ls`;
die();
$timeNow = strtotime('now');

$dayAgo = 0;
while ($dayAgo <= 731) {
    $pageCurrent = 1;
    $dateLinkText = date('Y-m-d', strtotime('-' . $dayAgo . ' day'));
    $domainsData = array();

    do {
        // prepare POST for file_get_contents function
        $postData = http_build_query(
            array(
                'page' => $pageCurrent,
                'EXP_DATE' => $dateLinkText,//'2012-10-23',
                'list_type' => 'PR',
                'tld' => ''
            )
        );

        $opts = array(
            'http' =>
            array(
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
        if(preg_match_all('#<tr>\s+<td\s+class=\'ui-widget-content\'><a\s+target="_blank"\s+href=\'/domain-tools/register/([^\']+)\'>.+src="/_images/pr/PageRank(\d+)\.jpg".+</tr>#isU',$content,$domainsDataTemp,PREG_SET_ORDER)){
            foreach($domainsDataTemp as $key=>$domainDataTemp){
                $domainsData[$pageCurrent][$key]['href'] = $domainDataTemp[1];
                $domainsData[$pageCurrent][$key]['pr'] = $domainDataTemp[2];
            }
        }

        # get the count of pages
        $pageCount = !isset($pageCount) ? (preg_match('#<span\s+class="pages">[^<]+<b>\d+</b>[^<]+<b>(\d+)</b></span>#is',
                                            $content,
                                            $pageArr
                                        ) ? $pageArr['1'] : 1) : $pageCount;
        ++$pageCurrent;
    } while ($pageCurrent <= $pageCount);


    die();
    $dayAgo++;
}