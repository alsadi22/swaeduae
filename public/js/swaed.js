document.addEventListener('DOMContentLoaded', () => {
  const nav = document.querySelector('.navbar');
  if(!nav) return;
  let last=0;
  window.addEventListener('scroll', () => {
    const y = window.scrollY || 0;
    nav.classList.toggle('shadow-sm', y>2);
    last = y;
  });
});
