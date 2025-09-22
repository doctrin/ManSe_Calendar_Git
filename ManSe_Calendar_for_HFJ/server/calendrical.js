// server/calendrical.js - PHP 기반 달력 데이터 빌더 (월 단위 호출로 성능 개선)
const { execSync } = require("child_process");
const moment = require("moment-timezone");

// 10간/12지
const heavenlyStems = ["甲","乙","丙","丁","戊","己","庚","辛","壬","癸"];
const earthlyBranches = ["子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥"];

// 甲子일 기준
const baseDate = moment.tz("1900-01-31", "Asia/Seoul");

// server/calendrical.js
const { execFileSync } = require("child_process");
const path = require("path");
const phpPath = "php"; // 환경 PATH에 php.exe 가 있어야 함
const cliPath = path.join(__dirname, "..", "..", "Lunar-master", "cli.php");

console.log("CLI PATH =>", cliPath); // 확인용

const DAY_GZ_OFFSET = 40; // ← 이 줄 추가

// PHP 호출
function callPhp(action, ...args) {
    try {
        const out = execFileSync(phpPath, [cliPath, action, ...args], { encoding: "utf8" });
        return JSON.parse(out);
    } catch (err) {
        console.error("PHP call error:", err.message);
        return { error: err.message };
    }
}

exports.getSexagenary = (y, m, d, hh = 0, mi = 0) =>
    callPhp("sexagenary", y, m, d, hh, mi);

// (이제 일 단위 대신) 월 단위 묶음 호출
function getMonthPack(y, m) {
    return callPhp("monthPack", y, m);
}

// 일진(간지)
function dayGanzhi(year, month, day) {
    const targetDate = moment.tz(`${year}-${month}-${day}`, "YYYY-M-D", "Asia/Seoul");
    // 기준일부터 며칠 지났는가 (+ 보정)
    const diffDays = targetDate.diff(baseDate, "days") + DAY_GZ_OFFSET; // ← 보정 적용
    const stem = heavenlyStems[(diffDays % 10 + 10) % 10];
    const branch = earthlyBranches[(diffDays % 12 + 12) % 12];
    return stem + branch;
}


// 헤더용 년/월 간지 (간단/안정 규칙)
function to2(n){ return String(n).padStart(2,"0"); }

function yearGanzhiForHeader(year, month, day) {
    // 임시: 음력/절기 고려 X, 그냥 양력 연도 기준
    // (추후 입춘 기준 로직 추가 가능)
    const stem = heavenlyStems[(year - 4) % 10]; // 甲子=1984 기준
    const branch = earthlyBranches[(year - 4) % 12];
    return stem + branch;
}

function monthBranchIndexByDate(y, m, d) {
    const boundaries = [
        { y: y - 1, m: 12, d: 7, idx: 10, br: "子" },
        { y, m: 1, d: 6, idx: 11, br: "丑" },
        { y, m: 2, d: 4, idx: 0,  br: "寅" },
        { y, m: 3, d: 6, idx: 1,  br: "卯" },
        { y, m: 4, d: 5, idx: 2,  br: "辰" },
        { y, m: 5, d: 6, idx: 3,  br: "巳" },
        { y, m: 6, d: 6, idx: 4,  br: "午" },
        { y, m: 7, d: 7, idx: 5,  br: "未" },
        { y, m: 8, d: 8, idx: 6,  br: "申" },
        { y, m: 9, d: 8, idx: 7,  br: "酉" },
        { y, m: 10, d: 8, idx: 8, br: "戌" },
        { y, m: 11, d: 7, idx: 9, br: "亥" },
        { y, m: 12, d: 7, idx: 10, br: "子" },
    ];
    const target = new Date(y, m-1, d);
    let last = boundaries[0];
    for (const b of boundaries) {
        const cur = new Date(b.y, b.m-1, b.d);
        if (cur <= target) last = b;
    }
    return last;
}
function monthGanzhiForHeader(year, month) {
    // 단순히 연 + 월로 계산 (정확하려면 24절기 필요)
    const stemIndex = ((year * 12 + month) - 1) % 10;
    const branchIndex = (month + 1) % 12; // 1월=寅월
    return heavenlyStems[stemIndex] + earthlyBranches[branchIndex];
}

// 월 달력 구성 (월 패키지 사용: PHP 1회 호출)
function buildMonthCalendar(year, month) {
    const pack = getMonthPack(year, month);
    if (!pack) return { days: [], header: {yearGanzhi:"", monthGanzhi:""} };

    // 절기 맵: "YYYY-MM-DD" -> {termName, termNameHan,...}
    const termMap = {};
    (pack.terms || []).forEach(t => {
        const key = (t.date || "").split(" ")[0];
        termMap[key] = t;
    });

    const dim = new Date(year, month, 0).getDate();
    const days = [];
    for (let d=1; d<=dim; d++) {
        const lunar = pack.lunar?.[d] || {};
        const ganzhi = dayGanzhi(year, month, d);
        const key = `${year}-${to2(month)}-${to2(d)}`;
        const term = termMap[key] || null;

        days.push({
            solar: { y: year, m: month, d },
            lunar,
            ganzhi,
            term
        });
    }

    const header = {
        yearGanzhi: yearGanzhiForHeader(year, month, 1),
        monthGanzhi: monthGanzhiForHeader(year, month, 1)
    };
    return { days, header };
}

// server/calendrical.js 안에 추가
function daysInGregorianMonth(year, month) {
    return new Date(year, month, 0).getDate(); // JS 내장 Date 객체로 해당 월의 마지막 날 구하기
}

// Gregorian 날짜 → Julian Day Number
function jdFromYMD(year, month, day, hour = 12, minute = 0) {
    const a = Math.floor((14 - month) / 12);
    const y = year + 4800 - a;
    const m = month + 12 * a - 3;
    const jdn =
        day +
        Math.floor((153 * m + 2) / 5) +
        365 * y +
        Math.floor(y / 4) -
        Math.floor(y / 100) +
        Math.floor(y / 400) -
        32045;
    // 시간 보정 (기본 정오=12시)
    const dayFraction = (hour - 12) / 24 + minute / 1440;
    return jdn + dayFraction;
}

// JD → 요일 (0=일, 1=월, … 6=토)
function weekdayFromJD(jd) {
    return (Math.floor(jd + 1.5) % 7);
}

// ────────────────────────────────
// 시주(시간 간지)
function hourGanzhi(year, month, day, hour = 0, minute = 0) {
    const heavenlyStems = ["甲","乙","丙","丁","戊","己","庚","辛","壬","癸"];
    const earthlyBranches = ["子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥"];

    // 기준일 (1900-01-31 00:00) → 甲子일 甲子시
    const baseDate = new Date(1900, 0, 31, 0, 0, 0);
    const target = new Date(year, month - 1, day, hour, minute, 0);

    const diffHours = Math.floor((target - baseDate) / (1000 * 60 * 60));

    const stem = heavenlyStems[(diffHours % 10 + 10) % 10];
    const branch = earthlyBranches[(diffHours % 12 + 12) % 12];

    return stem + branch;
}

// 특정 날짜/시간의 간지(년/월/일/시) 가져오기
function getSexagenary(year, month, day, hour = 0, minute = 0) {
    return callPhp("sexagenary", year, month, day, hour, minute);
}


module.exports = {
    buildMonthCalendar,
    daysInGregorianMonth,
    jdFromYMD,
    weekdayFromJD,
    dayGanzhi,

    // alias 붙이기
    yearGanzhiTermBased: yearGanzhiForHeader,
    monthGanzhiTermBased: monthGanzhiForHeader,

    // 혹시 나중에 필요할 수 있어서 원래 이름도 같이 export
    yearGanzhiForHeader,
    monthGanzhiForHeader,
    hourGanzhi, // ← 추가!
    getSexagenary
};



