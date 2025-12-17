let lastScrollTop = 0;
const header = document.querySelector('.cs_main_header');

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop && scrollTop > 100) {
        // Scrolling down - remove cs_gescout_show
        header.classList.remove('cs_gescout_show');
    } else {
        // Scrolling up - add cs_gescout_show
        header.classList.add('cs_gescout_show');
    }
    
    lastScrollTop = scrollTop;
});