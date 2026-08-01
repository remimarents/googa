window.GOOGA_CULTURE_TEST = {
  version: 'mth-0.2-draft',
  titleSo: 'Laba dal, hal sheeko',
  titleNo: 'Mellom to hjem',
  subtitleSo: 'Tilmaamahaaga dhaqan ee Soomaali iyo Noorweey',
  subtitleNo: 'Ditt norsk-somaliske kulturkompass',
  introSo: 'Uma baahnid inaad kala doorato labadaada hoy. Aqoonta aad ka hesho midkood waxay kaa caawin kartaa inaad si fiican u fahanto kan kale.',
  introNo: 'Du trenger ikke velge mellom dine to hjem. Kunnskap fra det ene kan hjelpe deg å forstå det andre.',
  disclaimerSo: 'Kani waa is-milicsi loogu talagalay dadka waaweyn. Ma cabbirayo inta aad Soomaali ama Noorwiiji dhab ahaan u tahay, mana qiimeynayo fikirkaaga siyaasadeed. Jawaabahaagu qalabkaaga ayey ku sii jiraan.',
  disclaimerNo: 'Dette er en refleksjonstest for voksne. Den måler ikke hvor «ekte» norsk eller somalisk du er, og vurderer ikke politiske meninger. Svarene blir på enheten din.',
  dimensions: {
    norway: { icon: '🧭', so: 'Aqoonta bulshada Norway', no: 'Norsk samfunnskompetanse' },
    heritage: { icon: '🌿', so: 'Xiriirka dhaqanka Soomaalida', no: 'Somalisk kulturforankring' },
    research: { icon: '🔎', so: 'Aqoonta ilaha iyo dijitaalka', no: 'Kilde- og digital kompetanse' },
    world: { icon: '🌍', so: 'Soomaaliya iyo dunida', no: 'Somalia og verden' },
    bridge: { icon: '🌉', so: 'Isku-xirka labada dhinac', no: 'Brobygging' },
    transmission: { icon: '✨', so: 'Gudbinta aqoonta', no: 'Videreføring' }
  },
  scale: [
    { value: 4, icon: '●', so: 'Si buuxda ayay ii khusaysaa', no: 'Stemmer helt', audio: 'audio/culture-test/scale-4.mp3' },
    { value: 3, icon: '◕', so: 'Si fiican ayay ii khusaysaa', no: 'Stemmer ganske godt', audio: 'audio/culture-test/scale-3.mp3' },
    { value: 2, icon: '◑', so: 'Qayb ahaan ayay ii khusaysaa', no: 'Stemmer delvis', audio: 'audio/culture-test/scale-2.mp3' },
    { value: 1, icon: '◔', so: 'Wax yar ayay ii khusaysaa', no: 'Stemmer lite', audio: 'audio/culture-test/scale-1.mp3' },
    { value: 0, icon: '○', so: 'Ima khusayso', no: 'Stemmer ikke', audio: 'audio/culture-test/scale-0.mp3' }
  ],
  questions: [
    { id:'MTH-NO-01', domain:'norway', so:'Waxaan si fiican ula socon karaa wararka iyo doodaha bulshada Norway si aan u fahmo go’aannada aniga iyo qoyskayga saameeya.', no:'Jeg kan følge norsk nyhets- og samfunnsdebatt godt nok til å forstå beslutninger som påvirker meg og familien min.' },
    { id:'MTH-NO-02', domain:'norway', so:'Waan garanayaa sida loo helo adeegyada dadweynaha Norway, ururrada iyo goobaha aqoonta marka aan macluumaad ama caawimo u baahanahay.', no:'Jeg vet hvordan jeg finner fram til norske offentlige tjenester, organisasjoner og fagmiljøer når jeg trenger informasjon eller hjelp.' },
    { id:'MTH-NO-03', domain:'norway', so:'Waxaan fahmaa xeerar badan oo aan qornayn oo ka jira shaqada, iskaa wax u qabso iyo nolosha bulshada Norway.', no:'Jeg forstår mange av de uskrevne kodene i norsk arbeidsliv, frivillighet og samfunnsliv.' },
    { id:'MTH-NO-04', domain:'norway', so:'Waxaan ka qayb geli karaa dood bulshada Norway ka socota, anigoo u furan in waayo-aragnimooyin kale ay keenaan aragti kale.', no:'Jeg kan delta i en norsk samfunnssamtale og samtidig være åpen for at andre erfaringer gir et annet perspektiv.' },
    { id:'MTH-HI-01', domain:'heritage', so:'Waxaan fahmaa Af-Soomaali igu filan si aan u garto macnayaasha muhiimka ah marka qoyska ama waayeelku ka sheekaynayaan meelo, dad iyo dhacdooyin.', no:'Jeg forstår nok somali til å få med meg viktige nyanser når familie eller eldre forteller om steder, mennesker og hendelser.' },
    { id:'MTH-HI-02', domain:'heritage', so:'Waxaan aqaan sheekooyin, maahmaahyo, gabayo ama dhaqammo Soomaaliyeed oo aan ereyadayda ku sharxi karo.', no:'Jeg kjenner somaliske historier, ordtak, dikt eller tradisjoner som jeg kan forklare med egne ord.' },
    { id:'MTH-HI-03', domain:'heritage', so:'Waxaan waayeelka ama dadka aqoonta haya weydiiyaa asalka waayo-aragnimada qoyska iyo caadooyinka dhaqanka.', no:'Jeg spør eldre eller andre kunnskapsbærere om bakgrunnen for familiens erfaringer og kulturelle praksiser.' },
    { id:'MTH-HI-04', domain:'heritage', so:'Waxaan aqaan meelo, dad ama xilliyo muhiim u ah taariikhda qoyskayga ama bulshada deegaanka ee Soomaaliya.', no:'Jeg kjenner til viktige steder, personer eller perioder i familiens eller lokalsamfunnets historie i Somalia.' },
    { id:'MTH-RE-01', domain:'research', so:'Markaan wax baarayo, waxaan raadiyaa ilo ka baxsan qoraallada iyo muuqaallada baraha bulshada.', no:'Når jeg vil undersøke noe, finner jeg fram til mer enn innlegg og videoer i sosiale medier.' },
    { id:'MTH-RE-02', domain:'research', so:'Waxaan hubiyaa cidda ka dambaysa il, goorta la daabacay iyo in ilo kale oo lagu kalsoon yahay ay sidaas oo kale sheegayaan.', no:'Jeg sjekker hvem som står bak en kilde, når den ble publisert og om andre troverdige kilder sier det samme.' },
    { id:'MTH-RE-03', domain:'research', so:'Waxaan adeegsan karaa khariidado dijitaal ah, maktabado, kayd taariikheed, qalab turjumaad ama keyd cilmi-baaris si aan raad u sii raaco.', no:'Jeg kan bruke digitale kart, biblioteker, arkiver, oversettelsesverktøy eller forskningsbaser for å følge et spor videre.' },
    { id:'MTH-RE-04', domain:'research', so:'Badanaa waan kala saari karaa xog la xaqiijiyey, fasiraad siyaasadeed iyo warar aan la hubin.', no:'Jeg klarer som regel å skille mellom dokumenterte opplysninger, politiske tolkninger og ubekreftede rykter.' },
    { id:'MTH-WO-01', domain:'world', so:'Waxaan xaaladda Soomaaliya kala socdaa ilo badan oo leh aragtiyo kala duwan.', no:'Jeg følger med på utviklingen i Somalia gjennom flere kilder med ulike ståsteder.' },
    { id:'MTH-WO-02', domain:'world', so:'Waxaan dhacdo hadda ka jirta Soomaaliya ku xiriirin karaa taariikh, gobolka ama siyaasadda juqraafiyeed ee ka ballaaran.', no:'Jeg kan sette en aktuell hendelse i Somalia inn i en større historisk, regional eller geopolitisk sammenheng.' },
    { id:'MTH-WO-03', domain:'world', so:'Waxaan isku dayaa inaan fahmo farqiga u dhexeeya danaha federaalka, gobollada, deegaanka, qurbo-joogta iyo kuwa caalamiga ah ee arrimaha Soomaaliya.', no:'Jeg prøver å forstå forskjellen mellom føderale, regionale, lokale, diaspora- og internasjonale interesser i saker som gjelder Somalia.' },
    { id:'MTH-WO-04', domain:'world', so:'Waan garanayaa halka aan ka raadin karo hay’ado ama shabakado lagu kalsoon yahay haddii aan doonayo inaan aqoon, xiriir ama ka-qaybgal ku biiriyo Soomaaliya.', no:'Jeg vet hvor jeg kan lete etter seriøse miljøer, organisasjoner eller nettverk hvis jeg vil bidra med kunnskap, kontakt eller engasjement knyttet til Somalia.' },
    { id:'MTH-BR-01', domain:'bridge', so:'Marka qoysku xuso qof, meel ama dhacdo, waxaan adeegsadaa ilo Noorwiiji ama caalami ah si aan wax badan uga barto.', no:'Når familien nevner en person, et sted eller en hendelse, bruker jeg gjerne norske eller internasjonale kilder for å lære mer.' },
    { id:'MTH-BR-02', domain:'bridge', so:'Markaan helo il qoran oo Soomaaliya ku saabsan, waxaan la wadaagaa dadka yaqaan taariikhda afka ah si aan u barbar dhigno sheekooyinka.', no:'Når jeg finner en skriftlig kilde om Somalia, tar jeg den gjerne tilbake til personer som kjenner den muntlige historien og sammenligner versjonene.' },
    { id:'MTH-BR-03', domain:'bridge', so:'Aqoonta hay’adaha iyo doodaha bulshada Norway waxay iga caawisaa inaan su’aalo fiican ka weydiiyo siyaasadda iyo bulshada Soomaaliya.', no:'Kunnskap om norske institusjoner og samfunnsdebatt hjelper meg å stille bedre spørsmål om politikk og samfunn i Somalia.' },
    { id:'MTH-BR-04', domain:'bridge', so:'Aqoonta Af-Soomaaliga iyo dhaqanka waxay iga caawisaa inaan arko macnayaal ka maqnaan kara warbixinta Noorwiijiga ama caalamiga ah.', no:'Somalisk språk- og kulturkunnskap hjelper meg å oppdage nyanser som kan mangle i norsk eller internasjonal omtale.' },
    { id:'MTH-VI-01', domain:'transmission', so:'Waxaan aqoonta aan ka helo bulshada Norway iyo Soomaaliya la wadaagaa carruurta, qoyska ama dadka igu dhow.', no:'Jeg deler det jeg lærer om norsk og somalisk samfunn med barn, familie eller andre rundt meg.' },
    { id:'MTH-VI-02', domain:'transmission', so:'Waxaan sheeko qoys ku kaydin karaa cod, qoraal ama sawir, anigoo kala caddeynaya waxa la hubo iyo waxa ah hal sheeko oo afka laga soo weriyey.', no:'Jeg kan dokumentere en familiehistorie med lyd, tekst eller bilde og samtidig markere hva som er sikkert og hva som er én muntlig versjon.' },
    { id:'MTH-VI-03', domain:'transmission', so:'Waxaan jeclahay inaan isku xiro dad ku kala nool Norway iyo Soomaaliya oo wax iska baran kara ama hawl la taaban karo ka wada shaqayn kara.', no:'Jeg kobler gjerne mennesker i Norge og Somalia som kan lære av hverandre eller samarbeide om noe konkret.' },
    { id:'MTH-VI-04', domain:'transmission', so:'Waxaan hayaa ugu yaraan hal tallaabo oo dhab ah oo aan ku xoojin karo luqadda, taariikhda, aqoonta bulshada ama xiriirka Soomaaliya.', no:'Jeg har minst ett realistisk neste steg for å styrke språk, historieforståelse, samfunnskunnskap eller kontakt med Somalia.' }
  ],
  resultLevels: [
    { min:78, key:'connector', icon:'✨', so:'Isku-xiraha dhaqamada', no:'Kulturkobleren' },
    { min:58, key:'bridge', icon:'🌉', so:'Buundo-dhise firfircoon', no:'Aktiv brobygger' },
    { min:38, key:'doors', icon:'🚪', so:'Albaabo badan ayaa furmaya', no:'Flere dører åpner seg' },
    { min:0, key:'explorer', icon:'🧭', so:'Sahamiyaha xiisaha leh', no:'Nysgjerrig utforsker' }
  ],
  resultIntroSo: 'Natiijadu ma aha xukun aqoonsigaaga. Waxay muujinaysaa albaabada aad inta badan isticmaasho hadda iyo halka tallaabo yar ay xoojin karto xiriirka labada hoy.',
  resultIntroNo: 'Resultatet er ikke en fasit på identiteten din. Det viser hvilke dører du bruker mest akkurat nå, og hvor et lite neste steg kan styrke forbindelsen mellom de to hjemmene.',
  actions: {
    norway: { so:'Dooro hal arrin oo dadweyne oo Norway ka jirta oo qurbo-joogta saamaysa. Raadi cidda go’aamisa, cidda ay saamayso iyo ilaha asalka ah.', no:'Velg én norsk offentlig sak som berører diasporaen, og finn hvem som beslutter, hvem som påvirkes og hvor primærkildene ligger.' },
    heritage: { so:'Ka codso qof waayeel ah ama aqoon haya inuu ka sheekeeyo hal meel, oraah ama xusuus. Kaydi ereyada iyo macnaha ku xeeran.', no:'Be en eldre eller annen kunnskapsbærer fortelle om ett sted, uttrykk eller minne. Ta vare på både ordene og sammenhengen.' },
    research: { so:'Hal sheegasho ku baar il asal ah iyo ugu yaraan hal il madax-bannaan ka hor intaadan sii gudbin.', no:'Undersøk én påstand gjennom en primærkilde og minst én uavhengig kilde før du deler den videre.' },
    world: { so:'Hal arrin oo Soomaaliya ah kala soco ilo leh aragtiyo kala duwan, dabadeed qor waxa ay ku heshiiyaan iyo waxa ay ku kala duwan yihiin.', no:'Følg én Somalia-sak gjennom kilder med ulike ståsteder, og noter hva de er enige og uenige om.' },
    bridge: { so:'Ka qaado wada hadal qoys hal magac, meel ama raad taariikheed, kuna xiriiri khariidad, kayd ama cilmi-baaris.', no:'Ta ett familienavn, sted eller historisk spor fra en samtale og koble det til kart, arkiv eller forskning.' },
    transmission: { so:'La wadaag qoyska hal wax oo cusub oo aad ogaatay, kuna martiqaad inay ku daraan ama saxaan.', no:'Del én ny oppdagelse med familien, og inviter noen til å supplere eller korrigere den.' }
  }
};

window.GOOGA_CULTURE_TEST.questions.forEach((question, index) => {
  question.audio = `audio/culture-test/question-${String(index + 1).padStart(2, '0')}.mp3`;
});
