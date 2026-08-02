const fs = require('fs');
const path = require('path');

global.window = {};
require('../culture-test-bank.js');
const test = window.GOOGA_CULTURE_TEST;
const fixtures = JSON.parse(fs.readFileSync(path.join(__dirname, '../config/culture-test-vectors.json'), 'utf8'));
const assert = (condition, message) => { if (!condition) throw new Error(message); };

function score(answers) {
  const totals = { norway:0, heritage:0, bridge:0 };
  const maxima = { norway:0, heritage:0, bridge:0 };
  test.questions.forEach((question, index) => {
    const option = question.options.find(item => item.id === answers[index]);
    assert(option, `Invalid answer at position ${index + 1}`);
    Object.keys(totals).forEach(key => {
      totals[key] += option.score[key];
      maxima[key] += Math.max(...question.options.map(item => item.score[key]));
    });
  });
  const values = Object.fromEntries(Object.keys(totals).map(key => [key, Math.round(totals[key] / maxima[key] * 100)]));
  const average = (values.norway + values.heritage + values.bridge) / 3;
  let profile = 'balanced';
  if (average < 35) profile = 'explorer';
  else if (values.norway >= 55 && values.heritage >= 55 && values.bridge >= 60) profile = 'fusion';
  else if (values.bridge >= 55 && values.bridge >= Math.max(values.norway, values.heritage)) profile = 'bridge';
  else if (values.heritage - values.norway >= 15) profile = 'heritage';
  else if (values.norway - values.heritage >= 15) profile = 'norway';
  return { values, profile };
}

assert(test.version === fixtures.instrument_version, 'Instrument/fixture version mismatch');
assert(test.scoringVersion === fixtures.scoring_version, 'Scoring/fixture version mismatch');
assert(test.questions.length === 24, 'Expected 24 questions');
assert(new Set(test.questions.map(item => item.id)).size === 24, 'Question IDs must be unique');

const coverage = { norway:0, heritage:0, bridge:0 };
for (const question of test.questions) {
  assert(question.options.length === 4, `${question.id} must have four options`);
  coverage[question.category] += 1;
  assert(new Set(question.options.map(item => item.id)).size === 4, `${question.id} option IDs must be unique`);
  for (const option of question.options) {
    assert(option.so && option.no && option.icon, `${question.id}/${option.id} is incomplete`);
    for (const key of Object.keys(coverage)) assert(Number.isInteger(option.score[key]) && option.score[key] >= 0 && option.score[key] <= 3, `${question.id}/${option.id}/${key} score is invalid`);
  }
}
assert(JSON.stringify(coverage) === JSON.stringify({ norway:8, heritage:8, bridge:8 }), 'Category coverage must be 8/8/8');

for (const vector of fixtures.vectors) {
  const actual = score(vector.answers);
  assert(JSON.stringify(actual.values) === JSON.stringify(vector.expected_values), `${vector.id} score changed: ${JSON.stringify(actual.values)}`);
  assert(actual.profile === vector.expected_profile, `${vector.id} profile changed: ${actual.profile}`);
}

console.log(`OK: ${test.questions.length} scenarios, 96 options, 8/8/8 coverage and ${fixtures.vectors.length} scoring vectors`);
