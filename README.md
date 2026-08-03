# Tasks REST API — Yii2 uslubidagi qatlamli arxitektura

Bu PHP 8.1+ va SQLite asosidagi Task CRUD API. U Yii2 dagi odatiy ajratishga mos ravishda `Entity`, `Repository`, `Service` va `Controller` qatlamlariga bo'lingan. Loyiha mustaqil ishlaydi; haqiqiy Yii2 paketini ulash uchun Composer va PHP CLI kerak bo'ladi.

## Talablar

- PHP 8.1+
- `pdo_sqlite` kengaytmasi

## Ishga tushirish

```bash
php -S localhost:8000 -t public
```

API manzili: `http://localhost:8000/api/tasks`

## Endpointlar

| Metod | Yo'l | Tavsif |
|---|---|---|
| `GET` | `/api/tasks` | Barcha tasklar |
| `GET` | `/api/tasks/{id}` | Bitta task |
| `POST` | `/api/tasks` | Yangi task yaratish |
| `PUT`, `PATCH` | `/api/tasks/{id}` | Taskni yangilash |
| `DELETE` | `/api/tasks/{id}` | Taskni o'chirish |

`POST`, `PUT` va `PATCH` so'rovlari JSON obyekt bo'lishi kerak. `title` majburiy matn; `description` matn yoki `null`; `is_done` esa `true` yoki `false`.

## Arxitektura

```
app/
├── Controller/TaskController.php  # HTTP endpointlar
├── Entity/Task.php                # Task domen modeli
├── Exception/HttpException.php    # Boshqariladigan HTTP xatolari
├── Http/JsonResponse.php          # JSON response helper
├── Infrastructure/Database.php    # PDO va SQLite sxemasi
├── Repository/TaskRepository.php  # Ma'lumotlar qatlami
└── Service/TaskService.php         # Biznes qoidalari va validatsiya
```

`public/index.php` routing va dependency yaratishni bajaradi. Har bir qatlamda vazifasini ko'rsatuvchi komment bor.

## Xavfsizlik va validatsiya

- SQL so'rovlari prepared statement orqali ishlaydi.
- Noto'g'ri JSON va ID uchun `400`, validatsiya xatolari uchun `422` qaytariladi.
- Ichki exception ma'lumoti klientga ochilmaydi, faqat server logiga yoziladi.
- CORS faqat `http://localhost:8000` uchun ochiq; productionda uni frontend domeningizga almashtiring.
