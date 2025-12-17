let lastScrollTop = 0;
const header = document.querySelector('.cs_main_header');

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop && scrollTop > 100) {
        // Scrolling down - hide header but keep cs_gescout_show
        header.classList.add('hidden');
        if (!header.classList.contains('cs_gescout_show')) {
            header.classList.add('cs_gescout_show');
        }
    } else {
        // Scrolling up - show header and keep cs_gescout_show
        header.classList.remove('hidden');
        if (!header.classList.contains('cs_gescout_show')) {
            header.classList.add('cs_gescout_show');
        }
    }
    
    lastScrollTop = scrollTop;
});