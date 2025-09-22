<?php
require_once __DIR__ . '/../Lunar-kasi/KASI_Lunar.php';
require_once __DIR__ . '/../Lunar-kasi/Lunar.php';
require_once __DIR__ . '/../Lunar-kasi/Lunar/Lunar_API.php';

use oops\KASI\Lunar as KasiLunar;
use oops\Lunar\Lunar as CoreLunar;
use oops\Lunar\Lunar_API;


$cmd = $argv[1] ?? null;

try {
    $kasi = new KasiLunar(); // 음/양력 변환
    $core = new CoreLunar(); // 간지 계산
	$api = new Lunar_API(); // 간지(세간지, 일간 등) 담당

    switch ($cmd) {
        case "solarToLunar":
            [$y, $m, $d] = array_slice($argv, 2, 3);
            $res = $kasi->tolunar(sprintf("%04d-%02d-%02d", $y, $m, $d));
            echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;

        case "lunarToSolar":
            [$y, $m, $d] = array_slice($argv, 2, 3);
            $res = $kasi->tosolar(sprintf("%04d-%02d-%02d", $y, $m, $d));
            echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;

        case "sexagenary":
            [$y, $m, $d, $h] = array_slice($argv, 2, 4);
            $res = $api->getGanZhi((int)$y, (int)$m, (int)$d, (int)$h);
            echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;

        case "dailyCalendar":
            [$y, $m] = array_slice($argv, 2, 2);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$m, (int)$y);
            $arr = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $gz = $core->getGanZhi((int)$y, (int)$m, $d, 12);
                $lunar = $api->tolunar(sprintf("%04d-%02d-%02d", $y, $m, $d));
                $arr[] = [
                    "solar" => sprintf("%04d-%02d-%02d", $y, $m, $d),
                    "lunar" => $lunar->fmt ?? null,
                    "gan"   => $gz['gan'] ?? null,
                    "ji"    => $gz['ji'] ?? null,
                ];
            }
            echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;

        case "monthPillar":
            [$y, $m] = array_slice($argv, 2, 2);

            // 1일 기준 간지 (CoreLunar 사용)
            $gz = $core->getGanZhi((int)$y, (int)$m, 1, 12);

            // 절기 (중기) 매핑
            $midTerms = [
                2 => "입춘", 3 => "경칩", 4 => "청명",
                5 => "입하", 6 => "망종", 7 => "소서",
                8 => "입추", 9 => "백로", 10 => "한로",
                11 => "입동", 12 => "대설", 1 => "소한"
            ];

            $rule = "approx";
            $targetDate = sprintf("%04d-%02d-01", $y, $m);

            if (isset($midTerms[(int)$m])) {
                $season = $api->season($midTerms[(int)$m], (int)$y);
                if ($season) {
                    $rule = "midTermBased";
                    $targetDate = $season->date;
                }
            }

            echo json_encode([
                "gan"        => $gz['gan'] ?? null,
                "ji"         => $gz['ji'] ?? null,
                "rule"       => $rule,
                "targetDate" => $targetDate
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode(["error" => "Unknown command: $cmd"]);
    }
} catch (Throwable $e) {
    echo json_encode([
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
}
