const groups = [
  { id:'0', label:'0–6', so:'Bilow yar', no:'Små oppdagere', emoji:'🦊', className:'a0' },
  { id:'7', label:'7–13', so:'Baareyaal', no:'Detektiver', emoji:'🔎', className:'a7' },
  { id:'13', label:'13–15', so:'Maskax fiiqan', no:'Skarpe hoder', emoji:'🧩', className:'a13' },
  { id:'16', label:'16+', so:'Sirdoon', no:'Tenkere', emoji:'✨', className:'a16' }
];

// Demo-bank: Somali wording should be reviewed by a native Somali editor before production.
const riddles = {
  '0': [
    {q:'Waxaan leeyahay afar lugood, laakiin ma socdo. Maxaan ahay?', no:'Jeg har fire bein, men går aldri. Hva er jeg?', a:'Kursi', an:'Stol', v:'🪑', o:[['🪑','Kursi','Stol'],['🐄','Sac','Ku'],['🐕','Eey','Hund']]},
    {q:'Waxaan leeyahay baalal, waan duulaa. Maxaan ahay?', no:'Jeg har vinger og kan fly. Hva er jeg?', a:'Shimbir', an:'Fugl', v:'🐦', o:[['🐦','Shimbir','Fugl'],['🐟','Kalluun','Fisk'],['🐈','Bisad','Katt']]},
    {q:'Waxaan ku noolahay biyo, waan dabaashaa. Maxaan ahay?', no:'Jeg bor i vann og svømmer. Hva er jeg?', a:'Kalluun', an:'Fisk', v:'🐟', o:[['🐟','Kalluun','Fisk'],['🦁','Libaax','Løve'],['🐫','Geel','Kamel']]},
    {q:'Waxaan cuntaa moos, geed baan fuulaa. Maxaan ahay?', no:'Jeg spiser banan og klatrer i trær. Hva er jeg?', a:'Daanyeer', an:'Ape', v:'🐒', o:[['🐒','Daanyeer','Ape'],['🐄','Sac','Ku'],['🐘','Maroodi','Elefant']]},
    {q:'Waxaan dhahaa moo. Maxaan ahay?', no:'Jeg sier mø. Hva er jeg?', a:'Sac', an:'Ku', v:'🐄', o:[['🐕','Eey','Hund'],['🐄','Sac','Ku'],['🐑','Ido','Sau']]}
  ],
  '7': [
    {q:'Waxaan leeyahay weji iyo laba gacan, laakiin ma arko waxna ma qabto. Maxaan ahay?', no:'Jeg har et ansikt og to hender, men kan ikke se eller holde noe. Hva er jeg?', a:'Saacad', an:'Klokke', v:'🕰️', o:[['🕰️','Saacad','Klokke'],['🤖','Robot','Robot'],['🧤','Galoof','Vott']]},
    {q:'Waxaan leeyahay il, laakiin ma arko. Maxaan ahay?', no:'Jeg har et øye, men kan ikke se. Hva er jeg?', a:'Irbad', an:'Nål', v:'🪡', o:[['🪡','Irbad','Nål'],['👁️','Il','Øye'],['🐦','Shimbir','Fugl']]},
    {q:'Waxaan noqdaa qoyan marka aan qalajiyo. Maxaan ahay?', no:'Jeg blir våt mens jeg tørker. Hva er jeg?', a:'Shukumaan', an:'Håndkle', v:'🧻', o:[['🧻','Shukumaan','Håndkle'],['☂️','Dallad','Paraply'],['🧼','Saabuun','Såpe']]},
    {q:'Inta badan aad qaaddo, raadad badan ayaad ka tagtaa. Maxaad tahay?', no:'Jo flere du tar, desto flere spor etterlater du. Hva er du?', a:'Tallaabooyin', an:'Fotspor', v:'👣', o:[['👣','Tallaabooyin','Fotspor'],['📚','Buugaag','Bøker'],['🍎','Tufaax','Epler']]},
    {q:'Waxaan leeyahay furayaal badan, laakiin albaab ma furo. Maxaan ahay?', no:'Jeg har mange nøkler, men åpner ingen dør. Hva er jeg?', a:'Biyaano', an:'Piano', v:'🎹', o:[['🎹','Biyaano','Piano'],['🔑','Fure','Nøkkel'],['🏠','Guri','Hus']]}
  ],
  '13': [
    {q:'Waxaan leeyahay magaalooyin, kaymo iyo bado, laakiin guryo, geedo iyo biyo ma lihi. Maxaan ahay?', no:'Jeg har byer, skoger og hav, men ingen hus, trær eller vann. Hva er jeg?', a:'Khariidad', an:'Kart', v:'🗺️', o:[['🗺️','Khariidad','Kart'],['🌍','Dunida','Jorden'],['🏙️','Magaalo','By']]},
    {q:'Waxaan leeyahay af, laakiin ma hadlo; sariir, laakiin ma seexdo. Maxaan ahay?', no:'Jeg har en munn, men snakker ikke; en seng, men sover ikke. Hva er jeg?', a:'Webi', an:'Elv', v:'🏞️', o:[['🏞️','Webi','Elv'],['🛏️','Sariir','Seng'],['👄','Af','Munn']]},
    {q:'Markaan yarahay waan dheerahay, markaan weynahay waan gaabanahay. Maxaan ahay?', no:'Når jeg er ung er jeg høy, og når jeg blir gammel er jeg lav. Hva er jeg?', a:'Shumac', an:'Stearinlys', v:'🕯️', o:[['🕯️','Shumac','Stearinlys'],['🌳','Geed','Tre'],['🕰️','Saacad','Klokke']]},
    {q:'Aniga oo aan noolayn ayaan koraa; hawo ayaan u baahanahay; biyo ayaana i dilaya. Maxaan ahay?', no:'Jeg vokser uten å leve, trenger luft, og vann dreper meg. Hva er jeg?', a:'Dab', an:'Ild', v:'🔥', o:[['🔥','Dab','Ild'],['🌱','Geed yar','Plante'],['💨','Hawo','Luft']]},
    {q:'Waxaan leeyahay qoorta, laakiin madax ma lihi. Maxaan ahay?', no:'Jeg har en hals, men ikke noe hode. Hva er jeg?', a:'Dhalo', an:'Flaske', v:'🍾', o:[['🍾','Dhalo','Flaske'],['🦒','Geri','Sjiraff'],['👕','Shaati','Skjorte']]}
  ],
  '16': [
    {q:"Waxaan jawaab u noqdaa, laakiin su'aal kasta kadib ayaan dhashaa. Maxaan ahay?", no:'Jeg er et svar, men blir født etter hvert spørsmål. Hva er jeg?', a:'Fikir', an:'Tanke', v:'💭', o:[['💭','Fikir','Tanke'],['📣','Cod','Lyd'],['⏳','Waqti','Tid']]},
    {q:'Waxaan kaa horreeyaa mar kasta, balse indhahaaga iguma arki kartid. Maxaan ahay?', no:'Jeg er alltid foran deg, men du kan ikke se meg. Hva er jeg?', a:'Mustaqbal', an:'Fremtiden', v:'🌅', o:[['🌅','Mustaqbal','Fremtiden'],['📜','Taariikh','Historie'],['👣','Raad','Spor']]},
    {q:'Dad badan ayaan isku xiraa, anigoo aan xadhig ahayn. Maxaan ahay?', no:'Jeg kobler sammen mange mennesker uten å være et tau. Hva er jeg?', a:'Luuqad', an:'Språk', v:'🗣️', o:[['🗣️','Luuqad','Språk'],['🧶','Xadhig','Tau'],['🚪','Albaab','Dør']]},
    {q:"Haddii aad magacayga sheegto, waan baaba'aa. Maxaan ahay?", no:'Hvis du sier navnet mitt, forsvinner jeg. Hva er jeg?', a:'Aamusnaan', an:'Stillhet', v:'🤫', o:[['🤫','Aamusnaan','Stillhet'],['🎵','Hees','Sang'],['🌧️','Roob','Regn']]},
    {q:'Waxaan ka fududahay baal, laakiin qofna ma hayn karo muddo dheer. Maxaan ahay?', no:'Jeg er lettere enn en fjær, men ingen kan holde meg lenge. Hva er jeg?', a:'Neef', an:'Pust', v:'🌬️', o:[['🌬️','Neef','Pust'],['🪶','Baal','Fjær'],['💡','Iftiin','Lys']]}
  ]
};

let language='so', group, index=0, score=0, locked=false;
const $=id=>document.getElementById(id);
function text(so,no){ return language==='no'?no:so }
function localize(){document.documentElement.lang=language;document.querySelectorAll('[data-so]').forEach(el=>el.textContent=text(el.dataset.so,el.dataset.no));$('languageToggle').title=language==='so'?'Vis tekst på norsk':'Tus tekst på somali';$('languageToggle').setAttribute('aria-label',$('languageToggle').title);if(group)renderRiddle();}
function showWelcome(){group=null;index=0;$('welcome').classList.remove('hidden');$('game').classList.add('hidden');document.body.removeAttribute('data-age');}
function chooseGroup(id){group=groups.find(g=>g.id===id);index=0;$('welcome').classList.add('hidden');$('game').classList.remove('hidden');document.body.dataset.age=id;renderRiddle();speak('Ku soo dhowow Googa. '+riddles[id][0].q);}
function renderGroups(){ $('ageGrid').innerHTML=groups.map(g=>`<button class="age-card ${g.className}" data-group="${g.id}"><span class="emoji">${g.emoji}</span><strong>${g.label}</strong><small>${text(g.so,g.no)}</small></button>`).join('');document.querySelectorAll('[data-group]').forEach(b=>b.onclick=()=>chooseGroup(b.dataset.group));}
function renderRiddle(){const r=riddles[group.id][index];locked=false;$('ageLabel').textContent=`${group.label} · ${text(group.so,group.no)}`;$('progress').textContent=`${index+1} / ${riddles[group.id].length}`;$('progressFill').style.width=`${((index+1)/riddles[group.id].length)*100}%`;$('caseTag').textContent=text('HALXIRAALE','GÅTE');$('riddleVisual').textContent=r.v;$('question').textContent=r.q;$('norwegianQuestion').textContent=r.no;$('norwegianQuestion').classList.toggle('hidden',language!=='no');$('feedback').classList.add('hidden');$('options').innerHTML=r.o.map(([emoji,so,no])=>`<button class="option" data-answer="${so}"><span class="choice-emoji">${emoji}</span><span>${text(so,no)}</span></button>`).join('');document.querySelectorAll('.option').forEach(btn=>btn.onclick=()=>answer(btn,r));}
function answer(btn,r){if(locked)return;const good=btn.dataset.answer===r.a;if(!good){btn.classList.add('wrong');return;}locked=true;btn.classList.add('correct');score++;$('stars').textContent=`✦ ${score}`;$('feedback').innerHTML=`<strong>${text('Waa sax!','Riktig!')} 🎉</strong>${text('Jawaabtu waa ','Svaret er ')}${text(r.a,r.an)}.`;$('feedback').classList.remove('hidden');speak(text('Waa sax. Jawaabtu waa ','Riktig. Svaret er ')+(language==='no'?r.an:r.a));setTimeout(()=>{if(index<riddles[group.id].length-1){index++;renderRiddle();}else{finish();}},1450);}
function finish(){$('riddleVisual').textContent='🏅';$('question').textContent=text('Waad dhammaysay kiiskan!','Du har løst saken!');$('norwegianQuestion').classList.add('hidden');$('options').innerHTML=`<button class="option correct" id="again"><span class="choice-emoji">🔁</span><span>${text('Mar kale ciyaar','Spill igjen')}</span></button>`;$('again').onclick=()=>{index=0;renderRiddle();};}
function speak(words){if(!('speechSynthesis'in window))return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(words);u.lang=language==='no'?'nb-NO':'so-SO';u.rate=.82;window.speechSynthesis.speak(u);}
$('languageToggle').onclick=()=>{language=language==='so'?'no':'so';renderGroups();localize();};$('homeButton').onclick=showWelcome;$('backButton').onclick=showWelcome;$('speakButton').onclick=()=>{const r=riddles[group.id][index];speak(language==='no'?r.no:r.q)};renderGroups();if('serviceWorker'in navigator)navigator.serviceWorker.register('./sw.js');
