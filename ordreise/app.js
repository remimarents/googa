const app = document.querySelector('#app');
const levels = window.GOOGA_ORDREISE_LEVELS || [];
const provisionalRecords = window.GOOGA_ORDREISE_PROVISIONAL || [];
const provisionalWords = provisionalRecords.filter((record) => record.enabled && record.status === 'provisional').map((record) => record.word);
const words = new Set([...(window.GOOGA_ORDREISE_WORDS || []), ...provisionalWords]);
const storageKey = `googa-ordreise:${window.GOOGA_ORDREISE_USER || 'guest'}`;
const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
const state = { level: Math.min(saved.level || 0, Math.max(0, levels.length - 1)), found: saved.found || {}, letters: [], bonus: [], norwegian: false };
const text = (so, no) => state.norwegian ? no : so;
const difficulty = { common: ['Caadi', 'Vanlig'], bonus: ['Dhexdhexaad', 'Middels'], advanced: ['Horumarsan', 'Avansert'] };

function save() { localStorage.setItem(storageKey, JSON.stringify({ level: state.level, found: state.found })); }
function current() { return levels[state.level]; }
function shuffle(items) { return [...items].sort(() => Math.random() - .5); }
function makeable(word, letters) { const pool = [...letters]; return [...word].every(letter => { const i = pool.indexOf(letter); if (i < 0) return false; pool.splice(i, 1); return true; }); }
function render() {
  const level = current(); if (!level) return;
  const found = new Set(state.found[level.id] || []); const complete = level.words.every(word => found.has(word));
  const label = difficulty[level.difficulty] || difficulty.common;
  app.innerHTML = `<section class="game-shell"><div class="scenery" aria-hidden="true"></div>
    <header class="topbar"><button class="new-game-button" data-action="restart">${text('Cusub','Ny')}</button><div class="level-heading"><p class="eyebrow">${text(`Heerka ${level.id} · ${label[0]}`,`Nivå ${level.id} · ${label[1]}`)}</p></div><a class="googa-link" href="../">G</a><button class="coins" aria-label="Ord funnet">${found.size}</button></header>
    <section class="board-panel"><div class="progress-row"><span>${found.size}/${level.words.length} ${text('eray','ord')}</span><span>${state.bonus.length} ${text('dheeraad','bonus')}</span></div><div class="word-board">${level.words.map(word => `<div class="word-slot ${found.has(word) ? 'found' : ''}">${found.has(word) ? word : '•'.repeat(word.length)}</div>`).join('')}</div><div class="message" id="message">${complete ? text('Waa la dhammeeyey!', 'Nivået er løst!') : text('Raadi dhammaan erayada', 'Finn alle ordene')}</div></section>
    <section class="controls-panel ${complete ? 'solved' : ''}"><div id="selected" class="preview">${state.letters.join('') || '&nbsp;'}</div><div class="wheel">${shuffle(level.letters).map((letter,index) => `<button class="letter" data-index="${index}" data-letter="${letter}">${letter}</button>`).join('')}</div><div class="actions"><button class="tool-button" data-action="clear">⌫</button><button class="tool-button primary" data-action="check">${text('Hubi','Sjekk')}</button><button class="tool-button" data-action="hint">?</button></div>${state.bonus.length ? `<p class="bonus">${text('Erayo dheeraad ah','Bonusord')}: ${state.bonus.join(', ')}</p>` : ''}<button class="next-button ${complete ? 'visible' : ''}" data-action="next">${text('Heerka xiga','Neste nivå')}</button>${window.GOOGA_ORDREISE_OWNER ? `<button class="owner-language" data-action="language">${state.norwegian ? 'Af-Soomaali' : 'Norsk'} · eier</button>` : ''}</section></section>`;
  app.querySelectorAll('.letter').forEach(button => button.addEventListener('click', () => { state.letters.push(button.dataset.letter); button.disabled=true; app.querySelector('#selected').textContent=state.letters.join(''); }));
  app.querySelectorAll('[data-action]').forEach(button => button.addEventListener('click', () => action(button.dataset.action)));
}
function say(value) { app.querySelector('#message').textContent=value; }
function action(type) { const level=current(); if (type==='language') { state.norwegian=!state.norwegian; render(); return; } if (type==='clear') {state.letters=[];render();return;} if(type==='restart'){state.level=0;state.found={};state.letters=[];state.bonus=[];save();render();return;} if(type==='next'){state.level=(state.level+1)%levels.length;state.letters=[];state.bonus=[];save();render();return;} if(type==='hint'){const f=new Set(state.found[level.id]||[]);const word=level.words.find(w=>!f.has(w));if(word)say(text(`Tilmaan: ${word[0]}`,`Hint: ${word[0]}`));return;} if(type!=='check')return;const word=state.letters.join('');state.letters=[];if(word.length<2||!makeable(word,level.letters)){render();say(text('Dooro xarfo sax ah','Velg gyldige bokstaver'));return;}const f=new Set(state.found[level.id]||[]);if(level.words.includes(word)){f.add(word);state.found[level.id]=[...f];save();render();return;}if(words.has(word)){if(!state.bonus.includes(word))state.bonus.push(word);render();say(text('Eray dheeraad ah!','Bonusord!'));return;}render();say(text('Eraygan kuma jiro ordbanka','Ordet finnes ikke i ordbanken'));}
render();
