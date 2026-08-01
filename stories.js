(() => {
  const stories = window.GOOGA_STORIES || [];
  const icon = window.GoogaReadAloud?.icon || '🔊';
  const $ = id => document.getElementById(id);
  let language = 'so';
  let activeStory = null;
  let sceneIndex = 0;
  let activitySolved = false;
  let lastFocus = null;

  const t = (so, no) => language === 'no' ? no : so;
  const localizeStatic = () => {
    document.documentElement.lang = language;
    document.querySelectorAll('[data-so]').forEach(el => { el.textContent = t(el.dataset.so, el.dataset.no); });
    $('storyLanguage').textContent = language === 'so' ? '🇳🇴' : '🇸🇴';
    $('storyLanguage').setAttribute('aria-label', language === 'so' ? 'Vis norsk tekst' : 'Muuji Af-Soomaali');
  };

  function renderLibrary() {
    $('storyGrid').innerHTML = stories.map(story => `
      <button class="story-tile" type="button" data-story="${story.id}" style="--accent:${story.accent};--soft:${story.soft}">
        <img src="${story.image}" alt="">
        <span class="tile-body">
          <span class="tile-top"><span class="tile-age">${story.ageLabel}</span><span class="tile-icon">${story.icon}</span></span>
          <h2>${t(story.title, story.titleNo)}</h2>
          <p>${t(story.subtitle, story.subtitleNo)}</p>
          <span class="tile-action">${t('Fur sheekada','Åpne historien')} <b>→</b></span>
        </span>
      </button>`).join('');
    document.querySelectorAll('[data-story]').forEach(button => button.onclick = () => openStory(button.dataset.story));
  }

  function openStory(id) {
    activeStory = stories.find(story => story.id === id);
    if (!activeStory) return;
    sceneIndex = 0;
    activitySolved = false;
    $('storyLibrary').classList.add('hidden');
    $('storyExperience').classList.remove('hidden');
    $('storyReader').style.setProperty('--active-accent', activeStory.accent);
    $('storyReader').style.setProperty('--active-soft', activeStory.soft);
    $('readerImage').src = activeStory.image;
    $('readerImage').alt = t(activeStory.title, activeStory.titleNo);
    $('readerAge').textContent = activeStory.ageLabel;
    window.scrollTo({top:0});
    renderScene();
    if (activeStory.age === '0') playScene(document.querySelector('.paragraph-play'));
  }

  function renderScene() {
    const scene = activeStory.scenes[sceneIndex];
    $('readerTitle').textContent = t(activeStory.title, activeStory.titleNo);
    $('readerSubtitle').textContent = t(activeStory.subtitle, activeStory.subtitleNo);
    $('readerSupport').textContent = t(activeStory.support, activeStory.supportNo);
    $('tapHint').textContent = activeStory.age === '0'
      ? t('Sawirka ama qoraalka taabo. Codka dhegeyso.','Trykk på bildet eller teksten og lytt.')
      : t('Qoraalka taabo si aad u hesho tarjumid iyo erayo.','Trykk på teksten for oversettelse og ordforklaringer.');
    $('sceneLabel').textContent = `${t('Qayb','Del')} ${sceneIndex + 1} / ${activeStory.scenes.length}`;
    $('sceneProgress').style.width = `${((sceneIndex + 1) / activeStory.scenes.length) * 100}%`;
    const youngWords = activeStory.age === '0' ? `<div class="young-words">${scene.words.map((word,i) => `<button class="young-word" type="button" data-young-word="${i}"><b>${word.icon}</b><span>${word.so}</span></button>`).join('')}</div>` : '';
    $('sceneCard').innerHTML = `<div class="paragraph-row"><button class="story-paragraph" id="storyParagraph" type="button">${scene.so}</button><button class="paragraph-play" type="button" aria-label="Dhegeyso">${icon}</button></div>${youngWords}`;
    $('storyParagraph').onclick = event => openSupport(event.currentTarget);
    document.querySelector('.paragraph-play').onclick = event => playScene(event.currentTarget);
    document.querySelectorAll('[data-young-word]').forEach(button => button.onclick = () => playWord(scene.words[Number(button.dataset.youngWord)], button, true));
    $('previousScene').disabled = sceneIndex === 0;
    $('nextScene').classList.toggle('hidden', sceneIndex === activeStory.scenes.length - 1);
    $('storyActivity').classList.toggle('hidden', sceneIndex !== activeStory.scenes.length - 1);
    if (sceneIndex === activeStory.scenes.length - 1) renderActivity();
    localizeStatic();
  }

  function playScene(button) {
    const scene = activeStory.scenes[sceneIndex];
    window.GoogaReadAloud?.play(scene.so, scene.audio, button);
  }

  function playWord(word, button, showMeaning = false) {
    window.GoogaReadAloud?.play(word.so, word.audio, button);
    if (showMeaning) {
      button.setAttribute('title', `${word.so} = ${word.no}`);
      button.setAttribute('aria-label', `${word.so}. ${word.no}`);
    }
    if (!$('wordResult').classList.contains('hidden') || $('supportOverlay').classList.contains('hidden') === false) {
      $('wordResult').textContent = `${word.icon} ${word.so} = ${word.no}`;
      $('wordResult').classList.remove('hidden');
    }
  }

  function openSupport(trigger) {
    const scene = activeStory.scenes[sceneIndex];
    lastFocus = trigger;
    $('supportOverlay').style.setProperty('--overlay-accent', activeStory.accent);
    $('supportOverlay').style.setProperty('--overlay-soft', activeStory.soft);
    $('supportLevel').textContent = `${activeStory.ageLabel} · ${t(activeStory.support, activeStory.supportNo)}`;
    $('supportTitle').textContent = activeStory.age === '0' ? t('Dhegeyso oo eeg','Lytt og se') : t('Macnaha qoraalka','Hjelp til teksten');
    $('supportOriginal').textContent = scene.so;
    $('supportNorwegian').textContent = scene.no;
    $('supportNote').textContent = scene.noteNo || '';
    $('supportNote').classList.toggle('hidden', !scene.noteNo);
    $('wordHeading').textContent = activeStory.age === '0' ? t('Sawirka taabo','Trykk på et bilde') : t('Eray taabo oo dhegeyso','Trykk på et ord og lytt');
    $('wordChips').innerHTML = scene.words.map((word,i) => `<button class="word-chip" type="button" data-word="${i}"><b>${word.icon}</b><span>${word.so}</span></button>`).join('');
    $('wordResult').classList.add('hidden');
    $('sheetPlay').innerHTML = icon;
    $('sheetPlay').onclick = event => playScene(event.currentTarget);
    document.querySelectorAll('[data-word]').forEach(button => button.onclick = () => playWord(scene.words[Number(button.dataset.word)], button));
    $('supportOverlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    $('sheetClose').focus();
    if (activeStory.age === '0') playScene($('sheetPlay'));
  }

  function closeSupport() {
    window.GoogaReadAloud?.stop();
    $('supportOverlay').classList.add('hidden');
    document.body.style.overflow = '';
    lastFocus?.focus();
  }

  function renderActivity() {
    const activity = activeStory.activity;
    $('activityPrompt').textContent = t(activity.promptSo, activity.promptNo);
    $('activityFeedback').classList.add('hidden');
    $('activityOptions').innerHTML = activity.options.map((option,i) => `<button class="activity-option" type="button" data-option="${i}"><b>${option.icon}</b><span>${t(option.so, option.no)}</span></button>`).join('');
    document.querySelectorAll('[data-option]').forEach(button => button.onclick = () => answerActivity(button, activity.options[Number(button.dataset.option)]));
  }

  function answerActivity(button, option) {
    if (activitySolved) return;
    const correct = option.so === activeStory.activity.answer;
    if (!correct) { button.classList.add('wrong'); return; }
    activitySolved = true;
    button.classList.add('correct');
    $('activityFeedback').textContent = t(activeStory.activity.successSo, activeStory.activity.successNo);
    $('activityFeedback').classList.remove('hidden');
  }

  $('readerBack').onclick = () => {
    window.GoogaReadAloud?.stop();
    activeStory = null;
    $('storyExperience').classList.add('hidden');
    $('storyLibrary').classList.remove('hidden');
    window.scrollTo({top:0});
  };
  $('previousScene').onclick = () => { if (sceneIndex > 0) { sceneIndex--; activitySolved = false; renderScene(); } };
  $('nextScene').onclick = () => { if (sceneIndex < activeStory.scenes.length - 1) { sceneIndex++; activitySolved = false; renderScene(); if (activeStory.age === '0') playScene(document.querySelector('.paragraph-play')); } };
  $('storyLanguage').onclick = () => { language = language === 'so' ? 'no' : 'so'; localizeStatic(); renderLibrary(); if (activeStory) renderScene(); };
  $('sheetClose').onclick = closeSupport;
  $('supportDismiss').onclick = closeSupport;
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && !$('supportOverlay').classList.contains('hidden')) closeSupport(); });

  renderLibrary();
  localizeStatic();
})();
