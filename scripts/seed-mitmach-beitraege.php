<?php
// ABOUTME: Einmal-Script zum Befüllen der DB mit Beispiel-Mitmach-Beiträgen.
// ABOUTME: Via WP-CLI ausführen: make wp CMD="eval-file /scripts/seed-mitmach-beitraege.php"

$beitraege = [
    [
        'title'     => 'Würde-Ausstellung im Stadtmuseum',
        'excerpt'   => 'Eine Wanderausstellung mit Kunstwerken und Texten zur Unantastbarkeit der Menschenwürde — für alle zugänglich und kostenlos.',
        'content'   => '<p>Die Ausstellung gastiert im Stadtmuseum Erfurt und zeigt Werke lokaler Künstlerinnen und Künstler. Der Eintritt ist frei. Schulklassen können kostenlose Führungen buchen.</p>',
        'kategorie' => 'kunst-kultur',
        'ort'       => 'Erfurt',
        'lat'       => 50.9848,
        'lng'       => 11.0299,
    ],
    [
        'title'     => 'Straßentheater: Würde sichtbar machen',
        'excerpt'   => 'Kurze Theaterstücke auf dem Wochenmarkt, die alltägliche Situationen aufzeigen, in denen Würde verletzt oder gestärkt wird.',
        'content'   => '<p>Das Ensemble tritt jeden ersten Samstag im Monat auf dem Marktplatz auf. Passantinnen und Passanten sind eingeladen, mitzumachen und ihre eigene Geschichte zu erzählen.</p>',
        'kategorie' => 'kunst-kultur',
        'ort'       => 'Weimar',
        'lat'       => 50.9795,
        'lng'       => 11.3235,
    ],
    [
        'title'     => 'Gesprächskreis: Was bedeutet Würde für mich?',
        'excerpt'   => 'Monatlicher offener Gesprächskreis für alle, die über Menschenwürde, Demokratie und gesellschaftlichen Zusammenhalt nachdenken wollen.',
        'content'   => '<p>Jeden dritten Mittwoch im Monat, 19 Uhr, im Café Zeitgeist. Keine Vorkenntnisse nötig — nur Gesprächsbereitschaft und Offenheit.</p>',
        'kategorie' => 'gespraech',
        'ort'       => 'Leipzig',
        'lat'       => 51.3397,
        'lng'       => 12.3731,
    ],
    [
        'title'     => 'Diskussionsabend: Würde in der Pflege',
        'excerpt'   => 'Pflegekräfte, Angehörige und Interessierte diskutieren, wie Würde im Pflegealltag gelebt werden kann — und was sich ändern muss.',
        'content'   => '<p>Eine Kooperation zwischen dem Pflegestützpunkt und dem Verein. Der Abend ist offen für alle. Anmeldung nicht erforderlich.</p>',
        'kategorie' => 'gespraech',
        'ort'       => 'Dresden',
        'lat'       => 51.0504,
        'lng'       => 13.7373,
    ],
    [
        'title'     => 'Würde-Tafeln am Bahnhof',
        'excerpt'   => 'Große Würdetafeln mit kraftvollen Botschaften wurden am Hauptbahnhof aufgehängt — täglich sehen sie Tausende Reisende.',
        'content'   => '<p>Die Tafeln wurden in Kooperation mit der Deutschen Bahn und der Stadt realisiert. Sie bleiben ein Jahr hängen und wandern danach in andere Städte.</p>',
        'kategorie' => 'strasse',
        'ort'       => 'Berlin',
        'lat'       => 52.5251,
        'lng'       => 13.3694,
    ],
    [
        'title'     => 'Flashmob: Würde gehört uns allen',
        'excerpt'   => 'Rund 80 Menschen versammelten sich auf dem Römer und hielten gemeinsam Schilder mit Würde-Botschaften in die Höhe — still, würdevoll, wirkungsvoll.',
        'content'   => '<p>Der Flashmob dauerte 15 Minuten und wurde von vielen Passantinnen spontan gefilmt. Mehrere Videos wurden viral geteilt.</p>',
        'kategorie' => 'strasse',
        'ort'       => 'Frankfurt am Main',
        'lat'       => 50.1109,
        'lng'       => 8.6821,
    ],
    [
        'title'     => 'Würde-Quiz für Familien',
        'excerpt'   => 'Ein spielerisches Quiz-Format für Familien mit Kindern ab 8 Jahren — Würde und Demokratie mit Spaß erleben.',
        'content'   => '<p>Das Quiz-Set kann kostenlos beim Verein ausgeliehen werden. Es enthält 60 Fragen in drei Schwierigkeitsstufen sowie ein Würfelspiel.</p>',
        'kategorie' => 'spiel-spass',
        'ort'       => 'München',
        'lat'       => 48.1371,
        'lng'       => 11.5754,
    ],
    [
        'title'     => 'Würde-Parcours für Jugendliche',
        'excerpt'   => 'Ein Outdoor-Parcours, der Jugendliche durch praktische Aufgaben für das Thema Menschenwürde sensibilisiert.',
        'content'   => '<p>Der Parcours wurde speziell für Jugendgruppen ab 12 Jahren entwickelt und kann als Tagesveranstaltung gebucht werden. Material wird gestellt.</p>',
        'kategorie' => 'spiel-spass',
        'ort'       => 'Stuttgart',
        'lat'       => 48.7758,
        'lng'       => 9.1829,
    ],
    [
        'title'     => 'Projektwoche: Würde an unserer Schule',
        'excerpt'   => 'Eine ganze Schulwoche lang haben sich Schülerinnen und Schüler mit dem Thema Menschenwürde beschäftigt — in Kunst, Ethik, Geschichte und Sport.',
        'content'   => '<p>Das Konzept der Projektwoche steht allen Schulen kostenlos zur Verfügung. Der Verein unterstützt bei der Vorbereitung und schickt auf Wunsch eine Referentin.</p>',
        'kategorie' => 'bildung',
        'ort'       => 'Hannover',
        'lat'       => 52.3759,
        'lng'       => 9.7320,
    ],
    [
        'title'     => 'Würde im Unterricht: Materialien für Lehrkräfte',
        'excerpt'   => 'Kostenlose Unterrichtsmaterialien für alle Klassenstufen — entwickelt von Pädagoginnen und Pädagogen, erprobt im Schulalltag.',
        'content'   => '<p>Die Materialien decken die Themen Grundgesetz, Menschenrechte, Empathie und Zivilcourage ab. Jetzt als PDF und als gedrucktes Heft erhältlich.</p>',
        'kategorie' => 'bildung',
        'ort'       => 'Hamburg',
        'lat'       => 53.5753,
        'lng'       => 10.0153,
    ],
    [
        'title'     => 'Würde-Frühstück im Frauenhaus',
        'excerpt'   => 'Gemeinsames Frühstück und offene Gesprächsrunde für Bewohnerinnen und ehrenamtliche Helferinnen — jeden Sonntag.',
        'content'   => '<p>Das Frühstück wird von Vereinsmitgliedern organisiert und finanziert. Anmeldungen für Ehrenamtliche sind jederzeit möglich.</p>',
        'kategorie' => 'soziales',
        'ort'       => 'Köln',
        'lat'       => 50.9333,
        'lng'       => 6.9500,
    ],
    [
        'title'     => 'Würde-Botschafter in der Tafel',
        'excerpt'   => 'Freiwillige begleiten die Ausgabe in der Lebensmitteltafel und sorgen dafür, dass alle Gäste mit Respekt und Würde behandelt werden.',
        'content'   => '<p>Die Botschafterinnen und Botschafter nehmen an einem kurzen Einführungsseminar teil. Einsätze sind flexibel planbar — auch kurzfristig.</p>',
        'kategorie' => 'soziales',
        'ort'       => 'Nürnberg',
        'lat'       => 49.4521,
        'lng'       => 11.0767,
    ],
    [
        'title'     => 'Würde im Betrieb: Workshopreihe für Teams',
        'excerpt'   => 'Drei halbtägige Workshops für Unternehmen — wie können wir Würde, Respekt und Fairness aktiv in unsere Unternehmenskultur einbringen?',
        'content'   => '<p>Die Workshops werden von zertifizierten Trainerinnen durchgeführt und können inhouse gebucht werden. Für Unternehmen unter 20 Mitarbeitenden sind sie kostenlos.</p>',
        'kategorie' => 'betriebe',
        'ort'       => 'Düsseldorf',
        'lat'       => 51.2217,
        'lng'       => 6.7762,
    ],
    [
        'title'     => 'Handwerksbetrieb als Würde-Ort',
        'excerpt'   => 'Eine Schreinerei in Freiburg hängt die Würdetafel im Eingangsbereich auf und macht sie zum Gesprächsthema — mit Kunden und Mitarbeitenden.',
        'content'   => '<p>Inhaber Klaus M. erklärt: „Würde ist für uns kein abstrakter Begriff, sondern gelebter Alltag. Die Tafel erinnert uns täglich daran." Andere Betriebe können die Aktion kostenlos übernehmen.</p>',
        'kategorie' => 'betriebe',
        'ort'       => 'Freiburg im Breisgau',
        'lat'       => 47.9990,
        'lng'       => 7.8421,
    ],
];

$created = 0;
$skipped = 0;

foreach ( $beitraege as $data ) {
    // Duplikate vermeiden
    $existing = get_posts( [
        'post_type'   => 'wuerde_beitrag',
        'post_status' => 'publish',
        'title'       => $data['title'],
        'numberposts' => 1,
    ] );

    if ( ! empty( $existing ) ) {
        WP_CLI::line( "Übersprungen (existiert): {$data['title']}" );
        $skipped++;
        continue;
    }

    $post_id = wp_insert_post( [
        'post_type'    => 'wuerde_beitrag',
        'post_status'  => 'publish',
        'post_title'   => $data['title'],
        'post_excerpt' => $data['excerpt'],
        'post_content' => $data['content'],
    ], true );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( "Fehler bei: {$data['title']} — " . $post_id->get_error_message() );
        continue;
    }

    // Kategorie-Term sicherstellen und zuweisen
    $term = get_term_by( 'slug', $data['kategorie'], 'wuerde_kategorie' );
    if ( ! $term ) {
        WP_CLI::warning( "Kategorie nicht gefunden: {$data['kategorie']}" );
    } else {
        wp_set_post_terms( $post_id, [ $term->term_id ], 'wuerde_kategorie' );
    }

    // Ort-Term sicherstellen und zuweisen
    wp_set_post_terms( $post_id, [ $data['ort'] ], 'wuerde_ort' );

    // Koordinaten
    update_post_meta( $post_id, 'wuerde_lat', $data['lat'] );
    update_post_meta( $post_id, 'wuerde_lng', $data['lng'] );

    WP_CLI::success( "Erstellt: {$data['title']} ({$data['ort']})" );
    $created++;
}

WP_CLI::line( "" );
WP_CLI::line( "Fertig: {$created} erstellt, {$skipped} übersprungen." );
