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
async function testCalcSaju() {

    const parsed = JSON.parse(sajuData.mySajuResult);

    const birthDate = parsed.birthDate;   // ✅ 서버에서 내려준 원본값
    const birthTime = parsed.birthTime;
    const lunar     = parsed.lunar;
    const leapMonth = parsed.leapMonth;

    console.log("생년월일:", birthDate);
    console.log("출생시간:", birthTime);
    console.log("음력여부:", lunar);
    console.log("윤달여부:", leapMonth);

    if (birthDate.length !== 8) {
        alert('생년월일은 YYYYMMDD 형식으로 입력해주세요.');
        return;
    }
    const data = await getMySajuCalc(birthDate, birthTime);
    if (data.error) {
        alert('오류: ' + data.error);
        console.error(data);
        return;
    }

    // 여기서 DOM에 뿌리거나, 일단 콘솔로 확인
    console.log('계산기반 결과', data);
    alert('계산기반 결과를 콘솔에서 확인하세요.');
}
