# TCG Inventory & Local Tournament Manager

A full‑stack **Laravel 11** application designed for local game stores, tournament organizers, and TCG hobby communities.  
It provides end‑to‑end management for **inventory**, **purchase orders**, **players**, and **Swiss‑style tournaments** — all running in a fully containerized environment.

---

## 🚀 Quick Start

Spin up the entire stack in one go:

```bash
docker-compose up -d --build
docker exec tcg_app composer install
docker exec tcg_app php artisan key:generate
docker exec tcg_app php artisan migrate --seed
# -> http://localhost:8080
# -> admin@tcgshop.local / password

---
## 📦 Project Structure

| Layer | Files | Highlights |
| --- | --- | --- |
| **Models** | 14 | Rich relationships, computed attributes (margin%, carton profit, stock status) |
| **Services** | 4 | Swiss pairing engine, DCI standings, margin calculator, weighted‑avg cost inventory |
| **Controllers** | 10 | Dashboard, Inventory, Products, Suppliers, POs, Players, Tournaments, Rounds, Pairings, Reports |
| **Migrations** | 11 | Users, suppliers, card sets, products, purchase orders, inventory, players, tournaments, registrations, rounds, pairings |
| **Seeders** | 6 | Preloaded users, suppliers, 19 card sets, 18 real TCG products, 24 players, sample tournaments |
| **Blade Views** | 30 | Dark sidebar UI, CRUD pages, inline stock adjust, live PO totals (Alpine.js) |
| **Docker** | 5 | Nginx, PHP‑FPM, MySQL, Redis, Mailpit |

Core Features
📊 Inventory & Costing
Weighted‑average cost recalculation on every PO receipt

Inline stock adjustments

Real TCG product data seeded

Carton‑level margin analysis:

Cost per unit

MSRP

Discount scenarios (5%–20%)

Break‑even pricing

🎮 Tournament Engine
Swiss pairings with:

Rematch avoidance

Automatic bye assignment

Live standings

Full DCI‑style tiebreakers:

OMW%

GWP%

OGW%

🧾 Purchase Orders
Live PO total calculations (Alpine.js)

Atomic stock updates on receipt

Supplier management

⚡ Performance & Infrastructure
Redis caching for standings & sessions

Queue‑ready architecture

Fully containerized dev environment

🖥️ Tech Stack
Laravel 11

MySQL 8

Redis 7

Nginx + PHP‑FPM

Docker Compose

Alpine.js

Mailpit

🧪 Seed Data Included
3 users (admin + staff)

4 suppliers

19 card sets

18 real TCG products with stock

24 players

Sample tournaments with rounds & pairings

Perfect for demos, testing, or bootstrapping a real store setup.

📈 Roadmap
[ ] Match result entry UI

[ ] Player ELO / ranking system

[ ] Multi‑store support

[ ] POS integration

[ ] Webhooks for Discord announcements