# TCG Inventory & Local Tournament Manager

A specialized retail and event management system for local card shops, built with PHP 8.2 / Laravel 11, MySQL 8, and Redis.

---

## Features

### Inventory Management
- **Product catalog** — booster boxes, cartons, ETBs, singles, accessories
- **Multi-game support** — Pokémon, MTG, Yu-Gi-Oh!, One Piece, Lorcana, Flesh & Blood, Digimon
- **Carton cost vs MSRP analysis** — per-unit cost, margin %, carton profit, price-point scenarios
- **Real-time stock levels** — weighted-average cost, low-stock alerts, reorder thresholds
- **Stock adjustments** — purchase receipts, manual counts, damage writes, sales
- **Transaction history** — full audit trail per product

### Purchasing
- **Purchase orders** — draft → ordered → partial → received workflow
- **Carton-level ordering** — tracks quantity ordered vs received per line
- **Live margin hints** — shows margin % as you add items to an order
- **Supplier management** — contacts, account numbers, payment terms, credit limits
- **One-click receive** — updates inventory and records transactions atomically

### Tournament Management
- **Swiss pairing engine** — correct round-1 random, subsequent rounds pair by points, no rematches, automatic byes
- **Full tiebreaker standings** — Match Points → OMW% → GWP% → OGW% (MTG/Pokémon DCI rules)
- **Multi-format support** — Standard, Expanded, Modern, Legacy, Draft, Sealed, Commander, Pre-Release
- **Player registration** — paid/unpaid tracking, deck name, mid-tournament drops
- **Round management** — start round, enter results per table, complete round
- **Redis-cached standings** — invalidated on every result change, fast renders

### Player Directory
- Player profiles with DCI/ID number
- Tournament history across all events
- Preferred game tracking

### Reports
- **Margin report** — all products sorted by margin, carton-level profitability
- **Inventory valuation** — cost vs retail value by game, unrealised margin
- **Tournament report** — revenue by game, avg players, full history

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2, Laravel 11 |
| Database | MySQL 8.0 |
| Cache / Queue / Session | Redis 7 |
| Frontend | Blade, Tailwind CSS (CDN), Alpine.js |
| Container | Docker + Docker Compose |
| Web server | Nginx (Alpine) |

---

## Quick Start (Docker)

```bash
# 1. Clone and enter the project
cd TCG-Inventory-Local-Tournament-Manager

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker-compose up -d --build

# 4. Install PHP dependencies
docker exec tcg_app composer install

# 5. Generate application key
docker exec tcg_app php artisan key:generate

# 6. Run migrations and seed sample data
docker exec tcg_app php artisan migrate --seed

# 7. Open your browser
open http://localhost:8080
```

**Default credentials:**
| Email | Password | Role |
|---|---|---|
| `admin@tcgshop.local` | `password` | Admin |
| `manager@tcgshop.local` | `password` | Manager |
| `staff@tcgshop.local` | `password` | Staff |

---

## Local Development (without Docker)

```bash
# Requirements: PHP 8.2+, Composer, MySQL 8, Redis

composer install

cp .env.example .env
# Edit .env — set DB_HOST=127.0.0.1, REDIS_HOST=127.0.0.1

php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## Project Structure

```
app/
├── Console/Commands/CheckLowStock.php   # Artisan: daily reorder alert
├── Http/Controllers/                    # 10 resource controllers
├── Http/Requests/                       # Validated form requests
├── Models/                              # 14 Eloquent models
├── Providers/AppServiceProvider.php
└── Services/
    ├── SwissPairingService.php          # Swiss algorithm with rematch avoidance
    ├── StandingsService.php             # DCI tiebreaker calculation + Redis cache
    ├── MarginCalculatorService.php      # Carton cost / MSRP / price scenarios
    └── InventoryService.php             # Stock in/out with weighted-average cost

database/
├── migrations/                          # 11 migration files
└── seeders/                             # 6 seeders with real TCG product data

resources/views/
├── layouts/app.blade.php                # Dark-theme sidebar layout
├── dashboard/                           # KPI cards, low-stock alerts, recent orders
├── inventory/                           # Stock levels + inline adjust + transactions
├── products/                            # CRUD + margin analysis page
├── purchase-orders/                     # Create with live totals, receive workflow
├── suppliers/                           # Supplier CRUD
├── players/                             # Player profiles + history
├── tournaments/                         # Event management + round/pairing UI
└── reports/                             # Margin, inventory, tournament reports
```

---

## Swiss Pairing Algorithm

Round 1 is fully random. Subsequent rounds:

1. Players sorted by current standings (MP → OMW% → GWP% → OGW%)
2. Grouped by match points, highest first
3. Within each group, pair sequentially avoiding rematches
4. Unpaired players "carry down" to the next point group
5. Last remaining player receives a bye (2 points, no game record)

## Standings Tiebreakers (DCI-compliant)

| Priority | Metric | Formula |
|---|---|---|
| 1 | Match Points | Win=3, Draw=1, Bye=2, Loss=0 |
| 2 | OMW% | avg(opponents' match win %, min 33%) |
| 3 | GWP% | player games won / games played (min 33%) |
| 4 | OGW% | avg(opponents' GWP%, min 33%) |

## Margin Calculator

For a booster box at $80 cost / $144.99 MSRP / 6 boxes per carton:

```
Margin per box:    $64.99  (44.8%)
Carton cost:       $480.00
Carton MSRP:       $869.94
Carton margin:     $389.94
Break-even price:  $80.00
```

Price scenario table also shows 5%, 10%, 15%, 20% discount impact.

---

## Artisan Commands

```bash
# Check low-stock products
php artisan inventory:check-low-stock

# Clear standings cache for a tournament
php artisan cache:forget standings.tournament.{id}

# Start queue worker (for future background jobs)
php artisan queue:work redis
```

---

## Docker Services

| Service | Port | Purpose |
|---|---|---|
| `tcg_nginx` | 8080 | Web server |
| `tcg_app` | 9000 | PHP-FPM |
| `tcg_mysql` | 3306 | Database |
| `tcg_redis` | 6379 | Cache / Sessions / Queues |
| `tcg_mailpit` | 8025 | Mail catcher (dev) |

---

## License

MIT
