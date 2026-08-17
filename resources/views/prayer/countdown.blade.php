<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prayer Countdown</title>
<style>
html,body{
margin:0;height:100%;
font-family:Arial,sans-serif;
background:#0b1d2a;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
}
.container{text-align:center}
.icon{font-size:90px}
.title{font-size:64px;font-weight:bold;margin:20px 0;color:#ffd54f}
.timer{font-size:140px;font-weight:bold;margin:30px 0}
.info{font-size:36px;color:#d9d9d9}
</style>
</head>
<body>
<div class="container">
<div class="icon">🕌</div>
<div class="title">ADZAN AKAN BERKUMANDANG</div>
<div class="timer" id="timer">01:00</div>
<div class="info">Mohon mempersiapkan diri untuk sholat</div>
</div>

<script>
let remaining = 60;
function render(){
 const m=String(Math.floor(remaining/60)).padStart(2,'0');
 const s=String(remaining%60).padStart(2,'0');
 document.getElementById('timer').textContent=`${m}:${s}`;
 if(remaining>0){remaining--;}
}
render();
setInterval(render,1000);
</script>
</body>
</html>
