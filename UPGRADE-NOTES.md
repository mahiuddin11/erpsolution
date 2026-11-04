# আপগ্রেড নোট — Laravel 12 (Laravel 8 → 9 → 10 → 11 → 12)

এই zip-এ Laravel 12.70.2-এ আপগ্রেড করা ERP প্রজেক্টের সম্পূর্ণ কপি আছে (vendor/ সহ)।
মূল প্রজেক্টে কোনো business logic বা UI/UX পরিবর্তন করা হয়নি — শুধু framework ও প্যাকেজ
আপগ্রেডের জন্য অফিসিয়াল গাইডে যা করতে বলা হয়েছে, সেটুকুই করা হয়েছে।

## সংস্করণের পথ
Laravel 8.83.29 → 9.52.22 → 10.50.4 → 11.57.1 → 12.70.2 (এক ধাপ করে, প্রতিটি ধাপ যাচাই করে)।

## PHP-এর প্রয়োজন
ডেপ্লয় মেশিনে PHP **8.2 বা তার বেশি** লাগবে (এই কপি PHP 8.3.6-এ যাচাই করা হয়েছে)।

## প্যাকেজ পরিবর্তন (সব ধাপ মিলিয়ে)
- `laravel/framework`: ^8.40 → ^9.0 → ^10.0 → ^11.0 → ^12.0
- `laravel/ui`: ^3.3 → ^4.0 (v4.6.3 ইনস্টল করা)
- `nunomaduro/collision`: ^5.0 → ^6.1 → ^7.0 → ^8.1 (v8.9.5 ইনস্টল করা)
- `phpunit/phpunit`: ^9.3.3 → ^10.0 → ^11.0 (11.5.57 ইনস্টল করা; কোনো PHP test নেই)
- `spatie/laravel-ignition`: নতুন (v2.12.1) — `facade/ignition`-এর বদলে
- `laravel/telescope`: 4.9 (exact) → ^4.0 → ^5.0 (v5.26.0 ইনস্টল করা)
- `nesbot/carbon`: Laravel 11 পর্যন্ত ^2.0-এ pin করা ছিল; **Laravel 12-এ Carbon 2 support
  বাদ দেওয়া হয়েছে, তাই এখন Carbon 3.15.1** (গাইডের বাধ্যতামূলক পরিবর্তন — নিচে প্রভাব দেখুন)
- `intervention/image`: ^2.5 (v2.7.2, অপরিবর্তিত)
- `rats/zkteco` (V002), `riskihajar/terbilang` (1.2.5): অপরিবর্তিত, Laravel 12-এ resolve হয়েছে
- বাদ দেওয়া হয়েছে:
  - `fideloper/proxy` — Laravel 9 থেকে framework নিজেই trusted proxy সামলায়
  - `fruitcake/laravel-cors` — Laravel 10 সমর্থন করে না; framework-এর
    `Illuminate\Http\Middleware\HandleCors` ব্যবহার করা হয়েছে
- `minimum-stability`: dev → stable
- Composer 2.10 Laravel 10-এর EOL security advisory-র জন্য ইনস্টল আটকে দিয়েছিল;
  composer.json-এ `policy.advisories.ignore-id`-তে ৭টি PKSA ID যোগ করা হয়েছে (শুধু ওই ID-গুলো;
  advisory block বন্ধ করা হয়নি)।

## বদলানো app-code ফাইল (শুধু framework-এর প্রয়োজনে)
- `composer.json`, `composer.lock` — version constraint ও dependency আপডেট
- `app/Http/Middleware/TrustProxies.php` — `Fideloper\Proxy\TrustProxies` → `Illuminate\Http\Middleware\TrustProxies`
- `config/mail.php` — `auth_mode` লাইন বাদ (Symfony Mailer-এ আর ব্যবহৃত হয় না)
- `app/Http/Kernel.php` — `\Fruitcake\Cors\HandleCors` → `\Illuminate\Http\Middleware\HandleCors`
- `phpunit.xml` — `<coverage>` থেকে `processUncoveredFiles` attribute বাদ (PHPUnit 10)
- Laravel 11 ও 12 ধাপে কোনো PHP app-code ফাইল বদলানো হয়নি। গাইডের নির্দেশ অনুযায়ী পুরনো
  (Laravel 10) application structure রাখা হয়েছে — Kernel.php, exception handler, provider
  ও config ফাইলে কোনো structure migration করা হয়নি।
- কোনো controller, repository, model, Blade, route বা query logic বদলানো হয়নি।

## ⚠️ Carbon 3-এর আচরণ পরিবর্তন — পড়া জরুরি
Laravel 12-এ Carbon 3 বাধ্যতামূলক। মেপে দেখা গেছে: `diffInDays()` এখন **signed float**
ফেরত দেয় (আগে int)। যেমন: অতীত→ভবিষ্যৎ `+27.21875`, উল্টোদিকে `-27.21875`।
এর প্রভাব এই প্রজেক্টে (app-code বদলানো হয়নি, তাই আচরণ framework-এর নতুন নিয়মে চলবে):
- Financial Dashboard aging: `$days` এখন ভগ্নাংশ হতে পারে (guard-এর জন্য ধনাত্মক থাকে)।
  ৩০/৬০/৯০ দিনের bucket সীমানায় ভগ্নাংশের কারণে সামান্য পার্থক্য হতে পারে।
  আগের মতো int চাইলে ভবিষ্যতে `(int)` cast যোগ করতে হবে — সেটা business সিদ্ধান্ত,
  এই আপগ্রেডে করা হয়নি।
- HRM leave-day হিসাব: তারিখ-শুধু মানের জন্য পূর্ণ float আসে (যেমন 4.0), `+1`-এর পরেও
  পূর্ণ সংখ্যাই থাকে; সময়সহ মান হলে ভগ্নাংশ আসতে পারে।
- `diff()` এখন CarbonInterval ফেরত দেয়; `format('%a days %h:%i')` আগের মতোই কাজ করে।

## যাচাইয়ের ফলাফল (Laravel 12)
- `composer validate`: পাস
- `php artisan --version`: Laravel Framework 12.70.2
- বদলানো PHP ফাইলে `php -l`: syntax error নেই
- `php artisan about`: ঠিকমতো চলে (Laravel 12.70.2, PHP 8.3.6)
- `php artisan route:list`: আগের ত্রুটিই আছে — `App\Transformers\ConversionTransformer`
  ক্লাস নেই (আপগ্রেডের আগে থেকেই ছিল; আপগ্রেডের ভাঙন নয়)
- HTTP smoke test (MariaDB চালু করে, 127.0.0.1:8000): `/` → 500, `/auth/login` → 500,
  `/api/financial-dashboard/kpis` → 302 (login redirect)। একই .env ও DB দিয়ে মূল Laravel 8
  code-ও হুবহু একই ফল দেয় (একই 500, একই `companies` query-তে `erpqa` access denied, একই 302) —
  অর্থাৎ 500-এর কারণ এই VM-এর DB credential mismatch (আগে থেকে থাকা পরিবেশগত সমস্যা),
  আপগ্রেডের regression নয়। DB-ছাড়া behavior (boot, routing, auth redirect) L8 ও L12-এ অভিন্ন।
  200 login-page render এই পরিবেশে যাচাই করা যায়নি। পরীক্ষার পর MariaDB ও server বন্ধ করা হয়েছে।

## ডেপ্লয় ধাপ
1. পুরনো ইনস্টলের backup নিন (code + ডাটাবেস)।
2. এই zip extract করুন। `.env` ফাইল zip-এ নেই — পুরনো ইনস্টলের `.env` ব্যবহার করুন
   (`.env.example` রেফারেন্সের জন্য আছে)।
3. vendor/ zip-এ আছে, তাই `composer install` লাগবে না।
4. নতুন migration নেই, তাই `php artisan migrate` লাগবে না। **সতর্কতা:** Telescope 5 আর
   নিজে থেকে migration load করে না। এই ডাটাবেসে telescope টেবিল আগে থেকেই আছে বলে
   migration publish করা হয়নি — বিদ্যমান ডাটাবেসে `vendor:publish --tag=telescope-migrations`
   চালাবেন না। শুধু একদম নতুন (খালি) ডাটাবেসে ওই কমান্ড চালিয়ে তারপর migrate করুন।
5. চালান: `php artisan config:clear`, `php artisan view:clear`, `php artisan route:clear`।
6. Login page ও dashboard খুলে দেখুন; Trial Balance মিলিয়ে দেখুন। Carbon 3-এর কারণে
   aging/leave-day হিসাবে উপরের ⚠️ অংশ মিলিয়ে দেখুন।

## জানা বিষয়
- `php artisan route:list` চালাতে ConversionTransformer-এর আগের ত্রুটি ঠিক করতে হবে
  (আলাদা কাজ; এই আপগ্রেডের অংশ নয়)।
- Telescope production-এ বন্ধ রাখার নিয়ম আগের মতোই প্রযোজ্য।
- পুরনো migration-গুলোতে `float('col', 12, 2)` আকারের কলাম আছে; Laravel 11 থেকে float-এর
  total/places argument আর ব্যবহৃত হয় না। ইতিমধ্যে চলা migration-এ এর কোনো প্রভাব নেই।
