const btnShowExpanded = document.querySelectorAll('.btnShowExpanded')
const expanded = document.querySelectorAll('.expanded')


btnShowExpanded.forEach((btn, index) => {
    btn.addEventListener('click', function () {
        const target = expanded[index]
        target.classList.toggle('hidden')
        btn.classList.toggle('rotate-180')
    })
})