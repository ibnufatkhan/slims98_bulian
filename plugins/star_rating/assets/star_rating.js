/**
 * Star Rating – public OPAC interactions
 */
(function () {
  function init() {
    var form = document.getElementById('star-rating-form');
    if (!form) return;

    var ratingInput = document.getElementById('sr-rating');
    var buttons = Array.prototype.slice.call(document.querySelectorAll('.sr-rate-btn'));
    var alertBox = document.getElementById('sr-alert');
    var submitBtn = document.getElementById('sr-submit');

    function paint(value, hoverOnly) {
      buttons.forEach(function (btn) {
        var v = parseInt(btn.getAttribute('data-value'), 10);
        var active = v <= value;
        btn.classList.toggle(hoverOnly ? 'is-hover' : 'is-active', active);
        if (!hoverOnly) {
          btn.classList.remove('is-hover');
          var icon = btn.querySelector('i');
          if (icon) {
            icon.className = active ? 'fa fa-star' : 'fa fa-star-o';
          }
        }
      });
    }

    buttons.forEach(function (btn) {
      btn.addEventListener('mouseenter', function () {
        paint(parseInt(btn.getAttribute('data-value'), 10), true);
      });
      btn.addEventListener('mouseleave', function () {
        buttons.forEach(function (b) { b.classList.remove('is-hover'); });
        paint(parseInt(ratingInput.value || '0', 10), false);
      });
      btn.addEventListener('click', function () {
        var value = parseInt(btn.getAttribute('data-value'), 10);
        ratingInput.value = String(value);
        paint(value, false);
      });
    });

    function showAlert(type, message) {
      if (!alertBox) return;
      alertBox.hidden = false;
      alertBox.className = 'sr-alert is-' + type;
      alertBox.textContent = message;
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var name = (form.reviewer_name.value || '').trim();
      var comment = (form.comment.value || '').trim();
      var rating = parseInt(ratingInput.value || '0', 10);

      if (!name) {
        showAlert('error', 'Nama wajib diisi.');
        return;
      }
      if (!rating || rating < 1 || rating > 5) {
        showAlert('error', 'Silakan pilih rating 1–5 bintang.');
        return;
      }
      if (!comment) {
        showAlert('error', 'Komentar wajib diisi.');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Mengirim...';

      var body = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: body,
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (result) {
          if (!result.ok || !result.data.success) {
            throw new Error((result.data && result.data.message) || 'Gagal mengirim ulasan.');
          }

          showAlert('success', result.data.message || 'Ulasan berhasil dikirim. Terima kasih!');
          form.reset();
          ratingInput.value = '';
          paint(0, false);

          // Refresh list after short delay so user sees confirmation.
          setTimeout(function () {
            window.location.reload();
          }, 900);
        })
        .catch(function (err) {
          showAlert('error', err.message || 'Terjadi kesalahan. Coba lagi.');
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Kirim Ulasan';
        });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
