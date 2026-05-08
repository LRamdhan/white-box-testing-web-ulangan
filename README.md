
# Pengujian White Box Web Ulangan SMKN Situraja

Repositori ini berisi skrip otomatisasi pengujian untuk sistem Web Ulangan SMKN Situraja. Pengujian ini menerapkan metode *White Box Testing* dengan teknik *Basis Path Testing* untuk memvalidasi integritas alur logika internal program pada level *End-to-End* (E2E).

## Pengelolaan Berkas

Struktur pengelolaan berkas pengujian diatur secara sistematis sebagai berikut:

* **/flowgraph**: Direktori ini berfungsi sebagai repositori seluruh representasi alur logika program dalam bentuk diagram *Control Flow Graph* (CFG). Direktori ini terbagi ke dalam sub-direktori untuk setiap fitur spesifik yang menjadi objek pengujian.
* **/tests/Feature**: Direktori ini menyimpan berkas skrip ***test case*** (kode pengujian) untuk setiap fitur. Setiap skrip dirancang secara unik untuk menguji setiap jalur independen (***independent path***) yang telah dipetakan sebelumnya.

## Cara Instalasi dan Penggunaan

### Prasyarat (*Requirement*)
Sebelum menjalankan pengujian, pastikan lingkungan pengembangan Anda telah memenuhi prasyarat berikut:
* PHP versi 8.3 atau yang lebih baru.
* Composer.
* Node.js (NPM).

### Instalasi
1.  Lakukan penggandaan repositori (*pull repository*) ke direktori lokal Anda.
2.  Buka terminal pada direktori proyek, kemudian jalankan perintah berikut untuk menginstal seluruh dependensi yang diperlukan:
    ```bash
    composer install
    npm install
    ```
3.  Konfigurasikan variabel lingkungan dengan membuat berkas `.env` baru berdasarkan salinan dari berkas `.env.example`. Pastikan pengaturan **basis data** dan URL aplikasi sudah sesuai dengan lingkungan server lokal Anda.

### Penggunaan
Untuk mengeksekusi seluruh rangkaian skenario pengujian secara otomatis, jalankan instruksi berikut melalui terminal:
```bash
composer run-script test-all
```