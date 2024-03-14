myGap = (window.innerWidth *-1 + 400);

new Glide('.glide', {
  type: 'carousel',
  autoplay: 5000,
  animationDuration: 700,
  focusAt: 'center',
  perView: 1.6,
  animationTimingFunc: 'linear',
  gap: 60,
  hoverpause: 1,
  breakpoints: {
    768: { perView: 1, gap: myGap },
    1024: { perView: 1.1,},    
  }
}).mount()