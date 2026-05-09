# Gemana

### *Fellowship through technology.*

**An open-source Community Management System built for Australian Non-Profits.**

[![Laravel](https://img.shields.io/badge/Laravel-v13.8-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![Livewire](https://img.shields.io/badge/Livewire-v4.3.0-FB70A9?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-77C1D2?style=flat-square&logo=alpine.js&logoColor=white)](https://alpinejs.dev/)
[![License: MIT](https://img.shields.io/badge/License-MIT-8B5CF6?style=flat-square)](LICENSE)
[![Built in Australia](https://img.shields.io/badge/Built_in-🇦🇺_Australia-00843D?style=flat-square)](https://intuitionlab.com.au)

---

## 🔮 What is Gemana?

**Gemana** *(Old English: fellowship, community)* is a robust, high-standard infrastructure built by [IntuitionLab Pty Ltd](https://intuitionlab.com.au) to empower small-to-medium Australian NGOs with expert-level digital management tools.

> Most management systems are generic. Gemana is built from the ground up to respect the unique **legal and social requirements** of the Australian non-profit sector.

---

## ✨ Key Features

| Feature | Description |
|---|---|
| 🇦🇺 **Australian Compliance** | Integrated ABN lookup and DGR (Deductible Gift Recipient) status verification — out of the box. |
| 🧾 **Automated Tax Receipts** | Generate professional, legally compliant PDF tax receipts automatically upon donation. |
| 🧙 **Setup Wizard** | A guided onboarding experience led by the *Gemana Sprite* — get your org tax-ready in minutes. |
| 🤖 **AEO & SEO Optimised** | Semantic architecture designed to be read by AI answer engines and traditional search engines alike. |
| 🛡️ **High Privacy** | Secure member journey tracking and contribution history with a focus on data integrity. |

---

## 🛠️ The Tech Stack — TALL

Gemana is built with the **TALL Stack**, delivering a reactive, modern user experience with a clean developer environment.

```
T  —  Tailwind CSS    Utility-first CSS for rapid, consistent UI development
A  —  Alpine.js       Lightweight JavaScript for browser-side interactions
L  —  Laravel         The PHP framework for web artisans  (v13.8)
L  —  Livewire        Dynamic interfaces without leaving PHP  (v4.3.0)
```

---

## 🚀 Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** `8.2+`
- **Composer**
- **Node.js** & **NPM**

---

### 🆕 Starting from Scratch

If you are initialising a new project from an empty directory in your VS Code terminal:

**Step 1 — Create the Laravel project:**
```bash
composer create-project laravel/laravel gemana
cd gemana
```

**Step 2 — Install the TALL Stack foundation:**
```bash
composer require livewire/livewire
npm install -D tailwindcss postcss autoprefixer vite laravel-vite-plugin
npm exec tailwindcss -- init -p
```

> **Note:** If `npm exec` fails, manually create `tailwind.config.js` and `postcss.config.js` in your project root.

**Step 3 — Initialise Git and push to GitHub:**
```bash
git init
git add .
git commit -m "Initial Laravel/TALL stack commit for Gemana"
git branch -M main
git remote add origin https://github.com/intuitionlab/gemana.git
git push -u origin main
```

---

### 🤝 Standard Installation (For Contributors)

**1. Clone the repository:**
```bash
git clone https://github.com/intuitionlab/gemana.git
cd gemana
```

**2. Install dependencies:**
```bash
composer install
npm install && npm run build
```

**3. Environment setup:**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Run migrations:**
```bash
php artisan migrate
```

---

## 🎨 Brand & Identity

Gemana is personified by the **Sleek Sprite** — a modern pixie-wizard helper rendered in *electric blue* and *vibrant purple*. This character guides administrators through complex tasks, turning data management into magic.

```
  ✦  Electric Blue  #3B82F6
  ✦  Vibrant Purple #8B5CF6
  ✦  Character      The Gemana Sprite
```

---

## 🏗️ Architecture

### Folder Structure

```
gemana/
├── app/
│   ├── Core/                  # Engine — module loader, theme manager, role system
│   ├── Modules/               # Each module is self-contained
│   │   ├── Members/
│   │   ├── Donations/
│   │   ├── Events/
│   │   ├── Blog/
│   │   ├── Documents/
│   │   ├── Notifications/
│   │   ├── Volunteering/
│   │   └── Newsletter/
│   └── Http/Controllers/
├── themes/
│   ├── gemana-default/        # Shipped theme
│   └── [custom-themes]/
├── config/
│   └── modules.php            # Module registry
└── database/migrations/
```

### Each Module Contains

```
Modules/Members/
├── ModuleServiceProvider.php  # Self-registers with Core
├── Models/
├── Http/Controllers/
├── Livewire/                  # Livewire components
├── Views/
├── Routes/
├── Migrations/
└── module.json                # Name, version, dependencies, toggle state
```

The `module.json` is the key — it's what the Super-Admin dashboard reads to show the on/off toggle, version, and description, just like WordPress plugins.

### Roles & Permissions

| Role | Access | 2FA |
|---|---|---|
| **Super-Admin** | Full system access, module & theme management | ✅ Required |
| **Admin** | Manage org content, members, events, donations | ✅ Required |
| **Team** | Assigned areas only (e.g. events coordinator) | ✅ Required |
| **Volunteer** | Rostering, check-ins, limited profile | ⚙️ Optional |
| **Member** | Tiered (General, Financial, Life, Honorary) | ⚙️ Optional |
| **Public** | Unauthenticated — sees public-facing site only | — |

Built on [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) — the industry standard for Laravel role/permission management.

### Theme System

Themes live in `/themes` and are just Laravel Blade view sets with their own CSS/JS assets. The active theme is set in the Super-Admin dashboard and stored in the database. The Core resolves views from the active theme folder first, falling back to defaults — same pattern as WordPress child themes.

### Suggested Build Order

```
Phase 1 — Foundation
  ✦ Core engine (module loader, theme resolver, role system)
  ✦ Super-Admin dashboard shell
  ✦ Default theme scaffold (public site + member portal layout)

Phase 2 — Identity & Access
  ✦ Members module (registration, profiles, membership levels)
  ✦ Auth (login, portal access by role)

Phase 3 — Money & Compliance
  ✦ Donations & Tax Receipts module (ABN, DGR, PDF generation)

Phase 4 — Engagement
  ✦ Events, Blog, Document Library

Phase 5 — Communication
  ✦ Email Notifications, Newsletter

Phase 6 — Volunteering
  ✦ Rostering, availability, check-ins
```

---

## 📄 License & Intellectual Property

**Software:** Distributed under the [MIT License](LICENSE). You are free to use, modify, and distribute the code.

**Branding:** The names **"Gemana"**, **"IntuitionLab"**, and the associated **"Sleek Sprite"** artwork are trademarks of IntuitionLab Pty Ltd. While you are free to use the software, please respect our branding guidelines to prevent market confusion.

---

Built with ❤️ by [**IntuitionLab**](https://intuitionlab.com.au) · Perth, Western Australia 🇦🇺

*Empowering communities. One organisation at a time.*