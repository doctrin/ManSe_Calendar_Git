// server.js
const express = require("express");
const path = require("path");
const { buildMonthCalendar } = require("./server/calendrical");

const app = express();
app.use("/public", express.static(path.join(__dirname, "public")));
app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

// 루트 → 이번 달로 리다이렉트
app.get("/", (req, res) => {
    const now = new Date();
    res.redirect(`/calendar/${now.getFullYear()}/${now.getMonth() + 1}`);
});

app.get("/calendar/:year/:month", (req, res) => {
    const year = parseInt(req.params.year, 10);
    const month = parseInt(req.params.month, 10);

    const { days, header } = buildMonthCalendar(year, month);

    // 첫날 요일(0=일…6=토)
    const firstDay = new Date(year, month - 1, 1).getDay();
    const prev = month === 1 ? { y: year - 1, m: 12 } : { y: year, m: month - 1 };
    const next = month === 12 ? { y: year + 1, m: 1 } : { y: year, m: month + 1 };

    res.render("calendar", {
        year,
        month,
        days,
        header,
        firstDay,
        prev,
        next
    });
});

// =================================================================
// [추가] 일진 달력 API (검증 완료된 PHP CLI 연동)
// =================================================================
// 우리가 완성한 cli.php가 있는 정확한 경로를 지정합니다.
const phpCliPath = path.join(__dirname, '..', 'Lunar-kasi', 'cli.php');

// GET /api/month-calendar/:year/:month 요청을 처리할 API 엔드포인트
app.get('/api/month-calendar/:year/:month', async (req, res) => {
    const year = parseInt(req.params.year, 10);
    const month = parseInt(req.params.month, 10);

    if (isNaN(year) || isNaN(month)) {
        return res.status(400).json({ error: '년도와 월은 숫자여야 합니다.' });
    }

    const daysInMonth = new Date(year, month, 0).getDate();
    const promises = [];

    for (let day = 1; day <= daysInMonth; day++) {
        const command = `php "${phpCliPath}" solarToLunar ${year} ${month} ${day}`;
        
        const promise = new Promise((resolve, reject) => {
            exec(command, (error, stdout, stderr) => {
                if (error) {
                    console.error(`PHP Error for ${year}-${month}-${day}:`, stderr);
                    return reject(new Error(`PHP 스크립트 실행 오류`));
                }
                try {
                    // PHP가 출력한 JSON 문자열을 실제 객체로 파싱합니다.
                    const dayData = JSON.parse(stdout);
                    // 프론트엔드에서 사용하기 편하도록 원래 양력 날짜 정보도 추가해줍니다.
                    dayData.solar = { year, month, day };
                    resolve(dayData);
                } catch (parseError) {
                    console.error(`JSON Parse Error for ${year}-${month}-${day}:`, stdout);
                    reject(new Error(`PHP 출력 JSON 파싱 오류`));
                }
            });
        });
        promises.push(promise);
    }

    try {
        const monthData = await Promise.all(promises);
        res.json(monthData);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.listen(3000, () => {
    console.log("달력 서버: http://localhost:3000");
});
