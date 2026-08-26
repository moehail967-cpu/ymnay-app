
  // Select2 init
  $(document).ready(function() {
    $('#countrySelect').select2({
      placeholder: 'Select country',
      allowClear: true,
    });
  });

  // Change Avatar
  function changeAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be under 2MB');
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('avatarImg').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  // Remove Avatar
  function removeAvatar() {
    document.getElementById('avatarImg').src = 'https://ui-avatars.com/api/?name=User&background=e8f0f5&color=1b3f50&size=80';
  }

  // Save Changes
  function saveChanges(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.textContent = 'Saving...';
    btn.disabled = true;
    setTimeout(() => {
      btn.textContent = 'Saved!';
      setTimeout(() => {
        btn.textContent = 'Save Changes';
        btn.disabled = false;
      }, 1500);
    }, 900);
  }
