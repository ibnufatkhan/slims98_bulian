(function () {
  'use strict';

  var cfg = window.LANDING_RATING || {};
  var form = document.getElementById('lr-form');
  if (!form || !cfg.submitUrl) return;

  var messageEl = document.getElementById('lr-message');
  var submitBtn = document.getElementById('lr-submit');
  var listEl = document.getElementById('lr-list');
  var avgNumberEl = document.getElementById('lr-avg-number');
  var avgStarsEl = document.getElementById('lr-avg-stars');
  var totalLabelEl = document.getElementById('lr-total-label');
  var distEl = document.getElementById('lr-distribution');

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function starsHtml(rating) {
    var value = Number(rating) || 0;
    var html = '<span class="lr-stars" aria-label="' + value.toFixed(1) + ' / 5">';
    for (var i = 1; i <= 5; i++) {
      if (value >= i) {
        html += '<span class="lr-star lr-star-full">&#9733;</span>';
      } else if (value >= i - 0.5) {
        html += '<span class="lr-star lr-star-half">&#9733;</span>';
      } else {
        html += '<span class="lr-star lr-star-empty">&#9733;</span>';
      }
    }
    return html + '</span>';
  }

  function setMessage(text, type) {
    if (!messageEl) return;
    messageEl.textContent = text || '';
    messageEl.classList.remove('is-success', 'is-error');
    if (type) messageEl.classList.add('is-' + type);
  }

  function renderList(items) {
    if (!listEl) return;
    if (!items || !items.length) {
      listEl.innerHTML = '<p class="lr-empty" id="lr-empty">' + escapeHtml(cfg.labels.empty) + '</p>';
      return;
    }

    listEl.innerHTML = items.map(function (item) {
      return (
        '<article class="lr-item">' +
          '<div class="lr-item-head">' +
            '<strong class="lr-item-name">' + escapeHtml(item.visitor_name) + '</strong>' +
            starsHtml(item.rating) +
          '</div>' +
          '<p class="lr-item-comment">' + escapeHtml(item.comment).replace(/\n/g, '<br>') + '</p>' +
          '<time class="lr-item-date" datetime="' + escapeHtml(item.created_at) + '">' +
            escapeHtml(item.created_at) +
          '</time>' +
        '</article>'
      );
    }).join('');
  }

  function renderStats(stats) {
    if (!stats) return;
    var total = Number(stats.total) || 0;
    var average = Number(stats.average) || 0;
    var distribution = stats.distribution || {};
    var maxDist = 1;

    Object.keys(distribution).forEach(function (key) {
      maxDist = Math.max(maxDist, Number(distribution[key]) || 0);
    });

    if (avgNumberEl) avgNumberEl.textContent = average.toFixed(1);
    if (avgStarsEl) avgStarsEl.innerHTML = starsHtml(average);
    if (totalLabelEl) {
      totalLabelEl.textContent = String(cfg.labels.reviews || '%d ulasan').replace('%d', String(total));
    }

    if (distEl) {
      Array.prototype.forEach.call(distEl.querySelectorAll('.lr-dist-row'), function (row) {
        var star = row.getAttribute('data-star');
        var count = Number(distribution[star]) || 0;
        var pct = total > 0 ? Math.round((count / maxDist) * 100) : 0;
        var fill = row.querySelector('.lr-dist-fill');
        var countEl = row.querySelector('.lr-dist-count');
        if (fill) fill.style.width = pct + '%';
        if (countEl) countEl.textContent = String(count);
      });
    }
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    setMessage('');

    var formData = new FormData(form);
    submitBtn.disabled = true;
    submitBtn.textContent = cfg.labels.sending || 'Mengirim...';

    fetch(cfg.submitUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(function (response) {
        return response.json().then(function (data) {
          return { ok: response.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.data || !result.data.status) {
          throw new Error((result.data && result.data.message) || cfg.labels.error);
        }

        setMessage(result.data.message || '', 'success');
        form.reset();
        var defaultRating = form.querySelector('#lr-rate-5');
        if (defaultRating) defaultRating.checked = true;
        renderStats(result.data.stats);
        renderList(result.data.items);
      })
      .catch(function (error) {
        setMessage(error.message || cfg.labels.error, 'error');
      })
      .finally(function () {
        submitBtn.disabled = false;
        submitBtn.textContent = cfg.labels.send || 'Kirim Ulasan';
      });
  });
})();
