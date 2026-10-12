/* ResolveDesk app interactions — sidebar, toasts, confirms, password toggles. */
(function () {
  'use strict';

  // ---- Mobile sidebar ----
  var toggle = document.getElementById('sidebarToggle');
  var closeBtn = document.getElementById('sidebarClose');
  var overlay = document.getElementById('sidebarOverlay');
  if (toggle) toggle.addEventListener('click', function () { document.body.classList.add('nav-open'); });
  if (closeBtn) closeBtn.addEventListener('click', function () { document.body.classList.remove('nav-open'); });
  if (overlay) overlay.addEventListener('click', function () { document.body.classList.remove('nav-open'); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') document.body.classList.remove('nav-open'); });

  // ---- Landing mobile menu ----
  var burger = document.getElementById('landingBurger');
  var menu = document.getElementById('landingMenu');
  if (burger && menu) burger.addEventListener('click', function () { menu.classList.toggle('open'); });

  // ---- Password visibility toggles ----
  document.querySelectorAll('.pass-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-target'));
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
      input.focus();
    });
  });

  // ---- Toasts ----
  function toast(message, type) {
    var wrap = document.getElementById('toastWrap');
    if (!wrap) return;
    var el = document.createElement('div');
    el.className = 'toast ' + (type || 'info');
    var icon = type === 'success' ? 'fa-circle-check' : type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info';
    el.innerHTML = '<i class="fa-solid ' + icon + '"></i><span></span>';
    el.querySelector('span').textContent = message;
    wrap.appendChild(el);
    setTimeout(function () { el.classList.add('out'); setTimeout(function () { el.remove(); }, 350); }, 4200);
  }
  window.showToast = toast;
  if (window.__FLASH__ && window.__FLASH__.message) {
    toast(window.__FLASH__.message, window.__FLASH__.type || 'info');
  }

  // ---- Delete / destructive confirms (progressive enhancement) ----
  // Any <form data-confirm="..."> gets a native confirm dialog before submit.
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(msg)) e.preventDefault();
      else {
        var btn = form.querySelector('[type=submit]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Working…'; }
      }
    });
  });

  // ---- Generic submit feedback (prevent double-submit) ----
  document.querySelectorAll('form:not([data-confirm])').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('[type=submit].btn-primary');
      if (btn && form.checkValidity()) {
        btn.dataset.label = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving…';
        setTimeout(function () { btn.disabled = false; btn.innerHTML = btn.dataset.label; }, 8000);
      }
    });
  });
})();
