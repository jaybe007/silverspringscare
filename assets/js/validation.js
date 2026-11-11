/**
 * Silver Springs Care - Form Validation & Submission Handler
 * WCAG accessible errors and AJAX submission
 */

document.addEventListener('DOMContentLoaded', () => {
  // Generic AJAX form binder for any form with data-ajax="true"
  document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const feedback = form.querySelector('.form-feedback') || form.querySelector('.alert');
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Submit';

      // Basic client check
      let isValid = true;
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim() || (input.type === 'checkbox' && !input.checked)) {
          input.setAttribute('aria-invalid', 'true');
          isValid = false;
        } else {
          input.removeAttribute('aria-invalid');
        }
      });

      if (!isValid) {
        if (feedback) {
          feedback.className = 'alert alert-danger';
          feedback.textContent = 'Please fill out all required fields marked with an asterisk (*).';
          feedback.style.display = 'flex';
        }
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Processing securely...</span>';
      }

      try {
        const formData = new FormData(form);
        const res = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
        });

        const data = await res.json();

        if (data.success) {
          if (feedback) {
            feedback.className = 'alert alert-success';
            feedback.innerHTML = `<strong>${data.message || 'Success!'}</strong> ${data.ref_code ? '<br>Reference ID: <strong>' + data.ref_code + '</strong>' : ''}`;
            feedback.style.display = 'flex';
          }
          form.reset();
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
          // Scroll feedback into view
          if (feedback) feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          if (feedback) {
            feedback.className = 'alert alert-danger';
            feedback.textContent = data.message || 'An error occurred. Please verify your details.';
            feedback.style.display = 'flex';
          }
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        if (feedback) {
          feedback.className = 'alert alert-danger';
          feedback.textContent = 'A connection error occurred. Please check your internet connection or call +61428996879.';
          feedback.style.display = 'flex';
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  });
});
