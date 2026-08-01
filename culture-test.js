(() => {
  const test = window.GOOGA_CULTURE_TEST;
  const $ = id => document.getElementById(id);
  const state = { index: 0, answers: [], norwegian: false };
  const show = id => ['cultureIntro','cultureQuestion','cultureResult'].forEach(name => $(name).classList.toggle('hidden', name !== id));
  const playButton = (button, text, audio) => {
    button.innerHTML = window.GoogaReadAloud.icon;
    button.onclick = event => { event.preventDefault(); window.GoogaReadAloud.play(text, audio, button); };
  };
  const localized = (so, no) => state.norwegian ? no : so;

  function updateLanguage() {
    $('cultureLanguage').innerHTML = state.norwegian ? '🇸🇴 <span>Af-Soomaali</span>' : '🇳🇴 <span>Norsk</span>';
    $('cultureTitle').textContent = localized(test.titleSo, test.titleNo);
    $('cultureSubtitle').textContent = localized(test.subtitleSo, test.subtitleNo);
    $('cultureThesis').textContent = localized(test.introSo, test.introNo);
    $('cultureDisclaimer').textContent = localized(test.disclaimerSo, test.disclaimerNo);
    $('cultureStart').firstElementChild.textContent = state.norwegian ? 'Start testen' : 'Bilow tijaabada';
    if (!$('cultureQuestion').classList.contains('hidden')) renderQuestion();
  }

  function renderQuestion() {
    const question = test.questions[state.index];
    const domain = test.dimensions[question.domain];
    $('cultureProgressLabel').textContent = `${state.index + 1} / ${test.questions.length}`;
    $('cultureProgressFill').style.width = `${((state.index + 1) / test.questions.length) * 100}%`;
    $('cultureDomainIcon').textContent = domain.icon;
    $('cultureDomainName').textContent = state.norwegian ? domain.no : domain.so;
    $('cultureDomainNo').textContent = state.norwegian ? domain.so : domain.no;
    $('cultureQuestionSo').textContent = localized(question.so, question.no);
    $('cultureQuestionNo').textContent = state.norwegian ? question.so : question.no;
    $('cultureQuestionNo').classList.add('hidden');
    $('cultureTranslate').innerHTML = state.norwegian ? '🇸🇴 <span>Eeg Af-Soomaali</span>' : '🇳🇴 <span>Eeg af-Noorwiiji</span>';
    playButton($('cultureQuestionSpeak'), question.so, question.audio);
    $('cultureScale').innerHTML = test.scale.map(item => `<div class="culture-answer-row"><button class="culture-answer${state.answers[state.index] === item.value ? ' selected' : ''}" type="button" data-value="${item.value}"><i>${item.icon}</i><span><b>${localized(item.so,item.no)}</b><small>${state.norwegian ? item.so : item.no}</small></span><em>→</em></button><button class="culture-speak culture-scale-speak" type="button" data-scale="${item.value}" aria-label="Dhegeyso jawaabta"></button></div>`).join('');
    $('cultureScale').querySelectorAll('[data-value]').forEach(button => button.onclick = () => answer(Number(button.dataset.value)));
    $('cultureScale').querySelectorAll('[data-scale]').forEach(button => {
      const item = test.scale.find(entry => entry.value === Number(button.dataset.scale));
      playButton(button, item.so, item.audio);
    });
  }

  function answer(value) {
    state.answers[state.index] = value;
    if (state.index < test.questions.length - 1) {
      state.index += 1;
      renderQuestion();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else renderResult();
  }

  function percentages() {
    const totals = {}, counts = {};
    test.questions.forEach((question, index) => {
      totals[question.domain] = (totals[question.domain] || 0) + Number(state.answers[index] || 0);
      counts[question.domain] = (counts[question.domain] || 0) + 1;
    });
    return Object.fromEntries(Object.keys(totals).map(key => [key, Math.round(totals[key] / (counts[key] * 4) * 100)]));
  }

  function renderResult() {
    const values = percentages();
    const axes = [
      { key:'tools', icon:'🧭', so:'Norway iyo qalabka', no:'Norge og verktøy', value:Math.round((values.norway + values.research) / 2), copySo:'Bulshada, luqadda iyo isticmaalka ilaha', copyNo:'Samfunn, språk og kildebruk' },
      { key:'heritage', icon:'🌿', so:'Soomaaliya iyo hidaha', no:'Somalia og kulturarv', value:Math.round((values.heritage + values.world) / 2), copySo:'Dhaqan nool, luqad iyo fahamka bulshada', copyNo:'Levende arv, språk og samfunnsblikk' },
      { key:'practice', icon:'🌉', so:'Buundada ficilka', no:'Bro i praksis', value:Math.round((values.bridge + values.transmission) / 2), copySo:'Isku xir, baar oo gudbi', copyNo:'Koble, undersøke og føre videre' }
    ];
    const total = Math.round(axes.reduce((sum, axis) => sum + axis.value, 0) / axes.length);
    const level = test.resultLevels.find(item => total >= item.min);
    const domainOrder = Object.keys(values).sort((a,b) => values[a] - values[b]).slice(0,2);
    $('cultureResultIcon').textContent = level.icon;
    $('cultureResultTitle').textContent = localized(level.so, level.no);
    $('cultureResultTitleNo').textContent = state.norwegian ? level.so : level.no;
    $('cultureResultIntro').textContent = localized(test.resultIntroSo, test.resultIntroNo);
    playButton($('cultureResultSpeak'), `${level.so}. ${test.resultIntroSo}`, `audio/culture-test/result-${level.key}.mp3`);
    $('cultureAxes').innerHTML = axes.map(axis => `<article style="--score:${axis.value}%"><span class="axis-icon">${axis.icon}</span><div><p><strong>${localized(axis.so,axis.no)}</strong><b>${axis.value}%</b></p><i><em></em></i><small>${localized(axis.copySo,axis.copyNo)}</small></div><button class="culture-speak axis-speak" type="button" data-axis="${axis.key}" aria-label="Dhegeyso qaybta"></button></article>`).join('');
    $('cultureAxes').querySelectorAll('[data-axis]').forEach(button => {
      const axis = axes.find(item => item.key === button.dataset.axis);
      playButton(button, `${axis.so}. ${axis.copySo}.`, `audio/culture-test/axis-${axis.key}.mp3`);
    });
    $('cultureActions').innerHTML = domainOrder.map((key,index) => { const action = test.actions[key]; const dimension = test.dimensions[key]; return `<article><span>${dimension.icon}</span><div><small>${index + 1} · ${localized(dimension.so,dimension.no)}</small><p>${localized(action.so,action.no)}</p></div><button class="culture-speak action-speak" type="button" data-action="${key}" aria-label="Dhegeyso tallaabada"></button></article>`; }).join('');
    $('cultureActions').querySelectorAll('[data-action]').forEach(button => { const action = test.actions[button.dataset.action]; playButton(button, action.so, `audio/culture-test/action-${button.dataset.action}.mp3`); });
    show('cultureResult');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  $('cultureLanguage').onclick = () => { state.norwegian = !state.norwegian; updateLanguage(); };
  $('cultureStart').onclick = () => { state.index = 0; state.answers = []; renderQuestion(); show('cultureQuestion'); window.scrollTo(0,0); };
  $('cultureBack').onclick = () => { if (state.index === 0) show('cultureIntro'); else { state.index -= 1; renderQuestion(); } };
  $('cultureTranslate').onclick = () => $('cultureQuestionNo').classList.toggle('hidden');
  $('cultureRestart').onclick = () => { state.index = 0; state.answers = []; show('cultureIntro'); window.scrollTo(0,0); };
  updateLanguage();
})();
