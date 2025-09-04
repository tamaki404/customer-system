    function toggleSidebar() {
        document.getElementById('sideAccess').classList.toggle('collapsed');
        document.getElementById('showScreen').classList.toggle('expanded');
        document.querySelector('.mobile-toggle').classList.toggle('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('mouseenter', () => item.style.transform = 'translateX(10px)');
            item.addEventListener('mouseleave', () => item.style.transform = 'translateX(0)');
            item.addEventListener('click', () => {
                item.style.transform = 'scale(0.95)';
                setTimeout(() => item.style.transform = 'scale(1)', 150);
            });
        });
    });