# Bariis på Grandis?! – testkontrakt

## Status

- Produkt: Googa
- Testnavn i appen: `Bariis på Grandis?!`
- Undertittel: `Hvor langt har det gått?`
- Tilgang: gratis og uten innlogging
- Motorstatus: implementert
- Instrumentversjon: `bpg-0.3`
- Scoringsversjon: `bpg-profile-1`
- Målgruppe: voksne med norsk-somalisk tilknytning
- Tidsbruk: omtrent 6–8 minutter
- Omfang: 24 situasjoner med fire svaralternativer

## Formål

Testen er en frivillig, humoristisk refleksjonsprofil, ikke en vurdering av hvor «ekte» norsk eller somalisk noen er. Hverdagslige situasjoner gjør testen lett å dele, mens resultatet viser hvordan norsk hverdagskompetanse, somalisk språk og kultur og praktisk brobygging kan styrke hverandre.

Kjernebudskap:

> Litt humor, to kulturer og et helt vanlig liv. Du trenger ikke velge – kunnskap om den ene siden kan gjøre den andre lettere å forstå.

## Spørsmålsdekning

| Situasjonsområde | Symbol | Spørsmål |
|---|---:|---:|
| Norsk hverdagsliv | 🧤 | BPG-NO-01–08 |
| Somaliske røtter | 🍚 | BPG-HI-01–08 |
| Mellom to kulturer | 🌉 | BPG-BR-01–08 |

## Profilscoring

Hvert svaralternativ har en eksplisitt vektor med 0–3 poeng på tre uavhengige akser:

- Norsk hverdagsradar: vaner, språk og norsk hverdagsliv
- Somalisk rotfeste: språk, familie, historier og kultur
- Brobyggerblikk: å koble, forklare og bruke begge sider

Hver akse normaliseres mot den høyeste tilgjengelige poengsummen på samme akse. Resultatet viser derfor en profil, ikke en samlet rangering. Alternativrekkefølgen endrer ikke mappingen. Alle spørsmål må besvares; siste svar sender ikke testen automatisk, men åpner en egen resultatknapp.

Profilreglene er redaksjonelle og ikke normbaserte:

- Ministeren for Bariis på Grandis: minst 55 på begge kulturaksene og minst 60 på brobyggeraksen
- Ambassadøren for sambuus og dugnad: brobyggeraksen er høyest og minst 55, uten at fusion-regelen er nådd
- Sjefen for bariisgryta: somalisk rotfeste ligger minst 15 poeng over norsk hverdagsradar
- Direktøren for Grandis og Vipps: norsk hverdagsradar ligger minst 15 poeng over somalisk rotfeste
- Styrelederen for shaah og kaffe: jevn eller blandet profil som ikke treffer reglene over
- Kjøleskapsdetektiven mellom to land: gjennomsnitt under 35

De to laveste aksene utløser konkrete, ikke-moraliserende samtaleforslag.

Resultatet presenteres som en humoristisk utmerkelse med situasjons- og mathumor. Brukeren kan dele resultatkortet gjennom enhetens delingsark, Facebook, e-post eller iMessage, lagre bildekortet eller vise en QR-kode som åpner den offentlige testen. QR-lenken inneholder ingen svar eller prosenter.

### Testvektorer

| Vektor | Svarmønster | Forventning |
|---|---|---|
| Lav | laveste samlede vektor per spørsmål | Kjøleskapsdetektiven mellom to land |
| Høy bro | høyeste brobyggerverdi per spørsmål | Ministeren for Bariis på Grandis eller Ambassadøren for sambuus og dugnad, avhengig av kulturaksene |
| Norsk tyngde | alternativer med høy norsk og lav somalisk verdi | Direktøren for Grandis og Vipps |
| Somalisk tyngde | alternativer med høy somalisk og lav norsk verdi | Sjefen for bariisgryta |
| Balansert | alternerte valg med omtrent like kulturakser | Styrelederen for shaah og kaffe eller Ministeren for Bariis på Grandis |
| Ubesvart siste spørsmål | 23 komplette svar | Ingen resultatberegning |

## Lyd og språkstøtte

- Somali er hovedspråk.
- Norsk vises med en tydelig språkknapp eller som støtte under spørsmålet.
- Små høyttalersymboler spiller ferdiginnspilte MP3-filer, ikke dynamisk nettleseropplesning når filen er tilgjengelig.
- Brukeren velger mellom den kvinnelige stemmen Ubax (`so-SO-UbaxNeural`) og den mannlige stemmen Muuse (`so-SO-MuuseNeural`). Valget vises med to små illustrerte figurer og huskes bare lokalt på enheten.
- Begge stemmene er generert med tempo −10 prosent. Et kort stemmeeksempel spilles når brukeren bytter forteller.
- Tidligere lydfiler for spørsmål, skala og resultater tilhører et eldre instrument og skal ikke brukes med den nye teksten.
- De nye spørsmålene og svaralternativene bruker enhetens somaliske tale som reserve frem til de ferdiginnspilte lydfilene er koblet inn.
- Etter språkgodkjenning skal begge lydsett regenereres for intro, alle 24 spørsmål, alle 96 svaralternativer, seks profiler, tre resultatakser og tre mulige neste steg.

## Personvern og etikk

- Ingen spørsmål om klan, religion, partipreferanse, statsborgerskap eller juridisk status.
- Ingen fritekst, navn på slektninger eller konkrete politiske standpunkter.
- Svar og score behandles bare i nettleseren og sendes ikke til Googa-serveren i denne MVP-en.
- Resultatet skal ikke brukes til rangering eller automatiske avgjørelser om mennesker.

## Releaseporter

1. Språk- og kulturgjennomgang av alle påstander, svartekster, resultater og lydmanus.
2. Regenerering og lytting av samtlige Ubax- og Muuse-klipp etter godkjent manus.
3. Fem til ti kvalitative piloter på tvers av alder, kjønn, migrasjonshistorie og språkferdighet.
4. Revisjon av formuleringer som kan antyde at skriftlig kunnskap er mer verdifull enn muntlig kunnskap.
5. Mobil-, tastatur-, lyd- og resultatprøve før offentlig lansering.

## Kildepremisser

- <https://ich.unesco.org/en/oral-traditions-and-expressions-00053>
- <https://www.medietilsynet.no/digitale-medier/feil-desinformasjon/>
- <https://www.regjeringen.no/globalassets/departementene/ud/vedlegg/utvikling/partnerland/partner_somalia.pdf>
- <https://publications.iom.int/books/migration-development-horn-africa-health-expertise-somali-diaspora-finland>
