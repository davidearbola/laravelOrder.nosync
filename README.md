# Order Tracker - Sistema Gestionale Ordini

Sistema web per la gestione di ordini e clienti, sviluppato con Laravel 12 e Vue 3.

## 📋 Indice

- [Descrizione](#descrizione)
- [Tecnologie Utilizzate](#tecnologie-utilizzate)
- [Requisiti di Sistema](#requisiti-di-sistema)
- [Installazione](#installazione)
- [Configurazione](#configurazione)
- [Esecuzione Test](#esecuzione-test)
- [Utilizzo](#utilizzo)
- [Scelte Tecniche](#scelte-tecniche)

## 📖 Descrizione

Order Tracker è un'applicazione web per uso interno che permette di gestire ordini e clienti di un piccolo negozio online. Il sistema include:

- **Gestione Clienti**: CRUD completo con soft delete e ripristino
- **Gestione Prodotti**: Catalogo con gestione giacenze
- **Gestione Ordini**: Creazione ordini multi-prodotto con carrello
- **Controllo Stock**: Riduzione automatica giacenze e validazione disponibilità
- **Transizioni Stato**: Workflow ordini con validazione transizioni
- **Autenticazione**: Accesso sicuro con Laravel Sanctum
- **API RESTful**: Endpoint JSON per tutte le funzionalità
- **UI Responsive**: Interfaccia mobile-first con Tailwind CSS

## 🛠 Tecnologie Utilizzate

### Backend
- **Framework**: Laravel 12
- **Database**: MySQL 8.0
- **Autenticazione**: Laravel Sanctum
- **Testing**: PHPUnit
- **Pattern**: Repository Pattern
- **PHP**: 8.2+

### Frontend
- **Framework**: Vue 3 (Composition API)
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios
- **Styling**: Tailwind CSS (via CDN)
- **Build Tool**: Vite

## 💻 Requisiti di Sistema

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- npm >= 9.x
- MySQL >= 8.0
- Git

## 🚀 Installazione

### 1. Clona il Repository

```bash
git clone <repository-url>
cd laravelOrder.nosync
```

### 2. Setup Backend (Laravel)

```bash
cd api

# Installa dipendenze
composer install

# Copia file di configurazione
cp .env.example .env

# Genera chiave applicazione
php artisan key:generate
```

### 3. Configura Database

Modifica il file `api/.env` con le tue credenziali MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=laravel_order_db
DB_USERNAME=root
DB_PASSWORD=root
```

### 4. Esegui Migration e Seeding

```bash
# Crea le tabelle
php artisan migrate

# Popola con dati di esempio
php artisan db:seed
```

Questo creerà:
- 1 utente test (email: `test@example.com`, password: `password`)
- 13 clienti di esempio (3 fissi + 10 random)
- 20 prodotti con stock (5 fissi + 15 random)
- 10 ordini di esempio

### 5. Setup Frontend (Vue 3)

```bash
cd ../client

# Installa dipendenze
npm install
```

## ⚙️ Configurazione

### Backend

Il file `api/.env` contiene tutte le configurazioni. Le principali:

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

# Database
DB_CONNECTION=mysql
DB_DATABASE=laravel_order_db

# CORS (già configurato per frontend)
```

### Frontend

Il file `client/src/api/axios.js` contiene la configurazione API:

```javascript
baseURL: 'http://localhost:8000/api'
```

## 🧪 Esecuzione Test

### Test Backend (PHPUnit)

```bash
cd api

# Esegui tutti i test
php artisan test

# Solo Feature Tests
php artisan test --testsuite=Feature

# Solo Unit Tests
php artisan test --testsuite=Unit

# Con output dettagliato
php artisan test --verbose
```

**Test implementati:**
- `OrderCreationTest` (Feature Test): Test creazione ordini con stock management (3 test)
- `OrderStatusTransitionTest` (Unit Test): Test transizioni stato e validazioni (6 test)

Tutti i test usano database SQLite in-memory, i dati di produzione non vengono toccati.

## 🎮 Utilizzo

### Avvia il Backend

```bash
cd api
php artisan serve
```

L'API sarà disponibile su: `http://localhost:8000`

### Avvia il Frontend

```bash
cd client
npm run dev
```

L'applicazione sarà disponibile su: `http://localhost:5173`

### Credenziali di Accesso

**Utente di test:**
- Email: `test@example.com`
- Password: `password`

### Workflow Applicazione

1. **Login**: Accedi con le credenziali
2. **Dashboard**: Visualizza statistiche ordini
3. **Clienti**: Gestisci anagrafica clienti
4. **Prodotti**: Gestisci catalogo e giacenze
5. **Ordini**:
   - Crea nuovi ordini selezionando cliente e prodotti
   - Visualizza dettagli ordini esistenti
   - Aggiorna stato ordini
   - Filtra per stato o numero ordine

## 🎯 Scelte Tecniche

### Pattern Architetturali

#### 1. Repository Pattern
Ho scelto il Repository Pattern per separare la logica di business dalle query al database:

**Vantaggi:**
- Codice più testabile e manutenibile
- Controller snelli e focalizzati sul routing
- Logica business centralizzata e riutilizzabile

**Implementazione:**
```
app/Repositories/
├── CustomerRepository.php
├── ProductRepository.php
└── OrderRepository.php
```

**Nota:** Lo `stock_status` dei prodotti (available/low/out_of_stock) viene calcolato dinamicamente tramite il metodo `getStockStatus()` nel [ProductResource](api/app/Http/Resources/ProductResource.php:24) in base alla quantità disponibile.

#### 2. Form Request Validation
Validazione centralizzata tramite Form Requests invece di validare nei controller:

**Vantaggi:**
- Validazione riutilizzabile
- Controller più puliti
- Messaggi di errore consistenti
- Facile aggiungere validazioni personalizzate

#### 3. API Resources
Trasformazione delle response tramite API Resources:

**Vantaggi:**
- Response JSON consistenti
- Controllo preciso dei dati esposti
- Nasconde campi sensibili (es. password)
- Include relazioni in modo controllato

### Scelte Funzionali

#### Stock Management
Ho implementato la gestione giacenze magazzino (opzionale nel PDF) perché:

- Aggiunge valore reale al sistema
- Dimostra competenza con transazioni database
- Previene errori (es. vendere prodotti non disponibili)
- Include validazioni con `DB::transaction()` per garantire atomicità delle operazioni

**Implementazione:**
- Stock decrementato alla creazione ordine
- Validazione disponibilità prima di creare ordine
- Rollback automatico in caso di errore
- Stock ripristinato su annullamento (potenziale implementazione futura)

#### Ordini Multi-Prodotto
Invece di un prodotto singolo per ordine, ho implementato un sistema completo:

- Tabella `order_items` per dettaglio prodotti
- Carrello nel frontend per aggiungere più prodotti
- Totale calcolato automaticamente dalla somma dei subtotals
- UI simile a e-commerce reali

#### Soft Delete Clienti
Implementato soft delete invece di hard delete:

**Vantaggi:**
- Mantiene storico ordini anche se cliente eliminato
- Possibilità di ripristino
- Conforme al requisito "cliente eliminato non elimina ordini"
- Filtro per visualizzare/nascondere clienti eliminati

#### Numero Ordine Auto-generato
Formato: `ORD-YYYY-NNNN` (es. ORD-2026-0001)

**Implementazione:**
- Numerazione progressiva per anno
- `lockForUpdate()` previene race conditions (accessi simultanei che genererebbero numeri duplicati)
- Reset automatico ogni anno

### Frontend

#### Vue 3 Composition API
Scelto invece di Options API per:
- Codice più conciso e leggibile
- Logica riutilizzabile e organizzata
- Pattern più moderno e performante

#### Tailwind CSS via CDN
Scelto CDN invece di build process per:
- Setup più veloce
- Nessun conflitto PostCSS
- File CSS minificato e cached
- Sufficiente per questo progetto

#### Pinia per State Management
Scelto invece di Vuex per:
- API più semplice e intuitiva
- Compatibilità nativa con Composition API
- Store modulari con meno codice ripetitivo

### Database

#### MySQL invece di SQLite
Scelto MySQL per produzione perché:
- Più adatto a produzione
- Migliori performance con dataset grandi
- Support transazioni più robusto
- Già configurato nel setup locale

**SQLite usato solo per test** (via `:memory:`)
