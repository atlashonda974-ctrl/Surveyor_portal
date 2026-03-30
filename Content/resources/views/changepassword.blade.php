@extends('master')
@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

  /* ─── Scoped to .cp-page so parent theme styles are untouched ─── */
  .cp-page {
    font-family: 'Inter', sans-serif;
    padding: 30px 20px;
  }

  .cp-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    max-width: 1800px;
  }

  .cp-card-header {
    padding: 20px 28px;
    border-bottom: 1px solid #f1f1f1;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .cp-card-header-icon {
    width: 36px;
    height: 36px;
    background: #e8f0ff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .cp-card-header-icon svg {
    width: 18px;
    height: 18px;
    stroke: #0d6dfc;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .cp-card-header h4 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    color: #1a202c;
    letter-spacing: -0.2px;
  }

  .cp-card-body {
    padding: 28px 28px 32px;
  }

  /* ─── Section labels ─── */
  .cp-section-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #a0aec0;
    margin: 0 0 14px;
  }

  .cp-divider {
    border: none;
    border-top: 1px solid #f1f1f1;
    margin: 24px 0;
  }

  /* ─── Fields ─── */
  .cp-field {
    margin-bottom: 18px;
  }

  .cp-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 7px;
  }

  .cp-input-wrap {
    position: relative;
  }

  .cp-input {
    width: 100%;
    padding: 10px 42px 10px 14px;
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    color: #2d3748;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
  }

  .cp-input::placeholder {
    color: #b0bec8;
    font-size: 13px;
  }

  .cp-input:focus {
    border-color: #0d6dfc;
    box-shadow: 0 0 0 3px rgba(13, 109, 252, 0.12);
  }

  .cp-toggle {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #a0aec0;
    display: flex;
    align-items: center;
    transition: color 0.2s;
  }

  .cp-toggle:hover { color: #4a5568; }
  .cp-toggle svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* ─── Strength box ─── */
  .cp-strength-box {
    display: none;
    margin-top: 10px;
    background: #f0f5ff;
    border: 1px solid #d0e2ff;
    border-radius: 8px;
    padding: 14px 16px;
  }

  .cp-strength-title {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #a0aec0;
    margin-bottom: 10px;
  }

  /* Strength bar */
  .cp-bar-wrap {
    display: flex;
    gap: 4px;
    margin-bottom: 14px;
  }

  .cp-bar-seg {
    height: 4px;
    flex: 1;
    border-radius: 2px;
    background: #e2e8f0;
    transition: background 0.3s;
  }

  /* Requirements list */
  .cp-req {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #a0aec0;
    padding: 2px 0;
    transition: color 0.2s;
  }

  .cp-req .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #cbd5e0;
    flex-shrink: 0;
    transition: background 0.25s;
  }

  .cp-req.valid       { color: #0d6dfc; }
  .cp-req.valid .dot  { background: #0d6dfc; box-shadow: 0 0 5px rgba(13,109,252,0.4); }
  .cp-req.invalid     { color: #e53e3e; }
  .cp-req.invalid .dot { background: #e53e3e; }

  /* ─── Submit button — original green #04AA6D ─── */
  .cp-submit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 28px;
    background: #0d6dfc;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    margin-top: 6px;
  }

  .cp-submit:hover {
    background: #0a58d4;
    box-shadow: 0 4px 14px rgba(13,109,252,0.3);
    transform: translateY(-1px);
  }

  .cp-submit:active {
    transform: translateY(0);
    box-shadow: none;
  }

  .cp-submit svg {
    width: 15px;
    height: 15px;
    stroke: #fff;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
  }
</style>

<div class="content-body">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xl-9 col-xxl-12">
        <div class="cp-page">

          <div class="cp-card">
            <!-- Header -->
            <div class="cp-card-header">
              <div class="cp-card-header-icon">
                <svg viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </div>
              <h4>Change Password</h4>
            </div>

            <!-- Body -->
            <div class="cp-card-body">
              <form id="cp-form" method="POST" action="{{ url('changePassword') }}" autocomplete="off" novalidate>
                {!! csrf_field() !!}

                <!-- Current password -->
                <p class="cp-section-label">Current</p>

                <div class="cp-field">
                  <label class="cp-label" for="fcur">Current Password</label>
                  <div class="cp-input-wrap">
                    <input class="cp-input" type="password" id="fcur" name="fcur" placeholder="Enter your current password" required>
                    <button type="button" class="cp-toggle" onclick="cpToggle('fcur', this)" tabindex="-1" title="Show/hide">
                      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                  </div>
                </div>

                <hr class="cp-divider">
                <p class="cp-section-label">New Password</p>

                <div class="cp-field">
                  <label class="cp-label" for="fnew">New Password</label>
                  <div class="cp-input-wrap">
                    <input class="cp-input" type="password" id="fnew" name="fnew" placeholder="Create a strong password" required>
                    <button type="button" class="cp-toggle" onclick="cpToggle('fnew', this)" tabindex="-1" title="Show/hide">
                      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                  </div>

                  <!-- Live strength indicator -->
                  <div class="cp-strength-box" id="cp-strength-box">
                    <p class="cp-strength-title">Password Strength</p>
                    <div class="cp-bar-wrap">
                      <div class="cp-bar-seg" id="cpbar1"></div>
                      <div class="cp-bar-seg" id="cpbar2"></div>
                      <div class="cp-bar-seg" id="cpbar3"></div>
                      <div class="cp-bar-seg" id="cpbar4"></div>
                      <div class="cp-bar-seg" id="cpbar5"></div>
                    </div>
                    <div class="cp-req" id="req-lower"><span class="dot"></span> Lowercase letter (a–z)</div>
                    <div class="cp-req" id="req-upper"><span class="dot"></span> Uppercase letter (A–Z)</div>
                    <div class="cp-req" id="req-number"><span class="dot"></span> Number (0–9)</div>
                    <div class="cp-req" id="req-special"><span class="dot"></span> Special character (!@#$...)</div>
                    <div class="cp-req" id="req-length"><span class="dot"></span> At least 8 characters</div>
                  </div>
                </div>

                <div class="cp-field">
                  <label class="cp-label" for="fconf">Confirm New Password</label>
                  <div class="cp-input-wrap">
                    <input class="cp-input" type="password" id="fconf" name="fconf" placeholder="Repeat your new password" required>
                    <button type="button" class="cp-toggle" onclick="cpToggle('fconf', this)" tabindex="-1" title="Show/hide">
                      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                  </div>
                </div>

                <button type="submit" class="cp-submit">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                  Update Password
                </button>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var cpPasswordPattern = /^(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
  var cpBarColors = ['#e53e3e', '#dd6b20', '#d69e2e', '#3b82f6', '#0d6dfc'];

  function cpToggle(id, btn) {
    var input = document.getElementById(id);
    var hidden = input.type === 'password';
    input.type = hidden ? 'text' : 'password';
    btn.querySelector('svg').innerHTML = hidden
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }

  var fnewEl      = document.getElementById('fnew');
  var strengthBox = document.getElementById('cp-strength-box');
  var bars        = [1,2,3,4,5].map(function(i){ return document.getElementById('cpbar' + i); });

  fnewEl.addEventListener('focus', function() { strengthBox.style.display = 'block'; });
  fnewEl.addEventListener('blur',  function() { if (!fnewEl.value) strengthBox.style.display = 'none'; });

  fnewEl.addEventListener('input', function() {
    var v = fnewEl.value;
    var checks = [
      { id: 'req-lower',   ok: /[a-z]/.test(v) },
      { id: 'req-upper',   ok: /[A-Z]/.test(v) },
      { id: 'req-number',  ok: /[0-9]/.test(v) },
      { id: 'req-special', ok: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(v) },
      { id: 'req-length',  ok: v.length >= 8 },
    ];
    var score = 0;
    checks.forEach(function(c) {
      var el = document.getElementById(c.id);
      el.className = 'cp-req' + (c.ok ? ' valid' : (v.length ? ' invalid' : ''));
      if (c.ok) score++;
    });
    bars.forEach(function(bar, i) {
      bar.style.background = i < score ? cpBarColors[score - 1] : '#e2e8f0';
    });
  });

  document.getElementById('cp-form').addEventListener('submit', function(e) {
    var sites = {!! json_encode($userpass) !!};
    var pnew  = document.getElementById('fnew').value;
    var pconf = document.getElementById('fconf').value;

    if (!cpPasswordPattern.test(pnew)) {
      e.preventDefault();
      alert('New password must contain at least 8 characters, one lowercase letter, one uppercase letter, one number, and one special character.');
      return;
    }
    if (pnew !== pconf) {
      e.preventDefault();
      alert('New passwords do not match.');
    }
  });
</script>

@endsection