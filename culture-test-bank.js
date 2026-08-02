window.GOOGA_CULTURE_TEST = {
  version: 'bpg-0.3',
  scoringVersion: 'bpg-profile-1',
  titleSo: 'Bariis på Grandis?!',
  titleNo: 'Bariis på Grandis?!',
  subtitleSo: 'Intee ayay gaartay?',
  subtitleNo: 'Hvor langt har det gått?',
  introSo: 'Kaftan yar, laba dhaqan iyo nolol dhab ah. Ma aha inaad mid doorato — aqoonta midkood waxay kaa caawin kartaa inaad kan kale si fiican u fahanto.',
  introNo: 'Litt humor, to kulturer og et helt vanlig liv. Du trenger ikke velge – kunnskap om den ene siden kan gjøre den andre lettere å forstå.',
  disclaimerSo: 'Tani waa tijaabo madadaalo iyo is-milicsi ah oo loogu talagalay dadka waaweyn. Ma cabbirayso inta aad Soomaali ama Noorwiiji dhab ahaan u tahay. Jawaabaha iyo natiijadu qalabkaaga ayey ku sii jiraan.',
  disclaimerNo: 'Dette er en humoristisk refleksjonstest for voksne. Den måler ikke hvor «ekte» norsk eller somalisk du er. Svarene og resultatet blir på enheten din.',
  dimensions: {
    norway: { icon:'🇳🇴', so:'Raadaarka nolosha Norway', no:'Norsk hverdagsradar', copySo:'Caadooyin, luqad iyo nolol maalmeed', copyNo:'Vaner, språk og norsk hverdagsliv' },
    heritage: { icon:'🌿', so:'Xididdada Soomaalida', no:'Somalisk rotfeste', copySo:'Af, qoys, sheekooyin iyo dhaqan', copyNo:'Språk, familie, historier og kultur' },
    bridge: { icon:'🌉', so:'Indhaha buundo-dhisaha', no:'Brobyggerblikk', copySo:'Labada dhinac isku xidh oo sharax', copyNo:'Koble, forklare og bruke begge sider' }
  },
  categories: {
    norway: { icon:'🧤', so:'Nolosha Norway', no:'Norsk hverdagsliv' },
    heritage: { icon:'🍚', so:'Xididdada Soomaalida', no:'Somaliske røtter' },
    bridge: { icon:'🌉', so:'Labada dhinac', no:'Mellom to kulturer' }
  },
  questions: [
    { id:'BPG-NO-01', category:'norway', so:'Sabtida waxaa jira dugnad xaafadda. Maxaad samaynaysaa?', no:'Borettslaget har dugnad på lørdag. Hva gjør du?', options:[
      {id:'a',icon:'🧤',so:'Waxaan imaadaa waqtigii, anigoo wata galoofyo iyo qorshe.',no:'Møter presis med arbeidshansker og en plan.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'🥟',so:'Waxaan keenaa sambuus; qof kale ha qaado rake-ga.',no:'Tar med sambuus – noen andre får ta riva.',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'📱',so:'Marka hore waxaan weydiiyaa waxa “dugnad” dhab ahaan yahay.',no:'Spør først hva «dugnad» egentlig innebærer.',score:{norway:1,heritage:1,bridge:2}},
      {id:'d',icon:'🌧️',so:'Waxaan rajaynayaa roob badan iyo in la baajiyo.',no:'Håper på styrtregn og avlysning.',score:{norway:0,heritage:0,bridge:0}}
    ]},
    { id:'BPG-NO-02', category:'norway', so:'Berri waxaad u baahan tahay qado shaqo. Maxaa ku jira boorsada?', no:'Du trenger lunsj på jobb i morgen. Hva havner i veska?', options:[
      {id:'a',icon:'🥪',so:'Rooti la diyaariyey habeenkii hore.',no:'Matpakke smurt kvelden før.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'🍚',so:'Bariiskii shalay — wuu ka fiican yahay rooti qalalan.',no:'Bariisrester – bedre enn tørre brødskiver.',score:{norway:1,heritage:3,bridge:2}},
      {id:'c',icon:'🛒',so:'Waxaan wax ka iibsadaa kantinka marka aan gaadho.',no:'Kjøper noe i kantina når jeg kommer.',score:{norway:2,heritage:0,bridge:0}},
      {id:'d',icon:'🍱',so:'Rooti, bariis iyo wax kasta oo qaboojiyaha ku jira.',no:'Matpakke, bariis og det kjøleskapet ellers byr på.',score:{norway:2,heritage:2,bridge:3}}
    ]},
    { id:'BPG-NO-03', category:'norway', so:'Qof aanad aqoon ayaa wiishka ka yidhaahda: “Cimilo xun maanta.” Maxaad tidhaahdaa?', no:'En fremmed i heisen sier: «For et vær i dag.» Hva svarer du?', options:[
      {id:'a',icon:'🌦️',so:'“Haa, laakiin toddobaadka dambe qorrax baa imanaysa.”',no:'«Ja, men det skal visst bli bedre neste uke.»',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'☀️',so:'Waxaan barbar dhigaa cimilada Norway iyo Soomaaliya.',no:'Sammenligner været i Norge og Somalia.',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'🤷',so:'Waan dhoola-caddeeyaa; cimiladu hadal ma mudna.',no:'Smiler høflig – været trenger ingen samtale.',score:{norway:0,heritage:1,bridge:0}},
      {id:'d',icon:'📲',so:'Waxaan furayaa app-ka cimilada si aan u hubiyo.',no:'Åpner værappen for å kontrollere påstanden.',score:{norway:2,heritage:0,bridge:1}}
    ]},
    { id:'BPG-NO-04', category:'norway', so:'Saaxiib ayaa Vipps kuugu soo dalbaday 47 karoon. Sideed uga jawaabtaa?', no:'En venn sender et Vipps-krav på 47 kroner. Hva gjør du?', options:[
      {id:'a',icon:'📲',so:'Waxaan bixiyaa 47-ka karoon isla markiiba.',no:'Betaler nøyaktig 47 kroner med én gang.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'☕',so:'Waxaan idhaahdaa: “Marka xiga shaah anigaa bixinaya.”',no:'Sier: «Jeg tar teen neste gang.»',score:{norway:1,heritage:2,bridge:2}},
      {id:'c',icon:'🧾',so:'Waxaan bixiyaa, laakiin waxaan la yaabaa xisaabta saxda ah.',no:'Betaler, men undrer meg over det nøyaktige regnskapet.',score:{norway:2,heritage:1,bridge:1}},
      {id:'d',icon:'🤝',so:'Waxaan bixiyaa oo kaftan ka sameeyaa farqiga dhaqanka.',no:'Betaler og gjør kulturforskjellen til en intern spøk.',score:{norway:3,heritage:2,bridge:3}}
    ]},
    { id:'BPG-NO-05', category:'norway', so:'17-ka May sidee ku bilaabataa?', no:'Hvordan starter 17. mai hos deg?', options:[
      {id:'a',icon:'🇳🇴',so:'Calan, jadwal iyo ogaanshaha meesha tareenku marayo.',no:'Flagg, tidsskjema og full kontroll på barnetoget.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'🥟',so:'Calan Norway ah iyo saxan sambuus ah.',no:'Norsk flagg og et fat sambuus.',score:{norway:3,heritage:3,bridge:3}},
      {id:'c',icon:'😴',so:'Waxaan hurdada ka kacaa marka sawaxanku bilaabmo.',no:'Våkner når korpset allerede er i gang.',score:{norway:1,heritage:0,bridge:0}},
      {id:'d',icon:'📺',so:'Waxaan ka daawadaa TV-ga oo qoyska sawirro u diraa.',no:'Ser det på TV og sender bilder til familien.',score:{norway:2,heritage:1,bridge:2}}
    ]},
    { id:'BPG-NO-06', category:'norway', so:'Waxaa lagugu martiqaaday hytta aan biyo lahayn. Maxaad diyaarsataa?', no:'Du er invitert på hytte uten innlagt vann. Hva forbereder du?', options:[
      {id:'a',icon:'🧦',so:'Dharka dhogorta, sharaabaad iyo shukulaato Kvikk Lunsj.',no:'Ullundertøy, ekstra sokker og Kvikk Lunsj.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'🫖',so:'Shaah, xawaash iyo dhar diiran.',no:'Termos med shaah, krydder og varme klær.',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'📶',so:'Marka hore waxaan hubiyaa Wi-Fi-ga.',no:'Sjekker først om det finnes Wi-Fi.',score:{norway:1,heritage:0,bridge:0}},
      {id:'d',icon:'🏠',so:'Waxaan soo jeediyaa inaan guriga magaalada joogno.',no:'Foreslår at vi heller blir hjemme i byen.',score:{norway:0,heritage:1,bridge:0}}
    ]},
    { id:'BPG-NO-07', category:'norway', so:'Martiqaadku wuxuu leeyahay saacadda 18:00. Goorma ayaad timaaddaa?', no:'Invitasjonen sier klokken 18.00. Når kommer du?', options:[
      {id:'a',icon:'⏰',so:'17:58, waxaan bannaanka sugaa laba daqiiqo.',no:'17.58, og venter ute i to minutter.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'💬',so:'Waxaan weydiiyaa: waqtiga Norway mise waqtiga qoyska?',no:'Spør: norsk tid eller familietid?',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'🚶',so:'Waxaan imaadaa marka dadka kale ay bilaabaan inay yimaadaan.',no:'Kommer når jeg ser at de andre begynner å dukke opp.',score:{norway:1,heritage:1,bridge:1}},
      {id:'d',icon:'🍲',so:'Waxa muhiimka ahi waa in cuntadu weli kulushahay.',no:'Det viktigste er at maten fortsatt er varm.',score:{norway:0,heritage:2,bridge:1}}
    ]},
    { id:'BPG-NO-08', category:'norway', so:'Qof ayaa fiidkii kuu soo bandhiga Grandiosa. Maxaad samaynaysaa?', no:'Noen serverer Grandiosa en sen kveld. Hva gjør du?', options:[
      {id:'a',icon:'🍕',so:'Waan cunaa — waa cunto caadi ah oo Norway ah.',no:'Spiser – det er jo norsk beredskapsmat.',score:{norway:3,heritage:0,bridge:1}},
      {id:'b',icon:'🌶️',so:'Waxaan raadiyaa basbaas ama xawaash.',no:'Leter etter basbaas eller annet krydder.',score:{norway:2,heritage:2,bridge:2}},
      {id:'c',icon:'🍚',so:'Waxaan weydiiyaa: “Bariis ma ku dari karnaa?”',no:'Spør: «Kan vi ha bariis på?»',score:{norway:2,heritage:3,bridge:3}},
      {id:'d',icon:'🙂',so:'Si edeb leh ayaan u cunaa, laakiin ma dalban doono.',no:'Spiser høflig, men bestiller det ikke selv.',score:{norway:1,heritage:1,bridge:1}}
    ]},

    { id:'BPG-HI-01', category:'heritage', so:'Qof weyn ayaa isticmaala maahmaah Soomaali ah oo aanad hubin. Maxaad samaynaysaa?', no:'En eldre bruker et somalisk ordtak du ikke er sikker på. Hva gjør du?', options:[
      {id:'a',icon:'💡',so:'Waan garanayaa maahmaahda oo tusaale ayaan ku daraa.',no:'Kjenner ordtaket og svarer med et eget eksempel.',score:{norway:0,heritage:3,bridge:2}},
      {id:'b',icon:'❓',so:'Waxaan weydiiyaa macnaha iyo goorta la isticmaalo.',no:'Spør hva det betyr og når det brukes.',score:{norway:1,heritage:3,bridge:3}},
      {id:'c',icon:'🙂',so:'Waan madax-ruxaa oo rajaynayaa inaan fahmay.',no:'Nikker og håper jeg forsto det riktig.',score:{norway:0,heritage:1,bridge:0}},
      {id:'d',icon:'🔎',so:'Waan raadiyaa, dabadeedna qof ayaan ka xaqiijiyaa.',no:'Søker det opp og kontrollerer betydningen med noen.',score:{norway:2,heritage:2,bridge:3}}
    ]},
    { id:'BPG-HI-02', category:'heritage', so:'Marti lama filaan ah ayaa albaabka timaadda. Maxaa dhacaya?', no:'Uventede gjester står på døra. Hva skjer?', options:[
      {id:'a',icon:'🫖',so:'Shaah iyo wax la cuno ayaa miiska saaran ka hor su’aalaha.',no:'Te og noe å spise kommer fram før spørsmålene.',score:{norway:0,heritage:3,bridge:1}},
      {id:'b',icon:'☕',so:'Waxaan bixiyaa shaah iyo qaxwo, qof walbana wuu doortaa.',no:'Setter fram både shaah og kaffe – alle får velge.',score:{norway:2,heritage:3,bridge:3}},
      {id:'c',icon:'📅',so:'Waxaan la yaabaa sababta aan ballan loo samayn.',no:'Lurer mest på hvorfor besøket ikke var avtalt.',score:{norway:3,heritage:0,bridge:0}},
      {id:'d',icon:'🥤',so:'Waxaan bixiyaa biyo oo wada hadalka bilaabaa.',no:'Tilbyr vann og starter samtalen.',score:{norway:1,heritage:1,bridge:1}}
    ]},
    { id:'BPG-HI-03', category:'heritage', so:'Kooxda qoyska waxaa ku jira farriin cod ah oo toddoba daqiiqo ah. Maxaad samaynaysaa?', no:'Familiechatten får en talemelding på sju minutter. Hva gjør du?', options:[
      {id:'a',icon:'🎙️',so:'Waan wada dhegaystaa oo cod ayaan ugu jawaabaa.',no:'Hører alt og svarer med en ny talemelding.',score:{norway:0,heritage:3,bridge:1}},
      {id:'b',icon:'📝',so:'Waan dhegaystaa, qodobbada muhiimka ahna waan soo koobaa.',no:'Lytter og oppsummerer hovedpoengene for resten.',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'😂',so:'Waxaan diraa emoji oo rajaynayaa inay ku filan tahay.',no:'Sender en passende emoji og håper det holder.',score:{norway:1,heritage:1,bridge:0}},
      {id:'d',icon:'⏩',so:'Waxaan dhigaa xawaare laba-laab ah.',no:'Setter avspillingen på dobbel hastighet.',score:{norway:2,heritage:1,bridge:1}}
    ]},
    { id:'BPG-HI-04', category:'heritage', so:'Aroos Soomaali ah ayaa muusiggu bilaabmaa. Xaggee joogtaa?', no:'Musikken starter i et somalisk bryllup. Hvor er du?', options:[
      {id:'a',icon:'💃',so:'Bartamaha ayaan joogaa; heesta iyo tallaabada waan aqaan.',no:'Midt på gulvet – jeg kjenner sangen og trinnene.',score:{norway:0,heritage:3,bridge:1}},
      {id:'b',icon:'👀',so:'Waxaan eegaa eeddo, dabadeedna waan raacaa.',no:'Følger med på en tante og prøver meg fram.',score:{norway:1,heritage:2,bridge:2}},
      {id:'c',icon:'📹',so:'Waxaan duubaa oo qof kale u sharxaa waxa dhacaya.',no:'Filmer litt og forklarer en annen hva som skjer.',score:{norway:2,heritage:2,bridge:3}},
      {id:'d',icon:'🍽️',so:'Miiska cuntada ayaan ka ilaaliyaa.',no:'Passer strategisk på matbordet.',score:{norway:0,heritage:1,bridge:0}}
    ]},
    { id:'BPG-HI-05', category:'heritage', so:'Qof ayaa ku weydiiya sida bariis Soomaali loo sameeyo. Maxaad tidhaahdaa?', no:'Noen spør hvordan man lager somalisk bariis. Hva svarer du?', options:[
      {id:'a',icon:'🤌',so:'Qiyaasta gacanta iyo urta ayaa kuu sheegaysa.',no:'Man må kjenne mengden og lukten – oppskrift er sekundært.',score:{norway:0,heritage:3,bridge:1}},
      {id:'b',icon:'⚖️',so:'Waxaan rabaa garaam, daqiiqo iyo heerkul sax ah.',no:'Jeg trenger gram, minutter og nøyaktig temperatur.',score:{norway:3,heritage:1,bridge:1}},
      {id:'c',icon:'📞',so:'Waxaan wacaa qof weyn, dabadeedna cuntada iyo sheekada waan kaydiyaa.',no:'Ringer en eldre og dokumenterer både maten og historien.',score:{norway:2,heritage:3,bridge:3}},
      {id:'d',icon:'📦',so:'Waxaan soo jeediyaa in meel laga dalbado.',no:'Foreslår at vi bestiller ferdig.',score:{norway:1,heritage:0,bridge:0}}
    ]},
    { id:'BPG-HI-06', category:'heritage', so:'Ilmo ayaa ku weydiiya eray Soomaali ah oo adag. Maxaad samaynaysaa?', no:'Et barn spør om et vanskelig somalisk ord. Hva gjør du?', options:[
      {id:'a',icon:'📖',so:'Waxaan ku sharxaa sheeko ama tusaale Soomaali ah.',no:'Forklarer med en somalisk historie eller et eksempel.',score:{norway:0,heritage:3,bridge:2}},
      {id:'b',icon:'↔️',so:'Waxaan helaa eray Noorwiiji ah, laakiin farqiga sidoo kale waan sharxaa.',no:'Finner et norsk ord og forklarer også forskjellen.',score:{norway:2,heritage:2,bridge:3}},
      {id:'c',icon:'👵',so:'Waxaan si wadajir ah u weydiinaynaa qof qoyska ka weyn.',no:'Vi spør en eldre i familien sammen.',score:{norway:1,heritage:3,bridge:3}},
      {id:'d',icon:'😅',so:'Waxaan idhaahdaa: “Markaad weynaato ayaad fahmi doontaa.”',no:'Sier: «Det forstår du når du blir eldre.»',score:{norway:0,heritage:1,bridge:0}}
    ]},
    { id:'BPG-HI-07', category:'heritage', so:'Qof cusub ayaa ku yidhaahda “adeer” ama “eedo”. Sideed u fahantaa?', no:'Noen omtales som «adeer» eller «eedo». Hvordan tolker du det?', options:[
      {id:'a',icon:'🌳',so:'Waxaan garanayaa xiriirka iyo waxa magacaasi tilmaamayo.',no:'Jeg skjønner relasjonen og hva betegnelsen signaliserer.',score:{norway:0,heritage:3,bridge:2}},
      {id:'b',icon:'🗺️',so:'Waxaan sawiraa geed qoys si qof walba u fahmo.',no:'Tegner et lite familiekart så alle henger med.',score:{norway:2,heritage:3,bridge:3}},
      {id:'c',icon:'🤷',so:'Dhammaan dadka waaweyn waxaan u haystaa adeer ama eeddo.',no:'Regner med at alle voksne er en slags tante eller onkel.',score:{norway:0,heritage:1,bridge:0}},
      {id:'d',icon:'📇',so:'Magaca ayaan kaydiyaa, faahfaahinta dambe ayaan bartaa.',no:'Lagrer navnet og lærer detaljene senere.',score:{norway:1,heritage:1,bridge:1}}
    ]},
    { id:'BPG-HI-08', category:'heritage', so:'Laba qof ayaa isla sheeko Soomaali ah si kala duwan u sheegaya. Maxaad samaynaysaa?', no:'To personer forteller den samme somaliske historien forskjellig. Hva gjør du?', options:[
      {id:'a',icon:'1️⃣',so:'Waxaan doortaa qofka aan ugu kalsoonahay.',no:'Velger versjonen til den jeg stoler mest på.',score:{norway:0,heritage:2,bridge:0}},
      {id:'b',icon:'🗣️',so:'Waxaan weydiiyaa halka ay kala duwanaanshuhu ka yimaadeen.',no:'Spør nysgjerrig hvorfor versjonene er ulike.',score:{norway:1,heritage:3,bridge:3}},
      {id:'c',icon:'🔎',so:'Waxaan raadshaa ilo qoran, anigoo aan diidin sheekada afka.',no:'Leter etter skriftlige spor uten å avvise den muntlige historien.',score:{norway:3,heritage:2,bridge:3}},
      {id:'d',icon:'🔥',so:'Labada sheeko ayaan ku raaxaystaa; mararka qaar kala duwanaanshuhu waa muhiim.',no:'Nyter begge – noen ganger er variasjonen en del av poenget.',score:{norway:0,heritage:3,bridge:2}}
    ]},

    { id:'BPG-BR-01', category:'bridge', so:'Qof qoyska ka mid ah ayaa helay warqad rasmi ah oo Noorwiiji ah. Maxaad samaynaysaa?', no:'Et familiemedlem har fått et offentlig brev på norsk. Hva gjør du?', options:[
      {id:'a',icon:'✅',so:'Anigaa arrinta oo dhan u qabta si ay dhakhso u dhammaato.',no:'Ordner alt selv så det blir gjort raskt.',score:{norway:3,heritage:1,bridge:1}},
      {id:'b',icon:'🗣️',so:'Waxaan sharxaa erayada iyo doorashooyinka, qofkuna isagaa go’aansada.',no:'Forklarer ord og alternativer, så personen kan bestemme selv.',score:{norway:3,heritage:2,bridge:3}},
      {id:'c',icon:'📸',so:'Sawir ayaan u diraa kooxda qoyska.',no:'Sender et bilde til familiechatten.',score:{norway:1,heritage:2,bridge:1}},
      {id:'d',icon:'📬',so:'Waxaan sugayaa warqad kale oo fudud.',no:'Venter og håper neste brev er enklere.',score:{norway:0,heritage:0,bridge:0}}
    ]},
    { id:'BPG-BR-02', category:'bridge', so:'War weyn oo Soomaaliya ku saabsan ayaa ku faafaya baraha bulshada. Maxaad samaynaysaa?', no:'En stor Somalia-nyhet sprer seg i sosiale medier. Hva gjør du?', options:[
      {id:'a',icon:'🚀',so:'Waan sii gudbiyaa — qof qoyska ka mid ah ayaa soo diray.',no:'Deler videre – den kom jo fra familien.',score:{norway:0,heritage:2,bridge:0}},
      {id:'b',icon:'📰',so:'Waxaan hubiyaa warbaahin iyo ilo rasmi ah.',no:'Sjekker redaktørstyrte og offisielle kilder.',score:{norway:3,heritage:0,bridge:2}},
      {id:'c',icon:'📞',so:'Waxaan weydiiyaa qof halkaas jooga waxa uu arkay.',no:'Spør noen lokalt hva de faktisk har sett.',score:{norway:0,heritage:3,bridge:2}},
      {id:'d',icon:'🧭',so:'Waxaan barbar dhigaa ilo, taariikh iyo dadka deegaanka.',no:'Sammenholder kilder, historikk og lokale erfaringer.',score:{norway:3,heritage:3,bridge:3}}
    ]},
    { id:'BPG-BR-03', category:'bridge', so:'Ilmo ayaa ku weydiiya: “Ma Soomaali baan ahay mise Noorwiiji?” Maxaad tidhaahdaa?', no:'Et barn spør: «Er jeg somalisk eller norsk?» Hva svarer du?', options:[
      {id:'a',icon:'2️⃣',so:'“Labadaba waad noqon kartaa,” dabadeedna tusaalooyin ayaan bixiya.',no:'«Du kan være begge deler», og gir konkrete eksempler.',score:{norway:2,heritage:2,bridge:3}},
      {id:'b',icon:'👴',so:'Waxaan weydiinaynaa awoowe ama ayeeyo sheekada qoyska.',no:'Vi spør en besteforelder om familiehistorien.',score:{norway:0,heritage:3,bridge:2}},
      {id:'c',icon:'📘',so:'Waxaan sharxaa dhalashada iyo waxa sharcigu yidhaahdo.',no:'Forklarer statsborgerskap og hva papirene sier.',score:{norway:3,heritage:0,bridge:1}},
      {id:'d',icon:'🍚',so:'Waxaan idhaahdaa: “Bariis iyo Grandis labadaba waad cuntaa.”',no:'Sier: «Du spiser jo både bariis og Grandis.»',score:{norway:1,heritage:2,bridge:2}}
    ]},
    { id:'BPG-BR-04', category:'bridge', so:'Qoyska ayaa isku khilaafsan caado ka duwan Norway iyo Soomaaliya. Maxaad samaynaysaa?', no:'Familien er uenig om en norm som er forskjellig i Norge og Somalia. Hva gjør du?', options:[
      {id:'a',icon:'🇳🇴',so:'Waxaan idhaahdaa: “Norway ayaan joognaa.”',no:'Sier: «Vi er i Norge nå.»',score:{norway:3,heritage:0,bridge:0}},
      {id:'b',icon:'🇸🇴',so:'Waxaan idhaahdaa: “Dhaqankeennu sidaas ma aha.”',no:'Sier: «Sånn gjør vi ikke i vår kultur.»',score:{norway:0,heritage:3,bridge:0}},
      {id:'c',icon:'↔️',so:'Waxaan sharxaa sababta labada dhinac, dabadeedna xal ayaan raadinnaa.',no:'Forklarer logikken på begge sider og leter etter en løsning.',score:{norway:2,heritage:2,bridge:3}},
      {id:'d',icon:'🍿',so:'Waan aamusa oo doodda daawadaa.',no:'Holder meg unna og følger debatten fra sidelinjen.',score:{norway:0,heritage:0,bridge:0}}
    ]},
    { id:'BPG-BR-05', category:'bridge', so:'Waxaad qorshaynaysaa safar Soomaaliya. Maxaa ugu horreeya?', no:'Du planlegger en reise til Somalia. Hva gjør du først?', options:[
      {id:'a',icon:'✈️',so:'Tigidh ayaan qabsadaa; faahfaahinta dambe ayaan eegaa.',no:'Bestiller fly – resten ordner seg.',score:{norway:1,heritage:1,bridge:0}},
      {id:'b',icon:'👨‍👩‍👧',so:'Qoyska ayaan u daayaa inay qorshaha sameeyaan.',no:'Lar familien ordne hele planen.',score:{norway:0,heritage:3,bridge:1}},
      {id:'c',icon:'🧭',so:'Waxaan isku daraa talo rasmi ah, aqoon deegaanka iyo xiriirka qoyska.',no:'Kombinerer offisielle råd, lokalkunnskap og familiekontakter.',score:{norway:3,heritage:3,bridge:3}},
      {id:'d',icon:'📱',so:'Waxaan raacaa talada qofka ugu caansan baraha bulshada.',no:'Følger rådene til den mest overbevisende profilen på nettet.',score:{norway:1,heritage:1,bridge:0}}
    ]},
    { id:'BPG-BR-06', category:'bridge', so:'Waxaad rabtaa inaad kaydiso sheeko qoys. Sideed u samaynaysaa?', no:'Du vil bevare en familiehistorie. Hvordan gjør du det?', options:[
      {id:'a',icon:'🎙️',so:'Cod Soomaali ah ayaan duubaa.',no:'Tar opp historien på somali.',score:{norway:0,heritage:3,bridge:2}},
      {id:'b',icon:'⌨️',so:'Noorwiiji ayaan ku qoraa si carruurtu u akhrido.',no:'Skriver den på norsk så barna kan lese den.',score:{norway:3,heritage:1,bridge:2}},
      {id:'c',icon:'🗂️',so:'Codka, turjumaadda, sawirrada iyo cidda sheegtay ayaan wada kaydiyaa.',no:'Bevarer lyd, oversettelse, bilder og hvem som fortalte.',score:{norway:3,heritage:3,bridge:3}},
      {id:'d',icon:'🧠',so:'Xusuusta ayaan ku hayaa — sida had iyo jeer loo sameeyey.',no:'Husker den – slik historier alltid har levd.',score:{norway:0,heritage:2,bridge:0}}
    ]},
    { id:'BPG-BR-07', category:'bridge', so:'Waxaad rabtaa inaad fahanto arrin siyaasadeed oo Soomaaliya ah. Maxaad samaynaysaa?', no:'Du vil forstå en politisk sak i Somalia. Hva gjør du?', options:[
      {id:'a',icon:'📱',so:'Waxaan dhegaystaa qof aan horay ugu kalsoonaa.',no:'Hører på en profil jeg allerede stoler på.',score:{norway:0,heritage:2,bridge:0}},
      {id:'b',icon:'📰',so:'Waxaan akhriyaa sida warbaahinta Norway u sharaxdo.',no:'Leser hvordan norske medier forklarer saken.',score:{norway:3,heritage:0,bridge:1}},
      {id:'c',icon:'📞',so:'Waxaan weydiiyaa dad ku nool meelo kala duwan oo Soomaaliya ah.',no:'Spør mennesker i ulike deler av Somalia.',score:{norway:0,heritage:3,bridge:2}},
      {id:'d',icon:'🌍',so:'Waxaan isku daraa ilo maxalli, Soomaali, Noorwiiji iyo caalami ah.',no:'Kombinerer lokale, somaliske, norske og internasjonale kilder.',score:{norway:3,heritage:3,bridge:3}}
    ]},
    { id:'BPG-BR-08', category:'bridge', so:'Waxaad martigelinaysaa qoysas Soomaali iyo Noorwiiji ah. Sidee habeenku u ekaanayaa?', no:'Du inviterer både somaliske og norske familier. Hvordan blir kvelden?', options:[
      {id:'a',icon:'🚪',so:'Koox walba waxay inta badan la joogtaa dadka ay taqaan.',no:'Gruppene holder seg mest til dem de allerede kjenner.',score:{norway:1,heritage:1,bridge:0}},
      {id:'b',icon:'🍽️',so:'Waxaan bixiyaa cunto labada dhinac ah, laakiin taas ayaan ku ekaadaa.',no:'Serverer mat fra begge steder og lar resten ordne seg.',score:{norway:2,heritage:2,bridge:1}},
      {id:'c',icon:'🎲',so:'Cunto, sheeko iyo ciyaar ayaa dadka isku qasaysa.',no:'Mat, historier og en liten lek får folk til å blande seg.',score:{norway:3,heritage:3,bridge:3}},
      {id:'d',icon:'🍕',so:'Grandis ayaan soo saaraa, bariiskana waan ag dhigaa — si cilmi ah.',no:'Setter fram Grandis med bariis ved siden av – for forskningens skyld.',score:{norway:3,heritage:3,bridge:2}}
    ]}
  ],
  profiles: [
    { key:'fusion', icon:'🍚🍕', so:'Bariis på Grandis', no:'Bariis på Grandis', summarySo:'Labada dhinac si dabiici ah ayaad isugu dartaa — mararka qaar xitaa isla saxanka.', summaryNo:'Du blander begge sider ganske naturlig – av og til på samme tallerken.' },
    { key:'bridge', icon:'🥟🧤', so:'Sambuus dugnad-ka', no:'Sambuus på dugnad', summarySo:'Waxaad jeceshahay inaad dadka, erayada iyo caadooyinka isku xidho.', summaryNo:'Du liker å koble mennesker, ord og vaner på tvers.' },
    { key:'heritage', icon:'🍚🌿', so:'Bariisku wuu taagan yahay', no:'Bariisen står støtt', summarySo:'Afka, qoyska iyo dhaqanka Soomaalidu meel adag ayay kaa joogaan.', summaryNo:'Språk, familie og somalisk kultur står sterkt hos deg.' },
    { key:'norway', icon:'🍕🇳🇴', so:'Grandis dhinaca yaal', no:'Grandis ved siden av', summarySo:'Nolosha Norway si fiican ayaad u taqaan, adigoon khasab ku ahayn inaad wax kale ka tagto.', summaryNo:'Du har god norsk hverdagsradar uten at det bestemmer hele historien din.' },
    { key:'balanced', icon:'🫖☕', so:'Shaah iyo qaxwo', no:'Shaah og kaffe', summarySo:'Waxaad leedahay isku-dar adiga kuu gaar ah, xaalad kastana si gaar ah ayaad ula qabsataa.', summaryNo:'Du har din egen blanding og skifter naturlig etter situasjonen.' },
    { key:'explorer', icon:'🧭', so:'Dhadhamiye xiiso leh', no:'Nysgjerrig smaksprøver', summarySo:'Waxaad bilowday sahanka; tallaabo yar ayaa kuu furi karta sheekooyin badan.', summaryNo:'Du er i utforskermodus, og små steg kan åpne mange nye samtaler.' }
  ],
  resultIntroSo:'Natiijadu ma aha xukun aqoonsigaaga. Waxay kaliya muujinaysaa jawaabaha aad dooratay maanta — iyo halka kaftan, su’aal ama wada hadal cusub kaa bilaabmi karo.',
  resultIntroNo:'Resultatet er ikke en fasit på identiteten din. Det viser bare hva svarene dine pekte mot i dag – og hvor en spøk, et spørsmål eller en ny samtale kan begynne.',
  actions: {
    norway:{so:'Dooro hal caado Norway ah oo aad rabto inaad si fiican u fahanto, oo qof weydii sababta loo sameeyo.',no:'Velg én norsk hverdagsvane du vil forstå bedre, og spør noen hvorfor den finnes.'},
    heritage:{so:'Qof qoyska ka mid ah weydii hal eray, maahmaah ama xusuus; codkiisana kaydi haddii uu oggol yahay.',no:'Spør noen i familien om ett ord, ordtak eller minne, og ta vare på stemmen hvis personen ønsker det.'},
    bridge:{so:'Hal fikrad laba luqadood ugu sharax qof kaa yar ama kaa weyn, oo weydii waxa ka maqnaa.',no:'Forklar én idé på begge språk til noen yngre eller eldre, og spør hva som manglet.'}
  }
};
