/**
 * Silver Springs Support Journey - Interactive 4-Step Experience
 */

document.addEventListener('DOMContentLoaded', () => {
  const journeyForm = document.getElementById('support-journey-form');
  if (!journeyForm) return;

  let currentStep = 1;
  const totalSteps = 4;

  const progressFill = document.querySelector('.journey-progress-fill');
  const stepPills = document.querySelectorAll('.journey-step-pill');
  const stepSlides = document.querySelectorAll('.journey-step-slide');
  const prevBtn = document.getElementById('journey-prev-btn');
  const nextBtn = document.getElementById('journey-next-btn');
  const submitBtn = document.getElementById('journey-submit-btn');

  function updateWizardUI() {
    // Update Progress bar
    if (progressFill) {
      progressFill.style.width = `${(currentStep / totalSteps) * 100}%`;
    }

    // Update Pills
    stepPills.forEach((pill, idx) => {
      const stepNum = idx + 1;
      pill.classList.remove('is-active', 'is-completed');
      if (stepNum === currentStep) {
        pill.classList.add('is-active');
      } else if (stepNum < currentStep) {
        pill.classList.add('is-completed');
      }
    });

    // Update Slides
    stepSlides.forEach((slide) => {
      const slideStep = parseInt(slide.dataset.step, 10);
      if (slideStep === currentStep) {
        slide.classList.add('is-active');
        const firstFocus = slide.querySelector('input:checked, input:not([type="hidden"]), select, textarea');
        if (firstFocus) firstFocus.focus();
      } else {
        slide.classList.remove('is-active');
      }
    });

    // Controls
    if (prevBtn) {
      prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    }

    if (nextBtn && submitBtn) {
      if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
      } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
      }
    }
  }

  // Handle Choice Card selections visually
  document.querySelectorAll('.choice-card').forEach(card => {
    const input = card.querySelector('input');
    if (!input) return;

    const syncCard = () => {
      if (input.type === 'radio') {
        const name = input.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
          const c = r.closest('.choice-card');
          if (c) c.classList.toggle('is-selected', r.checked);
        });
      } else if (input.type === 'checkbox') {
        card.classList.toggle('is-selected', input.checked);
      }
    };

    input.addEventListener('change', syncCard);
    card.addEventListener('click', (e) => {
      if (e.target !== input) {
        if (input.type === 'radio') {
          input.checked = true;
        } else if (input.type === 'checkbox') {
          input.checked = !input.checked;
        }
        syncCard();
      }
    });

    // Initial sync
    syncCard();
  });

  // Next Step Action with validation
  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (validateCurrentStep(currentStep)) {
        if (currentStep < totalSteps) {
          currentStep++;
          updateWizardUI();
        }
      }
    });
  }

  // Previous Step Action
  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentStep > 1) {
        currentStep--;
        updateWizardUI();
      }
    });
  }

  function validateCurrentStep(step) {
    const activeSlide = document.querySelector(`.journey-step-slide[data-step="${step}"]`);
    if (!activeSlide) return true;

    if (step === 1) {
      const selectedGoals = activeSlide.querySelectorAll('input[name="support_goals[]"]:checked');
      if (selectedGoals.length === 0) {
        alert('Please choose at least one area of support you are interested in exploring.');
        return false;
      }
    }

    if (step === 2) {
      const name = document.getElementById('journey_name');
      const email = document.getElementById('journey_email');
      const phone = document.getElementById('journey_phone');

      if (!name || !name.value.trim()) {
        alert('Please enter your full name so our team knows how to address you.');
        if (name) name.focus();
        return false;
      }
      if (!email || !email.value.trim() || !email.value.includes('@')) {
        alert('Please enter a valid email address.');
        if (email) email.focus();
        return false;
      }
      if (!phone || !phone.value.trim()) {
        alert('Please enter a contact phone number.');
        if (phone) phone.focus();
        return false;
      }
    }

    if (step === 4) {
      const consent = activeSlide.querySelector('input[name="journey_consent"]');
      if (consent && !consent.checked) {
        alert('Please confirm your consent before submitting.');
        if (consent) consent.focus();
        return false;
      }
    }

    return true;
  }

  // Form submission via AJAX or graceful fallback
  journeyForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validateCurrentStep(currentStep)) return;

    const feedbackBox = document.getElementById('journey-feedback');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting Your Preferences...';
    }

    try {
      const formData = new FormData(journeyForm);
      const res = await fetch(journeyForm.action || 'api/submit-journey.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });

      const data = await res.json();
      if (data.success) {
        // Render success state
        document.querySelector('.journey-content-pane').innerHTML = `
          <div style="text-align: center; padding: 3rem 1rem;">
            <div style="width: 72px; height: 72px; background: var(--color-sage-100); color: var(--color-forest-700); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <span class="badge badge-sage" style="margin-bottom: 1rem;">Enquiry Successfully Received</span>
            <h2 style="font-size: 2rem; margin-bottom: 1rem;">Thank you, ${data.name || 'friend'}!</h2>
            <p class="lead" style="max-width: 620px; margin: 0 auto 2rem;">
              Your initial support exploration has been registered with reference <strong>#${data.ref_code || 'SSC-REF'}</strong>. A dedicated Silver Springs Care team member will connect with you within 1 business day.
            </p>
            <div style="background: var(--color-slate-50); border-radius: var(--radius-lg); padding: 1.5rem; max-width: 580px; margin: 0 auto 2rem; text-align: left; border: 1px solid var(--color-slate-200);">
              <h4 style="margin-bottom: 0.5rem;">What Happens Next?</h4>
              <ol style="list-style: decimal; padding-left: 1.25rem; font-size: 0.95rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.5rem;">
                <li><strong>Gentle Introduction:</strong> We will reach out via your preferred contact method for a relaxed, pressure-free chat.</li>
                <li><strong>Explore Possibilities:</strong> We discuss what matters to you, your goals, and how support can be structured around your life.</li>
                <li><strong>No Lock-In:</strong> You remain in complete control of every choice.</li>
              </ol>
            </div>
            <a href="index.php" class="btn btn-navy">Return to Homepage</a>
          </div>
        `;
        if (prevBtn) prevBtn.style.display = 'none';
        if (submitBtn) submitBtn.style.display = 'none';
      } else {
        if (feedbackBox) {
          feedbackBox.className = 'alert alert-danger';
          feedbackBox.textContent = data.message || 'An error occurred. Please try again.';
          feedbackBox.style.display = 'block';
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Complete & Send Enquiry';
        }
      }
    } catch (err) {
      if (feedbackBox) {
        feedbackBox.className = 'alert alert-danger';
        feedbackBox.textContent = 'Network error. Please try submitting again or call 1300 772 273 directly.';
        feedbackBox.style.display = 'block';
      }
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Complete & Send Enquiry';
      }
    }
  });

  // Initialize
  updateWizardUI();
});
