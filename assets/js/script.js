document.addEventListener('DOMContentLoaded', function() {
    // Age Calculator
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');

    if(dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age;
        });
    }

    // License Plate Validation
    const lpChar1 = document.getElementById('lp_char1');
    const lpNum = document.getElementById('lp_num');
    const lpChar2 = document.getElementById('lp_char2');

    if(lpChar1) {
        lpChar1.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0,1);
        });
    }
    if(lpNum) {
        lpNum.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0,5);
        });
    }
    if(lpChar2) {
        lpChar2.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0,2);
        });
    }

    // Three separate car photo uploads (front / back / interior)
    const photoInputs = document.querySelectorAll('.car-photo-input');
    const selectedHashes = new Map();

    photoInputs.forEach(input => {
        input.addEventListener('change', function() {
            const box = this.closest('.upload-box');
            const label = box.querySelector('.upload-label');
            const preview = box.querySelector('.upload-preview');
            const placeholder = box.querySelector('.upload-placeholder');
            const slot = box.dataset.slot;

            if (!this.files || !this.files[0]) {
                return;
            }

            const file = this.files[0];
            const maxSize = 5 * 1024 * 1024;
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];

            if (!allowed.includes(file.type) || file.size > maxSize) {
                alert('Please choose a JPG, PNG, or WEBP image under 5MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const dataUrl = e.target.result;

                for (const [otherSlot, hash] of selectedHashes.entries()) {
                    if (otherSlot !== slot && hash === dataUrl.slice(0, 120)) {
                        alert('This image was already chosen for another view. Please pick a different photo for ' + slot + '.');
                        input.value = '';
                        return;
                    }
                }
                selectedHashes.set(slot, dataUrl.slice(0, 120));

                preview.src = dataUrl;
                preview.hidden = false;
                placeholder.style.display = 'none';
                label.classList.add('has-file');
                box.classList.add('is-filled');
            };
            reader.readAsDataURL(file);
        });
    });

    const addCarForm = document.getElementById('addCarForm');
    if (addCarForm) {
        addCarForm.addEventListener('submit', function(e) {
            const required = ['img_front', 'img_back', 'img_interior'];
            const missing = required.filter(id => {
                const el = document.getElementById(id);
                return !el || !el.files || !el.files.length;
            });
            if (missing.length) {
                e.preventDefault();
                alert('Please upload all three photos: Front, Back, and Interior.');
            }
        });
    }

    // Real-Time User Filter (Admin Panel)
    const userFilter = document.getElementById('userFilter');
    if(userFilter) {
        userFilter.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                const email = row.cells[0].textContent.toLowerCase();
                if(email.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Real-Time Car Filter (Showroom Panel)
    const carFilter = document.getElementById('carFilter');
    if(carFilter) {
        carFilter.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const cards = document.querySelectorAll('#carGrid .car-card');
            cards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                const meta = card.querySelector('.car-meta').textContent.toLowerCase();
                const summary = card.querySelector('.car-info p') ? card.querySelector('.car-info p').textContent.toLowerCase() : '';
                if(title.includes(filter) || meta.includes(filter) || summary.includes(filter)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

function copyPhone() {
    const phone = document.getElementById('sellerPhone').innerText;
    navigator.clipboard.writeText(phone).then(() => {
        // High-end subtle toast or direct alert
        alert("Phone number copied successfully: " + phone);
    });
}
