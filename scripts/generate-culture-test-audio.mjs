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
  ['disclaimer.mp3', test.disclaimerSo]
]);
test.questions.forEach((question, index) => {
  const number = String(index + 1).padStart(2, '0');
  clips.set(`question-${number}.mp3`, question.so);
  question.options.forEach(option => clips.set(`question-${number}-${option.id}.mp3`, option.so));
});
Object.entries(test.dimensions).forEach(([key, dimension]) => clips.set(`axis-${key}.mp3`, `${dimension.so}. ${dimension.copySo}.`));
test.profiles.forEach(profile => clips.set(`profile-${profile.key}.mp3`, `${profile.so}. ${profile.summarySo}. ${test.resultIntroSo}`));
Object.entries(test.actions).forEach(([key, action]) => clips.set(`action-${key}.mp3`, action.so));

for (const [filename, text] of clips) {
  execFileSync(nodeBin, [edgeTtsBin, '--text', text, '--voice', voice, '--rate', '-10%', '--filepath', join(outputDir, filename)], { stdio: 'inherit' });
}
console.log(`Generated ${clips.size} prerecorded ${voiceKey} clips in ${outputDir}`);
