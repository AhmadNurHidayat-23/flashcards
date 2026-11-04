# Flashcards

Kartu belajar digital interaktif untuk membuat materi tanya-jawab, melatih ingatan, dan melihat hasil latihan.

## Fitur

- Buat, edit, dan hapus kumpulan serta kartu belajar.
- Kartu dengan animasi flip, navigasi, pengacakan, dan shortcut keyboard.
- Tandai Sudah Hafal atau Belum Hafal; progres dihitung dari kartu yang dijawab.
- Hasil dan sesi latihan tersimpan di SQLite dan dapat dibuka kembali.
- Ulangi kartu yang belum hafal atau belum dijawab tanpa menghapus materi.
- Antarmuka responsif berbahasa Indonesia.

## Teknologi

Laravel 13, Blade, SQLite, HTML5, CSS3, Vanilla JavaScript, dan Vite.

## Menjalankan

Gunakan PHP 8.5, Composer, dan Node.js 22.12+.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
php artisan serve --host=127.0.0.1
```

Buka http://127.0.0.1:8000. Untuk pengembangan aset, jalankan `npm run dev` di terminal lain.
Spasi/Enter membalik kartu; panah kiri/kanan berpindah kartu. Penguasaan dihitung dari kartu hafal dibagi seluruh kartu dalam sesi.
Pengelolaan tanpa login dibatasi ke komputer lokal. Jangan buka aplikasi melalui proxy atau tunnel publik tanpa autentikasi.

Pengujian: `php artisan test` dan `npm run build`.
