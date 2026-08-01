#!/usr/bin/env node

import { mkdirSync, readFileSync, existsSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';
import vm from 'node:vm';

const thisDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(thisDir, '..');
const bankPath = join(rootDir, 'bank.js');
const audioDir = join(rootDir, 'audio');
const edgeTtsBin = '/opt/homebrew/lib/node_modules/openclaw/node_modules/node-edge-tts/bin.js';
const nodeBin = '/opt/homebrew/bin/node';
const voice = 'so-SO-UbaxNeural';

if (!existsSync(nodeBin) || !existsSync(edgeTtsBin)) {
  console.error('Missing local node-edge-tts installation.');
  process.exit(1);
}

mkdirSync(audioDir, { recursive: true });

const bankSource = readFileSync(bankPath, 'utf8');
const context = { window: {} };
vm.createContext(context);
vm.runInContext(bankSource, context);
const bank = context.window.GOOGA_BANK;

if (!Array.isArray(bank) || !bank.length) {
  console.error('No GOOGA_BANK found in bank.js');
  process.exit(1);
}

const renderSpeech = (id, text, kind) => {
  const outputPath = join(audioDir, `${id}-${kind}.mp3`);
  execFileSync(nodeBin, [edgeTtsBin, '--text', text, '--voice', voice, '--rate', '-12%', '--filepath', outputPath], {
    stdio: 'inherit'
  });
};

for (const item of bank) {
  renderSpeech(item.id, item.q, 'q');
  renderSpeech(item.id, item.a, 'a');
}

console.log(`Generated ${bank.length * 2} audio files in ${audioDir}`);
