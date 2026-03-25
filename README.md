# 💄 MUA Management System (Laravel SaaS)

A modern, scalable **Makeup Artist (MUA) Management System** built with
Laravel.\
Designed to help MUA professionals manage bookings, services, customers,
schedules, and business growth efficiently.

------------------------------------------------------------------------

## 🚀 Features

### 🗓 Booking & Scheduling

-   Create and manage bookings بسهولة
-   Calendar view (FullCalendar integration)
-   Prevent double booking (conflict detection)

### 💼 Service Management

-   Create and manage services
-   Set pricing & duration
-   Categorize services (Wedding, Event, etc.)

### 👩‍💼 Customer Management

-   Store customer data
-   Track booking history
-   CRM-ready structure

### 📊 Dashboard & Reports

-   Revenue tracking
-   Booking statistics
-   Business insights

### 💳 Payment System

-   Manual & future gateway support
-   Invoice generation (PDF)
-   Payment tracking

### 📆 Calendar Integration

-   Visual schedule management
-   Real-time booking display
-   Ready for Google Calendar sync

### 🧾 SaaS Multi-Tenant System

-   Multiple users (MUA) supported
-   Data isolation per user
-   Subscription-based model

------------------------------------------------------------------------

## 🧱 Tech Stack

-   **Backend:** Laravel
-   **Frontend:** Blade + Tailwind CSS
-   **Database:** MySQL / SQLite
-   **Calendar:** FullCalendar.js
-   **PDF:** DOMPDF

------------------------------------------------------------------------

## ⚙️ Installation

``` bash
git clone https://github.com/your-repo/mua-system.git
cd mua-system

composer install
cp .env.example .env
php artisan key:generate

# setup database
php artisan migrate

npm install
npm run dev

php artisan serve
```

------------------------------------------------------------------------

## 🔐 Authentication

Uses Laravel Breeze: - Login / Register - Secure authentication flow

------------------------------------------------------------------------

## 💡 SaaS Flow

1.  User selects plan (Free / Pro / Premium)
2.  Registers account
3.  Gets onboarding experience
4.  Starts managing bookings
5.  Upgrade via billing system

------------------------------------------------------------------------

## 📂 Project Structure

    app/
     ├── Models
     ├── Services
     ├── Http/Controllers
     ├── Scopes

    resources/views/
    routes/
    database/

------------------------------------------------------------------------

## 📈 Future Improvements

-   Google Calendar Sync
-   WhatsApp Notifications
-   Payment Gateway (Midtrans / Stripe)
-   Mobile App (API ready)
-   AI-based scheduling

------------------------------------------------------------------------

## 🧠 Author

Built for modern MUA businesses & digital entrepreneurs.

------------------------------------------------------------------------

## 📄 License

This project is open-source and available under the MIT License.
![Laravel](https://img.shields.io/badge/Laravel-Framework-red)
![License](https://img.shields.io/badge/license-MIT-blue)