# API Documentation - Order Tracker

Documentazione completa degli endpoint API REST del sistema Order Tracker.

## 📋 Indice

- [Base URL](#base-url)
- [Autenticazione](#autenticazione)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Endpoint](#endpoint)
  - [Authentication](#authentication)
  - [Customers](#customers)
  - [Products](#products)
  - [Orders](#orders)

## 🌐 Base URL

```
http://localhost:8000/api
```

## 🔐 Autenticazione

L'API usa **Laravel Sanctum** per l'autenticazione basata su token.

### Flow di Autenticazione

1. **Login**: Ottieni token tramite `/login`
2. **Richieste**: Includi token nell'header `Authorization: Bearer {token}`
3. **Logout**: Revoca token tramite `/logout`

### Header Richiesti

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer {your-token-here}
```

## 📦 Response Format

Tutte le response sono in formato JSON.

### Success Response (Single Resource)

```json
{
  "data": {
    "id": 1,
    "name": "Mario Rossi",
    "email": "mario@example.com",
    ...
  }
}
```

### Success Response (Collection)

```json
{
  "data": [
    { "id": 1, "name": "Item 1", ... },
    { "id": 2, "name": "Item 2", ... }
  ],
  "links": {
    "first": "http://localhost:8000/api/customers?page=1",
    "last": "http://localhost:8000/api/customers?page=10",
    "prev": null,
    "next": "http://localhost:8000/api/customers?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

## ⚠️ Error Handling

### Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### HTTP Status Codes

| Code | Significato |
|------|-------------|
| 200  | OK - Richiesta completata con successo |
| 201  | Created - Risorsa creata con successo |
| 204  | No Content - Richiesta completata senza contenuto |
| 400  | Bad Request - Richiesta malformata |
| 401  | Unauthorized - Autenticazione richiesta o fallita |
| 403  | Forbidden - Non autorizzato ad accedere |
| 404  | Not Found - Risorsa non trovata |
| 422  | Unprocessable Entity - Errori di validazione |
| 500  | Internal Server Error - Errore del server |

---

## 📚 Endpoint

## Authentication

### Register User

Crea un nuovo utente operatore.

```http
POST /api/register
```

**Request Body:**

```json
{
  "name": "Mario Rossi",
  "email": "mario@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201 Created):**

```json
{
  "user": {
    "id": 1,
    "name": "Mario Rossi",
    "email": "mario@example.com",
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-10T10:00:00.000000Z"
  },
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz"
}
```

### Login

Autentica un utente e ottieni il token.

```http
POST /api/login
```

**Request Body:**

```json
{
  "email": "test@example.com",
  "password": "password"
}
```

**Response (200 OK):**

```json
{
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-10T10:00:00.000000Z"
  },
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz"
}
```

**Error Response (401 Unauthorized):**

```json
{
  "message": "Le credenziali fornite non sono corrette"
}
```

### Logout

Revoca il token corrente.

```http
POST /api/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**

```json
{
  "message": "Logout effettuato con successo"
}
```

### Get Current User

Ottieni i dati dell'utente autenticato.

```http
GET /api/user
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**

```json
{
  "id": 1,
  "name": "Test User",
  "email": "test@example.com",
  "created_at": "2026-01-10T10:00:00.000000Z",
  "updated_at": "2026-01-10T10:00:00.000000Z"
}
```

---

## Customers

Tutti gli endpoint richiedono autenticazione.

### List Customers

Ottieni lista paginata di clienti.

```http
GET /api/customers
```

**Esempio:**

```http
GET /api/customers?page=1&search=mario&show_deleted=with
```

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Mario Rossi",
      "email": "mario@example.com",
      "address": "Via Roma 1, Milano",
      "phone": "+39 333 1234567",
      "is_deleted": false,
      "orders_count": 5,
      "created_at": "2026-01-10T10:00:00.000000Z",
      "updated_at": "2026-01-10T10:00:00.000000Z",
      "deleted_at": null
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Get Customer

Ottieni dettagli di un singolo cliente.

```http
GET /api/customers/{id}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "name": "Mario Rossi",
    "email": "mario@example.com",
    "address": "Via Roma 1, Milano",
    "phone": "+39 333 1234567",
    "is_deleted": false,
    "orders_count": 5,
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-10T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### Create Customer

Crea un nuovo cliente.

```http
POST /api/customers
```

**Request Body:**

```json
{
  "name": "Luigi Verdi",
  "email": "luigi@example.com",
  "address": "Corso Italia 10, Roma",
  "phone": "+39 333 7654321"
}
```

**Validation Rules:**

- `name`: required, string, max:255
- `email`: required, email, unique:customers
- `address`: required, string, max:500
- `phone`: required, string, max:20

**Response (201 Created):**

```json
{
  "data": {
    "id": 2,
    "name": "Luigi Verdi",
    "email": "luigi@example.com",
    "address": "Corso Italia 10, Roma",
    "phone": "+39 333 7654321",
    "is_deleted": false,
    "created_at": "2026-01-10T11:00:00.000000Z",
    "updated_at": "2026-01-10T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### Update Customer

Aggiorna un cliente esistente.

```http
PUT /api/customers/{id}
```

**Request Body:**

```json
{
  "name": "Luigi Verdi",
  "email": "luigi.verdi@example.com",
  "address": "Viale Europa 20, Roma",
  "phone": "+39 333 9876543"
}
```

**Validation Rules:**

- `name`: required, string, max:255
- `email`: required, email, unique:customers,email,{id}
- `address`: required, string, max:500
- `phone`: required, string, max:20

**Response (200 OK):**

```json
{
  "data": {
    "id": 2,
    "name": "Luigi Verdi",
    "email": "luigi.verdi@example.com",
    "address": "Viale Europa 20, Roma",
    "phone": "+39 333 9876543",
    "is_deleted": false,
    "created_at": "2026-01-10T11:00:00.000000Z",
    "updated_at": "2026-01-10T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

### Delete Customer (Soft Delete)

Elimina un cliente (soft delete).

```http
DELETE /api/customers/{id}
```

**Business Rules:**

- Il cliente può essere eliminato solo se **non ha ordini attivi** (in_attesa o in_lavorazione)
- Gli ordini completati/annullati non impediscono l'eliminazione
- Il cliente viene marcato come eliminato ma rimane nel database

**Response (200 OK):**

```json
{
  "message": "Cliente eliminato con successo"
}
```

**Error Response (422 Unprocessable Entity):**

```json
{
  "message": "Non è possibile eliminare il cliente. Ha 3 ordini attivi (in attesa o in lavorazione)."
}
```

### Restore Customer

Ripristina un cliente eliminato.

```http
POST /api/customers/{id}/restore
```

**Response (200 OK):**

```json
{
  "message": "Cliente ripristinato con successo",
  "data": {
    "id": 1,
    "name": "Mario Rossi",
    "email": "mario@example.com",
    "is_deleted": false,
    ...
  }
}
```

---

## Products

### List Products

Ottieni lista paginata di prodotti.

```http
GET /api/products?page=1
```

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "code": "PROD-001",
      "name": "Laptop Dell XPS 15",
      "description": "Laptop professionale 15 pollici",
      "price": 1299.99,
      "stock_quantity": 15,
      "stock_status": "available",
      "created_at": "2026-01-10T10:00:00.000000Z",
      "updated_at": "2026-01-10T10:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

**Stock Status Values:**

- `available`: Stock > 10
- `low`: Stock tra 1 e 10
- `out_of_stock`: Stock = 0

### Get Product

Ottieni dettagli di un singolo prodotto.

```http
GET /api/products/{id}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "code": "PROD-001",
    "name": "Laptop Dell XPS 15",
    "description": "Laptop professionale 15 pollici",
    "price": 1299.99,
    "stock_quantity": 15,
    "stock_status": "available",
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-10T10:00:00.000000Z"
  }
}
```

### Create Product

Crea un nuovo prodotto.

```http
POST /api/products
```

**Request Body:**

```json
{
  "code": "PROD-002",
  "name": "iPhone 15 Pro",
  "description": "Smartphone Apple ultima generazione",
  "price": 1299.00,
  "stock_quantity": 25
}
```

**Validation Rules:**

- `code`: required, string, unique:products, max:50
- `name`: required, string, max:255
- `description`: nullable, string
- `price`: required, numeric, min:0, max:999999.99
- `stock_quantity`: required, integer, min:0

**Response (201 Created):**

```json
{
  "data": {
    "id": 2,
    "code": "PROD-002",
    "name": "iPhone 15 Pro",
    "description": "Smartphone Apple ultima generazione",
    "price": 1299.00,
    "stock_quantity": 25,
    "stock_status": "available",
    "created_at": "2026-01-10T11:00:00.000000Z",
    "updated_at": "2026-01-10T11:00:00.000000Z"
  }
}
```

### Update Product

Aggiorna un prodotto esistente.

```http
PUT /api/products/{id}
```

**Request Body:**

```json
{
  "code": "PROD-002",
  "name": "iPhone 15 Pro Max",
  "description": "Smartphone Apple ultima generazione - versione Max",
  "price": 1499.00,
  "stock_quantity": 30
}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 2,
    "code": "PROD-002",
    "name": "iPhone 15 Pro Max",
    "description": "Smartphone Apple ultima generazione - versione Max",
    "price": 1499.00,
    "stock_quantity": 30,
    "stock_status": "available",
    "created_at": "2026-01-10T11:00:00.000000Z",
    "updated_at": "2026-01-10T12:00:00.000000Z"
  }
}
```

### Delete Product

Elimina un prodotto.

```http
DELETE /api/products/{id}
```

**Response (200 OK):**

```json
{
  "message": "Prodotto eliminato con successo"
}
```

---

## Orders

### List Orders

Ottieni lista paginata di ordini.

```http
GET /api/orders?page=1&status=in_attesa&search=ORD-2026
```

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-2026-0001",
      "customer_id": 1,
      "customer_name": "Mario Rossi",
      "date": "2026-01-10",
      "total_amount": 2599.98,
      "status": "in_lavorazione",
      "status_label": "In Lavorazione",
      "status_color": "blue",
      "can_be_modified": true,
      "created_at": "2026-01-10T10:00:00.000000Z",
      "updated_at": "2026-01-10T11:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Get Order

Ottieni dettagli completi di un ordine con i suoi prodotti.

```http
GET /api/orders/{id}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "order_number": "ORD-2026-0001",
    "customer_id": 1,
    "customer": {
      "id": 1,
      "name": "Mario Rossi",
      "email": "mario@example.com",
      "address": "Via Roma 1, Milano",
      "phone": "+39 333 1234567",
      "is_deleted": false
    },
    "customer_name": "Mario Rossi",
    "date": "2026-01-10",
    "total_amount": 2599.98,
    "status": "in_lavorazione",
    "status_label": "In Lavorazione",
    "status_color": "blue",
    "can_be_modified": true,
    "items": [
      {
        "id": 1,
        "order_id": 1,
        "product_id": 1,
        "product": {
          "id": 1,
          "code": "PROD-001",
          "name": "Laptop Dell XPS 15",
          "price": 1299.99
        },
        "quantity": 2,
        "unit_price": 1299.99,
        "subtotal": 2599.98
      }
    ],
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-10T11:00:00.000000Z"
  }
}
```

**Note:**
- Se il cliente è stato eliminato, `customer_name` sarà "Cliente eliminato"
- `customer` sarà `null` o conterrà i dati con `is_deleted: true`

### Create Order

Crea un nuovo ordine con prodotti.

```http
POST /api/orders
```

**Request Body:**

```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ]
}
```

**Validation Rules:**

- `customer_id`: required, exists:customers,id
- `items`: required, array, min:1
- `items.*.product_id`: required, exists:products,id
- `items.*.quantity`: required, integer, min:1

**Business Rules:**

1. Verifica disponibilità stock per ogni prodotto
2. Se stock insufficiente, rollback completo
3. Numero ordine generato automaticamente
4. Stock decrementato per ogni prodotto
5. Totale calcolato automaticamente
6. Stato iniziale: `in_attesa`

**Response (201 Created):**

```json
{
  "data": {
    "id": 5,
    "order_number": "ORD-2026-0005",
    "customer_id": 1,
    "customer_name": "Mario Rossi",
    "date": "2026-01-10",
    "total_amount": 3898.97,
    "status": "in_attesa",
    "status_label": "In Attesa",
    "status_color": "gray",
    "can_be_modified": true,
    "items": [
      {
        "id": 10,
        "product_id": 1,
        "quantity": 2,
        "unit_price": 1299.99,
        "subtotal": 2599.98,
        "product": {
          "id": 1,
          "name": "Laptop Dell XPS 15",
          "code": "PROD-001"
        }
      },
      {
        "id": 11,
        "product_id": 2,
        "quantity": 1,
        "unit_price": 1298.99,
        "subtotal": 1298.99,
        "product": {
          "id": 2,
          "name": "iPhone 15 Pro",
          "code": "PROD-002"
        }
      }
    ],
    "created_at": "2026-01-10T14:00:00.000000Z",
    "updated_at": "2026-01-10T14:00:00.000000Z"
  }
}
```

**Error Response - Stock Insufficiente (422):**

```json
{
  "message": "Stock insufficiente per Laptop Dell XPS 15. Disponibili: 5, Richiesti: 10"
}
```

### Update Order

Aggiorna un ordine esistente.

```http
PUT /api/orders/{id}
```

**Request Body:**

```json
{
  "customer_id": 1,
  "status": "in_lavorazione",
  "items": [
    {
      "product_id": 1,
      "quantity": 3
    }
  ]
}
```

**Business Rules:**

1. **Ordini completati/annullati NON modificabili**
2. Transizioni stato devono essere valide:
   - `in_attesa` → `in_lavorazione` ✅
   - `in_attesa` → `annullato` ✅
   - `in_lavorazione` → `completato` ✅
   - `in_lavorazione` → `annullato` ✅
   - `completato` → `in_attesa` ❌
   - `annullato` → qualsiasi ❌

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "order_number": "ORD-2026-0001",
    "status": "in_lavorazione",
    "status_label": "In Lavorazione",
    "can_be_modified": true,
    ...
  }
}
```

**Error Response - Ordine Non Modificabile (422):**

```json
{
  "message": "Non è possibile modificare un ordine completato o annullato"
}
```

**Error Response - Transizione Invalida (422):**

```json
{
  "message": "Transizione non valida da completato a in_attesa"
}
```

### Delete Order

Elimina un ordine (solo se modificabile).

```http
DELETE /api/orders/{id}
```

**Business Rules:**

- Solo ordini con stato `in_attesa` o `in_lavorazione` possono essere eliminati
- Ordini `completato` o `annullato` non possono essere eliminati

**Response (200 OK):**

```json
{
  "message": "Order deleted successfully"
}
```

**Error Response (422):**

```json
{
  "message": "L'ordine non può essere eliminato"
}
```
