# DEKROYSHOP - High-Performance WarZ Game Item Store & Catalog
## ออกแบบสำหรับ Free Hosting (InfinityFree) | อัตลักษณ์ BMW Corporate Automotive | เชื่อมต่อ Facebook Fanpage 100%

ระบบเว็บร้านค้าไอเทมและคลังแสงเกม WarZ (ToyStoryZ) พัฒนาด้วย **Pure PHP 8.4 + MySQL/MariaDB (PDO)** ไร้ Framework หนัก ไร้ Node.js/npm และไม่ต้อง Build ใด ๆ โหลดรวดเร็ว ใช้ทรัพยากรต่ำเป็นพิเศษ ไฟล์รูปภาพ WebP คุณภาพสูงขนาดกะทัดรัด (เฉลี่ย 6–9 KB ต่อรูป) สามารถนำขึ้นฟรีโฮสติ้งของ **InfinityFree** ได้จริง 100%

---

## จุดเด่นของระบบ (System Highlights)

1. **InfinityFree Ready (100% Compatible)**:
   - ทำงานได้ทันทีบน Shared / Free Hosting ไม่ติดข้อจำกัด Inode หรือ CPU limit
   - ไฟล์ฐานข้อมูลรวมขนาดเพียง **1.14 MB** พร้อมข้อมูล 3,540 ไอเทม และ 8 เซ็ตคอมโบ
   - ใช้ทรัพยากร Memory ต่ำมาก (< 2MB ต่อ HTTP Request)
2. **BMW Corporate-Automotive Light Canvas Design**:
   - พื้นหลังหลักแบบ Light Canvas (`#ffffff`) ให้ความรู้สึกพรีเมียม สบายตา สะอาดตา
   - แผ่นการ์ดโมเดลสีเทาซอฟต์เพลต (`#fafafa`) พร้อมเส้นแบ่งแฮร์ไลน์ขอบคม
   - Dark Navy Hero Band (`#1a2129`) เพียงแถบเดียวต่อหน้าเพื่อเน้นโฟกัสโมเดลนำ
   - เส้นสายความแรง M-Performance Tricolor (`#0066b1` / `#002b49` / `#e21a1a`)
   - ปุ่มทรงเรขาคณิต Rectangular 0px ขอบเหลี่ยมสไตล์รถยุโรป
   - คอนทราสต์ฟอนต์ระดับ Editorial: Heavy 700 (Display/Nav/Button) ปะทะ Light 300 (Body)
3. **Facebook Fanpage Direct Acquisition**:
   - ลูกค้าสามารถดูสินค้า ราคา รหัสไอเทม สเปก และภาพ 3D
   - ปุ่มหลัก **"ทักสอบถาม / สั่งซื้อผ่าน FACEBOOK FANPAGE"** เชื่อมตรงสู่เพจ `https://www.facebook.com/dekroyzz`
   - ระบบ **1-Click Copy Item Slip**: คัดลอกชื่อ รหัส และราคาไอเทม พร้อมส่งให้แอดมินในแชททันที
4. **WarZ StoreIcons Complete Ingestion**:
   - ดึงและแปลงไฟล์ StoreIcons ทั้งหมด 3,532 รูปจาก `Data\Weapons\StoreIcons` (.dds) ให้เป็น WebP ขนาด 256x256 px
   - ดึงข้อมูลชื่อภาษาไทย/อังกฤษ และคำอธิบายแท้จากไฟล์เกม `itemsDB.xml`
5. **100% Matched Loadout Sets (หมวดหมู่เซ็ตคอมโบ 6 ชิ้น)**:
   - 1 เซ็ตประกอบด้วย 6 ชิ้นสมบูรณ์แบบ: **หัว + ตัว + ปืน + มีด + กระเป๋า + ตัวละคร**
   - โชว์รูมตรวจสอบความเข้ากันได้ (Loadout Compatibility Showroom) แสดงสถานะ *100% MATCHED - SYNCHRONIZED*

---

## สารบัญคู่มือ (Documentation Index)

1. [วิธีนำขึ้น InfinityFree ทีละขั้นตอน](#1-วิธีนำขึ้น-infinityfree-ทีละขั้นตอน)
2. [โครงสร้างฐานข้อมูลและไฟล์ SQL](#2-โครงสร้างฐานข้อมูลและไฟล์-sql)
3. [วิธีตั้งค่า config/config.php](#3-วิธีตั้งค่า-configconfigphp)
4. [ข้อมูลเข้าสู่ระบบ Admin](#4-ข้อมูลเข้าสู่ระบบ-admin)
5. [ระบบหมวดหมู่และเซ็ตคอมโบ 6 ชิ้น](#5-ระบบหมวดหมู่และเซ็ตคอมโบ-6-ชิ้น)
6. [การจัดการ StoreIcons และการเพิ่มสินค้า](#6-การจัดการ-storeicons-และการเพิ่มสินค้า)
7. [การแก้ปัญหาที่พบบ่อย (Troubleshooting)](#7-การแก้ปัญหาที่พบบ่อย-troubleshooting)

---

## 1. วิธีนำขึ้น InfinityFree ทีละขั้นตอน

### ขั้นตอนที่ 1: สมัครและสร้าง Hosting บน InfinityFree
1. เข้าไปที่ [https://www.infinityfree.com](https://www.infinityfree.com) แล้วลงทะเบียนเข้าใช้งาน
2. ในหน้า **Client Area** ให้กดปุ่ม **Create Account**
3. เลือกโดเมนฟรี เช่น `dekroyshop.infinityfreeapp.com` หรือ `dekroyshop.rf.gd`
4. รอระบบ Provisioning บัญชีประมาณ 1–2 นาที

### ขั้นตอนที่ 2: สร้างฐานข้อมูล MySQL บน vPanel
1. เข้าไปที่ **Control Panel (vPanel)** ของบัญชีที่สร้างไว้
2. เลื่อนไปที่หัวข้อ **Databases** แล้วคลิกที่ **MySQL Databases**
3. ในช่อง **Create New Database** ให้พิมพ์ชื่อ เช่น `shop` หรือ `dekroy` แล้วกด **Create Database**
4. จดบันทึกค่าที่ระบบแสดงขึ้นมา:
   - **MySQL Host Name**: เช่น `sql100.infinityfree.com`
   - **MySQL Database Name**: เช่น `epiz_12345678_shop`
   - **MySQL User Name**: เช่น `epiz_12345678`
   - **MySQL Password**: รหัสผ่าน vPanel ของบัญชีคุณ

### ขั้นตอนที่ 3: Import ฐานข้อมูลผ่าน phpMyAdmin
1. ในหน้า MySQL Databases ให้กดปุ่ม **phpMyAdmin** ข้างชื่อฐานข้อมูลที่เพิ่งสร้าง
2. คลิกเลือกชื่อฐานข้อมูลที่แถบเมนูด้านซ้าย
3. คลิกแท็บ **Import (นำเข้า)** ด้านบน
4. ในส่วน *File to import* ให้กด **Choose File** แล้วเลือกไฟล์ SQL รวมชุดสมบูรณ์:
   ```
   /dekroyshop/sql/infinityfree_full_database.sql
   ```
   *(ไฟล์นี้มีขนาดเพียง 1.14 MB ซึ่งผ่านเกณฑ์ Upload Limit ของ phpMyAdmin ได้อย่างสบาย)*
5. ตรวจสอบว่า Format เป็น **SQL** แล้วเลื่อนลงล่างสุดกดปุ่ม **Import (นำเข้า)**
6. เมื่อเสร็จสิ้น จะได้ตารางครบทั้ง 11 ตาราง พร้อมข้อมูลไอเทม 3,540 ชิ้น และ 8 เซ็ตคอมโบ

### ขั้นตอนที่ 4: ตั้งค่าไฟล์ `config/config.php`
เปิดไฟล์ `config/config.php` แล้วระบุข้อมูลการเชื่อมต่อของ InfinityFree:

```php
// โหมดการทำงานบนโฮสจริงให้ตั้งเป็น production
define('APP_ENV', 'production');

// URL ประจำเว็บไซต์ของคุณ
define('APP_URL', 'https://dekroyshop.infinityfreeapp.com');

// ข้อมูลการเชื่อมต่อฐานข้อมูล MySQL ของ InfinityFree
define('DB_HOST', 'sql100.infinityfree.com'); // ใช้ค่า MySQL Host Name จาก vPanel
define('DB_PORT', '3306');
define('DB_NAME', 'epiz_12345678_shop');      // ใช้ค่า MySQL Database Name จาก vPanel
define('DB_USER', 'epiz_12345678');           // ใช้ค่า MySQL User Name จาก vPanel
define('DB_PASS', 'รหัสผ่าน_vPanel_ของคุณ');
define('DB_CHARSET', 'utf8mb4');
```

### ขั้นตอนที่ 5: อัปโหลดไฟล์ขึ้นโฮสติ้งผ่าน FTP (FileZilla)
1. ดาวน์โหลดและเปิดโปรแกรม **FileZilla Client**
2. นำข้อมูล FTP จาก InfinityFree vPanel มาใส่ใน FileZilla:
   - **Host:** `ftpupload.net`
   - **Username:** เช่น `epiz_12345678`
   - **Password:** รหัสผ่าน vPanel
   - **Port:** `21`
   - กด **Quickconnect**
3. ทางฝั่งขวา (Remote Site) ให้ดับเบิลคลิกเข้าไปที่โฟลเดอร์:
   ```
   /htdocs/
   ```
4. ทางฝั่งซ้าย (Local Site) ให้เข้าไปในโฟลเดอร์ `c:\WarZ ToyStoryZ1\dekroyshop`
5. เลือกไฟล์และโฟลเดอร์ทั้งหมด (รวมทั้ง `.htaccess`) แล้วคลิกขวาเลือก **Upload**
6. เมื่ออัปโหลดเสร็จสิ้น เปิดบราวเซอร์เข้าชมเว็บไซต์ของคุณได้ทันที!

---

## 2. โครงสร้างฐานข้อมูลและไฟล์ SQL

ระบบมีตารางทั้งหมด 11 ตาราง รองรับ UTF-8 (utf8mb4_unicode_ci):

| ตาราง | คำอธิบาย |
| :--- | :--- |
| `categories` | หมวดหมู่ไอเทม (Sniper, Assault, Armor, Backpack, Melee, Hero, Medical, etc.) |
| `products` | ข้อมูลไอเทมทั้งหมด 3,540 รายการ พร้อมรูปภาพ WebP และ Item Code |
| `item_sets` | เซ็ตไอเทมคอมโบ 6 ชิ้น (หัว + ตัว + ปืน + มีด + กระเป๋า + ตัวละคร) 100% Match |
| `store_icons` | คลังรูปภาพ StoreIcons จากไฟล์เกม WarZ (3,532 รายการ) |
| `orders` | รายการคำสั่งซื้อของลูกค้า |
| `order_items` | รายการไอเทมภายในแต่ละคำสั่งซื้อ |
| `users` | บัญชีผู้ใช้งานและผู้ดูแลระบบ |
| `roles` | สิทธิ์การเข้าถึง (`admin`, `customer`) |
| `settings` | การตั้งค่าชื่อร้าน ข้อมูลการติดต่อ และสถานะร้าน |
| `activity_logs` | บันทึกประวัติการกระทำสำคัญในระบบเพื่อความปลอดภัย |
| `product_images` | รูปภาพเพิ่มเติมของสินค้า |

### ไฟล์ SQL ในโฟลเดอร์ `/sql/`:
- `infinityfree_full_database.sql` (1.14 MB): ไฟล์ SQL รวมครบทุกอย่างในไฟล์เดียว แนะนำสำหรับ InfinityFree
- `database.sql` (11 KB): โครงสร้างตารางเปล่า + บัญชี Admin เริ่มต้น
- `import_all_items.sql` (1.17 MB): ข้อมูลไอเทมและรูปภาพทั้งหมด 3,540 รายการ

---

## 3. วิธีตั้งค่า config/config.php

ไฟล์ตั้งค่าตั้งอยู่ที่ `/config/config.php` จัดระเบียบอย่างชัดเจน:
- **`APP_ENV`**: `'development'` แสดงข้อผิดพลาดเพื่อการแก้ไข / `'production'` ซ่อน Error เพื่อความปลอดภัย
- **`APP_URL`**: ระบบมีกลไก Auto-detect อัตโนมัติ หรือจะระบุคงที่ เช่น `'https://dekroy.shop'`
- **`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`**: พารามิเตอร์เชื่อมต่อฐานข้อมูล

---

## 4. ข้อมูลเข้าสู่ระบบ Admin

ระบบหลังบ้านถูกออกแบบให้เบา ใช้งานง่าย และมีความปลอดภัยสูง:

- **URL เข้าสู่ระบบแอดมิน:** `http://yourdomain.com/admin/login.php`
- **Username:** `admin`
- **Password:** `Admin@123456`

### ฟังก์ชันในระบบแอดมิน:
- **Dashboard**: แสดงสรุปยอดคำสั่งซื้อ สต็อกสินค้า และบันทึกกิจกรรมล่าสุด
- **จัดการสินค้า (Products)**: เพิ่ม ลบ แก้ไข ปรับสต็อก และเปลี่ยนหมวดหมู่
- **เซ็ตคอมโบ (Item Sets)**: จัดการชุดเซ็ต 6 ชิ้น ตรวจสอบคะแนนความเข้ากันได้
- **คลังไอคอน (Store Icons)**: จัดการรูปภาพไอคอนที่แปลงมาจากเกม WarZ
- **รายการคำสั่งซื้อ (Orders)**: ตรวจสอบคำสั่งซื้อ อัปเดตสถานะการส่งของเข้าตัวละคร
- **ตั้งค่าระบบ (Settings)**: เปลี่ยนชื่อร้าน ข้อความแท็กไลน์ และอีเมลติดต่อ

---

## 5. ระบบหมวดหมู่และเซ็ตคอมโบ 6 ชิ้น

### หมวดหมู่มาตรฐาน:
1. **Sniper Rifles**: สไนเปอร์ยิงระยะไกล (Barrett, VSS, AW Magnum, SVD, CheyTac, etc.)
2. **Assault Rifles & SMG**: ปืนกลจู่โจมและปืนกลมือ (M4A1, AK-47, FN SCAR, etc.)
3. **Body Armor & Vests**: เกราะลำตัวและเสื้อเกราะแทคติคอล
4. **Helmets & Headgear**: หมวกกันกระสุน หน้ากาก และหมวกแฟชั่น
5. **Backpacks**: กระเป๋าเป้ทหารและกระเป๋าความจุสูง
6. **Characters & Heroes**: ตัวละครและสกินฮีโร่พิเศษ
7. **Melee Weapons**: อาวุธระยะประชิด มีด ค้อน ดาบ
8. **Loadout Sets (เซ็ตคอมโบ)**: ชุดรวม 6 ชิ้นราคาพิเศษ
9. **Mystery Boxes & Crates**: กล่องสุ่มและไอเทมกล่องของขวัญ
10. **Medical & Survival**: ยารักษา ผ้าพันแผล และเสบียงยังชีพ

### ระบบความเข้ากันได้ (Loadout Compatibility):
- แต่ละเซ็ตประกอบด้วย 6 ชิ้นที่เข้าคู่กัน 100%:
  `[01. หัว]` + `[02. ตัว]` + `[03. ปืน]` + `[04. มีด]` + `[05. กระเป๋า]` + `[06. ตัวละคร]`
- มีหน้าร้านเฉพาะทางที่ `/sets.php` พร้อมปุ่มทักแฟนเพจสั่งซื้อยกเซ็ตในราคาพิเศษ

---

## 6. การจัดการ StoreIcons และการเพิ่มสินค้า

- รูปภาพ StoreIcons ทั้งหมด 3,532 ไฟล์ ถูกแปลงจาก `.dds` เป็น `.webp` ขนาด 256x256 px เรียบร้อยแล้ว อยู่ในโฟลเดอร์ `/assets/storeicons/`
- รูปภาพแต่ละรูปมีขนาดเฉลี่ยเพียง **6–9 KB** ทำให้โหลดไวมาก แม้ใช้งานบนมือถือหรือเน็ตความเร็วต่ำ
- หากต้องการแปลงรูปภาพ StoreIcon เพิ่มเติมในอนาคต สามารถใช้สคริปต์ Python ในโฟลเดอร์ `tools/`:
  ```bash
  python tools/import_all_warz_icons.py
  ```

---

## 7. การแก้ปัญหาที่พบบ่อย (Troubleshooting)

### Q: ขึ้นข้อความ "Database Connection Error" หรือหน้าขาว?
1. ตรวจสอบค่า `DB_HOST` ใน `config/config.php` — บน InfinityFree ต้องใช้ค่า MySQL Host Name จาก vPanel เช่น `sqlxxx.infinityfree.com` **ห้ามใช้ localhost หรือ 127.0.0.1**
2. ตรวจสอบว่าชื่อฐานข้อมูลและชื่อผู้ใช้มี Prefix เช่น `epiz_12345678_...` ครบถ้วน
3. เปลี่ยน `define('APP_ENV', 'development');` ชั่วคราวเพื่อดูข้อความ Error ที่ชัดเจน

### Q: รูปภาพไม่แสดงบนโฮสติ้ง?
1. ตรวจสอบว่าได้อัปโหลดโฟลเดอร์ `/assets/storeicons/` ขึ้นไปครบถ้วนหรือไม่
2. ตรวจสอบสิทธิ์ของโฟลเดอร์ (Folder Permissions) ให้เป็น `0755`

### Q: ปุ่ม Facebook Fanpage ไม่เปิดหน้าเพจ?
- ลิงก์มาตรฐานถูกตั้งค่าไว้ที่: `https://www.facebook.com/dekroyzz`
- ตรวจสอบการเชื่อมต่ออินเทอร์เน็ตของเครื่อง หรือแก้ไข URL ใน `includes/frontend-header.php` และ `product.php` หากมีการเปลี่ยนแฟนเพจในอนาคต

---

*DEKROYSHOP &copy; 2026 Developed with Senior Engineering Standards for WarZ Communities on InfinityFree Hosting.*
