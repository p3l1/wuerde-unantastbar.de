<?php
/**
 * Template Name: Lookbook
 *
 * ABOUTME: Vollständiges Lookbook-Template – zeigt alle Theme-Komponenten in einer Übersicht.
 * ABOUTME: Kein Build-Step erforderlich; alle Komponenten werden direkt als HTML gerendert.
 */

get_header();
?>
<div class="lb-layout">

  <!-- ========================================================
       SIDEBAR-NAVIGATION
       ======================================================== -->
  <nav class="lb-sidebar" aria-label="Lookbook Navigation">
    <p class="lb-sidebar__label">Komponenten</p>
    <ul class="lb-sidebar__list">
      <li><a href="#navigation" class="lb-sidebar__link">Navigation</a></li>
      <li><a href="#typografie" class="lb-sidebar__link">Typografie</a></li>
      <li><a href="#farben"     class="lb-sidebar__link">Farben &amp; Krone</a></li>
      <li><a href="#buttons"    class="lb-sidebar__link">Buttons</a></li>
      <li><a href="#karten"     class="lb-sidebar__link">Karten</a></li>
      <li><a href="#formulare"  class="lb-sidebar__link">Formulare</a></li>
      <li><a href="#mach-mit"   class="lb-sidebar__link">Mach mit</a></li>
    </ul>

    <!-- Mobile: Tab-Navigation -->
    <div class="lb-tabs" role="tablist" aria-label="Abschnitte">
      <button class="lb-tab" role="tab" data-target="navigation">Navigation</button>
      <button class="lb-tab" role="tab" data-target="hero">Hero</button>
      <button class="lb-tab" role="tab" data-target="typografie">Typografie</button>
      <button class="lb-tab" role="tab" data-target="farben">Farben</button>
      <button class="lb-tab" role="tab" data-target="buttons">Buttons</button>
      <button class="lb-tab" role="tab" data-target="karten">Karten</button>
      <button class="lb-tab" role="tab" data-target="formulare">Formulare</button>
      <button class="lb-tab" role="tab" data-target="mach-mit">Mach mit</button>
      <button class="lb-tab" role="tab" data-target="footer">Footer</button>
    </div>
  </nav>

  <!-- ========================================================
       HAUPTBEREICH
       ======================================================== -->
  <main class="lb-main">

    <!-- KOPFZEILE -->
    <header class="lb-page-header">
      <h1 class="lb-page-title brand-text">Lookbook</h1>
      <p class="lb-page-subtitle">Alle Theme-Komponenten auf einen Blick — Corporate Identity des Vereins für Menschenwürde und Demokratie&nbsp;e.V.</p>
    </header>

    <!-- ====================================================
         5. NAVIGATION
         ==================================================== -->
    <section id="navigation" class="lb-section">
      <h2 class="lb-section__title">Navigation</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Header — Desktop</h3>
        <div class="lb-component__preview lb-component__preview--flush">
          <div class="demo-header">
            <div class="demo-header__brand brand-text">Verein f&uuml;r Menschenw&uuml;rde und Demokratie e.V.</div>
            <nav class="demo-nav" aria-label="Demo Hauptnavigation">
              <ul class="demo-nav__list">
                <li><a href="#" class="demo-nav__link demo-nav__link--active">Startseite</a></li>
                <li class="demo-nav__item--dropdown">
                  <a href="#" class="demo-nav__link demo-nav__link--has-dropdown" aria-expanded="false">
                    &Uuml;ber uns
                    <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                  </a>
                  <ul class="demo-dropdown">
                    <li><a href="#" class="demo-dropdown__link">Verein &amp; Geschichte</a></li>
                    <li><a href="#" class="demo-dropdown__link">Vorstand</a></li>
                    <li><a href="#" class="demo-dropdown__link">Satzung</a></li>
                  </ul>
                </li>
                <li><a href="#" class="demo-nav__link">Mach mit</a></li>
                <li><a href="#" class="demo-nav__link">Kontakt</a></li>
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Header — Mobil</h3>
        <div class="lb-component__preview lb-component__preview--phone">
          <div class="lb-phone-mock">
            <div class="lb-phone-mock__screen">
              <div class="demo-header">
                <div class="demo-header__brand brand-text">W&uuml;rde Unantastbar</div>
                <button class="demo-hamburger" aria-label="Men&uuml; &ouml;ffnen" aria-expanded="false" data-hamburger>
                  <span></span><span></span><span></span>
                </button>
              </div>
              <div class="demo-mobile-nav demo-mobile-nav--fullscreen" aria-hidden="true" data-mobile-nav>
                <ul class="demo-mobile-nav__list">
                  <li><a href="#">Startseite</a></li>
                  <li><a href="#">&#220;ber uns</a></li>
                  <li><a href="#">Mach mit</a></li>
                  <li><a href="#">Kontakt</a></li>
                </ul>
                <div class="demo-mobile-nav__brand brand-text">Verein f&uuml;r Menschenw&uuml;rde und Demokratie e.V.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    

    <!-- ====================================================
         1. TYPOGRAFIE
         ==================================================== -->
    <section id="typografie" class="lb-section">
      <h2 class="lb-section__title">Typografie</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Überschriften (TANKER)</h3>
        <div class="lb-component__preview">
          <h1>H1 — Menschenwürde ist unantastbar</h1>
          <h2>H2 — Demokratie braucht Engagement</h2>
          <h3>H3 — Gemeinsam für eine offene Gesellschaft</h3>
          <h4>H4 — Veranstaltungen und Termine</h4>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Vereinsname / Brand-Text (PALLY)</h3>
        <div class="lb-component__preview">
          <p class="brand-text lb-brand-display">Würde Unantastbar</p>
          <p class="brand-text lb-brand-large">Verein für Menschenwürde und Demokratie</p>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Lead-Text</h3>
        <div class="lb-component__preview">
          <p class="lb-lead">Wir setzen uns aktiv für die Menschenwürde ein — in unserer Region, in Deutschland und weltweit. Demokratie ist keine Selbstverständlichkeit, sie muss täglich gelebt werden.</p>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Fließtext</h3>
        <div class="lb-component__preview">
          <p>Der Verein wurde 2020 gegründet und vereint Menschen aus unterschiedlichen gesellschaftlichen Bereichen. Unsere Mitglieder engagieren sich in Bildung, Kultur, Handwerk und Kirche. Gemeinsam stehen wir für eine Gesellschaft, in der jeder Mensch in Würde leben kann.</p>
          <p>Wir organisieren regelmäßig Veranstaltungen, Diskussionsrunden und Aktionen. Alle Interessierten sind herzlich willkommen — Mitgliedschaft ist nicht Voraussetzung für Teilnahme.</p>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Blockzitat</h3>
        <div class="lb-component__preview">
          <blockquote class="lb-blockquote">
            <p>Die Würde des Menschen ist unantastbar. Sie zu achten und zu schützen ist Verpflichtung aller staatlichen Gewalt.</p>
            <cite>Grundgesetz der Bundesrepublik Deutschland, Artikel 1</cite>
          </blockquote>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Schriftgrößen-Skala</h3>
        <div class="lb-component__preview lb-type-scale">
          <div class="lb-type-row"><span style="font-size:var(--text-6xl)">Aa</span><code>--text-6xl · 3.75rem · 60px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-5xl)">Aa</span><code>--text-5xl · 3rem · 48px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-4xl)">Aa</span><code>--text-4xl · 2.25rem · 36px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-3xl)">Aa</span><code>--text-3xl · 1.875rem · 30px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-2xl)">Aa</span><code>--text-2xl · 1.5rem · 24px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-xl)">Aa</span><code>--text-xl · 1.25rem · 20px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-lg)">Aa</span><code>--text-lg · 1.125rem · 18px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-base)">Aa</span><code>--text-base · 1rem · 16px</code></div>
          <div class="lb-type-row"><span style="font-size:var(--text-sm)">Aa</span><code>--text-sm · 0.875rem · 14px</code></div>
        </div>
      </div>
    </section>

    <!-- ====================================================
         2. FARBEN
         ==================================================== -->
    <section id="farben" class="lb-section">
      <h2 class="lb-section__title">Farben</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Primärfarben</h3>
        <div class="lb-color-grid">
          <div class="lb-color-tile" style="background:var(--color-teal);color:#fff;">
            <span class="lb-color-tile__name">Türkis</span>
            <span class="lb-color-tile__hex">#00ACA0</span>
            <span class="lb-color-tile__var">--color-teal</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-yellow);color:#1A1A1A;">
            <span class="lb-color-tile__name">Gelb</span>
            <span class="lb-color-tile__hex">#F7BC2F</span>
            <span class="lb-color-tile__var">--color-yellow</span>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Sekundärfarben</h3>
        <div class="lb-color-grid lb-color-grid--secondary">
          <div class="lb-color-tile" style="background:var(--color-pink);color:#1A1A1A;">
            <span class="lb-color-tile__name">Rosa</span>
            <span class="lb-color-tile__hex">#F2ACC6</span>
            <span class="lb-color-tile__var">--color-pink</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-blue);color:#1A1A1A;">
            <span class="lb-color-tile__name">Blau</span>
            <span class="lb-color-tile__hex">#96D3DF</span>
            <span class="lb-color-tile__var">--color-blue</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-red);color:#fff;">
            <span class="lb-color-tile__name">Rot</span>
            <span class="lb-color-tile__hex">#E41D21</span>
            <span class="lb-color-tile__var">--color-red</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-green);color:#fff;">
            <span class="lb-color-tile__name">Grün</span>
            <span class="lb-color-tile__hex">#1EA84F</span>
            <span class="lb-color-tile__var">--color-green</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-orange);color:#fff;">
            <span class="lb-color-tile__name">Orange</span>
            <span class="lb-color-tile__hex">#EC6907</span>
            <span class="lb-color-tile__var">--color-orange</span>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Krone — Farbvarianten</h3>
        <div class="lb-component__preview">
          <div class="lb-krone-grid">
            <div class="lb-krone-item">
              <div class="lb-krone-item__swatch" style="background:#00ACA0;">
                <img class="lb-krone-item__img"
                     src="<?php echo esc_url( wuerde_asset_url( 'krone-white.png' ) ); ?>"
                     alt="Krone weiß" width="72" height="72">
              </div>
              <span class="lb-krone-item__label">Weiß<br>auf Türkis</span>
            </div>
            <div class="lb-krone-item">
              <div class="lb-krone-item__swatch" style="background:#F5F5F5;box-shadow:inset 0 0 0 1px var(--color-border);">
                <img class="lb-krone-item__img"
                     src="<?php echo esc_url( wuerde_asset_url( 'krone-teal.png' ) ); ?>"
                     alt="Krone türkis" width="72" height="72">
              </div>
              <span class="lb-krone-item__label">Türkis<br>auf Hell</span>
            </div>
            <div class="lb-krone-item">
              <div class="lb-krone-item__swatch" style="background:#1A1A1A;">
                <img class="lb-krone-item__img"
                     src="<?php echo esc_url( wuerde_asset_url( 'krone-yellow.png' ) ); ?>"
                     alt="Krone gelb" width="72" height="72">
              </div>
              <span class="lb-krone-item__label">Gelb<br>auf Schwarz</span>
            </div>
            <div class="lb-krone-item">
              <div class="lb-krone-item__swatch" style="background:#F5F5F5;box-shadow:inset 0 0 0 1px var(--color-border);">
                <img class="lb-krone-item__img"
                     src="<?php echo esc_url( wuerde_asset_url( 'krone-black.png' ) ); ?>"
                     alt="Krone schwarz" width="72" height="72">
              </div>
              <span class="lb-krone-item__label">Schwarz<br>auf Hell</span>
            </div>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Neutralfarben</h3>
        <div class="lb-color-grid lb-color-grid--neutral">
          <div class="lb-color-tile lb-color-tile--bordered" style="background:var(--color-white);color:#1A1A1A;">
            <span class="lb-color-tile__name">Weiß</span>
            <span class="lb-color-tile__hex">#FFFFFF</span>
            <span class="lb-color-tile__var">--color-white</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-gray-100);color:#1A1A1A;">
            <span class="lb-color-tile__name">Gray 100</span>
            <span class="lb-color-tile__hex">#F5F5F5</span>
            <span class="lb-color-tile__var">--color-gray-100</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-gray-200);color:#1A1A1A;">
            <span class="lb-color-tile__name">Gray 200</span>
            <span class="lb-color-tile__hex">#E8E8E8</span>
            <span class="lb-color-tile__var">--color-gray-200</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-gray-400);color:#1A1A1A;">
            <span class="lb-color-tile__name">Gray 400</span>
            <span class="lb-color-tile__hex">#9E9E9E</span>
            <span class="lb-color-tile__var">--color-gray-400</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-gray-700);color:#fff;">
            <span class="lb-color-tile__name">Gray 700</span>
            <span class="lb-color-tile__hex">#4A4A4A</span>
            <span class="lb-color-tile__var">--color-gray-700</span>
          </div>
          <div class="lb-color-tile" style="background:var(--color-black);color:#fff;">
            <span class="lb-color-tile__name">Schwarz</span>
            <span class="lb-color-tile__hex">#1A1A1A</span>
            <span class="lb-color-tile__var">--color-black</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ====================================================
         3. BUTTONS
         ==================================================== -->
    <section id="buttons" class="lb-section">
      <h2 class="lb-section__title">Buttons</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Primary — Türkis</h3>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn--primary btn--lg">Mitmachen (groß)</button>
          <button class="btn btn--primary">Mitmachen</button>
          <button class="btn btn--primary" disabled>Mitmachen (deaktiviert)</button>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Secondary — Gelb</h3>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn--secondary btn--lg">Mehr erfahren (groß)</button>
          <button class="btn btn--secondary">Mehr erfahren</button>
          <button class="btn btn--secondary" disabled>Mehr erfahren (deaktiviert)</button>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Outline</h3>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn--outline btn--lg">Kontakt (groß)</button>
          <button class="btn btn--outline">Kontakt</button>
          <button class="btn btn--outline" disabled>Kontakt (deaktiviert)</button>
        </div>
      </div>

      <div class="lb-component lb-component--dark">
        <h3 class="lb-component__label lb-component__label--light">Ghost — weiß auf Dunkel</h3>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn--ghost btn--lg">Spenden (groß)</button>
          <button class="btn btn--ghost">Spenden</button>
          <button class="btn btn--ghost" disabled>Spenden (deaktiviert)</button>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Krone-Button — Für besondere Aktionen</h3>
        <p style="font-size:var(--text-sm);color:var(--color-text-muted);margin-bottom:var(--space-4);">Kombiniert das Krone-Symbol mit dem CTA — türkiser Hintergrund, weiße Krone; bei Hover gelb mit türkiser Krone.</p>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn-crown">
            <span class="btn-crown__icon" aria-hidden="true"></span>
            Jetzt mitmachen (groß)
          </button>
          <button class="btn btn-crown">
            <span class="btn-crown__icon" aria-hidden="true"></span>
            Jetzt mitmachen
          </button>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Krone-Button Outline</h3>
        <div class="lb-component__preview lb-button-row">
          <button class="btn btn-crown btn-crown--outline btn--lg">
            <span class="btn-crown__icon" aria-hidden="true"></span>
            Mehr erfahren (groß)
          </button>
          <button class="btn btn-crown btn-crown--outline">
            <span class="btn-crown__icon" aria-hidden="true"></span>
            Mehr erfahren
          </button>
        </div>
      </div>
    </section>

    <!-- ====================================================
         4. KARTEN
         ==================================================== -->
    <section id="karten" class="lb-section">
      <h2 class="lb-section__title">Karten</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Standard-Karte</h3>
        <div class="lb-cards-grid">
          <article class="card">
            <div class="card__image">
              <img src="https://placehold.co/600x400/00ACA0/FFFFFF?text=Bild" alt="Platzhalterbild" width="600" height="400">
            </div>
            <div class="card__body">
              <h3 class="card__title">Gemeinschaft stärken</h3>
              <p class="card__text">Wir bringen Menschen zusammen, die sich für eine offene und gerechte Gesellschaft einsetzen — über Generationen und Milieus hinweg.</p>
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="card__link">Mehr erfahren</a>
            </div>
          </article>
          <article class="card">
            <div class="card__image">
              <img src="https://placehold.co/600x400/F7BC2F/1A1A1A?text=Bild" alt="Platzhalterbild" width="600" height="400">
            </div>
            <div class="card__body">
              <h3 class="card__title">Veranstaltungen & Aktionen</h3>
              <p class="card__text">Von Diskussionsabenden bis zu Straßenaktionen — wir sind aktiv vor Ort und laden alle ein, dabei zu sein.</p>
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="card__link">Zur Übersicht</a>
            </div>
          </article>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Highlight-Box (farbig)</h3>
        <div class="lb-cards-grid">
          <div class="highlight-box highlight-box--teal">
            <h3 class="highlight-box__title">Jetzt Mitglied werden!</h3>
            <p class="highlight-box__text">Unterstütze den Verein und werde Teil der Bewegung für Menschenwürde und Demokratie.</p>
            <button class="btn btn--ghost">Mitglied werden</button>
          </div>
          <div class="highlight-box highlight-box--yellow">
            <h3 class="highlight-box__title">Nächste Veranstaltung</h3>
            <p class="highlight-box__text">Diskussionsabend: Demokratie in der Krise? — 15. April 2026, 19:00 Uhr, Stadtbibliothek.</p>
            <button class="btn btn--outline">Anmelden</button>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Karte mit Krone-Wasserzeichen</h3>
        <div class="lb-cards-grid">
          <article class="card" style="position:relative;overflow:hidden;">
            <span class="crown-watermark" aria-hidden="true"></span>
            <div class="card__body">
              <h3 class="card__title">Menschenwürde verteidigen</h3>
              <p class="card__text">Das Krone-Symbol steht für die Würde jedes Menschen — unantastbar und universell gültig.</p>
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="card__link">Mehr erfahren</a>
            </div>
          </article>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Zitat-Karte</h3>
        <div class="lb-cards-grid">
          <div class="quote-card">
            <svg class="quote-card__icon" aria-hidden="true" width="32" height="32" viewBox="0 0 32 32">
              <path fill="currentColor" d="M9.333 20H4L8 12h-4V4h8v8l-2.667 8zM21.333 20H16l4-8h-4V4h8v8l-2.667 8z"/>
            </svg>
            <blockquote class="quote-card__text">Demokratie bedeutet, dass auch diejenigen gehört werden, die sonst keine Stimme haben.</blockquote>
            <cite class="quote-card__author">— Maria Müller, Vereinsmitglied</cite>
          </div>
          <div class="quote-card">
            <svg class="quote-card__icon" aria-hidden="true" width="32" height="32" viewBox="0 0 32 32">
              <path fill="currentColor" d="M9.333 20H4L8 12h-4V4h8v8l-2.667 8zM21.333 20H16l4-8h-4V4h8v8l-2.667 8z"/>
            </svg>
            <blockquote class="quote-card__text">Hier finde ich Menschen, denen dasselbe wichtig ist wie mir. Das gibt Kraft für den Alltag.</blockquote>
            <cite class="quote-card__author">— Klaus Schmidt, Mitgründer</cite>
          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Personen / Profil-Elemente</h3>
        <div class="lb-cards-grid">

          <article class="profile-card">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02405-scaled.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1964</p>
              <h3 class="profile-card__name">Ralf Knoblauch</h3>
              <p class="profile-card__title">Tischler · Dipl. Theologe · Diakon · Bildhauer</p>
              <p class="profile-card__text">Die unantastbare Würde des Menschen ist seit vielen Jahren mein Lebensthema. Es findet Ausdruck in Königsskulpturen aus Eichenholz — und genau daraus entstand der Impuls für diese Initiative.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02379-scaled.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1970</p>
              <h3 class="profile-card__name">Anja Knoblauch</h3>
              <p class="profile-card__title">Dipl. Theologin · Pastoralreferentin</p>
              <p class="profile-card__text">Mit meinem Engagement möchte ich einen nachhaltigen Beitrag zu einer offenen, vielfältigen und solidarischen Gesellschaft leisten — und sichtbare Zeichen für demokratische, menschenzugewandte Haltungen stärken.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02481-683x1024.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1993</p>
              <h3 class="profile-card__name">Lukas Schmalenstroer</h3>
              <p class="profile-card__title">Sozialarbeiter · Jugendreferent · Fotograf · Berufungscoach</p>
              <p class="profile-card__text">Für Vielfalt und Toleranz einzustehen ist nicht optional. Ich mache bei „für Menschenwürde und Demokratie" mit, weil dieser Ansatz positiv ist: eher FÜR etwas zu stehen, als dagegen.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/11/LS_06793-683x1024.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1952</p>
              <h3 class="profile-card__name">Christoph Henn</h3>
              <p class="profile-card__title">Dipl. Agr. Ing. · Sonderschullehrer & Heilpädagoge (i.R.)</p>
              <p class="profile-card__text">Ich erlebe, dass Menschlichkeit und gegenseitige Akzeptanz alles andere als selbstverständlich sind. Deshalb engagiere ich mich dafür, dass Menschen unabhängig von Herkunft oder Orientierung in wertschätzendem Miteinander leben können.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card profile-card--no-photo">
            <div class="profile-card__media profile-card__media--solid-teal" aria-hidden="true">
              <span class="crown-watermark" aria-hidden="true"></span>
            </div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Beispiel ohne Foto</p>
              <h3 class="profile-card__name">Person ohne Bild</h3>
              <p class="profile-card__title">Rolle / Untertitel</p>
              <p class="profile-card__text">Fallback-Variante, wenn kein Portrait verfügbar ist: die linke Fläche ist vollständig in CI-Farbe gefüllt (statt eines kreisförmigen Avatars).</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Personen / Profil-Elemente (vertikal)</h3>
        <div class="lb-cards-grid">

          <article class="profile-card profile-card--vertical">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02405-scaled.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1964</p>
              <h3 class="profile-card__name">Ralf Knoblauch</h3>
              <p class="profile-card__title">Tischler &middot; Dipl. Theologe &middot; Diakon &middot; Bildhauer</p>
              <p class="profile-card__text">Die unantastbare Würde des Menschen ist seit vielen Jahren mein Lebensthema.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card profile-card--vertical">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02379-scaled.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1970</p>
              <h3 class="profile-card__name">Anja Knoblauch</h3>
              <p class="profile-card__title">Dipl. Theologin &middot; Pastoralreferentin</p>
              <p class="profile-card__text">Mit meinem Engagement möchte ich einen nachhaltigen Beitrag zu einer solidarischen Gesellschaft leisten.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card profile-card--vertical">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02481-683x1024.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1993</p>
              <h3 class="profile-card__name">Lukas Schmalenstroer</h3>
              <p class="profile-card__title">Sozialarbeiter &middot; Jugendreferent &middot; Fotograf</p>
              <p class="profile-card__text">Für Vielfalt und Toleranz einzustehen ist nicht optional.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

          <article class="profile-card profile-card--vertical">
            <div class="profile-card__media" style="--profile-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/11/LS_06793-683x1024.jpg');" aria-hidden="true"></div>
            <div class="profile-card__body">
              <p class="profile-card__meta"><span class="crown-accent" aria-hidden="true"></span> Jahrgang 1952</p>
              <h3 class="profile-card__name">Christoph Henn</h3>
              <p class="profile-card__title">Dipl. Agr. Ing. &middot; Sonderschullehrer (i.R.)</p>
              <p class="profile-card__text">Ich engagiere mich dafür, dass Menschen in wertschätzendem Miteinander leben können.</p>
              <div class="profile-card__actions">
                <a class="btn btn--outline" href="#">Mehr erfahren</a>
                <a class="btn btn-crown" href="#"><span class="btn-crown__icon" aria-hidden="true"></span>Mach mit</a>
              </div>
            </div>
          </article>

        </div>
      </div>

    </section>

    <!-- ====================================================
         7. FORMULARE
         ==================================================== -->
    <section id="formulare" class="lb-section">
      <h2 class="lb-section__title">Formulare</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Standard-Felder</h3>
        <div class="lb-component__preview">
          <form class="demo-form" novalidate>
            <div class="form-group">
              <label class="form-label" for="lb-name">Name</label>
              <input class="form-input" type="text" id="lb-name" name="name" placeholder="Dein vollständiger Name">
            </div>
            <div class="form-group">
              <label class="form-label" for="lb-email">E-Mail-Adresse</label>
              <input class="form-input" type="email" id="lb-email" name="email" placeholder="deine@email.de">
            </div>
            <div class="form-group">
              <label class="form-label" for="lb-select">Bereich</label>
              <select class="form-input form-select" id="lb-select" name="category">
                <option value="">Bitte wählen …</option>
                <option>Kirche &amp; Glaube</option>
                <option>Bildung &amp; Schule</option>
                <option>Kommunalpolitik</option>
                <option>Handwerk &amp; Beruf</option>
                <option>Gesundheit &amp; Soziales</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="lb-textarea">Nachricht</label>
              <textarea class="form-input form-textarea" id="lb-textarea" name="message" rows="5" placeholder="Deine Nachricht an uns …"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label" for="lb-file">Datei anhängen</label>
              <div class="form-file">
                <input class="form-file__input" type="file" id="lb-file" name="attachment">
                <label class="form-file__label" for="lb-file">
                  <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 16.5h14M10 3v10m-4-4 4-4 4 4"/></svg>
                  Datei auswählen oder hierher ziehen
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="form-checkbox">
                <input type="checkbox" name="consent">
                <span>Ich habe die <a href="#">Datenschutzerklärung</a> gelesen und stimme zu.</span>
              </label>
            </div>
            <button class="btn btn--primary" type="submit">Absenden</button>
          </form>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Fehler-States</h3>
        <div class="lb-component__preview">
          <form class="demo-form" novalidate>
            <div class="form-group form-group--error">
              <label class="form-label" for="lb-name-err">Name <span class="form-required" aria-hidden="true">*</span></label>
              <input class="form-input form-input--error" type="text" id="lb-name-err" name="name" value="" aria-describedby="lb-name-err-msg" aria-invalid="true">
              <p class="form-error-message" id="lb-name-err-msg" role="alert">Bitte gib deinen Namen ein.</p>
            </div>
            <div class="form-group form-group--error">
              <label class="form-label" for="lb-email-err">E-Mail <span class="form-required" aria-hidden="true">*</span></label>
              <input class="form-input form-input--error" type="email" id="lb-email-err" name="email" value="keine-email" aria-describedby="lb-email-err-msg" aria-invalid="true">
              <p class="form-error-message" id="lb-email-err-msg" role="alert">Bitte gib eine gültige E-Mail-Adresse ein.</p>
            </div>
            <div class="form-group form-group--success">
              <label class="form-label" for="lb-tel-ok">Telefon</label>
              <input class="form-input form-input--success" type="tel" id="lb-tel-ok" name="tel" value="+49 89 123456">
              <p class="form-success-message">Sieht gut aus!</p>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- ====================================================
         8. MACH-MIT-KOMPONENTEN
         ==================================================== -->
    <section id="mach-mit" class="lb-section">
      <h2 class="lb-section__title">Mach-mit-Komponenten</h2>

      <div class="lb-component">
        <h3 class="lb-component__label">Suchfeld</h3>
        <div class="lb-component__preview">
          <form class="mitmach-search" role="search" aria-label="Mitmach-Suche">
            <div class="mitmach-search__wrapper">
              <svg class="mitmach-search__icon" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="9" r="6"/><path d="m15 15 3 3" stroke-linecap="round"/></svg>
              <input class="mitmach-search__input" type="search" placeholder="Suche nach Möglichkeiten …" aria-label="Suche">
              <button class="mitmach-search__btn btn btn--primary" type="submit">Suchen</button>
            </div>
          </form>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Kategorie-Filter (Accordion)</h3>
        <div class="lb-component__preview">
          <div class="category-accordion" id="lb-category-accordion">

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="true" aria-controls="lb-cat-kirche">
                <span class="category-accordion__dot" style="background:var(--color-cat-kirche)"></span>
                Kirche &amp; Glaube
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel" id="lb-cat-kirche">
                <ul class="category-accordion__list">
                  <li><a href="#">Kirchenchor Herzo</a></li>
                  <li><a href="#">Gemeindeberatung</a></li>
                  <li><a href="#">Ökumenisches Frühstück</a></li>
                </ul>
              </div>
            </div>

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="false" aria-controls="lb-cat-bildung">
                <span class="category-accordion__dot" style="background:var(--color-cat-bildung)"></span>
                Bildung &amp; Schule
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel category-accordion__panel--closed" id="lb-cat-bildung">
                <ul class="category-accordion__list">
                  <li><a href="#">Nachhilfeprojekt</a></li>
                  <li><a href="#">Lesepatenschaften</a></li>
                </ul>
              </div>
            </div>

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="false" aria-controls="lb-cat-kommunal">
                <span class="category-accordion__dot" style="background:var(--color-cat-kommunal)"></span>
                Kommunalpolitik
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel category-accordion__panel--closed" id="lb-cat-kommunal">
                <ul class="category-accordion__list">
                  <li><a href="#">Stadtratssitzungen begleiten</a></li>
                  <li><a href="#">Bürgerinitiative Innenstadt</a></li>
                </ul>
              </div>
            </div>

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="false" aria-controls="lb-cat-handwerk">
                <span class="category-accordion__dot" style="background:var(--color-cat-handwerk)"></span>
                Handwerk &amp; Beruf
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel category-accordion__panel--closed" id="lb-cat-handwerk">
                <ul class="category-accordion__list">
                  <li><a href="#">Repair-Café</a></li>
                  <li><a href="#">Ausbildungsbegleitung</a></li>
                </ul>
              </div>
            </div>

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="false" aria-controls="lb-cat-gesundheit">
                <span class="category-accordion__dot" style="background:var(--color-cat-gesundheit)"></span>
                Gesundheit &amp; Soziales
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel category-accordion__panel--closed" id="lb-cat-gesundheit">
                <ul class="category-accordion__list">
                  <li><a href="#">Tafel-Helfer</a></li>
                  <li><a href="#">Begleitung älterer Menschen</a></li>
                </ul>
              </div>
            </div>

            <div class="category-accordion__item">
              <button class="category-accordion__trigger" aria-expanded="false" aria-controls="lb-cat-sonstiges">
                <span class="category-accordion__dot" style="background:var(--color-cat-sonstiges)"></span>
                Sonstiges
                <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
              </button>
              <div class="category-accordion__panel category-accordion__panel--closed" id="lb-cat-sonstiges">
                <ul class="category-accordion__list">
                  <li><a href="#">Offene Angebote</a></li>
                  <li><a href="#">Neue Ideen einbringen</a></li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="lb-component">
        <h3 class="lb-component__label">Mitmach-Karten-Grid</h3>
        <div class="lb-component__preview">
          <div class="mitmach-grid">

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-kirche)">Kirche &amp; Glaube</div>
              <h3 class="mitmach-card__title">Kirchenchor Herzo</h3>
              <p class="mitmach-card__text">Singen für die Gemeinschaft — jeden Mittwoch, 19 Uhr. Keine Vorkenntnisse nötig.</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">Wöchentlich</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Details</a>
              </div>
            </article>

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-bildung)">Bildung &amp; Schule</div>
              <h3 class="mitmach-card__title">Lesepatenschaften</h3>
              <p class="mitmach-card__text">Fördere Lesekompetenz bei Grundschulkindern — 1 Stunde pro Woche reicht.</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">Flexibel</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Details</a>
              </div>
            </article>

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-kommunal)">Kommunalpolitik</div>
              <h3 class="mitmach-card__title">Bürgerinitiative Innenstadt</h3>
              <p class="mitmach-card__text">Gestalte deine Stadt aktiv mit — bei Planungstreffen und öffentlichen Veranstaltungen.</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">Monatlich</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Details</a>
              </div>
            </article>

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-handwerk)">Handwerk &amp; Beruf</div>
              <h3 class="mitmach-card__title">Repair-Café</h3>
              <p class="mitmach-card__text">Reparieren statt wegwerfen — bringe dein Handwerk ein und hilf anderen.</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">2× im Monat</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Details</a>
              </div>
            </article>

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-gesundheit)">Gesundheit &amp; Soziales</div>
              <h3 class="mitmach-card__title">Tafel-Helfer</h3>
              <p class="mitmach-card__text">Unterstütze die Lebensmittelausgabe und helfe Menschen in schwierigen Situationen.</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">Wöchentlich</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Details</a>
              </div>
            </article>

            <article class="mitmach-card">
              <div class="mitmach-card__category" style="--cat-color:var(--color-cat-sonstiges)">Sonstiges</div>
              <h3 class="mitmach-card__title">Neue Ideen einbringen</h3>
              <p class="mitmach-card__text">Hast du eine eigene Idee? Wir sind offen für neue Projekte und Initiativen!</p>
              <div class="mitmach-card__footer">
                <span class="mitmach-card__tag">Jederzeit</span>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mitmach-card__link">Kontakt</a>
              </div>
            </article>

          </div>
        </div>
      </div>
    </section>

  

</main>
</div>

<?php get_footer(); ?>
