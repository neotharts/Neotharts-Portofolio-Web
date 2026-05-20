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
- *(Dalam pengembangan)*

---

## Deskripsi

Portfolio website untuk Neotharts, platform portfolio digital untuk artist dengan fitur manajemen artwork, visitor tracking, dan admin panel. Built with Laravel 13.

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
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
- **Password:** `password`

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── VisitorController.php
│   ├── Models/             # Eloquent models
│   └── Middleware/
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── public/
│   ├── img/               # Static images
│   └── storage/           # Linked storage (symlink)
├── resources/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript
│   └── views/             # Blade templates
├── routes/
│   └── web.php            # Web routes
└── storage/
    ├── app/               # File uploads
    └── logs/              # Application logs
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
- ✅ Artwork Management (CRUD)
- ✅ Visitor Tracking
- ✅ Responsive Design
- ✅ File Upload with Storage
- ✅ Authentication & Authorization
- ✅ Dynamic Homepage with Latest Artworks

## 📊 Database Schema

- **users**: Admin users with role management
- **artworks**: Portfolio artworks with categories
- **visitors**: Website visitor tracking
- **cache, sessions, jobs**: Laravel internals

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
