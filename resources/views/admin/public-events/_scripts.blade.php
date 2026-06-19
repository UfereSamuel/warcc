<script>
document.getElementById('registration_required').addEventListener('change', function() {
    const registrationFields = document.getElementById('registration-fields');
    registrationFields.style.display = this.checked ? 'block' : 'none';
});

if (document.getElementById('registration_required').checked) {
    document.getElementById('registration-fields').style.display = 'block';
}

const startDateField = document.getElementById('start_date');
const endDateField = document.getElementById('end_date');
const registrationDeadlineField = document.getElementById('registration_deadline');

if (startDateField.hasAttribute('min')) {
    const today = new Date().toISOString().split('T')[0];
    startDateField.min = today;
    endDateField.min = today;
}

startDateField.addEventListener('change', function() {
    endDateField.min = this.value;
    if (endDateField.value && endDateField.value < this.value) {
        endDateField.value = this.value;
    }
    registrationDeadlineField.max = this.value;
});

const summaryField = document.getElementById('summary');
summaryField.addEventListener('input', function() {
    const remaining = 500 - this.value.length;
    let feedbackElement = document.getElementById('summary-feedback');

    if (!feedbackElement) {
        feedbackElement = document.createElement('small');
        feedbackElement.id = 'summary-feedback';
        feedbackElement.className = 'form-text text-muted';
        this.parentNode.appendChild(feedbackElement);
    }

    feedbackElement.textContent = `${remaining} characters remaining`;
    feedbackElement.className = remaining < 0 ? 'form-text text-danger'
        : (remaining < 50 ? 'form-text text-warning' : 'form-text text-muted');
});

document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) {
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        let preview = document.getElementById('image-preview');
        if (!preview) {
            preview = document.createElement('img');
            preview.id = 'image-preview';
            preview.className = 'mt-2 img-thumbnail';
            preview.style.maxWidth = '200px';
            document.getElementById('featured_image').parentNode.appendChild(preview);
        }
        preview.src = event.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
