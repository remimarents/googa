#!/usr/bin/env node

import { mkdirSync, readFileSync, existsSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';
import vm from 'node:vm';

const thisDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(thisDir, '..');
const voiceKey = process.argv.find(value => value.startsWith('--voice='))?.split('=')[1] || 'ubax';
const voices = {
  ubax: { id: 'so-SO-UbaxNeural', output: join(rootDir, 'audio', 'culture-test'), sample: 'Waxaan ahay Ubax. Waxaan kuu akhrinayaa Af-Soomaaliga.' },
  muuse: { id: 'so-SO-MuuseNeural', output: join(rootDir, 'audio', 'culture-test', 'muuse'), sample: 'Waxaan ahay Muuse. Waxaan kuu akhrinayaa Af-Soomaaliga.' }
};
if (!voices[voiceKey]) throw new Error('Use --voice=ubax or --voice=muuse.');
const selectedVoice = voices[voiceKey];
const outputDir = selectedVoice.output;
const edgeTtsBin = '/opt/homebrew/lib/node_modules/openclaw/node_modules/node-edge-tts/bin.js';
const nodeBin = '/opt/homebrew/bin/node';
const voice = selectedVoice.id;

if (!existsSync(nodeBin) || !existsSync(edgeTtsBin)) throw new Error('Missing local node-edge-tts installation.');
mkdirSync(outputDir, { recursive: true });

const context = { window: {} };
vm.createContext(context);
vm.runInContext(readFileSync(join(rootDir, 'culture-test-bank.js'), 'utf8'), context);
const test = context.window.GOOGA_CULTURE_TEST;
if (!test?.questions?.length) throw new Error('No culture test bank found.');

const clips = new Map([
  ['voice-sample.mp3', selectedVoice.sample],
  ['intro.mp3', test.introSo],
  ['disclaimer.mp3', test.disclaimerSo],
  ['axis-tools.mp3', 'Norway iyo qalabka. Bulshada, luqadda iyo isticmaalka ilaha.'],
  ['axis-heritage.mp3', 'Soomaaliya iyo hidaha. Dhaqan nool, luqad iyo fahamka bulshada.'],
  ['axis-practice.mp3', 'Buundada ficilka. Isku xir, baar oo gudbi.']
]);
test.questions.forEach((question, index) => clips.set(`question-${String(index + 1).padStart(2, '0')}.mp3`, question.so));
test.scale.forEach(item => clips.set(`scale-${item.value}.mp3`, item.so));
test.resultLevels.forEach(item => clips.set(`result-${item.key}.mp3`, `${item.so}. ${test.resultIntroSo}`));
Object.entries(test.actions).forEach(([key, action]) => clips.set(`action-${key}.mp3`, action.so));

for (const [filename, text] of clips) {
  execFileSync(nodeBin, [edgeTtsBin, '--text', text, '--voice', voice, '--rate', '-10%', '--filepath', join(outputDir, filename)], { stdio: 'inherit' });
}
console.log(`Generated ${clips.size} prerecorded ${voiceKey} clips in ${outputDir}`);
