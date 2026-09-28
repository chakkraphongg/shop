# 🚀 DEKROYSHOP - Deployment & GitHub Setup Guide

คู่มือการนำโปรเจกต์ขึ้น **GitHub** และนำไปติดตั้ง/Deploy บน **InfinityFree** หรือ PHP Hosting อื่นๆ

---

## 📌 โครงสร้างสำคัญของโปรเจกต์
- `index.php` : หน้าแรกของร้านค้า (Modern Gaming Arsenal + Real 3D WebGL Showcase)
- `config/config.php` : ไฟล์ตั้งค่าหลัก มีระบบตรวจจับ URL อัตโนมัติ (`APP_URL`)
- `config/config.local.example.php` : ตัวอย่างไฟล์ตั้งค่า Database สำหรับโฮสติ้งจริง (InfinityFree)
- `sql/dekroyshop_backup.sql` : ไฟล์ Dump ฐานข้อมูลฉบับสมบูรณ์ (พร้อมตารางและสินค้า 2,231 รายการ)
- `assets/models/` : คลังโมเดล 3D WebGL และ Texture คุณภาพสูง
- `assets/storeicons/` : ภาพไอคอนไอเทมสไตล์ Half-body Transparent Cutouts

---

## 🛠️ ขั้นตอนที่ 1: การนำขึ้น GitHub Repository

> **หมายเหตุ:** GitHub ใช้สำหรับจัดเก็บ Source Code และ Version Control เพื่อความปลอดภัยและทำงานร่วมกัน

1. **สร้าง Repository บน GitHub:**
   - เข้าเว็บ [GitHub.com](https://github.com) แล้วกด **New repository**
   - ตั้งชื่อ repository เช่น `dekroyshop`
   - เลือกเป็น **Public** หรือ **Private** แล้วกด **Create repository**

2. **เปิด Terminal / Command Prompt ในโฟลเดอร์โปรเจกต์:**
   ```bash
   cd "c:\WarZ ToyStoryZ1\dekroyshop"
   ```

3. **Initialize Git และ Push โค้ดขึ้น GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit: DEKROYSHOP Full-Stack PHP Web App with 3D WebGL"
   git branch -M main
   git remote add origin https://github.com/<YOUR_USERNAME>/<YOUR_REPO_NAME>.git
   git push -u origin main
   ```

---

## 🌐 ขั้นตอนที่ 2: การ Deploy บน InfinityFree Hosting

### 1. นำเข้าฐานข้อมูล (MySQL Import)
1. ล็อกอินเข้าสู่ **InfinityFree Control Panel (vPanel)**
2. ไปที่เมนู **MySQL Databases** แล้วสร้าง Database ใหม่ เช่น `epiz_XXXXXX_dekroyshop`
3. คลิกเปิด **phpMyAdmin** ของฐานข้อมูลนั้น
4. ไปที่แท็บ **Import (นำเข้า)** -> เลือกไฟล์ `sql/dekroyshop_backup.sql` จากเครื่อง -> กด **Import (ลงมือ)**

### 2. อัปโหลดไฟล์เว็บไซต์ (File Upload)
- **วิธีที่ 1 (แนะนำ):** ใช้โปรแกรม **FileZilla** เชื่อมต่อ FTP ด้วยข้อมูลจาก InfinityFree Account Details
- **วิธีที่ 2:** ใช้ **Online File Manager** บน vPanel
- นำไฟล์ทั้งหมดในโฟลเดอร์ `dekroyshop` ไปวางไว้ในโฟลเดอร์ **`htdocs/`** ของ InfinityFree

### 3. ตั้งค่าการเชื่อมต่อฐานข้อมูล (Database Config)
1. ในโฟลเดอร์ `config/` บนโฮสติ้ง ให้สร้างไฟล์ชื่อ **`config.local.php`** (หรือคัดลอกจาก `config.local.example.php`)
2. ใส่ข้อมูล Database จาก vPanel ของ InfinityFree ดังนี้:
   ```php
   <?php
   declare(strict_types=1);

   define('APP_ENV', 'production');

   // ข้อมูลจาก InfinityFree vPanel
   define('DB_HOST', 'sqlXXX.infinityfree.com');      // ดูที่ MySQL Host ใน vPanel
   define('DB_PORT', '3306');
   define('DB_NAME', 'epiz_XXXXXX_dekroyshop');       // ชื่อ Database ที่สร้าง
   define('DB_USER', 'epiz_XXXXXX');                  // MySQL Username ใน vPanel
   define('DB_PASS', 'YOUR_VPANEL_PASSWORD');         // รหัสผ่านบัญชี vPanel
   define('DB_CHARSET', 'utf8mb4');
   ```

---

## ✅ การทดสอบและเข้าใช้งาน
- **หน้าเว็บไซต์หลัก:** `http://your-domain.infinityfreeapp.com/index.php`
- **ระบบแอดมิน:** `http://your-domain.infinityfreeapp.com/admin/login.php`
- **ชื่อผู้ใช้เริ่มต้น:** `admin` | **รหัสผ่าน:** `admin1234` (แนะนำให้เปลี่ยนรหัสผ่านทันทีหลังติดตั้ง)
