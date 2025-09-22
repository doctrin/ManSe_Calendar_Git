<?php
/*
 * Test code for KASI_Lunar
 * $Id$
 */

$iniget = function_exists ('___ini_get') ? '___ini_get' : 'ini_get';
$iniset = function_exists ('___ini_set') ? '___ini_set' : 'ini_set';

$cwd = getcwd ();
$ccwd = basename ($cwd);
if ( $ccwd == 'tests' ) {
	$oldpath = $iniget ('include_path');
	$newpath = preg_replace ("!/{$ccwd}!", '', $cwd);
	$iniset ('include_path', $newpath . ':' . $oldpath);
}

/*
 * oops\KASI/Lunar API import
 */
require_once 'KASI_Lunar.php';

$lunar = new oops\KASI\Lunar;

$days = array (
	'1391-02-05',
	'1582-10-04',
	'1582-10-05',
	'1582-10-15',
	'1583-03-03', // not leap
	'1583-04-03', // leap
	'2050-12-31',
	'2051-12-31',
);

$ment = array (
	'유효범위 시작날자',
	'율리우스력 마지막 날자',
	'율리우스력과 그레고리력 사이의 존재하지 않는 날',
	'그레고리력 시작 날자',
	'윤달 체크(아님)',
	'윤달 체크(맞음)',
	'유효범위 마지막 날자',
	'유효범위 밖'
);

try {
	# 절기 정보 확인
	#   stdClass Object
	#   (
	#       [name]  => 입춘
	#       [hname] => 立春
	#       [stamp] => 1423022280
	#       [date]  => 2015-02-04 12:58
	#       [year]  => 2015
	#       [month] => 02
	#       [day]   => 04
	#       [hour]  => 12
	#       [min]   => 58
	#   )
	$season = $lunar->season ('입춘', 2015);
	echo "2015년 {$season->name}({$season->hname}) => {$season->stamp} <= 검증값 1423022280 : ";
	if ( $season->stamp == 1423022280 )
		echo "OK\n";
	else
		echo "FAIL\n";
	echo "";

	echo "-----------------------------------------------------------------------\n";

	$i = 0;
	foreach ( $days as $day ) {
		$l = $lunar->tolunar ($day);

		$leap  = $l->leap ? 'true' : 'false';
		$lmoon = $l->lmoon ? 'true' : 'false';

		$s = $lunar->tosolar ($l->fmt, $l->leap);

		echo <<<EOF
지정 날자: $day - {$ment[$i++]}
음력 변환: {$l->fmt}, leap: {$leap}, LargeMoon: {$lmoon}
양력 변환: {$s->fmt} {$s->jd}
-----------------------------------------------------------------------

EOF;
	}
} catch ( Exception $e ) {
	echo $e->Message () . "\n";
	print_r ($e->TraceAsArray ()) . "\n";
	$e->finalize ();
}

?>
