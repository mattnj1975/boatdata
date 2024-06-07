let banner_animation = document.querySelector('.banner_animation');
if (banner_animation) {
    let banner_svg = banner_animation.querySelectorAll('.banner_svg:not(.van)');
    setTimeout(() => {
        banner_svg.forEach(ele => {
            ele.classList.add('floating');
        })
    }, 2200);
}

window.addEventListener('scroll', function () {
    var navbar = document.getElementById('navbar'); // Replace 'navbar' with the ID of your navbar element
    var scrollPosition = window.scrollY;
    if (navbar) {
        // Adjust the scroll position threshold and color as needed
        if (scrollPosition > 10) {
            navbar.classList.add('navbar_scroll');
        } else {
            navbar.classList.remove('navbar_scroll');
        }
    }
});

var video = document.querySelectorAll('.video-tag-grid');
var video_play = document.querySelectorAll('.play');
var video_pause = document.querySelectorAll('.pause');
video_pause.forEach(btn => {
    btn.style.cssText = `display:none`;
})

video_play.forEach((btn, i) => {
    btn.addEventListener('click', () => {
        video[i].play();
        btn.style.cssText = `display:none`;
        video_pause[i].style.cssText = `display:block`;
    })
})
video_pause.forEach((btn, i) => {
    btn.addEventListener('click', () => {
        video[i].pause();
        btn.style.cssText = `display:none`
        video_play[i].style.cssText = `display:block`
    })
})

const offcanvas_close = document.querySelector('.offcanvas-close');
const offcanvas_open = document.querySelector('.offcanvas-open');
const offcanvas_ = document.querySelector('.offcanvas');
const offcanvas_o = document.querySelector('.offcanvas-opacity');
if (offcanvas_open) {
    document.addEventListener('DOMContentLoaded', () => {
        offcanvas_open.addEventListener('click', canvas);
        offcanvas_close.addEventListener('click', canvas)
        offcanvas_o.addEventListener('click', canvas)

        function canvas() {
            offcanvas_.classList.toggle('collapse-offcanvas');
            setTimeout(() => {
                offcanvas_.classList.toggle('hidden');
                offcanvas_o.classList.toggle('hidden');
            }, 230);
        }
    })
}