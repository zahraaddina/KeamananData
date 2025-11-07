# Pedoman Etika & Keamanan — Praktikum SQL Injection (SQLi)

**Versi:** 1.0  
**Terakhir diperbarui:** 2025-10-03

> Dokumen ini menjelaskan aturan etika, persyaratan lingkungan, dan prosedur keamanan yang harus diikuti sebelum, selama, dan setelah menjalankan materi praktikum terkait SQL Injection (SQLi). Baca dan patuhi semua poin berikut — pelanggaran dapat berakibat sanksi akademik dan/atau hukum.

---

## 1. Tujuan
Praktikum ini bertujuan:
- Memahami konsep SQL Injection secara teoritis.  
- Mengidentifikasi pola kode rentan.  
- Menerapkan mitigasi (prepared statements, input validation, password hashing).

Fokus: **pencegahan & remediasi**, bukan eksploitasi untuk tujuan ilegal.

---

## 2. Lingkungan Pelaksanaan (WAJIB dipenuhi)
- Semua eksperimen with vulnerable code **hanya** di VM/mesin lokal yang **terisolasi** (host-only / network-disabled).  
- VM harus memiliki snapshot/backup untuk rollback.  
- Hanya instruktur yang menyimpan materi eksploit (payload) & melakukan demonstrasi eksploit di lab tertutup.  
- Hindari penggunaan akun DB `root` pada machine yang terhubung jaringan.

---

## 3. Aturan & Batasan
- **Dilarang** menjalankan exploit di sistem tanpa izin tertulis pemilik.  
- **Dilarang** membagikan payload eksploit atau hasil eksploitasi di repo publik.  
- File deliberately vulnerable boleh disimpan di repo **hanya** jika diberi peringatan jelas dan diakses oleh instruktur/VM yang aman.  
- Peserta wajib menyetujui kode etik praktikum sebelum mendapat akses materi lab.

---

## 4. Peran Instruktur
- Menyediakan lab terisolasi & snapshot VM.  
- Menyimpan payload & skrip eksploit offline.  
- Menjalankan demo exploit hanya pada VM snapshot offline dan melakukan cleanup setelah selesai.

---

## 5. Tanggung Jawab Peserta
- Ikuti instruksi keamanan; tidak melakukan pengujian di lingkungan publik.  
- Laporkan temuan kerentanan melalui saluran resmi bila menemukan masalah di sistem institusi.  
- Hapus salinan materi rentan setelah praktikum sesuai instruksi.

---

## 6. Prosedur Cleanup
Sebelum demo: buat snapshot VM.  
Setelah demo: hapus file sensitif, bersihkan log, dan rollback snapshot.

---

## 7. Konsekuensi Pelanggaran
Pelanggaran kebijakan dapat berakibat diskualifikasi, tindakan administratif, atau proses hukum bila melanggar peraturan.

---

## 8. Kontak Pelaporan
- Tim IT Keamanan: dhendra marutho  
- Dosen Pengampu: dhendra@unimus.ac.id
