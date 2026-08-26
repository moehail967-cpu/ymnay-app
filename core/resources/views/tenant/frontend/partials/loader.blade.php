<style>
.loader{display:none;z-index:999999}
.loader.loader_page_single{position:fixed;inset:0;width:100%;height:100%;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
.loader-01{position:fixed;top:40%;left:0%;transform:translate(-50%,-50%);display:flex;flex-direction:column;align-items:center;gap:18px;z-index:1}
.loader-spinner-track{position:relative;width:70px;height:70px}
.loader-spinner-ring{position:absolute;inset:0;border-radius:50%;border:3px solid rgba(0,0,0,.07);border-top-color:var(--main-color-one,#333);animation:loaderSpinCW .9s cubic-bezier(.55,.15,.45,.85) infinite}
.loader-spinner-ring-inner{position:absolute;inset:12px;border-radius:50%;border:2px solid rgba(0,0,0,.05);border-bottom-color:var(--main-color-one,#333);opacity:.75;animation:loaderSpinCCW .65s cubic-bezier(.55,.15,.45,.85) infinite}
.loader-spinner-core{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:9px;height:9px;background:var(--main-color-one,#333);border-radius:50%;animation:loaderCorePulse 1.8s ease-in-out infinite}
.loader-dots{display:flex;gap:7px;align-items:center}
.loader-dot{width:10px;height:10px;background:var(--main-color-one,#333);border-radius:50%;animation:loaderDotBounce 1.2s ease-in-out infinite;opacity:.4}
.loader-dot:nth-child(1){animation-delay:0s}
.loader-dot:nth-child(2){animation-delay:.2s}
.loader-dot:nth-child(3){animation-delay:.4s}
@keyframes loaderSpinCW{to{transform:rotate(360deg)}}
@keyframes loaderSpinCCW{to{transform:rotate(-360deg)}}
@keyframes loaderCorePulse{0%,100%{transform:translate(-50%,-50%) scale(1);opacity:1}50%{transform:translate(-50%,-50%) scale(.35);opacity:.35}}
@keyframes loaderDotBounce{0%,80%,100%{transform:scale(.55);opacity:.35}40%{transform:scale(1);opacity:1}}
</style>
<div class="loader loader_page_single">
    <x-loaders.loader-01/>
</div>
