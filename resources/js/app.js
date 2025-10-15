import Alpine from 'alpinejs';

// Only start Alpine if it's not already started
if (!window.Alpine) {
  window.Alpine = Alpine;
  Alpine.start();
}