
    function toggleReadMore() {
      const text = document.getElementById('desc-text');
      const btn = document.getElementById('read-more-btn');
      const isCollapsed = text.style.webkitLineClamp !== 'unset' && text.style.webkitLineClamp !== '';
      if (isCollapsed) {
        text.style.webkitLineClamp = 'unset';
        text.style.display = 'block';
        btn.textContent = 'Read Less..';
      } else {
        text.style.display = '-webkit-box';
        text.style.webkitLineClamp = '3';
        btn.textContent = 'Read More..';
      }
    }

    function copyText(btn, text) {
      navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('i');
        icon.classList.remove('tabler-copy');
        icon.classList.add('tabler-check');
        setTimeout(() => {
          icon.classList.remove('tabler-check');
          icon.classList.add('tabler-copy');
        }, 1500);
      });
    }
