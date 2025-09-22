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

app.listen(3000, () => {
    console.log("달력 서버: http://localhost:3000");
});
