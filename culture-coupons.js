(() => {
  const dialog = document.getElementById('couponDialog');
  if (!dialog) return;

  document.querySelectorAll('[data-coupon-demo]').forEach((coupon) => {
    coupon.addEventListener('click', () => {
      if (typeof dialog.showModal === 'function') dialog.showModal();
      else dialog.setAttribute('open', '');
    });
  });

  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close();
  });
})();
