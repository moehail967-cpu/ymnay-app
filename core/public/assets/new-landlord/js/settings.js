

function toggleRenewal() {
    const renewalBody = document.getElementById('renewal-body')
    const chevron = document.getElementById('renewal-chevron')
        renewalBody.classList.toggle('hidden')
        chevron.classList.toggle('rotate-180')

}


// Update label on toggle change
function updateLabel(name) {
    const checkbox = document.getElementById('toggle-' + name);
    const label = document.getElementById('label-' + name);
    label.textContent = checkbox.checked ? 'Auto-renewal enabled' : 'Auto-renewal disabled';
}

// Save Changes
function saveChanges() {
    const btn = event.currentTarget;
    btn.textContent = 'Saving...';
    btn.disabled = true;
    setTimeout(() => {
        btn.textContent = 'Saved!';
        setTimeout(() => {
            btn.textContent = 'Save Change';
            btn.disabled = false;
        }, 1500);
    }, 800);
}

