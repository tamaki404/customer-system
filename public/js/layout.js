function toggleSidebar() {
    document.getElementById('sideAccess').classList.toggle('collapsed');
    document.getElementById('showScreen').classList.toggle('expanded');
    document.querySelector('.mobile-toggle').classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const sidebar = document.getElementById('sideAccess');
    const toggleBtn = document.querySelector('.mobile-toggle');

    navItems.forEach(item => {
        item.addEventListener('mouseenter', () => item.style.transform = 'translateX(10px)');
        item.addEventListener('mouseleave', () => item.style.transform = 'translateX(0)');
        item.addEventListener('click', () => {
            item.style.transform = 'scale(0.95)';
            setTimeout(() => item.style.transform = 'scale(1)', 150);
        });
    });

    document.addEventListener('click', function(e) {
        if (
            !sidebar.contains(e.target) &&  
            !toggleBtn.contains(e.target) && 
            !sidebar.classList.contains('collapsed') 
        ) {
            sidebar.classList.add('collapsed');
            document.getElementById('showScreen').classList.remove('expanded');
            toggleBtn.classList.remove('active');
        }
    });
});
