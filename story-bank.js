// Original MVP retellings based on Somali oral tradition.
// Somali text must receive a final review from Googa's Somali language editor before public release.
window.GOOGA_STORIES = [
  {
    id: 'diin-dawaco', age: '0', ageLabel: '0–6', accent: '#d9a31f', soft: '#fff0ad',
    title: 'Diin iyo Dawaco', titleNo: 'Skilpadden og reven',
    subtitle: 'Sheeko yar oo ku saabsan dulqaadka', subtitleNo: 'En liten historie om tålmodighet',
    image: 'assets/stories/diin-dawaco.png', icon: '🐢', support: 'Sawir · Cod · Eray', supportNo: 'Bilde · Lyd · Ord',
    scenes: [
      {
        so: 'Maalin qorrax leh, Diin wuxuu ku socday geed weyn hoostiisa. Dawaco ayaa u timid.',
        no: 'En solrik dag gikk Skilpadden under et stort tre. Reven kom bort til ham.',
        audio: 'audio/stories/diin-dawaco-scene-1.mp3',
        words: [
          {so:'Diin',no:'skilpadde',icon:'🐢',audio:'audio/stories/diin-dawaco-word-diin.mp3'},
          {so:'Dawaco',no:'rev',icon:'🦊',audio:'audio/stories/diin-dawaco-word-dawaco.mp3'},
          {so:'geed',no:'tre',icon:'🌳',audio:'audio/stories/diin-dawaco-word-geed.mp3'}
        ]
      },
      {
        so: 'Dawaco waxay tiri: “Aan tartanno!” Diin wuu qoslay. Wuxuu ogaa inuusan degdeg ahayn.',
        no: 'Reven sa: «La oss løpe om kapp!» Skilpadden lo. Han visste at han ikke var rask.',
        audio: 'audio/stories/diin-dawaco-scene-2.mp3',
        words: [
          {so:'tartanno',no:'konkurrere',icon:'🏁',audio:'audio/stories/diin-dawaco-word-tartanno.mp3'},
          {so:'qoslay',no:'lo',icon:'😄',audio:'audio/stories/diin-dawaco-word-qoslay.mp3'}
        ]
      },
      {
        so: 'Diin si tartiib ah ayuu u socday, mana uusan joogsan. Dawaco way daashay. Diin wuxuu gaaray geedkii.',
        no: 'Skilpadden gikk sakte, men stoppet aldri. Reven ble sliten. Skilpadden kom fram til treet.',
        audio: 'audio/stories/diin-dawaco-scene-3.mp3',
        words: [
          {so:'tartiib',no:'sakte',icon:'🐾',audio:'audio/stories/diin-dawaco-word-tartiib.mp3'},
          {so:'joogsan',no:'stoppe',icon:'✋',audio:'audio/stories/diin-dawaco-word-joogsan.mp3'}
        ]
      }
    ],
    activity: {
      promptSo: 'Yaa aan joogsan?', promptNo: 'Hvem stoppet aldri?',
      options: [{so:'Diin',no:'Skilpadden',icon:'🐢'},{so:'Dawaco',no:'Reven',icon:'🦊'}], answer:'Diin',
      successSo:'Waa sax! Diin si tartiib ah ayuu u socday, laakiin ma joogsan.',
      successNo:'Riktig! Skilpadden gikk sakte, men han stoppet aldri.'
    }
  },
  {
    id: 'wiil-waal', age: '7', ageLabel: '7–13', accent: '#07898d', soft: '#c9f3ee',
    title: 'Wiil Waal', titleNo: 'Wiil Waal og den kloke jenta',
    subtitle: 'Halxiraale, caddaalad iyo gabar maskax badan', subtitleNo: 'En gåte om rettferdighet og klokskap',
    image: 'assets/stories/wiil-waal.png', icon: '💡', support: 'Tarjumid · Erayo · Halxiraale', supportNo: 'Oversettelse · Ord · Gåte',
    scenes: [
      {
        so: 'Wiil Waal wuxuu isugu yeeray raggii degaanka. Wuxuu weydiiyey halxiraale: “Ii keena qaybta idaha ee dadka mideyn karta ama kala diri karta.”',
        no: 'Wiil Waal kalte sammen mennene i området. Han ga dem en gåte: «Ta med den delen av sauen som kan samle mennesker eller splitte dem.»',
        audio:'audio/stories/wiil-waal-scene-1.mp3',
        words:[
          {so:'halxiraale',no:'gåte',icon:'❓',audio:'audio/stories/wiil-waal-word-halxiraale.mp3'},
          {so:'ido',no:'sau',icon:'🐑',audio:'audio/stories/wiil-waal-word-ido.mp3'},
          {so:'mideyn',no:'forene',icon:'🤝',audio:'audio/stories/wiil-waal-word-mideyn.mp3'}
        ]
      },
      {
        so: 'Nin sabool ah ayaa gurigiisii ku noqday isagoo walwalsan. Gabadhiisii ayaa dhegeysatay, dabadeedna waxay tiri: “Aabbe, feeraha u gee.”',
        no: 'En fattig mann gikk bekymret hjem. Datteren lyttet og sa: «Far, ta med ribbeina.»',
        audio:'audio/stories/wiil-waal-scene-2.mp3',
        words:[
          {so:'gabadhiisii',no:'datteren hans',icon:'👧🏾',audio:'audio/stories/wiil-waal-word-gabadh.mp3'},
          {so:'dhegeysatay',no:'lyttet',icon:'👂',audio:'audio/stories/wiil-waal-word-dhegeyso.mp3'},
          {so:'feeraha',no:'ribbeina',icon:'🦴',audio:'audio/stories/wiil-waal-word-feeraha.mp3'}
        ]
      },
      {
        so: 'Gabadhu waxay sharaxday: “Haddii si caddaalad ah loo qaybsado, cuntadu dadka way mideysaa. Haddii hunguri yimaado, way kala dirtaa.” Wiil Waal wuxuu gartay inay gabadhu xigmadda lahayd.',
        no: 'Jenta forklarte: «Når maten deles rettferdig, samler den mennesker. Når grådigheten tar over, splitter den dem.» Wiil Waal forsto at jenta hadde funnet visdommen i gåten.',
        audio:'audio/stories/wiil-waal-scene-3.mp3',
        words:[
          {so:'caddaalad',no:'rettferdighet',icon:'⚖️',audio:'audio/stories/wiil-waal-word-caddaalad.mp3'},
          {so:'hunguri',no:'grådighet',icon:'🙌',audio:'audio/stories/wiil-waal-word-hunguri.mp3'},
          {so:'xigmad',no:'visdom',icon:'✨',audio:'audio/stories/wiil-waal-word-xigmad.mp3'}
        ]
      }
    ],
    activity:{
      promptSo:'Maxaa dadka mideeya?',promptNo:'Hva samler mennesker?',
      options:[{so:'Caddaalad',no:'Rettferdighet',icon:'⚖️'},{so:'Hunguri',no:'Grådighet',icon:'🙌'},{so:'Cabsi',no:'Frykt',icon:'🌑'}],answer:'Caddaalad',
      successSo:'Sax! Sheekadu waxay ina tusaysaa in wax la wadaago si caddaalad ah.',
      successNo:'Riktig! Historien viser at en rettferdig deling kan samle mennesker.'
    }
  },
  {
    id: 'cigaal-shidaad', age: '13', ageLabel: '13–15', accent: '#4d75bd', soft: '#d8e6ff',
    title: 'Cigaal Shidaad', titleNo: 'Cigaal Shidaad og trestubben',
    subtitle: 'Waxa aan moodno iyo waxa runta ah', subtitleNo: 'Det vi forestiller oss, og det som er virkelig',
    image:'assets/stories/cigaal-shidaad.png',icon:'🌙',support:'Macne · Erayo · Faham',supportNo:'Mening · Ord · Forståelse',
    scenes:[
      {
        so:'Habeen mugdi ah, Cigaal Shidaad wuxuu u baxay inuu reerkiisa u raadiyo meel cusub. Waddadu way aamusnayd, iftiinkuna aad buu u yaraa.',
        no:'En mørk natt dro Cigaal Shidaad ut for å finne et nytt sted til familien. Veien var stille, og det var nesten ikke lys.',
        audio:'audio/stories/cigaal-shidaad-scene-1.mp3',
        words:[
          {so:'habeen',no:'natt',icon:'🌙',audio:'audio/stories/cigaal-shidaad-word-habeen.mp3'},
          {so:'mugdi',no:'mørke',icon:'🌑',audio:'audio/stories/cigaal-shidaad-word-mugdi.mp3'},
          {so:'raadiyo',no:'lete etter',icon:'🔎',audio:'audio/stories/cigaal-shidaad-word-raadiyo.mp3'}
        ]
      },
      {
        so:'Wuxuu arkay wax madow oo dhulka ka soo baxay. Maskaxdiisu waxay ka dhigtay libaax weyn. Cigaal meel ayuu ku hakaday, cabsi darteedna habeenkii oo dhan wuu sugay.',
        no:'Han så noe mørkt som stakk opp fra bakken. I tankene hans ble det til en stor løve. Cigaal stanset og ventet hele natten fordi han var redd.',
        audio:'audio/stories/cigaal-shidaad-scene-2.mp3',
        words:[
          {so:'libaax',no:'løve',icon:'🦁',audio:'audio/stories/cigaal-shidaad-word-libaax.mp3'},
          {so:'cabsi',no:'frykt',icon:'😨',audio:'audio/stories/cigaal-shidaad-word-cabsi.mp3'},
          {so:'sugay',no:'ventet',icon:'⏳',audio:'audio/stories/cigaal-shidaad-word-sugay.mp3'}
        ]
      },
      {
        so:'Markii waagu baryay, Cigaal wuxuu arkay inuusan libaaxu jirin. Waxa uu ka baqayay wuxuu ahaa kurtun geed. Wuxuu qosol ku bartay in muuqaalka iyo xaqiiqadu mararka qaarkood kala duwan yihiin.',
        no:'Da morgenen kom, så Cigaal at det ikke fantes noen løve. Det han hadde fryktet, var en trestubbe. Med et smil lærte han at inntrykk og virkelighet noen ganger er forskjellige.',
        audio:'audio/stories/cigaal-shidaad-scene-3.mp3',
        words:[
          {so:'waagu',no:'daggryet',icon:'🌅',audio:'audio/stories/cigaal-shidaad-word-waagu.mp3'},
          {so:'kurtun',no:'trestubbe',icon:'🪵',audio:'audio/stories/cigaal-shidaad-word-kurtun.mp3'},
          {so:'xaqiiqo',no:'virkelighet',icon:'👁️',audio:'audio/stories/cigaal-shidaad-word-xaqiiqo.mp3'}
        ]
      }
    ],
    activity:{
      promptSo:'Maxay sheekadu ina baraysaa?',promptNo:'Hva forteller historien oss?',
      options:[
        {so:'Cabsidu waxay beddeli kartaa waxa aan aragno',no:'Frykt kan forandre det vi tror vi ser',icon:'👁️'},
        {so:'Kurtun walba waa libaax',no:'Alle trestubber er løver',icon:'🦁'},
        {so:'Habeenkii waxba lama barto',no:'Vi lærer ingenting om natten',icon:'🌙'}
      ],answer:'Cabsidu waxay beddeli kartaa waxa aan aragno',
      successSo:'Waa sax. Cigaal wuxuu ka baqay sawir ay maskaxdiisu samaysay, ee ma ahayn libaax dhab ah.',
      successNo:'Riktig. Cigaal ble redd for et bilde tankene hans skapte, ikke for en virkelig løve.'
    }
  },
  {
    id:'caraweelo',age:'16',ageLabel:'16+',accent:'#ad527e',soft:'#f5d5e4',
    title:'Caraweelo',titleNo:'Caraweelo – en dronning, flere fortellinger',
    subtitle:'Hal magac, xusuuso iyo aragtiyo kala duwan',subtitleNo:'Ett navn, flere minner og perspektiver',
    image:'assets/stories/caraweelo.png',icon:'♛',support:'Tarjumid · Dhaqan · Milicsi',supportNo:'Oversettelse · Kultur · Refleksjon',
    scenes:[
      {
        so:'Magaca Caraweelo wuxuu ku nool yahay sheekooyin badan oo jiilba jiil u gudbiyey. Qaar waxay ku xusuustaan boqorad awood badan oo hoggaan qaadatay xilli aan dumarka si fudud loo maqli jirin.',
        no:'Navnet Caraweelo lever i mange fortellinger som er overført mellom generasjoner. Noen husker henne som en mektig dronning som tok ledelsen i en tid da kvinner ikke uten videre ble hørt.',
        noteNo:'Caraweelo er en legendarisk skikkelse. Fortellingene om henne varierer mellom fortellere og områder.',
        audio:'audio/stories/caraweelo-scene-1.mp3',
        words:[
          {so:'boqorad',no:'dronning',icon:'♛',audio:'audio/stories/caraweelo-word-boqorad.mp3'},
          {so:'hoggaan',no:'ledelse',icon:'🧭',audio:'audio/stories/caraweelo-word-hoggaan.mp3'},
          {so:'jiil',no:'generasjon',icon:'🌿',audio:'audio/stories/caraweelo-word-jiil.mp3'}
        ]
      },
      {
        so:'Qaar kale waxay Caraweelo uga sheekeeyaan sidii taliye adag oo cabsi dhalisay. Isla magacaas ayaa sidaas ku noqon kara astaan geesinimo u ah qof, halka qof kale uu uga arko digniin ku saabsan awoodda.',
        no:'Andre forteller om Caraweelo som en hard hersker som skapte frykt. Det samme navnet kan derfor være et symbol på mot for én person og en advarsel om makt for en annen.',
        noteNo:'Muntlige fortellinger bevarer ikke bare hendelser. De bevarer også verdiene og konfliktene til dem som forteller.',
        audio:'audio/stories/caraweelo-scene-2.mp3',
        words:[
          {so:'taliye',no:'hersker',icon:'🪶',audio:'audio/stories/caraweelo-word-taliye.mp3'},
          {so:'geesinimo',no:'mot',icon:'⭐',audio:'audio/stories/caraweelo-word-geesinimo.mp3'},
          {so:'awood',no:'makt',icon:'⚖️',audio:'audio/stories/caraweelo-word-awood.mp3'}
        ]
      },
      {
        so:'Marka aynu sheekada Caraweelo dhegeysanno, hal jawaab oo keliya ma raadinayno. Waxaynu weydiin karnaa: Yaa sheekada sheegay? Maxay doonayeen inay xusuustaan? Maxayse ahayd casharka ay jiilka dambe siinayeen?',
        no:'Når vi lytter til historien om Caraweelo, leter vi ikke bare etter ett svar. Vi kan spørre: Hvem fortalte historien? Hva ønsket de å huske? Og hvilken lærdom ville de gi neste generasjon?',
        noteNo:'Denne MVP-en bruker Caraweelo til å vise kildekritikk og perspektiv, ikke til å slå fast én autoritativ versjon.',
        audio:'audio/stories/caraweelo-scene-3.mp3',
        words:[
          {so:'xusuustaan',no:'huske',icon:'🧠',audio:'audio/stories/caraweelo-word-xusuus.mp3'},
          {so:'cashar',no:'lærdom',icon:'📖',audio:'audio/stories/caraweelo-word-cashar.mp3'},
          {so:'aragti',no:'perspektiv',icon:'👁️',audio:'audio/stories/caraweelo-word-aragti.mp3'}
        ]
      }
    ],
    activity:{
      promptSo:'Maxaa muhiim ah marka sheeko afka laga soo gudbiyey la akhrinayo?',promptNo:'Hva er viktig når vi leser en muntlig overlevert fortelling?',
      options:[
        {so:'In la ogaado inay jiri karaan aragtiyo kala duwan',no:'Å forstå at flere perspektiver kan finnes',icon:'◉'},
        {so:'In hal nooc oo keliya run yahay',no:'At bare én versjon kan være sann',icon:'1'},
        {so:'In su’aalo aan la weydiin',no:'At vi ikke stiller spørsmål',icon:'×'}
      ],answer:'In la ogaado inay jiri karaan aragtiyo kala duwan',
      successSo:'Sax. Sheekooyinka afka ahi way is beddeli karaan, aragti kastaana waxay ina baraysaa wax ku saabsan qofkii gudbiyey.',
      successNo:'Riktig. Muntlige fortellinger kan forandre seg, og hvert perspektiv forteller også noe om den som videreførte historien.'
    }
  }
];
