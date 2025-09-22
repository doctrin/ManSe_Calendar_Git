<?php
// cli.php - Node.js ↔ PHP 브리지 (정확 간지 계산 포함)
// - 외부 공개 API만 사용: solarToLunarPublic / lunarToSolarPublic / getSolarTermPublic
// - 간지(년/월/일/시) 계산은 여기서 구현
// - 24절기 경계는 getSolarTermPublic 결과(최근 절기/중기)로 판정
//
// 사용 예:
//   php cli.php solarToLunar 2025 9 1
//   php cli.php lunarToSolar 2025 7 10 false
//   php cli.php solarTerm    2025 9 1
//   php cli.php monthPack    2025 9
//   php cli.php sexagenary   2025 9 1 13 0
//   php cli.php help

declare(strict_types=1);
date_default_timezone_set('Asia/Seoul');

//require_once __DIR__ . '/Lunar/Lunar_API.php';

require_once __DIR__ . '/../Lunar-kasi/Lunar/Lunar_API.php';

$action  = $argv[1] ?? null;
$y       = isset($argv[2]) ? (int)$argv[2] : 0;
$m       = isset($argv[3]) ? (int)$argv[3] : 0;
$d       = isset($argv[4]) ? (int)$argv[4] : 0;
$hh      = isset($argv[5]) ? (int)$argv[5] : 0;
$mi      = isset($argv[6]) ? (int)$argv[6] : 0;
$leapArg = $argv[5] ?? 'false';
$leap    = filter_var($leapArg, FILTER_VALIDATE_BOOLEAN);

try {
    $api = new \oops\Lunar_API();

    // ───────────────────────────────────
    // 공통 유틸
    // ───────────────────────────────────
    $HS = ["甲","乙","丙","丁","戊","己","庚","辛","壬","癸"]; // 천간
    $EB = ["子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥"]; // 지지
    $MBR = ["寅","卯","辰","巳","午","未","申","酉","戌","亥","子","丑"]; // 월지(입춘 기준: 寅 시작)

    $daysInMonth = function(int $y, int $m): int {
        $t = strtotime(sprintf('%04d-%02d-01', $y, $m));
        return (int) date('t', $t);
    };

    $idxHS = function(string $gan) use ($HS): ?int {
        $i = array_search($gan, $HS, true);
        return ($i === false) ? null : $i;
    };

    // 일진(간지) 계산: 기준일 1900-01-31(甲子일) 서울 정오 기준
    $dayGanzhi = function(int $y, int $m, int $d) use ($HS, $EB): array {
        $base = new DateTime('1900-01-31 12:00', new DateTimeZone('Asia/Seoul')); // 甲子
        $tar  = new DateTime(sprintf('%04d-%02d-%02d 12:00', $y, $m, $d), new DateTimeZone('Asia/Seoul'));
        $diff = (int)$base->diff($tar)->format('%r%a');
        $stem = $HS[(($diff % 10) + 10) % 10];
        $bran = $EB[(($diff % 12) + 12) % 12];
        return ['gan' => $stem, 'ji' => $bran];
    };

    // 해당 연도의 '입춘(立春)' 날짜 구하기 (못 찾으면 2/4 fallback)
    $lichunDate = function(int $year) use ($api): DateTime {
        for ($mm = 1; $mm <= 3; $mm++) {
            $dim = (int) date('t', strtotime(sprintf('%04d-%02d-01', $year, $mm)));
            for ($dd = 1; $dd <= $dim; $dd++) {
                $t = $api->getSolarTermPublic($year, $mm, $dd);
                if (is_array($t) && isset($t['termNameHan']) && $t['termNameHan'] === '立春') {
                    $dateStr = substr($t['date'] ?? '', 0, 10);
                    if ($dateStr) {
                        return new DateTime($dateStr . ' 00:00', new DateTimeZone('Asia/Seoul'));
                    }
                }
            }
        }
        return new DateTime(sprintf('%04d-02-04 00:00', $year), new DateTimeZone('Asia/Seoul'));
    };

    // 년주(입춘 기준) 계산
    $yearGanzhiTermBased = function(int $y, int $m, int $d) use ($HS, $EB, $lichunDate): array {
        $today = new DateTime(sprintf('%04d-%02d-%02d 00:00', $y, $m, $d), new DateTimeZone('Asia/Seoul'));
        $lc    = $lichunDate($y);
        $yCalc = ($today < $lc) ? $y - 1 : $y;
        // 1984년 갑자(甲子)년 기준
        $i60   = (($yCalc - 1984) % 60 + 60) % 60;
        return ['gan' => $HS[$i60 % 10], 'ji' => $EB[$i60 % 12]];
    };

    // 월주(절(節) 기준) 계산
    // getSolarTermPublic(y,m,d)가 돌려주는 최근 절기정보로 month index 산출
    // termIndex가 홀수(中氣)이면 직전 짝수(節)로 내림
    $monthGanzhiTermBased = function(int $y, int $m, int $d) use ($api, $HS, $MBR, $yearGanzhiTermBased, $idxHS): array {
        $t  = $api->getSolarTermPublic($y, $m, $d);
        $ti = isset($t['termIndex']) ? (int)$t['termIndex'] : null;

        // termIndex가 없으면 대략적 fallback: (리스크 있지만, 보강)
        if ($ti === null) {
            // 2/4~ → 寅(0), 3/6~ → 卯(1) ... 등으로 근사
            // 그래도 연간으로 월간 맞출 때 가장 보수적으로 처리
            $approxMap = [
                '01-06' => 11, // 小寒(22) 근사 → 11(丑)
                '02-04' => 0,  // 立春 → 0(寅)
                '03-06' => 1,  // 驚蟄 → 1(卯)
                '04-05' => 2,  // 清明 → 2(辰)
                '05-06' => 3,  // 立夏 → 3(巳)
                '06-06' => 4,  // 芒種 → 4(午)
                '07-07' => 5,  // 小暑 → 5(未)
                '08-08' => 6,  // 立秋 → 6(申)
                '09-08' => 7,  // 白露 → 7(酉)
                '10-08' => 8,  // 寒露 → 8(戌)
                '11-07' => 9,  // 立冬 → 9(亥)
                '12-07' => 10, // 大雪 → 10(子)
            ];
            $md = sprintf('%02d-%02d', $m, $d);
            $mi = 0;
            foreach ($approxMap as $k => $v) {
                if ($md >= $k) $mi = $v;
            }
            $ygz = $yearGanzhiTermBased($y, $m, $d);
            $ys  = $idxHS($ygz['gan']);
            $startStem = (($ys * 2) + 2) % 10;   // 寅월 간 시작
            return ['gan' => $HS[($startStem + $mi) % 10], 'ji' => $MBR[$mi]];
        }

        // 짝수 index(節)만 월경계로 사용 → 홀수(中氣)는 직전 짝수로 보정
        $evenTi = ($ti % 2 === 1) ? $ti - 1 : $ti; // 0,2,4,...,22
        $mi     = intdiv($evenTi, 2);              // 0..11 (寅..丑)

        // 연간으로 월간 산출
        $ygz = $yearGanzhiTermBased($y, $m, $d);
        $ys  = $idxHS($ygz['gan']);                 // 0..9
        $startStem = (($ys * 2) + 2) % 10;          // 寅월의 간 시작 인덱스
        return ['gan' => $HS[($startStem + $mi) % 10], 'ji' => $MBR[$mi]];
    };

    // 시주 계산 (프로젝트 룰에 맞춘 경계: 23:29, 01:29, ... )
    $hourGanzhi = function(int $y, int $m, int $d, int $hh = 0, int $mi = 0) use ($HS, $EB, $dayGanzhi): array {
        // 자(23:29~01:29) / 축(01:29~03:29) ... 경계
        $bounds = [
            [23,29], [1,29], [3,29], [5,29],
            [7,29],  [9,29], [11,29],[13,29],
            [15,29], [17,29],[19,29],[21,29]
        ];
        $tmin = $hh * 60 + $mi;

        // 경계 찾기
        $idx = 0; // 0=子,1=丑,...,11=亥
        for ($i = 0; $i < 12; $i++) {
            $s  = $bounds[$i][0] * 60 + $bounds[$i][1];
            $e  = $bounds[($i + 1) % 12][0] * 60 + $bounds[($i + 1) % 12][1];
            if ($s <= $e) {
                if ($tmin >= $s && $tmin < $e) { $idx = $i; break; }
            } else {
                // 자정 넘어감(23:29 ~ 01:29)
                if ($tmin >= $s || $tmin < $e) { $idx = $i; break; }
            }
        }
        $hourBranch = $EB[$idx];

        // 일간으로 시간간 산출
        $dg = $dayGanzhi($y, $m, $d);
        $dayStemIdx = array_search($dg['gan'], $HS, true); // 0..9
        // 甲己:0, 乙庚:1, 丙辛:2, 丁壬:3, 戊癸:4
        $group = intdiv($dayStemIdx, 2);
        $hourStemIdx = ($group * 2 + $idx) % 10;

        return ['gan' => $HS[$hourStemIdx], 'ji' => $hourBranch];
    };

    // sexagenary: 년/월/일/시주 종합
    $sexagenary = function(int $y, int $m, int $d, int $hh = 0, int $mi = 0)
        use ($yearGanzhiTermBased, $monthGanzhiTermBased, $dayGanzhi, $hourGanzhi): array {
        $yg = $yearGanzhiTermBased($y, $m, $d);
        $mg = $monthGanzhiTermBased($y, $m, $d);
        $dg = $dayGanzhi($y, $m, $d);
        $hg = $hourGanzhi($y, $m, $d, $hh, $mi);
        return [
            'error'     => '',
            'input'     => sprintf('%04d-%02d-%02d %02d:%02d', $y, $m, $d, $hh, $mi),
            'yearGan'   => $yg['gan'], 'yearJi' => $yg['ji'],
            'monthGan'  => $mg['gan'], 'monthJi'=> $mg['ji'],
            'dayGan'    => $dg['gan'], 'dayJi'  => $dg['ji'],
            'hourGan'   => $hg['gan'], 'hourJi' => $hg['ji'],
        ];
    };

    // ───────────────────────────────────
    // 액션 처리
    // ───────────────────────────────────
    switch ($action) {
        case 'help':
            echo json_encode([
                'usage' => [
                    'solarToLunar  YYYY MM DD',
                    'lunarToSolar  YYYY MM DD leap(true|false)',
                    'solarTerm     YYYY MM DD',
                    'monthPack     YYYY MM',
                    'sexagenary    YYYY MM DD HH MI'
                ]
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'solarToLunar':
            if ($argc < 5) { echo json_encode(['error'=>'Usage: cli.php solarToLunar YYYY MM DD']); exit(1); }
            $out = $api->solarToLunarPublic($y, $m, $d);
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'lunarToSolar':
            if ($argc < 6) { echo json_encode(['error'=>'Usage: cli.php lunarToSolar YYYY MM DD leap']); exit(1); }
            $out = $api->lunarToSolarPublic($y, $m, $d, $leap);
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'solarTerm':
            if ($argc < 5) { echo json_encode(['error'=>'Usage: cli.php solarTerm YYYY MM DD']); exit(1); }
            $out = $api->getSolarTermPublic($y, $m, $d);
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'monthPack':
            if ($argc < 4) { echo json_encode(['error'=>'Usage: cli.php monthPack YYYY MM']); exit(1); }
            $dim = $daysInMonth($y, $m);
            $lunarMap = [];
            $terms    = [];
            $seen     = [];

            for ($day = 1; $day <= $dim; $day++) {
                $lunarMap[$day] = $api->solarToLunarPublic($y, $m, (int)$day);
                $t = $api->getSolarTermPublic($y, $m, (int)$day);
                if (is_array($t) && isset($t['date'])) {
                    $date = substr($t['date'], 0, 10);
                    [$ty,$tm] = array_map('intval', explode('-', $date));
                    if ($ty === $y && $tm === $m && empty($seen[$date])) {
                        $terms[] = $t;
                        $seen[$date] = true;
                    }
                }
            }
            echo json_encode(['lunar'=>$lunarMap, 'terms'=>$terms], JSON_UNESCAPED_UNICODE);
            break;
/*
        case 'sexagenary':
            if ($argc < 7) {
                echo json_encode(['error'=>'Usage: cli.php sexagenary YYYY MM DD HH MI']); exit(1);
            }
            $out = $sexagenary($y, $m, $d, $hh, $mi);
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;
*/
		case "sexagenary":
			$y  = intval($argv[2] ?? 0);
			$m  = intval($argv[3] ?? 0);
			$d  = intval($argv[4] ?? 0);
			$hh = intval($argv[5] ?? 0);

			try {
				$kasi = new KASI_Lunar();
				$result = $kasi->getGanji($y, $m, $d, $hh);

				echo json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;
			} catch (\Throwable $e) {
				echo json_encode(["error" => $e->getMessage()]) . PHP_EOL;
			}
			break;
			
		case 'monthGanZhiDebug':
			if ($argc < 4) { echo json_encode(['error'=>'Usage: cli.php monthGanZhiDebug YYYY MM DD']); exit(1); }
			$out = $api->getMonthGanZhiPublic($y, $m, $d);
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			break;

        default:
            echo json_encode(['error'=>"Unknown action: $action"]); exit(1);
    }

} catch (Throwable $e) {
    echo json_encode(['error'=>$e->getMessage()]); exit(1);
}
