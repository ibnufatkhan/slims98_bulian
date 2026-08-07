(function () {
  'use strict';

  function qs(root, sel) { return root.querySelector(sel); }
  function qsa(root, sel) { return Array.prototype.slice.call(root.querySelectorAll(sel)); }

  function post(cfg, action, data) {
    var body = new URLSearchParams();
    body.set('action', action);
    Object.keys(data || {}).forEach(function (k) {
      var v = data[k];
      if (Array.isArray(v)) body.set(k, v.join(','));
      else body.set(k, v == null ? '' : String(v));
    });
    var url = (cfg && cfg.solveUrl) || 'index.php?p=iam_not_robot';
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'same-origin',
      body: body.toString()
    }).then(function (r) { return r.json(); });
  }

  function boot(root, cfg) {
    if (!root || root.__iamnrBound) return;
    root.__iamnrBound = true;

    var state = {
      challenge: cfg.challenge,
      answer: null,
      verified: false
    };

    var checkbox = qs(root, '.iamnr-checkbox');
    var modal = qs(root, '.iamnr-modal');
    var titleEl = qs(root, '.iamnr-modal-title');
    var hintEl = qs(root, '.iamnr-modal-hint');
    var bodyEl = qs(root, '.iamnr-modal-body');
    var tokenEl = qs(root, '.iamnr-token');
    var idEl = qs(root, '.iamnr-challenge-id');
    var msg;

    function setLoading(on) {
      root.classList.toggle('is-loading', !!on);
    }

    function setVerified(on) {
      state.verified = !!on;
      root.classList.toggle('is-verified', !!on);
      checkbox.checked = !!on;
      if (!on) tokenEl.value = '';
    }

    function openModal() {
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.hidden = true;
      document.body.style.overflow = '';
      if (!state.verified) {
        checkbox.checked = false;
        root.classList.remove('is-loading');
      }
    }

    function showMsg(text, ok) {
      if (!msg) {
        msg = document.createElement('p');
        msg.className = 'iamnr-msg';
        bodyEl.parentNode.insertBefore(msg, qs(root, '.iamnr-modal-foot'));
      }
      msg.textContent = text || '';
      msg.classList.toggle('is-ok', !!ok);
    }

    function renderChallenge() {
      var ch = state.challenge || {};
      titleEl.textContent = ch.title || (cfg.labels && cfg.labels.title) || "I'm not a robot";
      hintEl.textContent = ch.hint || (cfg.labels && cfg.labels.hint) || '';
      bodyEl.innerHTML = '';
      state.answer = null;
      showMsg('');

      var type = ch.type;
      var data = ch.data || {};

      if (type === 'stopsign' || type === 'vegetables' || type === 'traffic_reverse') {
        renderGrid(data.tiles || []);
      } else if (type === 'wiggle') {
        renderWiggle(data.code || '');
      } else if (type === 'affirmation') {
        renderAffirmation(data.options || []);
      } else if (type === 'tictactoe') {
        renderTtt(data.board || []);
      } else if (type === 'whack') {
        renderWhack(data.need || 5);
      } else if (type === 'math_order') {
        renderMath(data.numbers || []);
      } else {
        bodyEl.textContent = 'Unsupported challenge';
      }
    }

    function renderGrid(tiles) {
      var selected = {};
      var grid = document.createElement('div');
      grid.className = 'iamnr-grid';
      tiles.forEach(function (tile, idx) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'iamnr-tile';
        btn.textContent = tile;
        btn.addEventListener('click', function () {
          if (selected[idx]) {
            delete selected[idx];
            btn.classList.remove('is-selected');
          } else {
            selected[idx] = true;
            btn.classList.add('is-selected');
          }
          state.answer = Object.keys(selected).map(Number).sort(function (a, b) { return a - b; });
        });
        grid.appendChild(btn);
      });
      bodyEl.appendChild(grid);
    }

    function renderWiggle(code) {
      var wrap = document.createElement('div');
      wrap.className = 'iamnr-wiggle';
      String(code).split('').forEach(function (ch) {
        var s = document.createElement('span');
        s.textContent = ch;
        wrap.appendChild(s);
      });
      var input = document.createElement('input');
      input.type = 'text';
      input.className = 'iamnr-input';
      input.autocomplete = 'off';
      input.placeholder = '•••••';
      input.addEventListener('input', function () {
        state.answer = input.value.trim();
      });
      bodyEl.appendChild(wrap);
      bodyEl.appendChild(input);
      setTimeout(function () { input.focus(); }, 50);
    }

    function renderAffirmation(options) {
      var list = document.createElement('div');
      list.className = 'iamnr-options';
      options.forEach(function (opt) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'iamnr-option';
        btn.textContent = opt;
        btn.addEventListener('click', function () {
          qsa(list, '.iamnr-option').forEach(function (el) { el.classList.remove('is-selected'); });
          btn.classList.add('is-selected');
          state.answer = opt;
        });
        list.appendChild(btn);
      });
      bodyEl.appendChild(list);
    }

    function renderTtt(board) {
      var pick = null;
      var grid = document.createElement('div');
      grid.className = 'iamnr-ttt';
      board.forEach(function (cell, idx) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = cell || '';
        if (cell) {
          btn.disabled = true;
        } else {
          btn.addEventListener('click', function () {
            qsa(grid, 'button').forEach(function (el) {
              if (!el.disabled) {
                el.textContent = '';
                el.classList.remove('is-pick');
              }
            });
            btn.textContent = 'X';
            btn.classList.add('is-pick');
            pick = idx;
            state.answer = String(idx);
          });
        }
        grid.appendChild(btn);
      });
      bodyEl.appendChild(grid);
    }

    function renderWhack(need) {
      var hits = 0;
      var arena = document.createElement('div');
      arena.className = 'iamnr-whack';
      var score = document.createElement('div');
      score.className = 'iamnr-whack-score';
      score.textContent = '0 / ' + need;
      var mole = document.createElement('button');
      mole.type = 'button';
      mole.className = 'iamnr-mole';
      mole.setAttribute('aria-label', 'mole');

      function place() {
        var maxX = Math.max(8, arena.clientWidth - 60);
        var maxY = Math.max(8, arena.clientHeight - 60);
        mole.style.left = Math.floor(Math.random() * maxX) + 'px';
        mole.style.top = Math.floor(Math.random() * maxY) + 'px';
      }

      mole.addEventListener('click', function () {
        hits += 1;
        score.textContent = hits + ' / ' + need;
        state.answer = String(hits);
        place();
        if (hits >= need) {
          showMsg((cfg.labels && cfg.labels.success) || 'Verified', true);
        }
      });

      arena.appendChild(mole);
      bodyEl.appendChild(arena);
      bodyEl.appendChild(score);
      setTimeout(place, 30);
    }

    function renderMath(numbers) {
      var order = [];
      var wrap = document.createElement('div');
      wrap.className = 'iamnr-math';
      numbers.forEach(function (n) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = String(n);
        btn.addEventListener('click', function () {
          if (btn.classList.contains('is-done')) return;
          btn.classList.add('is-done');
          order.push(n);
          state.answer = order.slice();
        });
        wrap.appendChild(btn);
      });
      bodyEl.appendChild(wrap);
    }

    function verify() {
      if (state.answer == null || state.answer === '' || (Array.isArray(state.answer) && !state.answer.length)) {
        showMsg((cfg.labels && cfg.labels.fail) || 'Not quite — try again');
        return;
      }
      setLoading(true);
      post(cfg, 'solve', {
        challenge_id: state.challenge.id || idEl.value,
        answer: state.answer
      }).then(function (res) {
        setLoading(false);
        if (res && res.ok) {
          tokenEl.value = res.token || '';
          setVerified(true);
          showMsg((cfg.labels && cfg.labels.success) || 'Verified', true);
          setTimeout(closeModal, 350);
        } else {
          setVerified(false);
          showMsg((res && res.message) || ((cfg.labels && cfg.labels.fail) || 'Not quite — try again'));
        }
      }).catch(function () {
        setLoading(false);
        showMsg((cfg.labels && cfg.labels.fail) || 'Not quite — try again');
      });
    }

    function retry() {
      setLoading(true);
      post(cfg, 'refresh', {
        challenge_id: state.challenge.id || idEl.value,
        section: cfg.section || 'memberarea'
      }).then(function (res) {
        setLoading(false);
        if (!res || !res.ok) {
          showMsg((res && res.message) || 'Failed to refresh');
          return;
        }
        state.challenge = res.challenge;
        idEl.value = res.challenge_id || res.challenge.id;
        root.setAttribute('data-iamnr-id', idEl.value);
        setVerified(false);
        renderChallenge();
      }).catch(function () {
        setLoading(false);
        showMsg('Failed to refresh');
      });
    }

    checkbox.addEventListener('click', function (e) {
      e.preventDefault();
      if (state.verified) return;
      setLoading(true);
      setTimeout(function () {
        setLoading(false);
        renderChallenge();
        openModal();
      }, 450);
    });

    qsa(root, '[data-iamnr-close]').forEach(function (el) {
      el.addEventListener('click', closeModal);
    });
    qs(root, '[data-iamnr-verify]').addEventListener('click', verify);
    qs(root, '[data-iamnr-retry]').addEventListener('click', retry);

    // Block form submit until verified
    var form = root.closest('form');
    if (form) {
      form.addEventListener('submit', function (e) {
        if (!state.verified || !tokenEl.value) {
          e.preventDefault();
          setLoading(true);
          setTimeout(function () {
            setLoading(false);
            renderChallenge();
            openModal();
          }, 200);
        }
      });
    }
  }

  function init() {
    var cfg = window.IAM_NOT_ROBOT;
    if (!cfg) return;
    var roots = document.querySelectorAll('.iamnr-root');
    roots.forEach(function (root) { boot(root, cfg); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
