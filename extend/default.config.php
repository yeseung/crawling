<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

define('KGINICIS_USE_CERT_SEED', isset($config['cf_cert_use_seed']) ? (int) $config['cf_cert_use_seed'] : 1);

// 유저 사이드뷰에서 아이콘 지정 안했을시 기본 no 프로필 이미지
define('G5_NO_PROFILE_IMG', '<span class="profile_img"><img src="'.G5_IMG_URL.'/no_profile.gif" alt="no_profile" width="'.$config['cf_member_icon_width'].'" height="'.$config['cf_member_icon_height'].'"></span>');

define('G5_USE_MEMBER_IMAGE_FILETIME', TRUE);

// 썸네일 처리 방식, 비율유지 하지 않고 썸네일을 생성하려면 주석을 풀고 값은 false 입력합니다. ( true 또는 주석으로 된 경우에는 비율 유지합니다. )
//define('G5_USE_THUMB_RATIO', false);




function get_curl($url, $timeout = 15)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, // 리다이렉트 자동 처리
        CURLOPT_SSL_VERIFYPEER => false, // 필요시 true로 변경
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; KimTajaBot/1.0; +https://kimtaja.com)',
        CURLOPT_HEADER => false
    ]);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'error' => $error_msg,
            'http_code' => $info['http_code'] ?? 0
        ];
    }

    curl_close($ch);

    if ($info['http_code'] !== 200) {
        return [
            'success' => false,
            'error' => "HTTP " . $info['http_code'],
            'http_code' => $info['http_code']
        ];
    }

    return [
        'success' => true,
        'data' => $response,
        'http_code' => 200
    ];
}


function insert_write($data)
{
	global $g5;

	//게시판 테이블 정보
	$bo_table = $data['bo_table'];
	if(!strlen($bo_table)) return FALSE; //bo_table 값이 지정되지 않았습니다.
	$board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
	if(!$board) return FALSE; //bo_table이 존재하지 않습니다.
	
	$write_table = $g5['write_prefix'].$bo_table;
	
	//회원정보 및 권한 확인
    $member = get_member($data['mb_id']);
    if(!$member) return FALSE; //mb_id가 존재하지 않습니다.
    //if($board[bo_write_level] > $member[mb_level]) return FALSE; //글쓰기 권한이 없습니다.
	
	//카테고리 설정
    $ca_name = $data['ca_name'];

	//변수 정리
    $wr_num = get_next_num($write_table);
    $ca_name = addslashes($ca_name);
    $html = "html1";
    $secret = "";
    $mail = "";
    $wr_subject = addslashes(trim($data['wr_subject']));
    $wr_content = addslashes(trim($data['wr_content']));
    if(!$wr_subject) return FALSE; //글 제목이 없습니다.
    if(!$wr_content) return FALSE; //글 내용이 없습니다.
    $mb_id = $member['mb_id'];
    $wr_password = $member['mb_password'];
    $wr_name = $board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick'];
	$wr_email = $member['mb_email'];
	$wr_homepage = $member['mb_homepage'];
    for($i=1; $i<=10; $i++){
        $wr = "wr_{$i}";
        ${$wr} = addslashes($data[$wr]);
    }
    $wr_link1 = $data['wr_link1'];
    $wr_link2 = $data['wr_link2'];
	$wr_hit = $data['wr_hit'];
	$wr_ip = $data['wr_ip'];
	
	//글 입력하기
	$sql = " insert into $write_table
                set wr_num = '$wr_num',
                     wr_reply = '',
                     wr_comment = 0,
                     ca_name = '$ca_name',
                     wr_option = '$html,$secret,$mail',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_link1 = '$wr_link1',
                     wr_link2 = '$wr_link2',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = '$wr_hit',
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '$mb_id',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '".G5_TIME_YMDHIS."',
                     wr_ip = '$wr_ip',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' ";             
    sql_query($sql);
    
    $wr_id = sql_insert_id();

    sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' "); //부모 아이디에 UPDATE
	sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '$mb_id' ) "); //새글 INSERT    
    sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}' "); //게시글 1 증가
	
	return array('bo_table' => $bo_table, 'wr_id' => $wr_id);
}