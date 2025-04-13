// Show modal if form was submitted successfully
if (window.formSubmitted) {
    const modal = document.getElementById('successModal');
    const data = window.submittedData;

    // Populate modal with submitted data
    document.getElementById('modalName').textContent = data.name;
    document.getElementById('modalAge').textContent = data.age;
    document.getElementById('modalEmail').textContent = data.email;
    document.getElementById('modalGender').textContent = data.gender;

    // Show modal
    modal.classList.remove('hidden');
}

// Close modal and reset form
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('successModal').classList.add('hidden');
    document.querySelector('form').reset();
    window.formSubmitted = false;
    // Remove any existing messages
    const messages = document.querySelectorAll('[role="alert"]');
    messages.forEach(message => message.remove());
});

// Reset button functionality
document.querySelector('button[name="reset"]').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('form').reset();
    const messages = document.querySelectorAll('[role="alert"]');
    messages.forEach(message => message.remove());
});