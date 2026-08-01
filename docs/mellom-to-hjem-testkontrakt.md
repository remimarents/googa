# Mellom to hjem – testkontrakt

## Status

- Produkt: Googa
- Testnavn i appen: `Laba dal, hal sheeko`
- Norsk arbeidstittel: `Mellom to hjem – ditt norsk-somaliske kulturkompass`
- Tilgang: eierforhåndsvisning
- Motorstatus: implementert
- Innholdsstatus: `draft`
- Instrumentversjon: `mth-0.2-draft`
- Målgruppe: voksne med norsk-somalisk tilknytning
- Tidsbruk: omtrent 6–8 minutter
- Omfang: 24 påstander, fire per delområde

Somalisk tekst og lydmanus skal kvalitetssikres av somalisk pedagog før testen åpnes for abonnenter. Testen er ikke normert eller psykometrisk validert.

## Formål

Testen er en frivillig refleksjonsprofil, ikke en vurdering av hvor «ekte» norsk eller somalisk noen er. Den skal vise hvordan norsk språk, samfunnskunnskap, kildekritikk og digitale verktøy kan åpne skriftlige kilder om Somalia, samtidig som somalisk språk, muntlig historie og kulturell kontekst gjør kildene lettere å forstå og utfordre.

Kjernebudskap:

> Du trenger ikke velge mellom dine to hjem. Kunnskap fra det ene kan hjelpe deg å forstå det andre.

## Områder og scoring

| Område | Symbol | Påstander |
|---|---:|---:|
| Norsk samfunnskompetanse | 🧭 | MTH-NO-01–04 |
| Somalisk kulturforankring | 🌿 | MTH-HI-01–04 |
| Kilde- og digital kompetanse | 🔎 | MTH-RE-01–04 |
| Somalia og verden | 🌍 | MTH-WO-01–04 |
| Brobygging | 🌉 | MTH-BR-01–04 |
| Videreføring | ✨ | MTH-VI-01–04 |

Svarskalaen går fra 0 (`Stemmer ikke`) til 4 (`Stemmer helt`). Tre hovedakser vises i resultatet:

- Norge og verktøy: norsk samfunnskompetanse + kilde- og digital kompetanse
- Somalia og kulturarv: somalisk kulturforankring + Somalia og verden
- Bro i praksis: brobygging + videreføring

Resultatoverskriftene er redaksjonelle, ikke normbaserte: Nysgjerrig utforsker, Flere dører åpner seg, Aktiv brobygger og Kulturkobleren. De to laveste delområdene utløser konkrete, ikke-moraliserende forslag.

## Lyd og språkstøtte

- Somali er hovedspråk.
- Norsk vises med en tydelig språkknapp eller som støtte under spørsmålet.
- Små høyttalersymboler spiller ferdiginnspilte MP3-filer, ikke dynamisk nettleseropplesning når filen er tilgjengelig.
- Brukeren velger mellom den kvinnelige stemmen Ubax (`so-SO-UbaxNeural`) og den mannlige stemmen Muuse (`so-SO-MuuseNeural`). Valget vises med to små illustrerte figurer og huskes bare lokalt på enheten.
- Begge stemmene er generert med tempo −10 prosent. Et kort stemmeeksempel spilles når brukeren bytter forteller.
- Begge lydsett inneholder intro, personvernforklaring, alle 24 spørsmål, alle fem svaralternativer, fire resultatnivåer, tre resultatakser og seks mulige neste steg.

## Personvern og etikk

- Ingen spørsmål om klan, religion, partipreferanse, statsborgerskap eller juridisk status.
- Ingen fritekst, navn på slektninger eller konkrete politiske standpunkter.
- Svar og score behandles bare i nettleseren og sendes ikke til Googa-serveren i denne MVP-en.
- Resultatet skal ikke brukes til rangering eller automatiske avgjørelser om mennesker.

## Releaseporter

1. Språk- og kulturgjennomgang av alle påstander, svartekster, resultater og lydmanus.
2. Regenerering og lytting av samtlige Ubax-klipp etter godkjent manus.
3. Fem til ti kvalitative piloter på tvers av alder, kjønn, migrasjonshistorie og språkferdighet.
4. Revisjon av formuleringer som kan antyde at skriftlig kunnskap er mer verdifull enn muntlig kunnskap.
5. Mobil-, tastatur-, lyd- og resultatprøve før offentlig lansering.

## Kildepremisser

- <https://ich.unesco.org/en/oral-traditions-and-expressions-00053>
- <https://www.medietilsynet.no/digitale-medier/feil-desinformasjon/>
- <https://www.regjeringen.no/globalassets/departementene/ud/vedlegg/utvikling/partnerland/partner_somalia.pdf>
- <https://publications.iom.int/books/migration-development-horn-africa-health-expertise-somali-diaspora-finland>
