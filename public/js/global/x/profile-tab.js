  const tabButtons = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');

  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const targetTab = button.getAttribute('data-tab');

      // Remove active classes from buttons and contents
      tabButtons.forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
      });

      tabContents.forEach(content => {
        content.classList.remove('active');
      });

      // Add active to the clicked button and related content
      button.classList.add('active');
      button.setAttribute('aria-selected', 'true');
      document.getElementById(targetTab + '-content').classList.add('active');
    });
  });