<main>

<h1>Frequently asked questions (FAQ):</h1>

<hr>


<h2>Warum unterscheiden sich die Conversions zwischen Compare und anderen Tracking Systemen?</h2>

<p>Die Berechnung der Conversions in Compare erfolgt nach folgendem Schema:<br/>
Einmal pro Nacht werden aus der Datenbank unseres SSO-Systems (Plenigo) die getätigten Käufe der letzten 5 Tage importiert. Dadurch erhalten wir ein kontinuierliches Bild aller getätigter Käufe seit mitte März 2021.</p>

<p>Im Plenigo selbst wird zu jedem Kauf die KaufURL gespeichert, welche in der Regel auch Artikel-ID enthält. Über diese ID kann ein getätigter Kauf einem Artikel zugewiesen werden. Die Summe aller getätigter Käufe auf diesem Artikel ergibt dann die Anzahl der Conversions. Wenn der Kunde nun sein Abo im Plenigo kündigt, wird dies als Kündigung am Artikel gewertet. Diese Bestellungen sind in der Detailansicht am Artikel einzeln auswertbar.
</p>

<p>Leider kam es vor, das die KaufURL nicht korrekt an Plenigo übertragen wurde (zum Beispiel, wenn die Artikel URL länger als 200 Zeichen ist). In dem Fall wissen wir leider überhaupt nicht wie die Conversion zuzuordnen ist. Ein absolut 100% genaues Tracking werden wir daher in naher Zukunft nicht realisieren können. Dennoch ist das SSO System Plenigo (also Compare) grundsätzlich die beste Datenquelle die wir haben.</p>

<p>Weiterhin kann es natürlich auch sein das in dem anderen Tracking System (Linkpulse, Kilkaya oder Google Analytics) eine Fehlzählung erfolgt ist. In der Regel sind das dann zu wenige Conversions z.B. wenn der Nutzer Browser Plugins installiert hat die ein Tracking gänzlich verhindern (z.B. bestimmte Adblocker). Eine zu höhe Conversion Zählung kann darauf zurückzuführen sein, das durch ein uns unbekanntes Fehlverhalten, eine Doppelbuchung ausgeführt wurde, die aber im Plenigo nur einmal gewertet wird. Grundsätzlich gibt es zwischen den Trackingverfahren auch bei Pageviews und Subscriberviews immer mal Abweichungen im Bereich von 5-10%.</p>

<hr>

<h2>Warum unterscheiden sich Klickzahlen und Subscriberviews in anderen Dashboard Systemen?</h2>
<p>Da Compare hauptsächlich dazu dient Artikel bzw. Gruppierungen von Artikeln (Ressorts, Audiences) miteinander zu vergleichen, werden in den meisten Compare Statistiken nur die Daten von den Artikeln selbst angezeigt. Externe Dashboards können die Gesamtstatistik also die Kombination von Artikeln und Überschsichtsseiten (Indexseiten) darstellen.</p> 

<hr>

<h2>Wie werden Subscriberviews gemesssen?</h2>
<p>Ein Subscriberview entsteht immer dann, wenn ein Nutzer im eingeloggten zustand, einen Seitenaufruf erzeugt. Dabei ist es egal ob dieser Aufrufe auf einem Artikel oder auf einer Übersichtsseite erzeugt wird. Die Subscriberviews eines Artikels weisen somit die Ansichten eines Textes hinter der Paywall aus.</p>

<hr>

<h2>Warum wird mein Artikel nicht dem richtigen Ressort zugewiesen?</h2>
<p>Im Compare Tool werden alle Artikel über RSS-Feeds importiert. Dabei wird jeder Artikel nur einmal mit seiner Artikel-ID abgespeichert, um keine Dubletten zu erzeugen. In einigen Fällen kommt es vor, das ein Artikel in mehreren RSS-Feeds vorhanden ist. Zum Beispiel wenn dieser mehreren Ressorts zugewiesen wurde. In dem Fall wird immer das Artikel Ressort des zuletzt importieren RSS-Feeds gesetzt.</p>

<hr>

<h2>Übersicht der Themengebiete aus dem DPA-Drive Projekt</h2>

<details>
<summary><b>Politik:</b> Politiker und politische Entscheidungsprozesse</summary>
- Themen, bei denen die Politik im Vordergrund steht<br />
- Statements von Politikern, Interviews (egal zu welchem Thema)<br />
- Neue Gesetzesvorhaben oder Verordnungen<br />
- Wahlen und Wahlergebnisse<br />
- Städtetag, Ministerien, Behörden wie Bauamt<br />
- Unruhen, Kriege, Militär und Konflikte<br />
- Finanzierungsvorhaben, die politische Institutionen betreffen, z.B. Behördern, Ministerien, Ämter
</details>

<details>
<summary><b>Fußball:</b> Alles rund um Fußball</summary>
- Spielberichte, Analysen und Ankündigungen<br />
- Interviews mit Fußballspieler, Trainer, Experten<br />
- Hintergründe zu Vereinen, Spielern, Turnieren<br />
- Fußballkultur (z.B. Geschichte, Fankultur)<br />
- Fußball-, Vereinsgeschichte /Jubiläen<br />
- Social-Media-Geschichten der Fußballer<br />
- Fußballpolitik, DFB etc.<br />
- Porträts über Menschen im Fußball werden in der Kategorie "Leute" eingeordnet<br />
</details>


<details>
<summary><b>Sport:</b> Alles außer Fußball</summary>
- analog zu Fußball für andere Sportarten
</details>

<details>
<summary><b>Verkehr:</b> Straßen, Staus, Bahn, etc.</summary>
- (keine Unfälle)<br />
- Verkehrsplanung: Bau neuer Straßen, Brücken, Tunnel, Bahnhöfe und Bushaltestellen, Verkehrsinfrastrukturprojekte<br />
- E-Mobilitätsnetze<br />
- Flughäfen, Häfen und Wasserwege<br />
- Stauprognosen und -meldungen<br />
- Baustellen<br />
- Straßensperrungen<br />
- Verkehrsüberwachung / Maut<br />
- Verspätungen und Ausfälle bei Bahn, Bus, Flugverkehr<br />
</details>

<details>
<summary><b>Infrastruktur:</b> Neuigkeiten zur Infrastruktur und lokale Umgebung</summary>
- Abriss von Gebäuden, Bauprojekte, Neu-/Umbau von Schulen, etc. (außer Wohngebäude!)<br />
- neue Packstationen, Gewerbeflächen<br />
- Spielplätze, Glascontainer, etc.<br />
- Energie- und Wasserversorgung<br />
- Infrastrukturgebühren<br />
- Mobilfunknetz, Telefonnetz, Internet-/TV-Anschluss, public WiFi etc.<br />
</details>

<details>
<summary><b>Wohnen:</b> Alles rund um Wohnimmobilien</summary>
- Entwicklung von Immobilienpreisen, neue Gesetze für Immobilien<br />
- Ausweisung von neuem Bauland<br />
- Immobilienfinanzierung, energetische Sanierung, Tipps zum Umbau<br />
- Bauprojekte von Wohngebäuden<br />
</details>

<details>
<summary><b>Gesundheit:</b> Alles rund um Körper und Fitness</summary>
- Krankheiten und Symptome (Beschreibung, Verlauf, Tipps zur Vermeidung, etc.)<br />
- Fitness (Ratschläge, Berichte, etc.)<br />
- Medikamente, Therapien, Diagnosen, Impfungen (nur gesundheitliche Aspekte!), Pflegemaßnahmen<br />
- Gesund bleiben (Sport, Bewegung, gesunde Ernährung, Abnehmen, Psyche, Früherkennung&Vorsorge, Körperpflege, Schlaf, Sex, Rauchstopp, Alkoholstopp, Sport & Bewegung)<br />
- Mein Körper (Gelenke, Herz, Immunsystem, Darm, Augen, Zähne etc.)<br />
- E-Health (Telemedizin, Rezept per App)<br />
- Familie ( Kinderwunsch, Schwangerschaft, Kindergesundheit, Palliativversorgung)<br />
- Arzneimittel, Heilmittel , Homöopathie, Naturheilkunde<br />
- Organspende<br />
</details>

<details>
<summary><b>Leute:</b> Geschichten über Menschen und herausgestellte Persönlichkeiten</summary>
- Geschichten, in denen ein bestimmter Mensch im Vordergrund steht (oder eine Gruppe von Menschen)<br />
- Figuren des öffentlichen Interesses (auch lokal)<br />
- In diesem Fall spielt der Kontext keine Rolle (z.B. ein Sportler-Porträt wird hier eingeordnet und nicht unter Sport)<br />
- Prominentenberichterstattung<br />
</details>

<details>
<summary><b>Religion: :</b> Glaube und Kirchen alles rund um Kirchen und ähnliche Organisationen</summary>
- Glaubens- und spirituelle Themen<br />
</details>

<details>
<summary><b>Soziales:</b> Alles um das Zusammenleben von Menschen</summary>
- Vereine (außer Fußball und sonstige Sportvereine), Organisationen, Ehrenamt<br />
- Zusammenleben der Menschen<br />
- Ehrenamtliches Engagement (Übungsleiter, Pauschale, Steuererklärung, Versicherung, Tätigkeit finden)<br />
- Hilfe für Bedürftige in allen Lebenslagen, Unterstützung für Menschen mit Behinderung, Hilfe für Asylbewerber, Flüchtlingsstrom und Integrationshilfe, Seniorenhilfe
(Seniorenberatung und Angebote für Senioren), Wohnungslosenhilfe, Kinder-, Frauen- und Familienhilfe, Kältebusse<br />
- Katastrophenschutz, Freiwillige Feuerwehr, THW, Johanniter, DRK, Malteser Hilfsdienst, DLRG, Entwicklungshelfer<br />
- Freiwilliges Soziales Jahr, Freiwilliges Ökologisches Jahr<br />
- Paten (Lernpaten, Kinderpate, Jobpate)<br />
- (wird eine Persönlichkeit in einer Organisation vorgestellt, ist es "Leute", z.B. die neue Vorsitzende)<br />
</details>

<details>
<summary><b>Wissenschaft:</b> Forschung und neue Erkenntnisse</summary>
- Themen und Ereignisse aus der Wissenschaft und Hochschulen (bei hochschulpolitischen Entscheidungen nur solche Artikel, die die Forschung betreffen)<br />
- Wissenschaftliche Studien aus allen Bereichen (z.B. Wirkung von Doping-Substanzen, Erforschung neuer Impfstoffe, (Astro-)Physik, Klimawandel und Ökosystem, Krebsrisiko,
Archäologie, Chemie, Computerspiele, neue Tierarten etc.)<br />
- Social Media in der Forschung<br />
- Hintergrund-Artikel mit wissenschaftlicher Begründung des Themas<br />
</details>

<details>
<summary><b>Unternehmen:</b> Nachrichten und Berichte über Unternehmen</summary>
- Spezifische Unternehmen über alle Branchen (vom lokalen Einzelhändler und Restaurant bis zum globalen Konzern)<br />
- Eröffnungen, Insolvenzen, Schließungen, Firmenjubiläen, wirtschaftliche Lage, Stellenabbau, Firmenporträts, Management, Technologie und Erfolg<br />
- Quartalszahlen, Unternehmenszahlen, Gewinnzahlen<br />
- Werden Unternehmen beispielhaft (und auswechselbar) für wirtschaftliche Zusammenhänge dargestellt, fällt der Artikel in die Kategorie Wirtschaft<br />
</details>

<details>
<summary><b>Wirtschaft:</b> Größere und volkswirtschaftliche Themen</summary>
- Berichte über aktuelle Wirtschaftsfragen (Wachstum, Arbeitslosigkeit, Inflation, etc.)<br />
- Berichte über Auswirkungen aktueller Situationen auf Unternehmen, z.B. "Senkt die Maskenpflicht den Umsatz im Einzelhandel?"<br />
- Auswirkungen der Politik auf die Wirtschaft. So erfährt man beispielsweise, welche Bedeutung das Hartz-Konzept und die LKW-Maut für die Wirtschaft haben und wie sie die
Entwicklung in Deutschland beeinflussen könnten.<br />
- Finanzmärkte, Börsen, Zinsen<br />
</details>

<details>
<summary><b>Kriminalität:</b> Verbrechen und Polizeiarbeit</summary>
- Kriminalfälle<br />
- Polizeiliche Ermittlungen<br />
- von der Tat bis zur Verhaftung<br />
- Terrorismus<br />
</details>

<details>
<summary><b>Justiz:</b> Gerichtsverhandlungen - Gerichtsverhandlungen</summary>
- Hintergründe zur Tat, auch Reportagen in den Milieus<br />
</details>

<details>
<summary><b>Katastrophen:</b> Unfälle und Naturkatastrophen</summary>
- Unfälle aller Art (Verkehrsunfälle, Brände, Explosionen, Grubenunglücke, Flugzeugabstürze)<br />
- Hungersnot, Überschwemmungen, Stürme, Vulkanausbrüche, Lawinen, Waldbrände, etc.<br />
- historische Katastrophen<br />
</details>

<details>
<summary><b>Bildung:</b> Kita, Schule, Ausbildung und Universität</summary>
- Berichte aus Bildungsinstitutionen (z.B. Unterrichtsausfall, Schulreformen, Ereignisse an Schulen)<br />
- Bildungsstudien (z.B. Pisa, Use The News)<br />
- Auswirkungen von Baumaßnahmen in Schulen auf Schüler und Eltern<br />
</details>

<details>
<summary><b>Erziehung:</b> Erziehung durch die Eltern - Erziehungstipps für Eltern</summary>
- Vorbereitung auf das Leben<br />
</details>

<details>
<summary><b>Kultur:</b> Unterhaltung</summary>
- Klassische Kultur (Theater, Oper, neue Bücher, etc.)<br />
- Populäre Kultur (z.B. Konzerte, neue Alben)<br />
- Filme, Serien, Kino, TV<br />
- Internet, Social Media, Influencer, Trends<br />
- Hobbys<br />
- Lesungen<br />
</details>

<details>
<summary><b>Verbraucher:</b> Was mein Leben praktischer macht</summary>
- Tipps (z.B. Steuersparen, …)<br />
- Strompreise, Benzinpreise, Lebensmittelpreise<br />
- Garten<br />
- Haustiere<br />
- Erfahrungen, um Fehler zu vermeiden<br />
- Wetterbericht<br />
</details>

<details>
<summary><b>Mix:</b> Einzelne Beiträge zu verschiedenen Themen aneinandergereiht</summary>
- z.B. Überblicksartikel, Leserbrief-Sammlungen etc.<br />
</details>


<!--
<p>
<b>Sport:</b>  Sport, Vereine, Vereinsleben<br/>

<b>Kultur:</b>  Events, Konzerte, Theater, Kneipen, Youtube, Netzwelt, Gaming, Kino<br/>

<b>Politik/Wirtschaft:</b>  Lokal- und Landespolitik, Corona-Bestimmungen, Wirtschaftsthemen, Investitionen, Verkehrsplanung usw.
Einkaufen + Wohnen, Strukturwandel<br/>

<b>Crime/Blaulicht:</b>  Diebstall, Unfälle, Feuerwehr, Verbrechen und darauffolgende Gerichtsprozesse<br/>

<b>Soziales:</b>  Kita gebühren, Wohnen und Umfeld, Feuerwehr bekommt neues Gebäude, Ortsbegehungen, Polizeichef wird 90, Kirchenthemen, Altersheime<br/>

<b>Gesundheit:</b>  Coronazahlen, Impfen, Krankenkassen, aber NICHT Politische Entscheidungen/Verordnungen, Ärzte der Region, Pflegekräfte<br/>

<b>Bildung:</b>  Digitale Schule, Uni, Forschungsthemen, Ausbildung<br/>

<b>Tourismus:</b>  Wandertouren, Restaurants, Sehenswürdigkeiten, Camping, Spreewald, Urlaub, Hotels Zoos, usw.<br/>

<b>Geschichte:</b>  Ortschronik, Schlösser & Denkmäler, Sublokale Geschichte<br/>

<b>Landwirtschaft:</b>  Vogelgrippe, Erntethemen, gerissene Schafe, Mastanlagen, Milchpreise<br/>

<b>Sonstiges:</b>  Hier landen meist Meinungsbeiträge z.B. Lesermeinungen oder Kommentare die sich nicht ohne weiteres zuordnen lassen
</p>
-->

<hr>



</main>
