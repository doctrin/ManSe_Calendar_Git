<?php
/**
 * Project: oops\KASI\Lunar:: 한국천문연구원 데이터를 기반으로 한 음/양력 변환 클래스<br>
 * Files:   Lnuar.php<br>
 * Dependency:
 *   - {@link http://pear.oops.org/docs/li_myException.html oops/myException}
 *   - {@link http://kr1.php.net/manual/en/book.calendar.php calendar extension}
 *
 * 이 패키지는 한국천문연구원의 음양력 데이터를 기반으로 하여 양력/음열간의 변환을
 * 제공하며, aero님의 Date-Korean-0.0.2 perl module을 PHP로 포팅한 것이다.
 *
 * 양력 기준으로 1391-02-05 부터 2050-12-31 까지의 기간만 가능하며, 절기, 합삭/망
 * 정보, 세차/월간/일진등의 정보는 oops\Lunar pear package를 이용하도록 한다.
 *
 * 이 패키지는 pear/Lunar package의 확장을 위하여 제작이 되었으며, 라이센스 문제로
 * pear/Lunar package와 별도의 패키지로 제작이 되었다.
 *
 * @category    Calendar
 * @package     oops\KASI\Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2024, OOPS.org
 * @license     GPL (or Perl license)
 * @link        https://github.com/OOPS-ORG-PHP/KASI-Lunar
 * @since       File available since release 0.0.1
 * @example     KASI-Lunar/tests/test.php Sample code
 * @filesource
 */
 
/**
 * Namespace oops\KASI;
 */
namespace oops\KASI;
 
/**
 * import myException class
 */
//require_once 'myException.php';
//set_error_handler('myException::myErrorHandler');
 
/**
 * import Lunar API
 */
require_once 'KASI_Lunar/Lunar_Tables.php';
require_once 'KASI_Lunar/Lunar_Seasons.php';
 
 
/**
 * oops\KASI pear package의 main class
 *
 * 한국천문연구원 데이터를 기반으로 한 음/양력 변환 클래스
 *
 * 이 패키지는 한국천문연구원의 음양력 데이터를 기반으로 하여 양력/음열간의 변환을
 * 제공하며, aero님의 Date-Korean-0.0.2 perl module을 PHP로 포팅한 것이다.
 *
 * 양력 기준으로 1391-02-05 부터 2050-12-31 까지의 기간만 가능하며, 절기, 합삭/망
 * 정보, 세차/월간/일진등의 정보는 oops\Lunar pear package를 이용하도록 한다.
 *
 * 이 패키지는 pear/Lunar package의 확장을 위하여 제작이 되었으며, 라이센스 문제로
 * pear/Lunar package와 별도의 패키지로 제작이 되었다. 물론 독립적으로 사용해도 무방
 * 하다.
 *
 * @package     oops/KASI/Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2024, OOPS.org
 * @license     GPL (or Perl license)
 * @example     KASI-Lunar/tests/test.php Sample code
 */
 
class Lunar {
    // {{{ +-- public properties
    /**#@+
     * @access public
     */
    /**
     * enable exception
     * @var boolean 
     */
    public $is_exception = true;
    /**#@-*/
    // }}}
 
    // {{{ +-- public version (void)
    /**
     * KASI_Lunar 의 현재 버전을 출력
     *
     * @access public
     * @return string  KASI_Lunar 버전
     */
    public function version () {
        # 관리를 위해 꼭 single quote 를 사용한다!
        return '2.0.1';
    }
    // }}}
 
    // {{{ +-- public (object) tolunar ($v = null)
    /**
     * 양력 날자를 음력으로 변환
     *
     * 예제:
     * {@example KASI-Lunar/tests/test.php 21 51}
     *
     * @access public
     * @return stdClass    음력 날자 정보 반환
     *
     *    <pre>
     *    stdClass Object
     *    (
     *        [fmt]    => 2013-06-09       // YYYY-MM-DD 형식의 음력 날자
     *        [jd]     => 2456582          // 율리어스 적일
     *        [year]   => 2013             // 연도
     *        [month]  => 6                // 월
     *        [day]    => 9                // 일
     *        [leap]   =>                  // 음력 윤달 여부
     *        [lmonth] => 1                // 평달/큰달 여부
     *    )
     *    </pre>
     *
     * @param int|string  날자형식
     *    - unixstmap (1970년 12월 15일 이후부터 가능)
     *    - Ymd or Y-m-d
     *    - null data (현재 시간)
     *    - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
     *      예.. 10월 14일은 그레고리력 10월 24일
     */
    public function tolunar ($v = null) {
        list ($y, $m, $d) = $this->toargs ($v);
 
        $jd = $this->cal2jd (array ($y, $m, $d));
 
        if ( $jd < Tables::$MinDate || $jd > Tables::$MaxDate ) {
            if ( $this->is_exception == true ) {
                throw new \myException (
                    'Invalid date period. Valid period is from 1391-02-05 to 2050-12-31 with solar',
                    E_USER_WARNING
                );
            }
            return false;
        }
 
        $day = $jd - Tables::$MinDate;
 
        $mon = $this->bisect (Tables::$month, $day);
        $yer = $this->bisect (Tables::$year, $mon);
 
        $month = $mon - Tables::$year[$yer] + 1;
        $days  = $day  - Tables::$month[$mon] + 1;
 
        // 큰달 작은달 체크
        $lmoon = Tables::$month[$mon+1] - Tables::$month[$mon];
        $lmoon = ($lmoon == 29) ? false : true;
 
        // 윤달 체크
        $leap = false;
        if ( Tables::$leap[$yer] != 0 && Tables::$leap[$yer] <= $month ) {
            if ( Tables::$leap[$yer] == $month )
                $leap = true;
            $month -= 1;
        }
 
        $year = $yer + Tables::$BaseYear;
 
        return (object) array (
            'fmt'   => $this->datestring ($year, $month, $days, '-'),
            'jd'    => $jd,
            'year'  => $year,
            'month' => $month,
            'day'   => $days,
            'week'  => $this->jd2week ($jd),
            'leap'  => $leap,
            'lmoon' => $lmoon
        );
    }
    // }}}
 
    // {{{ +-- public (object) tosolar ($v = null, $leap = false)
    /**
     * 음력 날자를 양력으로 변환.
     *
     * 예제:
     * {@example KASI-Lunar/tests/test.php 21 51}
     *
     * @access public
     * @return stdClass    양력 날자 정보 object 반환
     *
     *    <pre>
     *    stdClass Object
     *    (
     *        [fmt]   => 2013-06-09       // YYYY-MM-DD 형식의 음력 날자
     *        [jd]    => 2456527          // 율리어스 적일
     *        [year]  => 2013             // 양력 연도
     *        [month] => 7                // 월
     *        [day]   => 16               // 일
     *        [week]  => 6                // 요일
     *    )
     *    </pre>
     *
     * @param int|string날자형식
     *
     *    - unixstmap (1970년 12월 15일 이후부터 가능)
     *    - Ymd or Y-m-d
     *    - null data (현재 시간)
     *
     * @param bool 윤달여부
     */
    public function tosolar ($v = null, $leap = false) {
        list ($y, $m, $d) = $this->toargs ($v, true);
 
        $yer = $y - Tables::$BaseYear;
 
        // 윤달이 아닐 경우 값 보정
        if ( $leap && (Tables::$leap[$yer] - 1) != $m )
             $leap = false;
 
        $month = Tables::$year[$yer] + $m - 1;
 
        if ( $leap && ($m + 1) == Tables::$leap[$yer] )
            $month++;
        else if ( Tables::$leap[$yer] && Tables::$leap[$yer] <= $m )
            $month++;
 
        $day = Tables::$month[$month] + $d - 1;
        if ( $d < 1 || $day >= Tables::$month[$month+1] ) {
            throw new \myException ('Invalid day', E_USER_WARNING);
            return false;
        }
 
        $day += Tables::$MinDate;
        $r = $this->jd2cal ($day);
 
        return (object) array (
            'fmt'   => $this->datestring ($r->year, $r->month, $r->day, '-'),
            'jd'    => $day,
            'year'  => $r->year,
            'month' => $r->month,
            'day'   => $r->day,
            'week'  => $r->week
        );
    }
    // }}}
 
    // {{{ +-- public (int) cal2jd ($v)
    /**
     * 날자를 율리우스 적일로 변환
     *
     * @access public
     * @return int 율리우스 적일(integer)
     * @param array 연/월/일 배열 : array ($y, $m, $d)
     */
    public function cal2jd ($v) {
        if ( ! extension_loaded ('calendar') ) {
            if ( $this->is_exception == true )
                throw new \myException ('Don\'t support the calendar extension in PHP', E_USER_WARNING);
            return false;
        }
 
        list ($y, $m, $d) = $v;
 
        $julian = false;
 
        $datestr = $this->datestring ($y, $m, $d);
        $julian = false;
        if ( $datestr < 15821015 ) {
            $julian = true;
            if ( $datestr > 15821004 ) {
                $d += 10;
                $julian = false;
            }
        }
 
        $old = date_default_timezone_get ();
        date_default_timezone_set ('UTC');
 
        $func = $julian ? 'JulianToJD' : 'GregorianToJD';
        if ( $y < 1 )
            $y--;
        $r = $func ((int) $m, (int) $d, (int) $y);
 
        date_default_timezone_set ($old);
        return $r;
    }
    // }}}
 
    // {{{ +-- public (object) cal4jd ($jd = null)
    /**
     * 율리우스 적일을 율리우스력 또는 그레고리력으로 변환
     *
     * oops\KASI\Lunar::jd2cal 의 alias method로 deprecated
     * 되었기 때문에 jd2cal method로 변경 해야 함.
     *
     * 1.0.2 부터 삭제 예정
     *
     * @access public
     * @return stdClass    율리우스력 또는 그레고리력 정보
     *
     *    <pre>
     *    stdClass Object
     *    (
     *        [year] => 2013              // 양력 연도
     *        [month] => 7                // 월
     *        [day] => 16                 // 일
     *        [week] => 6                 // 요일
     *    )
     *    </pre>
     *
     * @param int 율리우스 적일. [default: 현재날의 적일]
     * @deprecated deprecated since version 1.0.1
     */
    public function cal4jd ($jd = null) {
        return $this->jd2cal ($jd);
    }
    // }}}
 
    // {{{ +-- public (object) jd2cal ($jd = null)
    /**
     * 율리우스 적일을 율리우스력 또는 그레고리력으로 변환
     *
     * @access public
     * @return stdClass    율리우스력 또는 그레고리력 정보
     *
     *    <pre>
     *    stdClass Object
     *    (
     *        [year] => 2013              // 양력 연도
     *        [month] => 7                // 월
     *        [day] => 16                 // 일
     *        [week] => 6                 // 요일
     *    )
     *    </pre>
     *
     * @param int 율리우스 적일. [default: 현재날의 적일]
     */
    public function jd2cal ($jd = null) {
        if ( ! extension_loaded ('calendar') ) {
            if ( $this->is_exception == true )
                throw new \myException ('Don\'t support the calendar extension in PHP', E_USER_WARNING);
            return false;
        }
 
        if ( ! $jd )
            $jd = unixtojd (time ());
 
        $cvt = ($jd > 2299160) ? CAL_GREGORIAN : CAL_JULIAN;
        $r = (object) cal_from_jd ($jd, $cvt);
        if ( $r->year < 0 )
            $r->year++;
 
        return (object) array (
            'year'  => $r->year,
            'month' => $r->month,
            'day'   => $r->day,
            'week'  => $r->dow
        );
    }
    // }}}
 
    // {{{ +-- public (int) jd2week ($jd = null)
    /**
     * 율리우스 적일로 요일 정보를 구함
     *
     * @access public
     * @return int         요일 배열 인덱스
     *
     *  0(일) ~ 6(토)
     *
     * @param int 율리우스 적일. [default: 현재날의 적일]
     */
    public function jd2week ($jd = null) {
        if ( ! $jd ) {
            $today = date ('Y-m-d', time ());
            $t = preg_split ('/-/', $today);
            $jd = $this->cal2jd ($t);
        }
 
        return fmod ($jd + 1, 7);
        /*
        $mjd = $jd - 2400000.5;
        $widx = $mjd % 7 + 3;
        if ( $widx < 3 )
            $widx += 7;
 
        if ( $widx > 6 )
            $widx -= 7;
 
        return $widx;
         */
    }
    // }}}
 
    // {{{ +-- public (object) season ($so, $year = null)
    /**
     * 연도별 절기의 입절시각을 구함
     *
     * @access public
     * @return array|false
     *
     *    <pre>
     *    stdClass Object
     *    (
     *        [name]   => 입춘             // 절기 이름
     *        [hname]  => 立春             // 절기 한지 이름
     *        [stamp]  => 1073348340       // 입절시각. Unix timestamp
     *        [date]   => 2004-02-04 20:56 // 입절 시각. YYYY-MM-DD HH:mm 형식의 양력 날자
     *        [year]   => 2004             // 입절 시각 연도
     *        [month]  => 2                // 입절 시각 월
     *        [day]    => 4                // 입절 시각 일
     *        [hour]   => 20               // 입절 시각 시간
     *        [min]    => 56               // 입절 시각 분
     *    )
     *    </pre>
     *
     * @param int 연도
     * @param string 절기 이름
     */
    public function season ($name, $year = null) {
        if ( $year == null )
            $year = date ('%Y', time ());
 
        if ( $year > 2026 || $year < 2004 ) {
            if ( $this->is_exception == true ) {
                throw new \myException (
                    'Support between 2004 and 2026',
                    E_USER_WARNING
                );
            }
            return false;
        }
 
        $id = array_search ($name, Seasons::$so24n);
        if ( $id === false ) {
            if ( $this->is_exception == true ) {
                throw new \myException (
                    "Invalid Invalid season name ($name).",
                    E_USER_WARNING
                );
            }
            return false;
        }
        $hid = Seasons::$so24hn[$id];
        # 절기 배열은 2004년이 첫번째 배열이다.
        $sid = $year - 2004;
        $so = Seasons::$so24[$sid][$id];
 
        $t = preg_split ('/[-]/', date ('Y-m-d-H-i-s', $so));
 
        return (object) array (
            'name'  => $name,
            'hname' => $hid,
            'stamp' => $so,
            'date'  => sprintf ("%d-%02d-%02d %02d:%02d", $t[0], $t[1], $t[2], $t[3], $t[4]),
            'year'  => $t[0],
            'month' => $t[1],
            'day'   => $t[2],
            'hour'  => $t[3],
            'min'   => $t[4]
        );
    }
    // }}}
 
    // {{{ +-- private (array) toargs ($v, $lanur = false)
    /**
     * 입력된 날자 형식을 연/월/일의 멤버를 가지는 배열로 반환한다.
     * 입력된 변수 값은 YYYY-MM-DD 형식으로 변환 된다.
     *
     * 예제:
     * {@example Lunar/tests/sample.php 30 25}
     *
     * @access public
     * @return array 
     *    <pre>
     *        Array
     *        (
     *            [0] => 2013
     *            [1] => 7
     *            [2] => 16
     *        )
     *    </pre>
     * @param string|int날자형식
     *
     *    - unixstmap (1970년 12월 15일 이후부터 가능)
     *    - Ymd or Y-m-d
     *    - null data (현재 시간)
     */
    private function toargs (&$v, $lunar = false) {
        if ( $v == null ) {
            $y = (int) date ('Y');
            $m = (int) date ('m');
            $d = (int) date ('d');
        } else {
            if ( $lunar ) {
 
            }
 
            if ( is_numeric ($v) && $v > 30000000 ) {
                // unit stamp ?
                $y = (int) date ('Y', $v);
                $m = (int) date ('m', $v);
                $d = (int) date ('d', $v);
            } else {
                if ( preg_match ('/^(-?[0-9]{1,4})[\/-]?([0-9]{1,2})[\/-]?([0-9]{1,2})$/', trim ($v), $match) ) {
                    array_shift ($match);
                    list ($y, $m, $d) = $match;
                } else {
                    if ( $this->is_exception == true )
                        throw new \myException ('Invalid Date Format', E_USER_WARNING);
                    return false;
                }
            }
 
            if ( ! $lunar && $y > 1969 && $y < 2038 ) {
                $fixed = mktime (0, 0, 0, $m, $d, $y);
                $y = (int) date ('Y', $fixed);
                $m = (int) date ('m', $fixed);
                $d = (int) date ('d', $fixed);
            } else {
                if ( $m > 12 || $d > 31 ) {
                    if ( $this->is_exception == true )
                        throw new \myException ('Invalid Date Format', E_USER_WARNING);
                    return false;
                }
            }
        }
        $v = $this->datestring ($y, $m, $d, '-');
 
        return array ($y, $m, $d);
    }
    // }}}
 
    // {{{ +-- private bisect ($a, $x)
    private function bisect ($a, $x) {
        $lo = 0;
        $hi = count ($a);
 
        while ( $lo < $hi ) {
            $mid = (int) (($lo + $hi) / 2);
            if ( $x < $a[$mid] )
                $hi = $mid;
            else
                $lo = $mid + 1;
        }
 
        return --$lo;
    }
    // }}}
 
    // {{{ +-- private datestring ($y, $m, $d)
    private function datestring ($y, $m, $d, $dash = '') {
        if ( $m < 10 )
            $m = '0' . (int) $m;
        if ( $d < 10 )
            $d = '0' . (int) $d;
 
        return $y . $dash . $m . $dash . $d;
    }
    // }}}
}
?>