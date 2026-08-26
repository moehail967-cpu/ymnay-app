

  function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('tabler-eye');
      icon.classList.add('tabler-eye-off');
    } else {
      input.type = 'password';
      icon.classList.remove('tabler-eye-off');
      icon.classList.add('tabler-eye');
    }
  }

  function updatePassword(e) {
    e.preventDefault();
    const current = document.getElementById('currentPass').value.trim();
    const newP    = document.getElementById('newPass').value.trim();
    const confirm = document.getElementById('confirmPass').value.trim();
    const error   = document.getElementById('matchError');
    const btn     = document.getElementById('submitBtn');

    error.classList.add('hidden');

    if (!current || !newP || !confirm) {
      alert('Please fill in all fields.');
      return;
    }
    if (newP !== confirm) {
      error.classList.remove('hidden');
      return;
    }
    if (newP.length < 6) {
      alert('New password must be at least 6 characters.');
      return;
    }

    btn.textContent = 'Updating...';
    btn.disabled = true;
    setTimeout(() => {
      btn.textContent = 'Updated!';
      setTimeout(() => {
        btn.textContent = 'Update Password';
        btn.disabled = false;
        resetForm();
      }, 1500);
    }, 900);
  }

  function resetForm() {
    document.getElementById('currentPass').value = '';
    document.getElementById('newPass').value = '';
    document.getElementById('confirmPass').value = '';
    document.getElementById('matchError').classList.add('hidden');
    ['currentPass','newPass','confirmPass'].forEach((id, i) => {
      document.getElementById(id).type = 'password';
    });
    ['eyeIcon1','eyeIcon2','eyeIcon3'].forEach(id => {
      const icon = document.getElementById(id);
      icon.classList.remove('tabler-eye-off');
      icon.classList.add('tabler-eye');
    });
  }
