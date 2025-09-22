<?php
/**
 * Project: Lunar_API :: 양력/음력 변환 코어 클래스<br>
 * File:    Lunar_API.php
 *
 * 이 패키지는 양력/음력간의 변환을 제공하는 API로, 고영창님의 '진짜만세력'
 * 0.92(Perl version)와 0.93(Pascal version)버전을 PHP로 포팅한 것이다.
 *
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -4712-02-08 ~ 9999-12-31
 *     + -9999-01-01 ~ 9999-12-31
 *     + 64bit 계산이 가능한 시점까지 가능할 듯..
 *
 * </pre>
 *
 * @category    Calendar
 * @package     oops\Lunar
 * @subpackage	Lunar Core API
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2024, OOPS.org
 * @license     고영창 (http://afnmp3.homeip.net/~kohyc/calendar/index.cgi)
 * @link        https://github.com/OOPS-ORG-PHP/Lunar
 * @filesource
 */

/**
 * Namespace oops
 */
namespace oops;

/**
 * Lunar Core API
 *
 * 이 패키지는 양력/음력간의 변환을 제공하는 API로, 고영창님의 '진짜만세력'
 * 0.92(Perl version)와 0.93(Pascal version)버전을 PHP로 포팅한 것이다.
 *
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -4712-02-08 ~ 9999-12-31
 *     + -9999-01-01 ~ 9999-12-31
 *     + 64bit 계산이 가능한 시점까지 가능할 듯..
 *
 * </pre>
 *
 * @package     oops\Lunar
 * @subpackage	Lunar Core API
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2024, OOPS.org
 * @license     고영창 (http://afnmp3.homeip.net/~kohyc/calendar/index.cgi)
 * @filesource
 */
Class Lunar_API {
	// {{{ +-- protected prpperties
	/**#@+
	 * @access protected
	 */
	/**
	 * @var array
	 */
	protected $month = array (
		0, 21355, 42843, 64498, 86335, 108366, 130578, 152958,
		175471, 198077, 220728, 243370, 265955, 288432, 310767,
		332928, 354903, 376685, 398290, 419736, 441060, 462295,
		483493, 504693, 525949
	);

	/**
	 * 십간(十干) 데이터
	 * @var array
	 */
	protected $gan = array ('갑', '을', '병', '정', '무', '기', '경', '신', '임', '계');
	/**
	 * 십간(十干) 한자 데이터
	 * @var array
	 */
	protected $hgan = array ('甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸');
	/**
	 * 십이지(十二支) 데이터
	 * @var array
	 */
	protected $ji = array ('자', '축', '인', '묘', '진', '사', '오', '미', '신', '유', '술', '해');
	/**
	 * 십이지(十二支) 한자 데이터
	 * @var array
	 */
	protected $hji = array ('子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥');
	/**
	 * 띠 데이터
	 * @var array
	 */
	protected $ddi = array ('쥐', '소', '호랑이', '토끼', '용', '뱀', '말', '양', '원숭이', '닭', '개', '돼지');

	/**#@+
	 * 병자년 경인월 신미일 기해시 입춘 데이터
	 * @var integer
	 */
	protected $unityear  = 1996;
	protected $unitmonth = 2;
	protected $unitday   = 4;
	protected $unithour  = 22;
	protected $unitmin   = 8;
	protected $unitsec   = 0;
	/**#@-*/

	/**#@+
	 * 병자년 데이터
	 * @var integer
	 */
	protected $uygan = 2;
	protected $uyji  = 0;
	protected $uysu  = 12;
	/**#@-*/

	/**#@+
	 * 경인년 데이터
	 * @var integer
	 */
	protected $umgan = 6;
	protected $umji  = 2;
	protected $umsu  = 26;
	/**#@-*/

	/**#@+
	 * 신미일 데이터
	 * @var integer
	 */
	protected $udgan = 7;
	protected $udji  = 7;
	protected $udsu  = 7;
	/**#@-*/

	/**#@+
	 * 기해시 데이터
	 * @var integer
	 */
	protected $uhgan = 5;
	protected $uhji  = 11;
	protected $uhsu  = 35;
	/**#@-*/

	/**#@+
	 * 정월 초하루 합삭 시간
	 * @var integer
	 */
	protected $unitmyear  = 1996;
	protected $unitmmonth = 2;
	protected $unitmday   = 19;
	protected $unitmhour  = 8;
	protected $unitmmin   = 30;
	protected $unitmsec   = 0;
	protected $moonlength = 42524;
	/**#@-*/
	/**#@-*/
	// }}}

	// {{{ +-- public prpperties
	/**#@+
	 * @access public
	 */
	/**
	 * 절기 데이터
	 * @var array
	 */
	protected $month_st = array (
		'입춘', '우수', '경칩', '춘분', '청명', '곡우',
		'입하', '소만', '망종', '하지', '소서', '대서',
		'입추', '처서', '백로', '추분', '한로', '상강',
		'입동', '소설', '대설', '동지', '소한', '대한',
		'입춘'
	);

	/**
	 * 절기(한자) 데이터
	 * @var array
	 */
	protected $hmonth_st = array (
		'立春', '雨水', '驚蟄', '春分', '淸明', '穀雨',
		'立夏', '小滿', '芒種', '夏至', '小暑', '大暑',
		'立秋', '處暑', '白露', '秋分', '寒露', '霜降',
		'立冬', '小雪', '大雪', '冬至', '小寒', '大寒',
		'立春'
	);

	/**
	 * 60간지 데이터
	 * @var array
	 */
	protected $ganji = array (
		'갑자', '을축', '병인', '정묘', '무진', '기사', '경오', '신미', '임신', '계유', '갑술', '을해',
		'병자', '정축', '무인', '기묘', '경진', '신사', '임오', '계미', '갑신', '을유', '병술', '정해',
		'무자', '기축', '경인', '신묘', '임진', '계사', '갑오', '을미', '병신', '정유', '무술', '기해',
		'경자', '신축', '임인', '계묘', '갑진', '을사', '병오', '정미', '무신', '기유', '경술', '신해',
		'임자', '계축', '갑인', '을묘', '병진', '정사', '무오', '기미', '경신', '신유', '임술', '계해'
	);
	/**
	 * 60간지 한자 데이터
	 * @var array
	 */
	protected $hganji = array (
		'甲子','乙丑','丙寅','丁卯','戊辰','己巳','庚午','辛未','壬申','癸酉','甲戌','乙亥',
		'丙子','丁丑','戊寅','己卯','庚辰','辛巳','壬午','癸未','甲申','乙酉','丙戌','丁亥',
		'戊子','己丑','庚寅','辛卯','壬辰','癸巳','甲午','乙未','丙申','丁酉','戊戌','己亥',
		'庚子','辛丑','壬寅','癸卯','甲辰','乙巳','丙午','丁未','戊申','己酉','庚戌','辛亥',
		'壬子','癸丑','甲寅','乙卯','丙辰','丁巳','戊午','己未','庚申','辛酉','壬戌','癸亥'
	);

	/**
	 * 요일 데이터
	 * @var array
	 */
	protected $week = array ('일','월','화','수','목','금','토');
	/**
	 * 요일 한자 데이터
	 * @var array
	 */
	protected $hweek = array ('日','月','火','水','木','金','土');
	/**
	 * 28일 데이터
	 * @var array
	 */
	protected $s28days = array (
		'角','亢','氐','房','心','尾','箕',
		'斗','牛','女','虛','危','室','壁',
		'奎','婁','胃','昴','畢','觜','參',
		'井','鬼','柳','星','張','翼','軫'
	);
	protected $s28days_hangul = array (
		'각', '항', '저', '방', '심', '미', '기',
		'두', '우', '녀', '허', '위', '실', '벽',
		'규', '수', '위', '묘', '필', '자', '삼',
		'정', '귀', '류', '성', '장', '익', '진'
	);
	/**#@-*/
	// }}}

	// 節(월 시작을 여는 12개)만 뽑아내기 위한 이름 목록
	private array $JIE_NAMES = ['立春','驚蟄','清明','立夏','芒種','小暑','立秋','白露','寒露','立冬','大雪','小寒'];

	// 節 이름 → 월지(寅~丑) 매핑
	private array $JIE_TO_BRANCH = [
		'立春' => '寅', '驚蟄' => '卯', '清明' => '辰',
		'立夏' => '巳', '芒種' => '午', '小暑' => '未',
		'立秋' => '申', '白露' => '酉', '寒露' => '戌',
		'立冬' => '亥', '大雪' => '子', '小寒' => '丑'
	];


	// {{{ +-- protected (int) div ($a, $b)
	/**
	 * 정수 몫을 반환
	 *
	 * @access protected
	 * @return int
	 * @param int
	 * @param int
	 */
	protected function div ($a, $b) {
		return (int) ($a / $b);
	}
	// }}}

	// {{{ +-- protected (int) disptimeday ($year, $month, $day)
	/**
	 * year의 1월 1일부터 해당 일자까지의 날자수
	 *
	 * @access public
	 * @return int 날자수
	 * @param  int 년
	 * @param  int 월
	 * @param  int 일
	 */
	protected function disptimeday ($year, $month, $day) {
		$e = $i = 0;

		for ( $i=1; $i<$month; $i++ ) {
			$e += 31;
			if ( $i == 2 || $i == 4 || $i == 6 || $i == 9 || $i == 11 )
				$e--;

			if ( $i == 2 ) {
				$e -= 2;
				if ( ($year % 4) == 0 ) $e++;
				if ( ($year % 100) == 0 ) $e--;
				if ( ($year % 400) == 0 ) $e++;
				if ( ($year % 4000) == 0 ) $e--;
			}
		}
		$e += $day;

		return $e;
	}
	// }}}

	// {{{ +-- protected (int) disp2days ($y1, $m1, $d1, $y2, $m2, $d2)
	/**
	 * y1,m1,d1일부터 y2,m2,d2까지의 일수 계산
	 *
	 * @access protected
	 * @return int 날자수
	 * @param int from year
	 * @param int from month
	 * @param int from day
	 * @param int until year
	 * @param int until month
	 * @param int until day
	 */
	protected function disp2days ($y1, $m1, $d1, $y2, $m2, $d2) {
		$p1 = $p2 = $pn1 = $pp1 = $pp2 = $pr = $dis = $ppp1 = $ppp2 = $k = 0;

		if ( $y2 > $y1 ) {
			$p1  = $this->disptimeday ($y1, $m1, $d1);
			$p1n = $this->disptimeday ($y1, 12, 31);
			$p2  = $this->disptimeday ($y2, $m2, $d2);
			$pp1 = $y1;
			$pp2 = $y2;
			$pr  = -1;
		} else {
			$p1  = $this->disptimeday ($y2, $m2, $d2);
			$p1n = $this->disptimeday ($y2, 12, 31);
			$p2  = $this->disptimeday ($y1, $m1, $d1);
			$pp1 = $y2;
			$pp2 = $y1;
			$pr  = 1;
		}

		if ( $y2 == $y1 )
			$dis = $p2 - $p1;
		else {
			$dis = $p1n - $p1;
			$ppp1 = $pp1 + 1;
			$ppp2 = $pp2 - 1;

			for ( $k = $ppp1; $k <= $ppp2; $k++ ) {
				# 0.93 diff {{{
				if ( $k == -9000 && $ppp2 > 1990 ) {
					$dis += 4014377;
					$k = 1991;
				} else if ( $k == -8000 && $ppp2 > 1990 ) {
					$dis += 3649135;
					$k = 1991;
				} else if ( $k == -7000 && $ppp2 > 1990 ) {
					$dis += 3283893;
					$k = 1991;
				} else if ( $k == -6000 && $ppp2 > 1990 ) {
					$dis += 2918651;
					$k = 1991;
				} else if ( $k == -5000 && $ppp2 > 1990 ) {
					$dis += 2553408;
					$k = 1991;
				} else if ( $k == -4000 && $ppp2 > 1990 ) {
					$dis += 2188166;
					$k = 1991;
				} else if ( $k == -3000 && $ppp2 > 1990 ) {
					$dis += 1822924;
					$k = 1991;
				# 0.93 diff }}}
				} else if ( $k == -2000 && $ppp2 > 1990 ) {
					$dis += 1457682;
					$k = 1991;
				} else if ( $k == -1750 && $ppp2 > 1990 ) {
					$dis += 1366371;
					$k = 1991;
				} else if ( $k ==-1500 && $ppp2 > 1990 ) {
					$dis += 1275060;
					$k = 1991;
				} else if ( $k ==-1250 && $ppp2 > 1990 ) {
					$dis += 1183750;
					$k = 1991;
				} else if ( $k ==-1000 && $ppp2 > 1990 ) {
					$dis += 1092439;
					$k = 1991;
				} else if ( $k == -750 && $ppp2 > 1990 ) {
					$dis += 1001128;
					$k = 1991;
				} else if ( $k == -500 && $ppp2 > 1990 ) {
					$dis += 909818;
					$k = 1991;
				} else if ( $k == -250 && $ppp2 > 1990 ) {
					$dis += 818507;
					$k = 1991;
				} else if ( $k == 0 && $ppp2 > 1990 ) {
					$dis += 727197;
					$k = 1991;
				} else if ( $k == 250 && $ppp2 > 1990 ) {
					$dis += 635887;
					$k = 1991;
				} else if ( $k == 500 && $ppp2 > 1990 ) {
					$dis += 544576;
					$k = 1991;
				} else if ( $k == 750 && $ppp2 > 1990 ) {
					$dis += 453266;
					$k = 1991;
				} else if ( $k == 1000 && $ppp2 > 1990 ) {
					$dis += 361955;
					$k = 1991;
				} else if ( $k == 1250 && $ppp2 > 1990 ) {
					$dis += 270644;
					$k = 1991;
				} else if ( $k == 1500 && $ppp2 > 1990 ) {
					$dis += 179334;
					$k = 1991;
				} else if ( $k == 1750 && $ppp2 > 1990 ) {
					$dis += 88023;
					$k = 1991;
				}

				$dis += $this->disptimeday ($k, 12, 31);
			}

			$dis += $p2;
			$dis *= $pr;
		}

		return $dis;
	}
	// }}}

	// {{{ +-- protected (int) getminbytime ($uy, $umm, $ud, $uh, $umin, $y1, $mo1, $d1, $h1, $mm1)
	/**
	 * uy,umm,ud,uh,umin과 y1,mo1,d1,h1,mm1사이의 시간(분)
	 *
	 * @access protected
	 * @return int 분
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 */
	function getminbytime ($uy, $umm, $ud, $uh, $umin, $y1, $mo1, $d1, $h1, $mm1) {
		$t = 0;

		$dispday = $this->disp2days ($uy, $umm, $ud, $y1, $mo1, $d1);
		$t = $dispday * 24 * 60 + ($uh - $h1) * 60 + ($umin - $mm1);

		return $t;
	}
	// }}}

	// {{{ +-- protected (array) getdatebymin ($tmin, $uyear, $umonth, $uday, $uhour, $umin)
	/**
	 * uyear,umonth,uday,uhour,umin으로부터 tmin(분)떨이진 시점의
	 * 년월일시분(태양력) 구하는 프로시져
	 *
	 * @access public
	 * @return array
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 */
	protected function getdatebymin ($tmin, $uyear, $umonth, $uday, $uhour, $umin) {
		$y1 = $mo1 = $d1 = $h1 = $mi1 = $t = 0;
		$y1 = $uyear - $this->div ($tmin, 525949);

		if ( $tmin > 0 ) {
			$y1 += 2 ;
			do {
				$y1--;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, 1, 1, 0, 0);
			} while ( $t < $tmin );

			$mo1 = 13 ;
			do {
				$mo1--;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, 1, 0, 0);
			} while ( $t < $tmin );

			$d1 = 32;
			do {
				$d1--;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, 0, 0);
			} while ( $t < $tmin );

			$h1 = 24 ;
			do {
				$h1--;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, $h1, 0);
			} while ( $t < $tmin );

			$t = $this->getminbytime ( $uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, $h1, 0);
			$mi1 =  $t - $tmin;
		} else {
			$y1 -= 2;
			do {
				$y1++;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, 1, 1, 0, 0);
			} while ( $t >= $tmin );

			$y1--;
			$mo1 = 0;
			do {
				$mo1++;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, 1, 0, 0);
			} while ( $t >= $tmin );

			$mo1--;
			$d1 = 0;
			do {
				$d1++;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, 0, 0);
			} while ( $t >= $tmin );

			$d1--;
			$h1 = -1 ;
			do {
				$h1++;
				$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, $h1, 0);
			} while ( $t >= $tmin );

			$h1--;
			$t = $this->getminbytime ($uyear, $umonth, $uday, $uhour, $umin, $y1, $mo1, $d1, $h1, 0);
			$mi1 = $t - $tmin;
		}

		return array ($y1, $mo1, $d1, $h1, $mi1);
	}
	// }}}

	// {{{ +-- protected (array) sydtoso24yd ($soloryear, $solormonth, $solorday, $solorhour, $solormin)
	/**
	 * 그레고리력의 년월시일분으로 60년의 배수, 세차, 월건(태양력),
	 * 일진, 시주를 구함
	 *
	 * @access public
	 * @return array
	 *
	 *   <pre>
	 *   Array
	 *   (
	 *       [0] => -17  // 60년의 배수
	 *       [1] => 29   // 60간지의 연도 배열 index
	 *       [2] => 55   // 60간지의 월 배열 index
	 *       [3] => 11   // 60간지의 일 배열 index
	 *       [4] => 20   // 60간지의 시 배열 index
	 *   )
	 *   </pre>
	 *
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 */
	protected function sydtoso24yd ($soloryear, $solormonth, $solorday, $solorhour, $solormin) {
		$displ2min = $this->getminbytime (
			$this->unityear, $this->unitmonth, $this->unitday, $this->unithour, $this->unitmin,
			$soloryear, $solormonth, $solorday, $solorhour, $solormin
		);
		$displ2day = $this->disp2days (
			$this->unityear, $this->unitmonth, $this->unitday, $soloryear,
			$solormonth, $solorday
		);

		// 무인년(1996)입춘시점부터 해당일시까지 경과년수
		$so24 = $this->div ($displ2min, 525949);

		if ( $displ2min >= 0 )
			$so24++;

		// 년주 구하기
		$so24year = ($so24 % 60) * -1;
		$so24year += 12;
		if ( $so24year < 0 )
			$so24year += 60;
		else if ( $so24year > 59 )
			$so24year -= 60;

		$monthmin100 = $displ2min % 525949;
		$monthmin100 = 525949 - $monthmin100;

		if ( $monthmin100 < 0 )
			$monthmin100 += 525949;
		else if ( $monthmin100 >= 525949 )
			$monthmin100 -= 525949;

		for ( $i=0; $i<=11; $i++ ) {
			$j = $i * 2;
			if ( ($this->month[$j] <= $monthmin100) && ($monthmin100 < $this->month[$j+2]))
				$so24month = $i;
		}

		// 월주 구하기
		$i = $so24month;
		$t = $so24year % 10 ;
		$t %= 5 ;
		$t = $t * 12 + 2 + $i;
		$so24month = $t ;
		if ( $so24month > 59 )
			$so24month -= 60;

		// 일주 구하기
		$so24day = $displ2day % 60;
		$so24day *= -1;
		$so24day += 7;
		if ( $so24day < 0 )
			$so24day += 60;
		else if ( $so24day > 59 )
			$so24day -= 60;

		if ( ($solorhour == 0 || $solorhour == 1) && $solormin < 30 )
			$i = 0;

		else if ( ($solorhour == 1 && $solormin >= 30) || $solorhour == 2
			|| ($solorhour == 3 && $solormin < 30) )
			$i = 1;

		else if ( ($solorhour == 3 && $solormin >= 30) || $solorhour == 4
			|| ($solorhour == 5 && $solormin < 30) )
			$i = 2;

		else if ( ($solorhour == 5 && $solormin >= 30) || $solorhour == 6
			|| ($solorhour == 7 && $solormin < 30) )
			$i = 3;

		else if ( ($solorhour == 7 && $solormin >= 30) || $solorhour == 8
			|| ($solorhour == 9 && $solormin < 30) )
			$i = 4;

		else if ( ($solorhour == 9 && $solormin >= 30) || $solorhour == 10
			|| ($solorhour == 11 && $solormin < 30) )
			$i = 5;

		else if ( ($solorhour == 11 && $solormin >= 30) || $solorhour == 12
			|| ($solorhour == 13 && $solormin < 30) )
			$i = 6;

		else if ( ($solorhour == 13 && $solormin >= 30) || $solorhour == 14
			|| ($solorhour == 15 && $solormin < 30) )
			$i = 7;

		else if ( ($solorhour == 15 && $solormin >= 30) || $solorhour == 16
			|| ($solorhour == 17 && $solormin < 30) )
			$i = 8;

		else if ( ($solorhour == 17 && $solormin >= 30) || $solorhour == 18
			|| ($solorhour == 19 && $solormin < 30) )
			$i = 9;

		else if ( ($solorhour == 19 && $solormin >= 30) || $solorhour == 20
			|| ($solorhour == 21 && $solormin < 30) )
			$i = 10;

		else if ( ($solorhour == 21 && $solormin >= 30) || $solorhour == 22
			|| ($solorhour == 23 && $solormin < 30) )
			$i = 11;

		else if ( $solorhour == 23 && $solormin >= 30 ) {
			$so24day++;
			if ( $so24day == 60 )
				$so24day = 0;
			$i = 0;
		}

		$t = $so24day % 10;
		$t %= 5;
		$t = $t * 12 + $i;
		$so24hour = $t;

	    return array ($so24, $so24year, $so24month, $so24day, $so24hour);
	}
	// }}}

	// {{{ +-- protected (array) solortoso24 ($soloryear, $solormonth, $solorday, $solorhour, $solormin)
	/**
	 * 절기 시간 구하기
	 *
	 * 그레고리력의 년월일시분이 들어있는 절기의 이름번호,
	 * 년월일시분을 얻음
	 *
	 * @access protected
	 * @return array
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 */
	protected function solortoso24 ($soloryear, $solormonth, $solorday, $solorhour, $solormin) {
		list ($so24, $so24year, $so24month, $so24day, $so24hour) =
			$this->sydtoso24yd ($soloryear, $solormonth, $solorday, $solorhour, $solormin);

		$displ2min = $this->getminbytime (
			$this->unityear, $this->unitmonth, $this->unitday, $this->unithour, $this->unitmin,
			$soloryear, $solormonth, $solorday, $solorhour, $solormin
		);

		# 이거 고민좀 해 봐야 할 듯!!
		# perl 과 php 의 음수의 나머지 결과값이 다르다! --;
		#$monthmin100 = $displ2min % 525949;
		#$monthmin100 = 525949 - $monthmin100;
		$monthmin100 = ($displ2min % 525949) * -1;

		if ( $monthmin100 < 0 )
			$monthmin100 += 525949;
		else if ( $monthmin100 >= 525949 )
			$monthmin100 -= 525949;

		$i = $so24month % 12 - 2;
		if ( $i == -2 ) $i = 10;
		else if ( $i == -1 ) $i = 11;

		$inginame  = $i * 2 ;
		$midname   = $i * 2 + 1;
		$outginame = $i * 2 + 2;

		$j = $i * 2;
		$tmin = $displ2min + ($monthmin100 - $this->month[$j]);

		list ($y1, $mo1, $d1, $h1, $mi1) =
			$this->getdatebymin ($tmin, $this->unityear, $this->unitmonth, $this->unitday, $this->unithour, $this->unitmin);

		$ingiyear  = $y1;
		$ingimonth = $mo1;
		$ingiday   = $d1;
		$ingihour  = $h1;
		$ingimin   = $mi1;

		$tmin = $displ2min + ($monthmin100 - $this->month[$j+1]);
		list ($y1, $mo1, $d1, $h1, $mi1) =
			$this->getdatebymin ($tmin, $this->unityear, $this->unitmonth, $this->unitday, $this->unithour, $this->unitmin);

		$midyear  = $y1;
		$midmonth = $mo1;
		$midday   = $d1;
		$midhour  = $h1;
		$midmin   = $mi1;

		$tmin = $displ2min + ($monthmin100 - $this->month[$j+2]);
		list ($y1, $mo1, $d1, $h1, $mi1) =
			$this->getdatebymin ($tmin, $this->unityear, $this->unitmonth, $this->unitday, $this->unithour, $this->unitmin);

		$outgiyear  = $y1;
		$outgimonth = $mo1;
		$outgiday   = $d1;
		$outgihour  = $h1;
		$outgimin   = $mi1;

		return array (
			$inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			$midname, $midyear, $midmonth, $midday, $midhour, $midmin,
			$outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin
		);
	}
	// }}}

	// {{{ +-- protected (int) degreelow ($d)
	/**
	 * 미지의 각도를 0~360도 이내로 만듬
	 *
	 * @access protected
	 * @return int
	 * @param int
	 */
	protected function degreelow ($d) {
		$di = $d;
		$i = $this->div ((int) $d, 360);
		$di = $d - ($i * 360);

		while ( $di >= 360 || $di < 0 ) {
			if ( $di > 0 )
				$di -= 360;
			else
				$di += 360;
		}

		return $di;
	}
	// }}}

	// {{{ +-- protected (int) moonsundegree ($day)
	/**
	 * 태양황력과 달황경의 차이 (1996 기준)
	 *
	 * @access protected
	 * @return int
	 * @param int
	 */
	protected function moonsundegree ($day) {
		$sl = (float) ($day * 0.98564736 + 278.956807);                // 평균 황경
		$smin = 282.869498 + 0.00004708 * $day;                        // 근일점 황경
		$sminangle = 3.14159265358979 * ($sl - $smin) / 180;           // 근점이각
		$sd = 1.919 * sin ($sminangle) + 0.02 * sin (2 * $sminangle);  // 황경차
		$sreal = $this->degreelow ($sl + $sd);                         // 진황경

		$ml = 27.836584 + 13.17639648 * $day;                          // 평균 황경
		$mmin = 280.425774 + 0.11140356 * $day;                        // 근지점 황경
		$mminangle = 3.14159265358979 * ($ml - $mmin) / 180;           // 근점이각
		$msangle = 202.489407 - 0.05295377 * $day;                     // 교점황경
		$msdangle = 3.14159265358979 * ($ml - $msangle) / 180;
		$md = 5.06889 * sin ($mminangle)
			+ 0.146111 * sin (2 * $mminangle)
			+ 0.01 * sin (3 * $mminangle)
			- 0.238056 * sin ($sminangle)
			- 0.087778 * sin ($mminangle + $sminangle)
			+ 0.048889 * sin ($mminangle - $sminangle)
			- 0.129722 * sin (2 * $msdangle)
			- 0.011111 * sin (2 * $msdangle - $mminangle)
			- 0.012778 * sin (2 * $msdangle + $mminangle);             // 황경차
		$mreal = $this->degreelow ($ml + $md);                         // 진황경
		$re = $this->degreelow ($mreal - $sreal);

		return $re;
	}
	// }}}

	// {{{ +-- protected (array) getlunarfirst ($syear, $smonth, $sday)
	/**
	 * 그레고리력 년월일이 들어있는 태음월의 시작합삭일지, 망일시,
	 * 끝합삭일시를 구함
	 *
	 * @access protected
	 * @return array
	 *
	 *   <pre>
	 *   Array
	 *   (
	 *       [0] => 2013    // 시작 합삭 년도
	 *       [1] => 7       // 시작 합삭 월
	 *       [2] => 8       // 시작 합삭 일
	 *       [3] => 16      // 시작 합삭 시
	 *       [4] => 15      // 시작 합삭 분
	 *       [5] => 2013    // 망 연도
	 *       [6] => 7       // 망 월
	 *       [7] => 23      // 망 일
	 *       [8] => 2       // 망 시
	 *       [9] => 59      // 망 분
	 *       [10] => 2013   // 끝 합삭 년도
	 *       [11] => 8      // 끝 합삭 월
	 *       [12] => 7      // 끝 합삭 일
	 *       [13] => 6      // 끝 합삭 시
	 *       [14] => 50     // 끝 합삭 분
	 *   )
	 *   </pre>
	 *
	 * @param int 년
	 * @param int 월
	 * @param int 일
	 */
	protected function getlunarfirst ($syear, $smonth, $sday) {
		$dm  = $this->disp2days ($syear, $smonth, $sday, 1995, 12, 31);
		$dem = $this->moonsundegree ($dm);

		$d  = $dm;
		$de = $dem;

		while ( $de > 13.5 ) {
			$d--;
			$de = $this->moonsundegree ($d);
		}

		while ( $de > 1 ) {
			$d -= 0.04166666666;
			$de = $this->moonsundegree ($d);
		}

		while ( $de < 359.99 ) {
			$d -= 0.000694444;
			$de = $this->moonsundegree ($d);
		}

		$d += 0.375;
		$d *= 1440;
		$i= (int) $d * -1;
		list ($year, $month, $day, $hour, $min)= $this->getdatebymin ($i, 1995, 12, 31, 0, 0);

		$d  = $dm;
		$de = $dem;

		while ( $de < 346.5 ) {
			$d++;
			$de = $this->moonsundegree ($d);
		}

		while ( $de < 359 ) {
			$d += 0.04166666666;
			$de = $this->moonsundegree ($d);
		}

		while ( $de > 0.01 ) {
			$d += 0.000694444;
			$de = $this->moonsundegree ($d);
		}

		$pd = $d;
		$d  += 0.375;
		$d  *= 1440;
		$i  = (int) $d * -1;
		list ($year2, $month2, $day2, $hour2, $min2) = $this->getdatebymin ($i, 1995, 12, 31, 0, 0);

		if ( $smonth == $month2 && $sday == $day2 ) {
			$year  = $year2;
			$month = $month2;
			$day   = $day2;
			$hour  = $hour2;
			$min   = $min2;

			# {{{ for 0.93 diff
			$d = $pd + 26;

			$de = $this->moonsundegree ($d);
			while ( $de < 346.5 ) {
				$d++;
				$de = $this->moonsundegree ($d);
			}
			# 위의 라인 부분이 pascal(0.93) 버전에서는 아래와 같이 처리 되어 있음.
			# 함수 반환 값의 차이가 없는 것으로 보아 성능 개선 코드일까?
			#$d = $pd;
			#while ( $de < 347 ) {
			#	$d++;
			#	$de = $this->moonsundegree ($d);
			#}
			# 0.93 diff }}}

			while ( $de < 359 ) {
				$d += 0.04166666666;
				$de = $this->moonsundegree ($d);
			}

			while ( $de > 0.01 ) {
				$d += 0.000694444;
				$de = $this->moonsundegree ($d);
			}

			$d += 0.375;
			$d *= 1440;
			$i = (int) $d * -1;
			list ($year2, $month2, $day2, $hour2, $min2) = $this->getdatebymin ($i, 1995, 12, 31, 0, 0);
		}

		# 음력 초하루
		$d = $this->disp2days ($year, $month, $day, 1995, 12, 31);
		# 음력 12월
		$d += 12;

		$de = $this->moonsundegree ($d);
		while ( $de < 166.5 ) {
			$d++;
			$de = $this->moonsundegree ($d);
		}
		while ( $de < 179 ) {
			$d += 0.04166666666;
			$de = $this->moonsundegree ($d);
		}
		while ( $de < 179.999 ) {
			$d += 0.000694444;
			$de = $this->moonsundegree ($d);
		}

		$d += 0.375;
		$d *= 1440;
		$i = (int) $d * -1;
		list ($year1, $month1, $day1, $hour1, $min1) = $this->getdatebymin ($i, 1995, 12, 31, 0, 0);

		return array (
			$year, $month, $day, $hour, $min,
			$year1, $month1, $day1, $hour1, $min1,
			$year2, $month2, $day2, $hour2, $min2
		);
	}
	// }}}

	// {{{ +-- protected (array) solartolunar ($solyear,$solmon,$solday)
	/**
	 * 양력 날자를 음력 날자로 변환
	 *
	 * @access protected
	 * @return array
	 *
	 *   <pre>
	 *   Array
	 *   (
	 *       [0] => 2013   // 음력 연도
	 *       [1] => 6      // 음력 월
	 *       [2] => 9      // 음력 일
	 *       [3] =>        // 음력 윤달 여부 (boolean)
	 *       [4] => 1      // 평달(false)/큰달(true) 여부 (boolean)
	 *   )
	 *   </pre>
	 *
	 * @param int 년
	 * @param int 월
	 * @param int 일
	 */
	protected function solartolunar ($solyear, $solmon, $solday) {
		list ($smoyear, $smomonth, $smoday, $smohour, $smomin,
			$y0, $mo0, $d0, $h0, $mi0, $y1, $mo1, $d1, $h1, $mi1)
			= $this->getlunarfirst ($solyear, $solmon, $solday);

		$lday = $this->disp2days ($solyear, $solmon, $solday, $smoyear, $smomonth, $smoday) + 1;

		$i = abs ($this->disp2days ($smoyear, $smomonth, $smoday, $y1, $mo1, $d1));
		if ( $i == 30 )
			$largemonth = 1; # 대월
		if ( $i == 29 )
			$largemonth = 0; # 소월

		list ($inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			$midname1, $midyear1, $midmonth1, $midday1, $midhour1, $midmin1,
			$outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin)
			= $this->solortoso24 ($smoyear, $smomonth, $smoday, $smohour, $smomin);

		$midname2 = $midname1 + 2;
		if ( $midname2 > 24 )
			$midname2 = 1;
		$s0 = $this->month[$midname2] - $this->month[$midname1];
		if ( $s0 < 0 )
			$s0 += 525949;
		$s0 *= -1;

		list ($midyear2, $midmonth2, $midday2, $midhour2, $midmin2)
			= $this->getdatebymin ($s0, $midyear1, $midmonth1, $midday1, $midhour1, $midmin1);

		if ( ($midmonth1 == $smomonth && $midday1 >= $smoday) || ($midmonth1 == $mo1 && $midday1 < $d1) ) {
			$lmonth  = ($midname1 - 1) / 2 + 1;
			$leap = 0;
		} else {
			if ( ($midmonth2 == $mo1 && $midday2<$d1) || ($midmonth2 == $smomonth && $midday2 >= $smoday) ) {
				$lmonth   = ($midname2 - 1) / 2 + 1;
				$leap = 0;
			} else {
				if (  $smomonth < $midmonth2 && $midmonth2 < $mo1 ) {
					$lmonth   = ($midname2 - 1) / 2 + 1;
					$leap = 0;
				} else {
					$lmonth   = ($midname1 - 1) / 2 + 1;
					$leap = 1;
				}
			}
		}


		$lyear = $smoyear;
		if ( $lmonth == 12 && $smomonth == 1 )
			$lyear--;

		if ( ($lmonth == 11 && $leap == 1) || $lmonth == 12 || $lmonth < 6 ) {
			list ($midyear1, $midmonth1, $midday1, $midhour1, $midmin1)
				= $this->getdatebymin (2880, $smoyear, $smomonth, $smoday, $smohour, $smomin);

			list ($outgiyear, $outgimonth, $outgiday, $lnp, $lnp2)
				= $this->solartolunar ($midyear1, $midmonth1, $midday1);

			$outgiday = $lmonth - 1;
			if ( $outgiday== 0 )
				$outgiday = 12;

			if ( $outgiday == $outgimonth ) {
				if ( $leap == 1 )
					$leap=0;
			} else {
				if ( $leap == 1 ) {
					if ( $lmonth != $outgimonth ) {
						$lmonth--;
						if ( $lmonth == 0 ) { 
							$lyear--;
							$lmonth = 12;
						}
						$leap = 0;
					}
				} else {
					if ( $lmonth == $outgimonth ) {
						$leap = 1;
					} else {  
						$lmonth--;
						if ( $lmonth == 0 ) {
							$lyear--;
							$lmonth = 12;
						}
					}
				}
			}

		}

		return array ($lyear, $lmonth, $lday, $leap ? true : false, $largemonth ? true : false);
	}
	// }}}

	// {{{ +-- protected (array) lunartosolar ($lyear, $lmonth, $lday, $leap)
	/**
	 * 음력 날자를 양력 날자로 변환
	 *
	 * @access protected
	 * @return array
	 *
	 *   <pre>
	 *   Array
	 *   (
	 *       [0] => 2013   // 양력 연도
	 *       [1] => 6      // 양력 월
	 *       [2] => 9      // 양력 일
	 *   )
	 *   </pre>
	 *
	 * @param int  년
	 * @param int  월
	 * @param int  일
	 * @param bool 음력 윤달 여부
	 */
	protected function lunartosolar ($lyear, $lmonth, $lday, $leap = false) {
		list ($inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			$midname, $midyear, $midmonth, $midday, $midhour, $midmin,
			$outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin)
			= $this->solortoso24 ($lyear,2,15,0,0);

		$midname = $lmonth * 2 - 1 ;
		$tmin = $this->month[$midname] * -1;
		list ($midyear, $midmonth, $midday, $midhour, $midmin) 
			= $this->getdatebymin ($tmin, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin);

		list ( $outgiyear, $outgimonth, $outgiday, $hour, $min,
			$yearm, $monthm1, $daym, $hourm, $minm,
			$year1, $month1, $day1, $hour1, $min1 )
			= $this->getlunarfirst ($midyear, $midmonth, $midday);

		list ($lyear2, $lmonth2, $lday2, $lnp, $lnp2) 
			= $this->solartolunar ($outgiyear, $outgimonth, $outgiday);

		if ( $lyear2 == $lyear && $lmonth == $lmonth2 ) {
			// 평달, 윤달
			$tmin = -1440 * $lday + 10;
			list ($syear, $smonth, $sday, $hour, $min)
				= $this->getdatebymin ($tmin, $outgiyear, $outgimonth, $outgiday, 0, 0);


			if ( $leap ) {
				list ($lyear2, $lmonth2, $lday2, $lnp, $lnp2)
					= $this->solartolunar ($year1, $month1, $day1);
				if ( $lyear2==$lyear && $lmonth==$lmonth2 ) {
					$tmin = -1440 * $lday + 10;
					list ($syear, $smonth, $sday, $hour, $min)
						= $this->getdatebymin ($tmin, $year1, $month1, $day1, 0, 0);
				}
			}
		} else {
			// 중기가 두번든 달의 전후
			list ($lyear2, $lmonth2, $lday2, $lnp, $lnp2)
				= $this->solartolunar ($year1, $month1, $day1);
			if ( $lyear2 == $lyear && $lmonth == $lmonth2 ) {
				$tmin = -1440 * $lday + 10;
				list ($syear, $smonth, $sday, $hour, $min)
					= $this->getdatebymin ($tmin, $year1, $month1, $day1, 0, 0);
			}
		}

		return array ($syear, $smonth, $sday);
	}
	// }}}

	// {{{ +-- protected (int) getweekday ($syear, $smonth, $sday)
	/**
	 * 그레고리력 날자를 요일의 배열 번호로 변환
	 *
	 * @access protected
	 * @return int
	 * @param int 년
	 * @param int 월
	 * @param int 일
	 */
	protected function getweekday ($syear, $smonth, $sday) {
		$d = $this->disp2days (
			$syear, $smonth, $sday,
			$this->unityear, $this->unitmonth, $this->unitday
		);

		$i = $this->div ($d, 7);
		$d -= $i * 7;

		while ( $d > 6 || $d < 0 ) {
			if ($d > 6)
				$d -= 7;
			else
				$d += 7;
		}

		if ( $d < 0 )
			$d += 7;

		return $d;
	}
	// }}}

	// {{{ +-- protected (int) get28sday ($syear, $smonth, $sday)
	/**
	 * 그레고리력의 날자에 대한 28수를 구함
	 *
	 * @access protected
	 * @return int
	 * @param int 년
	 * @param int 월
	 * @param int 일
	 */
	protected function get28sday ($syear, $smonth, $sday) {
		$d = $this->disp2days (
			$syear, $smonth, $sday,
			$this->unityear, $this->unitmonth, $this->unitday
		);

		$i = $this->div ($d, 28);
		$d -= $i * 28;

		while ( $d > 27 || $d < 0 ) {
			if ($d > 27)
				$d -= 28;
			else
				$d += 28;
		}

		# {{{ for 0.93 diff
		# parscal(0.93) 버전에서는 이 2라인이 제거 되어 있음.
		# 실행의 값은 차이가 없는데, 성능 개선인가?
		if ( $d < 0 )
			$d += 7;
		# 0.93 diff }}}

		$d -= 11;

		if ( $d < 0 )
			$d += 28;

		return $d;
	}
	// }}}
	
	
	    // === [추가] Node/CLI용 public 래퍼 ===
    // 내부 protected solartolunar() 를 외부에서 호출할 수 있도록 감싼 메서드
    public function solarToLunarPublic($y, $m, $d) {
        // 내부 원형은 소문자: solartolunar
        $res = $this->solartolunar((int)$y, (int)$m, (int)$d);  // [ly, lm, ld, leap(bool), large(bool)]
        return [
            'ly'    => (int)$res[0],
            'lm'    => (int)$res[1],
            'ld'    => (int)$res[2],
            'leap'  => (bool)$res[3],
            'large' => (bool)$res[4],
        ];
    }

    // 내부의 음력→양력 메서드 이름이 보통 'lunartosolar' (전부 소문자)일 가능성이 큼
    // 파일 안에서 정확한 이름을 한번 Ctrl+F로 확인해줘: function lunartosolar(
    public function lunarToSolarPublic($ly, $lm, $ld, $leap = false) {
        // 내부 원형이 'lunartosolar' 라면 그대로 호출
        $res = $this->lunartosolar((int)$ly, (int)$lm, (int)$ld, (bool)$leap); // [y, m, d] 형태가 보통임
        return [
            'y' => (int)$res[0],
            'm' => (int)$res[1],
            'd' => (int)$res[2],
        ];
    }

	public function getSolarTermPublic($y, $m, $d) {
		list($inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			 $midname, $midyear, $midmonth, $midday, $midhour, $midmin,
			 $outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin)
			= $this->solortoso24($y, $m, $d, 12, 0); // 정오 기준

		return [
			'termIndex'   => $midname,                        // 절기 번호 (1~24)
			'termName'    => $this->month_st[$midname] ?? null,   // 절기 이름 (한글)
			'termNameHan' => $this->hmonth_st[$midname] ?? null,  // 절기 이름 (한자)
			'date'        => sprintf("%04d-%02d-%02d %02d:%02d", $midyear, $midmonth, $midday, $midhour, $midmin)
		];
	}
	
	/**
	 * Gregorian → Julian Day Number (시간 반영)
	 */
	private function toJulianDay(int $y, int $m, int $d, int $hh = 0, int $ii = 0): float {
		if ($m <= 2) {
			$y -= 1;
			$m += 12;
		}
		$A = intdiv($y, 100);
		$B = 2 - $A + intdiv($A, 4);

		// 시각을 JD에 포함 (일단위 소수 부분)
		$dayFraction = ($hh + $ii / 60.0) / 24.0;

		return floor(365.25 * ($y + 4716))
			 + floor(30.6001 * ($m + 1))
			 + $d + $dayFraction + $B - 1524.5;
	}

	/**
	 * JD → 간지 (일주 계산)
	 * 기준일: 1984-02-02 (甲子日), JD = 2445728.5
	 */
	private function dayGanJiFromGregorian(int $y, int $m, int $d, int $hh = 0, int $ii = 0): array {
		$jd = $this->toJulianDay($y, $m, $d, $hh, $ii);

		// 기준일 JD = 2445728.5 (정오 기준)
		$baseJD = 2445728.5;

		// offset은 정수일수 차이
		$offset = intval(floor($jd - $baseJD + 0.5));
		$offset = $offset % 60;
		if ($offset < 0) $offset += 60;

		$gan = $this->gan[$offset % 10];
		$ji  = $this->ji[$offset % 12];

		return ["gan" => $gan, "ji" => $ji];
	}



	/**
	 * 시주 계산 (일간 + 시간 공식 기반)
	 */
	private function hourGanJiFromTime(string $dayGan, int $hh): array {
		// 2시간 단위 시지 index (자시 = 0)
		$hourIndex = intval(($hh + 1) / 2) % 12;
		$hourJi    = $this->ji[$hourIndex];

		// 일간 → 기준 offset
		$dayGanIndex = array_search($dayGan, $this->gan, true);
		$hourGanTable = [
			0 => 0, 1 => 2, 2 => 4, 3 => 6, 4 => 8,
			5 => 0, 6 => 2, 7 => 4, 8 => 6, 9 => 8
		];
		$base = $hourGanTable[$dayGanIndex];
		$hourGan = $this->gan[($base + $hourIndex) % 10];

		return ["gan" => $hourGan, "ji" => $hourJi];
	}


	
	
	/**
     * Sexagenary pillars (Year/Month/Day/Hour) 정확 버전
     */
    public function getSexagenaryPublic(int $y, int $m, int $d, int $hh = 0, int $ii = 0): array {
		try {
			date_default_timezone_set("Asia/Seoul");
			$dt = new \DateTime(sprintf("%04d-%02d-%02d %02d:%02d", $y, $m, $d, $hh, $ii));

			// 년주, 월주 (OOPS API 기반)
			$yearData    = $this->solarToLunar($y, $m, $d);
			$yearGanZhi  = $this->getYearGanZhi($yearData);
			$monthGanZhi = $this->getMonthGanZhi($y, $m, $d);

			// 일주
			$dayGanJi = $this->dayGanJiFromGregorian($y, $m, $d, $hh, $ii);
			//error_log("DEBUG dayGanJi: gan=" . $dayGanJi["gan"] . " ji=" . $dayGanJi["ji"]);
			echo "[DEBUG] dayGanJi: " . print_r($dayGanJi, true) . PHP_EOL;

			// 시주 계산 (별도 함수 활용)
			$hourGanJi = $this->hourGanJiFromTime($dayGanJi["gan"], $hh);
			//error_log("DEBUG hourGanJi: gan=" . $hourGanJi["gan"] . " ji=" . $hourGanJi["ji"]);
			echo "[DEBUG] hourGanJi: " . print_r($hourGanJi, true) . PHP_EOL;

			return [
				"error"    => "",
				"input"    => $dt->format("Y-m-d H:i"),
				"yearGan"  => $yearGanZhi["gan"],
				"yearJi"   => $yearGanZhi["ji"],
				"monthGan" => $monthGanZhi["gan"],
				"monthJi"  => $monthGanZhi["ji"],
				"dayGan"   => $dayGanJi["gan"],
				"dayJi"    => $dayGanJi["ji"],
				"hourGan"  => $hourGanJi["gan"],
				"hourJi"   => $hourGanJi["ji"]
			];
		} catch (\Throwable $e) {
			return ["error" => $e->getMessage()];
		}
	}





	// 연주 계산 (입춘 기준)
	private function getYearGanZhi(array $lunarData): array {
		// 음력 변환 결과 안에 양력 날짜도 포함되어 있어야 함
		$y = (int)$lunarData['sy'];
		$m = (int)$lunarData['sm'];
		$d = (int)$lunarData['sd'];

		// 입춘 시점 확인
		$term = $this->getSolarTerm($y, 2); // 2월 초의 입춘
		$input = strtotime(sprintf("%04d-%02d-%02d", $y, $m, $d));
		$lichun = strtotime($term['date']);

		// 입춘 전이면 전년도
		if ($input < $lichun) {
			$y -= 1;
		}

		$gan = $this->gan[($y - 4) % 10];  // 甲子년 4AD 기준
		$ji  = $this->ji[($y - 4) % 12];
		return ["gan" => $gan, "ji" => $ji];
	}

	// 월주 계산 (절입 기준)
	private function getMonthGanZhi(int $y, int $m, int $d): array {
		$input = strtotime(sprintf("%04d-%02d-%02d", $y, $m, $d));
		$term = $this->getSolarTermPublic($y, $m, $d);
		$monthIndex = (($term['termIndex'] - 3) + 12) % 12; 
		$ji = $this->ji[$monthIndex];

		// 연간 → 인월 간 매칭표
		$yearGan = $this->getYearGanZhi($this->solarToLunar($y, $m, $d))['gan'];
		$monthGanStartMap = [
			"甲" => "丙", "己" => "丙",
			"乙" => "戊", "庚" => "戊",
			"丙" => "庚", "辛" => "庚",
			"丁" => "壬", "壬" => "壬",
			"戊" => "甲", "癸" => "甲",
		];
		$startGan = $monthGanStartMap[$yearGan];

		// 간 배열에서 시작점 찾기
		$startIndex = array_search($startGan, $this->gan, true);
		$gan = $this->gan[($startIndex + $monthIndex) % 10];

		return ["gan" => $gan, "ji" => $ji];
	}




	
	// (A) 하루 치 term 정보 가져오기 - 존재하는 getSolarTermPublic을 감싸는 프록시
	private function _termOf(int $y, int $m, int $d): ?array {
		if (!method_exists($this, 'getSolarTermPublic')) return null;
		// 기대 리턴: ['termIndex'=>int, 'termName'=>string, 'termNameHan'=>string, 'date'=>'YYYY-MM-DD HH:MM']
		$t = $this->getSolarTermPublic($y, $m, $d);
		if (!is_array($t) || empty($t['termName']) || empty($t['date'])) return null;
		return $t;
	}

	// (B) 해당 "월"의 첫 번째 節(절입)을 찾아서 돌려준다.
	// 찾으면 ['name'=>..., 'date'=>'YYYY-MM-DD HH:MM'] 형태 반환. 없으면 null.
	private function _firstJieOfMonth(int $y, int $m): ?array {
		// 1~15일 사이를 훑어서, "해당 달에 속하는" '절(節)' 날짜를 최초 1개 잡는다.
		$firstJie = null;

		// 달의 말일 계산
		$dim = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));

		// 1~min(15,말일) 범위에서 하루씩 체크
		for ($d = 1; $d <= min(15, $dim); $d++) {
			$t = $this->_termOf($y, $m, $d);
			if (!$t) continue;

			// 이 날에 '새로운' term이 시작했는지(전날과 이름이 달라졌는지) 확인
			$prevTs = strtotime(sprintf('%04d-%02d-%02d', $y, $m, $d)) - 86400;
			$py = (int)date('Y', $prevTs);
			$pm = (int)date('m', $prevTs);
			$pd = (int)date('d', $prevTs);

			$tPrev = $this->_termOf($py, $pm, $pd);

			$isBoundary = (!$tPrev) || ($tPrev['termName'] !== $t['termName']);

			// 경계일이고, 그 term이 '절(節)'에 속한다면 채택
			if ($isBoundary && in_array($t['termName'], $this->JIE_NAMES, true)) {
				$when = substr($t['date'], 0, 16); // 'YYYY-MM-DD HH:MM'
				$firstJie = ['name' => $t['termName'], 'date' => $when];
				break;
			}
		}

		// 혹시 1~15일 안에서 못잡았으면 16~말일까지 재탐색(예외적 해 보완)
		if (!$firstJie) {
			for ($d = 16; $d <= $dim; $d++) {
				$t = $this->_termOf($y, $m, $d);
				if (!$t) continue;
				$prevTs = strtotime(sprintf('%04d-%02d-%02d', $y, $m, $d)) - 86400;
				$py = (int)date('Y', $prevTs);
				$pm = (int)date('m', $prevTs);
				$pd = (int)date('d', $prevTs);

				$tPrev = $this->_termOf($py, $pm, $pd);
				$isBoundary = (!$tPrev) || ($tPrev['termName'] !== $t['termName']);
				if ($isBoundary && in_array($t['termName'], $this->JIE_NAMES, true)) {
					$when = substr($t['date'], 0, 16);
					$firstJie = ['name' => $t['termName'], 'date' => $when];
					break;
				}
			}
		}

		return $firstJie;
	}

	
	// (C) 입춘 기준 연주
	private function _yearGanZhiByLichun(int $y, int $m, int $d): array {
		// 2월의 '입춘'을 잡는다 (해당 연도 2월의 첫 節)
		$lichun = $this->_firstJieOfMonth($y, 2); // ['name'=>'立春', 'date'=>'YYYY-MM-DD HH:MM'] 예상
		$tsInput = strtotime(sprintf('%04d-%02d-%02d', $y, $m, $d));
		$tsLichun = $lichun ? strtotime($lichun['date']) : strtotime(sprintf('%04d-02-04 00:00', $y)); // 아주 예외시 2/4 00:00 가정

		$y4g = $y;
		if ($tsInput < $tsLichun) $y4g = $y - 1; // 입춘 전이면 전년도

		// 간지 계산 (甲子 4AD 기준 관례)
		$gan = $this->gan[(($y4g - 4) % 10 + 10) % 10];
		$ji  = $this->ji[(($y4g - 4) % 12 + 12) % 12];
		return ['gan'=>$gan, 'ji'=>$ji, 'y'=>$y4g];
	}

	// (D) 절입 기준 월주
	private function _monthGanZhiByJie(int $y, int $m, int $d, string $yearGan): array {
		// 그 달을 여는 절(節) 찾기
		$firstJie = $this->_firstJieOfMonth($y, $m);
		if (!$firstJie) {
			// 아주 예외: 못 찾으면 "월1일 00:00"을 절입으로 가정(임시)
			$firstJie = ['name' => null, 'date' => sprintf('%04d-%02d-01 00:00', $y, $m)];
		}

		// 월지: 절 이름 기준 매핑
		$monthBranch = null;
		if ($firstJie['name'] && isset($this->JIE_TO_BRANCH[$firstJie['name']])) {
			$monthBranch = $this->JIE_TO_BRANCH[$firstJie['name']];
		} else {
			// 절 이름을 못 얻었으면 월 번호로 추정 매핑 (寅=2월, …)
			$BR = ['丑','寅','卯','辰','巳','午','未','申','酉','戌','亥','子']; // 1~12월 임시 추정
			$monthBranch = $BR[($m-1) % 12];
		}

		// 월간: "연간 Index*2 + 월지 Index" 규칙
		$yearGanIdx = array_search($yearGan, $this->gan, true); // 0..9
		$monthBranchIdx = array_search($monthBranch, $this->ji, true); // 0..11
		$gan = $this->gan[($yearGanIdx * 2 + $monthBranchIdx) % 10];

		return ['gan'=>$gan, 'ji'=>$monthBranch];
	}

	// Lunar_API.php 안에 추가
	public function getMonthGanZhiPublic(int $y, int $m, int $d): array {
		$result = $this->getMonthGanZhi($y, $m, $d);
		// 내부 절입일 디버그 출력
		echo "[DEBUG] Input={$y}-{$m}-{$d}, Gan={$result['gan']}, Ji={$result['ji']}" . PHP_EOL;
		return $result;
	}
	
	// Lunar_API.php 안에 추가
	public function getSolarTerm(int $y, int $m): ?array {
		if (!method_exists($this, "getSolarTermPublic")) {
			return null;
		}
		// month 첫날 기준으로 절기 호출
		$out = $this->getSolarTermPublic($y, $m, 1);
		return $out;
	}
	
} // end of Lunar_API

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim: set filetype=php noet sw=4 ts=4 fdm=marker:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
?>
