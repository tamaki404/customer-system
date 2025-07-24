
  const modal = document.getElementById('myModal');
  const openBtn = document.getElementById('openModalBtn');
  const closeBtn = document.querySelector('.close-btn');

  openBtn.onclick = () => modal.style.display = 'block';
  closeBtn.onclick = () => modal.style.display = 'none';
  window.onclick = (e) => {
    if (e.target === modal) modal.style.display = 'none';
  };

