# Queue Worker

Du an dang dung `QUEUE_CONNECTION=database`.

Neu khong co worker:
- mail se nam trong bang `jobs`
- Gmail thong bao dang nhap se khong duoc gui
- job nen khac cung khong duoc xu ly

## Local

Chay full local:

```bash
npm run dev
```

Lenh nay se chay dong thoi:
- `vite`
- `php artisan serve`
- `composer run queue:work`

Neu ban tu chay server rieng:

```bash
php artisan serve
composer run queue:work
```

## Worker chuan

```bash
composer run queue:work
```

Lenh thuc te:

```bash
php artisan queue:work database --queue=default --tries=3 --timeout=90 --sleep=3 --backoff=10 --max-time=3600
```

## Failed Jobs

Xem job loi:

```bash
composer run queue:failed
```

Retry toan bo job loi:

```bash
composer run queue:retry
```

Restart worker sau khi deploy:

```bash
composer run queue:restart
```
