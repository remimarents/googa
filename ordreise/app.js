const app = document.querySelector('#app');
const levels = window.GOOGA_ORDREISE_LEVELS || [];
const words = new Set(window.GOOGA_ORDREISE_WORDS || []);
const storageKey = `googa-ordreise:${window.GOOGA_ORDREISE_USER || 'guest'}`;
const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
const state = { level: Math.min(saved.level || 0, Math.max(0, levels.length - 1)), found: saved.found || {}, letters: [], bonus: [] };

function save() { localStorage.setItem(storageKey, JSON.stringify({ level: state.level, found: state.found })); }
function shuffle(items) { return [...items].sort(() => Math.random() - .5); }
function current() { return levels[state.level]; }
function isMakeable(word, letters) { const pool = [...letters]; return [...word].every(letter => { const i = pool.indexOf(letter); if (i < 0) return false; pool.splice(i, 1); return true; }); }
function render() {
  const level = current();
  if (!level) { app.innerHTML = '<p>Ciyaartu weli ma diyaarsana.</p>'; return; }
  const found = new Set(state.found[level.id] || []);
  const solved = level.words.every(word => found.has(word));
  app.innerHTML = `<section class="shell">
    <header><a href="../" class="back">← Googa</a><div><p class="eyebrow">SAFARKA ERAYADA</p><h1>Heerka ${level.id}</h1></div><button class="restart" data-action="restart">Bilow</button></header>
    <p class="intro">Samee erayada ka imanaya xarfaha. Eray kasta oo sax ah waa horumar!</p>
    <div class="progress"><i style="width:${(found.size / level.words.length) * 100}%"></i></div>
    <section class="answers">${level.words.map(word => `<div class="answer ${found.has(word) ? 'found' : ''}">${found.has(word) ? word : '•'.repeat(word.length)}</div>`).join('')}</section>
    <p id="message" class="message">${solved ? 'Aad baad u fiican tahay! Heerkan waad dhammaysay.' : 'Raadi dhammaan erayada.'}</p>
    <div id="selected" class="selected">${state.letters.join('') || '&nbsp;'}</div>
    <div class="wheel">${shuffle(level.letters).map((letter, index) => `<button class="letter" data-index="${index}" data-letter="${letter}">${letter}</button>`).join('')}</div>
    <div class="tools"><button data-action="clear">Tirtir</button><button class="primary" data-action="check">Hubi</button><button data-action="hint">Tilmaan</button></div>
    ${state.bonus.length ? `<p class="bonus">Erayo dheeraad ah: ${state.bonus.join(', ')}</p>` : ''}
    <button class="next ${solved ? '' : 'hidden'}" data-action="next">Heerka xiga →</button>
  </section>`;
  app.querySelectorAll('.letter').forEach(button => button.addEventListener('click', () => { state.letters.push(button.dataset.letter); button.disabled = true; app.querySelector('#selected').textContent = state.letters.join(''); }));
  app.querySelectorAll('[data-action]').forEach(button => button.addEventListener('click', () => action(button.dataset.action)));
}
function message(text) { app.querySelector('#message').textContent = text; }
function action(type) {
  const level = current();
  if (type === 'clear') { state.letters = []; render(); return; }
  if (type === 'restart') { state.level = 0; state.found = {}; state.letters = []; state.bonus = []; save(); render(); return; }
  if (type === 'next') { state.level = (state.level + 1) % levels.length; state.letters = []; state.bonus = []; save(); render(); return; }
  if (type === 'hint') { const found = new Set(state.found[level.id] || []); const word = level.words.find(item => !found.has(item)); if (word) message(`Tilmaan: eraygu wuxuu ka bilaabmaa ${word[0]}.`); return; }
  if (type !== 'check') return;
  const attempt = state.letters.join('');
  state.letters = [];
  if (attempt.length < 2 || !isMakeable(attempt, level.letters)) { render(); message('Dooro xarfo sax ah.'); return; }
  const found = new Set(state.found[level.id] || []);
  if (level.words.includes(attempt)) { found.add(attempt); state.found[level.id] = [...found]; save(); render(); return; }
  if (words.has(attempt)) { if (!state.bonus.includes(attempt)) state.bonus.push(attempt); render(); message('Eray fiican! Waa eray dheeraad ah.'); return; }
  render(); message('Eraygan weli kuma jiro ordbanka.');
}
render();
