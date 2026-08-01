<?php
declare(strict_types=1);

const GOOGA_OWNER_EMAILS = [
    'remi@marents.no',
    'kadiye86@gmail.com',
];
const GOOGA_PRIVATE_LOGOUT_EMAIL = 'remi@marents.no';

const GOOGA_STORAGE_FILE = __DIR__ . '/storage/googa-data.json';
const GOOGA_VERSION_FILE = __DIR__ . '/version.json';
const GOOGA_MONTHLY_PRICE_ID = 'price_1TzhsqBgfqMKZQayEiKoejoO';
const GOOGA_INTRO_PRICE_ID = 'price_1TzedtBgfqMKZQayCgFS0j8s';
const GOOGA_ANNUAL_PRICE_ID = 'price_1TzmDwBgfqMKZQayxKbWwxqG';
const GOOGA_PRODUCT_ID = 'prod_UzdvEnhniM03Gn';
const GOOGA_GIFT_PRODUCT_ID = 'prod_UzlljFWqn5hJtb';
const GOOGA_GIFT_PRICE_IDS = [
    3 => 'price_1TzmEEBgfqMKZQaytImS1WYO',
    6 => 'price_1TzmEEBgfqMKZQay6u2NRsPv',
    12 => 'price_1TzmEFBgfqMKZQaySvycir8h',
];
const GOOGA_ORGANIZATION_PRODUCT_ID = 'prod_UzllCH1ZAPbwWw';
const GOOGA_ORGANIZATION_PRICE_ID = 'price_1TzmEFBgfqMKZQayzpsOlsYn';
const GOOGA_ORDREISE_LIFETIME_PRODUCT_ID = 'prod_UzkeQIlE4R9YVf';
const GOOGA_ORDREISE_LIFETIME_PRICE_ID = 'price_1Tzl8kBgfqMKZQayczXhHMGe';
const GOOGA_ORDREISE_FREE_START_AT = '2026-09-01T00:00:00+02:00';
const GOOGA_STRIPE_ENV_FILE = '/home/ferdighet/.googa-stripe.env';
const GOOGA_FAMILY_SECRET_FILE = '/home/ferdighet/.googa-family.env';
const GOOGA_PUBLIC_BASE_URL = 'https://ferdighet.no/googa';
// Owners can always preview stories. Set to true only when the first story season is ready for subscribers.
const GOOGA_STORIES_PUBLIC = false;
