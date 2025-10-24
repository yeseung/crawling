<?php
header("content-type:text/html; charset=utf-8");
include_once('./_common.php');

exit;



for ($p=3; $p>=1; $p--){

    $url = "https://arcade9087463562.mycafe24.com/crawling/list.php?page=".$p;
    echo $url."<br>";
    
    $result = get_curl($url);


    if (!isset($result['success']) || $result['success'] !== true) {
        $code = isset($result['http_code']) ? $result['http_code'] : 0;
        $err  = isset($result['error']) ? $result['error'] : 'UNKNOWN';
        echo "Error: {$err} (HTTP {$code})";
        exit;
    }

    // 1) payload 확보: 이미 items가 배열이면 그대로 사용, 아니면 data를 JSON 파싱
    $payload = $result;
    if (!isset($payload['items'])) {
        $payload = json_decode($result['data'] ?? '', true);
    }

    $items = (isset($payload['items']) && is_array($payload['items'])) ? $payload['items'] : [];

    if (!$items) {
        echo "No items.";
        exit;
    }

    // 2) 최신 → 과거 순으로 출력 (역순)
    foreach (array_reverse($items) as $row) {
        $subject = htmlspecialchars($row['subject'] ?? '', ENT_QUOTES, 'UTF-8');
        //$link    = htmlspecialchars($row['link'] ?? '', ENT_QUOTES, 'UTF-8');

        // list.php가 encode(퍼센트 인코딩된 전체 URL)를 이미 줍니다. 다시 urlencode 하지 마세요!
        $viewUrl = "https://arcade9087463562.mycafe24.com/crawling/view.php?url=" . ($row['encode'] ?? '');
        echo "<h4>{$subject}</h4>\n";
        echo "<div style='color:#888;font-size:12px;margin-bottom:6px'>" . htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') . "</div>";

        $res = get_curl($viewUrl);
        if (!isset($res['success']) || $res['success'] !== true) {
            $code = $res['http_code'] ?? 0;
            $err  = $res['error'] ?? 'UNKNOWN';
            echo "Error: {$err} (HTTP {$code})<hr>\n";
            continue;
        }

        // payload 확보: 이미 배열일 수도, data(JSON 문자열)일 수도 있음
        $payload = $res;
        if (!isset($payload['img']) && isset($res['data'])) {
            $decoded = json_decode($res['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        $imgs = (isset($payload['img']) && is_array($payload['img'])) ? $payload['img'] : [];
        $imgHtml = '';
        foreach ($imgs as $src) {
            $imgHtml .= '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"><br>' . "\n";
        }

        // content는 view.php가 준 HTML(일부 태그 포함)이라고 가정
        $contentHtml = $payload['content'] ?? '';

        // 최종 출력: 이미지 + 본문
        $content = $imgHtml . $contentHtml;
        echo $content . "<hr>\n";


        $q['mb_id'] = "admin";
        $q['ca_name'] = "서울";
        $q['bo_table'] = "buy";
        $q['wr_subject'] = $subject;
        $q['wr_content'] = '<div>'.$content.'</div>';
        $q['wr_hit'] = mt_rand(300,500);
        $q['wr_ip'] = $_SERVER["REMOTE_ADDR"];

        $result = insert_write($q);

        print_r($result);

    }


}
exit;



