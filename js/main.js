
// main.js - minimal interactions
document.addEventListener('DOMContentLoaded',function(){
  // Mobile nav toggle (if present)
  const toggle = document.getElementById('nav-toggle');
  if(toggle){
    toggle.addEventListener('click', ()=> {
      const nav = document.getElementById('site-nav');
      nav.style.display = (nav.style.display==='block') ? 'none' : 'block';
    });
  }
});
