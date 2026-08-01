#!/usr/bin/env node

import { mkdirSync, existsSync } from 'node:fs';
import { join, resolve } from 'node:path';
import { execFileSync } from 'node:child_process';

const rootDir = resolve(import.meta.dirname, '..');
const outputDir = join(rootDir, 'audio', 'ui');
const edgeTtsBin = '/opt/homebrew/lib/node_modules/openclaw/node_modules/node-edge-tts/bin.js';
const nodeBin = '/opt/homebrew/bin/node';
const voice = 'so-SO-UbaxNeural';

const clips = {
  'login-hero': 'Halxiraalo Af-Soomaali ah. Waqtiyo yar oo wadajir ah. Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah.',
  'login-account': 'Hore ayaad xisaab u leedahay? Geli e-mailkaaga iyo furaha sirta ah.',
  'login-reset': 'Ma illowday furaha sirta ah? Waxaad samayn kartaa mid cusub.',
  'plan-trial': 'Tijaabada Googa. Shan karoon laba maalmood, ka dib konton karoon bishii.',
  'plan-monthly': 'Rukhsadda Googa. Konton karoon bishii.',
  'login-terms': 'Stripe ayaa si ammaan ah u maamusha lacag-bixinta. Carruurtu waxay ku xirmaan QR-ka qoyska kadib marka waalidku galo.',
  'payment-cancelled': 'Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.',
  'payment-error': 'Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day.',
  'payment-processing': 'Lacag-bixinta waa la xaqiijinayaa. Dib u hubi marin daqiiqad yar gudahood.',
  'portal-error': 'Maareynta rukhsadda lama furi karin hadda.',
  'access-title': 'Akoonkaaga waa la aqoonsaday.',
  'access-intro': 'Waad soo gashay, laakiin koontadan weli ma leh marin firfircoon gudaha Googa. Dooro tijaabada ama rukhsadda bishii si aad u bilowdo.',
  'access-offer': 'Tijaabadu waxay ku kacaysaa shan karoon hadda, ka dibna rukhsaddu waa konton karoon bishii ilaa aad joojiso. Marka lacag-bixinta la xaqiijiyo, waxaad si toos ah u geli doontaa app-ka.',
  'family-title': 'La wadaag carruurtaada.',
  'family-qr': 'QR-koodhkani wuu joogto yahay. Qalab cusub marka uu sawiro, adiga ayaa marka hore oggolaanaya codsiga. Qalabka afraad wuxuu beddelaa kii ugu horreeyey.',
  'family-save': 'Ku kaydi Googa. Ka dhig app ku jira shaashadda guriga. iPhone ama iPad: Safari, La wadaag, Ku dar Shaashadda Guriga, dabadeed Ku dar. Android: Chrome, fur liiska, kadib Ku rakib app-ka ama Ku dar shaashadda guriga.',
  'family-pending': 'Sug oggolaanshaha waalidka. Codsi ayaa loo diray qalabka waalidka. Marka la oggolaado, Googa si toos ah ayuu u furmayaa.',
  'password-new': 'Samee furaha cusub. Dooro fure gaar ah oo ka kooban ugu yaraan toban xaraf.',
  'password-expired': 'Xiriirku wuu dhacay. Xiriirkan lama isticmaali karo ama waqtigiisii ayaa dhammaaday. Codso xiriir cusub.',
  'welcome': 'Ciyaar. Ogaansho. Af-Soomaali. Maskaxda yar, sir weyn! Dooro da’daada. Googa wuxuu kuu hayaa halxiraale maanta.',
  'age-0': 'Eber ilaa lix sano. Bilow yar.',
  'age-7': 'Toddoba ilaa saddex iyo toban sano. Baareyaal.',
  'age-13': 'Saddex iyo toban ilaa shan iyo toban sano. Maskax fiiqan.',
  'age-16': 'Lix iyo toban sano iyo ka weyn. Sirdoon.'
};

if (!existsSync(nodeBin) || !existsSync(edgeTtsBin)) throw new Error('Missing local node-edge-tts installation.');
mkdirSync(outputDir, { recursive: true });
for (const [name, text] of Object.entries(clips)) {
  execFileSync(nodeBin, [edgeTtsBin, '--text', text, '--voice', voice, '--rate', '-12%', '--filepath', join(outputDir, `${name}.mp3`)], { stdio: 'inherit' });
}
console.log(`Generated ${Object.keys(clips).length} Ubax UI clips.`);
