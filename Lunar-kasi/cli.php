<?php
/**
 * [진짜 최종 완성본]
 * index.php 소스 코드 기반, 요일(weekday) 데이터 오류를 수정한 최종 CLI.
 */

// 불필요한 경고 메시지는 출력하지 않도록 설정
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

try {
    // 1. index.php와 동일하게, 필요한 라이브러리 파일들을 로드합니다.
    require_once __DIR__ . '/KASI_Lunar.php';
    require_once __DIR__ . '/Lunar.php';

    // 2. CLI 인자(Arguments)를 파싱합니다.
    $args = array_slice($argv, 1);
    if (empty($args)) {
        echo "사용법: php cli.php [command] [arguments...]\n";
        echo "지원 명령어: solarToLunar\n";
        exit(0);
    }
    $command = array_shift($args);
    
    // 3. 명령어에 따라 로직을 분기합니다.
    $result = null;
    switch ($command) {
        case 'solarToLunar':
            if (count($args) < 3) {
                throw new InvalidArgumentException("solarToLunar: 3개의 인자(년, 월, 일)가 필요합니다.");
            }
            list($year, $month, $day) = $args;
            
            // 4. index.php와 동일한 방식으로 객체를 생성하고 함수를 호출합니다.
            $lunar = new oops\Lunar;
            $dateString = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $lunarData = $lunar->tolunar($dateString);
            $ganjiData = $lunar->dayfortune($dateString);

            // 5. [핵심 수정] 한글 요일을 숫자로 변환합니다.
            $weekdayMap = ['일' => 0, '월' => 1, '화' => 2, '수' => 3, '목' => 4, '금' => 5, '토' => 6];
            $weekdayNumber = isset($weekdayMap[$lunarData->week]) ? $weekdayMap[$lunarData->week] : null;

            // 6. 가져온 데이터를 Node.js가 사용하기 좋은 JSON 형식으로 조합합니다.
            $result = [
                'year'    => (int)$lunarData->year,
                'month'   => (int)$lunarData->month,
                'day'     => (int)$lunarData->day,
                'leap'    => $lunarData->leap,
                'weekday' => $weekdayNumber, // 수정된 요일 숫자
                'ganji'   => "{$ganjiData->hyear}년 {$ganjiData->hmonth}월 {$ganjiData->hday}일"
            ];
            break;
        
        default:
            throw new InvalidArgumentException("현재 'solarToLunar' 명령어만 지원합니다.");
            break;
    }
    
    // 7. 최종 결과를 JSON으로 출력합니다.
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    if (!headers_sent()) { http_response_code(400); }
    echo json_encode(['error' => get_class($e) . ': ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}

echo "\n";
exit(0);