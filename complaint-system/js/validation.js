/* Client-side validation (mirrors server rules — server is authoritative). */
(function () {
  'use strict';

  function mark(input, ok) {
    input.classList.toggle('invalid', !ok);
    return ok;
  }
  function err(input, msg) {
    var label = input.closest('label.field');
    if (!label) return;
    var old = label.querySelector('.js-error');
    if (old) old.remove();
    if (msg) {
      var em = document.createElement('em');
      em.className = 'f-error js-error';
      em.textContent = msg;
      label.appendChild(em);
    }
  }
  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // ---- Login ----
  var login = document.getElementById('loginForm');
  if (login) {
    login.addEventListener('submit', function (e) {
      var email = login.querySelector('[name=email]');
      var pass = login.querySelector('[name=password]');
      var ok = true;
      err(email, ''); err(pass, '');
      if (!emailRe.test(email.value.trim())) { mark(email, false); err(email, 'Enter a valid email address.'); ok = false; } else mark(email, true);
      if (pass.value === '') { mark(pass, false); err(pass, 'Password is required.'); ok = false; } else mark(pass, true);
      if (!ok) { e.preventDefault(); window.showToast && window.showToast('Please fix the highlighted fields.', 'error'); }
    });
  }

  // ---- Register ----
  var reg = document.getElementById('registerForm');
  if (reg) {
    var name = reg.querySelector('[name=name]');
    var email = reg.querySelector('[name=email]');
    var p1 = reg.querySelector('[name=password]');
    var p2 = reg.querySelector('[name=confirm_password]');
    // Live password-match hint
    function checkMatch() {
      if (!p1.value || !p2.value) { err(p2, ''); return; }
      if (p1.value !== p2.value) { mark(p2, false); err(p2, 'Passwords do not match yet.'); }
      else { mark(p2, true); err(p2, ''); }
    }
    p1.addEventListener('input', checkMatch);
    p2.addEventListener('input', checkMatch);

    reg.addEventListener('submit', function (e) {
      var ok = true;
      [name, email, p1, p2].forEach(function (i) { err(i, ''); });
      if (name.value.trim().length < 2) { mark(name, false); err(name, 'Enter your full name (min 2 characters).'); ok = false; } else mark(name, true);
      if (!emailRe.test(email.value.trim())) { mark(email, false); err(email, 'Enter a valid email address.'); ok = false; } else mark(email, true);
      if (p1.value.length < 8) { mark(p1, false); err(p1, 'Password must be at least 8 characters.'); ok = false; }
      else if (!/[A-Za-z]/.test(p1.value) || !/\d/.test(p1.value)) { mark(p1, false); err(p1, 'Include at least one letter and one number.'); ok = false; }
      else mark(p1, true);
      if (p2.value === '') { mark(p2, false); err(p2, 'Please confirm your password.'); ok = false; }
      else if (p1.value !== p2.value) { mark(p2, false); err(p2, 'Passwords do not match.'); ok = false; } else mark(p2, true);
      if (!ok) { e.preventDefault(); window.showToast && window.showToast('Please fix the highlighted fields.', 'error'); }
    });
  }

  // ---- Complaint form ----
  var cf = document.getElementById('complaintForm');
  if (cf) {
    cf.addEventListener('submit', function (e) {
      var cat = cf.querySelector('[name=category]');
      var subj = cf.querySelector('[name=subject]');
      var desc = cf.querySelector('[name=description]');
      var ok = true;
      [cat, subj, desc].forEach(function (i) { err(i, ''); });
      if (!cat.value) { mark(cat, false); err(cat, 'Choose a category.'); ok = false; } else mark(cat, true);
      if (subj.value.trim().length < 5) { mark(subj, false); err(subj, 'Subject needs at least 5 characters.'); ok = false; } else mark(subj, true);
      if (desc.value.trim().length < 10) { mark(desc, false); err(desc, 'Description needs at least 10 characters.'); ok = false; } else mark(desc, true);
      if (!ok) { e.preventDefault(); window.showToast && window.showToast('Please complete the required fields.', 'error'); }
    });
  }

  // ---- Status form (admin) ----
  var sf = document.getElementById('statusForm');
  if (sf) {
    sf.addEventListener('submit', function (e) {
      var st = sf.querySelector('[name=status]');
      if (!st.value) { e.preventDefault(); window.showToast && window.showToast('Select a status.', 'error'); }
    });
  }
})();
