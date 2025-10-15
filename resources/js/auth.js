// resources/js/auth.js
document.addEventListener('DOMContentLoaded', () => {
  // ===== 分頁切換（在 user/auth.blade.php 才會出現） =====
  const tabs = document.querySelectorAll('.auth-tab');
  const forms = document.querySelectorAll('.auth-form');
  if (tabs.length && forms.length) {
    const showTab = (tabName) => {
      tabs.forEach(t => t.classList.toggle('active', t.id === `tab-${tabName}`));
      forms.forEach(f => f.classList.toggle('active', f.id === `form-${tabName}`));
    };

    // 點擊切換
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab; // 'login' | 'register'
        showTab(target);
      });
    });

    // 初始：如果 Blade 用 $is_register_active 決定 active，就尊重現有 class；
    // 若都沒有 active，預設顯示 login。
    const hasActive = [...tabs].some(t => t.classList.contains('active')) || [...forms].some(f => f.classList.contains('active'));
    if (!hasActive) showTab('login');
  }

  // ===== 顯示/隱藏密碼 =====
  document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const inputId = btn.dataset.target || 'password';
      const input = document.getElementById(inputId);
      const textSpan = btn.querySelector('.toggle-text') || btn;
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        textSpan.textContent = '隱藏';
      } else {
        input.type = 'password';
        textSpan.textContent = '顯示';
      }
    });
  });

  // ===== 密碼強度 + 再次輸入比對（在註冊頁會存在） =====
  const pwd = document.getElementById('password');
  const pwd2 = document.getElementById('password_confirmation');
  const strengthEl = document.getElementById('password-strength');
  const matchEl = document.getElementById('password-match');

  const updateStrength = (v) => {
    if (!strengthEl) return;
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[a-z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const colors = ['', '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#28a745'];
    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    strengthEl.style.backgroundColor = colors[s] || '#e9ecef';
    strengthEl.style.width = widths[s] || '0%';
  };

  const updateMatch = () => {
    if (!pwd || !pwd2 || !matchEl) return;
    if (!pwd2.value) { matchEl.textContent = ''; matchEl.className = 'password-match'; return; }
    if (pwd.value === pwd2.value) { matchEl.textContent = '✓ 密碼一致'; matchEl.className = 'password-match match'; }
    else { matchEl.textContent = '✗ 密碼不一致'; matchEl.className = 'password-match no-match'; }
  };

  if (pwd) pwd.addEventListener('input', () => { updateStrength(pwd.value); updateMatch(); });
  if (pwd2) pwd2.addEventListener('input', updateMatch);

  // ===== 註冊提交 loading =====
  const regForm = document.getElementById('registerForm');
  if (regForm) {
    regForm.addEventListener('submit', () => {
      const submitBtn = document.getElementById('submit-btn');
      if (!submitBtn) return;
      const btnText = submitBtn.querySelector('.btn-text');
      const btnSpinner = submitBtn.querySelector('.btn-spinner');
      if (btnText) btnText.style.display = 'none';
      if (btnSpinner) btnSpinner.style.display = 'inline';
      submitBtn.disabled = true;
    });
  }

  // ===== 驗證碼刷新 =====
  const refreshBtn = document.getElementById('refresh-btn');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', async () => {
      const original = refreshBtn.innerHTML;
      refreshBtn.innerHTML = '🔄 刷新中...';
      refreshBtn.style.pointerEvents = 'none';
      try {
        // 若需要，你可以在 Blade 上把 route 透過 data-url 綁上去
        const url = refreshBtn.dataset.url || '/captcha/refresh';
        const res = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        const out = document.getElementById('captchaText');
        const input = document.getElementById('captchaInput');
        if (out) out.textContent = data.captcha || '';
        if (input) input.value = '';
      } catch (e) {
        alert('刷新驗證碼失敗，請稍後重試。');
      } finally {
        refreshBtn.innerHTML = original;
        refreshBtn.style.pointerEvents = 'auto';
      }
    });
  }
});
