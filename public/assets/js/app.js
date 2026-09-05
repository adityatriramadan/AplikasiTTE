// Minimal progressive enhancement hook for the static asset layer.
document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('form[method="post"]');
  forms.forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');
      if (button) {
        button.disabled = true;
      }
    }, { once: true });
  });
});