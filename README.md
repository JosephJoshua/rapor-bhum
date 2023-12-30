# Rapor BHum

Aplikasi berbasis web untuk mengelola nilai Budaya Humanis.

## Cara Menjalankan

1. Copy file `.env.example` dan rename menjadi `.env`, kemudian ganti nilai untuk setiap key sesuai konfigurasi yang diinginkan.

2. Jalankan perintah

   ```
       composer install && npm install
   ```

   untuk menginstall package yang dibutuhkan proyek.

3. Jalankan perintah

   ```
       php artisan migrate
   ```

   untuk menyesuaikan skema database.

4. Jalankan perintah
   ```
       npm run dev && php artisan serve
   ```
   untuk memulai development server.
