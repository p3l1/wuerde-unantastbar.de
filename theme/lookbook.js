/**
 * Lookbook — Minimales JavaScript
 * Aktive Sidebar-Links, Accordion, Hamburger-Toggle
 */

(function () {
  'use strict';

  // =========================================================================
  // Intersection Observer — Aktives Sidebar-Item beim Scrollen
  // =========================================================================

  function initSidebarObserver() {
    const sidebarLinks = document.querySelectorAll('.lb-sidebar__link');
    const sections = document.querySelectorAll('.lb-section');

    if (!sidebarLinks.length || !sections.length) return;

    const linkMap = {};
    sidebarLinks.forEach(function (link) {
      const id = link.getAttribute('href').replace('#', '');
      linkMap[id] = link;
    });

    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            const id = entry.target.getAttribute('id');
            sidebarLinks.forEach(function (l) {
              l.classList.remove('is-active');
            });
            if (linkMap[id]) {
              linkMap[id].classList.add('is-active');
            }
            // Mobile-Tabs synchronisieren
            syncActiveMobileTab(id);
          }
        });
      },
      {
        rootMargin: '-10% 0px -60% 0px',
        threshold: 0,
      }
    );

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }

  // =========================================================================
  // Mobile Tabs — aktives Tab synchronisieren
  // =========================================================================

  function syncActiveMobileTab(id) {
    const tabs = document.querySelectorAll('.lb-tab');
    tabs.forEach(function (tab) {
      const target = tab.getAttribute('data-target');
      tab.classList.toggle('is-active', target === id);
    });
  }

  function initMobileTabs() {
    const tabs = document.querySelectorAll('.lb-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        const target = tab.getAttribute('data-target');
        const section = document.getElementById(target);
        if (section) {
          section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  // =========================================================================
  // Akkordeon — Kategorie-Filter
  // =========================================================================

  function initAccordion() {
    const triggers = document.querySelectorAll('.category-accordion__trigger');

    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        const expanded = trigger.getAttribute('aria-expanded') === 'true';
        const panelId = trigger.getAttribute('aria-controls');
        const panel = document.getElementById(panelId);

        if (!panel) return;

        if (expanded) {
          trigger.setAttribute('aria-expanded', 'false');
          panel.classList.add('category-accordion__panel--closed');
        } else {
          trigger.setAttribute('aria-expanded', 'true');
          panel.classList.remove('category-accordion__panel--closed');
        }
      });
    });
  }

  // =========================================================================
  // Hamburger-Toggle — Demo
  // =========================================================================

  function initHamburger() {
    const hamburger = document.getElementById('lb-hamburger-demo');
    const mobileNav = document.getElementById('lb-mobile-nav');

    if (!hamburger || !mobileNav) return;

    hamburger.addEventListener('click', function () {
      const isOpen = hamburger.classList.toggle('is-open');
      hamburger.setAttribute('aria-expanded', String(isOpen));
      mobileNav.classList.toggle('is-open', isOpen);
      mobileNav.setAttribute('aria-hidden', String(!isOpen));
    });
  }

  // =========================================================================
  // Init
  // =========================================================================

  function init() {
    initSidebarObserver();
    initMobileTabs();
    initAccordion();
    initHamburger();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
