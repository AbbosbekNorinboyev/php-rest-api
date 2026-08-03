# PHP REST API — Tasks

Oddiy, framework talab qilmaydigan PHP REST API. SQLite bazasidan foydalanadi (o'rnatish talab qilinmaydi — birinchi ishga tushganda avtomatik yaratiladi).

## Talablar

- PHP 8.1+ (`match` va `str_starts_with` ishlatilgan)
- `pdo_sqlite` kengaytmasi (odatda PHP bilan birga keladi)

## Ishga tushirish

Loyiha papkasida:

```bash
php -S localhost:8000 -t public
```

API manzili: `http://localhost:8000/api/tasks`

## Endpointlar

| Metod  | Yo'l               | Tavsif                     |
|--------|--------------------|-----------------------------|
| GET    | /api/tasks         | Barcha tasklar ro'yxati     |
| GET    | /api/tasks/{id}    | Bitta task                  |
| POST   | /api/tasks         | Yangi task yaratish         |
| PUT    | /api/tasks/{id}    | Taskni yangilash            |
| DELETE | /api/tasks/{id}    | Taskni o'chirish             |

## Misollar (curl)

**Barcha tasklarni olish:**
```bash
curl http://localhost:8000/api/tasks
```

**Yangi task yaratish:**
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title": "Java o'\''rganish", "description": "OOP mavzusini takrorlash"}'
```

**Taskni yangilash (bajarilgan deb belgilash):**
```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{"is_done": true}'
```

**Taskni o'chirish:**
```bash
curl -X DELETE http://localhost:8000/api/tasks/1
```

## Loyiha strukturasi

```
php-rest-api/
├── public/
│   └── index.php          # Kirish nuqtasi va router
├── src/
│   ├── Database.php        # PDO ulanish (SQLite)
│   ├── Response.php        # JSON javob helper
│   └── TaskController.php  # CRUD mantiqi
├── database/
│   └── schema.sql           # Jadval sxemasi
└── README.md
```

## Keyingi qadamlar (ixtiyoriy)

- MySQL/PostgreSQL'ga o'tish uchun `Database.php` faylidagi DSN'ni o'zgartiring
- Autentifikatsiya (JWT yoki API key) qo'shish
- Validatsiya kutubxonasi (masalan Respect/Validation) ulash
- PHPUnit bilan testlar yozish
