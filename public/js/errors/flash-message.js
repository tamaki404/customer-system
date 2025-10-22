        document.addEventListener('DOMContentLoaded', function () {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = 1;
                setTimeout(() => {
                    flashMessage.style.opacity = 0;
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 400);
                }, 3000);
            }
        });


 