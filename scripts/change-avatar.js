document.querySelector('.avatar-container').addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('changeAvatarModal'));
    modal.show();
  });

  document.getElementById('saveAvatarBtn').addEventListener('click', () => {
    const fileInput = document.getElementById('avatarInput');
    const avatarImage = document.getElementById('avatarImage');

    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.onload = (e) => {
        avatarImage.src = e.target.result;
      };
      reader.readAsDataURL(fileInput.files[0]);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('changeAvatarModal'));
    modal.hide();
  });