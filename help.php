<?php
session_start();
$HELP_PASSWORD = 'changyou-123';
if (isset($_GET['logout'])) { unset($_SESSION['help_authed']); header('Location: help.php'); exit; }
$authed = isset($_SESSION['help_authed']) && $_SESSION['help_authed'] === true;
$error = '';
if (!$authed && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
    if (hash_equals($HELP_PASSWORD, $pwd)) {
        $_SESSION['help_authed'] = true;
        header('Location: help.php'); exit;
    } else {
        $error = '密码错误';
    }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>帮助中心 - 常友圈 表情密文翻译器</title>
  <link rel="icon" type="image/png" href="/logo.png">
  <link rel="stylesheet" href="/fontawesome6.4/css/all.min.css">
  <link rel="stylesheet" href="/fontawesome6.4/app-controls.css">
  <style>
    :root { --fg:#e6edf3; --muted:#9aa4b2; --panel:rgba(35,45,65,0.9); --border:rgba(255,255,255,0.08); --accent:#ff9966; }
    *{box-sizing:border-box}
    body{margin:0;padding:24px;font-family:Segoe UI,Arial,sans-serif;background:linear-gradient(135deg,#2c3e50 0%,#4a6491 100%);color:var(--fg)}
    .container{max-width:980px;margin:0 auto}
    header{padding:12px 0 20px;border-bottom:1px solid var(--border);margin-bottom:20px}
    h1{font-size:28px;margin:0;display:flex;align-items:center;gap:10px;background:linear-gradient(90deg,#ff9966,#ff5e62);-webkit-background-clip:text;background-clip:text;color:transparent}
    .panel{background:var(--panel);border:1px solid var(--border);border-radius:12px;padding:18px;margin:16px 0}
    .panel h2{font-size:18px;margin:0 0 8px;display:flex;align-items:center;gap:8px;color:#ffd7c8}
    .panel p{margin:6px 0;color:var(--muted);line-height:1.65}
    .kbd{display:inline-block;padding:0 6px;border:1px solid var(--border);border-bottom-width:2px;border-radius:6px;background:rgba(255,255,255,0.05);color:#e9eef5;font-family:monospace}
    .actions{display:flex;gap:10px;margin-top:8px}
    .btn{padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:rgba(255,255,255,0.06);color:#fff;cursor:pointer}
    .btn:hover{background:rgba(255,255,255,0.1)}
    .warn{color:#ffb4a8}
    footer{margin-top:26px;color:var(--muted);text-align:center;font-size:12px}
    .login{max-width:420px;margin:8vh auto 0}
    .login h2{margin:0 0 6px;font-size:22px}
    .login .msg{color:#ffb4a8;margin:8px 0 0}
    .input{width:100%;padding:12px;border-radius:8px;border:1px solid var(--border);background:rgba(255,255,255,0.06);color:#fff;margin:8px 0}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1><i class="fa-solid fa-circle-question"></i><span>帮助中心</span></h1>
      <div class="actions">
        <button id="backBtn" class="btn"><i class="fa-solid fa-house"></i> 返回首页</button>
        <?php if ($authed): ?><a class="btn" href="?logout=1"><i class="fa-solid fa-right-from-bracket"></i> 退出</a><?php endif; ?>
      </div>
    </header>
    <?php if (!$authed): ?>
      <div class="panel login">
        <h2><i class="fa-solid fa-lock"></i> 访问受限</h2>
        <p>请输入帮助页面密码。</p>
        <form method="post" action="help.php">
          <input class="input" type="password" name="pwd" placeholder="输入密码">
          <button class="btn" type="submit"><i class="fa-solid fa-door-open"></i> 进入</button>
        </form>
        <?php if ($error): ?><div class="msg"><?php echo htmlspecialchars($error,ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>
      </div>
    <?php else: ?>
      <div class="panel">
        <h2><i class="fa-solid fa-vial"></i> 多语言自测</h2>
        <p>一次性用 8 语种样例执行「加密 → 解密」，检验是否可完全还原。</p>
        <div style="margin:8px 0">
          <span class="kbd">密钥</span>
          <input id="devKey" type="text" placeholder="可留空" class="input" style="display:inline-block;width:auto;min-width:240px;margin-left:6px">
        </div>
        <div style="margin:8px 0">
          <label><input id="devNumbers" type="checkbox" checked> 加密数字</label>
          <label style="margin-left:14px"><input id="devPunc" type="checkbox" checked> 加密标点</label>
          <label style="margin-left:14px"><input id="devSpaces" type="checkbox" checked> 保留空格</label>
        </div>
        <div class="actions">
          <button id="runSelfTestBtn" class="btn"><i class="fa-solid fa-play"></i> 运行自测</button>
        </div>
        <div id="devReport" style="margin-top:10px;display:none"></div>
      </div>
      <div class="panel">
        <h2><i class="fa-solid fa-shield-heart"></i> 使用与排查</h2>
        <p>- 互通请使用相同密钥。</p>
        <p>- 若包含标点，请勾选加密标点。</p>
        <p>- 若复制后不一致，检查是否混入不可见字符。</p>
      </div>
    <?php endif; ?>
    <footer>© 2026 常友圈 - 表情密文翻译器</footer>
  </div>
  <script>
    (function(){
      var backBtn = document.getElementById('backBtn');
      if (backBtn) backBtn.addEventListener('click', function(){
        location.href = '/';
      });
    })();
  </script>
<?php if ($authed): ?>
  <script>
    function h32(s){let h=2166136261>>>0;for(let i=0;i<s.length;i++){h^=s.charCodeAt(i);h=Math.imul(h,16777619);}return h>>>0;}
    function rng(a){return function(){let t=a+=0x6D2B79F5;t=Math.imul(t^(t>>>15),t|1);t^=t+Math.imul(t^(t>>>7),t|61);return((t^(t>>>14))>>>0)/4294967296;}}
    function shuffle(arr,seed){const r=rng(seed>>>0),a=arr.slice();for(let i=a.length-1;i>0;i--){const j=Math.floor(r()*(i+1));const t=a[i];a[i]=a[j];a[j]=t;}return a;}
    const AEmojis=["🐶","🐱","🐭","🐹","🐰","🦊","🐻","🐼","🐨","🐯","🦁","🐮","🐷","🐸","🐵","🐔","🐧","🐦","🐤","🦆","🦅","🦉","🦇","🐺","🐗","🐴","🦄","🐝","🐛","🦋","🐌","🐞","🐜","🦗","🕷","🦂","🐢","🐍","🦎","🐊","🐅","🐆","🦓","🦍","🐘","🦏","🐪","🐫","🦒","🐃","🐂","🐄","🐎","🐖","🐏","🐑","🐐","🦌","🐕","🐩","🐈","🐓","🦃","🕊","🐇","🐁","🐀","🐿","🦔","🐾","🐉","🐲","🌵","🎄","🌲","🌳","🌴","🌱","🌿","☘️","🍀","🎍","🎋","🍃","🍂","🍁","🍄","🌾","💐","🌷","🌹","🥀","🌺","🌸","🌼","🌻","🌞","🌝","🌛","🌜","🌚","🌕","🌖","🌗","🌘","🌑","🌒","🌓","🌔","🌙","🌎","🌍","🌏","🪐","💫","⭐","🌟","✨","⚡","☄️","💥","🔥","🌪","🌈","☀️","🌤","⛅","🌥","☁️","🌦","🌧","⛈","🌩","🌨","❄️","☃️","⛄","🌬","💨","💧","💦","☔","☂️","🌊","🌫"];
    const PEmojis=["😀","😃","😄","😁","😆","😅","😂","🤣","☺️","😊","😇","🙂","🙃","😉","😌","😍","🥰","😘","😗","😙","😚","😋","😛","😝","😜","🤪","🤨","🧐","🤓","😎","🥸","🤩","🥳","😏","😒","😞","😔","😟","😕","🙁","☹️","😣","😖","😫","😩","🥺","😢","😭","😤","😠","😡","🤬","🤯","😳","🥵","🥶","😱","😨","😰","😥","😓","🤗","🤔","🤭","🤫","🤥","😶","😐","😑","😬","🙄","😯","😦","😧","😮","😲","🥱","😴","🤤","😪","😵","🤐","🥴","🤢","🤮","🤧","😷","🤒","🤕","🤑","🤠","😈","👿","👹","👺","🤡","💩","👻","💀","☠️","👽","👾","🤖","🎃","😺","😸","😹","😻","😼","😽","🙀","😿","😾","👋","🤚","🖐️","✋","🖖","👌","🤌","🤏","✌️","🤞","🤟","🤘","🤙","👈","👉","👆","🖕","👇","☝️","👍","👎","✊","👊","🤛","🤜","👏","🙌","👐","🤲","🤝","🙏","✍️","💅","🤳","💪","🦾","🦿"];
    function getSeed(){const v=document.getElementById('devKey')?.value||'';return v? h32(v):0;}
    function zAlp(seed){const b=['\u200B','\u200C','\u200D','\u2060'];return seed? shuffle(b,seed):b;}
    function encCp(cp,seed){const MOD=0x110000;let v=(cp+(seed%MOD)+MOD)%MOD;const a=zAlp(seed);if(v===0)return a[0];let out='';while(v>0){out+=a[v&3];v>>>=2;}return out;}
    function decCp(run,seed){const a=zAlp(seed);const m=new Map([[a[0],0],[a[1],1],[a[2],2],[a[3],3]]);let val=0,sh=0;for(let i=0;i<run.length;i++){const ch=run[i];if(!m.has(ch))break;val|=(m.get(ch)&3)<<sh;sh+=2;}const MOD=0x110000;let cp=(val-(seed%MOD));cp=((cp%MOD)+MOD)%MOD;return cp;}
    const SENTINEL='\u2063';
    function classify(ch){
      if(/[0-9]/.test(ch))return'num';
      if(/[a-zA-Z]/.test(ch))return'en';
      if(/[\u00C0-\u024F]/.test(ch))return'lat';
      if(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(ch))return'enp';
      if(/[¡¿«»…–—‚‘’„“”]/.test(ch))return'wp';
      if(/[，。！？、；："'（）《》【】｛｝·～＠＃￥％＆＊＋－＝＿]/.test(ch))return'cp';
      if(/[\u4e00-\u9fff]/.test(ch))return'cjk';
      if(/[\u3040-\u30FF\u31F0-\u31FF\uFF66-\uFF9D]/.test(ch))return'jp';
      if(/[\uAC00-\uD7AF\u1100-\u11FF\u3130-\u318F]/.test(ch))return'kr';
      if(/\s/.test(ch))return'sp';
      return'oth';
    }
    function buildMaps(){
      const seed=getSeed();
      const AE= seed? shuffle(AEmojis,seed):AEmojis;
      const PE= seed? shuffle(PEmojis,(seed^0x9e3779b9)>>>0):PEmojis;
      const c2e=new Map();const e2c=new Map();
      for(let i=0;i<=9;i++){const ch=String(i);const e=AE[i%AE.length];c2e.set(ch,e);e2c.set(e,ch);}
      for(let i=0;i<26;i++){const ch=String.fromCharCode(65+i);const e=AE[(10+i)%AE.length];c2e.set(ch,e);e2c.set(e,ch);}
      for(let i=0;i<26;i++){const ch=String.fromCharCode(97+i);const e=AE[(36+i)%AE.length];c2e.set(ch,e);e2c.set(e,ch);}
      const enP=['!','@','#','$','%','^','&','*','(',')','_','+','-','=', '[',']','{','}','|','\\',';',';',':','"',"'",',','.','<','>','/','?','`','~'];
      enP.forEach((ch,idx)=>{const e=AE[(62+idx)%AE.length];c2e.set(ch,e);e2c.set(e,ch);});
      const zhP=['，','。','！','？','、','；','：','「','」','『','』','（','）','《','》','【','】','｛','｝','·','～','＠','＃','￥','％','＆','＊','＋','－','＝','＿'];
      zhP.forEach((ch,idx)=>{const e=PE[idx%PE.length];c2e.set(ch,e);e2c.set(e,ch);});
      return {AE,PE,c2e,e2c};
    }
    function encText(s,opts){
      const {AE,PE,c2e}=buildMaps();
      const seed=getSeed();let out='';
      for(let i=0;i<s.length;i++){
        const ch=s[i];const t=classify(ch);
        let enc=false;
        if(t==='sp'){ if(opts.sp){enc=true;} else{out+=ch;continue;} }
        else if(t==='num'){enc=opts.num;}
        else if(t==='enp'||t==='cp'||t==='wp'){enc=opts.punc;}
        else if(t==='en'||t==='lat'||t==='cjk'||t==='jp'||t==='kr'){enc=true;}
        if(!enc){out+=ch;continue;}
        let e=c2e.get(ch);
        if(!e&&(t==='cjk'||t==='jp'||t==='kr')){const idx=Math.abs((ch.charCodeAt(0)*1315423911)>>>0)%PE.length;e=PE[idx];}
        if(!e&&(t==='lat'||t==='wp')){const idx=Math.abs((ch.charCodeAt(0)*2654435761)>>>0)%AE.length;e=AE[idx];}
        if(!e){out+=ch;continue;}
        if(t==='cjk'||t==='jp'||t==='kr'||t==='lat'||t==='wp'){out+=e+SENTINEL+encCp(ch.codePointAt(0),seed);}
        else{out+=e;}
      }
      return out;
    }
    function decText(s){
      const {AE,PE,e2c}=buildMaps();
      const seed=getSeed();const maxLen=Math.max(...AE.map(e=>e.length),...PE.map(e=>e.length),2);let out='';let i=0;
      while(i<s.length){
        let matched=false;const tryLen=Math.min(maxLen,s.length-i);
        for(let len=tryLen;len>=1;len--){
          const tok=s.substr(i,len);const isE=AE.includes(tok)||PE.includes(tok);if(!isE)continue;
          const next=i+len;
          if(next<s.length&&s[next]===SENTINEL){
            let j=next+1;const alpha=new Set(zAlp(seed));let run='';while(j<s.length&&alpha.has(s[j])){run+=s[j];j++;}
            if(run){const cp=decCp(run,seed);out+=String.fromCodePoint(cp);i=j;matched=true;break;}
          }
          const m=e2c.get(tok);if(m){out+=m;i+=len;matched=true;break;}
        }
        if(!matched){out+=s[i];i++;}
      }
      return out;
    }
    function runDevSelfTest(){
      const report=document.getElementById('devReport');if(!report)return;
      const opts={
        num:document.getElementById('devNumbers')?.checked!==false,
        punc:document.getElementById('devPunc')?.checked!==false,
        sp:document.getElementById('devSpaces')?.checked===false
      };
      const cases=[
        { code:'zh', text:"中文简体：你好，世界！这是测试。" },
        { code:'zh-TW', text:"中文繁體：你好，世界！這是測試。" },
        { code:'en', text:"English: Café Noël façade — test, OK?" },
        { code:'es', text:"Español: ¡Hola! corazón, piñata, acción, ¿vale?" },
        { code:'fr', text:"Français : élève, cœur, façade, Noël — «oui» ?" },
        { code:'de', text:"Deutsch: Grüße, schön, Straße, Fußball — „Ja!“" },
        { code:'ja', text:"日本語：こんにちは世界。テストです。" },
        { code:'ko', text:"한국어: 안녕하세요 세계. 이것은 테스트입니다." }
      ];
      const norm=(s)=>s.normalize('NFC').replace(/[\u200B\u200C\u200D\u2060\u2063]/g,'');
      const esc=(s)=>s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      let rows='';let pass=0;
      for(const c of cases){
        const enc=encText(c.text,opts);
        const dec=decText(enc);
        const ok=norm(dec)===norm(c.text);
        if(ok)pass++;
        rows+=`<tr>
          <td style="white-space:nowrap;color:#b0b0d0;">${esc(c.code)}</td>
          <td>${esc(c.text)}</td>
          <td>${esc(enc)}</td>
          <td>${esc(dec)}</td>
          <td style="white-space:nowrap;font-weight:600;${ok?'color:#4ecdc4':'color:#ff5e62'}">${ok?'通过':'失败'}</td>
        </tr>`;
      }
      report.innerHTML = `
        <div class="panel" style="padding:0">
          <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
            <div><i class="fa-solid fa-table"></i> 报告</div>
            <div>${pass}/${cases.length}</div>
          </div>
          <div style="overflow:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr>
                  <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">语言</th>
                  <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">原文</th>
                  <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">加密</th>
                  <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">解密</th>
                  <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">结果</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          </div>
        </div>
      `;
      report.style.display='';
    }
    document.getElementById('runSelfTestBtn')?.addEventListener('click', runDevSelfTest);
  </script>
<?php endif; ?>
</body>
</html>
