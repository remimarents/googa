(() => {
  const icon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4Z"/><path d="M16 9.2a4 4 0 0 1 0 5.6M18.5 6.7a7.5 7.5 0 0 1 0 10.6"/></svg>';
  let activeAudio = null;
  let activeButton = null;

  const stop = () => {
    if (activeAudio) {
      activeAudio.pause();
      activeAudio.currentTime = 0;
      activeAudio = null;
    }
    if ('speechSynthesis' in window) window.speechSynthesis.cancel();
    if (activeButton) activeButton.classList.remove('is-playing');
    activeButton = null;
  };

  const browserSpeech = (text, button) => {
    if (!('speechSynthesis' in window)) return;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'so-SO';
    utterance.rate = 0.82;
    utterance.onend = stop;
    utterance.onerror = stop;
    activeButton = button;
    button.classList.add('is-playing');
    window.speechSynthesis.speak(utterance);
  };

  const play = (text, audioPath, button) => {
    stop();
    if (typeof window.GOOGA_AUDIO_PATH_RESOLVER === 'function') {
      try { audioPath = window.GOOGA_AUDIO_PATH_RESOLVER(audioPath, text) || audioPath; } catch (_) {}
    }
    if (!audioPath) {
      browserSpeech(text, button);
      return;
    }
    const audio = new Audio(audioPath);
    activeAudio = audio;
    activeButton = button;
    button.classList.add('is-playing');
    audio.addEventListener('ended', stop, { once: true });
    audio.addEventListener('error', () => {
      activeAudio = null;
      browserSpeech(text, button);
    }, { once: true });
    audio.play().catch(() => {
      activeAudio = null;
      browserSpeech(text, button);
    });
  };

  const enhance = (root = document) => {
    root.querySelectorAll('[data-speak-button]:not([data-speak-ready])').forEach(button => {
      button.dataset.speakReady = '1';
      button.innerHTML = icon;
      button.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        play(button.dataset.speakSo || '', button.dataset.speakAudio || '', button);
      });
    });
    root.querySelectorAll('[data-speak-so]:not([data-speak-ready])').forEach(element => {
      element.dataset.speakReady = '1';
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'read-aloud';
      button.innerHTML = icon;
      button.setAttribute('aria-label', 'Dhegeyso qoraalkan Af-Soomaaliga');
      button.title = 'Dhegeyso Af-Soomaaliga';
      button.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        play(element.dataset.speakSo || element.textContent.trim(), element.dataset.speakAudio || '', button);
      });
      element.append(' ', button);
    });
  };

  window.GoogaReadAloud = { enhance, play, stop, icon };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => enhance());
  else enhance();
})();
