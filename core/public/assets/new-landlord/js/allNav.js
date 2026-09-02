// pageintaion sliders
$(window).on('scroll', function () {
  if ($(this).scrollTop() > 10) {
    $('#mainNav').addClass(
      'bg-white backdrop-blur-md shadow-md translate-y-0 opacity-100',
    );
  } else {
    $('#mainNav').removeClass(
      'bg-primary backdrop-blur-md shadow-md translate-y-0 opacity-100',
    );
  }
});