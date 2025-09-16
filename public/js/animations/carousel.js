        (function(){
            var images = document.querySelectorAll('.carousel-image');
            if(!images || images.length === 0) return;
            var prevBtn = document.querySelector('.carousel-btn.prev');
            var nextBtn = document.querySelector('.carousel-btn.next');
            var currentIndex = 0;

            function showSlide(index){
                images[currentIndex].classList.remove('active');
                currentIndex = (index + images.length) % images.length;
                images[currentIndex].classList.add('active');
            }

            function nextSlide(){ showSlide(currentIndex + 1); }
            function prevSlide(){ showSlide(currentIndex - 1); }

            var timer = setInterval(nextSlide, 4000);
            function resetTimer(){
                clearInterval(timer);
                timer = setInterval(nextSlide, 4000);
            }

            if(prevBtn){ prevBtn.addEventListener('click', function(){ prevSlide(); resetTimer(); }); }
            if(nextBtn){ nextBtn.addEventListener('click', function(){ nextSlide(); resetTimer(); }); }
        })();