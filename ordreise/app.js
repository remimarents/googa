const app = document.querySelector('#app');
const levels = window.GOOGA_ORDREISE_LEVELS || [];
const provisionalRecords = window.GOOGA_ORDREISE_PROVISIONAL || [];
const provisionalWords = provisionalRecords.filter(record => record.enabled && record.status === 'provisional').map(record => record.word);
const words = new Set([...(window.GOOGA_ORDREISE_WORDS || []), ...provisionalWords]);
const digraphs = new Set(['DH', 'KH', 'SH']);
const storageKey = `googa-ordreise:${window.GOOGA_ORDREISE_USER || 'guest'}`;
const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
const premium = Boolean(window.GOOGA_ORDREISE_PREMIUM);
const canPurchase = Boolean(window.GOOGA_ORDREISE_CAN_PURCHASE);
const freeLevelCount = 10;
const playableLevelCount = premium ? levels.length : Math.min(freeLevelCount, levels.length);
const state = { level: Math.min(saved.level || 0, Math.max(0, playableLevelCount - 1)), found: saved.found || {}, letters: [], selectedTiles: new Set(), bonus: [], norwegian: false };
const text = (so, no) => state.norwegian ? no : so;
const difficulty = { common: ['Caadi', 'Vanlig'], bonus: ['Dhexdhexaad', 'Middels'], advanced: ['Horumarsan', 'Avansert'] };

function save() { localStorage.setItem(storageKey, JSON.stringify({ level: state.level, found: state.found })); }
function current() { return levels[state.level]; }
function shuffle(items) { return [...items].sort(() => Math.random() - .5); }
function makeable(word, letters) { const pool = [...letters]; return [...word].every(letter => { const i = pool.indexOf(letter); if (i < 0) return false; pool.splice(i, 1); return true; }); }
function tilesFor(letters) { const tiles = []; for (let i = 0; i < letters.length; i += 1) { const pair = `${letters[i] || ''}${letters[i + 1] || ''}`; if (digraphs.has(pair)) { tiles.push(pair); i += 1; } else tiles.push(letters[i]); } return tiles; }
function resetSelection() { state.letters = []; state.selectedTiles = new Set(); }

function render() {
  const level = current(); if (!level) return;
  const found = new Set(state.found[level.id] || []); const complete = level.words.every(word => found.has(word));
  const label = difficulty[level.difficulty] || difficulty.common; const tiles = shuffle(tilesFor(level.letters));
  const atFreeGate = !premium && complete && state.level === playableLevelCount - 1;
  const paymentMessage = window.GOOGA_ORDREISE_PAYMENT === 'cancelled' ? text('Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.', 'Betalingen ble avbrutt. Ingen penger er trukket.') : window.GOOGA_ORDREISE_PAYMENT === 'success' ? text('Waad furtay dhammaan heerarka!', 'Alle nivåene er nå åpnet!') : '';
  app.innerHTML = `<section class="game-shell"><div class="scenery" aria-hidden="true"></div>
    <header class="topbar"><button class="new-game-button" data-action="restart">${text('Cusub', 'Ny')}</button><div class="level-heading"><p class="eyebrow">${text(`Heerka ${level.id} · ${label[0]}`, `Nivå ${level.id} · ${label[1]}`)}</p>${!premium ? `<small class="free-progress">${text(`${freeLevelCount} heer oo bilaash ah`, `${freeLevelCount} nivåer gratis`)}</small>` : ''}</div><a class="googa-link" href="../">G</a><button class="update-game" data-pwa-update data-update-label="${text('Cusboonaysii', 'Oppdater')}" data-updating-label="${text('Waa la cusboonaysiinayaa …', 'Oppdaterer …')}" type="button">${text('Cusboonaysii', 'Oppdater')}</button><button class="coins" aria-label="Ord funnet">${found.size}</button></header>
    <section class="board-panel"><div class="progress-row"><span>${found.size}/${level.words.length} ${text('eray', 'ord')}</span><span>${state.bonus.length} ${text('dheeraad', 'bonus')}</span></div><div class="word-board">${level.words.map(word => `<div class="word-slot ${found.has(word) ? 'found' : ''}">${found.has(word) ? word : '•'.repeat(word.length)}</div>`).join('')}</div><div class="message" id="message">${complete ? text('Waa la dhammeeyey!', 'Nivået er løst!') : text('Raadi dhammaan erayada', 'Finn alle ordene')}</div></section>
    <section class="controls-panel ${complete ? 'solved' : ''}"><div id="selected" class="preview">${state.letters.join('') || '&nbsp;'}</div><div class="wheel" aria-label="Bokstavhjul">${tiles.map((tile, index) => `<button class="letter" style="--angle:${(360 / tiles.length) * index}deg" data-index="${index}" data-letter="${tile}" aria-label="${tile}">${tile}</button>`).join('')}<div class="wheel-core">✦</div><svg class="swipe-trace" viewBox="0 0 300 300" preserveAspectRatio="none" aria-hidden="true"><polyline points=""></polyline></svg></div><div class="actions"><button class="tool-button" data-action="clear">⌫</button><button class="tool-button primary" data-action="check">${text('Hubi', 'Sjekk')}</button><button class="tool-button" data-action="hint">?</button></div>${state.bonus.length ? `<p class="bonus">${text('Erayo dheeraad ah', 'Bonusord')}: ${state.bonus.join(', ')}</p>` : ''}${atFreeGate ? `<section class="unlock-card"><strong>${text('Safarka sii wad', 'Fortsett reisen')}</strong><span>${text('Fur 991 heer oo kale hal mar.', 'Lås opp 991 brett til med ett kjøp.')}</span>${canPurchase ? `<form action="../checkout.php" method="post"><input type="hidden" name="csrf" value="${window.GOOGA_ORDREISE_PAYMENT_CSRF}"><input type="hidden" name="kind" value="ordreise_lifetime"><button class="unlock-button" type="submit">${text('Fur dhammaan · kr 59', 'Lås opp alle · 59 kr')}</button></form>` : `<a class="unlock-button unlock-link" href="help.php">${text('Eeg rukhsadda Googa', 'Se Googa-abonnement')}</a>`}</section>` : `<button class="next-button ${complete ? 'visible' : ''}" data-action="next">${text('Heerka xiga', 'Neste nivå')}</button>`}${paymentMessage ? `<p class="payment-message">${paymentMessage}</p>` : ''}${window.GOOGA_ORDREISE_OWNER ? `<button class="owner-language" data-action="language">${state.norwegian ? 'Af-Soomaali' : 'Norsk'} · eier</button>` : ''}</section></section>`;
  wireWheel();
  app.querySelectorAll('[data-action]').forEach(button => button.addEventListener('click', () => action(button.dataset.action)));
}

function wireWheel() {
  const wheel = app.querySelector('.wheel'); const trace = wheel.querySelector('polyline'); let drawing = false; let points = [];
  const pointFor = event => { const box = wheel.getBoundingClientRect(); return [((event.clientX - box.left) / box.width) * 300, ((event.clientY - box.top) / box.height) * 300]; };
  const selectAt = event => { const element = document.elementFromPoint(event.clientX, event.clientY); const button = element?.closest('.letter'); if (!button || state.selectedTiles.has(button.dataset.index)) return; state.selectedTiles.add(button.dataset.index); state.letters.push(button.dataset.letter); button.classList.add('selected'); app.querySelector('#selected').textContent = state.letters.join(''); };
  const draw = event => { points.push(pointFor(event)); trace.setAttribute('points', points.map(point => point.join(',')).join(' ')); selectAt(event); };
  wheel.addEventListener('pointerdown', event => { if (!event.target.closest('.letter')) return; drawing = true; points = [pointFor(event)]; trace.setAttribute('points', points[0].join(',')); wheel.setPointerCapture?.(event.pointerId); selectAt(event); event.preventDefault(); });
  wheel.addEventListener('pointermove', event => { if (drawing) draw(event); });
  wheel.addEventListener('pointerup', () => { drawing = false; });
  wheel.addEventListener('pointercancel', () => { drawing = false; });
}

function say(value) { app.querySelector('#message').textContent = value; }
function action(type) { const level = current(); if (type === 'language') { state.norwegian = !state.norwegian; render(); return; } if (type === 'clear') { resetSelection(); render(); return; } if (type === 'restart') { state.level = 0; state.found = {}; resetSelection(); state.bonus = []; save(); render(); return; } if (type === 'next') { state.level = (state.level + 1) % playableLevelCount; resetSelection(); state.bonus = []; save(); render(); return; } if (type === 'hint') { const found = new Set(state.found[level.id] || []); const word = level.words.find(item => !found.has(item)); if (word) say(text(`Tilmaan: ${word[0]}`, `Hint: ${word[0]}`)); return; } if (type !== 'check') return; const word = state.letters.join(''); resetSelection(); if (word.length < 2 || !makeable(word, level.letters)) { render(); say(text('Dooro xarfo sax ah', 'Velg gyldige bokstaver')); return; } const found = new Set(state.found[level.id] || []); if (level.words.includes(word)) { found.add(word); state.found[level.id] = [...found]; save(); render(); return; } if (words.has(word)) { if (!state.bonus.includes(word)) state.bonus.push(word); render(); say(text('Eray dheeraad ah!', 'Bonusord!')); return; } render(); say(text('Eraygan kuma jiro ordbanka', 'Ordet finnes ikke i ordbanken')); }
render();
