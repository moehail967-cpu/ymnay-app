const btnAddTicket = document.getElementById('btnAddTicket')
const btnCloseModal = document.getElementById('btnCloseModal')
const openModal = document.getElementById('openModal')
const modalContent = document.getElementById('modalContent')

btnAddTicket.addEventListener('click', () => {
    openModal.classList.remove('hidden')
})

btnCloseModal.addEventListener('click', () => {
    openModal.classList.add('hidden')
})

// click outside close
openModal.addEventListener('click', (e) => {
    if (!modalContent.contains(e.target)) {
        openModal.classList.add('hidden')
    }
})