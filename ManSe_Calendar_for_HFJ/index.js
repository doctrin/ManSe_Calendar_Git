const sqlite3 = require('sqlite3').verbose();
const express = require('express');
const app = express();
const path = require('path');

app.use(express.static(path.join(__dirname)));

// ✅ 뷰 엔진 설정
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));


const cors = require('cors');
// const serverless = require('serverless-http');

// 십신 전환을 위한 json 파일
const sibsin = require('./sibsin.json');

//
const calendrical = require('./server/calendrical');


// 시주 추출위한 함수
const moment = require('moment-timezone');


// CORS 이슈 해결
// let corsOptions = {
//     origin: 'https://sajualla.pages.dev/',
//     credentials: true
// }
// app.use(cors(corsOptions));
app.use(cors());


// Get 요청 받음
app.get('/', function(req, res) {
    // res.send('Welcome Server World');
});


// POST 요청 받을 수 있게 만듬
app.use(express.json()); // for parsing application/json
app.use(express.urlencoded({ extended: true })); // for parsing application/x-www-form-urlencoded


// POST method route GetMySaju
// 내 사주에 대한 정보 가져오기
app.post('/GetMySaju', async function (req, res) {
    console.log(req.body);
    let {birthDate, lunar, leapMonth, birthTime} = req.body;


    // 태어난 시간이 2330 ~ 2359 경우, 子시 부터는 다음날로 계산
    let sajuBirthDate = birthDate;
    if (birthTime.length === 4) {
        if (birthTime >= '2330' && birthTime <= '2359') {
            const nextDate = moment(birthDate, 'YYYYMMDD').add(1, 'day');
            sajuBirthDate = nextDate.format('YYYYMMDD');
        }
    }

    const year = sajuBirthDate.slice(0, 4); // 첫 4글자(연도)를 추출
    const month = parseInt(sajuBirthDate.slice(4, 6), 10); // 다음 2글자(월)를 추출
    const day = parseInt(sajuBirthDate.slice(6, 8), 10); // 마지막 2글자(일)를 추출
    // console.log(year, month, day); // 출력: 1995 3 15

    let mySaju;

    try {
        mySaju = await getBirth(year, month, day, lunar, leapMonth);
    } catch (error) {
        console.log(error);
    }

    if (mySaju.length == 0) {
        let mySajuResult = JSON.stringify({
            "error": "유효하지 않은 생년월일 정보가 있습니다."
        });
        res.json({"mySajuResult": mySajuResult});
        return;
    }


    // DB에서 가져온 결과 값이 12절기에 해당되면
    // 월이 바뀌는 12절기 절입 시간 처리를 한번 더 해야함
    // 출생시간이 절입시간 이전이면 전일에 해당되는 년,월주를 세워야함
    // 출생시간 모름일 경우도 이전 년,월주 세우는 걸로 함
    let season12TimeCheck = false;
    const sBirthDate = mySaju[0].cd_sy +
        mySaju[0].cd_sm.padStart(2, '0') +
        mySaju[0].cd_sd.padStart(2, '0');

    if (mySaju[0].cd_terms_time !== null) {
        if (mySaju[0].cd_kterms === '입춘' || mySaju[0].cd_kterms === '경칩' || mySaju[0].cd_kterms === '청명' ||
            mySaju[0].cd_kterms === '입하' || mySaju[0].cd_kterms === '망종' || mySaju[0].cd_kterms === '소서' ||
            mySaju[0].cd_kterms === '입추' || mySaju[0].cd_kterms === '백로' || mySaju[0].cd_kterms === '한로' ||
            mySaju[0].cd_kterms === '입동' || mySaju[0].cd_kterms === '대설' || mySaju[0].cd_kterms === '소한') {

            if (birthTime.length === 4) {
                const sBirthDateTime = sBirthDate + birthTime;
                // 양력 생년월일시 12절기 절입시간 비교
                if (sBirthDateTime < mySaju[0].cd_terms_time) {
                    // 절입시간 이전이면 전날 년,월주를 구함
                    season12TimeCheck = true;
                }
            } else {
                // 출생시간 모름일 경우도 전날 년,월주를 구함
                season12TimeCheck = true;
            }
        }
    }

    // 12절기에 해당되면 생년월일을 전날로 계산해서 만세력 다시 구한다
    let mySajuEve;
    if (season12TimeCheck) {
        const sBirthDateMoment = moment(`${sBirthDate}`, 'YYYYMMDD');
        const sEveBirthDate = sBirthDateMoment.add(-1, 'day').format('YYYYMMDD');

        let year = sEveBirthDate.slice(0, 4);
        let month = parseInt(sEveBirthDate.slice(4, 6), 10);
        let day = parseInt(sEveBirthDate.slice(6, 8), 10);

        try {
            mySajuEve = await getBirth(year, month, day, false, false);
        } catch (error) {
            console.log(error);
        }
    }
    // 만나이 계산하기
    const age = getAge(birthDate);
    const ageStr = '만 ' + age + '세';
    // console.log(ageStr);

    // 양력 생일 문자열 변환
    let sBirthDayStr = '(양) ' +
        mySaju[0].cd_sy + '년 ' +
        mySaju[0].cd_sm + '월 ' +
        mySaju[0].cd_sd + '일';
    // console.log(sBirthDayStr);

    // 음력 생일 문자열 변환
    let lBirthDayStr = '(음) ' +
        mySaju[0].cd_ly + '년 ' +
        mySaju[0].cd_lm + '월 ' +
        mySaju[0].cd_ld + '일';
    if (leapMonth) {
        lBirthDayStr = lBirthDayStr + ' 윤달';
    }
    // console.log(lBirthDayStr);

    // 子시 경우 다음날로 계산을 했으니 원래 생일을 보여주기 위해 하루를 뺀다
    if (sajuBirthDate !== birthDate) {
        const sViewBirthDate = mySaju[0].cd_sy +
            mySaju[0].cd_sm.padStart(2, '0') +
            mySaju[0].cd_sd.padStart(2, '0');
        const sViewBirthDateMoment = moment(`${sViewBirthDate}`, 'YYYYMMDD');
        const sViewBirthDatePrev = sViewBirthDateMoment.add(-1, 'day').format('YYYYMMDD');
        sBirthDayStr = '(양) ' +
            sViewBirthDatePrev.slice(0, 4) + '년 ' +
            parseInt(sViewBirthDatePrev.slice(4, 6), 10) + '월 ' +
            parseInt(sViewBirthDatePrev.slice(6, 8), 10) + '일';

        const lViewBirthDate = mySaju[0].cd_ly +
            mySaju[0].cd_lm.padStart(2, '0') +
            mySaju[0].cd_ld.padStart(2, '0');
        const lViewBirthDateMoment = moment(`${lViewBirthDate}`, 'YYYYMMDD');
        const lViewBirthDatePrev = lViewBirthDateMoment.add(-1, 'day').format('YYYYMMDD');
        lBirthDayStr = '(음) ' +
            lViewBirthDatePrev.slice(0, 4) + '년 ' +
            parseInt(lViewBirthDatePrev.slice(4, 6), 10) + '월 ' +
            parseInt(lViewBirthDatePrev.slice(6, 8), 10) + '일';
        if (leapMonth) {
            lBirthDayStr = lBirthDayStr + ' 윤달';
        }
    }

    // 출생시간 문자열 변환
    let birthTimeStr = '(시간)모름';
    if (birthTime.length == 4) {
        birthTimeStr =
            birthTime.substring(0,2) + '시 ' +
            birthTime.substring(2,4) + '분(-30)';
    }
    // console.log(birthTimeStr);

    let hygan; // 년간
    let hyjee; // 년지
    if (season12TimeCheck) {
        let hyganjeeEve = mySajuEve[0].cd_hyganjee; // 년주
        hygan = hyganjeeEve.substring(0,1); // 년간
        hyjee = hyganjeeEve.substring(1,2); // 년지
    } else {
        let hyganjee = mySaju[0].cd_hyganjee; // 년주
        hygan = hyganjee.substring(0,1); // 년간
        hyjee = hyganjee.substring(1,2); // 년지
        // console.log(hyganjee + ' ' + hygan + ' ' + hyjee);
    }

    let hmgan; // 월간
    let hmjee; // 월지
    if (season12TimeCheck) {
        let hmganjeeEve = mySajuEve[0].cd_hmganjee; // 월주
        hmgan = hmganjeeEve.substring(0,1); // 월간
        hmjee = hmganjeeEve.substring(1,2); // 월지
    } else {
        let hmganjee = mySaju[0].cd_hmganjee; // 월주
        hmgan = hmganjee.substring(0,1); // 월간
        hmjee = hmganjee.substring(1,2); // 월지
        // console.log(hmganjee + ' ' + hmgan + ' ' + hmjee);
    }

    let hdganjee = mySaju[0].cd_hdganjee; // 일주
    let hdgan = hdganjee.substring(0,1); // 일간
    let hdjee = hdganjee.substring(1,2); // 일지
    // console.log(hdganjee + ' ' + hdgan + ' ' + hdjee);

    // 년간 십신 추출하기
    let sibsin_hygan = getSibsin(hdgan, hygan);
    // console.log("sibsin_hygan== > "+ sibsin_hygan);

    // 년지 십신 추출하기
    let sibsin_hyjee = getSibsin(hdgan, hyjee);
    // console.log("sibsin_hyjee== > "+ sibsin_hyjee);

    // 월간 십신 추출하기
    let sibsin_hmgan = getSibsin(hdgan, hmgan);
    // console.log("sibsin_hmgan== > "+ sibsin_hmgan);

    // 월지 십신 추출하기
    let sibsin_hmjee = getSibsin(hdgan, hmjee);
    // console.log("sibsin_hmjee== > "+ sibsin_hmjee);

    // 일지 십신 추출하기
    let sibsin_hdjee = getSibsin(hdgan, hdjee);
    // console.log("sibsin_hdjee== > "+ sibsin_hdjee);


    let hhgan = '';
    let hhjee = '';
    let sibsin_hhgan = '';
    let sibsin_hhjee = '';

    // 출생시간이 입력되었을때만 추출하기
    if (birthTime.length == 4) {
        // 시주 추출하기
        let hhganjee = getTime(hdgan, birthTime);
        // console.log("hhganjee== > "+ hhganjee);

        // 시간 추출하기
        hhgan = hhganjee.substring(0,1); // 시간

        // 시지 추출하기
        hhjee = hhganjee.substring(1,2); // 시지

        // 시간 십신 추출
        sibsin_hhgan = getSibsin(hdgan, hhgan);

        // 시지 십신 추출
        sibsin_hhjee = getSibsin(hdgan, hhjee);
    }

    let mySajuResult = JSON.stringify({
        "error": "",
        "birthDate": birthDate,       // ✅ 원본 생년월일
        "birthTime": birthTime,       // ✅ 원본 출생시간
        "lunar": lunar,               // ✅ 원본 음력 여부
        "leapMonth": leapMonth,       // ✅ 원본 윤달 여부
        "ageStr": ageStr, // 만 나이
        "sBirthDayStr": sBirthDayStr, // 양력 생일
        "lBirthDayStr": lBirthDayStr, // 음력 생일
        "birthTimeStr": birthTimeStr, // 출생 시간

        "hygan": hygan, // 년간
        "sibsin_hygan": sibsin_hygan, // 십신_년간
        "hyjee": hyjee, // 년지
        "sibsin_hyjee": sibsin_hyjee, // 십신_년지

        "hmgan": hmgan, // 월간
        "sibsin_hmgan": sibsin_hmgan, // 십신_월간
        "hmjee": hmjee, // 월지
        "sibsin_hmjee": sibsin_hmjee, // 십신_월지

        "hdgan": hdgan, // 일간
        "hdjee": hdjee, // 일지
        "sibsin_hdjee": sibsin_hdjee, // 십신_일지

        "hhgan" : hhgan, // 시간
        "sibsin_hhgan" : sibsin_hhgan, // 십신_시간
        "hhjee" : hhjee, // 시지
        "sibsin_hhjee" : sibsin_hhjee // 십신_시지
    });

    console.log(mySajuResult);

    res.json({"mySajuResult": mySajuResult});
});
// getBirth function
// 만세력 DB에서 해당 사주 정보를 읽어온다.
async function getBirth(year, month, day, lunar, leapMonth) {
    // open the database
    let db = new sqlite3.Database('./database/manseryuk_Sqlite.db');

    let sql = '';
    // 양력
    if (lunar === false) {
        sql = `SELECT * FROM calenda_data WHERE cd_sy='${year}' AND cd_sm='${month}' AND cd_sd='${day}'`;
        // 음력
    } else {
        sql = `SELECT * FROM calenda_data WHERE cd_ly='${year}' AND cd_lm='${month}' AND cd_ld='${day}' AND cd_leap_month=${leapMonth}`;
    }

    try {
        const rows = await new Promise((resolve, reject) => {
            db.all(sql, (err, rows) => {
                if (err) {
                    console.error(err.message);
                    reject(err);
                } else {
                    // console.log(rows);
                    resolve(rows);
                }
            });
        });

        // close the database connection
        db.close();
        return rows;

    } catch (err) {
        console.error(err.message);
        return null;
    }
}


// 일간별 글자에 해당되는 십신 찾기
function getSibsin(ilgan, gulja) {
    for (let i = 0; i < sibsin[ilgan].length; i++) {
        // console.log(sibsin[ilgan][i][gulja]);
        return sibsin[ilgan][i][gulja];
    }
}


// 일간별 시주 찾기
function getTime(ilgan, timeStr) {
    let formattedTime = timeStr.substr(0, 2) + ":" + timeStr.substr(2); // 문자열 조합
    let myTime = moment(formattedTime, 'HH:mm', 'Asia/Seoul');

    const jitime = [
        ['子', '자'], ['丑', '축'], ['寅', '인'], ['卯', '묘'],
        ['辰', '진'], ['巳', '사'], ['午', '오'], ['未', '미'],
        ['申', '신'], ['酉', '유'], ['戌', '술'], ['亥', '해']
    ];

    // 자, 축, 인, 묘, 진, 사, 오, 미, 신, 유, 술, 해
    const startTimes = [
        '23:29', '01:29', '03:29', '05:29',
        '07:29', '09:29', '11:29', '13:29',
        '15:29', '17:29', '19:29', '21:29'
    ];

    // 입력 시간이 어느 구간에 해당하는지 찾기
    let jeeIndex = 0;
    for (let i = 0; i < startTimes.length; i++) {
        const startTime = moment(startTimes[i], 'HH:mm', 'Asia/Seoul');
        const endTime = moment(startTimes[(i + 1) % startTimes.length], 'HH:mm', 'Asia/Seoul');

        // 현재 시간이 시작 시간과 끝 시간 사이에 있다면 해당하는 구간이라고 판단
        if (myTime.isBetween(startTime, endTime, null, '[]')) {
            jeeIndex = i;
            break;
        }
    }

    let sijee = jitime[jeeIndex][0];
    // console.log('sijee==>' + sijee);
    jeeIndex = jeeIndex % 10;
    // console.log('jeeIndex==>' + jeeIndex);

    const ganjeeArr = ["甲", "乙", "丙", "丁", "戊", "己", "庚", "辛", "壬", "癸"];

    let siganjee;

    switch(ilgan) {
        case "甲":
        case "己":
            siganjee = ganjeeArr[jeeIndex].concat(sijee);
            break;
        case "乙":
        case "庚":
            // 배열 순서 변경
            const shiftedGanjeeArr2 = [...ganjeeArr.slice(2), ...ganjeeArr.slice(0, 2)];
            siganjee = shiftedGanjeeArr2[jeeIndex].concat(sijee);
            break;
        case "丙":
        case "辛":
            // 배열 순서 변경
            const shiftedGanjeeArr4 = [...ganjeeArr.slice(4), ...ganjeeArr.slice(0, 4)];
            siganjee = shiftedGanjeeArr4[jeeIndex].concat(sijee);
            break;
        case "丁":
        case "壬":
            // 배열 순서 변경
            const shiftedGanjeeArr6 = [...ganjeeArr.slice(6), ...ganjeeArr.slice(0, 6)];
            siganjee = shiftedGanjeeArr6[jeeIndex].concat(sijee);
            break;
        case "戊":
        case "癸":
            // 배열 순서 변경
            const shiftedGanjeeArr8 = [...ganjeeArr.slice(8), ...ganjeeArr.slice(0, 8)];
            siganjee = shiftedGanjeeArr8[jeeIndex].concat(sijee);
            break;
        default:
            break;
    }

    // console.log(siganjee); // 시주
    return siganjee;
}


// 만나이 가져오기
function getAge(birth) {
    birthStr = birth.replace(/^(\d{4})(\d{2})(\d{2})$/, '$1-$2-$3');
    const birthDate = new Date(birthStr);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

// DB 연결 함수
function getDailyInfo(year, month, day) {
    return new Promise((resolve, reject) => {
        const db = new sqlite3.Database('./database/manseryuk_Sqlite.db');
        const sql = `SELECT cd_hyganjee, cd_hmganjee, cd_hdganjee,
                            cd_ly, cd_lm, cd_ld, cd_kterms, cd_hweek
                     FROM calenda_data
                     WHERE cd_sy=? AND cd_sm=? AND cd_sd=?`;
        db.get(sql, [year, month, day], (err, row) => {
            db.close();
            if (err) reject(err);
            else resolve(row);
        });
    });
}

// ✅ 일진달력 API (DB 사용 안 함, 순수 계산)
app.get('/calendar/:year/:month', (req, res) => {
    const year = parseInt(req.params.year);
    const month = parseInt(req.params.month);

    let result = [];
    const daysInMonth = new Date(year, month, 0).getDate();

    for (let d = 1; d <= daysInMonth; d++) {
        const dateObj = new Date(year, month - 1, d);

        /* AS-IS
        result.push({
            date: `${year}-${String(month).padStart(2, "0")}-${String(d).padStart(2, "0")}`,
            weekday: dateObj.getDay(),                          // 요일 (0=일, 6=토)
            weekdayStr: ['일','월','화','수','목','금','토'][dateObj.getDay()],
            dayGZ: calendrical.dayGanzhi(year, month, d),       // 일간지
            monthGZ: calendrical.monthGanzhiTermBased(year, month),      // 월간지 (추가 가능)
            yearGZ: calendrical.yearGanzhiTermBased(year),               // 년간지 (추가 가능)
            hourGZ: calendrical.hourGanzhi(year, month, d, 12, 0, true) // 정오 기준 시주
        });

        */

        //TO-BE
        const L = calendrical.solarToLunar(year, month, d) || {};
        result.push({
            date: `${year}-${String(month).padStart(2,"0")}-${String(d).padStart(2,"0")}`,
            weekday: dateObj.getDay(),
            weekdayStr: ['일','월','화','수','목','금','토'][dateObj.getDay()],
            dayGZ: calendrical.dayGanzhi(year, month, d),
            monthGZ: calendrical.monthGanzhiTermBased(year, month, d),
            yearGZ: calendrical.yearGanzhiTermBased(year, month, d),
            hourGZ: calendrical.hourGanzhi(year, month, d, 12), // 12시 정오 기준 시주
            // 음력: solarToLunar이 돌려주는 필드명(lm, ld, leap, large)을 그대로 보관
            lunar: {
                lm:   L.lm ?? null,
                ld:   L.ld ?? null,
                leap: !!L.leap,
                large: !!L.large
            }
        });

    }

    res.json(result);
});


// ✅ 일진달력 팝업 페이지 (calendar.ejs 활용)
app.get('/calendar-page/:year/:month', (req, res) => {
    const year = parseInt(req.params.year, 10);
    const month = parseInt(req.params.month, 10);

    const { days, header } = require('./server/calendrical').buildMonthCalendar(year, month);

    const firstDay = new Date(year, month - 1, 1).getDay();
    const prev = month === 1 ? { y: year - 1, m: 12 } : { y: year, m: month - 1 };
    const next = month === 12 ? { y: year + 1, m: 1 } : { y: year, m: month + 1 };

    res.render('calendar', {
        year,
        month,
        days,
        header,
        firstDay,
        prev,
        next
    });
});


// ✅ 현재 연월 기본 라우트
app.get('/calendar-page', (req, res) => {
    const now = new Date();
    res.redirect(`/calendar-page/${now.getFullYear()}/${now.getMonth() + 1}`);
});


// ✅ 계산 기반(달력 계산) 사주 API
app.post("/GetMySajuCalc", (req, res) => {
    //const { year, month, day, hour, minute } = req.body;

    console.log("-------------> 받은 값 : ", req.body);
    const birthDate = req.body.birthDate;
    const birthTime = req.body.birthTime;

    const year  = parseInt(birthDate.substr(0, 4));   // "1976" → 1976
    const month = parseInt(birthDate.substr(4, 2));   // "01"   → 1
    const day   = parseInt(birthDate.substr(6, 2));   // "16"   → 16

    const hour   = parseInt(birthTime.substr(0, 2));  // "18" → 18
    const minute = parseInt(birthTime.substr(2, 2));  // "30" → 30

    console.log("---> substr 결과 : "+year+'_'+month+'_'+day+'_'+hour+'_'+minute);

    const result = calendrical.getSexagenary(
        parseInt(year),
        parseInt(month),
        parseInt(day),
        parseInt(hour) || 0,
        parseInt(minute) || 0
    );

    res.json(result);
});




// module.exports.handler = serverless(app);
app.listen(3000, function () {
    console.log('Server is running on port 3000');
});