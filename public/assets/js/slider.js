let currentSlide = 0;
const slides = document.querySelectorAll('#slider div');
const controls = document.querySelector('.controls-slider');

slides.forEach((slide, index) => {
    const dot = document.createElement('span');
    if(index === 0){
        dot.classList.add('active');
    }
    dot.addEventListener('click', () => goToSlide(index));
    controls.appendChild(dot);
});

function updateActiveSlide() {
    slides.forEach(slide => slide.classList.remove('active'));
    slides[currentSlide].classList.add('active');

    const dots = document.querySelectorAll('.controls-slider span');
    dots.forEach(dot => dot.classList.remove('active'));
    dots[currentSlide].classList.add('active');
}

function goToSlide(index) {
    currentSlide = index;
    updateActiveSlide();
}

function showNextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    updateActiveSlide();
}

function showPrevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateActiveSlide();
}

// document.getElementById('next').addEventListener('click', showNextSlide);
// document.getElementById('prev').addEventListener('click', showPrevSlide);

updateActiveSlide();