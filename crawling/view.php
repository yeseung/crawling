<?php
header("Content-type:application/json; charset=utf-8");
//header("content-type:text/html; charset=utf-8");


function json_out($arr, $code = 200){
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function ensure_dir($path){
    if (!is_dir($path) && !@mkdir($path, 0777, true)) {
        json_out(["success"=>false,"error"=>"MKDIR_FAILED","message"=>"디렉터리 생성 실패: $path"], 500);
    }
}
function abs_url($url, $base){
    // 절대/상대 URL 보정
    if (preg_match('~^https?://~i', $url)) return $url;
    if (strlen($url) && $url[0] === '/') return rtrim($base, '/').$url;
    return rtrim($base, '/').'/'.$url;
}


$q = $matches = array();

if ($_GET['url'])
	$url = urldecode(trim($_GET['url']));
else
	die('Err');

//echo $url; exit;


include "../Snoopy.class.php";
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
//echo $html; exit;


$start_marker = "<div class='board_txt_area fr-view'>";
$end_marker   = '<div class="comment_section" id="comment_area">';

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
//echo $section; exit;


$pattern = '/<img\b[^>]*?(?:alt\s*=\s*["\']([^"\']*)["\'][^>]*?)?src\s*=\s*["\']([^"\']+)["\'][^>]*>/i';
preg_match_all($pattern, $section, $matches, PREG_PATTERN_ORDER);

$images = [];
for ($i = 0, $N = count($matches[0]); $i < $N; $i++) {
    $src = isset($matches[2][$i]) ? trim($matches[2][$i]) : '';
    if ($src === '') continue;
    $src = html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $images[] = abs_url($src, $BASE_ORIGIN);
}
$images = array_values(array_unique($images));

/* 텍스트 본문 (일부 태그 허용 버전/필요시 순수 텍스트만으로 변경 가능) */
$content_text = trim(strip_tags($section, '<p><br><a><span><strong><em><ul><ol><li><b><i>'));
$content_text = str_replace('<strong>매장 사진이 있으면 꼭! 등록 해주세요. 판매에 도움이 됩니다.</strong>', '', $content_text);
$content_text = str_replace('<p>사진등록란</p>', '', $content_text);

/* 응답 */
json_out([
    "success" => true,
    "url"     => $url,
    "count"   => count($images),
    "img"     => $images,
    "content" => $content_text
]);





exit;


