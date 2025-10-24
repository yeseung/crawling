<?php
header("Content-Type: application/json; charset=utf-8");
//header("content-type:text/html; charset=utf-8");

require_once "../Snoopy.class.php";
ini_set("allow_url_fopen","1");

// ---------- helpers ----------
function json_out($arr, $code = 200){
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ---------- input ----------
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// ---------- fetch ----------
//sell
//$url = "https://www.xn--3e0b036btifksj.com/40/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //서울v
//$url = "https://www.xn--3e0b036btifksj.com/93/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경기v
//$url = "https://www.xn--3e0b036btifksj.com/92/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //강원v
//$url = "https://www.xn--3e0b036btifksj.com/91/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //인천v
//$url = "https://www.xn--3e0b036btifksj.com/90/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //충북v
//$url = "https://www.xn--3e0b036btifksj.com/89/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //충남v
//$url = "https://www.xn--3e0b036btifksj.com/88/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경북v
//$url = "https://www.xn--3e0b036btifksj.com/87/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경남v
//$url = "https://www.xn--3e0b036btifksj.com/86/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //전북v
//$url = "https://www.xn--3e0b036btifksj.com/85/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //전남v
//$url = "https://www.xn--3e0b036btifksj.com/84/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //제주v

//buy
$url = "https://www.xn--3e0b036btifksj.com/122/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //서울v
//$url = "https://www.xn--3e0b036btifksj.com/125/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경기v
//$url = "https://www.xn--3e0b036btifksj.com/126/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //강원v
//$url = "https://www.xn--3e0b036btifksj.com/127/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //인천v
//$url = "https://www.xn--3e0b036btifksj.com/128/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //충북v
//$url = "https://www.xn--3e0b036btifksj.com/129/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //충남v
//$url = "https://www.xn--3e0b036btifksj.com/130/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경북v
//$url = "https://www.xn--3e0b036btifksj.com/131/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //경남v
//$url = "https://www.xn--3e0b036btifksj.com/132/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //전북v
//$url = "https://www.xn--3e0b036btifksj.com/133/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //전남v
//$url = "https://www.xn--3e0b036btifksj.com/134/?q=YToxOntzOjEyOiJrZXl3b3JkX3R5cGUiO3M6MzoiYWxsIjt9&page=".$page; //제주v

//echo $url;
$snoopy = new Snoopy;
$snoopy->agent = !empty($_SERVER['HTTP_USER_AGENT'])
    ? $_SERVER['HTTP_USER_AGENT']
    : 'Mozilla/5.0 (compatible; KimTajaBot/1.0; +https://kimtaja.com)';
$snoopy->read_timeout = 15;
$snoopy->referer = "https://www.xn--3e0b036btifksj.com/";

$submit_url = "https://www.xn--3e0b036btifksj.com/backpg/login.cm";
$submit_vars["back_url"] = "Lw%3D%3D";
$submit_vars["back_url_auth"] = "";
$submit_vars["used_login_btn"] = "Y";
$submit_vars["back_url"] = "Lw%3D%3D";
$submit_vars["uid"] = "";
$submit_vars["passwd"] = "";
$snoopy->submit($submit_url,$submit_vars);
$snoopy->setcookies();


if (!$snoopy->fetch($url)) {
    json_out([
        "success" => false,
        "page"    => $page,
        "error"   => "FETCH_FAILED",
        "message" => trim($snoopy->error)
    ], 502);
}

$html = $snoopy->results;

// ---------- encoding ----------
$enc = mb_detect_encoding($html, ['UTF-8','EUC-KR','CP949','ISO-8859-1'], true);
if ($enc && $enc !== 'UTF-8') {
    $html = @mb_convert_encoding($html, 'UTF-8', $enc);
}

// ---------- slice target section ----------
$start_marker = '<li class="views" style="display: ">조회</li>';
$end_marker   = '<div class="li_footer';

$start_pos = strpos($html, $start_marker);
if ($start_pos === false) {
    json_out([
        "success" => false,
        "page"    => $page,
        "error"   => "START_MARKER_NOT_FOUND",
        "message" => "시작 마커를 찾을 수 없습니다.",
        "marker"  => $start_marker
    ]);
}

$tail = substr($html, $start_pos + strlen($start_marker));
$end_pos = strpos($tail, $end_marker);
if ($end_pos === false) {
    json_out([
        "success" => false,
        "page"    => $page,
        "error"   => "END_MARKER_NOT_FOUND",
        "message" => "종료 마커를 찾을 수 없습니다.",
        "marker"  => $end_marker
    ]);
}

$section = substr($tail, 0, $end_pos);
//echo $section;


// ---------- extract subjects ----------
$subjects = [];
$re_subject = '/<span\b[^>]*\bcursor\s*:\s*pointer\b[^>]*>(.*?)<\/span>/is';
if (preg_match_all($re_subject, $section, $m1)) {
    foreach ($m1[1] as $txt) {
        $t = trim(strip_tags($txt));
        if ($t !== '') $subjects[] = $t;
    }
}

// ---------- extract links (index) ----------
$links = [];
$base_origin = 'https://www.xn--3e0b036btifksj.com';

$re_link = '/
    <a\b
    (?=[^>]*\bclass\s*=\s*(["\'])[^\1>]*\btitle_link\b[^\1>]*\b_fade_link\b[^\1>]*\1)  # class에 title_link, _fade_link 모두 존재
    (?=[^>]*\bhref\s*=\s*(["\'])(.*?)\2)                                              # href 캡처
    [^>]*>
/xsi';

if (preg_match_all($re_link, $section, $m)) {
    // $m[3]가 href 값들
    foreach ($m[3] as $href) {
        $href = trim(html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        // 절대경로 보정
        if (strpos($href, 'http://') !== 0 && strpos($href, 'https://') !== 0) {
            $href = rtrim($base_origin, '/') . '/' . ltrim($href, '/');
        }
        $links[] = $href;
    }
    $links = array_values(array_unique($links));
}

//print_r($subjects);
//print_r($links);exit;

// ---------- pair subjects & links ----------
$len = min(count($subjects), count($links));
$list = [];
for ($i = 0; $i < $len; $i++) {
    $list[] = [
        'subject' => $subjects[$i],
        'link'   => $links[$i],
        'encode'   => urlencode($links[$i])
    ];
}


// ---------- response ----------
json_out([
    "success"  => true,
    "page"     => $page,
    "count"    => count($subjects),
    "items"    => $list,
    // 필요 시 디버깅용 길이만 보존(본문은 용량 커질 수 있어 미포함)
    "html_len" => strlen($section)
]);



exit;






