<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GradNet — Where Graduates Stay Connected</title>
<meta name="description" content="Reconnect with classmates, expand your professional network, join alumni events, and stay engaged with your school through one unified platform.">
<link rel="icon" type="image/png" href="{{ asset('images/favicon-rounded.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--navy:#001540;--blue:#003087;--blue-mid:#1e4db7;--blue-light:#e8eef8;--gold:#c4972f;--gold-light:#fdf3dc;--surface:#fff;--g50:#f9fafc;--g100:#f0f2f7;--g200:#e2e6f0;--td:#0d1b2a;--tb:#3d4f6e;--tm:#7988a1;--r:16px;--ease:cubic-bezier(.4,0,.2,1)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;color:var(--tb);background:#fff;-webkit-font-smoothing:antialiased;overflow-x:hidden}
a{text-decoration:none;color:inherit}
img{display:block;max-width:100%}

@keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
@keyframes floatAlt{0%,100%{transform:translateY(-6px)}50%{transform:translateY(6px)}}
@keyframes pulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(196,151,47,0)}50%{box-shadow:0 0 0 10px rgba(196,151,47,.15)}}
.reveal{opacity:0;transform:translateY(24px);transition:opacity .6s var(--ease),transform .6s var(--ease)}
.reveal.visible{opacity:1;transform:translateY(0)}
.rl{opacity:0;transform:translateX(-30px);transition:opacity .7s var(--ease),transform .7s var(--ease)}
.rl.visible{opacity:1;transform:translateX(0)}
.rr{opacity:0;transform:translateX(30px);transition:opacity .7s var(--ease),transform .7s var(--ease)}
.rr.visible{opacity:1;transform:translateX(0)}
.d1{transition-delay:.1s}.d2{transition-delay:.2s}.d3{transition-delay:.3s}.d4{transition-delay:.4s}.d5{transition-delay:.5s}.d6{transition-delay:.6s}

/* NAV */
.lpnav{position:fixed;top:0;left:0;right:0;z-index:1000;padding:0 40px;height:68px;display:flex;align-items:center;justify-content:space-between;transition:background .35s,box-shadow .35s}
.lpnav.scrolled{background:rgba(255,255,255,.95);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);box-shadow:0 1px 0 rgba(0,0,0,.07),0 4px 24px rgba(0,0,0,.06)}
/* Default (on dark hero) */
.nav-brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:1.25rem;letter-spacing:-.03em;color:#fff}
.nav-brand img{width:36px;height:36px;border-radius:9px;object-fit:contain}
.nav-brand span{color:var(--gold)}
.nav-links{display:flex;align-items:center;gap:2px}
.nav-links a{padding:8px 16px;border-radius:8px;font-size:.875rem;font-weight:500;color:rgba(255,255,255,.8);transition:all .18s var(--ease)}
.nav-links a:hover{background:rgba(255,255,255,.12);color:#fff}
.nav-actions{display:flex;align-items:center;gap:10px}
.btn-ghost{padding:8px 18px;border-radius:9px;font-size:.875rem;font-weight:600;color:rgba(255,255,255,.9);border:1.5px solid rgba(255,255,255,.3);background:transparent;transition:all .18s;cursor:pointer}
.btn-ghost:hover{background:rgba(255,255,255,.12);color:#fff}
.btn-solid{padding:8px 20px;border-radius:9px;font-size:.875rem;font-weight:600;color:#fff;background:var(--gold);border:1.5px solid transparent;transition:all .18s;cursor:pointer;box-shadow:0 2px 8px rgba(196,151,47,.4)}
.btn-solid:hover{background:#d4a73a;box-shadow:0 4px 16px rgba(196,151,47,.5);transform:translateY(-1px)}
/* Scrolled (on white bg) */
.lpnav.scrolled .nav-brand{color:var(--navy)}
.lpnav.scrolled .nav-brand span{color:var(--gold)}
.lpnav.scrolled .nav-links a{color:var(--tb)}
.lpnav.scrolled .nav-links a:hover{background:var(--g100);color:var(--blue)}
.lpnav.scrolled .btn-ghost{color:var(--blue);border-color:var(--blue-light)}
.lpnav.scrolled .btn-ghost:hover{background:var(--blue-light)}
.lpnav.scrolled .btn-solid{background:var(--blue);box-shadow:0 2px 8px rgba(0,48,135,.3)}
.lpnav.scrolled .btn-solid:hover{background:var(--navy)}

/* HERO */
.hero{min-height:100vh;background:linear-gradient(160deg,var(--navy) 0%,#002070 40%,var(--blue) 70%,var(--blue-mid) 100%);display:flex;align-items:center;position:relative;overflow:hidden;padding:100px 0 80px}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 80%,rgba(196,151,47,.15) 0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(30,77,183,.3) 0%,transparent 50%)}
.hero-grid{position:absolute;inset:0;opacity:.035;background-image:linear-gradient(rgba(255,255,255,.9) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.9) 1px,transparent 1px);background-size:48px 48px}
.hero-content{position:relative;z-index:1}
.hero-badge{display:inline-flex;align-items:center;gap:8px;padding:7px 16px;border-radius:100px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);font-size:.8rem;font-weight:600;margin-bottom:24px;letter-spacing:.03em;backdrop-filter:blur(8px);animation:fadeUp .6s var(--ease) both}
.hero-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--gold);animation:pulseGlow 2s infinite}
.hero-headline{font-size:clamp(2.2rem,5vw,3.8rem);font-weight:900;line-height:1.08;letter-spacing:-.035em;color:#fff;margin-bottom:22px;animation:fadeUp .6s var(--ease) .1s both}
.hero-headline em{font-style:normal;color:var(--gold)}
.hero-sub{font-size:clamp(1rem,2vw,1.15rem);line-height:1.75;color:rgba(255,255,255,.72);max-width:520px;margin-bottom:36px;animation:fadeUp .6s var(--ease) .2s both}
.hero-ctas{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:48px;animation:fadeUp .6s var(--ease) .3s both}
.btn-gold{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:12px;font-size:1rem;font-weight:700;color:var(--navy);background:var(--gold);border:none;cursor:pointer;transition:all .2s var(--ease);box-shadow:0 4px 20px rgba(196,151,47,.45)}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(196,151,47,.5);background:#d4a73a;color:var(--navy)}
.btn-outline-w{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:12px;font-size:1rem;font-weight:600;color:#fff;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.25);cursor:pointer;transition:all .2s var(--ease);backdrop-filter:blur(8px)}
.btn-outline-w:hover{background:rgba(255,255,255,.18);transform:translateY(-2px)}
.hero-trust{display:flex;align-items:center;gap:12px;animation:fadeUp .6s var(--ease) .4s both}
.trust-avatars{display:flex}
.trust-av{width:32px;height:32px;border-radius:50%;border:2px solid rgba(255,255,255,.4);margin-left:-8px;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:800;color:#fff;position:relative}
.trust-avatars>.trust-av:first-child{margin-left:0}
.hero-trust p{font-size:.82rem;color:rgba(255,255,255,.7)}
.hero-trust strong{color:#fff}

/* MOCKUP */
.hero-visual{position:relative;z-index:1;animation:fadeIn .8s var(--ease) .3s both}
.mockup-browser{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);border-radius:16px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,20,.5),0 0 0 1px rgba(255,255,255,.08);backdrop-filter:blur(4px);animation:float 6s ease-in-out infinite}
.browser-bar{background:rgba(255,255,255,.08);border-bottom:1px solid rgba(255,255,255,.1);padding:10px 16px;display:flex;align-items:center;gap:10px}
.bdots{display:flex;gap:5px}
.bdots span{width:10px;height:10px;border-radius:50%}
.bdots span:nth-child(1){background:#ff5f57}
.bdots span:nth-child(2){background:#febc2e}
.bdots span:nth-child(3){background:#28c840}
.burl{flex:1;background:rgba(255,255,255,.08);border-radius:6px;padding:4px 12px;font-size:.7rem;color:rgba(255,255,255,.5);font-family:monospace}
.mbody{display:flex;height:340px}
.msidebar{width:52px;background:rgba(0,20,60,.6);border-right:1px solid rgba(255,255,255,.06);display:flex;flex-direction:column;align-items:center;padding:12px 0;gap:6px}
.msi{width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;font-size:.75rem;color:rgba(255,255,255,.5)}
.msi.active{background:var(--blue);color:#fff}
.mmain{flex:1;background:rgba(10,20,50,.4);padding:12px;overflow:hidden}
.mtopbar{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.msearch{flex:1;height:26px;border-radius:6px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1)}
.mav{width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-mid))}
.mpost{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:10px;margin-bottom:8px}
.mph{display:flex;align-items:center;gap:6px;margin-bottom:6px}
.mpav{width:22px;height:22px;border-radius:50%;flex-shrink:0}
.mpname{width:70px;height:6px;border-radius:3px;background:rgba(255,255,255,.25)}
.mpt{width:30px;height:5px;border-radius:3px;background:rgba(255,255,255,.1);margin-left:auto}
.mpline{height:5px;border-radius:3px;background:rgba(255,255,255,.12);margin-bottom:4px}
.mpline.short{width:60%}
.mpimg{height:60px;border-radius:6px;background:linear-gradient(135deg,rgba(30,77,183,.4),rgba(196,151,47,.3));margin-top:6px}
.mpact{display:flex;gap:8px;margin-top:6px}
.mpill{height:18px;width:44px;border-radius:100px;background:rgba(255,255,255,.07)}
.float-card{position:absolute;background:rgba(255,255,255,.95);border-radius:14px;padding:14px 18px;box-shadow:0 16px 48px rgba(0,0,0,.25);display:flex;align-items:center;gap:12px;border:1px solid rgba(255,255,255,.8);white-space:nowrap}
.fc1{bottom:60px;left:-40px;animation:floatAlt 5s ease-in-out infinite}
.fc2{top:40px;right:-30px;animation:float 5.5s ease-in-out 1s infinite}
.fc3{bottom:120px;right:-20px;animation:floatAlt 6s ease-in-out .5s infinite}
.fi{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
.fi.b{background:var(--blue-light);color:var(--blue)}
.fi.g{background:var(--gold-light);color:var(--gold)}
.fi.gr{background:#e6f4ed;color:#1a7f4b}
.fc-label{font-size:.7rem;color:var(--tm);font-weight:500;margin-bottom:2px}
.fc-value{font-size:1rem;font-weight:800;color:var(--td)}

/* STATS */
.stats-sec{background:var(--surface);padding:80px 0;border-bottom:1px solid var(--g200)}
.stats-label{text-align:center;font-size:.8rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--tm);margin-bottom:48px}
.stat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:var(--g200);border-radius:var(--r);overflow:hidden}
.stat-card{background:var(--surface);padding:32px 24px;text-align:center;transition:all .25s var(--ease)}
.stat-card:hover{background:var(--g50);transform:translateY(-2px)}
.si{font-size:1.5rem;margin-bottom:12px}
.sn{font-size:2.5rem;font-weight:900;letter-spacing:-.04em;color:var(--navy);line-height:1;margin-bottom:6px}
.sn .ss{font-size:1.6rem;color:var(--gold)}
.sd{font-size:.82rem;color:var(--tm);font-weight:500}

/* SECTION CHROME */
.eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--blue);margin-bottom:16px}
.eyebrow::before{content:'';width:20px;height:2px;background:var(--gold);border-radius:2px}
.sec-title{font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:900;letter-spacing:-.03em;color:var(--td);line-height:1.15;margin-bottom:16px}
.sec-title em{font-style:normal;color:var(--blue)}
.sec-sub{font-size:1.05rem;color:var(--tm);line-height:1.7;max-width:560px}

/* WHY */
.why-sec{background:var(--g50);padding:100px 0}
.problem-card{
    background:var(--surface);border-radius:var(--r);padding:32px 28px;
    border:1px solid var(--g200);position:relative;overflow:hidden;
    transition:all .28s var(--ease);
}
.problem-card::after{
    content:'';position:absolute;inset:0;border-radius:var(--r);
    background:linear-gradient(135deg,rgba(244,63,94,.04) 0%,transparent 60%);
    pointer-events:none;
}
.problem-card:hover{box-shadow:0 12px 40px rgba(0,0,0,.08);transform:translateY(-4px);border-color:rgba(244,63,94,.2)}
.p-icon{
    width:48px;height:48px;border-radius:14px;
    background:linear-gradient(135deg,#fff1f4,#fde8ed);
    color:#e11d48;display:flex;align-items:center;justify-content:center;
    font-size:1.1rem;margin-bottom:18px;
    box-shadow:0 4px 12px rgba(225,29,72,.15);
}
.p-title{font-size:1rem;font-weight:800;color:var(--td);margin-bottom:8px;letter-spacing:-.01em}
.p-desc{font-size:.875rem;color:var(--tm);line-height:1.65}
.solution-card{
    background:var(--surface);border-radius:var(--r);padding:32px 28px;
    border:1px solid var(--g200);position:relative;overflow:hidden;
    transition:all .28s var(--ease);
}
.solution-card::after{
    content:'';position:absolute;inset:0;border-radius:var(--r);
    background:linear-gradient(135deg,rgba(0,48,135,.04) 0%,transparent 60%);
    pointer-events:none;
}
.solution-card:hover{box-shadow:0 12px 40px rgba(0,48,135,.1);transform:translateY(-4px);border-color:rgba(0,48,135,.18)}
.s-icon{
    width:48px;height:48px;border-radius:14px;
    background:linear-gradient(135deg,var(--blue-light),#d0dcf5);
    color:var(--blue);display:flex;align-items:center;justify-content:center;
    font-size:1.1rem;margin-bottom:18px;
    box-shadow:0 4px 12px rgba(0,48,135,.15);
}
.s-title{font-size:1rem;font-weight:800;color:var(--td);margin-bottom:8px;letter-spacing:-.01em}
.s-desc{font-size:.875rem;color:var(--tm);line-height:1.65}
.why-divider{display:flex;align-items:center;justify-content:center;gap:16px;padding:32px 0;color:var(--blue);font-size:.875rem;font-weight:700}
.why-divider::before,.why-divider::after{content:'';flex:1;height:1px}
.why-divider::before{background:linear-gradient(90deg,transparent,var(--g200))}
.why-divider::after{background:linear-gradient(90deg,var(--g200),transparent)}

/* FEATURES */
.feat-sec{background:var(--surface);padding:100px 0}
.feat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px}
.feat-card{background:var(--g50);border:1px solid var(--g200);border-radius:var(--r);padding:28px 24px;transition:all .3s var(--ease);cursor:default;position:relative;overflow:hidden}
.feat-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,48,135,.1);border-color:transparent;background:var(--surface)}
.feat-card:hover .feat-icon{background:var(--blue);color:#fff}
.feat-icon{width:48px;height:48px;border-radius:12px;background:var(--blue-light);color:var(--blue);display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:16px;transition:all .3s var(--ease)}
.feat-title{font-size:1rem;font-weight:700;color:var(--td);margin-bottom:6px}
.feat-desc{font-size:.85rem;color:var(--tm);line-height:1.6}

/* TOUR */
.tour-sec{background:var(--navy);padding:100px 0;position:relative;overflow:hidden}
.tour-sec::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 70% 50%,rgba(30,77,183,.4) 0%,transparent 60%)}
.tour-sec .eyebrow{color:var(--gold)}
.tour-sec .sec-title{color:#fff}
.tour-sec .sec-sub{color:rgba(255,255,255,.6)}
.tour-tabs{display:flex;gap:4px;flex-wrap:wrap;margin-bottom:36px}
.ttab{padding:9px 20px;border-radius:8px;font-size:.85rem;font-weight:600;color:rgba(255,255,255,.55);background:transparent;border:1px solid rgba(255,255,255,.1);cursor:pointer;transition:all .2s var(--ease)}
.ttab:hover{color:#fff;background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.25)}
.ttab.active{color:#fff;background:var(--blue);border-color:var(--blue)}
.tpanel{display:none}
.tpanel.active{display:block;animation:fadeUp .4s var(--ease) both}
.tmock{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:20px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.5)}
.tbar{background:rgba(255,255,255,.06);padding:12px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid rgba(255,255,255,.08)}
.tbar .bdots span{width:11px;height:11px;border-radius:50%}
.tbar .bdots span:nth-child(1){background:#ff5f57}
.tbar .bdots span:nth-child(2){background:#febc2e}
.tbar .bdots span:nth-child(3){background:#28c840}
.turl{flex:1;background:rgba(255,255,255,.06);border-radius:6px;padding:5px 12px;font-size:.72rem;color:rgba(255,255,255,.4);font-family:monospace}
.tscreen{background:#f0f2f7;min-height:380px;display:flex;position:relative;overflow:hidden}
.tside{width:190px;background:#fff;border-right:1px solid #e2e6f0;padding:16px 12px;flex-shrink:0}
.tb{font-size:.9rem;font-weight:800;color:var(--navy);margin-bottom:20px;padding:0 4px}
.tni{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;font-size:.78rem;font-weight:500;color:var(--tm);margin-bottom:2px}
.tni.active{background:var(--blue-light);color:var(--blue);font-weight:600}
.tnd{width:16px;height:16px;border-radius:4px;background:currentColor;opacity:.4;flex-shrink:0}
.tmain{flex:1;padding:20px;overflow:hidden}
.tth{display:flex;gap:8px;align-items:center;margin-bottom:16px}
.ttsearch{flex:1;height:30px;background:#fff;border-radius:8px;border:1px solid #e2e6f0}
.ttav{width:30px;height:30px;border-radius:50%;background:var(--blue);flex-shrink:0}
.tpost{background:#fff;border-radius:10px;padding:14px;border:1px solid #e2e6f0;margin-bottom:10px}
.tph{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.tpa{width:28px;height:28px;border-radius:50%;flex-shrink:0}
.tpname{font-size:.78rem;font-weight:700;color:var(--td)}
.tptime{font-size:.68rem;color:var(--tm)}
.tptext{font-size:.78rem;color:var(--tb);line-height:1.5;margin-bottom:8px}
.tpimg{height:100px;border-radius:8px;overflow:hidden;margin-bottom:8px}
.tpimg img{width:100%;height:100%;object-fit:cover}
.tpacts{display:flex;gap:12px}
.tpact{font-size:.72rem;color:var(--tm);font-weight:500}
.tevent{background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e2e6f0;margin-bottom:10px;display:flex}
.tei{width:80px;flex-shrink:0;overflow:hidden}
.tei img{width:100%;height:100%;object-fit:cover}
.teb{padding:10px 12px}
.tet{font-size:.8rem;font-weight:700;color:var(--td);margin-bottom:3px}
.tem{font-size:.7rem;color:var(--tm)}
.talgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.talcard{background:#fff;border-radius:8px;padding:12px;text-align:center;border:1px solid #e2e6f0}
.talav{width:40px;height:40px;border-radius:50%;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:800;color:#fff}
.talname{font-size:.72rem;font-weight:700;color:var(--td)}
.talprog{font-size:.65rem;color:var(--tm)}

/* BENEFITS */
.ben-sec{padding:100px 0}
.ben-list{list-style:none;display:flex;flex-direction:column;gap:16px;margin-top:32px}
.ben-item{display:flex;align-items:flex-start;gap:14px}
.ben-check{width:24px;height:24px;border-radius:6px;background:var(--blue-light);color:var(--blue);display:flex;align-items:center;justify-content:center;font-size:.7rem;flex-shrink:0;margin-top:2px}
.ben-text strong{display:block;font-size:.95rem;font-weight:700;color:var(--td);margin-bottom:2px}
.ben-text span{font-size:.85rem;color:var(--tm);line-height:1.5}
.bv{background:linear-gradient(145deg,var(--navy) 0%,var(--blue) 100%);border-radius:24px;padding:40px;color:#fff;position:relative;overflow:hidden;min-height:420px;display:flex;flex-direction:column;justify-content:center}
.bv::before{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.04);right:-80px;bottom:-80px}
.bvnum{font-size:2.8rem;font-weight:900;letter-spacing:-.04em;color:var(--gold);line-height:1;margin-bottom:4px}
.bvlabel{font-size:.85rem;color:rgba(255,255,255,.65);font-weight:500}
.bvdiv{height:1px;background:rgba(255,255,255,.12);margin:12px 0 20px}

/* HOW */
.how-sec{background:var(--surface);padding:100px 0}
.steps{position:relative}
.steps::before{content:'';position:absolute;left:27px;top:60px;bottom:60px;width:2px;background:linear-gradient(to bottom,var(--blue-light),var(--blue),var(--blue-light));z-index:0}
.step{display:flex;gap:24px;align-items:flex-start;position:relative;z-index:1;margin-bottom:40px}
.step:last-child{margin-bottom:0}
.stepnum{width:56px;height:56px;border-radius:16px;flex-shrink:0;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:900;letter-spacing:-.02em;box-shadow:0 4px 16px rgba(0,48,135,.3);transition:all .25s var(--ease)}
.step:hover .stepnum{background:var(--gold);transform:scale(1.08)}
.stepbody{padding-top:10px}
.steptitle{font-size:1.05rem;font-weight:700;color:var(--td);margin-bottom:4px}
.stepdesc{font-size:.875rem;color:var(--tm);line-height:1.6}

/* SECURITY */
.sec-sec{background:var(--g50);padding:80px 0}
.secgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}
.seccard{background:var(--surface);border:1px solid var(--g200);border-radius:var(--r);padding:24px 20px;text-align:center;transition:all .25s var(--ease)}
.seccard:hover{border-color:var(--blue);box-shadow:0 8px 24px rgba(0,48,135,.08);transform:translateY(-2px)}
.secicon{font-size:1.8rem;margin-bottom:12px;color:var(--blue)}
.sectitle{font-size:.875rem;font-weight:700;color:var(--td);margin-bottom:4px}
.secdesc{font-size:.78rem;color:var(--tm);line-height:1.5}

/* FINAL CTA */
.fcta{background:linear-gradient(145deg,var(--navy) 0%,#002060 50%,var(--blue) 100%);padding:120px 0;text-align:center;position:relative;overflow:hidden}
.fcta::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(196,151,47,.12) 0%,transparent 50%),radial-gradient(ellipse at 80% 50%,rgba(30,77,183,.3) 0%,transparent 50%)}
.fcta>*{position:relative;z-index:1}
.fcta-title{font-size:clamp(2rem,4vw,3.2rem);font-weight:900;color:#fff;letter-spacing:-.03em;margin-bottom:16px;line-height:1.1}
.fcta-title em{color:var(--gold);font-style:normal}
.fcta-sub{font-size:1.1rem;color:rgba(255,255,255,.65);max-width:520px;margin:0 auto 40px;line-height:1.7}
.fcta-btns{display:flex;justify-content:center;flex-wrap:wrap;gap:14px}
.btn-cta-g{display:inline-flex;align-items:center;gap:9px;padding:15px 32px;border-radius:12px;font-size:1rem;font-weight:700;color:var(--navy);background:var(--gold);border:none;cursor:pointer;box-shadow:0 4px 24px rgba(196,151,47,.4);transition:all .2s var(--ease)}
.btn-cta-g:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(196,151,47,.5);background:#d4a73a;color:var(--navy)}
.btn-cta-o{display:inline-flex;align-items:center;gap:9px;padding:15px 32px;border-radius:12px;font-size:1rem;font-weight:600;color:#fff;background:transparent;border:1.5px solid rgba(255,255,255,.3);cursor:pointer;transition:all .2s var(--ease)}
.btn-cta-o:hover{background:rgba(255,255,255,.1);transform:translateY(-2px)}

/* FOOTER */
.lpfoot{background:var(--navy);padding:64px 0 32px}
.foot-top{display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr;gap:40px;margin-bottom:56px}
.foot-brand{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.foot-brand img{width:32px;height:32px;border-radius:8px}
.foot-brand span{font-size:1.1rem;font-weight:800;color:#fff;letter-spacing:-.02em}
.foot-desc{font-size:.85rem;color:rgba(255,255,255,.4);line-height:1.7;max-width:240px;margin-bottom:24px}
.foot-social{display:flex;gap:10px}
.foot-social a{width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);font-size:.85rem;transition:all .2s}
.foot-social a:hover{background:var(--blue);color:#fff;border-color:var(--blue)}
.foot-col-title{font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-bottom:16px}
.foot-links{list-style:none;display:flex;flex-direction:column;gap:10px}
.foot-links li a{font-size:.875rem;color:rgba(255,255,255,.5);transition:color .18s}
.foot-links li a:hover{color:#fff}
.foot-bottom{border-top:1px solid rgba(255,255,255,.08);padding-top:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.foot-copy{font-size:.8rem;color:rgba(255,255,255,.3)}
.foot-legal{display:flex;gap:20px}
.foot-legal a{font-size:.8rem;color:rgba(255,255,255,.3);transition:color .18s}
.foot-legal a:hover{color:rgba(255,255,255,.7)}

@media(max-width:991px){.nav-links{display:none}.hero-visual{display:none}.stat-grid{grid-template-columns:repeat(3,1fr)}.foot-top{grid-template-columns:1fr 1fr}.steps::before{left:22px}.stepnum{width:46px;height:46px;font-size:.95rem}}
@media(max-width:767px){.hero-ctas{flex-direction:column}.stat-grid{grid-template-columns:repeat(2,1fr)}.fcta-btns{flex-direction:column;align-items:center}.foot-top{grid-template-columns:1fr}.foot-bottom{flex-direction:column;text-align:center}}
</style>
</head>
<body>

<!-- NAV -->
<nav class="lpnav" id="lpNav">
  <a class="nav-brand" href="#"><img src="{{ asset('images/gradnet-logo.png') }}" alt="GradNet" onerror="this.style.display='none'" style="display:block;"><span style="white-space:nowrap">Grad<b style="color:var(--gold);font-weight:inherit">Net</b></span></a>
  <div class="nav-links">
    <a href="#features">Features</a>
    <a href="#how-it-works">How It Works</a>
    <a href="#for-schools">For Schools</a>
    <a href="#security">Security</a>
  </div>
  <div class="nav-actions">
    <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
    <a href="{{ route('login') }}" class="btn-solid">Try Demo</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-grid"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 hero-content">
        <div class="hero-badge"><div class="hero-badge-dot"></div> The #1 Alumni Networking Platform</div>
        <h1 class="hero-headline">Where Graduates<br>Stay <em>Connected</em><br>Beyond Graduation</h1>
        <p class="hero-sub">Reconnect with classmates, expand your professional network, join alumni events, and stay engaged with your school through one unified platform.</p>
        <div class="hero-ctas">
          <a href="{{ route('login') }}" class="btn-gold"><i class="fas fa-eye"></i> Try Demo Now</a>
          <a href="#features" class="btn-outline-w"><i class="fas fa-play-circle"></i> Explore Features</a>
        </div>
        <div class="hero-trust">
          <div class="trust-avatars">
            @php $avc=['#1e4db7','#003087','#c4972f','#1a7f4b','#7c3aed'];$avi=['PA','KH','CE','EA','JM']; @endphp
            @foreach($avi as $i=>$init)
            <div class="trust-av" style="background:{{$avc[$i]}};z-index:{{5-$i}};">{{$init}}</div>
            @endforeach
          </div>
          <p><strong>{{ number_format($stats['alumni']) }}+</strong> alumni already connected</p>
        </div>
      </div>
      <div class="col-lg-6 hero-visual">
        <div style="position:relative;padding:20px 60px 20px 20px;">
          <div class="mockup-browser">
            <div class="browser-bar">
              <div class="bdots"><span></span><span></span><span></span></div>
              <div class="burl">gradnet.ph</div>
            </div>
            <div class="mbody">
              <div class="msidebar">
                <div class="msi active"><i class="fas fa-home"></i></div>
                <div class="msi"><i class="fas fa-users"></i></div>
                <div class="msi"><i class="fas fa-calendar"></i></div>
                <div class="msi"><i class="fas fa-newspaper"></i></div>
                <div class="msi"><i class="fas fa-comment"></i></div>
              </div>
              <div class="mmain">
                <div class="mtopbar"><div class="msearch"></div><div class="mav"></div></div>
                @foreach([['linear-gradient(135deg,#003087,#1e4db7)',true],['linear-gradient(135deg,#c4972f,#e8b84b)',false],['linear-gradient(135deg,#1a7f4b,#2ea86a)',false]] as $mp)
                <div class="mpost">
                  <div class="mph"><div class="mpav" style="background:{{$mp[0]}}"></div><div class="mpname"></div><div class="mpt"></div></div>
                  <div class="mpline"></div><div class="mpline short"></div>
                  @if($mp[1])<div class="mpimg"></div>@endif
                  <div class="mpact"><div class="mpill"></div><div class="mpill"></div></div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="float-card fc1">
            <div class="fi b"><i class="fas fa-user-check"></i></div>
            <div><div class="fc-label">Connections</div><div class="fc-value">{{ $stats['connections'] }}+ alumni</div></div>
          </div>
          <div class="float-card fc2">
            <div class="fi g"><i class="fas fa-calendar-check"></i></div>
            <div><div class="fc-label">Upcoming Events</div><div class="fc-value">{{ $stats['events'] }} events</div></div>
          </div>
          <div class="float-card fc3">
            <div class="fi gr"><i class="fas fa-newspaper"></i></div>
            <div><div class="fc-label">Community Posts</div><div class="fc-value">{{ $stats['posts'] }}+ updates</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- WHY -->
<section class="why-sec">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-7 reveal">
        <div class="eyebrow" style="justify-content:center;">Why GradNet Exists</div>
        <h2 class="sec-title">The <em>Problem</em> We're Solving</h2>
        <p class="sec-sub mx-auto">Most alumni lose contact after graduation. Schools struggle to engage graduates. Career opportunities become disconnected from alumni communities.</p>
      </div>
    </div>
    <div class="row g-4 mb-4">
      @php $probs=[['fas fa-unlink','Alumni Lose Touch','After graduation, 80% of alumni lose contact with classmates within 2 years. Friendships and networks fade without a central platform.'],['fas fa-school','Schools Can\'t Engage','Institutions have no efficient way to communicate with graduates, track alumni achievements, or build lasting community beyond graduation day.'],['fas fa-briefcase','Opportunities Get Lost','Job openings, mentorship opportunities, and professional referrals that could benefit alumni communities never reach the right people.']]; @endphp
      @foreach($probs as $i=>$p)
      <div class="col-md-4 reveal d{{$i+1}}">
        <div class="problem-card">
          <div class="p-icon"><i class="{{$p[0]}}"></i></div>
          <div class="p-title">{{$p[1]}}</div>
          <div class="p-desc">{{$p[2]}}</div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="why-divider reveal"><i class="fas fa-arrow-down me-2"></i> GradNet solves all of this</div>
    <div class="row g-4">
      @php $sols=[['fas fa-users','One Unified Network','Every graduate in a single, searchable alumni directory. Find batchmates, connect with professionals, and rebuild relationships that matter.'],['fas fa-bullhorn','Direct School Channel','Schools post news, announcements, and events that reach every alumnus instantly. Two-way communication that keeps the community alive.'],['fas fa-handshake','Career Opportunity Hub','Alumni share job openings, offer mentorship, and build professional relationships that turn a school\'s network into a powerful career engine.']]; @endphp
      @foreach($sols as $i=>$s)
      <div class="col-md-4 reveal d{{$i+1}}">
        <div class="solution-card">
          <div class="s-icon"><i class="{{$s[0]}}"></i></div>
          <div class="s-title">{{$s[1]}}</div>
          <div class="s-desc">{{$s[2]}}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="feat-sec" id="features">
  <div class="container">
    <div class="row mb-5">
      <div class="col-lg-6 reveal">
        <div class="eyebrow">Platform Features</div>
        <h2 class="sec-title">Everything Your Alumni<br>Community <em>Needs</em></h2>
        <p class="sec-sub">A complete suite of tools built specifically for alumni engagement — from networking and events to news, messaging, and analytics.</p>
      </div>
    </div>
    <div class="feat-grid">
      @php $feats=[['fas fa-address-book','Alumni Directory','Search and filter the complete alumni directory by program, year, location, and employment status.'],['fas fa-network-wired','Professional Networking','Send connection requests, build your professional network, and stay in touch with fellow graduates.'],['fas fa-calendar-star','Events & Reunions','Discover and RSVP to homecoming events, career fairs, medical missions, and alumni reunions.'],['fas fa-rss','Alumni News Feed','A social feed where alumni share updates, milestones, photos, and professional achievements.'],['fas fa-comments','Direct Messaging','Private conversations between alumni — communicate without leaving the platform.'],['fas fa-users-rectangle','Groups & Communities','Join program-based or interest-based groups to connect with like-minded alumni.'],['fas fa-newspaper','School Announcements','Get the latest news and updates directly from your alma mater.'],['fas fa-bell','Smart Notifications','Real-time alerts for connections, messages, event reminders, and new school posts.'],['fas fa-shield-halved','Admin Dashboard','Powerful tools for administrators to manage users, approve registrations, and moderate content.'],['fas fa-chart-line','Analytics & Reporting','Deep insights into alumni engagement, event attendance, and community growth over time.']]; @endphp
      @foreach($feats as $i=>$f)
      <div class="feat-card reveal d{{min($i%6+1,6)}}">
        <div class="feat-icon"><i class="{{$f[0]}}"></i></div>
        <div class="feat-title">{{$f[1]}}</div>
        <div class="feat-desc">{{$f[2]}}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- PRODUCT TOUR -->
<section class="tour-sec">
  <div class="container">
    <div class="row mb-5">
      <div class="col-lg-6 reveal">
        <div class="eyebrow">Interactive Preview</div>
        <h2 class="sec-title">See the Platform<br>In <em style="color:var(--gold)">Action</em></h2>
        <p class="sec-sub">Explore GradNet's key screens — the same interface your alumni community will use every day.</p>
      </div>
    </div>
    <div class="tour-tabs reveal">
      <button class="ttab active" onclick="showTour('feed',this)">Alumni Feed</button>
      <button class="ttab" onclick="showTour('events',this)">Events</button>
      <button class="ttab" onclick="showTour('directory',this)">Directory</button>
      <button class="ttab" onclick="showTour('news',this)">News</button>
      <button class="ttab" onclick="showTour('messages',this)">Messages</button>
    </div>
    <div class="reveal d2">

      <!-- FEED PANEL -->
      <div class="tpanel active" id="tour-feed">
        <div class="tmock">
          <div class="tbar"><div class="bdots"><span></span><span></span><span></span></div><div class="turl">gradnet.ph/feed</div></div>
          <div class="tscreen">
            <div class="tside">
              <div class="tb">GradNet</div>
              <div class="tni active"><div class="tnd" style="background:#003087"></div>Feed</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Connections</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Events</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>News</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Messages</div>
            </div>
            <div class="tmain">
              <div class="tth"><div class="ttsearch"></div><div class="ttav"></div></div>
              <div class="tpost">
                <div class="tph"><div class="tpa" style="background:linear-gradient(135deg,#003087,#1e4db7)"></div><div><div class="tpname">Maria Santos · BSN 2019</div><div class="tptime">2 hours ago</div></div></div>
                <div class="tptext">Just passed the board exam! 🎉 Four years of hard work finally paid off. Shoutout to my batchmates who kept me motivated. GradNet forever! 💙</div>
                <div class="tpimg"><img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&h=120&fit=crop&auto=format" alt="" loading="lazy"></div>
                <div class="tpacts"><span class="tpact">❤️ 48 Loves</span><span class="tpact">💬 12 Comments</span></div>
              </div>
              <div class="tpost">
                <div class="tph"><div class="tpa" style="background:linear-gradient(135deg,#c4972f,#e8b84b)"></div><div><div class="tpname">Carlo Reyes · BSBA 2020</div><div class="tptime">5 hours ago</div></div></div>
                <div class="tptext">Happy to share I just got promoted to Senior Manager! Thank you everyone who believed in me. 🏆</div>
                <div class="tpacts"><span class="tpact">❤️ 31 Loves</span><span class="tpact">💬 7 Comments</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- EVENTS PANEL -->
      <div class="tpanel" id="tour-events">
        <div class="tmock">
          <div class="tbar"><div class="bdots"><span></span><span></span><span></span></div><div class="turl">gradnet.ph</div></div>
          <div class="tscreen">
            <div class="tside">
              <div class="tb">GradNet</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Feed</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Connections</div>
              <div class="tni active"><div class="tnd" style="background:#003087"></div>Events</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>News</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Messages</div>
            </div>
            <div class="tmain">
              <div class="tth"><div class="ttsearch"></div><div class="ttav"></div></div>
              <div style="font-size:.85rem;font-weight:700;color:var(--td);margin-bottom:10px;">📅 Upcoming Events</div>
              <div class="tevent"><div class="tei"><img src="https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=80&h=120&fit=crop&auto=format" alt="" loading="lazy"></div><div class="teb"><div class="tet">Alumni Homecoming & Grand Reunion 2025</div><div class="tem">📍 School Gymnasium · Jun 26</div><div style="margin-top:6px;"><span style="background:#003087;color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:100px;">RSVP Going</span></div></div></div>
              <div class="tevent"><div class="tei"><img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=80&h=120&fit=crop&auto=format" alt="" loading="lazy"></div><div class="teb"><div class="tet">Tech Talk: AI & Machine Learning</div><div class="tem">📍 CS Building Room 301 · Jun 19</div><div style="margin-top:6px;"><span style="background:#e8eef8;color:#003087;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:100px;">RSVP Now</span></div></div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- DIRECTORY PANEL -->
      <div class="tpanel" id="tour-directory">
        <div class="tmock">
          <div class="tbar"><div class="bdots"><span></span><span></span><span></span></div><div class="turl">gradnet.ph</div></div>
          <div class="tscreen">
            <div class="tside">
              <div class="tb">GradNet</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Feed</div>
              <div class="tni active"><div class="tnd" style="background:#003087"></div>Connections</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Events</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>News</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Messages</div>
            </div>
            <div class="tmain">
              <div class="tth"><div class="ttsearch"></div><div class="ttav"></div></div>
              <div style="font-size:.85rem;font-weight:700;color:var(--td);margin-bottom:10px;">👥 Alumni Directory</div>
              <div class="talgrid">
                @php $mockAl=[['#003087','MS','Maria Santos','BSN 2019'],['#c4972f','KB','Kian Bahia','BSCS 2022'],['#1a7f4b','CA','Christian Avena','BSCS 2022'],['#7c3aed','EA','Errol Alday','BSCS 2022'],['#dc2626','JM','John Marata','BSCS 2022'],['#0891b2','KE','Ken Estillo','BSCS 2022']]; @endphp
                @foreach($mockAl as $a)
                <div class="talcard"><div class="talav" style="background:{{$a[0]}}">{{$a[1]}}</div><div class="talname">{{$a[2]}}</div><div class="talprog">{{$a[3]}}</div></div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- NEWS PANEL -->
      <div class="tpanel" id="tour-news">
        <div class="tmock">
          <div class="tbar"><div class="bdots"><span></span><span></span><span></span></div><div class="turl">gradnet.ph</div></div>
          <div class="tscreen">
            <div class="tside">
              <div class="tb">GradNet</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Feed</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Connections</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Events</div>
              <div class="tni active"><div class="tnd" style="background:#003087"></div>News</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Messages</div>
            </div>
            <div class="tmain">
              <div class="tth"><div class="ttsearch"></div><div class="ttav"></div></div>
              <div style="font-size:.85rem;font-weight:700;color:var(--td);margin-bottom:10px;">📰 Latest News</div>
              <div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e2e6f0;margin-bottom:10px;"><img src="https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=500&h=90&fit=crop&auto=format" alt="" loading="lazy" style="width:100%;height:80px;object-fit:cover;"><div style="padding:10px;"><div style="font-size:.8rem;font-weight:700;color:var(--td);margin-bottom:4px;">GradNet Alumni Homecoming 2025 — A Night to Remember</div><div style="font-size:.7rem;color:var(--tm);">June 7, 2026</div></div></div>
              <div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e2e6f0;"><img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500&h=70&fit=crop&auto=format" alt="" loading="lazy" style="width:100%;height:65px;object-fit:cover;"><div style="padding:10px;"><div style="font-size:.8rem;font-weight:700;color:var(--td);margin-bottom:4px;">Partners with Leading Hospitals for Nursing Internship</div><div style="font-size:.7rem;color:var(--tm);">June 2, 2026</div></div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- MESSAGES PANEL -->
      <div class="tpanel" id="tour-messages">
        <div class="tmock">
          <div class="tbar"><div class="bdots"><span></span><span></span><span></span></div><div class="turl">gradnet.ph</div></div>
          <div class="tscreen">
            <div class="tside">
              <div class="tb">GradNet</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Feed</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Connections</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>Events</div>
              <div class="tni"><div class="tnd" style="background:#65676b"></div>News</div>
              <div class="tni active"><div class="tnd" style="background:#003087"></div>Messages</div>
            </div>
            <div class="tmain" style="padding:0;display:flex;">
              <div style="width:150px;border-right:1px solid #e2e6f0;padding:12px 8px;overflow:hidden;">
                <div style="font-size:.72rem;font-weight:700;color:var(--td);margin-bottom:10px;padding:0 4px;">Messages</div>
                @php $convos=[['#003087','KH','Kian Bahia','Bro are you going?','2h'],['#c4972f','CA','Christian Avena','See you tonight!','5h'],['#1a7f4b','EA','Errol Alday','Thanks man!','1d']]; @endphp
                @foreach($convos as $c)
                <div style="display:flex;align-items:center;gap:8px;padding:7px 6px;border-radius:8px;margin-bottom:2px;{{$loop->first?'background:#e8eef8':''}}">
                  <div style="width:28px;height:28px;border-radius:50%;background:{{$c[0]}};flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:800;color:#fff;">{{$c[1]}}</div>
                  <div style="min-width:0;"><div style="font-size:.7rem;font-weight:700;color:var(--td);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{$c[2]}}</div><div style="font-size:.65rem;color:var(--tm);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{$c[3]}}</div></div>
                </div>
                @endforeach
              </div>
              <div style="flex:1;display:flex;flex-direction:column;padding:12px;">
                <div style="font-size:.75rem;font-weight:700;color:var(--td);text-align:center;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #e2e6f0;">Kian Bahia</div>
                <div style="flex:1;display:flex;flex-direction:column;gap:8px;justify-content:flex-end;">
                  <div style="align-self:flex-start;background:#f0f2f7;border-radius:12px 12px 12px 4px;padding:8px 12px;font-size:.72rem;color:var(--tb);max-width:80%;">Bro are you going to the homecoming? 🎉</div>
                  <div style="align-self:flex-end;background:#003087;border-radius:12px 12px 4px 12px;padding:8px 12px;font-size:.72rem;color:#fff;max-width:80%;">Of course! Already RSVP'd. 💙</div>
                  <div style="align-self:flex-start;background:#f0f2f7;border-radius:12px 12px 12px 4px;padding:8px 12px;font-size:.72rem;color:var(--tb);max-width:80%;">Let's coordinate with the gang! 🤙</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FOR ALUMNI -->
<section class="ben-sec" id="for-alumni">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 rl">
        <div class="eyebrow">For Alumni</div>
        <h2 class="sec-title">Your Career Starts<br>with Your <em>Network</em></h2>
        <p class="sec-sub">GradNet gives every graduate the tools to stay connected, grow professionally, and give back to the community that shaped them.</p>
        <ul class="ben-list">
          @php $ab=[['Reconnect With Classmates','Find batchmates from any year or program through the alumni directory.'],['Expand Your Professional Network','Build connections with alumni across industries and regions.'],['Discover Career Opportunities','Access job postings and referrals shared within the alumni community.'],['Stay Informed','Get school news, announcements, and event updates directly in your feed.'],['Join Alumni Events','RSVP to homecomings, reunions, career fairs, and community outreach.']]; @endphp
          @foreach($ab as $b)
          <li class="ben-item"><div class="ben-check"><i class="fas fa-check"></i></div><div class="ben-text"><strong>{{$b[0]}}</strong><span>{{$b[1]}}</span></div></li>
          @endforeach
        </ul>
        <div class="mt-4"><a href="{{ route('login') }}" class="btn-gold" style="display:inline-flex;"><i class="fas fa-eye"></i> Try Demo</a></div>
      </div>
      <div class="col-lg-6 rr">
        <div class="bv">
          <div><div class="bvnum">{{ number_format($stats['alumni']) }}+</div><div class="bvlabel">Alumni registered on GradNet</div></div>
          <div class="bvdiv"></div>
          <div><div class="bvnum">{{ number_format($stats['connections']) }}+</div><div class="bvlabel">Professional connections made</div></div>
          <div class="bvdiv"></div>
          <div><div class="bvnum">{{ $stats['events'] }}</div><div class="bvlabel">Alumni events organized</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOR SCHOOLS -->
<section class="ben-sec" id="for-schools" style="background:var(--g50);">
  <div class="container">
    <div class="row align-items-center g-5 flex-lg-row-reverse">
      <div class="col-lg-6 rr">
        <div class="eyebrow">For Schools</div>
        <h2 class="sec-title">Build a Lasting<br>Alumni <em>Community</em></h2>
        <p class="sec-sub">Give your institution the tools to engage graduates long after graduation — from event management to analytics that show real impact.</p>
        <ul class="ben-list">
          @php $sb=[['Alumni Engagement Tools','Keep graduates connected to your institution with a dedicated platform.'],['Event Management','Create and promote events, track RSVPs, and measure attendance.'],['Direct Communication','Publish news and announcements that reach every alumnus instantly.'],['Graduate Tracking','Monitor alumni achievements, employment outcomes, and program success.'],['Community Building','Foster a thriving community of graduates who support each other.'],['Admin Analytics','Detailed reports on user growth, engagement, and platform activity.']]; @endphp
          @foreach($sb as $b)
          <li class="ben-item"><div class="ben-check" style="background:#e6f4ed;color:#1a7f4b;"><i class="fas fa-check"></i></div><div class="ben-text"><strong>{{$b[0]}}</strong><span>{{$b[1]}}</span></div></li>
          @endforeach
        </ul>
        <div class="mt-4"><a href="{{ route('login') }}" class="btn-gold" style="display:inline-flex;background:var(--blue);color:#fff;box-shadow:0 4px 16px rgba(0,48,135,.4);"><i class="fas fa-school"></i> Try Demo</a></div>
      </div>
      <div class="col-lg-6 rl">
        <div class="bv" style="background:linear-gradient(145deg,#0f2744,#1a3a6e);">
          <div style="font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:20px;">Admin Dashboard</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            @php $adst=[['fas fa-users','Total Alumni',number_format($stats['alumni'])],['fas fa-edit','Posts Published',$stats['posts'].'+'],['fas fa-calendar','Events Created',$stats['events']],['fas fa-newspaper','News Articles',$stats['news']]]; @endphp
            @foreach($adst as $a)
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:16px;">
              <i class="{{$a[0]}}" style="color:var(--gold);font-size:1.2rem;margin-bottom:8px;display:block;"></i>
              <div style="font-size:1.6rem;font-weight:900;color:#fff;letter-spacing:-.03em;">{{$a[2]}}</div>
              <div style="font-size:.72rem;color:rgba(255,255,255,.45);margin-top:2px;">{{$a[1]}}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-sec" id="how-it-works">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-6 reveal">
        <div class="eyebrow" style="justify-content:center;">Simple Onboarding</div>
        <h2 class="sec-title">Up and Running<br>in <em>Minutes</em></h2>
        <p class="sec-sub mx-auto">Getting started with GradNet is simple. Follow these five steps and you'll be connected with your alumni community right away.</p>
      </div>
    </div>
    <div class="row"><div class="col-lg-6 mx-auto">
      <div class="steps">
        @php $steps=[['01','fas fa-user-edit','Create Your Profile','Sign up with your email, fill in your graduation details, program, and add a profile photo. Your alumni identity starts here.'],['02','fas fa-search','Find Your Alumni','Search the directory by program, graduation year, or name to find batchmates and colleagues you want to reconnect with.'],['03','fas fa-user-plus','Connect & Network','Send connection requests and build your professional alumni network. Every connection opens new opportunities.'],['04','fas fa-calendar-check','Join Events & Communities','RSVP to upcoming alumni events, join interest-based groups, and participate in reunions and professional summits.'],['05','fas fa-rocket','Grow Your Opportunities','Share achievements, discover job openings, offer mentorship, and help shape the next generation of graduates.']]; @endphp
        @foreach($steps as $i=>$s)
        <div class="step reveal d{{$i+1}}">
          <div class="stepnum">{{$s[0]}}</div>
          <div class="stepbody">
            <div class="steptitle"><i class="{{$s[1]}} me-2" style="color:var(--gold);font-size:.9rem;"></i>{{$s[2]}}</div>
            <div class="stepdesc">{{$s[3]}}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div></div>
  </div>
</section>

<!-- SECURITY -->
<section class="sec-sec" id="security">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-5 reveal">
        <div class="eyebrow" style="justify-content:center;">Security & Privacy</div>
        <h2 class="sec-title">Your Data is<br><em>Protected</em></h2>
        <p class="sec-sub mx-auto">GradNet is built with security-first architecture. Every piece of alumni data is handled with care and protected by modern security standards.</p>
      </div>
    </div>
    <div class="secgrid reveal">
      @php $secs=[['fas fa-lock','Secure Authentication','Encrypted login with session management and brute-force protection.'],['fas fa-shield-alt','Protected User Data','All sensitive data encrypted at rest and in transit using HTTPS.'],['fas fa-user-shield','Role-Based Access','Granular permissions — alumni, administrators, and moderators.'],['fas fa-eye-slash','Privacy Controls','Graduates control what information is visible on their profile.'],['fas fa-server','Trusted Infrastructure','Reliable hosting with regular backups and uptime monitoring.']]; @endphp
      @foreach($secs as $s)
      <div class="seccard"><div class="secicon"><i class="{{$s[0]}}"></i></div><div class="sectitle">{{$s[1]}}</div><div class="secdesc">{{$s[2]}}</div></div>
      @endforeach
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="fcta">
  <div class="container">
    <h2 class="fcta-title">See GradNet<br><em>In Action</em> — Try the Demo</h2>
    <p class="fcta-sub">Browse the full platform as a real alumni user. No signup required — just click and explore everything GradNet has to offer.</p>
    <div class="fcta-btns">
      <a href="{{ route('login') }}" class="btn-cta-g"><i class="fas fa-eye"></i> Try Demo Now</a>
      <a href="{{ route('login') }}" class="btn-cta-o"><i class="fas fa-sign-in-alt"></i> Sign In</a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="lpfoot">
  <div class="container">
    <div class="foot-top">
      <div>
        <div class="foot-brand"><img src="{{ asset('images/gradnet-logo.png') }}" alt="GradNet" onerror="this.style.display='none'" style="display:block;"><span style="white-space:nowrap">Grad<b style="color:var(--gold);font-weight:inherit">Net</b></span></div>
        <p class="foot-desc">The unified alumni networking platform that keeps graduates connected, informed, and growing — long after graduation day.</p>
        <div class="foot-social">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div>
        <div class="foot-col-title">Platform</div>
        <ul class="foot-links"><li><a href="#features">Features</a></li><li><a href="#for-alumni">For Alumni</a></li><li><a href="#for-schools">For Schools</a></li><li><a href="#how-it-works">How It Works</a></li><li><a href="#security">Security</a></li></ul>
      </div>
      <div>
        <div class="foot-col-title">Community</div>
        <ul class="foot-links"><li><a href="{{ route('login') }}">Alumni Feed</a></li><li><a href="{{ route('login') }}">Events</a></li><li><a href="{{ route('login') }}">News</a></li><li><a href="{{ route('login') }}">Groups</a></li><li><a href="{{ route('login') }}">Careers</a></li></ul>
      </div>
      <div>
        <div class="foot-col-title">Account</div>
        <ul class="foot-links"><li><a href="{{ route('login') }}">View Live Demo</a></li><li><a href="{{ route('login') }}">Sign In</a></li><li><a href="{{ route('password.request') }}">Forgot Password</a></li><li><a href="#">Contact Us</a></li></ul>
      </div>
    </div>
    <div class="foot-bottom">
      <div>
        <div class="foot-copy">© {{ date('Y') }} GradNet. All rights reserved.</div>
        <div style="font-size:.75rem;color:rgba(255,255,255,.22);margin-top:4px;">
          Designed &amp; developed by <span style="color:rgba(255,255,255,.45);font-weight:600;">Prince Arvee Avena</span> · BSCS 2022
        </div>
      </div>
      <div class="foot-legal"><a href="#">Privacy Policy</a><a href="#">Terms of Service</a><a href="#">Cookie Policy</a></div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const nav=document.getElementById('lpNav');
window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',scrollY>40),{passive:true});

const ro=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');ro.unobserve(e.target)}})},{threshold:.1,rootMargin:'0px 0px -40px 0px'});
document.querySelectorAll('.reveal,.rl,.rr').forEach(el=>ro.observe(el));

function animateCounter(el,target,dur=1800){
  let start=null;
  const ease=t=>t<.5?2*t*t:-1+(4-2*t)*t;
  function step(ts){if(!start)start=ts;const p=Math.min((ts-start)/dur,1);el.textContent=Math.floor(ease(p)*target).toLocaleString();if(p<1)requestAnimationFrame(step);else el.textContent=target.toLocaleString()}
  requestAnimationFrame(step);
}
const co=new IntersectionObserver(es=>{es.forEach(e=>{if(!e.isIntersecting)return;const t=parseInt(e.target.closest('[data-target]')?.dataset.target||'0');animateCounter(e.target,t);co.unobserve(e.target)})},{threshold:.5});
document.querySelectorAll('.counter').forEach(el=>co.observe(el));

function showTour(id,btn){
  document.querySelectorAll('.tpanel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.ttab').forEach(t=>t.classList.remove('active'));
  document.getElementById('tour-'+id).classList.add('active');
  btn.classList.add('active');
}

document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{
    const t=document.querySelector(a.getAttribute('href'));
    if(t){e.preventDefault();window.scrollTo({top:t.getBoundingClientRect().top+scrollY-80,behavior:'smooth'})}
  });
});
</script>
</body>
</html>
