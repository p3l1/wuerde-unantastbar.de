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
  // Hamburger-Toggle — alle Instanzen unabhaengig
  // =========================================================================

  function initHamburger() {
    const hamburgers = document.querySelectorAll('[data-hamburger]');

    hamburgers.forEach(function (hamburger) {
      const container = hamburger.closest('.lb-component__preview, .hero-demo-scene');
      const mobileNav = container ? container.querySelector('[data-mobile-nav]') : null;

      if (!mobileNav) return;

      hamburger.addEventListener('click', function () {
        const isOpen = hamburger.classList.toggle('is-open');
        hamburger.setAttribute('aria-expanded', String(isOpen));
        mobileNav.classList.toggle('is-open', isOpen);
        mobileNav.setAttribute('aria-hidden', String(!isOpen));
      });
    });
  }

  // =========================================================================
  // Nav-Dropdown — Toggle per Klick, schliessen per Klick ausserhalb
  // =========================================================================

  function initNavDropdowns() {
    const items = document.querySelectorAll('.demo-nav__item--dropdown');

    items.forEach(function (item) {
      const trigger = item.querySelector('.demo-nav__link--has-dropdown');
      const dropdown = item.querySelector('.demo-dropdown');
      if (!trigger || !dropdown) return;

      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        const isOpen = item.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', String(isOpen));
      });
    });

    document.addEventListener('click', function (e) {
      items.forEach(function (item) {
        if (!item.contains(e.target)) {
          item.classList.remove('is-open');
          const trigger = item.querySelector('.demo-nav__link--has-dropdown');
          if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
      });
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
    initNavDropdowns();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
