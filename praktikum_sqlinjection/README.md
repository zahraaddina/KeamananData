# Praktikum: SQL Injection (SQLi) — Demo & Materi
**Repo:** dhendra-vibiano/praktikum_sqlinjection  
**Pemilik / Dosen:** Dhendra Marutho

> Repo ini berisi materi praktikum terkait SQL Injection (teori + contoh kode).  
> **Penting:** beberapa file bersifat *deliberately vulnerable* untuk tujuan pembelajaran (hanya untuk lab terisolasi). Baca `ETHICS.md` sebelum menjalankan.

---

## Ringkasan isi
- `db_setup.sql` — SQL untuk membuat database dan tabel demo (`users_vul`, `users_safe`).  
- `create_user_safe.php` / `create_user_safe_form.php` — buat user **aman** (hash).  
- `create_user_vul.php` / `create_user_vul_form.php` — buat user **rentan** (DEMO ONLY).  
- `login_safe.php` — login menggunakan prepared statements + hashing (AMAN).  
- `login_vul.php` — login rentan (contoh pola concatenation) — **JANGAN** deploy di jaringan publik.  
- `dashboard.php`, `logout.php` — halaman sederhana setelah login.  
- `ETHICS.md` — pedoman etika & keamanan praktikum (WAJIB dibaca).  
- `student_package.zip` (opsional) — paket khusus mahasiswa (hanya file *safe*).

---

## Cara cepat setup (lokal / dev)
1. Pastikan Anda memiliki webserver lokal (XAMPP/MAMP/LAMP) dan MySQL.  
2. Tempatkan folder repo di document root (mis. `C:\xampp\htdocs\praktikum_sqlinjection` atau `/var/www/html/praktikum_sqlinjection`).  
3. Import `db_setup.sql` ke MySQL (phpMyAdmin atau CLI).  
4. Akses di browser:
   - `http://localhost/praktikum_sqlinjection/create_user_safe.php` → buat akun aman.  
   - `http://localhost/praktikum_sqlinjection/login_safe.php` → coba login.  
   - **Versi rentan** hanya untuk lab instruktur (`create_user_vul.php`, `login_vul.php`) dan **harus** dijalankan pada VM/host terisolasi.

---

## Safety & Etika (ringkasan)
Sebelum menjalankan kode, baca `ETHICS.md`. Singkatnya:
- File rentan hanya untuk VM/host terisolasi yang tidak terhubung ke internet.  
- Jangan jalankan exploit di sistem yang bukan milik Anda.  
- Jangan sebarkan payload eksploit atau hasil eksploitasi.

---

## Cara kontribusi / submit tugas
- Mahasiswa: buat branch `student/<nim>` lalu push perubahan.  
- Buat Pull Request ke `main` dengan deskripsi dan lampirkan laporan singkat.  
- Kontribusi instruktur: gunakan branch `instructor` untuk file lab sensitif (di-private repo atau offline).

---

## Tips administrasi repo
- Tambahkan `.gitignore` minimal:
