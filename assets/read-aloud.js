(() => {
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
      button.textContent = '🔊';
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

  window.GoogaReadAloud = { enhance, play, stop };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => enhance());
  else enhance();
})();
