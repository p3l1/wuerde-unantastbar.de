<?php
/**
 * Title: Startseiten-Text
 * Slug: wuerde/startseite-text
 * Categories: text
 * Description: Einleitungstext der Startseite mit rundem Sticker, Grundidee-Link und „Idee teilen"-Button.
 */
$sticker = esc_url( get_theme_file_uri( 'assets/sticker-menschenwuerde.png' ) );
?>
<!-- wp:group {"className":"startseite-text","layout":{"type":"constrained"}} -->
<div class="wp-block-group startseite-text">

<!-- wp:image {"width":"120px","className":"startseite-text__sticker"} -->
<figure class="wp-block-image startseite-text__sticker" style="width:120px"><img src="<?php echo $sticker; ?>" alt="Ich wähle Menschenwürde"/></figure>
<!-- /wp:image -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Gemeinsam mit euch</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Seit Anfang 2024 engagiert sich unser „Verein für Menschenwürde und Demokratie“ dafür, dass die Würde eines jeden Menschen unantastbar ist und nicht zur Diskussion stehen darf. Viele wirken inzwischen deutschlandweit daran mit. <strong>Mehr als 50 000 Würdetafeln</strong> sind bereits gebrannt und in Politik, Bildung, Kultur und Gesellschaft sichtbar. Unzählige Aktionen haben schon dazu stattgefunden.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__cta-link"} -->
<p class="startseite-text__cta-link"><a href="#">Hier geht es zu unserer Grundidee →</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__muted"} -->
<p class="startseite-text__muted">Unsere Aktionen sind alle überparteilich und entsprechen dem Unparteilichkeitsgrundsatz.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__muted"} -->
<p class="startseite-text__muted">Die Würde aller Menschen ist in Artikel 1 unseres Grundgesetzes fest verankert und somit zentraler Bestandteil unserer Demokratie. Leider wird die Menschenwürde immer noch angetastet, sei es durch soziale Ungerechtigkeit, Diskriminierung oder politische Entwicklungen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__muted"} -->
<p class="startseite-text__muted">Wir danken allen, die sich durch die vielen verschiedenen Aktionen für die Menschenwürde einsetzen. Sei es durch das Brennen und Verteilen von Würdetafeln, das Aufhängen von Plakaten, die Organisation von Veranstaltungen und vieles mehr.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__lead-out"} -->
<p class="startseite-text__lead-out">Wir machen weiter und hoffen, ihr auch.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"startseite-text__mitmach"} -->
<p class="startseite-text__mitmach"><strong>Mach mit:</strong> Teile deine Idee für mehr Würde.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"btn-crown"} -->
<div class="wp-block-button btn-crown"><a class="wp-block-button__link wp-element-button" href="#">Idee teilen</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
