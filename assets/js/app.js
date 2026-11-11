document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-demo]').forEach(button => {
    button.addEventListener('click', () => {
      const email = document.querySelector('input[name="email"]');
      const password = document.querySelector('input[name="password"]');
      if (email && password) {
        email.value = button.dataset.demo;
        password.value = 'Classora@123';
        password.focus();
      }
    });
  });
});
