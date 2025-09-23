// sajuData를 사용하여 해당 페이지에 보여줍니다.
const sajuData = JSON.parse(localStorage.getItem('sajuData'));
const mySaju = JSON.parse(sajuData.mySajuResult);

// 사주 생년월일 정보
document.querySelector('#nameAge').textContent = sajuData.name + ' (' + mySaju.ageStr + ', ' + sajuData.sex + ')';
document.querySelector('#sBirthday').textContent = mySaju.sBirthDayStr
document.querySelector('#lBirthday').textContent = mySaju.lBirthDayStr
document.querySelector('#birthTime').textContent = mySaju.birthTimeStr


// 년간
const hyganTd = document.querySelector('#hygan');
const img1 = document.createElement('img');
let hyganImg = matchGanjee(mySaju.hygan);
img1.src = hyganImg;
img1.style.width = "100%";
hyganTd.appendChild(img1);

// 육신_년간
const sibsin_hyganTd = document.querySelector('#sibsin_hygan');
sibsin_hyganTd.textContent = mySaju.sibsin_hygan;

// 년지
const hyjeeTd = document.querySelector('#hyjee');
const img2 = document.createElement('img');
let hyjeeImg = matchGanjee(mySaju.hyjee);
img2.src = hyjeeImg;
img2.style.width = "100%";
hyjeeTd.appendChild(img2);

// 육신_년지
const sibsin_hyjeeTd = document.querySelector('#sibsin_hyjee');
sibsin_hyjeeTd.textContent = mySaju.sibsin_hyjee;



// 월간
const hmganTd = document.querySelector('#hmgan');
const img3 = document.createElement('img');
let hmganImg = matchGanjee(mySaju.hmgan);
img3.src = hmganImg;
img3.style.width = "100%";
hmganTd.appendChild(img3);

//육신_월간
const sibsin_hmganTd = document.querySelector('#sibsin_hmgan');
sibsin_hmganTd.textContent = mySaju.sibsin_hmgan;

// 월지
const hmjeeTd = document.querySelector('#hmjee');
const img4 = document.createElement('img');
let hmjeeImg = matchGanjee(mySaju.hmjee);
img4.src = hmjeeImg;
img4.style.width = "100%";
hmjeeTd.appendChild(img4);

//육신_월지
const sibsin_hmjeeTd = document.querySelector('#sibsin_hmjee');
sibsin_hmjeeTd.textContent = mySaju.sibsin_hmjee;

// 일간
const hdganTd = document.querySelector('#hdgan');
const img5 = document.createElement('img');
let hdganImg = matchGanjee(mySaju.hdgan);
img5.src = hdganImg;
img5.style.width = "100%";
hdganTd.appendChild(img5);


// 일지
const hdjeeTd = document.querySelector('#hdjee');
const img6 = document.createElement('img');
let hdjeeImg = matchGanjee(mySaju.hdjee);
img6.src = hdjeeImg;
img6.style.width = "100%";
hdjeeTd.appendChild(img6);

//육신_일지
const sibsin_hdjeeTd = document.querySelector('#sibsin_hdjee');
sibsin_hdjeeTd.textContent = mySaju.sibsin_hdjee;


// 시간
const hhganTd = document.querySelector('#hhgan');
const img7 = document.createElement('img');
let hhganImg = matchGanjee(mySaju.hhgan);
img7.src = hhganImg;
img7.style.width = "100%";
hhganTd.appendChild(img7);

// 육신_시간
const sibsin_hhganTd = document.querySelector('#sibsin_hhgan');
sibsin_hhganTd.textContent = mySaju.sibsin_hhgan;

// 시지
const hhjeeTd = document.querySelector('#hhjee');
const img8 = document.createElement('img');
let hhjeeImg = matchGanjee(mySaju.hhjee);
img8.src = hhjeeImg;
img8.style.width = "100%";
hhjeeTd.appendChild(img8);

// 육신_시지
const sibsin_hhjeeTd = document.querySelector('#sibsin_hhjee');
sibsin_hhjeeTd.textContent = mySaju.sibsin_hhjee;
// 간지에 맞는 그림 매핑
function matchGanjee(ganjee) {
    let imgName;
    switch (ganjee) {
        case '甲':
            imgName = './img/gab_mok.jpg';
            break;
        case '乙':
            imgName = './img/eul_mok.jpg';
            break;
        case '丙':
            imgName = './img/byeong_hwa.jpg';
            break;
        case '丁':
            imgName = './img/jeong_hwa.jpg';
            break;
        case '戊':
            imgName = './img/mu_to.jpg';
            break;
        case '己':
            imgName = './img/gi_to.jpg';
            break;
        case '庚':
            imgName = './img/gyeong_geum.jpg';
            break;
        case '辛':
            imgName = './img/sin_geum.jpg';
            break;
        case '壬':
            imgName = './img/im_su.jpg';
            break;
        case '癸':
            imgName = './img/gye_su.jpg';
            break;
        case '寅':
            imgName = './img/in_mok.jpg';
            break;
        case '卯':
            imgName = './img/myo_mok.jpg';
            break;
        case '辰':
            imgName = './img/jin_to.jpg';
            break;
        case '巳':
            imgName = './img/sa_hwa.jpg';
            break;
        case '午':
            imgName = './img/oh_hwa.jpg';
            break;
        case '未':
            imgName = './img/mi_to.jpg';
            break;
        case '申':
            imgName = './img/jee_sin_geum.jpg';
            break;
        case '酉':
            imgName = './img/yu_geum.jpg';
            break;
        case '戌':
            imgName = './img/sul_to.jpg';
            break;
        case '亥':
            imgName = './img/hae_su.jpg';
            break;
        case '子':
            imgName = './img/ja_su.jpg';
            break;
        case '丑':
            imgName = './img/chug_to.jpg';
            break;
        default:
            imgName = './img/question.jpg';
            break;
    }
    return imgName;
}
// =======================
// 사주풀이 첫번째
// 내 사주 월지 내용을 보여준다.
let tdWoljiSeason = document.getElementById("woljiSeason");
let woljiSeason = getWolji(mySaju.hmjee);
tdWoljiSeason.innerHTML = woljiSeason[0];

// 계절별 간략 게시글 블로그 링크 연결하기
function getSeasonOutline() {
    if (woljiSeason[1] === '봄') {
        window.open('https://m.blog.naver.com/weisoon/223097066285');
    } else if (woljiSeason[1] === '여름') {
        window.open('https://m.blog.naver.com/weisoon/223097612334');
    } else if (woljiSeason[1] === '가을') {
        window.open('https://m.blog.naver.com/weisoon/223108552777');
    } else if (woljiSeason[1] === '겨울') {
        window.open('https://m.blog.naver.com/weisoon/223108577105');
    }
}

// 계절별 요약 게시글 블로그 링크 연결하기
function getSeasonSummay() {
    if (woljiSeason[1] === '봄') {
        window.open('https://m.blog.naver.com/weisoon/223097065504');
    } else if (woljiSeason[1] === '여름') {
        window.open('https://m.blog.naver.com/weisoon/223098351915');
    } else if (woljiSeason[1] === '가을') {
        window.open('https://m.blog.naver.com/weisoon/223111432973');
    } else if (woljiSeason[1] === '겨울') {
        window.open('https://m.blog.naver.com/weisoon/223111869255');
    }
}

// 계절별 상세 게시글 블로그 링크 연결하기
function getSeasonDetail() {
    if (woljiSeason[1] === '봄') {
        window.open('https://m.blog.naver.com/weisoon/223097067499');
    } else if (woljiSeason[1] === '여름') {
        window.open('https://m.blog.naver.com/weisoon/223098706976');
    } else if (woljiSeason[1] === '가을') {
        window.open('https://m.blog.naver.com/weisoon/223111433811');
    } else if (woljiSeason[1] === '겨울') {
        window.open('https://m.blog.naver.com/weisoon/223111868549');
    }
}


// 월지에 맞는 내용 보여주기
function getWolji(hmjee) {
    let content;
    let content1 = '사주 해석 출발은<br><strong>월지</strong>부터 시작해요.';
    let content2 = '<br>당신의 월지는<br><strong>';
    let content3 = '';
    let content4 = '';
    let content5 = '</strong>에<br>태어난 사주이군요.';
    let content6 = '<br><em>해당 계절 특성에 대해서<br>한번 알아볼까요?</em>';

    switch (hmjee) {
        case '寅':
            content3 = '인(寅)목, ';
            content4 = '봄';
            break;
        case '卯':
            content3 = '묘(卯)목, ';
            content4 = '봄';
            break;
        case '辰':
            content3 = '진(辰)토, ';
            content4 = '봄';
            break;
        case '巳':
            content3 = '사(巳)화, ';
            content4 = '여름';
            break;
        case '午':
            content3 = '오(午)화, ';
            content4 = '여름';
            break;
        case '未':
            content3 = '미(未)토, ';
            content4 = '여름';
            break;
        case '申':
            content3 = '신(申)금, ';
            content4 = '가을';
            break;
        case '酉':
            content3 = '유(酉)금, ';
            content4 = '가을';
            break;
        case '戌':
            content3 = '술(戌)토, ';
            content4 = '가을';
            break;
        case '亥':
            content3 = '해(亥)수, ';
            content4 = '겨울';
            break;
        case '子':
            content3 = '자(子)수, ';
            content4 = '겨울';
            break;
        case '丑':
            content3 = '축(丑)토, ';
            content4 = '겨울';
            break;
    }

    content = content1 + content2 + content3 + content4 + content5 + content6;
    return [content, content4];
}

// 입력값 모으기 (필드 id는 프로젝트에 맞춰 필요시 수정)
function collectInputs() {
    // 예: YYYYMMDD / HHmm
    const birthDate = (document.getElementById('birthDate')?.value || '').trim();
    const birthTime = (document.getElementById('birthTime')?.value || '').trim();
    return { birthDate, birthTime };
}

// 계산기반 호출
async function getMySajuCalc(birthDate, birthTime) {
    const res = await fetch('/GetMySajuCalc', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ birthDate, birthTime })
    });
    return res.json();
}

// (테스트 버튼용) 계산버전 호출해서 결과 확인
/**
 * [수정됨] localStorage의 정보를 사용하여, 계산 API('/GetMySajuCalc')를 호출하고
 * 년주, 월주, 일주, 시주를 콘솔에 출력합니다.
 */
async function testCalcSaju() {
    const birthDate = localStorage.getItem('birthDate'); // sessionStorage 대신 localStorage 사용
    const birthTime = localStorage.getItem('birthTime'); // sessionStorage 대신 localStorage 사용

    if (!birthDate) {
        // [수정] index.html에서 넘어온 정보를 담고 있는 sajuData 객체를 직접 사용합니다.
        const sajuDataFromStorage = JSON.parse(localStorage.getItem('sajuData'));
        if (!sajuDataFromStorage) {
            alert("localStorage에서 사주 정보를 찾을 수 없습니다. index.html 페이지에서 다시 입력해주세요.");
            return;
        }
        const mySaju = JSON.parse(sajuDataFromStorage.mySajuResult);

        // mySaju 객체에서 birthDate와 birthTime을 가져옵니다.
        const storedBirthDate = mySaju.birthDate;
        const storedBirthTime = mySaju.birthTime;

        if (!storedBirthDate) {
            alert("localStorage에서 생년월일 정보를 찾을 수 없습니다.");
            return;
        }

        console.log(`[요청 정보] (LocalStorage) 생년월일: ${storedBirthDate}, 출생시간: ${storedBirthTime}`);
        await callSajuCalcAPI(storedBirthDate, storedBirthTime);

    } else {
        // 만약을 위해 sessionStorage 로직도 남겨둡니다.
        console.log(`[요청 정보] (SessionStorage) 생년월일: ${birthDate}, 출생시간: ${birthTime}`);
        await callSajuCalcAPI(birthDate, birthTime);
    }
}

// 공통 API 호출 로직을 별도 함수로 분리
async function callSajuCalcAPI(birthDate, birthTime) {
    try {
        const response = await fetch('/GetMySajuCalc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                birthDate: birthDate,
                birthTime: birthTime || "" // 시간이 없으면 빈 문자열로 보냅니다.
            }),
        });

        if (!response.ok) {
            throw new Error(`서버 응답 오류: ${response.statusText}`);
        }

        const sajuData = await response.json();

        // [핵심] 서버로부터 받은 결과를 콘솔에 보기 좋게 출력합니다. (시주 포함)
        console.log("---------- [계산 결과 (DB 미사용)] ----------");
        console.log("년주(年柱):", sajuData.yearPillar);
        console.log("월주(月柱):", sajuData.monthPillar);
        console.log("일주(日柱):", sajuData.dayPillar);
        console.log("시주(時柱):", sajuData.hourPillar || "(시간 정보 없음)"); // 시주 출력
        console.log("------------------------------------------");

        alert("계산이 완료되었습니다. F12를 눌러 콘솔창에서 결과를 확인하세요.");

    } catch (error) {
        console.error('사주 계산 중 오류 발생:', error);
        alert('사주 정보를 계산하는 데 실패했습니다.');
    }
}

// =======================================================
// [추가] 일진 달력 팝업 기능 (기존 코드 아래에 추가하세요)
// =======================================================

/**
 * 달력 팝업을 열고, 백엔드 API로부터 받은 데이터로 내용을 채웁니다.
 * @param {number} year - 달력을 표시할 년도
 * @param {number} month - 달력을 표시할 월 (1-12)
 */
async function openCalendar(year, month) {
    // 1. 백엔드에 해당 월의 일진 데이터를 요청합니다.
    try {
        const response = await fetch(`/api/month-calendar/${year}/${month}`);
        if (!response.ok) {
            throw new Error(`서버 응답 오류: ${response.statusText}`);
        }
        const monthData = await response.json();
        
        // 2. 달력 UI를 생성하고 화면에 표시합니다.
        createCalendarPopup(year, month, monthData);

    } catch (error) {
        console.error('달력 데이터를 불러오는 중 오류 발생:', error);
        alert('달력 정보를 가져오는 데 실패했습니다.');
    }
}

/**
 * [수정됨] 전달받은 데이터를 기반으로 달력 팝업 HTML을 생성하고 화면에 띄웁니다.
 * @param {number} year
 * @param {number} month
 * [수정됨] @param {Array<Object>} monthData - 일별 데이터 배열 (PHP CLI 결과)
 */
/**
 * [수정됨] 전달받은 데이터를 기반으로 달력 팝업 HTML을 생성하고 화면에 띄웁니다.
 * (헤더 형식 변경 완료)
 */
function createCalendarPopup(year, month, monthData) {
    // 기존에 열려있는 팝업이 있다면 제거
    const existingPopup = document.getElementById('calendar-popup');
    if (existingPopup) {
        existingPopup.remove();
    }

    const popup = document.createElement('div');
    popup.id = 'calendar-popup';
    popup.style.cssText = `
        position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 600px; background: white; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 1000; padding: 20px; font-family: sans-serif;
    `;

    // [핵심 수정] 헤더를 만들기 전에, 첫날 데이터에서 년주와 월주 정보를 추출합니다.
    let yearGanji = '';
    let monthGanji = '';
    if (monthData && monthData.length > 0) {
        const ganjiParts = monthData[0].ganji.split(' '); // "을사(...)년 갑신(...)월 계유(...)일"
        if (ganjiParts.length === 3) {
            yearGanji = ganjiParts[0].replace('년', '');   // "을사(乙巳)"
            monthGanji = ganjiParts[1].replace('월', ''); // "갑신(甲申)"
        }
    }

    // [핵심 수정] 헤더 (년/월, 이전/다음 버튼)
    const prevMonthDate = new Date(year, month - 2);
    const nextMonthDate = new Date(year, month);
    // [핵심 수정] h2 태그에 추출한 년주와 월주를 요청하신 형식으로 함께 표시합니다.
    const headerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <button onclick="openCalendar(${prevMonthDate.getFullYear()}, ${prevMonthDate.getMonth() + 1})">◀ 이전 달</button>
            <h2 style="color: black;">${year}년 (${yearGanji}) ${String(month).padStart(2, '0')}월 (${monthGanji})</h2>
            <button onclick="openCalendar(${nextMonthDate.getFullYear()}, ${nextMonthDate.getMonth() + 1})">다음 달 ▶</button>
        </div>
    `;

    // 테이블 생성 (이하 로직은 동일)
    let tableHTML = `
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead><tr>
                <th style="color: red;">일</th>
                <th style="color: black;">월</th>
                <th style="color: black;">화</th>
                <th style="color: black;">수</th>
                <th style="color: black;">목</th>
                <th style="color: black;">금</th>
                <th style="color: blue;">토</th>
            </tr></thead>
            <tbody>
    `;

    const firstDayOfWeek = new Date(year, month - 1, 1).getDay();
    let currentDay = 1;
    let tableBody = '';

    for (let i = 0; i < 6; i++) {
        let row = '<tr>';
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDayOfWeek || currentDay > monthData.length) {
                row += '<td></td>';
            } else {
                const dayData = monthData[currentDay - 1];
                const ganjiParts = dayData.ganji.split(' ');
                const dayGanji = ganjiParts.length === 3 ? ganjiParts[2].replace('일', '') : '';

                row += `
                    <td style="border: 1px solid #ddd; padding: 5px; vertical-align: top; height: 80px;">
                        <div style="font-weight: bold; color: ${j === 0 ? 'red' : (j === 6 ? 'blue' : 'black')}">${dayData.solar.day}</div>
                        <div style="font-size: 12px; color: green;">${dayData.month}.${dayData.day}${dayData.leap ? '윤' : ''}</div>
                        <div style="font-size: 13px; color: #555;">${dayGanji}</div>
                    </td>
                `;
                currentDay++;
            }
        }
        row += '</tr>';
        tableBody += row;
        if (currentDay > monthData.length) break;
    }

    tableHTML += tableBody + '</tbody></table>';

    const closeButtonHTML = `<button onclick="this.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; cursor: pointer;">X</button>`;

    popup.innerHTML = headerHTML + tableHTML + closeButtonHTML;
    document.body.appendChild(popup);
}