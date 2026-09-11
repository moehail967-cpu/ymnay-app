const buttons = document.querySelectorAll('.page-btn');

buttons.forEach(btn => {
  btn.addEventListener('click', () => {

    // reset all buttons
    buttons.forEach(b => {
      b.classList.remove('bg-primary', 'text-white');
      b.classList.add('bg-white', 'text-black');
    });

    // activate clicked button
    btn.classList.remove('bg-white', 'text-black');
    btn.classList.add('bg-primary', 'text-white');
  });
});
