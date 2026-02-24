// Live range slider labels (backup for browsers)
document.querySelectorAll('input[type=range]').forEach(slider => {
    slider.addEventListener('input', function () {
        this.nextElementSibling.value = this.value;
    });
});

// Form validation before submit
document.querySelector('form').addEventListener('submit', function (e) {
    const name = document.querySelector('input[name=name]').value.trim();
    const cgpa = parseFloat(document.querySelector('input[name=cgpa]').value);

    if (name === '') {
        alert('⚠️ Please enter your name!');
        e.preventDefault();
        return;
    }

    if (isNaN(cgpa) || cgpa < 0 || cgpa > 10) {
        alert('⚠️ CGPA must be between 0 and 10!');
        e.preventDefault();
        return;
    }

    // Show loading message on submit
    const btn = document.querySelector('.btn');
    btn.textContent = 'Predicting... ⏳';
    btn.style.background = '#6d28d9';
    btn.disabled = true;
});