// ui.js - small UI helpers
// Reveal elements with class .reveal-on-scroll or specific selectors
document.addEventListener('DOMContentLoaded', function(){
  // 1) Reveal the 'destacados' title after user interaction (more robust selector)
  const destacados = document.querySelector('.destacados');
  if (destacados){
    function revealDestacados(){
      destacados.classList.add('visible');
      removeRevealListeners();
    }

    function onUserInteraction(e){
      if (e.type === 'scroll' && window.scrollY === 0) return;
      revealDestacados();
    }

    function onKey(e){
      const keys = ['ArrowDown','PageDown',' '];
      if (keys.includes(e.key)) revealDestacados();
    }

    function removeRevealListeners(){
      window.removeEventListener('scroll', onUserInteraction);
      window.removeEventListener('wheel', onUserInteraction);
      window.removeEventListener('touchstart', onUserInteraction);
      window.removeEventListener('keydown', onKey);
    }

    window.addEventListener('scroll', onUserInteraction, { passive: true });
    window.addEventListener('wheel', onUserInteraction, { passive: true });
    window.addEventListener('touchstart', onUserInteraction, { passive: true });
    window.addEventListener('keydown', onKey);
  }
+
  // 2) IntersectionObserver reveal for elements with .reveal-on-scroll.
  // To avoid hiding content when JS is absent, we only add the hidden state at runtime.
+  const items = document.querySelectorAll('.reveal-on-scroll');
+  if (items && items.length){
+    // mark them as hidden now (JS-driven) so without JS they remain visible
+    items.forEach(it => it.classList.add('js-hidden'));
+
+    const observer = new IntersectionObserver((entries, obs) => {
+      entries.forEach(entry => {
+        if (entry.isIntersecting) {
+          const el = entry.target;
+          const parent = el.parentElement;
+          if (parent && parent.classList.contains('reveal-stagger')){
+            const children = Array.from(parent.querySelectorAll('.reveal-on-scroll'));
+            children.forEach((child, idx) => {
+              setTimeout(()=> {
+                child.classList.add('visible');
+                child.classList.remove('js-hidden');
+              }, idx * 120);
+              obs.unobserve(child);
+            });
+          } else {
+            el.classList.add('visible');
+            el.classList.remove('js-hidden');
+            obs.unobserve(el);
+          }
+        }
+      });
+    }, { root: null, threshold: 0.12 });
+
+    items.forEach(it => observer.observe(it));
+  }
+
+
+  // 3) Mobile menu toggle: class-based, better accessibility (ESC, outside click, focus)
+  const toggle = document.querySelector('.nav-toggle');
+  const menu = document.querySelector('.menu');
+  if (toggle && menu){
+    let lastFocused = null;
+
+    function openMenu(){
+      lastFocused = document.activeElement;
+      document.body.classList.add('nav-open');
+      toggle.setAttribute('aria-expanded', 'true');
+      menu.classList.add('open');
+      // focus first focusable item inside menu
+      const firstLink = menu.querySelector('a, button');
+      if (firstLink) firstLink.focus();
+      document.addEventListener('keydown', onKeyDown);
+      document.addEventListener('click', onDocClick);
+    }
+
+    function closeMenu(){
+      document.body.classList.remove('nav-open');
+      toggle.setAttribute('aria-expanded', 'false');
+      menu.classList.remove('open');
+      document.removeEventListener('keydown', onKeyDown);
+      document.removeEventListener('click', onDocClick);
+      // restore focus to the toggle button if possible
+      if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
+      lastFocused = null;
+    }
+
+    function onKeyDown(e){
+      if (e.key === 'Escape' || e.key === 'Esc'){
+        closeMenu();
+      }
+    }
+
+    function onDocClick(e){
+      if (!menu.contains(e.target) && !toggle.contains(e.target)){
+        closeMenu();
+      }
+    }
+
+    toggle.addEventListener('click', function(e){
+      const expanded = this.getAttribute('aria-expanded') === 'true';
+      if (expanded) closeMenu(); else openMenu();
+    });
+  }
+
+});
+
+
