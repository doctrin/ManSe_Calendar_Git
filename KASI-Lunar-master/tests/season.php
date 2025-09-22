<?php
/*
 * Test code for KASI_Lunar
 * $Id$
 *
 * 특정년도의 절기 정보 비교 검증
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

require_once 'KASI_Lunar.php';

$confirm = array (
	1075895760, 1107452580, 1139009220, 1170566280, 1202122800, 1233679800,
	1265237280, 1296793980, 1328350920, 1359907980, 1391464980, 1423022280
);

try {
	$lunar = new oops\KASI\Lunar;

	for ( $i = 2004; $i < 2016; $i++ ) {
		$j = $i - 2004;
		$season = $lunar->season ('입춘', $i);

		echo "{$i} {$season->name}({$season->hname}) => {$season->stamp} <= 검증값 {$confirm[$j]} : ";
		if ( $season->stamp == $confirm[$j] )
			echo "OK\n";
		else
			echo "FAIL\n";
		#print_r ($season);
	}

} catch ( Exception $e ) {
	echo $e->Message () . "\n";
	print_r ($e->TraceAsArray ()) . "\n";
	$e->finalize ();
}

