# KASI-Lunar

[![GitHub license](https://img.shields.io/badge/license-GPLv2-blue.svg)](http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)


Solar/Lunar convert API with KASI(한국 천문 과학 연구원) data

### Description

이 패키지는 ***한국천문연구원***의 음양력 데이터를 기반으로 하여 양력/음열간의 변환을
제공하며, [aero](http://aero.sarang.net/)님의 [Date-Korean-0.0.2](http://search.cpan.org/~aero/Date-Korean-0.0.2/)
perl module을 PHP로 포팅한 것 입니다.

양력 기준으로 1391-02-05 부터 2050-12-31 까지의 기간만 가능하며, 절기, 합삭/망
정보, 세차/월간/일진등의 정보는 [Lunar](https://github.com/OOPS-ORG-PHP/Lunar/) pear package를
이용하도록 합니다.

2.0.0 부터 2004년 부터 2026년까지의 천문 과학 연구원의 절기 데이터를 추가 제공 합니다.

이 패키지는 [Lunar](https://github.com/OOPS-ORG-PHP/Lunar/) pear package의 확장을 위하여 제작이
되었으며, 라이센스 문제로 [Lunar](https://github.com/OOPS-ORG-PHP/Lunar/) pear package와 별도의
패키지로 제작이 되었습니다. (물론 독립적으로도 사용을 할 수 있습니다.)

### License

GPLv2

### Installation

```bash
[root@host ~]$ pear channel-discover pear.oops.org
Adding Channel "pear.oops.org" succeeded
Discovery of channel "pear.oops.org" succeeded
[root@host ~]$ pear install oops/Lunar
```

### dependency
  * PHP >= 5.3.0
  * PHP extensions
    * calendar
  * Pear pakcages
    * [myException](https://github.com/OOPS-ORG-PHP/myException/) >= 1.0.1
  
### Sample codes
```php
<?php
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

echo "-----------------------------------------------------------------------\n";

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
	print_r ($lunar->season ('입춘', 2015));

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
```
