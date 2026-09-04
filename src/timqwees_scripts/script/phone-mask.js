export function initPhoneMask() {
  function attach(input) {
    if (input.dataset.maskAttached) return;
    input.dataset.maskAttached = '1';
    input.setAttribute('inputmode', 'tel');
    input.setAttribute('autocomplete', 'tel');
    input.addEventListener('focus', function() {
        if (!this.value) this.value = '+7 ';
    });
    input.addEventListener('input', function() {
        var x = this.value.replace(/\D/g, '').substring(0, 11);
        if (x.length === 0) { this.value = ''; return; }
        var formatted = '+7 ';
        if (x.length > 1) formatted += '(' + x.substring(1, 4);
        if (x.length >= 4) formatted += ') ' + x.substring(4, 7);
        if (x.length >= 7) formatted += '-' + x.substring(7, 9);
        if (x.length >= 9) formatted += '-' + x.substring(9, 11);
        this.value = formatted;
    });
  }

  function initAll() {
    document.querySelectorAll('input[type="tel"]').forEach(attach);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
  const observer = new MutationObserver((mutations) => {
    mutations.forEach(m => {
      m.addedNodes.forEach(node => {
        if (node.nodeType === 1) {
          if (node.matches && node.matches('input[type="tel"]')) attach(node);
          node.querySelectorAll && node.querySelectorAll('input[type="tel"]').forEach(attach);
        }
      });
    });
  });
  observer.observe(document.body, { childList: true, subtree: true });
}
