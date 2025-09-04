(function () {
  // KPI count-up
  const animate = el => {
    const target = parseInt(el.getAttribute('data-count') || '0', 10);
    if (isNaN(target)) return;
    const dur = 800; const s = performance.now();
    const step = t => { const p = Math.min(1, (t - s)/dur); el.textContent = Math.floor(target*p).toString(); if(p<1) requestAnimationFrame(step); };
    requestAnimationFrame(step);
  };
  document.querySelectorAll('.kpi-value[data-count]').forEach(animate);
})();
