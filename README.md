# Neotharts Portfolio

## Identitas Mahasiswa
- **Nama:** Muhammad Naufal
- **NRP:** 5124500010
- **Kelas:** MMB A

---

## 📊 Gantt Chart - Development Progress

| Fitur | M1 | M2 | M3 | M4 |
|-------|:--:|:--:|:--:|:--:|
| **Setup Project** | ██ | | | |
| **Admin Dashboard** | ██ | ██ | | |
| **CRUD Artwork** | ██ | ██ | | |
| **Visitor Tracking** | ██ | ██ | | |
| **Dynamic Homepage** | ██ | ██ | | |
| **Authentication** | ██ | ██ | | |
| **Artwork List Page** | | ██ | ██ | |
| **Image Compression** | | ██ | | |
| **Services Management** | | | ██ | |
| **Commission Page** | | | ██ | |
| **Multiple Image Upload** | | | ██ | |
| **Image Crop 4:5** | | | ██ | |
| **Visitor Chart** | | | ██ | |
| **Username Login** | | | | ██ |
| **Profile Management** | | | | ██ |
| **Password Security** | | | | ██ |
| **Dashboard Layout** | | | | ██ |
| **Contact Page & Messages** | | | | ██ |
| **3D Character Page** | | | | ██ |
| **Live2D Homepage Widget** | | | | ██ |
| **Fullscreen Mobile Navigation** | | | | ██ |

**Legend:**
- M1 = Minggu 1 (30 Apr - 6 Mei)
- M2 = Minggu 2 (7 - 13 Mei)
- M3 = Minggu 3 (14 - 20 Mei)
- M4 = Minggu 4 (21 - 27 Mei)
- ██ = Active Development

---

### 📅 Detail Progress Mingguan

#### 📆 Minggu 1 (30 April - 6 Mei 2026)
- Inisialisasi project Laravel
- Setup database dan migrations
- Konfigurasi environment

#### 📆 Minggu 2 (7 - 13 Mei 2026)
- Admin dashboard dengan statistik
- CRUD artwork dengan upload file
- Dynamic homepage (3 artwork terbaru)
- Visitor tracking system
- Authentication & admin middleware
- Responsive UI (glass morphism theme)
- Fitur `art_for` untuk track client
- Halaman artwork list
- ImageService (kompresi gambar otomatis)
- Perbaikan layout homepage

#### 📆 Minggu 3 (14 - 20 Mei 2026)
- Manajemen Services (CRUD commission types)
- Halaman commission page
- Multiple image upload (max 12)
- Fitur crop 4:5 untuk thumbnail
- Visitor chart 7 hari terakhir
- Login dengan username (bukan email)
- Menu profil admin (ubah nama, username, password)
- Password di-hash dengan bcrypt
- Hapus statistik "Total Artists" (single artist)
- Perbaikan layout dashboard
- Fix bug crop image untuk existing artwork

#### 📆 Minggu 4 (21 - 27 Mei 2026)
- Halaman Contact dengan form pesan
- Upload attachment pada contact form
- Admin Messages untuk melihat dan mengelola pesan masuk
- Halaman 3D Character interaktif
- Live2D widget pada homepage
- Layout responsive untuk section Latest Art + Live2D
- Fullscreen mobile/tablet navigation reusable
- Animasi scroll reveal khusus homepage
- Perbaikan navbar public agar konsisten di semua halaman

---

## Deskripsi

Portfolio website untuk Neotharts, platform portfolio digital untuk artist dengan fitur manajemen artwork, commission services, contact message management, visitor tracking, Live2D homepage widget, 3D character page, dan admin panel. Built with Laravel 13.

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite (default) or MySQL

### Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/neotharts/Neotharts-Portofolio-Web
   cd neotharts-portofolio
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite

   # Or configure MySQL in .env file
   ```

6. **Run Migrations & Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Storage Link**
   ```bash
   php artisan storage:link
   ```

9. **Start Server**
   ```bash
   php artisan serve
   ```

   Visit `http://127.0.0.1:8000`

## 🔐 Admin Access

- **URL:** `/login`
- **Username:** `admin`
- **Password:** `165165165Nnn`

## 📁 Project Structure

```
neotharts-portofolio/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── ArtworkController.php
│   │   │   │   ├── MessageController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   └── TosController.php
│   │   │   ├── AuthController.php
│   │   │   ├── ArtworkListController.php
│   │   │   ├── CommissionController.php
│   │   │   ├── ContactController.php
│   │   │   ├── HomeController.php
│   │   │   ├── ThreeDController.php
│   │   │   └── VisitorController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── Artwork.php
│   │   ├── Message.php
│   │   ├── Service.php
│   │   ├── SiteSetting.php
│   │   ├── User.php
│   │   └── Visitor.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       ├── AttachmentService.php
│       └── ImageService.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   │   ├── admin.css
│   │   ├── commission.css
│   │   └── live2d.css
│   ├── img/
│   ├── js/
│   │   ├── live2d-home.js
│   │   └── mobile-fullscreen-nav.js
│   └── kai/
├── resources/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── views/
│       ├── admin/
│       │   ├── artworks/
│       │   ├── messages/
│       │   ├── profile/
│       │   ├── services/
│       │   ├── tos/
│       │   ├── dashboard.blade.php
│       │   ├── layout.blade.php
│       │   └── artworks.blade.php
│       ├── auth/
│       │   └── login.blade.php
│       ├── partials/
│       │   └── mobile-fullscreen-nav.blade.php
│       ├── vendor/
│       ├── artwork_list.blade.php
│       ├── commission.blade.php
│       ├── contact.blade.php
│       ├── home.blade.php
│       ├── three_d.blade.php
│       └── welcome.blade.php
├── routes/
│   └── web.php
├── storage/
├── tests/
├── composer.json
└── package.json
```

## 🛡️ Security Notes

- **Never commit sensitive files:**
  - `.env` (contains API keys, database credentials)
  - `database/*.sqlite` (contains database data)
  - `storage/app/*` (uploaded files)
  - `storage/logs/*` (application logs)

- **Environment variables are ignored** by `.gitignore`

- **Use strong passwords** in production

## 🎨 Features

- ✅ Admin Dashboard with Statistics
- ✅ Artwork Management (CRUD + Multiple Image Upload)
- ✅ Image Compression & Crop 4:5 Thumbnail
- ✅ Services Management (Commission Types)
- ✅ Visitor Tracking with 7-Day Chart
- ✅ Commission Page (Form for Clients)
- ✅ Contact Page with Attachment Upload
- ✅ Admin Messages Inbox
- ✅ Dynamic Homepage & Artwork List
- ✅ Live2D Homepage Widget
- ✅ 3D Character Page
- ✅ Fullscreen Mobile/Tablet Navigation
- ✅ Homepage Scroll Reveal Animation
- ✅ Admin Profile Management (Username/Password)
- ✅ Responsive Design (Glass Morphism Theme)
- ✅ Secure Authentication (Bcrypt Hashing)

## 📊 Database Schema

| Table | Description |
|-------|-------------|
| **users** | Admin users (username, email, password, is_admin) |
| **artworks** | Portfolio artworks (title, images, type, list_service, art_for, is_published) |
| **services** | Commission types (name, type, starting_price, sort_order) |
| **messages** | Contact messages (name, email, subject, message, attachments, read status) |
| **visitors** | Website visitor tracking (ip_address, user_agent, visited_at) |
| **cache, sessions, jobs** | Laravel internals |

### Database Migrations

| Migration | Description |
|----------|-------------|
| `create_users_table` | Users table |
| `create_artworks_table` | Artworks table |
| `add_is_admin_to_users_table` | Admin role flag |
| `add_art_for_to_artworks_table` | Client tracking |
| `add_list_service_to_artworks_table` | Service tags |
| `add_username_to_users_table` | Username for login |
| `create_services_table` | Commission types |
| `create_visitors_table` | Visitor tracking |
| `create_messages_table` | Contact messages |
| `add_attachments_to_messages_table` | Message attachments |

## 🛠️ Development

### Available Commands

```bash
# Development server
php artisan serve

# Asset compilation
npm run dev      # Development with hot reload
npm run build    # Production build

# Database
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter TestName
```

## 📝 License

This project is private and proprietary.

## 👥 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'add feature'`)
4. Push to branch (`git push origin`)
5. Open Pull Request

---

**Built with ❤️ using Laravel 13**

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
