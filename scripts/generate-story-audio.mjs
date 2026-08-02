#!/usr/bin/env node

import { mkdirSync, readFileSync, existsSync } from 'node:fs';
import { basename, dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';
import vm from 'node:vm';

const thisDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(thisDir, '..');
const bankPath = join(rootDir, 'story-bank.js');
const outputDir = join(rootDir, 'audio', 'stories');
const edgeTtsBin = '/opt/homebrew/lib/node_modules/openclaw/node_modules/node-edge-tts/bin.js';
const nodeBin = '/opt/homebrew/bin/node';
const voice = 'so-SO-UbaxNeural';

if (!existsSync(nodeBin) || !existsSync(edgeTtsBin)) throw new Error('Missing local node-edge-tts installation.');
mkdirSync(outputDir, { recursive: true });

const context = { window: {} };
vm.createContext(context);
vm.runInContext(readFileSync(bankPath, 'utf8'), context);
const stories = context.window.GOOGA_STORIES;
if (!Array.isArray(stories) || !stories.length) throw new Error('No GOOGA_STORIES found.');

const clips = new Map();
for (const story of stories) {
  for (const scene of story.scenes) {
    if (scene.audio) clips.set(basename(scene.audio), scene.so);
    for (const word of scene.words) if (word.audio) clips.set(basename(word.audio), word.so);
  }
}

for (const [filename, text] of clips) {
  execFileSync(nodeBin, [edgeTtsBin, '--text', text, '--voice', voice, '--rate', '-12%', '--filepath', join(outputDir, filename)], { stdio: 'inherit' });
}
console.log(`Generated ${clips.size} Ubax story clips.`);
