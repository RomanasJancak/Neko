# Neko – Paskutinės Mylios Logistikos ir Siuntų Paskirstymo Valdymo Platforma

**Neko** yra gamyboje naudojama žiniatinklio pagrįsta operacijų ir atsiskaitymų platforma kurjeriams ir dviračių pristatymo bendrovėms. Ji valdo užsakymų kūrimą, kasdieninį paskirstymą, darbuotojų/dviračių priskyrimą, dinaminę kainodarą ir automatizuotą sąskaitų faktūrų generavimą vienoje integruotoje sistemoje.

---

## Turinys

- [Apžvalga](#apžvalga)
- [Pagrindinės Funkcijos](#pagrindinės-funkcijos)
- [Technologijų Paketas](#technologijų-paketas)
- [Projekto Struktūra](#projekto-struktūra)
- [Diegimas ir Instaliacija](#diegimas-ir-instaliacija)
- [Pagrindiniai Verslo Procesai](#pagrindiniai-verslo-procesai)
- [API Dokumentacija](#api-dokumentacija)
- [Duomenų Bazės Modeliai](#duomenų-bazės-modeliai)
- [Konfigūracija ir Nustatymai](#konfigūracija-ir-nustatymai)
- [Saugumas ir Leidimai](#saugumas-ir-leidimai)
- [Atsarginės kopijos ir Atkūrimas](#atsarginės-kopijos-ir-atkūrimas)
- [Kūrimas](#kūrimas)
- [Licencija](#licencija)

---

## Apžvalga

Neko skirta logistikos operacijų komandose, kurioms reikia:

1. **Fiksavimo ir pristatymo užsakymų sekimo** iš kelių klientų su sudėtingais maršrutais.
2. **Realaus laiko darbo paskirstymo ir priskyrimo** kurjeriams ir dviračiams naudojant drag-and-drop sąsają.
3. **Darbuotojų pajėgumo planavimo** naudojant mėnesinį/savaitinį darbo krūvio kalendorių.
4. **Dinaminės kainos nustatymo** atsižvelgiant į atstumą, svorį, laiką ir kliento taisykles.
5. **Automatizuoto sąskaitų faktūrų generavimo ir siuntimo** su nepakeičiamomis kopijomis ir PDF/el. pašto palaikymu.
6. **Leidimų ir audito žurnalų valdymo** atitiktims ir atsakomybei.

### Tiksliniai Vartotojai

- **Dispečeriai**: Realaus laiko užduočių priskyrimas ir valdymas.
- **Vadovai**: Darbuotojų planavimas, pajėgumo valdymas, našumo stebėjimas.
- **Finansai**: Sąskaitų faktūrų valdymas, mokėjimų sekimas, atsiskaitymo suderinimas.
- **Kurjeriai** (per API): Prieiga prie darbo krūvio ir pristatymo informacijos.

---

## Pagrindinės Funkcijos

### 1. Užsakymų ir Pristatymo Valdymas
- Pristatymo užsakymų kūrimas, atnaujinimas ir sekimas su lanksčiais užduočių tipais (pasiėmimas, pristatymas, grąžinimas, pasirinktinė).
- Kelių pakuočių tipų palaikymas su kiekio ribomis ir papildomais paslaugomis.
- Šablonai greitam pasikartojančių maršrutų dubliavimui.
- Laukų fiksavimas netyčiam pakeitimams išvengti vykdymo metu.
- Pastabos ir veiklos sekimas kiekvienai užduočiai.

### 2. Paskirstymo Sąsaja
- Dienos peržiūra rodanti nepaskirstytus užsakymus ir kurjerio stulpelius.
- Drag-and-drop užduočių priskyrimas kurjeriams.
- Realaus laiko kurjerio prieinamumas ir darbo krūvio vizualizacija.
- Būsenos spalvų kodavimas pagal užduoties būseną ir tipą.
- Kopijavimo/masinio apdorojimo operacijos greitam priskyrimui.

### 3. Darbo Krūvio Planavimas
- Mėnesinis ir savaitinis kalendoriaus peržiūra pajėgumo planavimui.
- Kurjerio ir dviračio priskyrimas dienai su pajėgumo procentu.
- Darbo krūvio įrašų pridėjimas/redagavimas/šalinimas iš kalendoriaus.
- Laisvų kurjerių ir laisvų dviračių suradimas pasirinktai dienai.

### 4. Kainodaros Variklis
- **Atstumo pagrįsta kaina** su ribomis ir žingsnine apskaita.
- **Svorio paremtos papildomos paslaugos**.
- **Laiko paremtos premijos** (ankstus rytas, vėlas vakaras, savaitgalis).
- **Sekmadienis ir šventadienių priemokos** (iš anksto nustatytos UK 2024–2028).
- **Pakuočių tipo kaina** su baziniu ir maksimaliu kiekio slenksčiu.
- **Tos pačios dienos grąžinimas** su premium tarifais.
- **Rankiniai kainos koregavimai** kiekvienai užduočiai.
- Nuolatinis kainos suskaidymas saugomas `job_prices` lentelėje auditui.

### 5. Atsiskaitymai ir Sąskaitos Faktūros
- **Automatinis sąskaitų faktūrų kūrimas**: Kai užduotis pasiekia būsenos id 14 (atsiskaitytina), ji automatiškai susieta su sekančio pirmadienio sąskaita faktūra kliento.
- **Savaitiniai sąskaitos okno langai**: Sąskaitos apima pirmadienis–sekmadienis pristatymo laikotarpius.
- **Sąskaitos eilučių agregavimas**: Kelios užduotys sugrupuotos į vieną sąskaitos eilutę su datų diapazono aprašymu.
- **Momentinės kopijos versijų kūrimas**: Kiekviena sugeneruota sąskaita PDF/el. pašte fiksina nepakeičiamą PVM, sumų ir užduočių momentinę kopiją.
- **Šablonais paremti el. laiškai**: Tinkintinas sąskaitos el. laiško tema ir kūnas kiekvienam klientui, su automatiniais PDF priedais.
- **Sąskaitos fiksavimas**: Po nustatyto skaičiaus dienų (numatytasis: 1 diena), sąskaitos yra fiksavamos, jei vartotojas nėra administratorius/superadministratorius.

### 6. Kliento ir Kainos Konfigūracija
- **Kelių adresų klientai** su atskirais pasiėmimo, sąskaitos ir sąskaitos faktūros pristatymo adresais.
- **Atstumo ir svorio taisyklių valdymas** kiekvienam klientui.
- **Pakuočių tipo priskyrimas** (kurie pakuočių tipai kiekvienam klientui prieinami).
- **Papildomų paslaugų taisyklių priskyrimas** (priemokos, taikomos konkretiems klientams).
- **Sąskaitos faktūros el. laiško šablonai** kiekvienam klientui su kintamųjų pakeitimais (`:client_name`, `:invoice_number`, `:invoice_date`, `:invoice_total`).

### 7. Vaidmenimis Paremta Prieiga (RBAC)
- **Vaidmenų ir leidimų valdymas** naudojant Spatie Laravel Permission.
- **Grynai apibrėžti leidimai**: `permission-view`, `permission-edit`, `setting-view`, `setting-edit`, `setting-create`.
- **Administratoriaus/SuperAdministratoriaus lygiai** su padidintos privilegijomis (ID 1–2).
- **Privilegijų padidinimo saugos** leidimų atnaujinimuose (vartotojai gali priskirti tik tų leidimų, kuriuos turi).

### 8. Nustatymai ir Tinklinimas
- **Globalūs nustatymai**: PVM norma, sąskaitos fiksavimo amžius (dienos).
- **Vartotojui specifiniai nustatymai**: Užduoties sąrašo rūšiavimo stulpelis/tvarka, pristatymo paieškos lauko nuostatos.
- Hierarchiniai nustatymai: Vartotojo nepaisymai grįžta prie globalių numatytųjų nustatymų.
- Plokščias JSON saugojimas su taškinio žymėjimo raktais lengvam pasigraudimui.

### 9. Duomenų Integritas ir Atsarginės Kopijos
- **SQL dump/restore sąsaja** su pasirinktiniu lentelės eksportu.
- **Apsauga nuo netyčio duomenų praradimo**: `users` lentelė yra atskirta nuo dump/restore.
- **Suskaidytas failų eksportas** didelėms duomenų bazėms (numatytasis 1 MB skaičiavimas).
- **Migracija sutvarkyta lentelės eksporta** užsienio raktų apribojimams respektuoti.

### 10. API ir Integracijos
- **Sanctum žetono autentifikacija** mobiliesiems ir trečiųjų šalių programoms.
- **RESTful galūnės** užduotims, vartotojams, darbo krūviams, vartotojo dienos būsenoms, vartotojo būsenoms.
- **OpenAPI/Swagger dokumentacija** su detaliomis pavyzdžiai.
- **Dviračio priskyrimo galūnė** su nuliniu argumentų vykdymo šablomu.

---

## Technologijų Paketas

### Serveris
- **Struktūra**: Laravel 10.10
- **Kalba**: PHP 8.3+
- **Duomenų bazė**: MySQL/MariaDB
- **Autentifikacija**: Laravel Sanctum (API žetonai)
- **Autorizacija**: Spatie Laravel Permission 5.10

### Kliento Pusė
- **Kūrimo Įrankis**: Vite 6.3.5
- **CSS Struktūra**: Bootstrap 5.2.3
- **DOM Manipuliacija**: Vanilla JavaScript + Axios 1.1.2
- **Šablonavimas**: Blade (Laravel žiūros variklis)
- **Ikosai**: Bootstrap Icons, FontAwesome

### Dokumentacija ir Įrankiai
- **API Dokumentai**: L5 Swagger (OpenAPI 3.0) + Scramble
- **PDF Generavimas**: Laravel DomPDF 3.1
- **CSV Tvarkymas**: League CSV 9.11
- **Google Maps Integracija**: AlexPechkarev Google Maps 10.0

---

## Projekto Struktūra

```
Neko/
├── app/
│   ├── Console/               # Artisan komandos
│   ├── Exceptions/            # Pasirinktinės išimtys
│   ├── Http/
│   │   ├── Controllers/       # Žiniatinklio valdikliai (CUD operacijos)
│   │   ├── Controllers/Api/   # REST API valdikliai
│   │   ├── Requests/          # Formos patvirtinimo taisykles
│   │   ├── Middleware/        # Autentifikacija, CORS, leidimai
│   │   └── Kernel.php         # HTTP tarpininkūjimo krūva
│   ├── Mail/                  # El. laiško šablonai (InvoiceSendMail)
│   ├── Models/                # Eloquent modeliai (Job, Invoice, User, ir kt.)
│   ├── Observers/             # Modelio gyvenimo ciklo nuorodų (JobObserver, PickupTaskObserver)
│   ├── Policies/              # Autorizacijos politika
│   ├── Providers/             # Paslaugos konteinėrio registracijos
│   ├── Services/              # Verslo logika
│   │   ├── InvoicePricingService.php
│   │   ├── InvoiceSnapshotService.php
│   │   ├── JobPriceCalculator.php
│   │   ├── JobPriceSnapshotService.php
│   │   ├── BikeAssignmentService.php
│   │   ├── BackupService.php
│   │   └── SettingsService.php
│   ├── Settings/              # Vartotojo nustatymų apibrėžimai
│   └── View/                  # Žiūrų sudarytuvai
├── config/                    # Konfigūracijos failai
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── mail.php
│   ├── permission.php         # Spatie leidimų konfigūracija
│   └── l5-swagger.php         # Swagger/OpenAPI konfigūracija
├── database/
│   ├── factories/             # Modelio fabrikų sedinimui/testavimui
│   ├── migrations/            # Schemos apibrėžimai
│   └── seeders/               # Duomenų bazės sedintukai
├── public/
│   ├── files/                 # Statinių išteklių (logotipai, ikonos)
│   └── build/                 # Vite sukompiluoti ištekliai
├── resources/
│   ├── css/                   # SCSS stiliaus lapai
│   ├── js/                    # JavaScript įėjimo taškai ir moduliai
│   │   ├── app.js
│   │   ├── routes.js          # Priekinės dalies maršruto žemėlapis
│   │   ├── job/
│   │   ├── task/
│   │   ├── client/
│   │   └── address/
│   ├── views/                 # Blade šablonai
│   │   ├── job/
│   │   ├── invoice/
│   │   ├── workload/
│   │   ├── day/
│   │   ├── client/
│   │   ├── role/
│   │   ├── setting/
│   │   └── layouts/
│   └── files/                 # Naudotojo įkelti failai
├── routes/
│   ├── api.php                # REST API maršrutai (Sanctum apsaugoti)
│   ├── web.php                # Žiniatinklio maršrutai (autentifikacijos tarpininkis)
│   ├── channels.php           # Transliacijos kanalai
│   └── console.php            # Artisan komandos
├── storage/
│   ├── app/                   # Programos sugeneruoti failai
│   ├── logs/                  # Žurnalai
│   └── api-docs/              # Sugeneruota API dokumentacija
├── tests/                     # PHPUnit testai
│   ├── Feature/
│   └── Unit/
├── composer.json              # PHP priklausomybės
├── package.json               # Node priklausomybės
├── vite.config.js             # Vite paketo konfigūracija
├── phpunit.xml                # PHPUnit konfigūracija
└── README.md                  # Šis failas
```

---

## Diegimas ir Instaliacija

### Reikalavimai

- PHP 8.3 ar aukštesnė versija
- MySQL 5.7+ arba MariaDB 10.2+
- Composer
- Node.js 16+ ir npm/yarn
- Git

### Žingsniai

1. **Klonuoti saugyklą:**
   ```bash
   git clone <repository-url> Neko
   cd Neko
   ```

2. **Instaliuoti PHP priklausomybes:**
   ```bash
   composer install
   ```

3. **Instaliuoti Node priklausomybes:**
   ```bash
   npm install
   ```

4. **Sukurti aplinkos failą:**
   ```bash
   cp .env.example .env
   ```

5. **Sugeneruoti programos raktą:**
   ```bash
   php artisan key:generate
   ```

6. **Konfigūruoti duomenų bazę `.env` dalyje:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=neko
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Paleisti migracijų:**
   ```bash
   php artisan migrate
   ```

8. **Pasėti pavyzdinių duomenų (pasirinktinai):**
   ```bash
   php artisan db:seed
   ```

9. **Sukurti priekinės dalies išteklius:**
   ```bash
   npm run build
   # Kūrimo metu su auto-perkrautimu:
   npm run dev
   ```

10. **Pradėti programą:**
    ```bash
    php artisan serve
    # Eiti į http://localhost:8000
    ```

### Pasirinktinai: API Žetono Generavimas

Generuoti Sanctum žetoną API prieigai:

```bash
php artisan tinker
>>> $user = User::first();
>>> $token = $user->createToken('AppToken')->plainTextToken;
>>> echo $token;
```

Arba apsilankyti `/get-token` (tik neprodukcijos metu), jei norite generuoti ir parodyti žetoną.

---

## Pagrindiniai Verslo Procesai

### Procesas 1: Pristatymo Užduoties Kūrimas ir Paskirstymas

1. **Vadovas sukuria užduotį** per `/jobs/create` su:
   - Klientu (atsiskaitymo subjektu)
   - Pasiėmimo vieta ir laiko langu
   - Vienu ar keliais pristatymo adresais su pakuočių tipais ir laiko langais
   - Pasirinktinai grąžinimo užduotimi
   - Pasirinktinai papildomais paslaugomis (maisto tvarkymu, ir kt.)

2. **Užduotis išsaugoma** ir būsena nustatoma (pvz., "nepaskirstyta").

3. **Sistema apskaičiuoja kainą** pagal atstumą, svorį, laiką ir kliento taisykles.

4. **Dispečeris peržiūri paskirstymo sąsają** (`/day/dashboard/{date}`) ir mato užduotį nepaskirstytame stulpelyje.

5. **Dispečeris įtempia užduotį į kurjerio stulpelį** ir užduotis yra iš naujo priskirta.

6. **Užduoties būsena atnaujinama** (pvz., "priskirta", "vykdoma", "baigta").

### Procesas 2: Automatinis Sąskaitos Faktūros Generavimas Užbaigus Užduotį

1. **Užduotis baigiama** ir būsena pakeičiama į id 14 (atsiskaitytina).

2. **JobObserver aptiko būsenos pakeitimą** ir suaktyvina `assignToInvoice()`.

3. **Sistema apskaičiuoja sekantį pirmadienį** iš užduoties datos ir ieško esamos sąskaitos faktūros su ta data kliento.

4. **Jei nėra sąskaitos faktūros**, ji kuriama su:
   - Sąskaitos data = sekantis pirmadienis
   - Dėl datos = sekantis pirmadienis + 30 dienų
   - Sąskaitos numeris = automatiškai sugeneruotas
   - Būsena = "juodraštis"

5. **Užduotis susieta** su sąskaitos eilute toje sąskaitoje.

6. **InvoicePricingService perskaičiuoja** sąskaitos eilutę ir sąskaitos faktūros sumas.

7. **Finansų vartotojas peržiūri sąskaitą** `/invoices`, peržiūri PDF, siunčia el. laišką klientui.

8. **Siunčiant, momentinė kopija** sukurta fiksuojanti PVM normą, sumas ir visas eilutes toje akimirkoj.

9. **Po sąskaitos fiksavimo datos** (numatytasis 1 diena), sąskaita yra fiksuota, nebent vartotojas yra administratorius.

### Procesas 3: Kurjerio Pajėgumo Valdymas

1. **Vadovas atidaro darbo krūvio kalendorių** `/workload/calendar?view=monthly`.

2. **Vadovas pasirenka dieną** ir spaudžia "Pridėti darbininką".

3. **Modalinis langas atsidaro** kurjerio ir dviračio priskyrimo su pajėgumo procento.

4. **Sistema išsaugo darbo krūvio įrašą** susieja kurjerį, dviratį ir dieną.

5. **Paskirstymo sąsaja** dabar rodo kurjerį kaip prieinamą tai dienai.

6. **Vadovas gali redaguoti** pajėgumą arba trinti darbo krūvio įrašą.

---

## API Dokumentacija

Visos API galūnės reikalauja Sanctum žetono autentifikacijos per `Authorization: Bearer {token}` žymę.

### Pagrindinis URL
```
/api
```

### Pagrindinės Galūnės

#### Užduotys
- `GET /api/jobs` — Sąrašas užduočių su išdalijimu ir filtravimais
- `POST /api/jobs` — Sukurti naują užduotį
- `GET /api/jobs/{job}` — Gauti užduoties informaciją
- `PUT /api/jobs/{job}` — Atnaujinti užduotį
- `DELETE /api/jobs/{job}` — Ištrinti užduotį

#### Darbo Krūviai
- `GET /api/workloads` — Sąrašas darbo krūvio
- `POST /api/workloads` — Sukurti darbo krūvį
- `GET /api/workloads/calendar` — Gauti kalendoriaus peržiūrą (mėnesinis arba savaitinis)
- `PATCH /api/workloads/{workload}/bike` — Priskirti arba sukeisti dviratį

#### Vartotojai
- `GET /api/users` — Sąrašas vartotojų
- `POST /api/users` — Sukurti vartotoją
- `GET /api/users/{user}` — Gauti vartotojo informaciją
- `PUT /api/users/{user}` — Atnaujinti vartotoją
- `DELETE /api/users/{user}` — Ištrinti vartotoją
- `GET /api/users/{user}/workloads` — Gauti vartotojo darbo krūvius
- `GET /api/users/{user}/workloads/{date}` — Gauti darbo krūvį konkrečiai dienai

#### Vartotojo Dienos Būsenos
- `GET /api/user-day-statuses` — Sąrašas dieninių būsenų
- `POST /api/user-day-statuses` — Priskirti būseną vartotojui dienai
- `GET /api/user-day-statuses/{user_day_status}` — Gauti būsenos įrašą
- `PUT /api/user-day-statuses/{user_day_status}` — Atnaujinti būseną
- `DELETE /api/user-day-statuses/{user_day_status}` — Ištrinti būseną

### OpenAPI/Swagger

Visą API dokumentaciją rasite:
```
/api/documentation  (L5 Swagger)
/api/docs           (Scramble alternatyva)
```

Sugeneruoti Swagger dokumentus:
```bash
php artisan l5-swagger:generate
```

---

## Duomenų Bazės Modeliai

### Pagrindiniai Modeliai

| Modelis | Tikslas |
|---------|---------|
| `Job` | Pristatymo užsakymas su pasiėmimo/pristatymo/grąžinimo užduotimis |
| `Task` | Atskira pasiėmimo, pristatymo, grąžinimo arba pasirinktinė užduotis |
| `Package` | Pristatymo paketas su tipu, kiekiu, adresu, laiko langu |
| `Pickuptask` | Pasiėmimo užduoties detalės (adresas, laikas, kliento vardas) |
| `ReturnTask` | Grąžinimo užduotis prekių pasigraudimui |
| `CustomTask` | Ad-hoc užduotis (nesusijusi su klientu) |
| `InvoiceItem` | Eilutės elementas grupuojantis kelias užduotis klientui |
| `Invoice` | Savaitinė sąskaita su automatiškai sugeneruotu numeriu ir datomis |
| `InvoiceSnapshot` | Nepakeičiama sąskaitos versijos nuotrauka (PVM, sumos, užduotys) |
| `JobPrice` | Nuolatinė kainos suskaidymo eilutė (atstumas, svoris, laikas, ir kt.) |
| `JobTemplate` | Naudojamas pristatymo maršrutų šablonas |
| `Client` | Pristatymo paslaugų klientas su kainų taisyklėmis |
| `User` | Sistemos vartotojas (dispečeris, vadovas, finansai, kurjeris) |
| `Workload` | Kurjerio ir dviračio priskyrimas specifinei dienai |
| `Day` | Kalendoriaus data skirta darbo krūviui/užduočių grupavimui |
| `Bike` | Transporto priemonė (būsena: turima/užimta) |
| `PackageType` | Pristatymo paketo klasifikacija (vokas, mažas dėžė, didelė dėžė, padėklas, ir kt.) |
| `AddOnRule` | Priemokos taisyklė (maisto tvarkymas, dydis ir kt.) |
| `Status` | Užduoties/užduočių būsena (nepaskirstyta, priskirta, vykdoma, baigta, ir kt.) |

### Santykiai ir Apribojimai

- `Job` turi daug `Task`ų.
- `Task` turi vieną `Pickuptask`, `Package`, `ReturnTask`, arba `CustomTask`.
- `Job` priklauso `Client`ui (per `clientToBill_id`).
- `InvoiceItem` turi daug `Job`ų.
- `Invoice` turi daug `InvoiceItem`ų.
- `Workload` susieja `User` ir `Bike` su `Day`.
- `AddOn` yra polimorfinė ir pritvirtinama prie `Job` arba `Package`.

---

## Konfigūracija ir Nustatymai

### Aplinkos Kintamieji

```env
APP_NAME="Neko"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://neko.example.com

DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=neko
DB_USERNAME=neko_user
DB_PASSWORD=secure_password

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@neko.example.com

SANCTUM_STATEFUL_DOMAINS=api.neko.example.com
```

### Globalūs Nustatymai (Duomenų Bazė)

Globalūs nustatymai saugomi `settings` lentelėje ir pasiekiami per `SettingsService`:

- `global.vatRate` — PVM procentas (numatytasis: 0.2 arba 20%)
- `global.invoiceLockDays` — Dienos po sąskaitos siuntimo, kada fiksuoti (numatytasis: 1)

### Vartotojo Nustatymai (Duomenų Bazė)

Vartotojui specifiniai nustatymai saugomi `user_settings` lentelėje:

- `models.job.view.index.sortColumn` — Užduoties sąrašo rūšiavimo laukas (id, clientName, date)
- `models.job.view.index.sortOrder` — Didėjimas arba mažėjimas
- `models.job.view.index.dropOffSearchFields` — JSON masyvas paieškojamų pristatymo laukų

---

## Saugumas ir Leidimai

### Autentifikacija

- **Žiniatinklis**: Laravel sesijos pagrįsta autentifikacija su `/login` ir `/register` maršrutais.
- **API**: Sanctum žetono pagrįsta autentifikacija su Bearer žetonais.

### Autorizacija

Leidimai valdomi per Spatie Laravel Permission. Pagrindiniai leidimai:

| Leidimas | Priedėlis |
|----------|----------|
| `permission-view` | Peržiūrėti leidimų matricą |
| `permission-edit` | Redaguoti vaidmenų leidimus |
| `setting-view` | Peržiūrėti globalius nustatymus |
| `setting-edit` | Redaguoti globalius nustatymus |
| `setting-create` | Kurti SQL atsargines kopijas |

### Privilegijų Padidinimo Saugos

In `RoleController::updatePermissions()`, vartotojai negali priskirti leidimų, kurių jie jau neturi. Tai neleido horizontaliams arba vertikaliems privilegijų padidinama.

### Sąskaitos Fiksavimas

Sąskaitos fiksuojamos po `global.invoiceLockDays` pasibaigus nuo `sent_at`, nebent vartotojas yra administratorius/superadministratorius:

```php
public function isLockedForUser(?User $user): bool
{
    if (!$this->isCompletedAndPastInvoiceLockDate()) return false;
    if ($user && $user->isAdminOrSuperAdmin()) return false;
    return true;
}
```

---

## Atsarginės Kopijos ir Atkūrimas

### Sukurti SQL Dump'ą

1. Apsilankyti `/setting/sql-dump` (reikalauja `can:setting-view`).
2. Pasirinkti lentelės, kurias įtraukti/atimti (vartotojų lentelė yra apribota).
3. Spausti "Create Dump" suskaidytiems SQL failams generuoti.
4. Failai saugomi `/storage/app/backups/sql/`.

### Atkurti iš SQL Dump'o

1. Eiti į `/setting/sql-dump` (reikalauja `can:setting-edit`).
2. Įkelti anksčiau išeksportuotą SQL failą.
3. Sistema patvirtina failą ir atkuria lentelės (praleisdama apribotą vartotojų lentelę).
4. Rodo vykdytų pareiškimų skaičių.

### Programinis Backup'as

```bash
php artisan backup:create
# arba naudoti paslaugą tiesiogiai kodą
```

---

## Kūrimas

### Paleisti Testus

```bash
php artisan test
# Su pasikliautinais
php artisan test --coverage
```

### Kodo Stilius

Formatuoti kodą su Laravel Pint:

```bash
composer run pint
```

### Duomenų Bazės Migracijos

Sukurti naują migraciją:

```bash
php artisan make:migration create_table_name
```

Paleisti nebaigtas migracijų:

```bash
php artisan migrate
```

Grąžinti paskutinį žingsnį:

```bash
php artisan migrate:rollback
```

### Tinker (REPL)

Interaktyvus PHP apvalkalas su programos kontekstu:

```bash
php artisan tinker
>>> $job = Job::first();
>>> $job->price();
```

### Derinimas

Įjungti debug režimą `.env`:

```
APP_DEBUG=true
```

Tikrinti žurnalus `/storage/logs/`.

---

## Licencija

Šis projektas yra savistabi programinė įranga. Neautorizuotas kopijavimas, modifikavimas ir platinimas yra draudžiami.

Dėl licencijos paklausimų, susisiekite su kūrimo komanda.

---

## Pagalba

Dėl problemų, funkcijos pasiūlymų arba bendros pagalbos:

1. Patikrinti šiame README esamą dokumentaciją.
2. Peržiūrėti API dokumentus `/api/documentation`.
3. Konsultuoti kodą pavyzdžiams.
4. Susisiekti su kūrimo komanda dėl verslo pagalbos.

---

## Žurnalas Pakeitimų

Žr. `CHANGELOG.md` versijos istorijos ir laužymo pakeitimų.

---

**Paskutinį kartą atnaujinta**: Gegužė 2026  
**Versija**: 1.0.0  
**Tvarkytojų**: Kūrimo Komanda
