# Bank Task API

Laravel API project for handling incoming bank transactions in two formats (`PayTech` and `Acme`) with two ingestion modes:
- `ingestion = 1`: process immediately and store in `transactions`.
- `ingestion = 0`: queue in `pending_transactions` and ingest later when re-enabled.

## Overview
- A bank endpoint generates mock transaction strings.
- The system receives transactions (webhook or local simulation), parses them, and stores them.
- There is an endpoint to toggle ingestion on/off.
- When ingestion is turned back on, pending batches are moved automatically to `transactions`.
- There is an endpoint to generate transfer XML.

## Data Model
### `settings`
- Contains the `ingestion` key (seeded by `SettingSeeder`, default `1`).

### `transactions`
- `amount`
- `refrance_key` (unique)
- `date`
- `notes` (JSON)

### `pending_transactions`
- `data` (JSON batch of raw transaction strings)

## API Endpoints
Base URL: `http://127.0.0.1:8000/api`

### 1) Generate bank transactions
- `GET /get-transactions`
- Query params:
- `type`: `PayTech` or `Acme` (default `PayTech`)
- `count`: number of transactions (optional)
- `webhook`: `1` or `0` (default `1`)

Examples:
- Generate and process directly:
`GET /api/get-transactions?type=PayTech&count=5&webhook=1`
- Generate and return raw data only:
`GET /api/get-transactions?type=Acme&count=3&webhook=0`

### 2) Receive transactions webhook
- `POST /transactions-webhook`
- Body:
```json
{
  "data": [
    "20260224100,00#1234567812345678#note/debt payment march/internal_reference/A462JE81",
    "20260224//1234567812345678//250,50"
  ]
}
```
- Response:
```json
{ "message": "Transactions received successfully" }
```

### 3) Toggle ingestion
- `PATCH /change-ingestion`
- Flips ingestion between `0` and `1`.
- On switching to `1`, all rows from `pending_transactions` are processed and removed.

### 4) Generate transfer XML
- `POST /sending-money`
- Body:
```json
{
  "reference": "REF-1001",
  "date": "2026-02-24",
  "amount": 1500.75,
  "currency": "USD",
  "sender_account": "ACC-001",
  "receiver_bank_code": "BANK-US-01",
  "receiver_account": "ACC-999",
  "beneficiary_name": "John Doe",
  "notes": ["invoice 44", "priority"],
  "payment_type": 1,
  "charge_details": "OUR"
}
```
- Response: XML string.

## Supported Raw Formats
### PayTech
Pattern:
`YYYYMMDD + amount(with comma) + # + reference + # + key/value pairs separated by /`

Example:
`20260224100,00#1234567812345678#note/debt payment march/internal_reference/A462JE81`

### Acme
Pattern:
`YYYYMMDD//reference//amount(with comma)`

Example:
`20260224//1234567812345678//250,50`

## Quick Test Flow
1. Make sure `ingestion` is `1`.
2. Call `GET /api/get-transactions?type=PayTech&count=5&webhook=1`.
3. Check `transactions` table.
4. Call `PATCH /api/change-ingestion` to disable ingestion.
5. Call generation endpoint again and verify rows go to `pending_transactions`.
6. Call `PATCH /api/change-ingestion` again to re-enable and ingest pending rows.

## Notes
- Field name is `refrance_key` in code and DB (not `reference_key`).
- Duplicate references are ignored using `insertOrIgnore` + unique key.
- No authentication is currently applied to API endpoints.
