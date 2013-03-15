<?php
$timeNow = strtotime('now');

$dayAgo = 0;
while ($dayAgo <= 731) {
    $pageCurrent = 1;

    do {
        $dateLinkText = date('Y-m-d', strtotime('-' . $dayAgo . ' day'));
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

        $mainLink = 'http://www.odditysoftware.com/__live/_post/domains.php';
        $context = stream_context_create($opts);
        $content = file_get_contents($mainLink, false, $context);
        if($pageCurrent === 2) die($content);

        $pageCount = isset($pageCount) ? (preg_match('#<span\s+class="pages">[^<]<b>\d+</b>[^<]<b>(\d+)</b></span>#is',
                                            $content,
                                            $pageArr
                                        ) ? $pageArr['1'] : 1) : $pageCount;
        die($pageCount);
        ++$pageCurrent;
    } while ($pageCurrent <= $pageCount);



    die($content);
    $dayAgo++;
}