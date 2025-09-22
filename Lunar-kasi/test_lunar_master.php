<?php
// test_lunar_master.php (calendar() 함수 사용 최종 검증 버전)

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

echo "--- Lunar-master 라이브러리 최종 정확도 테스트 (calendar 함수 사용) ---\n";

try {
    // 1. Lunar_API 클래스가 정의된 파일을 정확하게 require_once 합니다.
    require_once __DIR__ . '/../Lunar-master/Lunar/Lunar_API.php';
    
    // 2. 다른 파일을 찾을 수 있도록 include_path를 설정합니다.
    $lunarMasterDir = __DIR__ . '/../Lunar-master';
    set_include_path(get_include_path() . PATH_SEPARATOR . $lunarMasterDir);

    // 3. "oops" 네임스페이스를 포함하여 클래스를 상속받습니다.
    class LunarMasterTest extends oops\Lunar_API {
        public function __call($name, $args) {
            return parent::$name(...$args);
        }
    }

    // 4. 테스트할 날짜를 지정합니다.
    $year = 2025;
    $month = 9;
    $day = 1;

    echo "입력된 양력: {$year}년 {$month}월 {$day}일\n";

    // 5. [핵심] Lunar-master의 인스턴스를 생성하고 진짜 함수인 calendar()를 호출합니다.
    $lunarMaster = new LunarMasterTest();
    // calendar() 함수는 년, 월, 일을 인자로 받습니다.
    $calendarData = $lunarMaster->calendar($year, $month, $day);

    // 6. calendar()가 반환한 상세 정보에서 일진(ganzhi) 값을 추출합니다.
    $ganjiIlju = $calendarData['ganzhi'];
    echo "계산된 일진: {$ganjiIlju}\n";

    // 7. 정확성 판별
    if ($ganjiIlju === '계유(癸酉)') {
        echo "\n[결론] ✅ 드디어 찾았습니다! Lunar-master의 'calendar' 함수가 정확합니다.\n";
    } else {
        echo "\n[결론] ❌ 이 라이브러리는 사용할 수 없습니다. (결과: {$ganjiIlju})\n";
    }

} catch (Throwable $e) {
    echo "\n[오류] 테스트 중 심각한 오류 발생: " . $e->getMessage() . "\n";
}