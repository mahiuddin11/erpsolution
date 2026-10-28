# ERP গভীর বিশ্লেষণ রিপোর্ট — Water Technology BD Ltd

**প্রজেক্ট:** Water Technology BD Limited ERP (Laravel মনোলিথ)
**বিশ্লেষণের তারিখ:** ২০২৬-১০-২৬
**ভিত্তি:** ব্যবহারকারীর Drive থেকে নেওয়া প্রজেক্ট কপি (`~/workspace/erp-qa/project/`) + ডেটাবেস ডাম্প (`wtblcom_soft (1).sql`, ৪৬ MB)
**পদ্ধতি:** সম্পূর্ণ READ-ONLY স্ট্যাটিক বিশ্লেষণ — কোনো প্রজেক্ট ফাইল বদলানো হয়নি, ওয়েব সার্ভার বা MariaDB চালু করা হয়নি, কোনো `.env` ফাইল খোলা বা উদ্ধৃত করা হয়নি, কোনো লাইভ এক্সপ্লয়েট চালানো হয়নি। প্রতিটি ফাইন্ডিং-এর সাথে ফাইল পাথ + লাইন/ফাংশন-এর প্রমাণ দেওয়া হয়েছে।
**ভাষা নীতি:** বর্ণনা বাংলায়; কোড আইডেন্টিফায়ার, ফাইল পাথ, টেবিল/কলাম নাম ইংরেজিতে অপরিবর্তিত।
**সতর্কতা:** এই কপিটি মূল কোড — ব্যবহারকারীর Laragon কপিতে তিনি নিজে যেসব ফিক্স করেছেন (যেমন store/update ফাংশনে `voucher_date` বসানো, ২০২৬-১০-২৫), সেগুলো এখানে নাও থাকতে পারে। যেখানে দুই কপির পার্থক্য প্রাসঙ্গিক, সেখানে তা স্পষ্ট করে লেখা হয়েছে।

---

## সারসংক্ষেপ

এটি একটি বড়, বহু-মডিউলযুক্ত, দীর্ঘদিন ধরে গড়ে ওঠা Laravel ERP — ফাইন্যান্স, ইনভেন্টরি, HRM, সেলস/POS, প্রজেক্ট, প্রোডাকশন, অ্যাসেট, সেটিংস — মোট ১৫৬টি কন্ট্রোলার, ১৩৮টি মডেল, ২১৯টি মাইগ্রেশন, ৫০২টি Blade ভিউ, ৯৬৯টি রুট-কল, ১৩৭টি ডেটাবেস টেবিল (২,৬৬,৩৩৯টি রো)। আর্কিটেকচার প্যাটার্ন সুসংগত (Controller → Service → Repository → Model + DataTables + Transformer), এবং কিছু জায়গায় ভালো ব্যাচড-কোয়েরি প্যাটার্নও আছে (যেমন trial balance)।

কিন্তু তিনটি কাঠামোগত সমস্যা পুরো সিস্টেমকে ঝুঁকিতে ফেলেছে:

1. **নিরাপত্তা ভেঙে পড়া:** লগইন ছাড়াই ডেটাবেস truncate করার টুল, লগইন ছাড়াই অ্যাডমিন-ক্লোন + পুরো DB/ফাইল ব্যাকআপ ডাউনলোড, খোলা রেজিস্ট্রেশন — এবং কোথাও কোনো role এনফোর্সমেন্ট নেই। অর্থাৎ যেকোনো লগইন-করা (এমনকি নিজে রেজিস্টার করা) ব্যবহারকারী ফাইন্যান্স API সহ সবকিছু দেখতে পারে।
2. **হিসাবের অখণ্ডতা (ledger integrity) ঝুঁকিতে:** ভাউচার আপডেটে ডাবল-পোস্টিং, ভুল-id-তে ডিলিট, ডাবল-অ্যাপ্রুভে ডাবল-পোস্টিং, কোনো ভাউচারে ডেবিট=ক্রেডিট চেক নেই, ভাউচার-নম্বর রেস। এগুলো নীরবে লেজার ভুল করে — ভুল সংখ্যা দেখানোই এদের লক্ষণ নয়, ভুল সংখ্যা জমা হওয়াই সমস্যা।
3. **কোড বনাম ডেটাবেস ড্রিফট:** ড্যাশবোর্ড `voucher_date`-ভিত্তিক, কিন্তু TB/BS রিপোর্ট `created_at`-ভিত্তিক (১৫৯ বনাম ০ বার); ডাম্পে `voucher_date` কলামই নেই; টাইপ-ব্যবস্থায় একাধিক ভোকাবুলারি; একই টেবিলের ডুপ্লিকেট মডেল; case-সংবেদনশীল ফাইল-নাম ভাঙন (Linux-এ চলে না, Windows-এ চলে)।

### এক নজরে যাচাইকৃত সংখ্যা

| আইটেম | সংখ্যা | গণনার উৎস |
|---|---|---|
| কন্ট্রোলার | ১৫৬টি ফাইল | `find app/Http/Controllers` |
| মডেল | ১৩৮টি ফাইল | `find app/Models` |
| মাইগ্রেশন | ২১৯টি ফাইল | `ls database/migrations` |
| সিডার | ৪৩টি ফাইল (১৬টি সক্রিয়) | `ls database/seeders` + DatabaseSeeder পড়ে |
| Blade ভিউ | ৫০২টি ফাইল | `find resources/views` |
| রুট-কল (সক্রিয়) | ৯৬৯টি, ১৫টি রুট ফাইলে | কমেন্ট-বাদ দিয়ে গণনা (`Route::resource` = ১ কল) |
| Transformer / Service / Repository | ৫৯ / ৮২ / ৭৮টি ফাইল | `ls` + `find` |
| কনফিগ ফাইল | ১৯টি | `ls config/` |
| কনসোল কমান্ড / জব | ১৫টি / ১টি | `ls app/Console/Commands`, `ls app/Jobs` |
| ডেটাবেস টেবিল | ১৩৭টি (৭৬টিতে ডেটা, ৬১টি খালি) | ডাম্পে `CREATE TABLE` গণনা |
| মোট ডেটা রো (ডাম্প) | ২,৬৬,৩৩৯টি | ডাম্পে INSERT গণনা |
| `account_transactions` | ৬৬,৭০৭টি রো | ডাম্পে INSERT গণনা |
| `chart_of_accounts` | ১,৫৪০টি রো | ডাম্পে INSERT গণনা |
| public/ অ্যাসেট | ১,৯৯২টি ফাইল, ~৮৬ MB | ডিস্ক থেকে মাপা |

### সবচেয়ে জরুরি ৫টি ফাইন্ডিং (বিস্তারিত §২–§৩-এ)

1. **লগইন ছাড়া DB truncate টুল (Critical):** `routes/web.php:127`, `GET/POST /cleare-data` — শুধু ৫টি টেবিল বাদে যেকোনো টেবিল truncate করা যায়; GET-এ সব টেবিলের নাম + row count দেখা যায়।
2. **লগইন ছাড়া অ্যাডমিন-ক্লোন + ব্যাকআপ ডাউনলোড (Critical):** `routes/web.php:399`, `ZktecoController.php:93+` — `/attendance/reset/{id}` থেকে `User::find(1)`-এর ক্লোন + `role_id=1` + হার্ডকোড করা ডিফল্ট পাসওয়ার্ড, `action=backup_db`-তে পুরো DB ডাউনলোড, `action=backup_files`-তে পুরো প্রজেক্ট zip ডাউনলোড, `table_name` দিলে যেকোনো টেবিল truncate।
3. **কোথাও role এনফোর্সমেন্ট নেই + খোলা রেজিস্ট্রেশন (Critical):** কোনো রুটে `role`/`can` মিডলওয়্যার নেই, `app/Policies/` নেই; `Auth::routes()` ডিফল্টে register চালু (`routes/web.php:378`); ফাইন্যান্স API-র ১০টি রুট (`routes/api.php:46-57`) শুধু `auth`-এ সুরক্ষিত, কন্ট্রোলারে কোনো role/branch চেক নেই — অর্থাৎ নিজে রেজিস্টার করা যেকোনো ব্যবহারকারী সব ফাইন্যান্স ডেটা দেখতে পারে।
4. **ভাউচার আপডেটে ডাবল-পোস্টিং ও ভুল-id-তে ডিলিট (Critical — হিসাবের অখণ্ডতা):** `ContraVoucherRepositories.php:195-244` (আপডেটে পুরনো ডিটেইলস/ট্রানজ্যাকশন না মুছে নতুন যোগ), `SupplierPaymentRepositories.php:211-264` (পুরনো লেজার-রো মুছে নতুন id বানিয়ে সেই নতুন id-তে ডিলিট — পুরনো পোস্টিং থেকে যায়), `CustomerPaymentRepositories.php:203` (লেজার-id দিয়ে `Transection` ডিলিট, যেখানে লিংক-কী `payment_id`)।
5. **ভাউচার-নম্বর রেস + ডাবল-অ্যাপ্রুভে ডাবল-পোস্টিং + Dr=Cr চেকের অভাব (Critical):** ভাউচার নম্বর `latest('id')+1` দিয়ে বানানো হয় (একসাথে দুজন করলে একই নম্বর); Debit/Credit `approve()`-এ approve-ফ্ল্যাগ চেক নেই (দুবার approve = দুবার পোস্টিং); চারটি ভাউচার সার্ভিসের ভ্যালিডেশনে শুধু invoice_no/date/note — ডেবিট=ক্রেডিট মেলানো হয় না।

---

## পদ্ধতি ও কভারেজের সীমা (স্বচ্ছভাবে)

- **৭টি বিশ্লেষণ-ধারা** (ইনভেন্টরি, ইনভেন্টরি-এক্সটেনশন, ডেটাবেস, ডেটাবেস-এক্সটেনশন, নিরাপত্তা, নিরাপত্তা-এক্সটেনশন, বাগ, বাগ-এক্সটেনশন, পারফরম্যান্স, পারফরম্যান্স-এক্সটেনশন) থেকে মোট ১০টি বিশ্লেষণ-ফাইল (৩,৩২৮ লাইন) তৈরি হয়েছে; এই রিপোর্ট সেগুলোর সংশ্লেষণ। সমন্বয়কারী নিজে সবচেয়ে জরুরি দাবিগুলো কোড/ডাম্পে পুনরায় যাচাই করেছেন (রুট, কন্ট্রোলার লাইন, মাইগ্রেশন, ডাম্পে `voucher_date`-এর ০ উপস্থিতি, Transformer ফাইল-নাম, session/Telescope/Kernel কনফিগ)।
- **পুরোপুরি পড়া হয়েছে:** ফাইন্যান্স API কন্ট্রোলার (১,৫৭১ লাইন, লাইভ অংশ), ফাইন্যান্স ড্যাশবোর্ড Blade (২,৫৩৪ লাইন), ভাউচার রিপোজিটরি ৪টি, পেমেন্ট রিপোজিটরি ২টি, SuperHelper-এর অ্যাকাউন্ট-ট্রি, Product/ChartOfAccount/AccountTransaction মডেল, Kernel + ১৫টি কমান্ডের signature, সব রুট ফাইলের গ্রুপ-কাঠামো, সব কনফিগ ফাইলের মূল কী, ডাম্পের সব CREATE TABLE।
- **Grep/স্ক্রিপ্ট-ভিত্তিক:** ১৫৬টি কন্ট্রোলারের মেথড-তালিকা, `use App\…` বনাম ফাইল-অস্তিত্ব, `view()`/`route()`-নাম, raw SQL (২০৭টি), `{!! !!}` (২২টি), ফর্ম বনাম `@csrf` (২৪১টি ফাইল), GET delete/status (১৩৯টি), `paginate(` বনাম `->get()` (৬৪২টি), `Cache::`, ৫০২টি Blade-এর কোয়েরি-প্যাটার্ন।
- **স্যাম্পল-ভিত্তিক, সম্পূর্ণ নয়:** ৫০২টি Blade end-to-end পড়া হয়নি (ডিরেক্টরি গণনা + এলাকা-ভিত্তিক স্যাম্পল); ১৫৬টি কন্ট্রোলারের মধ্যে ফাইন্যান্স/ভাউচার/পেমেন্ট/রিপোর্ট অংশ গভীরভাবে পড়া হয়েছে, বাকিগুলো মেথড-তালিকা + grep-স্তরে; Repositories (৭৮) / Services (৮২)-এর প্রতিটি ফাইল লাইন-বাই-লাইন পড়া হয়নি; vendor কোড পরীক্ষা করা হয়নি।
- **যা করা হয়নি (ইচ্ছাকৃত সীমা):** লাইভ সার্ভার/DB চালানো, লাইভ এক্সপ্লয়েট, `.env` পড়া, `route:list` চালানো, DB কোয়েরি, মাপা রেসপন্স-টাইম — তাই সব পারফরম্যান্স-লাভ কোয়ালিটেটিভ ("Nটির বদলে ১টি", "full-table scan-এর বদলে index scan"); কোনো মাপা সময় বা শতাংশ দাবি করা হয়নি। যেসব বিষয়ে লাইভ যাচাই দরকার, সেগুলো "live verification pending" হিসেবে চিহ্নিত (§৫-এর শেষে তালিকা)।

---

## ১. ডকুমেন্টেশন

### ১.১ প্রজেক্ট ওভারভিউ

এটি **Water Technology BD Limited** (ওয়াটার ট্রিটমেন্ট / RO ব্যবসা)-এর একটি মনোলিথিক Laravel ERP। প্রজেক্টের `README.md` নিজেই বলছে এটি *"development and testing purpose only, not the production ready version"*। `PROJECT_DOCUMENTATION.md` (৪৪৬ লাইন) একে HRM, Inventory, Sales, Project, Accounting, Assets মডিউলযুক্ত সম্পূর্ণ ERP হিসেবে বর্ণনা করে — যা কোডের সঙ্গে মোটামুটি মেলে, তবে কিছু দাবি ভুল (§১.১০ দেখুন)।

কাজের ধরন বোঝার মূল সূত্র: প্রায় সব CRUD একই প্যাটার্নে চলে — **Controller → Service → Repository → Model**, সঙ্গে DataTables-এর জন্য `dataProcessing*` মেথড ও আউটপুট সাজাতে **Transformer**।

### ১.২ টেক স্ট্যাক (composer.json বনাম composer.lock যাচাইকৃত)

| উপাদান | দাবি / সংস্করণ | মন্তব্য |
|---|---|---|
| PHP | composer.json: `^7.3\|^8.0`; রানটাইমে PHP 8.3.6 | composer.json-এর রেঞ্জের বাইরে, কিন্তু Laravel 8.83.29 এখানে চলছে |
| laravel/framework | v8.83.29 (`^8.40` দাবি) | |
| laravel/ui | v3.4.6 | auth scaffolding-এর উৎস; লগইন URL `/auth/login` (স্ট্যান্ডার্ড `/login` নয়) |
| laravel/tinker | v2.11.1 | |
| fideloper/proxy | 4.4.2 | TrustProxies-এ ব্যবহৃত — USED |
| fruitcake/laravel-cors | v2.2.0 | Kernel-এ ব্যবহৃত — USED |
| guzzlehttp/guzzle | 7.15.1 | Laravel Http ক্লায়েন্টের মাধ্যমে পরোক্ষ + ২টি import — USED |
| intervention/image | 2.7.2 | **INSTALLED-BUT-UNREFERENCED** — `Intervention\|Image::` প্যাটার্নে app/views/routes-এ ০টি মিল |
| rats/zkteco | V002 | ZKTeco বায়োমেট্রিক অ্যাটেনড্যান্স — USED (২টি কমান্ড + ১টি Job) |
| riskihajar/terbilang | 1.2.5 | ইন্দোনেশীয় সংখ্যা-থেকে-কথা — USED (৯টি Blade-ইনভয়েসে `Terbilang::make`) |
| laravel/telescope | 4.9.0 (require-dev) | Provider + config + public/vendor অ্যাসেট আছে — ডিফল্ট enabled (§২.১৩) |
| phpunit/phpunit | 9.6.35 (require-dev) | ইনস্টল থাকলেও টেস্ট-কোড শূন্য (tests/ -এ কোনো PHP টেস্ট নেই) |
| laravel/sail | v1.25.0 (require-dev) | ডকার-সেটআপের প্রমাণ নেই — কার্যত অব্যবহৃত |
| Frontend | Laravel Mix 6, Vue 2.6.12, Bootstrap 4.6.0, jQuery 3.6, AdminLTE 3.1 | UI মূলত Blade + AdminLTE; Vue শুধু আংশিক; বিল্ড এন্ট্রি `webpack.mix.js`: `resources/js/app.js` → `public/js`, `resources/sass/app.scss` → `public/css`, `.vue()` সক্রিয় |
| ডেটাবেস / কিউ / ফাইলসিস্টেম (config থেকে) | ডিফল্ট `mysql` / `sync` / `local` | কিউ ডিফল্ট `sync` — অর্থাৎ কিউ ওয়ার্কার ছাড়াই সব সিঙ্ক্রোনাস (§৪.১০) |
| কাস্টম কনফিগ | `navigation.php` (২,০৯৪ লাইন, ৬৪০ label), `officeLocation.php`, `purchaseType.php`, `type.php`, `telescope.php` | `type.php`-এর ১–১২ ম্যাপ ভাউচার-পোস্টিংয়ের সংখ্যা-টাইপের সঙ্গে মেলে না (§১.৮) |

### ১.৩ আর্কিটেকচার

- **মনোলিথ, লেয়ার্ড:** HTTP (Controller) → Service (ভ্যালিডেশন + ব্যবসায়িক নিয়ম) → Repository (কোয়েরি) → Model। ব্যতিক্রম: ফাইন্যান্স ড্যাশবোর্ড API কন্ট্রোলার সরাসরি Model কোয়েরি করে (Service/Repository ছাড়া)।
- **রেন্ডারিং:** Blade + AdminLTE; ডেটা-টেবিল DataTables (সার্ভার-সাইড `dataProcessing*` JSON); ড্যাশবোর্ডগুলো Blade + JS-এর মাধ্যমে `/api/*` কল করে ডেটা আনে।
- **অথেনটিকেশন:** Laravel UI (session গার্ড); `api.php`-ও `web` মিডলওয়্যার স্ট্যাক পায় (স্ট্যান্ডার্ড `api` গ্রুপ কমেন্ট-আউট, §১.৫) — অর্থাৎ API-গুলো session/CSRF-নির্ভর, টোকেন-নির্ভর নয়।
- **ভ্যালিডেশন:** কোনো Form Request ক্লাস নেই (`app/Http/Requests/` অস্তিত্বহীন) — ভ্যালিডেশন Service-স্তরের `storeValidation()/updateValidation()` রুল-অ্যারে + Controller-এর `$this->validate()` কলে (app/ জুড়ে ১৯০টি validate-কল)।
- **ইভেন্ট/লিসেনার:** নেই (`app/Events`, `app/Listeners` অস্তিত্বহীন); `EventServiceProvider`-এ শুধু Laravel ডিফল্ট।
- **কিউ/শিডিউল:** ৩টি সক্রিয় শিডিউল — `attendance:sync` প্রতি মিনিটে, `attendance:attendanceAutoclose` রাত ২২:০০, `activitylogs:cleanup` রাত ০০:০০ (`app/Console/Kernel.php:39-41`)। একটি বড় মাসিক-বেতন শিডিউল ব্লক কমেন্ট-আউট আছে।

### ১.৪ ডিরেক্টরি কাঠামো (দ্বিতীয় স্তর পর্যন্ত, যাচাইকৃত)

```
app/
├── Console/            # Kernel.php + Commands/ (১৫টি কমান্ড)
├── Exceptions/         # ১টি (Handler)
├── Helpers/            # ৫টি (Helper, SuperHelper, ActivityLog, CustomHrmHelper, ZktecoMatching)
├── Http/               # Controllers/ (১৫৬) + Middleware/ (৮, সব Laravel ডিফল্ট) + Kernel.php
├── Jobs/               # ১টি (ZktecoSetUser)
├── Models/             # ১৩৮টি
├── Providers/          # ৭টি (App, Auth, Broadcast, Event, Helper, Route, Telescope)
├── Repositories/       # ৭৮টি (১২ সাব-ডিরেক্টরি + রুট-লেভেল ২টি)
├── Rules/              # ১টি (PhoneNumberValidationRules)
├── Services/           # ৮২টি (১২ সাব-ডিরেক্টরি + রুট-লেভেল ৩টি)
└── Transformers/       # ৫৯টি
config/                 # ১৯টি ফাইল (app, auth, broadcasting, cache, cors, database,
                        #   filesystems, hashing, logging, mail, navigation, officeLocation,
                        #   purchaseType, queue, services, session, telescope, type, view)
database/               # factories/ (১টি: UserFactory) + migrations/ (২১৯টি) + seeders/ (৪৩টি)
routes/                 # ১৫টি ফাইল (নিচে §১.৫)
resources/              # css/ (app.css, খালি) + js/ (app.js, bootstrap.js, components/, sales/)
                        #   + lang/ (৪টি ফাইল, শুধু en) + sass/ + views/ (৫০২টি Blade)
public/                 # index.php, backend/, css/, js/, vendor/, robots.txt, web.config,
                        #   mix-manifest.json (মাত্র ২টি এন্ট্রি, ভার্সনিং নেই) — মোট ১,৯৯২টি ফাইল, ~৮৬ MB
tests/                  # কোনো PHP টেস্ট নেই — শুধু quary.sql ও voucherdate.sql
bootstrap/, storage/    # Laravel স্ট্যান্ডার্ড
composer.json, composer.lock, package.json, package-lock.json, webpack.mix.js
PROJECT_DOCUMENTATION.md, README.md, dependency.text, server.php, error_log (একাধিক)
```

`composer.json` autoload-এ `files` হিসেবে `app/Helpers/Helper.php` ও `app/Helpers/ActivityLog.php` সবসময় লোড হয়। `app/Helpers/SuperHelper.php`-এ `getOldAccount()` আছে — ফাইন্যান্স ড্যাশবোর্ডের অ্যাকাউন্ট-ট্রি রেজোলিউশনের মূল হেল্পার।

### ১.৫ মডিউল ম্যাপ (রুট ফাইল + কন্ট্রোলার ডিরেক্টরি থেকে)

| মডিউল | রুট ফাইল | প্রধান কন্ট্রোলার | কাজ |
|---|---|---|---|
| ফাইন্যান্স / অ্যাকাউন্টস | web.php (Settings গ্রুপ) + api.php + reports.php | Settings: DabitVoucher, CreditVoucher, ContraVoucher, JournalVoucher, Accounts, ChartOfAccountTree, Opening, CustomerOpening, Transfer, CashBook, Expense; Api/FinancialDashboardApiController; Dashboard/FinanceDashboardController; Reports: ReportController, FinancialStatementController, StatementOfChangesInEquityController, CheckRegisterController | ভাউচার, চার্ট অফ অ্যাকাউন্টস, TB/BS/আয়-ব্যয়, ক্যাশবুক, ব্যাংকবুক, ডে-বুক, রিটেইন্ড আর্নিং, চেক রেজিস্টার, ফাইন্যান্স ড্যাশবোর্ড API |
| ইনভেন্টরি / স্টক / পারচেজ | inventory.php (সবচেয়ে বড়: ২২৬ রুট-কল) | InventorySetup: Product, Category, MainCategory, Brand, Unit, Conversion, Purchase, PurchaseOrder, PurchaseRequisition, PurchaseReturn, Grn, StockTransfer, StockAjdustment, Adjust, StockReport, Customer, CustomerGroup, Supplier, ProductOpeningStock | পণ্য, ক্যাটাগরি, ব্র্যান্ড, ইউনিট, পারচেজ, PO/PR, GRN, স্টক ট্রান্সফার/অ্যাডজাস্টমেন্ট, ওপেনিং স্টক, স্টক রিপোর্ট |
| HRM | hrm.php (১৩৭ রুট-কল) | Hrm: Employee, Position, Attendance, AttendanceLog, SalarySheet, PaySheet, LeaveApplication, ApproveLeaveApplication, LoneApplication, ApproveLoneApplication, CashApplication, Holiday, Award; Recruitment: CandidateInformation, CandidateShortlist, CandidateSelection; Test/ZktecoSyncController | কর্মচারী, অ্যাটেনড্যান্স (ZKTeco), বেতন শিট/পে শিট, ছুটি/ঋণ/ক্যাশ আবেদন ও অনুমোদন, হলিডে, অ্যাওয়ার্ড, নিয়োগ |
| সেলস / POS | sale.php | Sale: Sale, SaleReturn, DeliveryChalan; Dashboard/PosDashboardController; Api/PosDashboardApiController | বিক্রয়, বিক্রয় ফেরত, ডেলিভারি চালান, POS ড্যাশবোর্ড |
| প্রজেক্ট | project.php (৯১ রুট-কল) | Project: Project, ProjectExpense, ProjectRequisition, ProjectReturn, ProjectTransfer, ProjectMoney, ProductUse, Invoice | প্রজেক্ট, প্রজেক্ট ব্যয়/টাকা/রিকুইজিশন/রিটার্ন/ট্রান্সফার, প্রজেক্টে পণ্য ব্যবহার, প্রজেক্ট ইনভয়েস |
| প্রোডাকশন | production.php | Production/ProductionController | উৎপাদন (কনভার্সন-ভিত্তিক) |
| সার্ভিস | service.php | Service/ServiceController | সার্ভিস |
| কাস্টমার / সাপ্লায়ার পেমেন্ট | customer.php, supplier.php | Payment: CustomerPaymentContorller, SupplierPaymentContorller (বানান কোডেই এরকম) | কাস্টমার/সাপ্লায়ার পেমেন্ট ও বকেয়া ইনভয়েস |
| অ্যাসেট ম্যানেজমেন্ট | assets.php | AssetsManagement: AssetsCategory, AssetsList, AssetsDestroy, AssetsWarranty, FinancialYear | সম্পদ, ক্যাটাগরি, ধ্বংস, ওয়ারেন্টি, ফাইন্যান্সিয়াল ইয়ার |
| সেটিংস / মাস্টার সেটআপ | web.php (Settings গ্রুপ) | Settings: Branch, Warehouses, Store, Company, Currency, Language, FiscalYear, Smtp, SmsSetting, Navigation, GeneralSettings, HrmSetup, CommissionRules, Authorizedcapital, Paidupcapital | ব্রাঞ্চ, ওয়্যারহাউস, স্টোর, কোম্পানি, কারেন্সি, ভাষা, ফিসক্যাল ইয়ার, SMTP/SMS, নেভিগেশন, মূলধন |
| ইউজার / রোল | web.php (Usermanage গ্রুপ) | Usermanage: UserController, UserRoleController; AdminRoleController (রুটে, খালি) | ইউজার, রোল/পারমিশন (RoleAccess মডেল), প্রোফাইল |
| ড্যাশবোর্ড | web.php + api.php | Dashboard: Home, FinanceDashboard, HrmDashboard, StoreDashboard, PosDashboard, ProcurementDashboard, ProjectDashboard, ManagementDashboard (খালি); Api: Financial/Hrm/Store/Pos/SmsConfiguration | ৭টি ড্যাশবোর্ড পেজ + ৫টি ড্যাশবোর্ড API গ্রুপ |
| রিপোর্ট | reports.php + chart.php | Reports (উপরে), Chart/ChartController | সব ফাইন্যান্স/স্টক রিপোর্ট + চার্ট |
| লিগ্যাসি / রুট-লেভেল কন্ট্রোলার | web.php | app/Http/Controllers/ (রুট) — ৩৬টি ফাইল (অনেকগুলো খালি) | পুরোনো জেনেরিক কন্ট্রোলার (CustomerLedger, SupplierLedger, GeneralLedger, Transection ইত্যাদি); বেশিরভাগ এখন Backend/* দিয়ে প্রতিস্থাপিত বলে মনে হয় |

### ১.৬ রুট ইনভেন্টরি

**মোট:** ১৫টি রুট ফাইল, ৯৬৯টি সক্রিয় রুট-কল (কমেন্ট-বাদ দিয়ে; `Route::resource` = ১ কল হিসেবে গোনা, যদিও Laravel তা ৭টি রুটে সম্প্রসারিত করে — আসল রেজিস্টার্ড রুট এর চেয়ে বেশি)।

| রুট ফাইল | রুট-কল | গ্রুপিং / মিডলওয়্যার | মন্তব্য |
|---|---|---|---|
| routes/web.php | ২৬৭ | মিশ্র — বড় অংশ `auth` গ্রুপে; শুরুর অংশ (লাইন ১–~৩৭০) auth-এর বাইরে | **সবচেয়ে ঝুঁকিপূর্ণ ফাইল:** লগইন-ছাড়া বিপজ্জনক রুট এখানে (§২.৩) |
| routes/inventory.php | ২২৬ | `auth` গ্রুপের ভেতরে | সবচেয়ে বড় রুট ফাইল |
| routes/hrm.php | ১৩৭ | `auth` | |
| routes/project.php | ৯১ | `auth` | |
| routes/api.php | ৬৬ | শুধু `auth` (session) | নিচে আলাদা করে দেখুন |
| routes/reports.php | ৪৮ | `auth` | |
| routes/sale.php | ৪২ | `auth` | |
| routes/supplier.php | ৩০ | `auth` | |
| routes/customer.php | ২৯ | `auth` | |
| routes/assets.php | ২১ | `auth` | |
| routes/service.php | ১৩ | `auth` | |
| routes/chart.php | ৯ | `auth` | |
| routes/channels.php | — | broadcast channels | Laravel ডিফল্ট |
| routes/console.php | — | console কমান্ড রেজিস্ট্রেশন | Laravel ডিফল্ট |
| routes/error_log | (লগ ফাইল, রুট নয়) | — | ডাম্পে-আসা সার্ভার error_log-এর কপি — public-এ থাকা উচিত নয় (§২.১৩) |

**routes/api.php — ৫টি গ্রুপ, মোট ৬৬টি রুট-কল, সবগুলো শুধু `auth`-এ (কোনো role নেই):**

| গ্রুপ | রুট | উদ্দেশ্য |
|---|---|---|
| `financial-dashboard` (১০টি) | kpis, kpi-details, cash-flow, expense-breakdown, revenue-comparison, transactions, invoices, ar-ap-aging, bank-cash-balance, aging-account-details | ফাইন্যান্স ড্যাশবোর্ডের ডেটা API (`FinancialDashboardApiController`) |
| `hrm-dashboard` (১১টি) | attendence, employee-count, salary-summary ইত্যাদি | HRM ড্যাশবোর্ড |
| `store-dashboard` (১১টি) | স্টক/সেলস সারাংশ | স্টোর ড্যাশবোর্ড |
| `pos-dashboard` (১৬টি) | সেলস/সেলস-ম্যান সারাংশ | POS ড্যাশবোর্ড |
| `sms-configuration` (১২টি) | config, send ইত্যাদি | SMS সেটিং ও SMS পাঠানো (`SmsConfigurationApiController`) |

**অস্বাভাবিক রুট-লোডিং (গুরুত্বপূর্ণ, কারণ এটি API-র নিরাপত্তা বদলে দেয়):** `RouteServiceProvider`-এ `routes/api.php` লোড হয় `prefix('api')` + **`middleware('web')`** দিয়ে; স্ট্যান্ডার্ড `api` মিডলওয়্যার গ্রুপ (থ্রটলিং-সহ) কমেন্ট-আউট করা। ফলে API রুটগুলো session-ভিত্তিক, CSRF-এর আওতায়, এবং `api` রেট-লিমিটার (৬০/মিনিট, config-এ সংজ্ঞায়িত) এগুলোতে কার্যকর নয়। `config/auth.php`-এ `api` গার্ড token driver-এ সংজ্ঞায়িত থাকলেও কোথাও ব্যবহৃত হয় না।

**ফাইন্যান্স ড্যাশবোর্ড কন্ট্রোলার — ১৫টি মেথড (যাচাইকৃত):** `app/Http/Controllers/Api/FinancialDashboardApiController.php` (১,৫৭১ লাইন) — লাইভ অংশ লাইন ১–৮১৪ (১০টি public + ৫টি private: `resolveRange`, `accountGroups`, `financialSummary`, `buildAgingReport`, `buildBankCashRows` private), লাইন ৮১৫–১৫৭১ পুরনো ভার্সনের কমেন্ট-আউট কপি (৬৬৭টি `//` লাইন)। মূল মেথড: `kpis`, `kpiDetails`, `arApAging`, `agingAccountDetails`, `bankCashBalance`, `cashFlow`, `expenseBreakdown`, `revenueComparison`, `transactions`, `invoices` (শেষটি খালি, §৩.৪)।

### ১.৭ মডেল (১৩৮টি) — সংক্ষেপ

**ফাইন্যান্স-সংক্রান্ত মূল মডেল (হ্যান্ড-ভেরিফায়েড):**

| মডেল | টেবিল | মূল রিলেশন / নোট |
|---|---|---|
| AccountTransaction | account_transactions | বেলংস-টু account (ChartOfAccount), customer, supplier, employee, branch, project, user, creator; `getVoucherUrlAttribute()` স্ট্রিং type (debit_voucher ইত্যাদি) মেলায় |
| ChartOfAccount | chart_of_accounts | সেলফ-রেফারেন্সিয়াল: parent (belongsTo নিজেই), children (hasMany), accountDetails (hasMany); `accountable` মরফিক রিলেশন (Customer/Supplier/Employee/Bank...) |
| Accounts | chart_of_accounts | **একই টেবিলের ডুপ্লিকেট মডেল** (ChartOfAccount-এর সঙ্গে) — কোন কোড কোনটি ব্যবহার করে, তা না দেখে পরিবর্তন করা বিপজ্জনক |
| DabitVoucher / CreditVoucher / ContraVoucher / JournalVoucher | dabit_vouchers ইত্যাদি | প্রতিটির hasMany details; হেডার `date` কলাম varchar(255) (শুধু all_vouchers.date আসল date টাইপ) |
| DabitVoucherDetail / CreditVoucherDetail / ContraVoucherDetail / JournalVoucherDetail | *_voucher_details | বেলংস-টু হেডার ও account |
| CustomerOpening / SupplierOpening / OpeningBalance | openings / opening_balances / transections | ওপেনিং ব্যালেন্সের একাধিক ধারা |
| Transection | transections | পেমেন্ট-সংক্রান্ত লিগ্যাসি লেজার (১৬টি রো) |

**বাকি মডেল — গ্রুপভিত্তিক (নামমাত্র; পূর্ণ টেবিল/fillable/রিলেশনশিপ স্ক্যান ইনভেন্টরি রিপোর্টের পরিশিষ্ট-খ-তে):** User, Role, RoleAccess, Permission, Branch, Warehouse, Store, Company, Navigation, Employee, Attendance, Salary, PaySheet, LeaveApplication, LoneApplication, CashApplication, Holiday, Award, Product, Category, MainCategory, Brand, Unit, Conversion, Purchase, PurchaseOrder, PurchaseRequisition, PurchaseReturn, Grn, Stock, StockTransfer, StockAdjustment, Sale, SaleReturn, DeliveryChalan, Customer, Supplier, Project, ProjectExpense, ProjectMoney, ProjectInvoice, Invoice, Inovice (**Invoice-এর ডুপ্লিকেট/টাইপো মডেল**), AssetsList, AssetsCategory, CheckRegister, CashBook, GeneralLedger, FiscalYear, FinancialYear, SmsSetting, SmsTemplate, Smtp, ActivityLog, Notification, Language, Currency, Commission, CommissionRules, Bank, PaymentMethod ইত্যাদি।

**মডেল-স্তরের সমস্যা (সংক্ষেপ; বিস্তারিত §২.১২, §৩-এ):** guarded=`[]` মডেল (AssetsList, Award, Destroyitems, FinancialYear, TransferDetails), guarded=`['id']` মডেল (Commission, OpeningBalance) — mass assignment ঝুঁকি; `AccountTransaction` মডেলে `voucher_date` fillable/casts এই কপিতে যাচাই-বহির্ভূত (ব্যবহারকারীর Laragon কপিতে তিনি নিজে যোগ করেছেন — এই কপি মূল কোড)।

### ১.৮ ডেটাবেস স্কিমা — ডোমেন-ভিত্তিক ডকুমেন্টেশন

**মোট:** ১৩৭টি টেবিল, সব InnoDB; ৭৬টিতে ডেটা আছে, ৬১টি খালি; মোট ২,৬৬,৩৩৯টি রো। পূর্ণ ১৩৭টি টেবিলের তালিকা (রো + PK + ইনডেক্স + প্রথম ১২টি কলাম) পরিশিষ্ট-গ-তে।

| ডোমেন | টেবিল | রো | মূল বিষয় |
|---|---|---|---|
| অর্থ / হিসাব | ১৮ | ১,৮৯৬ | chart_of_accounts-ই আসল চার্ট; accounts খালি স্টাব; ২টি ম্যানুয়াল ব্যাকআপ টেবিল (PK/ইনডেক্স ছাড়া) |
| ভাউচার / লেনদেন | ১০ | ১,৫০,৬২৬ (মোট ডেটার অর্ধেকের বেশি) | account_transactions-ই একক লেজার সত্য; ৪টি হেডার + ৪টি ডিটেইল + all_vouchers |
| ইনভেন্টরি / ক্রয় / বিক্রয় | ৩৯ | ৬৬,৫২১ | পণ্য, পারচেজ, সেলস, স্টক — সবচেয়ে বড় ডোমেন |
| কাস্টমার / সাপ্লায়ার / প্রজেক্ট | ২৩ | ১,৫৭১ | |
| HRM | ১৭ | ৩৭,৮৬২ | attendances একাই ৩৭,২১০ রো; বেতন টেবিলগুলো সব খালি |
| ইউজার / রোল / সেটিংস | ১৯ | ১,১৬৮ | |
| লগ / সিস্টেম / বিবিধ | ১১ | ৬,৬৯৫ | **telescope_entries ৫,৮২৭ রো — একটি প্রোডাকশন ডাম্পে** |

**প্রধান টেবিলের বিবরণ:**

- **`account_transactions` (৬৬,৭০৭ রো) — লেজারের কেন্দ্র।** মূল কলাম: `id`, `account_id`, `customer_id`, `supplier_id`, `employee_id`, `branch_id`, `project_id`, `type` (enum-এ ২৯টি ভ্যালু সংজ্ঞায়িত, কিন্তু ডেটায় শুধু ৬টি আসে — নিচে), `table_id`, `debit`, `credit`, `amount`, `date` (?), `created_at`, `updated_at`। **ডাম্পে `voucher_date` কলাম নেই (০ উপস্থিতি)** — যদিও মাইগ্রেশন `2026_10_18_164858_add_voucher_date_to_account_transactions_table.php` এটিকে nullable date হিসেবে সংজ্ঞায়িত করে (ইনডেক্স ছাড়া, §৪.২)। মাইগ্রেশনটি ডাম্পের migrations টেবিলেও অনুপস্থিত (সর্বশেষ রেকর্ড ২০২৬-০৯-২০) — অর্থাৎ ডাম্পটি ১৮ অক্টোবরের আগের স্ন্যাপশট, অথবা মাইগ্রেশন চালানো হয়নি।
- **`chart_of_accounts` (১,৫৪০ রো) — হিসাবের গাছ।** মূল কলাম: `id`, `accountCode`, `account_code` (দ্বৈত কোড কলাম), `account_name`, `parent_id`, `branch_id`, `status`, `balance_type`, `accountable_type`, `accountable_id`, `unique_identifier` (UNIQUE), `deleted_at`। `accountCode`-তে ৪৪টি ডুপ্লিকেট ভ্যালু (UNIQUE নেই), কিন্তু `unique_identifier`-এ ০ ডুপ্লিকেট — তাই কোড-ম্যাচিংয়ে `unique_identifier` বা `id` ব্যবহার করতে হবে, `accountCode` নয়।
- **ভাউচার হেডার (৪টি):** dabit_vouchers ১৬,৩৭৩ / credit_vouchers ১,০৩০ / contra_vouchers ১,২৫০ / journal_vouchers ৫,৭১৯টি রো; ডিটেইল: ৩৬,৮৯৮ / ২,১১৭ / ১,২৪৬ / ১৯,২৭৬টি রো। all_vouchers মাত্র ১০টি রো (লিগ্যাসি/পরীক্ষামূলক ধারা বলে মনে হয়)।
- **`accounts` (০ রো) — স্টাব:** স্কিমায় শুধু `id` + timestamps; কোনো INSERT নেই; আসল চার্ট `chart_of_accounts`-এ।

**`account_transactions.type` — ডেটায় যাচাইকৃত ৬টি ভ্যালু (মোট ৬৬,৭০৭):** debit_voucher ৩৬,৬৭২; journal_voucher ১৯,২৭৬; purchase ৪,৫৮৬; contra_voucher ২,৪৯২; credit_voucher ২,১০৭; sale ১,৫৭৪। Enum-এর বাকি ২৩টি ভ্যালু (সব salary/loan/opening টাইপ-সহ) কখনো আসে না। আরো: `employee_id` ডেটায় কখনো পূরণ হয়নি; `customer_id`-তে মাত্র ২টি distinct ভ্যালু।

**`table_id`-এর অর্থ type-ভেদে বদলায় (সবচেয়ে বিপজ্জনক স্কিমা-ফাঁদ):** debit-এর ৩৬,৬৭২/৩৬,৬৭২ রো হেডার-id-র সঙ্গে মেলে; credit-এর ২,১০৭/২,১০৭ হেডার-id-র সঙ্গে মেলে; **কিন্তু contra-র ২,৪৯২/২,৪৯২ ডিটেইল-id-র সঙ্গে মেলে (হেডার মাত্র ১০টি) এবং journal-এর ১৯,২৭৬/১৯,২৭৬ ডিটেইল-id-র সঙ্গে মেলে (হেডার ০টি)**। এক অর্থ ধরে নিয়ে জয়েন করলে অর্ধেক টাইপের জন্য ভুল হবে — `tests/voucherdate.sql`-এর ব্যাকফিল-স্ক্রিপ্ট ঠিক এই ভুলই করে (contra ১০/২,৪৯২, journal ০/১৯,২৭৬ ম্যাচ), তাই তা চালানো যাবে না (§৩.১২)।

**ইনডেক্স ও FK-ঘাটতি (সারাংশ; বিস্তারিত §৪.২, §৪.৩):** FK মাত্র ৩৭টি, ২৭টি টেবিলে — সব warehouse/project/branch/user/sale রেফারেন্সে; `account_id`, customer/supplier/employee, ভাউচার-লিংক, `chart_of_accounts.parent_id`-এর জন্য কোনো FK নেই। ডেটা আজ পরিষ্কার (১,২৬৬টি account_id, ২৬২টি supplier_id, ৫৩টি project_id সব resolve হয়; ০ orphan) — কিন্তু কিছু তা enforce করে না। `account_transactions`-এ `type`, `created_at`, `table_id`-তে কোনো ইনডেক্স নেই; `contra_*` টেবিলে PK-ছাড়া কোনো ইনডেক্সই নেই; `voucher_date`-তে কোনো ইনডেক্স নেই (মাইগ্রেশনেও নেই)।

**নামকরণের সমস্যা (বিভ্রান্তির উৎস):** `dabit_vouchers` (debit নয়), `sales__details` (ডবল আন্ডারস্কোর), `stock_ajdustment_detailsts`, `inovices`, `lones`, `candidate_inforamtions`, `descaption` কী, `CustomerPaymentContorller`/`SupplierPaymentContorller` (ডবল l), `Smpt` (SMTP নয়), `ExpenseCategoryRepositorie` (s ছাড়া)।

**মাইগ্রেশন বনাম ডাম্প ড্রিফট:** ২১৯টি মাইগ্রেশন ফাইল বনাম ডাম্পের migrations টেবিলে ২১৮টি রো — পার্থক্য শুধু voucher_date মাইগ্রেশনটি। ১৩২টি create-মাইগ্রেশন সব ডাম্পে আছে; ৫টি ডাম্প-টেবিলের কোনো create মাইগ্রেশন নেই (২টি COA ব্যাকআপ, migrations নিজে, ২টি Telescope ইন্টারনাল টেবিল)। আরো বড় ড্রিফট সিডার-স্তরে: ৫টি সিডার এমন টেবিল টার্গেট করে যেগুলো ডাম্পেও নেই, কোনো মাইগ্রেশনেও নেই (districts, divisions, upazilas, unions, stores — মডেল আছে, টেবিল নেই) — চালালে error দেবে (§৩.১১)।

### ১.৯ মূল ওয়ার্কফ্লো

**ভাউচার পোস্টিং — দুটি আলাদা প্যাটার্ন (সবচেয়ে গুরুত্বপূর্ণ ওয়ার্কফ্লো):**

| ধাপ | Dabit / Credit (দুই ধাপ) | Contra / Journal (এক ধাপ) |
|---|---|---|
| store() | শুধু হেডার + Details তৈরি হয়; `voucher_date` এই কপিতে অনেক সোর্সে সেট হয় না (§৩.৫) | হেডার + Details + AccountTransaction একসাথে তৈরি হয় |
| approve() | AccountTransaction তৈরি হয় (এই কপিতে type সংখ্যা ৫/৬ হিসেবে উল্লেখ আছে — তবে ডাম্পে AT.type-এ শুধু স্ট্রিং ভ্যালু পাওয়া গেছে; কোন ফিল্ডে সংখ্যা যায়, তা স্ট্যাটিকভাবে পুরোপুরি মেলানো যায়নি — live trace pending) | approve ধাপ নেই |
| নম্বর | `latest('id')+1` দিয়ে বানানো হয় — রেস আছে (§৩.২) | একই |
| Dr=Cr চেক | নেই (§৩.২) | নেই |

**টাইপ-ব্যবস্থার ত্রিভুজ (ডকুমেন্ট-বিহীন):** AT.type-এ স্ট্রিং (debit_voucher ইত্যাদি); রিপোজিটরিতে সংখ্যা-টাইপের উল্লেখ (৫/৬/৭/৮); `config/type.php`-তে ১–১২ ম্যাপ — তিনটি ভোকাবুলারির ম্যাপিং কোথাও লেখা নেই। নতুন কোড লেখার আগে এই ম্যাপিং হাতে যাচাই করা বাধ্যতামূলক।

**ফাইন্যান্স ড্যাশবোর্ড ডেটা ফ্লো:** Blade (২,৫৩৪ লাইন) প্রথম পেইন্টে ~৭টি API কল করে (kpis, ar-ap-aging, bank-cash-balance, cash-flow, expense-breakdown, revenue-comparison, transactions) → কন্ট্রোলার সরাসরি Model কোয়েরি করে → `accountGroups()` দিয়ে চার্ট-ট্রি রেজোলিউশন (প্রতি এন্ডপয়েন্টে নতুন করে) → JSON। Aging-এর হিসাব `buildAgingReport()`-এ হয় (range='all' ডিফল্টে সব AR/AP লেনদেন PHP-তে বাকেট করা হয়)। ড্রিল-ডাউন মোডাল `agingAccountDetails` থেকে লেনদেন-স্তরের ডেটা আনে।

**অথেনটিকেশন ফ্লো:** `/` → লগইন পেজ (`Water Technology BD Ltd`) → `/auth/login` (prefix `auth` + `Auth::routes()`) → সফল লগইনে `/admin/home`। লগইন থ্রটলিং ফ্রেমওয়ার্ক-ডিফল্ট; পাসওয়ার্ড bcrypt; রিসেট expire/throttle ৬০ মিনিট। কিন্তু রেজিস্ট্রেশন ডিফল্টে চালু (§২.৩) এবং কোনো role চেক নেই (§২.২) — ফলে অথেনটিকেশন থাকলেও অথরাইজেশন কার্যত নেই।

**ZKTeco অ্যাটেনড্যান্স ফ্লো:** প্রতি মিনিটে `attendance:sync` কমান্ড বাইরের API থেকে পাঞ্চ আনে (page_size=1000, ৩ দিনের উইন্ডো) → প্রতি পাঞ্চে `Employee::where(id_card)->first()` → প্রতি employee-দিনে read+write → `ZktecoSetUser` Job ডিভাইসে ইউজার তৈরি করে (কিউ `sync`, তাই ওয়েব-রিকোয়েস্টের ভেতরেই চলে)।

### ১.১০ ডকুমেন্টেশন গ্যাপ ও বৈপরীত্য (PROJECT_DOCUMENTATION.md বনাম কোড)

- দাবি করে Laravel 8.40 — lock-এ 8.83.29।
- দাবি করে "সব রুট /admin-এর অধীন" — ভুল; বহু রুট /admin-এর বাইরে (যেমন /cleare-data, /attendance/*, /zkteco-sync/*)।
- উল্লেখ নেই: ড্যাশবোর্ড API, SMS, লেজার-মার্জ, ZKTeco-sync, হার্ডকোড করা অ্যাকাউন্ট ID (১৮/১৯/২২/২৩/২৪, AR=৫, AP=১৬, cash=৭, bank=৮), টাইপ-কোড, fideloper/proxy ও tinker।
- §৯-এর ৪টি Recent Change-এর মধ্যে শুধু Attendance Auto Checkout কোড-যাচাইকৃত; বাকি ৩টি যাচাই-বহির্ভূত।
- README-র "Setup Instructions" হেডিং-এর নিচে কোনো ধাপ নেই।
- ডেড কোডের পাহাড়: ফাইন্যান্স কন্ট্রোলারে ৬৬৭টি কমেন্ট-লাইন (পুরনো ভার্সনের কপি), ReportController-এ ২,৭৭৮টি কমেন্ট-লাইন, ৬,৪৯৬ লাইনের মধ্যে — রক্ষণাবেক্ষণে ভুল ভার্সন পড়ার ঝুঁকি।

---

## ২. নিরাপত্তা (Security)

**সামগ্রিক রায়:** এই সিস্টেমের নিরাপত্তা-অবস্থা **গুরুতর**। সবচেয়ে বড় সমস্যা কাঠামোগত: অথরাইজেশন শুধু UI-তে বোতাম লুকিয়ে রাখা হয় (`Helper::roleAccess()`), সার্ভার-পাশে enforce করা হয় না। ফলে যেকোনো লগইন-করা ব্যবহারকারী — এমনকি নিজে রেজিস্টার করা ব্যবহারকারী — সরাসরি URL/API ডেকে সবকিছু করতে পারে। নিচের Critical আইটেমগুলো প্রথমে ঠিক করতে হবে, অন্যথায় বাকি ফিক্সের অর্থ কম।

> **নোট:** কোনো সিক্রেটের মান (পাসওয়ার্ড, API কী, ইমেইল) এই রিপোর্টে লেখা হয়নি — শুধু উপস্থিতি + ফাইল + লাইন দেওয়া হয়েছে।

### ২.১ Critical

**S-01 — লগইন ছাড়া যেকোনো টেবিল truncate করার টুল**
- প্রমাণ: `routes/web.php:127` — `Route::match(['get', 'post'], '/cleare-data', function () {...})`; রুটটি কোনো `auth` গ্রুপের ভেতরে নেই।
- প্রভাব: GET-এ সব টেবিলের নাম + row count দেখা যায়; POST-এ মাত্র ৫টি টেবিল বাদে যেকোনো টেবিল truncate করা যায়। একজন অপরিচিত ব্যক্তি পুরো ডেটাবেস মুছে দিতে পারে।
- ফিক্স: রুটটি মুছে ফেলুন; যদি কখনো লাগে, শুধু `auth` + admin-role-এর পেছনে, POST-এ, CSRF-সহ, কনফার্মেশন-সহ, এবং টেবিল-allowlist-সহ রাখুন। প্রোডাকশনে কখনোই নয়।

**S-02 — লগইন ছাড়া অ্যাডমিন-ক্লোন, DB ব্যাকআপ ও ফাইল ব্যাকআপ ডাউনলোড, truncate**
- প্রমাণ: `routes/web.php:399` — `Route::get('/attendance/reset/{id?}', 'ZktecoController@reset')`; `app/Http/Controllers/ZktecoController.php:93` (reset), `:100` (`User::find(1)`-এর ক্লোন), `:110` (`role_id=1`), `:119` (`action=backup_db` → পুরো DB ডাউনলোড), `:131` (`action=backup_files` → পুরো প্রজেক্ট zip ডাউনলোড), `:155` (`DB::table($table)->truncate()`)।
- প্রভাব: লগইন ছাড়াই অ্যাডমিন-লেভেল অ্যাকাউন্ট তৈরি, পুরো ডেটাবেস ও পুরো সোর্স-কোড ডাউনলোড, যেকোনো টেবিল মুছে ফেলা। এটি একাই সম্পূর্ণ সিস্টেম-কম্প্রোমাইজের সমতুল্য।
- ফিক্স: `reset()`-এর বিপজ্জনক শাখাগুলো (ক্লোন, backup_db, backup_files, truncate) মুছে ফেলুন; রুটটি `auth` + admin-role-এর পেছনে নিন; ইতিমধ্যে তৈরি হওয়া ক্লোন অ্যাকাউন্ট থাকলে তা নিষ্ক্রিয় করুন এবং পাসওয়ার্ড রোটেট করুন।

**S-03 — পাবলিক রেজিস্ট্রেশন চালু**
- প্রমাণ: `routes/web.php:378` — `Auth::routes()` ডিফল্টে (register অফ করা হয়নি); `resources/views/auth/register.blade.php` বিদ্যমান; নতুন ব্যবহারকারীর কোনো role নির্ধারণ হয় না।
- প্রভাব: যে কেউ নিজে অ্যাকাউন্ট বানিয়ে লগইন করতে পারে — এবং S-05-এর কারণে সেই অ্যাকাউন্ট দিয়েই ফাইন্যান্সসহ সব ডেটা দেখা যায়।
- ফিক্স: `Auth::routes(['register' => false])`; রেজিস্ট্রেশন শুধু অ্যাডমিন-তৈরি ব্যবহারকারী দিয়ে।

**S-04 — আরো লগইন-ছাড়া রুট**
- প্রমাণ (সব `routes/web.php`, `auth` গ্রুপের বাইরে): `:60-64` zkteco-sync (index/check/confirm/apply-correction), `:66` `/create-employee` (ভেতরে `dd()` ডিবাগ কোডসহ), `:122-123` `/product-stock-matching` + `POST /product-stock-matching/bulk-update` (স্টক স্ট্যাটাস বদলানো যায়), `:370` `attends` resource (কন্ট্রোলারেও middleware নেই), `:381` `/comparison`, `:395-397` `/attendance` ও `/attendance/store`, `:755` `/admin/debug/balance-sheet` (নামে admin হলেও auth গ্রুপের বাইরে)।
- প্রভাব: অ্যাটেনড্যান্স-সংশোধন, স্টক-বাল্ক-আপডেট, ডিবাগ-রিপোর্ট — লগইন ছাড়াই।
- ফিক্স: প্রতিটি রুট হয় `auth` (+ প্রয়োজনে role) গ্রুপে নিন, নয়তো মুছে ফেলুন; `/create-employee`-এর `dd()` সরান।

**S-05 — Role/permission এনফোর্সমেন্ট নেই; শুধু UI-তে লুকানো**
- প্রমাণ: ১৫টি রুট ফাইলের কোনোটিতে `role`/`can` মিডলওয়্যার নেই; `app/Policies/` ডিরেক্টরি নেই; `AuthServiceProvider`-এ policy কমেন্ট-আউট; `Helper::roleAccess()` শুধু DataTables-এর বোতাম লুকায়, সার্ভার-পাশে কিছু ব্লক করে না; ৮টি মিডলওয়্যার সব Laravel ডিফল্ট, কোনো কাস্টম role মিডলওয়্যার নেই।
- প্রভাব: যেকোনো লগইন-করা ব্যবহারকারী সরাসরি URL ডেকে যেকোনো মডিউলের পেজ/অ্যাকশন চালাতে পারে।
- ফিক্স: একটি role মিডলওয়্যার বানিয়ে সব মডিউল-গ্রুপে লাগান; কন্ট্রোলার-স্তরে `authorize()`/role চেক যোগ করুন; UI-লুকানোকে নিরাপত্তা হিসেবে গণ্য করা বন্ধ করুন।

**S-05(a) — ফাইন্যান্স ড্যাশবোর্ড API: আগের রিপোর্ট এখনো সত্য**
- প্রমাণ: `routes/api.php:46-57`-এর ১০টি রুট শুধু `auth`-এ; `FinancialDashboardApiController`-এ কোনো `roleAccess`/`authorize` চেক নেই, branch-scope-ও নেই। একই অবস্থা pos-dashboard (১৬টি), store-dashboard (১১টি), sms-configuration (১২টি) রুটে।
- প্রভাব: যেকোনো লগইন-করা ব্যবহারকারী সব ব্রাঞ্চের সব ফাইন্যান্স ডেটা (KPI, aging, ব্যাংক/ক্যাশ, লেনদেন) দেখতে পারে।
- ফিক্স: এই API গ্রুপগুলোতে finance-role চেক + branch-scope যোগ করুন।

**S-14 — হার্ডকোড করা ডিফল্ট পাসওয়ার্ড (ক্লোন-অ্যাকাউন্টে)**
- প্রমাণ: `ZktecoController.php:106`-এর কাছাকাছি (মান এই রিপোর্টে লেখা হয়নি) — ক্লোন-অ্যাকাউন্টে হার্ডকোড করা ডিফল্ট পাসওয়ার্ড সেট করা হয়।
- প্রভাব: ক্লোন-অ্যাকাউন্টের পাসওয়ার্ড যে কেউ অনুমান করতে পারে।
- ফিক্স: ক্লোন-ফিচার মুছে ফেলুন (S-02-এর ফিক্সের অংশ); কোথাও হার্ডকোড করা পাসওয়ার্ড রাখবেন না।

### ২.২ Medium

**S-06 — SMS কনফিগ ও SMS পাঠানো**
- প্রমাণ: `SmsConfigurationApiController::getConfig()` মডেলের `$hidden` বাইপাস করে `api_key` ফেরত দেয়; যেকোনো authed ব্যবহারকারী `send()` দিয়ে সব কর্মচারীকে SMS পাঠাতে পারে; `SmsService.php:271,463`-এ `CURLOPT_SSL_VERIFYPEER=false`।
- প্রভাব: SMS প্রোভাইডারের API কী ফাঁস (বিল/অপব্যবহার), অননুমোদিত বাল্ক SMS, TLS-যাচাই বন্ধ থাকায় ম্যান-ইন-দ্য-মিডল ঝুঁকি।
- ফিক্স: `getConfig()` থেকে `api_key` সরান; `send()`-এ admin-role চেক; cURL-এ TLS যাচাই চালু করুন।

**S-07 — DataTables `dir` প্যারামিটার সরাসরি `orderBy()`-তে**
- প্রমাণ: যেমন `ProductRepositories.php:59-66`, `CheckRegisterController.php:163` — রিকোয়েস্টের `order.0.dir` সরাসরি `orderBy()`-তে যায়; ভেন্ডর গ্রামারে direction escape হয় না।
- প্রভাব: প্যাটার্ন কোডে নিশ্চিত; এটি আসলে ইনজেকশনে পরিণত হয় কিনা, তা live যাচাই সাপেক্ষ (এই রিপোর্টে এক্সপ্লয়েট চালানো হয়নি)। বাকি ২০৭টি raw SQL মিলে ব্যবহারকারীর ইনপুট bound প্যারামিটার হিসেবে যায় — নিশ্চিত ইনজেকশন পাওয়া যায়নি।
- ফিক্স: `dir`-কে `['asc','desc']` allowlist-এ বাঁধুন; সব DataTables সর্ট-কলাম allowlist করুন।

**S-08 — কোম্পানি লোগো/ফেভিকন/সিগনেচার আপলোড: mime/size যাচাই ছাড়া, মূল ফাইলনামে সরাসরি public-তে**
- প্রমাণ: `CompanyRepositories.php:145-152,178-195` — ফাইল মূল নামে `public/backend/`-তে যায়, mime/size যাচাই ছাড়া। কর্মচারীর ছবিতেও mime নেই (Leave/Lone-এ mime আছে — ঠিক আছে)।
- প্রভাব: `.php` আপলোড করতে পারলে রিমোট কোড এক্সিকিউশন সম্ভব — তবে লাইভ এক্সিকিউশন যাচাই করা হয়নি (live verification pending)।
- ফিক্স: mime + size যাচাই, র্যান্ডম ফাইলনাম, সম্ভব হলে non-public ডিস্কে সংরক্ষণ; `public/backend/`-এ `.htaccess` দিয়ে PHP এক্সিকিউশন বন্ধ করুন।

**S-09 — ১৩৯টি state-changing GET রুট (delete/status)**
- প্রমাণ: app/ + routes/ জুড়ে grep — ১৩৯টি GET রুট delete/status বদলায়। ভালো দিক: `VerifyCsrfToken::$except` খালি (কোনো ব্যতিক্রম নেই)।
- প্রভাব: CSRF-এর মাধ্যমে (ক্ষতিকর লিংকে ক্লিক করিয়ে) রেকর্ড মুছে ফেলা বা স্ট্যাটাস বদলানো যায়।
- ফিক্স: delete/status-কে POST/DELETE-তে নিন (CSRF টোকেন-সহ); GET রুট শুধু পড়ার জন্য রাখুন।

**S-10 — Session কনফিগ**
- প্রমাণ: `config/session.php:34` — `lifetime` ১০০,০০০,০০০ মিনিট (~১৯০ বছর); `:49` — `encrypt=false`; `:171` — `secure` env-নির্ভর; ভালো দিক: `http_only=true`, `same_site=lax`।
- প্রভাব: একবার চুরি হওয়া সেশন কার্যত চিরস্থায়ী; সেশন কুকি এনক্রিপ্ট হয় না।
- ফিক্স: lifetime বাস্তবসম্মত করুন (যেমন ১২০ মিনিট + remember-me আলাদা), `encrypt=true`, প্রোডাকশনে `secure=true`।

**S-11 — Stored XSS (suspected — লাইভ যাচাই সাপেক্ষ)**
- প্রমাণ: ২২টি `{!! !!}` আনএসকেপড আউটপুটের মধ্যে ঝুঁকিপূর্ণ: `sidebar.blade.php:216,234` (DB নেভিগেশন লেবেল), `daybook.blade.php:115` (account_name), purchase/sale Blade (productNames via `getRawOriginal`), `SuperHelper.php:753` `accountledger()`। sales JS-এ `.html(data)`/`.append()` দিয়ে আনএসকেপড DOM insertion (এক্সটেনশন)।
- প্রভাব: DB-তে থাকা মান (যেমন অ্যাকাউন্ট/পণ্যের নাম) দিয়ে স্ক্রিপ্ট ইনজেকশন — যদি আক্রমণকারী ওই মান ঢোকাতে পারে।
- ফিক্স: `{!! !!}`-কে `{{ }}`-এ নিন যেখানে HTML দরকার নেই; যেখানে HTML দরকার, সেখানে allowlist-ভিত্তিক স্যানিটাইজার ব্যবহার করুন; JS-এ `.text()` ব্যবহার করুন।

**S-12 — Mass assignment**
- প্রমাণ: `guarded=[]` মডেল — AssetsList, Award, Destroyitems, FinancialYear, TransferDetails; `guarded=['id']` — Commission, OpeningBalance; `CommissionRulesRepositories.php:142,150`-এ `$request->all()` (তবে মডেলের `$fillable` সীমিত — তাই ঝুঁকি আংশিক)।
- প্রভাব: `guarded=[]` মডেলে রিকোয়েস্টের যেকোনো ফিল্ড সরাসরি সেভ হতে পারে (যেমন status/role-জাতীয় ফিল্ড, যদি ফর্মে থাকে)।
- ফিক্স: সব মডেলে স্পষ্ট `$fillable` দিন; `$request->all()`-এর বদলে `$request->only([...])` ব্যবহার করুন।

**S-13 — Telescope ও public/error_log (live যাচাই সাপেক্ষ)**
- প্রমাণ: `config/telescope.php:65` — ডিফল্ট `enabled=true`, সব watcher চালু; gate শুধু ২টি নির্দিষ্ট ইমেইলের জন্য (ইমেইল এই রিপোর্টে লেখা হয়নি); `public/vendor/telescope/` অ্যাসেট আছে; `public/error_log` (২১৬ KB, পুরোনো সার্ভার পাথ-সহ) `.htaccess` দিয়ে ব্লক হয় না; ডাম্পে `telescope_entries` ৫,৮২৭ রো — অর্থাৎ আসল রেকর্ডিং হয়েছে।
- প্রভাব: প্রোডাকশনে চালু থাকলে Telescope-এ কোয়েরি/রিকোয়েস্ট/মডেল ডেটা দেখা যেতে পারে; error_log থেকে সার্ভার-পাথ ফাঁস হতে পারে।
- ফিক্স: প্রোডাকশনে Telescope বন্ধ করুন (`TELESCOPE_ENABLED=false`); `public/error_log` মুছুন; error_log-কে public-এর বাইরে রাখুন।

**S-15 — IDOR (অন্যের রেকর্ড ID বদলে দেখা)**
- প্রমাণ: POS/Store/Finance API ও admin show/edit-এ ID বদলে অন্যের রেকর্ড দেখা যায়; ব্যতিক্রম: HRM leave/announcement-এ ownership/department চেক আছে।
- প্রভাব: যেকোনো লগইন-করা ব্যবহারকারী অন্য ব্রাঞ্চ/ব্যবহারকারীর রেকর্ড দেখতে পারে।
- ফিক্স: show/edit-এ ownership/branch চেক যোগ করুন (S-05-এর অংশ হিসেবে)।

**E-01 — বাকি ১৩টি রুট ফাইল: auth আছে, role নেই**
- প্রমাণ: web/api ছাড়া বাকি ১৩টি রুট ফাইলের সব রুট `auth`-এ আছে, কিন্তু role কোথাও নেই (এক্সটেনশন-অডিট)।
- ফিক্স: S-05-এর ফিক্সের অংশ হিসেবে মডিউল-ভিত্তিক role মিডলওয়্যার লাগান।

**E-02 — রিপোজিটরি/সার্ভিস-স্তরের সমস্যা (এক্সটেনশন-অডিট)**
- প্রমাণ: রিপোজিটরিতে ৬৪টি `$this->user_id = 1` হার্ডকোড (অডিট-ট্রেইলের ভরসা নষ্ট করে); `navEmpty` কনফার্মেশন ছাড়া Role/Permission টেবিল truncate করে; activity-log ৩০ দিন পর মুছে ফেলা হয় (ফরেনসিক ট্রেইল ছোট হয়); ZKTeco Job-এ ডিভাইস-ইউজার খালি পাসওয়ার্ডে তৈরি হয়; MySQL `strict=false`; DB পাসওয়ার্ড fallback খালি; CORS `*`; অফিস স্থানাঙ্ক `config/officeLocation.php`-তে হার্ডকোড; `storage/app/public/`-তে NID/সিগনেচার/ছুটির ফাইল আছে (এই কপিতে `public/storage` সিমলিংক নেই, কিন্তু সিমলিংক চালু হলেই সেগুলো পাবলিক হয়ে যাবে)।
- ফিক্স: হার্ডকোড করা user_id-র বদলে `auth()->id()`; truncate-এ কনফার্মেশন + allowlist; activity-log রিটেনশন বাড়ান; ডিভাইস-ইউজারে পাসওয়ার্ড বাধ্যতামূলক করুন; `strict=true`; CORS নির্দিষ্ট ডোমেইনে সীমিত করুন; স্থানাঙ্ক env-এ নিন; sensitive ফাইল non-public ডিস্কে রাখুন।

### ২.৩ Normal

- **S-17 — পাসওয়ার্ড নীতি অসামঞ্জস্য:** অ্যাডমিন-তৈরি ব্যবহারকারীর জন্য `min:6`, রেজিস্ট্রেশনে `min:8`।
- **S-16 — `dd()`-সহ লাইভ catch ব্লক:** যেমন `ActivityLog.php:98`, `PaySheetController.php:508` — এরর হলে ডিবাগ আউটপুট ব্যবহারকারীকে দেখানো হতে পারে।
- **AuthenticateSession মিডলওয়্যার কমেন্ট-আউট** — পাসওয়ার্ড বদলালে অন্য ডিভাইসের সেশন বাতিল হয় না।
- **ইউজার-তৈরিতে privilege escalation প্যাটার্ন (Medium-ঘেঁষা):** ইউজার-তৈরির ফ্লোতে role নির্ধারণের নিয়ন্ত্রণ দুর্বল — S-05-এর ফিক্সের সঙ্গে দেখতে হবে।

### ২.৪ যা ঠিক আছে (নেতিবাচক ফলাফল — খুঁজেও পাওয়া যায়নি)

পাসওয়ার্ড bcrypt; রিসেট expire/throttle ৬০ মিনিট; লগইন থ্রটলিং ফ্রেমওয়ার্ক-ডিফল্ট; `VerifyCsrfToken::$except` খালি; phpinfo, Debugbar, public/-এ zip/sql/bak/.env কপি, `eval`/`shell_exec`/`unserialize`, সরাসরি `$_GET`/`$_POST` — পাওয়া যায়নি; `env('X','default-secret')` ধরনের ফলব্যাক পাওয়া যায়নি; ActivityLog-এ password/api_key/token বাদ দেওয়া হয়।

---

## ৩. বাগ রিপোর্ট

**সামগ্রিক রায়:** বাগগুলোর মধ্যে সবচেয়ে বিপজ্জনকগুলো হিসাবের অখণ্ডতা-সংক্রান্ত — এগুলো ক্র্যাশ করে না, নীরবে ভুল পোস্টিং তৈরি করে। দ্বিতীয় বড় শ্রেণি ভাঙা রেফারেন্স (৬৫টি রুটে মেথড নেই, case-সংবেদনশীল ফাইল-নাম) — Linux-এ চলে না কিন্তু Windows-এ চলে বলে Laragon-এ ধরা পড়ে না।

### ৩.১ আগের ফাইন্যান্স-কন্ট্রোলার ফাইন্ডিংস যাচাই (এই কপিতে)

| আগের দাবি | এই কপিতে যাচাই | মন্তব্য |
|---|---|---|
| (a) `created_at` দিয়ে ফিল্টার | **আংশিকভাবে NOT PRESENT, তবে দ্বন্দ্ব আছে** | ফাইন্যান্স কন্ট্রোলার লাইভ কোড `voucher_date`-ভিত্তিক (৫৪টি রেফারেন্স; `created_at` শুধু লাইন ৩১৯/৪৮৫-এ ফলব্যাক + কমেন্ট-কপিতে)। কিন্তু `ReportController`-এ `voucher_date` ০ বার বনাম `created_at` ১৫৯ বার — **TB/BS বনাম ড্যাশবোর্ডের সিস্টেমেটিক দ্বন্দ্ব CONFIRMED** |
| (b) হার্ডকোড ম্যাজিক অ্যাকাউন্ট ID | **CONFIRMED** (Medium) | `accountGroups`-এ ১৮/১৯/২২/২৪/২৩/২০; AR=৫, AP=১৬, cash=৭, bank=৮ |
| (c) ফাইন্যান্স API-তে রোল-চেক নেই | **কোড-ফ্যাক্ট CONFIRMED** | §২.২-এর S-05(a) দেখুন |
| (d) `invoices()` খালি অ্যারে | **CONFIRMED** (Normal) | লাইন ৮০৯–৮১১; finance.blade.php-এর Invoices ট্যাব স্থায়ীভাবে "No invoices found" দেখায় |
| (e) `overdue_count` হার্ডকোড ০ | **CONFIRMED** (Normal) | লাইন ১৪৬, TODO-সহ: `// TODO-CONFIRM: invoice/overdue table na thakle 0` |
| (f) `buildBankCashRows`/`expenseBreakdown`-এ undefined-`$ids` | **NOT PRESENT** | লাইভ কোডে ও সব Api-ব্যবহারে `$ids` সংজ্ঞায়িত — এই বাগ এই কপিতে নেই |

### ৩.২ Critical বাগ (৯টি)

**B-C1 — কন্ট্রা আপডেটে ডাবল-পোস্টিং**
- প্রমাণ: `ContraVoucherRepositories.php:195-244` — আপডেটে পুরনো ডিটেইলস/ট্রানজ্যাকশন না মুছে নতুন যোগ করা হয়।
- প্রভাব: কন্ট্রা ভাউচার এডিট করলে পুরনো পোস্টিং + নতুন পোস্টিং — দুটোই লেজারে থাকে; ব্যালেন্স দ্বিগুণ হয়।
- ফিক্স: আপডেটের শুরুতে পুরনো ডিটেইলস ও তাদের AccountTransaction মুছে তারপর নতুন তৈরি করুন (ট্রানজ্যাকশনের ভেতরে); অথবা আপডেট-কে "পুরনো বাতিল + নতুন" হিসেবে লিখুন।

**B-C2 — সাপ্লায়ার-পেমেন্ট আপডেটে ভুল-id-তে ডিলিট**
- প্রমাণ: `SupplierPaymentRepositories.php:211-264` — পুরনো লেজার-রো মুছে নতুন id বানিয়ে সেই নতুন id-তে ট্রানজ্যাকশন-ডিলিট চালানো হয়; ফলে পুরনো পোস্টিং + পুরনো `transection` থেকে যায়।
- প্রভাব: সাপ্লায়ার-পেমেন্ট এডিট করলে পুরনো পোস্টিং লেজারে রয়ে যায় + নতুন পোস্টিং যোগ হয় — সাপ্লায়ার ব্যালেন্স ভুল।
- ফিক্স: ডিলিট সবসময় পুরনো id/কী দিয়ে করুন, নতুন id দিয়ে নয়; আপডেটের আগে/পরে row-count যাচাই যোগ করুন।

**B-C3 — কাস্টমার-পেমেন্ট আপডেটে ভুল-কীতে ডিলিট**
- প্রমাণ: `CustomerPaymentRepositories.php:203` — `Transection::where('id', $id)` যেখানে `$id` লেজার-id, লিংক-কী `payment_id` নয়।
- প্রভাব: ভুল রো মোছা + ডাবল-কাউন্ট — দুটোই সম্ভব।
- ফিক্স: `where('payment_id', $paymentId)` (সঠিক লিংক-কী) দিয়ে ডিলিট করুন।

**B-C4 — ডাবল-অ্যাপ্রুভে ডাবল-পোস্টিং**
- প্রমাণ: Debit/Credit `approve()`-এ approve-ফ্ল্যাগ চেক নেই — একই ভাউচার দুবার approve করা যায়।
- প্রভাব: একই ভাউচার দুবার পোস্টিং — লেজার দ্বিগুণ।
- ফিক্স: approve-এর শুরুতে `if ($voucher->approved) return/abort;` + approve-কে DB ট্রানজ্যাকশনের ভেতরে রাখুন; approve-ফ্ল্যাগে UNIQUE/স্ট্যাটাস-চেক যোগ করুন।

**B-C5 — কোনো ভাউচারে ডেবিট=ক্রেডিট চেক নেই**
- প্রমাণ: চারটি ভাউচার সার্ভিসের ভ্যালিডেশনে শুধু invoice_no/date/note — ডেবিট-টোটাল = ক্রেডিট-টোটাল মেলানো হয় না।
- প্রভাব: অসম ভাউচার পোস্ট হয়ে TB-কে অমিল করতে পারে (২০২৬-১০-১১-এর ৬২৫-টাকার orphan-এর মতো ঘটনার পুনরাবৃত্তির পথ খোলা থাকে)।
- ফিক্স: store/update/approve-এ `sum(debit) == sum(credit)` চেক বাধ্যতামূলক করুন; অমিল হলে ভ্যালিডেশন-এরর দিন, পোস্ট করবেন না।

**B-C6 — ভাউচার-নম্বর রেস**
- প্রমাণ: ভাউচার নম্বর `latest('id')+1` দিয়ে বানানো হয়।
- প্রভাব: একসাথে দুজন ভাউচার বানালে একই নম্বর — ডুপ্লিকেট নম্বর (ডাম্পে DV00060, DV00063 ×২ করে, ৫টি contra নম্বর ×২ পাওয়া গেছে — §১.৮)।
- ফিক্স: নম্বর বানানোকে DB ট্রানজ্যাকশন + row-lock-এর ভেতরে নিন, অথবা সিকোয়েন্স/UNIQUE কনস্ট্রেইন্ট দিয়ে ডুপ্লিকেট আটকান।

**B-C7 — `ConversionTransformer` রেজোলিউশন ব্যর্থ (route:list ভাঙনের কারণ)**
- প্রমাণ: `app/Transformers/`-এ `ConversionTransformer.php` (বড় C) **নেই**; আছে শুধু `conversionTransformer.php` (ছোট c, ক্লাস-নামও ছোট হাতের); `ConversionController.php:10,31` বড় হাতের নাম import/type-hint করে; Composer autoloader প্রোবে capital-C নামে `class_exists` = false, lowercase-এ = true। বাকি ৫৮টি Transformer-এর ক্লাস-নাম ফাইল-নামের সঙ্গে হুবহু মেলে — এটাই একমাত্র ব্যতিক্রম।
- প্রভাব: Linux-এ (case-sensitive ফাইলসিস্টেম) এই কন্ট্রোলার লোড হয় না; Windows/Laragon-এ (case-insensitive) চলে যায় — তাই Laragon-এ ধরা পড়ে না। সেটআপে `php artisan route:list` এই ক্লাসের অভাবে ব্যর্থ হয়েছিল বলে রেকর্ড আছে; তবে Laravel 8-এ `route:list` কন্ট্রোলার ইনস্ট্যানশিয়েট করে না — তাই `route:list`-ই এই কারণে ভাঙে কিনা, তার সঠিক ট্রিগার-পথ **live verification pending** (ক্লাস-ভাঙা নিজে প্রমাণিত)।
- ফিক্স: ফাইলটির নাম `ConversionTransformer.php` করুন (ক্লাস-নামসহ); একই অডিটে `candidateSelectionRepositories` (ছোট c) টাইপ-হিন্ট ভাঙাও ঠিক করুন (নিচে B-C8)।

**B-C8 — `candidateSelectionRepositories` (ছোট c) টাইপ-হিন্ট ভাঙা**
- প্রমাণ: `CandidateSelectionService.php:6,21`; প্রোবে capital-C নামে `class_exists` = false।
- প্রভাব: ওই সার্ভিস ব্যবহার করলেই ক্লাস-না-পাওয়া এরর।
- ফিক্স: ফাইল/ক্লাস-নামের case ঠিক করুন (B-C7-এর মতো)।

**B-C9 — রুট আছে, মেথড নেই — ৬৫টি**
- প্রমাণ: ১৫টি রুট ফাইলের মেথড-অস্তিত্ব যাচাইয়ে ৬৫টি রুট-মেথড পাওয়া যায়নি (কেস-ভিন্নতা ও bare-`function` বাদ দিয়ে): `show` ৩৯টি, Holiday ৬টি, HrmSetup ৭টি, খালি Procurement/Project ড্যাশবোর্ড কন্ট্রোলার-সহ। যাচাই করে বাদ দেওয়া ফলস-পজিটিভ: `StockreportController` (ইমপোর্ট-রেজোলিউশনে কাজ করে), `dataProcessinguser/position` (PHP মেথড-কল case-insensitive), `ReportController::voucher` (bare function = public)।
- প্রভাব: ওই URL-গুলোতে গেলে ৫০০/মেথড-না-পাওয়া এরর।
- ফিক্স: মেথডগুলো বানান, অথবা মৃত রুটগুলো মুছে ফেলুন; রুট-ফাইল পরিষ্কার করা না হলে নতুন ডেভেলপার ভুল রুট ধরে কাজ করবে।

### ৩.৩ Medium বাগ (১৬টি)

1. **অনেক সোর্সে `voucher_date` সেট হয় না** — Contra, SupplierPayment, SaleReturn approve, ProjectExpense, AssetsList, Adjust, ApproveLoneApplication থেকে তৈরি AccountTransaction-এ `voucher_date` সেট হয় না। ফলে ওই টাকা ড্যাশবোর্ডে (voucher_date-ভিত্তিক) অদৃশ্য, কিন্তু TB-তে (created_at-ভিত্তিক) থাকে — **ড্যাশবোর্ড বনাম TB-র অমিলের একটি প্রমাণিত কারণ**। নোট: ব্যবহারকারীর Laragon কপিতে তিনি নিজে store/update-এ `voucher_date` বসিয়েছেন (২০২৬-১০-২৫) — এই কপি মূল কোড, তাই এখানে এই বাগ আছে।
2. **KPI কার্ড বনাম ড্রিল-ডাউন মেলে না** — pending_payments: কার্ডে শুধু unpaid, ড্রিল-ডাউনে ডেট-ফিল্টার ছাড়া অ্যাডভান্স-সহ দেখায়।
3. **রেঞ্জ-ফিল্টারসহ এজিং-এ পুরো ওপেনিং-ব্যালেন্স যোগ হয়** (as-of নয়) — তারিখ-ফিল্টার দিলেও ওপেনিং পুরোটা ৯১+ বাকেটে যোগ হয়, ফলে ফিল্টার-করা এজিং ভুল হয়।
4. **ব্যাংক/ক্যাশে প্যারেন্টের সরাসরি পোস্টিং বাদ যায়** — `buildBankCashRows` শুধু child অ্যাকাউন্টের পোস্টিং নেয়; প্যারেন্ট অ্যাকাউন্টে সরাসরি পোস্টিং থাকলে তা বাদ পড়ে।
5. **`expenseBreakdown` চার্টে COGS নেই ও নেগেটিভ বাদ যায়** — ব্যয়-চার্ট অসম্পূর্ণ।
6. **`getOldAccount`-এর `orWhere` ফাঁকে inactive অ্যাকাউন্ট ড্যাশবোর্ড থেকে উধাও হয়** — status-ফিল্টার `orWhere`-এর কারণে ভুলভাবে প্রয়োগ হয়।
7. **জার্নাল-আপডেটে `invoice` দিয়ে অতিরিক্ত-ব্যাপক ডিলিট** — একই invoice_no-র অন্য ভাউচারের লেনদেনও মুছে যেতে পারে।
8. **Approved ভাউচার এডিটে লেজার পুরনো থাকে** — এডিট করলে হেডার/ডিটেইলস বদলায় কিন্তু AccountTransaction আপডেট হয় না।
9. **`dd()` ২৩টি লাইভ-জায়গায়** — এরর/ডিবাগ-পথে ব্যবহারকারীকে ডিবাগ আউটপুট দেখানো হতে পারে।
10. **ভিউ নেই ৭টি** — রুট/কন্ট্রোলার যে ভিউ ডাকে, তা নেই (৫০০ এরর)।
11. **ডেড বুকিং/কুরিয়ার ভিউ-স্তূপ** — অব্যবহৃত ভিউ ফাইল রক্ষণাবেক্ষণে বিভ্রান্তি তৈরি করে।
12. **ফোন-ভ্যালিডেশন রুলের `88`-প্রিফিক্স/ভুল-ফিল্ড যুক্তি-ভুল** — `PhoneNumberValidationRules` ভুল ফিল্ড/প্রিফিক্স যাচাই করে।
13. **ZKTeco হার্ডকোড-IP + sync-কিউ** — ডিভাইস IP কোডে হার্ডকোড (পরিবেশ বদলালে ভাঙবে); sync-কাজ ওয়েব-রিকোয়েস্টের ভেতরে চলে (§৪.১০)।
14. **Kernel-এ ভুল-কেস নেমস্পেস-এন্ট্রি** — কমেন্ট-আউট শিডিউল ব্লকে ভুল case-এর ক্লাস-নাম (আনকমেন্ট করলেই ভাঙবে)।
15. **ম্যাজিক অ্যাকাউন্ট ID** — §৩.১(b): চার্ট বদলালে (যেমন Tax with Demand-এর move) ড্যাশবোর্ডের হিসাব নীরবে ভুল হতে পারে; ID-গুলো কনফিগ/ডেটাবেস-চালিত করা উচিত।
16. **RE রিপোর্টের কোড-প্রমাণিত ভুল-পথ** — §৩.৬ দেখুন।

### ৩.৪ Normal বাগ (১২টি)

`invoices()` খালি অ্যারে ও `overdue_count` হার্ডকোড ০ (স্টাব — §৩.১); ডেড কন্ট্রোলার (AllVoucher/Transection); `descaption` টাইপো-কী; সাইলেন্ট ডিফল্ট রেঞ্জ (range না দিলে চুপচাপ 'all' ধরা হয়); `config/type.php`-র ১৩/ডুপ্লিকেট/টাইপো; CityTransformer অরফ্যান (কোনো কন্ট্রোলার ব্যবহার করে না); "Updata" টাইপো (Update-এর বদলে); sales-JS অরফ্যান + VAT-রাউন্ডিং (লেটেন্ট — এখনো ট্রিগার হয়নি); সিডার-সংক্রান্ত সমস্যা (§৩.১১); টেস্ট-ফাইলের বিপজ্জনক SQL (§৩.১২); ডকুমেন্টেশন-গ্যাপ (§১.১০)।

### ৩.৫ ডেটা-অসঙ্গতি যাচাই (ডাম্প থেকে — সব CONFIRMED, দুটোতে গুরুত্বপূর্ণ সংশোধনসহ)

**(ক) Tax with Demand (id ৭৩৯, কোড CA00739) — CONFIRMED।** chart_of_accounts-এ id ৭৩৯-এর `parent_id=23` (parent ২৩ = Indirect Expenses, `balance_type='debit'`) — অর্থাৎ move করা হয়েছে ঠিকই — কিন্তু তার নিজের `balance_type='credit'` রয়ে গেছে। একটি expense-গ্রুপের child credit balance type বহন করছে = TB/BS-এ sign-ঝুঁকি। ফিক্স: `balance_type` credit → debit করতে হবে (SELECT preview + ব্যাকআপ-সহ, ব্যবহারকারীর নিয়ম অনুযায়ী)।

**(খ) Kaligonj Factory Rent (অ্যাকাউন্ট id ৪২৫) — CONFIRMED, তবে প্রত্যাশিত চিত্রের চেয়ে সূক্ষ্ম।** ২০টি account_transactions রো: মোট Dr ২,৫০০,০০০, Cr ১,৬০০,০০০, net Dr **৯০০,০০০**। Jul-২০২৪–May-২০২৫ উইন্ডো net ঠিক ০ (Dr ৮×২০০,০০০ = Cr ১,৬০০,০০০) — কিন্তু **month-equal নয়**: Sep-২০২৪ Cr মাত্র ১১৫,৫০০ আর Oct-২০২৪ Cr ২৮৪,৫০০ (২০০,০০০ + ৮৪,৫০০ — JV00393-এর নিজের note বলে Sep ৮৪,৫০০ + Oct ২০০,০০০)। যেসব মাসে কোনো রো-ই নেই: Jan, Feb, Apr ২০২৫, plus Jun-২০২৫–Jul-২০২৬ গ্যাপ। Reversal-গুলো bulk-posted **২০২৬-০১-৩১** (JV03546/03566/03567/03568/03569 হেডারের created_at আর matching AT updated_at সব ২০২৬-০১-৩১) backdated voucher date দিয়ে। ২০২৬: DV15282, DV15964, DV16543 — ৩০০,০০০ করে, **কোনো reversal নেই — ৯০০,০০০ unreversed**। সিদ্ধান্তের জন্য JV-গুলোর other legs + tenancy চুক্তি দেখা দরকার — ডাম্প থেকে তা settle করা যাবে না (live verification pending)।

**(গ) তিনটি অভিন্ন Bank Interest ক্রেডিট ১,৩৭১,১৬৮ — CONFIRMED (তারিখ-সংশোধনসহ)।** ১,৩৭১,১৬৮-এর জন্য ঠিক ১২টি hit (৬টি AT + ৬টি journal details, আর কোথাও নেই)। JV03243/03245/03247, প্রতিটি balanced pair: Dr আলাদা UCBL FDR অ্যাকাউন্টে (১২৪১/১২৪২/১২৪৩) + Cr অ্যাকাউন্ট ৭৬৫ Bank Interest (Profit)-এ; AT ids ৪২৮৮৪/৮৫, ৪২৮৮৮/৮৯, ৪২৮৯২/৯৩; হেডার created ২০২৫-১২-৩০। **তারিখ-নুয়ান্স:** ১৫-Dec-২০২৫ হলো ভাউচার-হেডারের `date`; account_transactions.created_at হলো ১৪-Dec-২০২৫ ১৮:০০। তিনটি আলাদা FDR-এর সুদ নাকি একই সুদের triple duplication — ডাম্প থেকে settle করা যাবে না, **bank advice লাগবে** (live verification pending)।

**(ঘ) অন্যান্য ডাম্প-যাচাই:** ১৪৬টি AT রো-তে debit ও credit দুটোই সেট আছে (both-null: ০); voucher_no ডুপ্লিকেট: DV00060, DV00063 (×২ করে), ৫টি contra নম্বর ×২; credit/journal নম্বর পরিষ্কার; ডেটা আজ orphan-মুক্ত (১,২৬৬টি account_id, ২৬২টি supplier_id, ৫৩টি project_id সব resolve হয়)।

### ৩.৬ RE (Retained Earnings) রিপোর্ট — কোড-বিশ্লেষণের সিদ্ধান্ত

- **"স্টেল-ক্যাশড parent-ম্যাপিং" তত্ত্ব কোড দিয়ে খণ্ডিত:** RE ট্রি প্রতি রিকোয়েস্টে লাইভ BFS-এ বানানো হয় (`ReportController.php:2743-2781`, কোনো `Cache::` নেই) — তাই পুরোনো parent-ম্যাপিং ক্যাশে আটকে থাকার তত্ত্ব সত্য নয়।
- **কিন্তু কোড-প্রমাণিত তিনটি ভুল-পথ আছে:** (১) `YEAR(created_at)` বনাম ভাউচার-বছর — রিপোর্ট বছর ভাগ করে created_at দিয়ে, ভাউচার-তারিখ দিয়ে নয়; (২) Blade শুধু income-বছর দেখায়; (৩) হার্ডকোড-রুট ১৭/২১ + `[25]` বাদ — একটি অ্যাকাউন্ট সরে পুরো ২০২৫-ইনকাম ০.০০ হতে পারে না।
- **২০২৫-ইনকাম ঠিক ০.০০ হওয়ার নির্দিষ্ট মেকানিজম স্ট্যাটিকভাবে প্রমাণ করা যায়নি — live verification pending** (DB-তে ৭৩৯-এর parent-চেইন ও `created_at`-বছর-ভিত্তিক সাম দেখতে হবে)। ২০২৫-ইনকাম = ০.০০-এর তদন্তে প্রথমে Tax ৭৩৯-এর balance_type ঠিক করা (§৩.৫(ক)) এবং রিপোর্টের বছর-ভিত্তিক সাম (created_at বনাম voucher date) দুটোই দেখা উচিত।

### ৩.৭ সিডার ও টেস্ট-ফাইল সমস্যা

- **AccountSeeder এই ডাম্পে destructive:** ১,৫৪০টি COA রো truncate করে ~২৫-নোডের গাছ reinsert করে — আসল DB-তে চালালে চার্ট ধ্বংস হবে।
- **NavigationSeeder** navigations truncate করে (এখানে harmless, ০ রো) কিন্তু unguarded role/access রো যোগ করে।
- **৫টি সিডার এমন টেবিল টার্গেট করে যেগুলো ডাম্পেও নেই, কোনো মাইগ্রেশনেও নেই** (districts, divisions, upazilas, unions, stores) — চালালে error দেবে।
- **DatabaseSeeder আসলে মাত্র ১৬টি সিডার কল করে;** বাকি ২৬টি ফাইল থাকলেও কখনো কল হয় না; ৩টি সিডার সম্পূর্ণ খালি (SmsSetting, Smtp, UserRole); ৩৬টি সিডার `run(Faker $faker)` সিগনেচার ব্যবহার করে।
- **শুধু SmsTemplateSeeder proven safe/idempotent** (updateOrCreate, ৫টি নাম ডাম্পের সাথে ঠিক মেলে); UserSeeder-এর একটি fixed phone ডাম্পে একবার already আছে — re-run-এ phone-UNIQUE failure-এর ঝুঁকি (মান এই রিপোর্টে উদ্ধৃত করা হয়নি); বাকি বেশিরভাগ unguarded Faker loop — re-run করলে ভুয়া রো যোগ হবে।
- **Factories:** শুধু `UserFactory.php` — স্টক Laravel টেমপ্লেট, UNUSED (DatabaseSeeder-এর factory লাইন কমেন্ট-আউট), অ্যাপ-অনুযায়ী ব্যবহারকারীর ফিল্ড (phone, role_id, branch_id, status, type, employee_id) সেট করে না।

### ৩.৮ টেস্ট-ফাইলের বিপজ্জনক SQL

- **`tests/quary.sql`** (ফাইল-নামের বানান `quary`, `query` নয়) — একটি স্ক্র্যাচ ডায়াগনস্টিক **plus লাইভ-ডেটা UPDATE** (product ১৪৯৫-এর জন্য purchases ও sales-এ ডিটেইলে warehouse ৬ / হেডারে ৫ সেট করবে) — SELECT preview + ব্যাকআপ ছাড়া চালানো যাবে না।
- **`tests/voucherdate.sql`** — `voucher_date` ব্যাকফিল-ড্রাফট যার **contra ও journal জয়েন ভুল** (§১.৮-এর `table_id` প্রমাণ অনুযায়ী: ডিটেইল id-কে হেডার id-র সাথে জয়েন করে — contra ১০/২,৪৯২, journal ০/১৯,২৭৬ ম্যাচ); এর `DATE(created_at)` ফলব্যাক contra/journal রো-কে হেডার-তারিখের চেয়ে ~১ দিন আগের তারিখ দেবে চুপচাপ; আর ডাম্প-করা DB-তে ফাইলটি outright error দেবে কারণ `voucher_date` কলামই সেখানে নেই। এই ফাইল চালাবেন না — সঠিক ব্যাকফিলের জন্য type-ভিত্তিক জয়েন (হেডার বনাম ডিটেইল) ব্যবহার করতে হবে।

### ৩.৯ Laravel 8 + PHP 8.3 সামঞ্জস্য

অ্যাপ-কোডে প্রমাণিত অসঙ্গতি কার্যত নেই: ডাইনামিক-প্রপার্টি স্ক্যানে সত্যিকারের কেস মেলেনি; `strftime`/`utf8_*`/`create_function`/কার্লি-ব্রেস ০টি; শুধু PHP 8-আচরণজনিত `count(null)` TypeError-পথ (ভাউচার-স্টোরে অ্যারে-ইনপুট না এলে)। vendor-কোড পরীক্ষা করা হয়নি। অর্থাৎ PHP 8.3 নিজে ধীরগতি বা ক্র্যাশের কারণ নয় — সমস্যা কোয়েরি ও আর্কিটেকচারে (§৪)।

---

## ৪. পারফরম্যান্স — সফটওয়্যার কেন ধীর হচ্ছে (বিস্তারিত)

**সামগ্রিক রায়:** ধীরগতির মূল কারণ সার্ভার-হার্ডওয়্যার নয় — (১) একই হিসাব এক লোডে বারবার চালানো, (২) বড় টেবিলে ইনডেক্স ছাড়া ফিল্টার, (৩) PHP-তে বাকেট/মার্জ করা যেখানে SQL GROUP BY যথেষ্ট, (৪) N+1 কোয়েরি, (৫) ক্যাশিং-এর সম্পূর্ণ অনুপস্থিতি, (৬) সিঙ্ক্রোনাস ভারী কাজ। সব লাভ কোয়ালিটেটিভ — মাপা সময়/শতাংশ দাবি করা হয়নি, কারণ লাইভ প্রোফাইলিং করা হয়নি।

### ৪.১ ফাইন্যান্স ড্যাশবোর্ড: এক লোডে একই aging হিসাব ৫ বার

- প্রমাণ: `finance.blade.php:1184, 2386-2407, 2532` — প্রথম পেইন্টে ~৭টি API রিকোয়েস্ট; `loadArAging()` (লাইন ২১৬৬) ও `loadApAging()` (লাইন ২১৮৬) দুটোই একই `/api/financial-dashboard/ar-ap-aging` fetch করে অর্ধেক ডেটা ফেলে দেয় — ফলে এক `loadArApAging()`-এ aging হিসাব **৪ বার** চলে, তার উপর `kpis()` (কন্ট্রোলার লাইন ১৪৪) আরো ১ বার `buildAgingReport` চালায়।
- ফিক্স: AR/AP-র জন্য ১টি fetch, `accountGroups()` মেমোয়াইজেশন, উইজেট lazy-load (প্রথমে KPI, তারপর aging, তারপর বাকি)।
- প্রত্যাশিত লাভ: aging-হিসাবের কোয়েরি/প্রসেসিং ৫× থেকে ১×-এ নামবে; প্রথম পেইন্ট দ্রুত হবে।

### ৪.২ `voucher_date`-তে ইনডেক্স নেই — ড্যাশবোর্ডের প্রায় সব কোয়েরি full-table scan-এর দিকে

- প্রমাণ: মাইগ্রেশন `2026_10_18_164858_add_voucher_date_to_account_transactions_table.php:13` কলাম যোগ করেছে `->index()` ছাড়া (`$table->date('voucher_date')->nullable()->after('type')` — সমন্বয়কারী নিজে যাচাই করেছেন); ডাম্পের ADD KEY অংশে (লাইন ২৭০৮২২–২৭০৮৩০) `voucher_date`/`created_at`/`type` কিছুই নেই — অথচ ড্যাশবোর্ডের প্রায় সব কোয়েরি `whereDate`/`whereBetween(voucher_date)` ব্যবহার করে।
- **সংশোধনী:** আগের একটি সারাংশে "voucher_date DATE কলাম + ইনডেক্স" বলা হয়েছিল — সেটি imprecise ছিল; প্রমাণিত সত্য হলো মাইগ্রেশনে ইনডেক্স নেই।
- ফিক্স: `(account_id, voucher_date)` কম্পোজিট ইনডেক্স + `type` ইনডেক্স (লাইভে `SHOW INDEX`/`EXPLAIN` দিয়ে যাচাই সাপেক্ষে)।
- প্রত্যাশিত লাভ: তারিখ-ফিল্টার করা কোয়েরি full-table scan-এর বদলে index scan করবে — ৬৬,৭০৭-রোর টেবিলে সবচেয়ে বড় একক লাভ।

### ৪.৩ চার্ট-ট্রি: `parent_id`-তে ইনডেক্স নেই + প্রতি লেভেলে কোয়েরি

- প্রমাণ: `chart_of_accounts.parent_id`-তে ইনডেক্স নেই (মাইগ্রেশন `2021_07_10_235359...:22` ও ডাম্পে নেই); `SuperHelper.php:723-742` (`getTypeOfAccount`/`getOldAccount`) প্রতি লেভেলে কোয়েরি চালায়; `accountGroups()` (কন্ট্রোলার ৬৫–৭৯) প্রতিটা এন্ডপয়েন্টে নতুন করে চলে — `kpis()`-এ দুবার।
- ফিক্স: `parent_id`-তে ইনডেক্স + পুরো চার্ট ১ কোয়েরিতে এনে PHP-তে ট্রি বানানো (balance-sheet-এর memory-map প্যাটার্ন)।
- প্রত্যাশিত লাভ: প্রতি এন্ডপয়েন্টে চার্ট-ট্রি কোয়েরি N-লেভেল থেকে ১টিতে নামবে।

### ৪.৪ এজিং: পুরো ইতিহাস PHP-তে বাকেট করা

- প্রমাণ: `buildAgingReport()` (লাইন ২৬৪–৩৭৫) range='all' ডিফল্টে সব AR/AP লেনদেন `get()` করে প্রতি রো-তে `Carbon::parse` + `diffInDays` চালায়।
- ফিক্স: SQL CASE-বাকেট + `GROUP BY` (এজিং-বাকেট ডেটাবেসেই হিসাব করা)।
- প্রত্যাশিত লাভ: সব রো PHP-তে টেনে আনার বদলে ডেটাবেস থেকে বাকেট-সাম আসবে; মেমরি ও CPU দুটোই কমবে।

### ৪.৫ প্রতি-child SUM কোয়েরি

- প্রমাণ: `buildBankCashRows()` (৬১৩–৬১৮) ও `expenseBreakdown()` (৭০৯–৭১৫) — প্রতি child-এ `getOldAccount()` + ১টি SUM; `groupLedgerData` (`ReportController.php:3571`) — প্রতি child-এ ৪টি SUM।
- ফিক্স: ১টি `whereIn` + `groupBy(account_id)` ব্যাচড কোয়েরি (trial-balance-এর প্যাটার্ন)।
- প্রত্যাশিত লাভ: child-সংখ্যার সমান কোয়েরি থেকে ১–২টি কোয়েরিতে নামবে।

### ৪.৬ ভাউচার তালিকায় N+1 (সবচেয়ে ঘন ঘন read-path)

- প্রমাণ: `DabitVoucherRepositories.php:92-98`, `CreditVoucherRepositories.php:94-100`, `JournalVoucherRepositories.php:96-101` — পেজ-কোয়েরিতে কোনো `with()` নেই, অথচ প্রতি রো-তে `details->sum`, `details->pluck('branch.name'/'project.name')`, `user`, `updatedBy` lazy-load হয়।
- ফিক্স: `with(['details.branch','details.project','user','updatedBy'])` + `withSum()`।
- প্রত্যাশিত লাভ: প্রতি পেজে (রো-সংখ্যা × ৪–৫) কোয়েরি থেকে মুষ্টিমেয় কোয়েরিতে নামবে।

### ৪.৭ স্টক write-loop-এ প্রতি লাইনে lock + save

- প্রমাণ: `SaleRepositories.php:681-693` (update, প্রতি লাইনে `lockForUpdate()->first()+save()`), `:831-850` (destroy, প্রতি লাইনে ২টি কোয়েরি), `StockTransferRepositories.php:684`, `StockAdjustmentRepositories.php:1173`, `ProjectTransferRepositories.php:1224-1260` (প্রতি ডিটেইলে ২টি lockForUpdate)।
- ফিক্স: `whereIn` + `keyBy` ব্যাচিং — তবে lock-সংস্করণ staging-এ deadlock-পরীক্ষা সাপেক্ষ।
- প্রত্যাশিত লাভ: লাইন-সংখ্যার সমান lock-কোয়েরি থেকে ব্যাচড আপডেটে নামবে; deadlock-ঝুঁকিও কমবে।

### ৪.৮ প্রতি-মিনিট অ্যাটেনড্যান্স sync

- প্রমাণ: `Kernel.php:39` — `attendance:sync` → `everyMinute()` (অন্য শিডিউল মাত্র ২টি); প্রতি রানে: ৩-দিনের উইন্ডোতে বাইরের API কল (page_size=1000), প্রতি পাঞ্চে ১টি `Employee::where(id_card)->first()` (`SyncAttendanceFromApi.php:65`, মেমোয়াইজেশন নেই), প্রতি employee-দিনে read+write — এবং কোনো `withoutOverlapping()` নেই, তাই ধীর রান পরের রানের সাথে সংঘর্ষ করতে পারে।
- ফিক্স: `withoutOverlapping()` যোগ করুন; employee-lookup মেমোয়াইজ করুন; উইন্ডো/পেজ-সাইজ বাস্তবসম্মত করুন।
- প্রত্যাশিত লাভ: প্রতি মিনিটে পাঞ্চ-সংখ্যার সমান কোয়েরি থেকে ব্যাচড লুকআপে নামবে; ওভারল্যাপ-সংঘর্ষ বন্ধ হবে।

### ৪.৯ ProductLedgerService: ৮+ আলাদা `get()` + PHP-তে মার্জ, পেজিনেশন নেই

- প্রমাণ: `ProductLedgerService.php:14` — এক ভিউতে ৮+ আলাদা `get()` (opening/purchase/adjust/transfer/project-transfer/sale-return) + PHP-তে merge/running-balance, পেজিনেশন নেই; প্রতি কলে `Schema::hasColumn` (লাইন ৪৫) ও full pluck; কলার: `StockReportController.php:198`, `ReportController.php:6343`।
- ফিক্স: একক ব্যাচড কোয়েরি + পেজিনেশন; `Schema::hasColumn`-কে ক্যাশ করুন বা সরান।
- প্রত্যাশিত লাভ: এক ভিউতে ৮+ বড় `get()` থেকে ১–২টি কোয়েরিতে নামবে।

### ৪.১০ ক্যাশিং শূন্য + কিউ সিঙ্ক্রোনাস + Telescope রেকর্ডিং

- প্রমাণ: app/-এ `Cache::` ব্যবহার ০টি (শুধু `config/app.php:202`-এ alias); `.env.example`-এ `CACHE_DRIVER=file`, `SESSION_DRIVER=file`; `config/queue.php:21` ডিফল্ট `sync` (database/redis সংজ্ঞায়িত কিন্তু ডিফল্ট নয়; jobs/failed_jobs মাইগ্রেশন আছে, রিপোতে কোনো worker/supervisor কনফিগ নেই); একমাত্র Job `ZktecoSetUser` (২টি dispatch-জায়গা, কোনো `onQueue()` নেই) — তাই ডিভাইস-কলসহ Job-টি ওয়েব-রিকোয়েস্টের ভেতরেই সিঙ্ক্রোনাসভাবে চলে; Telescope ডিফল্ট চালু (সব watcher, database ড্রাইভার, chunk 1000, Query slow 100ms, Model hydrations true) — এবং filter-এ `isScheduledTask()` থাকায় প্রতি-মিনিটের sync-এর এন্ট্রিও non-local-এ রেকর্ড হয়।
- ফিক্স: রেফারেন্স/মাস্টার ডেটা (চার্ট-ট্রি, ব্রাঞ্চ, পণ্য-তালিকা) ক্যাশ করুন, পোস্টিং-এ strict invalidation-সহ (stale ব্যালেন্স সবচেয়ে বড় অ্যাকাউন্টিং-ঝুঁকি); কিউ ড্রাইভার database/redis-এ নিয়ে worker চালান; প্রোডাকশনে Telescope বন্ধ করুন।
- সতর্কতা: ড্রাইভার আগে বদলে worker পরে চালালে Job jobs টেবিলে জমে থাকবে — ক্রম ঠিক রাখুন (আগে worker, তারপর ড্রাইভার বদল)।

### ৪.১১ ফ্রন্টএন্ড ওজন: ~৮৬ MB public/, প্রতি পেজে ৩০+ স্ক্রিপ্ট

- প্রমাণ: public/ ১,৯৯২টি ফাইল / ~৮৬ MB; ব্যাকএন্ড লেআউটে প্রতি পেজে ৩০+ আলাদা script (এর মধ্যে `pdfmake.min.js` ১.২৫ MB + `vfs_fonts.js` ০.৮৮ MB সব পেজে, শুধু PDF-এক্সপোর্টের জন্য), `demo.js` দুবার লোড (লাইন ৩৬ ও ৫৯), `.map` ফাইলগুলো প্রোডাকশনে অপ্রয়োজনীয় (সবচেয়ে বড় ৩টি: `pdfmake.min.js.map` ~৩.৭ MB, `adminlte.min.css.map` ~৩.৬ MB, `pdfmake.js.map` ~৩.১ MB); ৩টি লোগো-PNG মিলিয়ে ~৫.৪ MB; ভার্সনিং নেই: `webpack.mix.js`-এ `.version()` নেই, `mix-manifest.json`-এ মাত্র ২টি hash-ছাড়া এন্ট্রি, আর ব্যাকএন্ড লেআউট `mix()` নয় `asset()` ব্যবহার করে (layouts/app.blade.php:13,20), `public/js/app.js` ১.৫ MB।
- ফিক্স: `.map` প্রোডাকশন থেকে সরান; pdfmake শুধু এক্সপোর্ট-পেজে লোড করুন; `demo.js` ডুপ্লিকেট সরান; লোগো কম্প্রেস করুন; `mix()` + `.version()` চালু করুন।
- প্রত্যাশিত লাভ: প্রতি পেজের ডাউনলোড-ওজন কয়েক MB কমবে; ক্যাশ-ভার্সনিং চালু হলে পুনরায় ভিজিটে ব্রাউজার-ক্যাশ কাজ করবে।

### ৪.১২ পেজিনেশনের অভাব + PHP-তে ফিল্টার

- প্রমাণ: `paginate(` মাত্র ৩ জায়গায় বনাম কন্ট্রোলারে `->get()` ৬৪২টি; ভাউচার ফর্মে প্রতি বার `Project/Customer/Supplier/Employee::all()` + `DabitVoucher::get()`; `ChartOfAccount::get()->where('status')` — SQL-এ নয়, PHP-তে ফিল্টার; `stocksummery`/stock report unpaginated `get()`; `ChartController@index` — ৬ বছরের জন্য লুপে ৬টি `DATE_FORMAT(created_at)` count (sargable নয়)।
- ফিক্স: তালিকায় সার্ভার-সাইড পেজিনেশন (২৫/৫০) + শুধু দরকারি কলাম; `where('status')` SQL-এ নিন; চার্ট-কাউন্ট ১টি GROUP BY কোয়েরিতে নিন।
- প্রত্যাশিত লাভ: বড় তালিকার পেজে সব রো টেনে আনার বদলে ২৫/৫০টি রো আসবে।

### ৪.১৩ পুরোনো রিপোর্টে প্রতি-অ্যাকাউন্ট কোয়েরি

- প্রমাণ: `FinancialStatementController::getAccountTreeBalanceAsOf()` (২০৮–২৩৭), `buildFixedAssetSchedule()` (~৩৩৪), `buildLeafBreakdownComparative()` (৪৩৯–৪৬০) — TB/BS-এর ব্যাচড প্যাটার্ন এখানে নেই।
- ফিক্স: TB/BS-এর মতো ২টি ব্যাচড SUM + memory-map ট্রি-হাঁটা ব্যবহার করুন।
- প্রত্যাশিত লাভ: অ্যাকাউন্ট-সংখ্যার সমান কোয়েরি থেকে ২টি কোয়েরিতে নামবে।

### ৪.১৪ ভালো প্যাটার্ন (রেফারেন্স হিসেবে রাখুন)

`trialbalance()`-এর ২টি ব্যাচড SUM; `balancesheet()`-এর memory-map ট্রি-হাঁটা; `kpiDetails`/`transactions`-এর `with()` eager load; `ledger()`-এর ব্যাচড invoice-grouping; `transactions()`-এর `take(200)` cap; DataTables `getList()`-এ সাধারণত offset/limit আছে; `StockAdjustmentRepositories.php:161`-এ `with(['branch','warehouse'])`; `SmsService::departmentsWithCount()` একটাই GROUP BY কোয়েরি; Store ড্যাশবোর্ডের ব্যাচড `groupBy(product_id)`। নতুন কোডে এই প্যাটার্নগুলো অনুসরণ করুন।

---

## ৫. অগ্রাধিকার সুপারিশ (যা আগে ঠিক করতে হবে, ক্রমানুসারে)

### ধাপ ১ — আজই (নিরাপত্তা-জরুরি, কোড-পরিবর্তন ছোট, প্রভাব সবচেয়ে বড়)
1. `/cleare-data` রুট মুছুন (`routes/web.php:127`)।
2. `ZktecoController::reset()`-এর বিপজ্জনক শাখা (ক্লোন, backup_db, backup_files, truncate) মুছুন; রুটটি `auth` + admin-role-এর পেছনে নিন; ইতিমধ্যে তৈরি ক্লোন-অ্যাকাউন্ট থাকলে নিষ্ক্রিয় করুন।
3. `Auth::routes(['register' => false])` করুন; বাকি লগইন-ছাড়া রুটগুলো (§২.১-এর S-04) হয় `auth`-এর পেছনে নিন, নয়তো মুছুন।
4. প্রোডাকশনে Telescope বন্ধ করুন; `public/error_log` মুছুন।

### ধাপ ২ — অথরাইজেশন (১–২ সপ্তাহ)
5. Role মিডলওয়্যার বানিয়ে সব মডিউল-গ্রুপে লাগান; ফাইন্যান্স/POS/স্টোর/SMS API-তে role + branch-scope যোগ করুন।
6. SMS `getConfig()` থেকে `api_key` সরান; `send()`-এ admin-role চেক; cURL-এ TLS যাচাই চালু করুন।
7. Company আপলোডে mime + র্যান্ডম নাম + non-public ডিস্ক (বা `.htaccess` দিয়ে PHP এক্সিকিউশন বন্ধ); state-changing GET রুটগুলোকে POST-তে নিন; DataTables `dir` allowlist করুন।

### ধাপ ৩ — হিসাবের অখণ্ডতা (ভাউচার-সংশোধন — টেস্ট-সহ)
8. Contra আপডেটে পুরনো ডিটেইলস/ট্রানজ্যাকশন মুছে তারপর নতুন তৈরি করুন; Supplier/Customer পেমেন্ট-আপডেটের ডিলিট সঠিক কী/id দিয়ে করুন।
9. সব ভাউচার-সার্ভিসে Dr=Cr চেক বাধ্যতামূলক করুন; `approve()`-এ approve-ফ্ল্যাগ চেক যোগ করুন; ভাউচার-নম্বর `latest('id')+1`-এর বদলে লক/সিকোয়েন্স/UNIQUE-ভিত্তিক করুন।
10. প্রতিটি সংশোধনের পর TB/BS টাই-আউট যাচাই করুন (আগের ৬২৫-টাকার orphan-এর মতো ঘটনা ধরতে দুটি Pest রিগ্রেশন-টেস্টের ধারণা আগেও দেওয়া হয়েছে: কোনো লাইনে `account_id = 0` নয়; প্রতি ভাউচারের Dr = Cr)।

### ধাপ ৪ — ডেটা-সংশোধন (SELECT preview + ব্যাকআপ-সহ, ব্যবহারকারীর নিয়ম অনুযায়ী)
11. Tax with Demand (id ৭৩৯): `balance_type` credit → debit (SELECT preview দেখিয়ে)।
12. Kaligonj Factory Rent ও triple Bank Interest: **স্বয়ংক্রিয়ভাবে "ঠিক" করবেন না** — JV-গুলোর other legs, tenancy চুক্তি, bank advice দেখে অ্যাকাউন্টস-টিমের সিদ্ধান্তে পোস্ট করুন।
13. ডুপ্লিকেট `voucher_no` ও `accountCode` পরিষ্কার করুন (ম্যাচিংয়ে `unique_identifier`/`id` ব্যবহার করুন)।

### ধাপ ৫ — কোড বনাম ডেটাবেস ড্রিফট ঠিক করা
14. Canonical DB ঠিক করুন (Laragon বনাম এই ডাম্পের স্ন্যাপশট — দুটি আলাদা; §৫.১ দেখুন) এবং তাতে বকেয়া মাইগ্রেশন (voucher_date-সহ) চালান।
15. ইনডেক্স যোগ করুন: `(account_id, voucher_date)`, `type`, `chart_of_accounts.parent_id`, ভাউচার-হেডারের `voucher_no`/`date`/`account_id`, ডিটেইল টেবিলের `*_voucher_id`/`account_id` (লাইভে EXPLAIN দিয়ে যাচাই সাপেক্ষে)।
16. **তারিখ-ভিত্তি এক করুন:** ড্যাশবোর্ড `voucher_date`, TB/BS `created_at` — দুটি ভিন্ন ভিত্তি দুটি ভিন্ন উত্তর দেয়। সিদ্ধান্ত নিন কোনটি canonical, আর অন্যটিকে তাতে আনুন (অথবা পার্থক্য ডকুমেন্ট করুন)। একইভাবে `tests/voucherdate.sql` চালাবেন না — type-ভিত্তিক সঠিক জয়েন দিয়ে নতুন ব্যাকফিল লিখুন।
17. Case-সংবেদনশীলতা ঠিক করুন: `conversionTransformer.php` → `ConversionTransformer.php`, `candidateSelectionRepositories` → সঠিক case; ৬৫টি মৃত রুট হয় বানান, নয়তো মুছুন।

### ধাপ ৬ — পারফরম্যান্স (প্রভাব-ক্রমে)
18. ড্যাশবোর্ড: ১টি aging fetch + `accountGroups()` মেমোয়াইজেশন + উইজেট lazy-load (§৪.১)।
19. এজিং SQL CASE-বাকেট + GROUP BY (§৪.৪); ব্যাংক/ক্যাশ/ব্যয়/groupLedger ব্যাচড `groupBy` (§৪.৫, §৪.১৩)।
20. ভাউচার তালিকায় `with()` + `withSum()` (§৪.৬); তালিকায় সার্ভার-সাইড পেজিনেশন (§৪.১২)।
21. রেফারেন্স-ডেটা ক্যাশ (পোস্টিং-এ strict invalidation-সহ) + কিউ worker + ভারী রিপোর্ট কিউতে (§৪.১০)।
22. স্টক write-loop ব্যাচিং (deadlock-পরীক্ষা-সহ, §৪.৭); অ্যাটেনড্যান্স sync-এ `withoutOverlapping()` + মেমোয়াইজেশন (§৪.৮); ProductLedgerService ব্যাচড কোয়েরি + পেজিনেশন (§৪.৯)।
23. ফ্রন্টএন্ড: `.map` সরান, pdfmake শুধু এক্সপোর্ট-পেজে, `demo.js` ডুপ্লিকেট সরান, `mix()` + `.version()` চালু করুন (§৪.১১)।

### ধাপ ৭ — পরিচ্ছন্নতা ও রক্ষণাবেক্ষণ
24. লাইভ কোড থেকে `dd()` সরান (২৩টি জায়গা); কমেন্ট-আউট পুরনো কোড-কপি মুছুন (ফাইন্যান্স কন্ট্রোলার ৬৬৭ লাইন, ReportController ২,৭৭৮ লাইন)।
25. সিডার নিরাপদ করুন: AccountSeeder/NavigationSeeder কখনো আসল DB-তে চালাবেন না; ৫টি মডেল-শুধু-টেবিলের সিডার ঠিক করুন; সিডার idempotent করুন; PHP টেস্ট লিখতে শুরু করুন (বর্তমানে ০)।
26. Session lifetime/encrypt/secure ঠিক করুন (§২.২, S-10); ফোন-ভ্যালিডেশন রুল ঠিক করুন; Kernel-এর ভুল-কেস এন্ট্রি ঠিক করুন; ম্যাজিক অ্যাকাউন্ট ID-গুলো কনফিগ/ডেটাবেস-চালিত করুন।

---

## ৫.১ ডাম্প-প্রোভেন্যান্স সতর্কতা (সংখ্যা পড়ার আগে জানা জরুরি)

- এই ডাম্পে `voucher_date` কলাম নেই (০ উপস্থিতি) এবং এর migrations টেবিল ২০২৬-০৯-২০-এ শেষ — অর্থাৎ ডাম্পটি ১৮ অক্টোবরের voucher_date মাইগ্রেশনের আগের অবস্থা ধরে, অথবা মাইগ্রেশন চালানো হয়নি।
- ডাম্পের AT টাইপ-সংখ্যা (debit ৩৬,৬৭২; journal ১৯,২৭৬; purchase ৪,৫৮৬; contra ২,৪৯২; credit ২,১০৭; sale ১,৫৭৪; মোট ৬৬,৭০৭) ব্যবহারকারীর ২৫ অক্টোবরের লাইভ Laragon কোয়েরির সংখ্যার (debit ৩৬,৩৯৭; journal ১৯,১৫০; purchase ৪,৪৯৮; contra ২,৪৪৬; credit ২,০৮১; sale ১,৫৩৮; আংশিক যোগফল ৬৬,১১০) চেয়ে বেশি — একই ৬টি টাইপে পার্থক্য ৫৯৭ রো। **তাই এই ডাম্প ≠ ব্যবহারকারীর Laragon DB-র স্ন্যাপশট।** এই রিপোর্টের সব সংখ্যা ডাম্প-ভিত্তিক; লাইভ DB-র সংখ্যা আলাদা হতে পারে — কোনটিকে "লাইভ" ধরতে হবে, তা ব্যবহারকারীকে ঠিক করতে হবে।

## ৫.২ Live verification pending (স্ট্যাটিকভাবে প্রমাণ করা যায়নি — অনুমান নয়)

1. `route:list` ভাঙনের সঠিক ট্রিগার-পথ (ক্লাস-ভাঙা প্রমাণিত; Laravel 8-এ `route:list` কন্ট্রোলার ইনস্ট্যানশিয়েট করে না বলে ট্রিগার-পথ লাইভে দেখতে হবে)।
2. DataTables `dir` প্যাটার্ন আসলে ইনজেকশনে পরিণত হয় কিনা।
3. Company আপলোডে `.php` আসলে এক্সিকিউট হয় কিনা।
4. Stored XSS-এর ক্ষেত্রে DB-মান আক্রমণকারী-নিয়ন্ত্রিত কিনা।
5. প্রোডাকশনে Telescope-এর আসল এক্সপোজার (gate-ইমেইল-নির্ভর) ও `public/error_log`-এর পৌঁছনো-যোগ্যতা।
6. RE রিপোর্টে ২০২৫-ইনকাম ঠিক ০.০০ হওয়ার নির্দিষ্ট মেকানিজম (৭৩৯-এর parent-চেইন + created_at-বছর-ভিত্তিক সাম DB-তে দেখতে হবে)।
7. Triple Bank Interest: তিনটি আলাদা FDR-এর সুদ নাকি একই সুদের ডুপ্লিকেশন (bank advice লাগবে)।
8. Kaligonj Factory Rent: JV-গুলোর other legs ও tenancy চুক্তি।
9. লাইভ DB-তে এখন `voucher_date` আছে কিনা এবং লাইভ ইনডেক্স-অবস্থা (SHOW INDEX/EXPLAIN)।
10. সব পারফরম্যান্স-লাভের মাপা মান (EXPLAIN, রেসপন্স-টাইম, সার্ভার OPcache/PHP সেটিং)।
11. SMS `send()`-এর আসল অথরাইজেশন-স্কোপ; ৬৫টি মৃত রুটের রানটাইম আচরণ (কিছু `__call`/ইমপোর্টে কাজ করতে পারে)।
12. রিপোজিটরিতে ৫/৬/৭/৮ সংখ্যা-টাইপ কোন ফিল্ডে যায় (ডাম্পে AT.type-এ শুধু স্ট্রিং পাওয়া গেছে — পূর্ণ ট্রেস বাকি)।

## ৫.৩ কভারেজ বিবৃতি (ঠিক কী দেখা হয়েছে, কী দেখা হয়নি)

- **পুরোপুরি পড়া:** ফাইন্যান্স API কন্ট্রোলার (লাইভ অংশ), FinanceDashboardController, RE-`retainedearning` + `account()` অঞ্চল, সংশ্লিষ্ট Blade-অঞ্চল, ৪টি ভাউচার-রিপো, Supplier/Customer-পেমেন্ট রিপো, SaleReturn approve, Kernel, ১টি Job, ১টি Rule, Handler, ৭টি Provider (স্কিম), সব কনফিগের মূল কী, ডাম্পের সব CREATE TABLE।
- **Grep/স্ক্রিপ্ট-ভিত্তিক (সব ফাইল):** ১৫৬টি কন্ট্রোলারের মেথড-তালিকা, `use App\…` বনাম ফাইল-অস্তিত্ব, `view()`/`route()`-নাম, ১৫টি রুট-ফাইলের মেথড-অস্তিত্ব, `AccountTransaction::create`-সাইটে `voucher_date`, `dd/`TODO, ডাইনামিক-প্রপার্টি, PHP 8.3-প্যাটার্ন, ১৯টি কনফিগের টপ-লেভেল কী, ৫৯টি Transformer-রেফারেন্স, ১৫টি কমান্ড-সিগনেচার।
- **পুরোপুরি পড়া হয়নি:** বাকি ~১৫০টি কন্ট্রোলারের ভেতরের লজিক, বেশিরভাগ সার্ভিস/রিপোজিটরির ভেতরের লজিক, বেশিরভাগ Blade, মাইগ্রেশন-ফাইলের ভেতরের কলাম-পরিবর্তন (ড্রিফট-স্তরে যাচাই হয়েছে), vendor কোড; Helpers-এর `CustomHrmHelper`/`ZktecoMatching` পুরো পড়া হয়নি; রুট-ফাইলগুলোর লোড-চেইন পুরোপুরি ট্রেস করা হয়নি; SMS-POST-রুটের CSRF-আচরণ ও sales-JS-এর আসল লোড-পাথ live verification pending।

---

## পরিশিষ্ট

- **পরিশিষ্ট-ক:** সব কন্ট্রোলার (১৫৬টি, ডিরেক্টরি-ভিত্তিক তালিকা)।
- **পরিশিষ্ট-খ:** সব মডেল (১৩৮টি, গ্রুপ-ভিত্তিক তালিকা)।
- **পরিশিষ্ট-গ:** সব ১৩৭টি ডেটাবেস টেবিল (ডোমেন-ভিত্তিক, রো + PK + ইনডেক্স + প্রথম ১২টি কলাম)।
- **পরিশিষ্ট-ঘ:** সব মাইগ্রেশন ফাইল (২১৯টি, নামের তালিকা)।
- **Blade ভিউ (৫০২টি):** পূর্ণ নাম-তালিকা এই রিপোর্টে ছাপা হয়নি (দৈর্ঘ্য), তবে এলাকা-ভিত্তিক মানচিত্র: `backend/` একাই ৪৮১টি (ড্যাশবোর্ড, ফাইন্যান্স, ইনভেন্টরি, HRM, সেলস, প্রজেক্ট, রিপোর্ট, সেটিংস, ইউজার), বাকি `auth/` + `layouts/` + `components/` + `emails/`-এ। গণনা `find resources/views -name '*.blade.php'` দিয়ে যাচাইকৃত।
- **রুট-বাই-রুট পূর্ণ তালিকা (৯৬৯টি কল):** এই রিপোর্টে ফাইল-স্তর + গ্রুপ-স্তর + api.php-র পূর্ণ বিবরণ দেওয়া হয়েছে (§১.৬); প্রতিটি রুট-কলের লাইন-নম্বরসহ তালিকা বিশ্লেষণ-ফাইলে সংরক্ষিত আছে।

---

## পরিশিষ্ট-ক: সব কন্ট্রোলার (ডিরেক্টরি-ভিত্তিক)

গণনা: `find app/Http/Controllers -name '*.php'` — মোট 156টি ফাইল।

- `app/Http/Controllers/AdminRoleController.php`
- `app/Http/Controllers/AllVoucherController.php`
- `app/Http/Controllers/Api/FinancialDashboardApiController.php`
- `app/Http/Controllers/Api/HrmDashboardApiController.php`
- `app/Http/Controllers/Api/PosDashboardApiController.php`
- `app/Http/Controllers/Api/SmsConfigurationApiController.php`
- `app/Http/Controllers/Api/StoreDashboardApiController.php`
- `app/Http/Controllers/Auth/ConfirmPasswordController.php`
- `app/Http/Controllers/Auth/ForgotPasswordController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/ResetPasswordController.php`
- `app/Http/Controllers/Auth/VerificationController.php`
- `app/Http/Controllers/Backend/ActivityLogs/ActivityLogsController.php`
- `app/Http/Controllers/Backend/AssetsManagement/AssetsCategoryController.php`
- `app/Http/Controllers/Backend/AssetsManagement/AssetsDestroyController.php`
- `app/Http/Controllers/Backend/AssetsManagement/AssetsListController.php`
- `app/Http/Controllers/Backend/AssetsManagement/AssetsWarrantyController.php`
- `app/Http/Controllers/Backend/AssetsManagement/FinancialYearController.php`
- `app/Http/Controllers/Backend/Chart/ChartController.php`
- `app/Http/Controllers/Backend/Dashboard/FinanceDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/HomeController.php`
- `app/Http/Controllers/Backend/Dashboard/HrmDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/ManagementDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/PosDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/ProcurementDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/ProjectDashboardController.php`
- `app/Http/Controllers/Backend/Dashboard/StoreDashboardController.php`
- `app/Http/Controllers/Backend/Hrm/ApproveCashReqApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/ApproveLeaveApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/ApproveLoneApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/AttendanceController.php`
- `app/Http/Controllers/Backend/Hrm/AttendanceLogController.php`
- `app/Http/Controllers/Backend/Hrm/AwardController.php`
- `app/Http/Controllers/Backend/Hrm/CashApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/EmployeeController.php`
- `app/Http/Controllers/Backend/Hrm/HolidayController.php`
- `app/Http/Controllers/Backend/Hrm/LeaveApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/LoneApplicationController.php`
- `app/Http/Controllers/Backend/Hrm/PaySheetController.php`
- `app/Http/Controllers/Backend/Hrm/PositionController.php`
- `app/Http/Controllers/Backend/Hrm/SalarySheetController.php`
- `app/Http/Controllers/Backend/InventorySetup/AdjustController.php`
- `app/Http/Controllers/Backend/InventorySetup/AdjustCreditController.php`
- `app/Http/Controllers/Backend/InventorySetup/AdjustmentDepostiController.php`
- `app/Http/Controllers/Backend/InventorySetup/BrandController.php`
- `app/Http/Controllers/Backend/InventorySetup/CategoryController.php`
- `app/Http/Controllers/Backend/InventorySetup/ConversionController.php`
- `app/Http/Controllers/Backend/InventorySetup/CustomerController.php`
- `app/Http/Controllers/Backend/InventorySetup/CustomerGroupController.php`
- `app/Http/Controllers/Backend/InventorySetup/GrnController.php`
- `app/Http/Controllers/Backend/InventorySetup/MainCategoryController.php`
- `app/Http/Controllers/Backend/InventorySetup/ProductController.php`
- `app/Http/Controllers/Backend/InventorySetup/ProductOpeningStockController.php`
- `app/Http/Controllers/Backend/InventorySetup/PurchaseController.php`
- `app/Http/Controllers/Backend/InventorySetup/PurchaseOrderController.php`
- `app/Http/Controllers/Backend/InventorySetup/PurchaseRequisitionController.php`
- `app/Http/Controllers/Backend/InventorySetup/PurchaseReturnController.php`
- `app/Http/Controllers/Backend/InventorySetup/PurchaseReturnDetailsController.php`
- `app/Http/Controllers/Backend/InventorySetup/StockAjdustmentController.php`
- `app/Http/Controllers/Backend/InventorySetup/StockReportController.php`
- `app/Http/Controllers/Backend/InventorySetup/StockTransferController.php`
- `app/Http/Controllers/Backend/InventorySetup/SupplierController.php`
- `app/Http/Controllers/Backend/InventorySetup/UnitController.php`
- `app/Http/Controllers/Backend/Payment/CustomerPaymentContorller.php`
- `app/Http/Controllers/Backend/Payment/SupplierPaymentContorller.php`
- `app/Http/Controllers/Backend/Production/ProductionController.php`
- `app/Http/Controllers/Backend/Project/InvoiceController.php`
- `app/Http/Controllers/Backend/Project/ProductUseController.php`
- `app/Http/Controllers/Backend/Project/ProjectController.php`
- `app/Http/Controllers/Backend/Project/ProjectExpenseController.php`
- `app/Http/Controllers/Backend/Project/ProjectMoneyController.php`
- `app/Http/Controllers/Backend/Project/ProjectRequisitionController.php`
- `app/Http/Controllers/Backend/Project/ProjectReturnController.php`
- `app/Http/Controllers/Backend/Project/ProjectTransferController.php`
- `app/Http/Controllers/Backend/Recruitment/CandidateInformationController.php`
- `app/Http/Controllers/Backend/Recruitment/CandidateSelectionController.php`
- `app/Http/Controllers/Backend/Recruitment/CandidateShortlistController.php`
- `app/Http/Controllers/Backend/Reports/CheckRegisterController.php`
- `app/Http/Controllers/Backend/Reports/FinancialStatementController.php`
- `app/Http/Controllers/Backend/Reports/ReportController.php`
- `app/Http/Controllers/Backend/Reports/StatementOfChangesInEquityController.php`
- `app/Http/Controllers/Backend/Sale/DeliveryChalanController.php`
- `app/Http/Controllers/Backend/Sale/SaleController.php`
- `app/Http/Controllers/Backend/Sale/SaleReturnController.php`
- `app/Http/Controllers/Backend/Sale/SaleReturnDetailsController.php`
- `app/Http/Controllers/Backend/Service/ServiceController.php`
- `app/Http/Controllers/Backend/Settings/AccountsController.php`
- `app/Http/Controllers/Backend/Settings/AuthorizedcapitalController.php`
- `app/Http/Controllers/Backend/Settings/BranchController.php`
- `app/Http/Controllers/Backend/Settings/CashBookController.php`
- `app/Http/Controllers/Backend/Settings/ChartOfAccountTreeController.php`
- `app/Http/Controllers/Backend/Settings/CommissionRulesController.php`
- `app/Http/Controllers/Backend/Settings/CompanyController.php`
- `app/Http/Controllers/Backend/Settings/ContraVoucherController.php`
- `app/Http/Controllers/Backend/Settings/CreditVoucherController.php`
- `app/Http/Controllers/Backend/Settings/CurrencyController.php`
- `app/Http/Controllers/Backend/Settings/CustomerOpeningController.php`
- `app/Http/Controllers/Backend/Settings/DabitVoucherController.php`
- `app/Http/Controllers/Backend/Settings/ExpenseCategoryController.php`
- `app/Http/Controllers/Backend/Settings/ExpenseController.php`
- `app/Http/Controllers/Backend/Settings/FiscalYearController.php`
- `app/Http/Controllers/Backend/Settings/GeneralSettingsController.php`
- `app/Http/Controllers/Backend/Settings/HrmSetupController.php`
- `app/Http/Controllers/Backend/Settings/JournalVoucherController.php`
- `app/Http/Controllers/Backend/Settings/LanguageController.php`
- `app/Http/Controllers/Backend/Settings/NavigationController.php`
- `app/Http/Controllers/Backend/Settings/OpeningController.php`
- `app/Http/Controllers/Backend/Settings/PaidupcapitalController.php`
- `app/Http/Controllers/Backend/Settings/SmsSettingController.php`
- `app/Http/Controllers/Backend/Settings/SmtpController.php`
- `app/Http/Controllers/Backend/Settings/StoreController.php`
- `app/Http/Controllers/Backend/Settings/TestController.php`
- `app/Http/Controllers/Backend/Settings/TransferController.php`
- `app/Http/Controllers/Backend/Settings/WarehousesController.php`
- `app/Http/Controllers/Backend/Test/ZktecoSyncController.php`
- `app/Http/Controllers/Backend/Usermanage/UserController.php`
- `app/Http/Controllers/Backend/Usermanage/UserRoleController.php`
- `app/Http/Controllers/BalanceTransferLogController.php`
- `app/Http/Controllers/BankController.php`
- `app/Http/Controllers/CompanyController.php`
- `app/Http/Controllers/CompanySetupController.php`
- `app/Http/Controllers/Controller.php`
- `app/Http/Controllers/CurrencyController.php`
- `app/Http/Controllers/CustomerLedgerController.php`
- `app/Http/Controllers/DistrictController.php`
- `app/Http/Controllers/DivisionController.php`
- `app/Http/Controllers/FiscalYearController.php`
- `app/Http/Controllers/FormController.php`
- `app/Http/Controllers/GeneralController.php`
- `app/Http/Controllers/GeneralLedgerController.php`
- `app/Http/Controllers/GeneralSetupController.php`
- `app/Http/Controllers/InoviceController.php`
- `app/Http/Controllers/InvoiceDetailsController.php`
- `app/Http/Controllers/LanguageController.php`
- `app/Http/Controllers/LedgerMergeController.php`
- `app/Http/Controllers/NavigationController.php`
- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/ProductUnitController.php`
- `app/Http/Controllers/PurchasesController.php`
- `app/Http/Controllers/PurchasesDetailsController.php`
- `app/Http/Controllers/SalesDetailsController.php`
- `app/Http/Controllers/SmtpController.php`
- `app/Http/Controllers/StockController.php`
- `app/Http/Controllers/StockReport.php`
- `app/Http/Controllers/StockSummaryController.php`
- `app/Http/Controllers/StoreController.php`
- `app/Http/Controllers/SupplierController.php`
- `app/Http/Controllers/SupplierLedgerController.php`
- `app/Http/Controllers/TransectionController.php`
- `app/Http/Controllers/TransferDetailsController.php`
- `app/Http/Controllers/UnionController.php`
- `app/Http/Controllers/UpazilaController.php`
- `app/Http/Controllers/UserManageController.php`
- `app/Http/Controllers/UserRoleController.php`
- `app/Http/Controllers/ZktecoController.php`

## পরিশিষ্ট-খ: সব মডেল

গণনা: `find app/Models -name '*.php'` — মোট 138টি ফাইল।

- `app/Models/AccountTransaction.php`
- `app/Models/Accounts.php`
- `app/Models/ActivityLog.php`
- `app/Models/Adjust.php`
- `app/Models/AdminRole.php`
- `app/Models/AllVoucher.php`
- `app/Models/Announcement.php`
- `app/Models/AssetsCategory.php`
- `app/Models/AssetsList.php`
- `app/Models/AssetsWarranty.php`
- `app/Models/Attendance.php`
- `app/Models/Authorizedcapital.php`
- `app/Models/Award.php`
- `app/Models/BalanceTransferLog.php`
- `app/Models/Bank.php`
- `app/Models/Branch.php`
- `app/Models/Brand.php`
- `app/Models/CandidateInforamtion.php`
- `app/Models/CandidateSelection.php`
- `app/Models/CandidateShortlist.php`
- `app/Models/CashBook.php`
- `app/Models/CashReq.php`
- `app/Models/Category.php`
- `app/Models/Chart.php`
- `app/Models/ChartOfAccount.php`
- `app/Models/CheckRegister.php`
- `app/Models/Commission.php`
- `app/Models/CommissionRule.php`
- `app/Models/Company.php`
- `app/Models/ContraVoucher.php`
- `app/Models/ContraVoucherDetails.php`
- `app/Models/Conversion.php`
- `app/Models/CreditVoucher.php`
- `app/Models/CreditVoucherDetails.php`
- `app/Models/Currency.php`
- `app/Models/Customer.php`
- `app/Models/CustomerGroup.php`
- `app/Models/CustomerOpening.php`
- `app/Models/CustomerPayment.php`
- `app/Models/DabitVoucher.php`
- `app/Models/DabitVoucherDetails.php`
- `app/Models/Destroyitems.php`
- `app/Models/District.php`
- `app/Models/Division.php`
- `app/Models/EducationInfo.php`
- `app/Models/EmpPayBonus.php`
- `app/Models/EmpPayDetails.php`
- `app/Models/Employee.php`
- `app/Models/Expense.php`
- `app/Models/ExpenseCategory.php`
- `app/Models/FinancialYear.php`
- `app/Models/FiscalYear.php`
- `app/Models/Form.php`
- `app/Models/General.php`
- `app/Models/GeneralLedger.php`
- `app/Models/GeneralSetup.php`
- `app/Models/Grn.php`
- `app/Models/Grn_detail.php`
- `app/Models/Holiday.php`
- `app/Models/Inovice.php`
- `app/Models/Invoice.php`
- `app/Models/InvoiceDetails.php`
- `app/Models/JournalVoucher.php`
- `app/Models/JournalVoucherDetails.php`
- `app/Models/Language.php`
- `app/Models/LeaveApplication.php`
- `app/Models/LoanDetail.php`
- `app/Models/Lone.php`
- `app/Models/MainCategory.php`
- `app/Models/MonthlyPayableSalary.php`
- `app/Models/Navigation.php`
- `app/Models/Opening.php`
- `app/Models/OpeningBalance.php`
- `app/Models/PaidupCapital.php`
- `app/Models/Position.php`
- `app/Models/PrDetails.php`
- `app/Models/Product.php`
- `app/Models/ProductOpeningStock.php`
- `app/Models/ProductOpeningStockDetails.php`
- `app/Models/ProductUnit.php`
- `app/Models/ProductUse.php`
- `app/Models/ProductUseDetails.php`
- `app/Models/Production.php`
- `app/Models/Project.php`
- `app/Models/ProjectExpense.php`
- `app/Models/ProjectMoney.php`
- `app/Models/ProjectRequisition.php`
- `app/Models/ProjectRequisitionDetails.php`
- `app/Models/ProjectReturn.php`
- `app/Models/ProjectReturnDetails.php`
- `app/Models/ProjectTransfer.php`
- `app/Models/ProjectTransferDetails.php`
- `app/Models/PurchaseOrder.php`
- `app/Models/PurchaseOrderDetail.php`
- `app/Models/PurchaseRequisition.php`
- `app/Models/PurchaseReturn.php`
- `app/Models/PurchaseReturnDetail.php`
- `app/Models/Purchases.php`
- `app/Models/PurchasesDetails.php`
- `app/Models/Report.php`
- `app/Models/ReturnDeposit.php`
- `app/Models/RoleAccess.php`
- `app/Models/SalarySheet.php`
- `app/Models/Sale.php`
- `app/Models/SaleReturn.php`
- `app/Models/SaleReturnDetails.php`
- `app/Models/Service.php`
- `app/Models/SmsConfiguration.php`
- `app/Models/SmsLog.php`
- `app/Models/SmsSetting.php`
- `app/Models/SmsTemplate.php`
- `app/Models/Smtp.php`
- `app/Models/Stock.php`
- `app/Models/StockAjdustment.php`
- `app/Models/StockAjdustmentDetailst.php`
- `app/Models/StockReport.php`
- `app/Models/StockSummary.php`
- `app/Models/StockTransfer.php`
- `app/Models/Store.php`
- `app/Models/Supplier.php`
- `app/Models/SupplierPayment.php`
- `app/Models/SupplierSelectPrice.php`
- `app/Models/Transection.php`
- `app/Models/Transfer.php`
- `app/Models/TransferDetails.php`
- `app/Models/Union.php`
- `app/Models/Upazila.php`
- `app/Models/User.php`
- `app/Models/UserManage.php`
- `app/Models/UserRole.php`
- `app/Models/Warehouse.php`
- `app/Models/WorkExpInfo.php`
- `app/Models/customerLedger.php`
- `app/Models/deliveryChalan.php`
- `app/Models/deliveryChalanDetails.php`
- `app/Models/sales_Details.php`
- `app/Models/supplierLedger.php`
- `app/Models/supplierSalePrice.php`

## পরিশিষ্ট-গ: সব ১৩৭টি ডেটাবেস টেবিল (ডোমেন-ভিত্তিক, রো + PK + ইনডেক্স + প্রথম ১২টি কলাম)

প্রতিটি টেবিলে: রো-সংখ্যা (ডাম্পে গোনা), PK, PK-ছাড়া ইনডেক্স, এবং প্রথম ১২টি কলাম। অর্থ-সংক্রান্ত প্রধান টেবিলের পূর্ণ বিবরণ §৩-এ আছে; এখানে তালিকা-স্তরের কভারেজ দেওয়া হলো যাতে কোনো টেবিল বাদ না পড়ে।

### অর্থ / হিসাব (finance)

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `accounts` | 0 | PRIMARY KEY (`id`) | — | `id`, `created_at`, `updated_at` |
| `chart_of_accounts` | 1,540 | PRIMARY KEY (`id`) | UNIQUE KEY `chart_of_accounts_unique_identifier_unique` (`unique_identifier`); KEY `chart_of_accounts_accountable_type_accountable_id_index` (`accountable_type`,`accountable_id`) | `id`, `accountCode`, `account_code`, `account_name`, `parent_id`, `branch_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+9) |
| `chart_of_accounts_backup_20260916` | 169 | PK নেই | — | `id`, `accountCode`, `account_code`, `account_name`, `parent_id`, `branch_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+9) |
| `chart_of_accounts_backup_supplier20260916` | 5 | PK নেই | — | `id`, `accountCode`, `account_code`, `account_name`, `parent_id`, `branch_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+9) |
| `general_ledgers` | 10 | PRIMARY KEY (`id`) | KEY `general_ledgers_general_id_form_id_chart_id_index` (`general_id`,`form_id`,`chart_id`) | `id`, `general_id`, `form_id`, `chart_id`, `date`, `debit`, `credit`, `memo`, `status`, `updated_by`, `created_by`, `deleted_by`, … (+3) |
| `general_setups` | 10 | PRIMARY KEY (`id`) | — | `id`, `currency`, `currency_position`, `language`, `timezone`, `dateformat`, `decimal_separate`, `thousand_separate`, `status`, `updated_by`, `created_by`, `deleted_by`, … (+3) |
| `generals` | 10 | PRIMARY KEY (`id`) | KEY `generals_branch_id_store_id_form_id_index` (`branch_id`,`store_id`,`form_id`) | `id`, `date`, `invoice_no`, `branch_id`, `store_id`, `form_id`, `debit`, `credit`, `note`, `status`, `updated_by`, `created_by`, … (+4) |
| `openings` | 0 | PRIMARY KEY (`id`) | KEY `openings_branch_id_account_id_index` (`branch_id`,`account_id`) | `id`, `branch_id`, `date`, `account_id`, `debit`, `credit`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+1) |
| `opening_balances` | 0 | PRIMARY KEY (`id`) | — | `id`, `date_`, `account_id`, `amount`, `note`, `created_by`, `created_at`, `updated_at` |
| `banks` | 0 | PRIMARY KEY (`id`) | — | `id`, `bank_name`, `account_name`, `account_number`, `branch`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `check_registers` | 126 | PRIMARY KEY (`id`) | — | `id`, `invoice_ref`, `cheque_no`, `cheque_date`, `cleared_date`, `from_account_id`, `to_account_id`, `payee_type`, `amount`, `debit`, `credit`, `description`, … (+5) |
| `cash_books` | 0 | PRIMARY KEY (`id`) | — | `id`, `created_at`, `updated_at` |
| `balance_transfer_logs` | 0 | PRIMARY KEY (`id`) | — | `id`, `from_account_id`, `to_account_id`, `branch_id`, `debit`, `credit`, `amount`, `date`, `note`, `deleted_at`, `created_at`, `updated_at` |
| `transections` | 16 | PRIMARY KEY (`id`) | — | `id`, `account_id`, `employee_id`, `branch_id`, `payment_id`, `type`, `date`, `debit`, `credit`, `amount`, `note`, `user_id`, … (+9) |
| `authorizedcapitals` | 0 | PRIMARY KEY (`id`) | — | `id`, `price`, `share`, `created_at`, `updated_at` |
| `paidup_capitals` | 0 | PRIMARY KEY (`id`) | — | `id`, `price`, `share`, `created_at`, `updated_at` |
| `financial_years` | 0 | PRIMARY KEY (`id`) | — | `id`, `f_year`, `f_year_start`, `f_year_end`, `status`, `created_at`, `updated_at` |
| `fiscal_years` | 10 | PRIMARY KEY (`id`) | KEY `fiscal_years_branch_id_index` (`branch_id`) | `id`, `date`, `fiscal_year`, `branch_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |

### ভাউচার / লেনদেন (vouchers & transactions)

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `account_transactions` | 66,707 | PRIMARY KEY (`id`) | KEY `account_transactions_branch_id_index` (`branch_id`); KEY `account_transactions_account_id_index` (`account_id`); KEY `account_transactions_supplier_id_index` (`supplier_id`); KEY `account_transactions_customer_id_index` (`customer_id`); KEY `account_transactions_employee_id_index` (`employee_id`); KEY `account_transactions_project_id_index` (`project_id`); KEY `account_transactions_invoice_index` (`invoice`); KEY `idx_account_transactions_warehouse_id` (`warehouse_id`); KEY `idx_account_transactions_backup_branch_id` (`backup_branch_id`) | `id`, `invoice`, `table_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `type`, `debit`, `credit`, `remark`, `supplier_id`, … (+8) |
| `dabit_vouchers` | 16,373 | PRIMARY KEY (`id`) | KEY `idx_dabit_vouchers_warehouse_id` (`warehouse_id`); KEY `idx_dabit_vouchers_backup_branch_id` (`backup_branch_id`) | `id`, `voucher_no`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `supplier_id`, `customer_id`, `employee_id`, `account_id`, `date`, `note`, … (+7) |
| `dabit_voucher_details` | 36,898 | PRIMARY KEY (`id`) | KEY `idx_dabit_voucher_details_warehouse_id` (`warehouse_id`); KEY `idx_dabit_voucher_details_backup_branch_id` (`backup_branch_id`) | `id`, `dabit_voucher_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `code`, `amount`, `created_at`, `updated_at`, `payment_invoice`, `debit`, … (+4) |
| `credit_vouchers` | 1,030 | PRIMARY KEY (`id`) | KEY `idx_credit_vouchers_warehouse_id` (`warehouse_id`); KEY `idx_credit_vouchers_backup_branch_id` (`backup_branch_id`) | `id`, `voucher_no`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `project_id`, `supplier_id`, `customer_id`, `employee_id`, `date`, `note`, … (+7) |
| `credit_voucher_details` | 2,117 | PRIMARY KEY (`id`) | KEY `idx_credit_voucher_details_warehouse_id` (`warehouse_id`); KEY `idx_credit_voucher_details_backup_branch_id` (`backup_branch_id`) | `id`, `credit_voucher_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `code`, `debit`, `credit`, `amount`, `created_at`, `updated_at`, … (+2) |
| `contra_vouchers` | 1,250 | PRIMARY KEY (`id`) | — | `id`, `voucher_no`, `branch_id`, `account_id`, `date`, `note`, `created_by`, `updated_by`, `created_at`, `updated_at` |
| `contra_voucher_details` | 1,246 | PRIMARY KEY (`id`) | — | `id`, `contra_voucher_id`, `branch_id`, `account_id`, `to_account_id`, `code`, `amount`, `created_at`, `updated_at` |
| `journal_vouchers` | 5,719 | PRIMARY KEY (`id`) | KEY `idx_journal_vouchers_warehouse_id` (`warehouse_id`); KEY `idx_journal_vouchers_backup_branch_id` (`backup_branch_id`) | `id`, `voucher_no`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `project_id`, `supplier_id`, `customer_id`, `employee_id`, `date`, `note`, … (+4) |
| `journal_voucher_details` | 19,276 | PRIMARY KEY (`id`) | KEY `idx_journal_voucher_details_warehouse_id` (`warehouse_id`); KEY `idx_journal_voucher_details_backup_branch_id` (`backup_branch_id`) | `id`, `journal_voucher_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `account_id`, `code`, `amount`, `created_at`, `updated_at`, `debit`, `credit`, … (+2) |
| `all_vouchers` | 10 | PRIMARY KEY (`id`) | KEY `all_vouchers_form_id_branch_id_store_id_index` (`form_id`,`branch_id`,`store_id`) | `id`, `date`, `form_id`, `voucher_no`, `branch_id`, `store_id`, `debit`, `credit`, `status_id`, `status`, `updated_by`, `created_by`, … (+4) |

### ইনভেন্টরি / ক্রয় / বিক্রয়

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `products` | 5,670 | PRIMARY KEY (`id`) | KEY `products_category_id_brand_id_unit_id_index` (`category_id`,`brand_id`,`unit_id`) | `id`, `name`, `productCode`, `category_id`, `brand_id`, `unit_id`, `purchases_price`, `sale_price`, `low_stock`, `status`, `updated_by`, `created_by`, … (+6) |
| `product_units` | 32 | PRIMARY KEY (`id`) | — | `id`, `name`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `categories` | 33 | PRIMARY KEY (`id`) | KEY `categories_parent_id_index` (`parent_id`) | `id`, `name`, `parent_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `main_categories` | 0 | PRIMARY KEY (`id`) | KEY `main_categories_parent_id_index` (`parent_id`) | `id`, `name`, `parent_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `brands` | 84 | PRIMARY KEY (`id`) | — | `id`, `name`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `stocks` | 10,044 | PRIMARY KEY (`id`) | KEY `stocks_general_id_branch_id_product_id_index` (`general_id`,`branch_id`,`product_id`); KEY `idx_stocks_backup_branch_id` (`backup_branch_id`) | `id`, `date`, `project_id`, `general_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `product_id`, `unit_price`, `total_price`, `quantity`, `status`, … (+7) |
| `stock_summaries` | 6,205 | PRIMARY KEY (`id`) | KEY `idx_stock_summaries_warehouse_id` (`warehouse_id`); KEY `idx_stock_summaries_backup_branch_id` (`backup_branch_id`); KEY `idx_stock_summaries_project_id` (`project_id`) | `id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`, `purchasetype` |
| `stock_ajdustments` | 50 | PRIMARY KEY (`id`) | KEY `stock_ajdustments_branch_id_index` (`branch_id`); KEY `idx_stock_ajdustments_warehouse_id` (`warehouse_id`); KEY `idx_stock_ajdustments_backup_branch_id` (`backup_branch_id`); KEY `idx_stock_ajdustments_project_id` (`project_id`) | `id`, `date`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `invoice_no`, `subtotal`, `grand_total`, `quantity`, `status`, `updated_by`, … (+10) |
| `stock_ajdustment_detailsts` | 50 | PRIMARY KEY (`id`) | KEY `stock_ajdustment_detailsts_purchases_id_index` (`purchases_id`); KEY `stock_ajdustment_detailsts_branch_id_index` (`branch_id`); KEY `stock_ajdustment_detailsts_category_id_index` (`category_id`); KEY `stock_ajdustment_detailsts_product_id_index` (`product_id`); KEY `idx_stock_ajdustment_detailsts_warehouse_id` (`warehouse_id`); KEY `idx_stock_ajdustment_detailsts_backup_branch_id` (`backup_branch_id`); KEY `idx_stock_ajdustment_detailsts_project_id` (`project_id`) | `id`, `purchases_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `category_id`, `purchase_type`, `product_id`, `date`, `approval_date`, `unit_price`, … (+9) |
| `stock_reports` | 0 | PRIMARY KEY (`id`) | — | `id`, `created_at`, `updated_at` |
| `purchases` | 1,388 | PRIMARY KEY (`id`) | KEY `purchases_supplier_id_branch_id_index` (`supplier_id`,`branch_id`); KEY `idx_purchases_warehouse_id` (`warehouse_id`); KEY `idx_purchases_backup_branch_id` (`backup_branch_id`) | `id`, `date`, `supplier_id`, `type`, `project_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `invoice_no`, `custom_invoice`, `payment_type`, `subtotal`, … (+24) |
| `purchases_details` | 7,760 | PRIMARY KEY (`id`) | KEY `purchases_details_purchases_id_index` (`purchases_id`); KEY `purchases_details_branch_id_index` (`branch_id`); KEY `purchases_details_product_id_index` (`product_id`); KEY `idx_purchases_details_warehouse_id` (`warehouse_id`); KEY `idx_purchases_details_backup_branch_id` (`backup_branch_id`) | `id`, `purchases_id`, `project_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `product_id`, `date`, `unit_price`, `total_price`, `quantity`, `status`, … (+10) |
| `purchase_orders` | 1,081 | PRIMARY KEY (`id`) | — | `id`, `order_date`, `invoice_no`, `supplier_id`, `account_id`, `purchase_requisition_id`, `branch_id`, `project_id`, `advance_payment`, `total_bill`, `status`, `note`, … (+6) |
| `purchase_order_details` | 7,415 | PRIMARY KEY (`id`) | — | `id`, `purchase_order_id`, `category_id`, `product_id`, `supplier_ledger_id`, `qty`, `unit_price`, `total_price`, `branch_id`, `project_id`, `status`, `created_at`, … (+2) |
| `purchase_requisitions` | 1,196 | PRIMARY KEY (`id`) | UNIQUE KEY `purchase_requisitions_invoice_no_unique` (`invoice_no`) | `id`, `invoice_no`, `project_id`, `branch_id`, `date`, `approve_by`, `total_qty`, `total_price`, `approve_at`, `update_by`, `note`, `status`, … (+3) |
| `pr_details` | 8,116 | PRIMARY KEY (`id`) | — | `id`, `pr_id`, `project_id`, `category_id`, `product_id`, `qty`, `remaining_qty`, `unit_price`, `total_price`, `branch_id`, `status`, `created_at`, … (+2) |
| `grns` | 1,029 | PRIMARY KEY (`id`) | — | `id`, `date`, `invoice_no`, `supplier_id`, `purchase_voucher_id`, `project_id`, `branch_id`, `total_price`, `total_qty`, `status`, `note`, `create_by`, … (+3) |
| `grn_details` | 6,978 | PRIMARY KEY (`id`) | — | `id`, `good_rcv_note_id`, `category_id`, `product_id`, `qty`, `approve_qty`, `purchase_voucher`, `unit_price`, `total_price`, `created_at`, `updated_at`, `purchasetype` |
| `sales` | 787 | PRIMARY KEY (`id`) | KEY `idx_sales_warehouse_id` (`warehouse_id`); KEY `idx_sales_backup_branch_id` (`backup_branch_id`); KEY `idx_sales_project_id` (`project_id`) | `id`, `customer_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `invoice_no`, `date`, `qty`, `payment_type`, `sale_type`, `net_total`, … (+17) |
| `sales__details` | 1,007 | PRIMARY KEY (`id`) | KEY `idx_sales__details_warehouse_id` (`warehouse_id`); KEY `idx_sales__details_backup_branch_id` (`backup_branch_id`); KEY `idx_sales__details_project_id` (`project_id`) | `id`, `date`, `sale_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `category_id`, `product_id`, `qty`, `rate`, `price`, … (+10) |
| `sale_returns` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `sale_returns_return_no_unique` (`return_no`); KEY `sale_returns_approved_by_foreign` (`approved_by`); KEY `idx_sale_returns_backup_branch_id` (`backup_branch_id`); KEY `idx_sale_returns_project_id` (`project_id`) | `id`, `return_no`, `original_sale_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `ledger_id`, `sales_person_id`, `return_date`, `return_type`, `grand_total`, … (+10) |
| `sale_return_details` | 0 | PRIMARY KEY (`id`) | KEY `idx_sale_return_details_warehouse_id` (`warehouse_id`); KEY `idx_sale_return_details_backup_branch_id` (`backup_branch_id`); KEY `idx_sale_return_details_project_id` (`project_id`) | `id`, `sale_return_id`, `sale_detail_id`, `product_id`, `returned_qty`, `unit_price`, `vat_percent`, `line_amount`, `condition`, `reason`, `created_at`, `updated_at`, … (+3) |
| `delivery_chalans` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `delivery_chalans_chalan_no_unique` (`chalan_no`) | `id`, `date`, `chalan_no`, `sale_id`, `branch_id`, `customer_id`, `note`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+1) |
| `delivery_chalan_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `chalan_id`, `sale_id`, `category_id`, `product_id`, `delivary_qty`, `updated_by`, `created_by`, `deleted_by`, `created_at`, `updated_at` |
| `productions` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `productions_productioncode_unique` (`productionCode`); KEY `productions_branch_id_product_id_index` (`branch_id`,`product_id`) | `id`, `date`, `productionCode`, `branch_id`, `product_type`, `category_id`, `name`, `brand_id`, `unit_id`, `purchases_price`, `sale_price`, `conversion_id`, … (+7) |
| `product_uses` | 0 | PRIMARY KEY (`id`) | — | `id`, `date`, `invoice_no`, `project_id`, `stock_total`, `use_total`, `create_by`, `update_by`, `deleted_at`, `created_at`, `updated_at` |
| `product_use_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `product_use_id`, `project_id`, `product_id`, `stock_qty`, `use_qty`, `deleted_at`, `created_at`, `updated_at` |
| `product_opening_stocks` | 36 | PRIMARY KEY (`id`) | KEY `idx_product_opening_stocks_warehouse_id` (`warehouse_id`); KEY `idx_product_opening_stocks_backup_branch_id` (`backup_branch_id`) | `id`, `invoice_no`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `created_by`, `date`, `qty`, `total_price`, `narration`, `created_at`, … (+1) |
| `product_opening_stock_details` | 49 | PRIMARY KEY (`id`) | KEY `product_opening_stock_details_branch_id_index` (`branch_id`); KEY `product_opening_stock_details_project_id_index` (`project_id`); KEY `product_opening_stock_details_category_id_index` (`category_id`); KEY `product_opening_stock_details_product_id_index` (`product_id`); KEY `idx_product_opening_stock_details_warehouse_id` (`warehouse_id`); KEY `idx_product_opening_stock_details_backup_branch_id` (`backup_branch_id`) | `id`, `product_opening_stock_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `category_id`, `purchasetype`, `product_id`, `date`, `unit_price`, `total_price`, … (+7) |
| `conversions` | 0 | PRIMARY KEY (`id`) | — | `id`, `title`, `rate`, `status`, `deleted_at`, `created_at`, `updated_at` |
| `adjusts` | 0 | PRIMARY KEY (`id`) | — | `id`, `account_id`, `check_date`, `check_no`, `bank_name`, `customer_id`, `branch_id`, `date`, `expire_date`, `day`, `debit`, `credit`, … (+9) |
| `destroyitems` | 0 | PRIMARY KEY (`id`) | — | `id`, `assetlist_id`, `reason`, `qty`, `destroy_date`, `destroy_by`, `created_at`, `updated_at` |
| `invoices` | 0 | PRIMARY KEY (`id`) | — | `id`, `invoiceCode`, `date`, `project_id`, `branch_id`, `customer_id`, `account_id`, `note`, `profit`, `total_value`, `collect`, `status`, … (+6) |
| `invoice_details` | 10 | PRIMARY KEY (`id`) | KEY `invoice_details_branch_id_store_id_invoice_id_product_id_index` (`branch_id`,`store_id`,`invoice_id`,`product_id`) | `id`, `invoice_id`, `branch_id`, `store_id`, `date`, `product_id`, `unit_pirce`, `total_price`, `status`, `updated_by`, `created_by`, `deleted_by`, … (+3) |
| `inovices` | 0 | PRIMARY KEY (`id`) | KEY `inovices_branch_id_store_id_index` (`branch_id`,`store_id`) | `id`, `date`, `customer_id`, `branch_id`, `store_id`, `invoice_no`, `payment_type`, `subtotal`, `discount`, `grand_total`, `loder`, `transportation`, … (+9) |
| `supplier_select_prices` | 7,499 | PRIMARY KEY (`id`) | — | `id`, `purchase_order_id`, `supplier_id`, `account_id`, `customer_id`, `purchases_price`, `status`, `created_at`, `updated_at` |
| `supplier_sale_prices` | 0 | PRIMARY KEY (`id`) | — | `id`, `product_id`, `supplier_id`, `purchases_price`, `created_at`, `updated_at` |
| `transfers` | 1 | PRIMARY KEY (`id`) | KEY `transfers_from_branch_id_to_branch_id_index` (`from_branch_id`,`to_branch_id`); KEY `idx_transfers_backup_from_branch_id` (`backup_from_branch_id`); KEY `idx_transfers_backup_to_branch_id` (`backup_to_branch_id`) | `id`, `voucher_code`, `from_branch_id`, `to_branch_id`, `from_warehouse_id`, `backup_from_branch_id`, `to_warehouse_id`, `backup_to_branch_id`, `date`, `approve_qty`, `approved_date`, `net_total`, … (+11) |
| `transfer_details` | 1 | PRIMARY KEY (`id`) | KEY `transfer_details_transfer_id_to_branch_id_product_id_index` (`transfer_id`,`to_branch_id`,`product_id`); KEY `idx_transfer_details_backup_from_branch_id` (`backup_from_branch_id`); KEY `idx_transfer_details_backup_to_branch_id` (`backup_to_branch_id`) | `id`, `transfer_id`, `from_branch_id`, `to_branch_id`, `from_warehouse_id`, `backup_from_branch_id`, `to_warehouse_id`, `backup_to_branch_id`, `product_id`, `purchasetype`, `category_id`, `qty`, … (+11) |

### কাস্টমার / সাপ্লায়ার / প্রজেক্ট

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `customers` | 303 | PRIMARY KEY (`id`) | KEY `customers_branch_id_index` (`branch_id`) | `id`, `business_name`, `customergroup_id`, `customer_type`, `branch_id`, `customerCode`, `name`, `email`, `phone`, `bin`, `address`, `city`, … (+12) |
| `customer_groups` | 47 | PRIMARY KEY (`id`) | — | `id`, `name`, `created_at`, `updated_at` |
| `customer_ledgers` | 0 | PRIMARY KEY (`id`) | — | `id`, `date`, `sale_id`, `adjust_id`, `bank_name`, `check_date`, `check_no`, `customer_id`, `branch_id`, `account_id`, `payment_type`, `debit`, … (+7) |
| `customer_openings` | 0 | PRIMARY KEY (`id`) | — | `id`, `created_at`, `updated_at` |
| `suppliers` | 484 | PRIMARY KEY (`id`) | — | `id`, `name`, `supplierCode`, `email`, `phone`, `address`, `specialNumber`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, … (+2) |
| `supplier_ledgers` | 5 | PRIMARY KEY (`id`) | — | `id`, `date`, `purchase_id`, `supplier_id`, `branch_id`, `payment_type`, `account_id`, `debit`, `credit`, `user_id`, `updated_by`, `created_by`, … (+3) |
| `projects` | 53 | PRIMARY KEY (`id`) | UNIQUE KEY `projects_projectcode_unique` (`projectCode`); KEY `projects_name_index` (`name`) | `id`, `name`, `projectCode`, `manager_id`, `branch_id`, `customer_id`, `ledger_id`, `budget`, `estimate_cost`, `estimate_profit`, `actual_costing`, `actual_costing_updated_at`, … (+12) |
| `project_billings` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `project_billings_invoice_no_unique` (`invoice_no`); KEY `project_billings_project_id_index` (`project_id`) | `id`, `project_id`, `invoice_no`, `billing_date`, `milestone_name`, `amount`, `status`, `created_by`, `created_at`, `updated_at` |
| `project_expenses` | 0 | PRIMARY KEY (`id`) | — | `id`, `categorie_id`, `account_id`, `project_id`, `date`, `amount`, `note`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`, `subcategorie_id` |
| `project_money` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `project_money_projectbanancecode_unique` (`projectBananceCode`) | `id`, `project_id`, `account_id`, `projectBananceCode`, `date`, `debit`, `credit`, `note`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, … (+2) |
| `project_poc_snapshots` | 0 | PRIMARY KEY (`id`) | KEY `project_poc_snapshots_project_id_index` (`project_id`); KEY `project_poc_snapshots_period_date_index` (`period_date`) | `id`, `project_id`, `period_date`, `estimate_cost`, `actual_cost_to_date`, `percent_complete`, `recognized_revenue`, `recognized_profit`, `billed_to_date`, `wip_amount`, `journal_voucher_id`, `created_by`, … (+1) |
| `project_requisitions` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `project_requisitions_invoice_no_unique` (`invoice_no`) | `id`, `invoice_no`, `project_id`, `branch_id`, `date`, `total_qty`, `approve_qty`, `unitprice_price`, `total_price`, `approve_at`, `update_by`, `note`, … (+6) |
| `project_requisition_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `project_requisition_id`, `category_id`, `product_id`, `supplier_id`, `qty`, `unit_price`, `total_price`, `status`, `deleted_at`, `created_at`, `updated_at` |
| `project_returns` | 0 | PRIMARY KEY (`id`) | — | `id`, `date`, `invoice_no`, `project_id`, `branch_id`, `stock_total`, `return_total`, `status`, `note`, `create_by`, `update_by`, `deleted_at`, … (+2) |
| `project_return_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `project_return_id`, `product_id`, `stock_qty`, `return_qty`, `deleted_at`, `created_at`, `updated_at` |
| `project_transfers` | 13 | PRIMARY KEY (`id`) | KEY `project_transfers_to_project_id_foreign` (`to_project_id`); KEY `idx_project_transfers_warehouse_id` (`warehouse_id`); KEY `idx_project_transfers_backup_branch_id` (`backup_branch_id`) | `id`, `transfer_type`, `order_date`, `invoice_no`, `purchase_requisition_id`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, `to_project_id`, `status`, `note`, … (+6) |
| `project_transfer_details` | 22 | PRIMARY KEY (`id`) | KEY `idx_project_transfer_details_warehouse_id` (`warehouse_id`); KEY `idx_project_transfer_details_backup_branch_id` (`backup_branch_id`) | `id`, `project_transfer_id`, `category_id`, `product_id`, `qty`, `requested_qty`, `unit_price`, `total_price`, `branch_id`, `warehouse_id`, `backup_branch_id`, `project_id`, … (+4) |
| `cash_reqs` | 644 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `amount`, `reason`, `approve_by`, `status`, `created_at`, `updated_at`, `approval_amount`, `account_id`, `recive_account_id`, `bank_name`, … (+1) |
| `return_deposits` | 0 | PRIMARY KEY (`id`) | — | `id`, `account_id`, `customer_id`, `branch_id`, `date`, `amount`, `payment_type`, `updated_by`, `created_by`, `created_at`, `updated_at` |
| `commissions` | 0 | PRIMARY KEY (`id`) | KEY `commissions_sale_id_foreign` (`sale_id`) | `id`, `employee_id`, `sale_id`, `commission_amount`, `status`, `created_at`, `updated_at` |
| `commission_rules` | 0 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `commission_type`, `fixed_percentage`, `min_amount`, `max_amount`, `percentage`, `created_at`, `updated_at` |
| `expenses` | 0 | PRIMARY KEY (`id`) | — | `id`, `expensecategorie_id`, `expensesubcategorie_id`, `branch_id`, `chartofaccount_id`, `date`, `amount`, `note`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`, … (+1) |
| `expense_categories` | 0 | PRIMARY KEY (`id`) | KEY `expense_categories_parent_id_index` (`parent_id`) | `id`, `name`, `parent_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |

### এইচআরএম (HRM)

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `employees` | 314 | PRIMARY KEY (`id`) | — | `id`, `branch_id`, `name`, `id_card`, `email`, `status`, `auto_checkout`, `user_id`, `dob`, `gender`, `personal_phone`, `office_phone`, … (+30) |
| `attendances` | 37,210 | PRIMARY KEY (`id`) | — | `id`, `emplyee_id`, `branch_id`, `date`, `sign_in`, `sign_out`, `created_at`, `updated_at`, `latitude`, `longitude`, `latitude_out`, `longitude_out` |
| `leave_applications` | 262 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `branch_id`, `apply_date`, `end_date`, `file`, `reason`, `payment_status`, `status`, `created_at`, `updated_at` |
| `holidays` | 6 | PRIMARY KEY (`id`) | UNIQUE KEY `holidays_date_unique` (`date`) | `id`, `date`, `year`, `month`, `title`, `note`, `created_at`, `updated_at` |
| `positions` | 52 | PRIMARY KEY (`id`) | — | `id`, `name`, `details`, `created_at`, `updated_at` |
| `salary_sheets` | 0 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `month`, `salary`, `paid_amount`, `overtime`, `incentive`, `bonus`, `due`, `paid_date`, `type`, `reason`, … (+5) |
| `monthly_payable_salaries` | 0 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `name`, `date`, `basic_salary`, `house_rent`, `medical_allowance`, `travel_allowance`, `food_allowance`, `total_salary`, `daily_rate`, `working_day`, … (+20) |
| `emp_pay_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `pay_sheet_id`, `branch_id`, `employee_id`, `payble_salary`, `amount`, `lone`, `created_at`, `updated_at`, `bonus_id`, `total_bonus` |
| `emp_pay_bonus_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `emp_pay_details_id`, `employee_id`, `monthly_payable_salaries_id`, `bonus_type`, `bonus_amount`, `remarks`, `created_at`, `updated_at` |
| `lones` | 18 | PRIMARY KEY (`id`) | — | `id`, `employee_id`, `branch_id`, `amount`, `file`, `lone_adjustment`, `adjustment_start`, `reason`, `status`, `approved_by`, `note`, `created_at`, … (+1) |
| `loan_details` | 0 | PRIMARY KEY (`id`) | — | `id`, `lone_id`, `employee_id`, `month`, `amount`, `status`, `note`, `created_at`, `updated_at` |
| `awards` | 0 | PRIMARY KEY (`id`) | — | `id`, `name`, `desc`, `gift_item`, `date`, `employee_id`, `award_by`, `created_at`, `updated_at` |
| `education_infos` | 0 | PRIMARY KEY (`id`) | — | `id`, `candidateinfo_id`, `obtain_degree`, `institution`, `cgpa`, `comments`, `created_at`, `updated_at` |
| `work_exp_infos` | 0 | PRIMARY KEY (`id`) | — | `id`, `candidateinfo_id`, `company_name`, `experience`, `supervisor`, `created_at`, `updated_at` |
| `candidate_inforamtions` | 0 | PRIMARY KEY (`id`) | — | `id`, `first_name`, `last_name`, `email`, `phone`, `alternate_phone`, `ssn`, `present_address`, `permanent_address`, `image`, `status`, `created_at`, … (+1) |
| `candidate_selections` | 0 | PRIMARY KEY (`id`) | — | `id`, `candidateinfo_id`, `employee_id`, `position`, `terms`, `created_at`, `updated_at` |
| `candidate_shortlists` | 0 | PRIMARY KEY (`id`) | — | `id`, `candidateinfo_id`, `position`, `interview_date`, `date`, `created_at`, `updated_at` |

### ইউজার / রোল / সেটিংস

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `users` | 249 | PRIMARY KEY (`id`) | UNIQUE KEY `users_phone_unique` (`phone`); UNIQUE KEY `users_email_unique` (`email`) | `id`, `name`, `phone`, `email`, `branch_id`, `email_verified_at`, `password`, `role_id`, `status`, `type`, `updated_by`, `created_by`, … (+6) |
| `user_roles` | 11 | PRIMARY KEY (`id`) | — | `id`, `role_name`, `parent_id`, `navigation_id`, `branch_id`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`, … (+1) |
| `user_manages` | 0 | PRIMARY KEY (`id`) | KEY `user_manages_branch_id_status_id_role_id_index` (`branch_id`,`status_id`,`role_id`) | `id`, `firstname`, `lastname`, `email`, `phone`, `branch_id`, `status_id`, `role_id`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, … (+2) |
| `admin_roles` | 10 | PRIMARY KEY (`id`) | KEY `admin_roles_admin_role_id_admin_id_navigation_id_parent_id_index` (`admin_role_id`,`admin_id`,`navigation_id`,`parent_id`) | `id`, `admin_role_id`, `admin_id`, `navigation_id`, `parent_id`, `test`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `role_accesses` | 253 | PRIMARY KEY (`id`) | KEY `role_accesses_user_id_role_id_index` (`user_id`,`role_id`) | `id`, `user_id`, `role_id`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `navigations` | 0 | PRIMARY KEY (`id`) | KEY `navigations_parent_id_index` (`parent_id`) | `id`, `parent_id`, `label`, `route`, `navigate_status`, `icon`, `object_class`, `extra_attribute`, `active`, `orderBy`, `updated_by`, `created_by`, … (+4) |
| `branches` | 9 | PRIMARY KEY (`id`) | KEY `branches_name_index` (`name`) | `id`, `name`, `branchCode`, `email`, `phone`, `address`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, … (+3) |
| `warehouses` | 3 | PRIMARY KEY (`id`) | UNIQUE KEY `warehouses_warehousecode_unique` (`warehouseCode`) | `id`, `branch_id`, `name`, `warehouseCode`, `email`, `phone`, `address`, `status`, `created_by`, `updated_by`, `deleted_by`, `created_at`, … (+2) |
| `companies` | 1 | PRIMARY KEY (`id`) | — | `id`, `logo`, `favicon`, `invoice_logo`, `autority_signature`, `company_name`, `website`, `phone`, `email`, `address`, `task_identification_number`, `status`, … (+6) |
| `forms` | 10 | PRIMARY KEY (`id`) | — | `id`, `name`, `account_name`, `account_number`, `branch`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `languages` | 10 | PRIMARY KEY (`id`) | — | `id`, `name`, `flug`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `currencies` | 10 | PRIMARY KEY (`id`) | — | `id`, `currency_name`, `currency_symbol`, `exchange_rate`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at` |
| `sms_configurations` | 1 | PRIMARY KEY (`id`) | — | `id`, `provider`, `sender_id`, `api_url`, `api_key`, `username`, `password`, `enabled`, `last_known_balance`, `balance_checked_at`, `created_by`, `updated_by`, … (+2) |
| `sms_settings` | 0 | PRIMARY KEY (`id`) | — | `id`, `api_key`, `api_secret`, `sender_mobile`, `sales`, `purchases`, `payment_voucher`, `receive_voucher`, `status`, `updated_by`, `created_by`, `deleted_by`, … (+3) |
| `sms_templates` | 5 | PRIMARY KEY (`id`) | KEY `sms_templates_type_index` (`type`) | `id`, `name`, `type`, `message`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at` |
| `sms_logs` | 595 | PRIMARY KEY (`id`) | KEY `sms_logs_status_created_at_index` (`status`,`created_at`) | `id`, `sms_template_id`, `recipient_type`, `recipient_name`, `recipient_phone`, `message`, `status`, `provider_response`, `sent_by`, `created_at`, `updated_at` |
| `smtps` | 0 | PRIMARY KEY (`id`) | — | `id`, `protocol`, `smtp_host`, `smtp_port`, `sender_mail`, `password`, `mail_type`, `status`, `updated_by`, `created_by`, `deleted_by`, `deleted_at`, … (+2) |
| `announcements` | 1 | PRIMARY KEY (`id`) | — | `id`, `title`, `description`, `type`, `department_id`, `department`, `expire_date`, `created_by`, `created_at`, `updated_at` |
| `services` | 0 | PRIMARY KEY (`id`) | — | `id`, `serciveCode`, `date`, `branch_id`, `customer_id`, `account_id`, `details`, `amount`, `updated_by`, `created_by`, `deleted_by`, `created_at`, … (+1) |

### লগ / সিস্টেম / বিবিধ

| টেবিল | রো | PK | PK-ছাড়া ইনডেক্স | কলাম (প্রথম ১২টি) |
|---|---|---|---|---|
| `activity_logs` | 626 | PRIMARY KEY (`id`) | KEY `activity_logs_user_id_created_at_index` (`user_id`,`created_at`); KEY `activity_logs_module_action_index` (`module`,`action`); KEY `activity_logs_record_id_record_type_index` (`record_id`,`record_type`); KEY `activity_logs_created_at_index` (`created_at`) | `id`, `user_id`, `user_name`, `action`, `module`, `sub_module`, `record_id`, `record_type`, `description`, `old_values`, `new_values`, `changed_fields`, … (+7) |
| `jobs` | 0 | PRIMARY KEY (`id`) | KEY `jobs_queue_index` (`queue`) | `id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at` |
| `failed_jobs` | 0 | PRIMARY KEY (`id`) | UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) | `id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at` |
| `migrations` | 218 | PRIMARY KEY (`id`) | — | `id`, `migration`, `batch` |
| `password_resets` | 0 | PK নেই | KEY `password_resets_email_index` (`email`) | `email`, `token`, `created_at` |
| `telescope_entries` | 5,827 | PRIMARY KEY (`sequence`) | UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`); KEY `telescope_entries_batch_id_index` (`batch_id`); KEY `telescope_entries_family_hash_index` (`family_hash`); KEY `telescope_entries_created_at_index` (`created_at`); KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`) | `sequence`, `uuid`, `batch_id`, `family_hash`, `should_display_on_index`, `type`, `content`, `created_at` |
| `telescope_entries_tags` | 12 | PRIMARY KEY (`entry_uuid`,`tag`) | KEY `telescope_entries_tags_tag_index` (`tag`) | `entry_uuid`, `tag` |
| `telescope_monitoring` | 0 | PRIMARY KEY (`tag`) | — | `tag` |
| `assets_categories` | 12 | PRIMARY KEY (`id`) | — | `id`, `category_name`, `reason_name`, `category_id`, `status`, `type`, `created_by`, `updated_by`, `created_at`, `updated_at` |
| `assets_lists` | 0 | PRIMARY KEY (`id`) | — | `id`, `name`, `payment_method_id`, `account_id`, `category_asset_id`, `_date`, `qty`, `amount`, `status`, `created_at`, `updated_at`, `payment_account` |
| `assets_warranties` | 0 | PRIMARY KEY (`id`) | — | `id`, `assetlist_id`, `form_date`, `to_date`, `desc`, `type`, `created_at`, `updated_at` |

---

**নোট:** উপরের ১৩৭টি টেবিলের সবগুলোই ডাম্প-ফাইল থেকে সরাসরি গণনা/পার্স করে যাচাই করা; রো-সংখ্যা ডাম্প-ভিত্তিক (লাইভ DB-র সংখ্যা আলাদা হতে পারে — §৫.১ দেখুন)।
## পরিশিষ্ট-ঘ: সব মাইগ্রেশন ফাইল

গণনা: `ls database/migrations` — মোট 219টি ফাইল।

- `2014_10_12_000000_create_users_table.php`
- `2014_10_12_100000_create_password_resets_table.php`
- `2018_08_08_100000_create_telescope_entries_table.php`
- `2019_08_19_000000_create_failed_jobs_table.php`
- `2021_07_09_184904_create_branches_table.php`
- `2021_07_10_175708_create_smtps_table.php`
- `2021_07_10_182649_create_user_roles_table.php`
- `2021_07_10_183325_create_user_manages_table.php`
- `2021_07_10_190051_create_employees_table.php`
- `2021_07_10_191007_create_currencies_table.php`
- `2021_07_10_191932_create_languages_table.php`
- `2021_07_10_201113_create_product_units_table.php`
- `2021_07_10_202034_create_categories_table.php`
- `2021_07_10_202530_create_fiscal_years_table.php`
- `2021_07_10_223439_create_brands_table.php`
- `2021_07_10_223707_create_products_table.php`
- `2021_07_10_230407_create_banks_table.php`
- `2021_07_10_230838_create_openings_table.php`
- `2021_07_10_234601_create_forms_table.php`
- `2021_07_10_235359_create_chart_of_accounts_table.php`
- `2021_07_11_000326_create_all_vouchers_table.php`
- `2021_07_11_001204_create_customers_table.php`
- `2021_07_11_002500_create_inovices_table.php`
- `2021_07_11_003840_create_invoice_details_table.php`
- `2021_07_11_010538_create_transfer_details_table.php`
- `2021_07_11_010942_create_suppliers_table.php`
- `2021_07_11_012240_create_general_ledgers_table.php`
- `2021_07_11_013119_create_purchases_table.php`
- `2021_07_11_014122_create_purchases_details_table.php`
- `2021_07_11_015003_create_stocks_table.php`
- `2021_07_11_015503_create_generals_table.php`
- `2021_07_11_015907_create_general_setups_table.php`
- `2021_07_11_181556_create_role_accesses_table.php`
- `2021_07_17_181234_create_admin_roles_table.php`
- `2021_07_17_181928_create_navigations_table.php`
- `2021_08_01_215713_create_companies_table.php`
- `2021_08_02_221844_create_sms_settings_table.php`
- `2021_08_23_140345_create_accounts_table.php`
- `2021_08_26_072836_create_stock_summaries_table.php`
- `2021_08_28_072026_create_cash_books_table.php`
- `2021_08_29_084143_create_transections_table.php`
- `2021_08_30_041510_create_customer_openings_table.php`
- `2021_08_31_105652_create_transfers_table.php`
- `2021_09_05_122626_create_adjusts_table.php`
- `2021_09_06_112902_create_sales_table.php`
- `2021_09_07_120053_create_sales__details_table.php`
- `2021_09_19_060218_add_columns_to_purchases_table.php`
- `2021_09_19_100235_create_customer_ledgers_table.php`
- `2021_09_19_105717_create_supplier_ledgers_table.php`
- `2021_09_21_100319_create_expenses_table.php`
- `2021_09_25_054145_create_expense_categories_table.php`
- `2021_09_29_044725_create_balance_transfer_logs_table.php`
- `2021_10_06_090629_create_purchase_requisitions_table.php`
- `2021_10_06_101211_create_conversions_table.php`
- `2021_10_06_162405_create_pr_details_table.php`
- `2021_10_07_082053_create_projects_table.php`
- `2021_10_09_044847_create_purchase_orders_table.php`
- `2021_10_09_113159_create_purchase_order_details_table.php`
- `2021_10_10_041823_create_project_money_table.php`
- `2021_10_11_035737_create_stock_ajdustments_table.php`
- `2021_10_11_040616_create_stock_ajdustment_detailsts_table.php`
- `2021_10_11_072226_add_purchase_type_to_purchases.php`
- `2021_10_11_082022_add_purchase_order_id_to_purchases.php`
- `2021_10_11_082828_add_payment_type_to_stock_ajdustments.php`
- `2021_10_11_111531_add_note_to_stock_ajdustments.php`
- `2021_10_12_040517_create_grns_table.php`
- `2021_10_12_063032_create_grn_details_table.php`
- `2021_10_12_105725_add_category_id_to_purchases_details.php`
- `2021_10_14_055329_create_stock_reports_table.php`
- `2021_10_18_101527_create_productions_table.php`
- `2021_10_21_103100_add_type_to_expenses.php`
- `2021_10_22_045458_create_project_expenses_table.php`
- `2021_10_22_081107_add_expense_id_to_project_expenses.php`
- `2021_10_23_132529_create_project_requisitions_table.php`
- `2021_10_23_153012_create_project_requisition_details_table.php`
- `2021_10_25_111515_create_product_uses_table.php`
- `2021_10_25_155030_create_product_use_details_table.php`
- `2021_11_02_044500_create_project_returns_table.php`
- `2021_11_02_055320_create_delivery_chalans_table.php`
- `2021_11_02_055914_create_project_return_details_table.php`
- `2021_11_02_061913_create_delivery_chalan_details_table.php`
- `2021_11_04_065939_add_closing_to_projects.php`
- `2021_11_23_043224_create_services_table.php`
- `2021_12_01_045321_create_invoices_table.php`
- `2021_12_06_103541_create_return_deposits_table.php`
- `2022_09_05_101742_add_sales_detail_column_to_sales__details.php`
- `2022_09_12_042408_create_positions_table.php`
- `2022_09_13_063303_create_salary_sheets_table.php`
- `2022_09_21_131818_create_account_transactions_table.php`
- `2022_11_02_080529_create_attendances_table.php`
- `2022_11_09_104901_create_supplier_sale_prices_table.php`
- `2022_11_28_154157_create_leave_applications_table.php`
- `2022_11_30_104229_create_lones_table.php`
- `2022_12_01_165003_create_emp_pay_details_table.php`
- `2022_12_01_173903_create_monthly_payable_salaries_table.php`
- `2023_07_03_161123_create_assets_categories_table.php`
- `2023_07_04_102637_create_assets_lists_table.php`
- `2023_07_04_144918_create_destroyitems_table.php`
- `2023_07_08_161602_create_financial_years_table.php`
- `2023_07_09_111839_create_awards_table.php`
- `2023_07_09_172448_create_candidate_inforamtions_table.php`
- `2023_07_11_153658_create_candidate_shortlists_table.php`
- `2023_07_12_103545_create_candidate_selections_table.php`
- `2023_07_15_150900_create_work_exp_infos_table.php`
- `2023_07_15_151414_create_education_infos_table.php`
- `2023_07_17_184720_create_dabit_vouchers_table.php`
- `2023_07_17_185124_create_dabit_voucher_details_table.php`
- `2023_07_18_184600_create_credit_vouchers_table.php`
- `2023_07_18_184632_create_credit_voucher_details_table.php`
- `2023_07_19_102143_create_contra_vouchers_table.php`
- `2023_07_19_102403_create_contra_voucher_details_table.php`
- `2023_07_19_114740_create_journal_vouchers_table.php`
- `2023_07_19_114948_create_journal_voucher_details_table.php`
- `2023_07_19_175557_create_assets_warranties_table.php`
- `2023_07_21_113159_create_customer_groups_table.php`
- `2023_07_23_100925_create_project_transfers_table.php`
- `2023_07_23_101149_create_project_transfer_details_table.php`
- `2023_09_05_112006_create_main_categories_table.php`
- `2023_09_05_152022_add_purchasetype_to_purchases_details.php`
- `2023_09_05_165556_add_purchase_to_stock_summaries.php`
- `2023_09_07_041240_add_purchasetype_to_sales__details.php`
- `2023_09_07_054733_add_purchasetype_to_pr_details.php`
- `2023_09_07_064851_add_purchasetype_to_purchase_order_details.php`
- `2023_09_07_073921_add_purchasetype_to_grn_details.php`
- `2023_09_07_114045_add_purchasetype_to_project_transfer_details.php`
- `2023_09_07_181148_add_databaseid_to_user_roles.php`
- `2023_09_10_180456_add_column_to_dabit_voucher_details_table.php`
- `2023_09_12_102954_add_to_column_creditvouchers_table.php`
- `2023_09_14_125926_create_jobs_table.php`
- `2023_09_21_142537_add_to_column_employees_table.php`
- `2023_09_23_125409_add_parent_id_to_products.php`
- `2023_09_23_194306_create_supplier_select_prices_table.php`
- `2023_09_24_172352_add_supplier_id_to_purchases_details.php`
- `2023_09_25_122608_add_paymentinvoice_to_account_transactions.php`
- `2023_09_25_152213_add_payment_invoice_to_dabit_voucher_details.php`
- `2023_09_25_165354_add_payment_invoice_to_credit_voucher_details.php`
- `2023_09_25_185250_add_payment_account_to_assets_lists.php`
- `2023_09_25_200421_add_depreciation_to_chart_of_accounts.php`
- `2023_09_26_141246_add_employee_id_to_users.php`
- `2023_09_27_125816_add_forent_id_to_transections.php`
- `2023_10_15_175742_add_bill_by_bill_payment_to_chart_of_accounts.php`
- `2023_12_19_111014_add_bank_name_to_chart_of_accounts.php`
- `2023_12_19_124723_add_debit_credit_to_dabit_voucher_details.php`
- `2024_01_08_155804_add_debit_credit_to_credit_voucher_details.php`
- `2024_01_10_184355_create_authorizedcapitals_table.php`
- `2024_01_10_184535_create_paidup_capitals_table.php`
- `2024_06_13_112311_create_opening_balances_table.php`
- `2024_06_13_154959_add_option_to_chart_of_accounts.php`
- `2024_07_03_144809_add_dabit_credit_to_journal_voucher_details.php`
- `2024_07_08_175335_add_opening_balance_to_chart_of_accounts_table.php`
- `2024_08_02_164220_add_unique_identifier_to_chart_of_accounts.php`
- `2024_08_25_200154_add_ledger_id_to_sales.php`
- `2024_08_27_223421_add_ledger_id_to_purchases.php`
- `2024_09_01_114659_create_product_opening_stocks_table.php`
- `2024_09_01_141251_create_product_opening_stock_details_table.php`
- `2024_09_04_182141_add_parent_id_to_branches.php`
- `2024_09_05_132909_add_vat_to_sales__details.php`
- `2024_09_05_150257_add_product_to_sales.php`
- `2024_09_19_112359_create_cash_reqs_table.php`
- `2024_09_23_131119_add_custome_invoice_to_sales.php`
- `2024_10_01_193518_add_googlemap_to_attendances.php`
- `2024_10_08_105426_add_approval_amount_to_cash_reqs.php`
- `2025_02_05_152358_create_commission_rules_table.php`
- `2025_02_05_171844_create_commissions_table.php`
- `2025_02_08_154256_add_status_to_employee_table.php`
- `2025_02_08_174641_add_box_to_products_table.php`
- `2025_02_10_164005_add_po_date_to_sales.php`
- `2025_03_22_090433_add_attendance_column_to_employees.php`
- `2025_04_12_152419_add_cost_center_to_dabit_voucher_details.php`
- `2025_04_12_171801_add_costcenter_to_credit_voucher_details.php`
- `2025_04_12_174846_add_costcenter_to_journal_voucher_details.php`
- `2025_04_13_182205_add_check_number_to_dabit_voucher_details.php`
- `2026_03_16_120411_add_ledger_id_to_projects_table.php`
- `2026_03_29_104947_add_account_id_to_purchase_orders_table.php`
- `2026_03_29_113744_add_account_id_to_supplier_select_prices_table.php`
- `2026_04_04_093932_add_auto_checkout_to_employees_table.php`
- `2026_04_07_115422_create_holidays_table.php`
- `2026_04_12_154429_add_missing_columns_to_monthly_payable_salaries_table.php`
- `2026_04_15_170718_add_approved_by_and_note_to_lones_table.php`
- `2026_04_16_130345_add_loan_type_to_account_transactions.php`
- `2026_04_16_170954_add_adjustment_start_to_lones_table.php`
- `2026_04_18_095538_create_loan_details_table.php`
- `2026_04_18_164519_add_recive_account_id_to_cash_reqs_table.php`
- `2026_04_19_162554_add_adjustment_columns_to_monthly_payable_salaries.php`
- `2026_04_21_112216_create_emp_pay_bonus_details_table.php`
- `2026_04_21_153542_add_bonus_name_to_emp_pay_details_table.php`
- `2026_04_26_112059_add_supplier_lager_to_purchase_order_details_table.php`
- `2026_04_26_151446_add_ledger_id_to_purchases_details_table.php`
- `2026_05_02_110901_add_party_type_to_account_transactions_table.php`
- `2026_05_05_130253_create_activity_logs_table.php`
- `2026_05_07_151334_fix_stocks_status_column_safe.php`
- `2026_05_09_151615_add_invoice_no_to_stocks_table.php`
- `2026_05_12_130910_create_check_registers_table.php`
- `2026_06_28_103205_fix_opening_balance_precision_in_chart_of_accounts.php`
- `2026_07_18_140147_add_actual_costing_to_projects_table.php`
- `2026_07_23_110542_alter_projects_table_for_poc.php`
- `2026_07_23_110742_create_project_poc_snapshots_table.php`
- `2026_07_23_110836_create_project_billings_table.php`
- `2026_07_23_110936_add_actual_costing_updated_at_to_projects_table.php`
- `2026_07_30_101306_create_announcements_table.php`
- `2026_07_30_134622_add_department_to_announcements_table.php`
- `2026_08_10_112123_add_transfer_type_to_project_transfers_table.php`
- `2026_08_11_094836_add_remining_qty_to_pr_details_table.php`
- `2026_08_11_095406_add_add_requested_qty_to_project_transfer_details_table.php`
- `2026_08_15_102007_create_warehouses_table.php`
- `2026_08_15_102851_add_warehouse_id_to_branches_table.php`
- `2026_08_15_111514_add_warehouse_id_to_stocks_table.php`
- `2026_08_20_161652_add_purchase_type_to_stock_ajdustment_detailsts_table.php`
- `2026_08_24_094559_create_sms_configurations_table.php`
- `2026_08_24_094721_create_sms_templates_table.php`
- `2026_08_24_095128_create_sms_logs_table.php`
- `2026_08_25_104415_add_sales_person_id_to_sales_table.php`
- `2026_08_27_154825_add_purchase_type_to_transfer_details_table.php`
- `2026_09_01_150546_change_price_columns_in_grn_details_table.php`
- `2026_09_13_152631_create_sale_returns_table.php`
- `2026_09_13_152646_create_sale_return_details_table.php`
- `2026_09_13_162452_add_approval_columns_to_sale_returns_table.php`
- `2026_09_20_131954_add_warehouse_columns_to_transfers_and_transfer_details.php`
- `2026_10_18_164858_add_voucher_date_to_account_transactions_table.php`
