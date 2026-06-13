# 📚 Texts to Book

Plateforme IA pour convertir des textes en livres structurés et professionnels avec génération automatique de contenu, images et export PDF.

**Table des matières**
- [Vue d’ensemble](#vue-densemble)
- [Architecture](#architecture)
- [Stack technique](#stack-technique)
- [Structure du projet](#structure-du-projet)
- [Installation](#installation)
- [Fonctionnalités](#fonctionnalités)
- [Développement](#développement)

---

## Vue d’ensemble

**Texts to Book** automatise la création de rapports et livres professionnels. L’utilisateur fournit un sujet, le système génère :

| Composant | Description |
|-----------|-------------|
| 🧠 Plan structuré | Architecture logique du contenu |
| ✍️ Contenu | Texte généré par IA |
| 🖼️ Images | Illustrations automatiques |
| 📊 Graphiques | Représentations visuelles |
| 📄 PDF | Export professionnel |

---

## Architecture

### 1. Flux de génération

```
User Input (sujet/prompt)
    ↓
[Orchestrator Service]
    ├─→ Outline Generation (IA)
    ├─→ Content Generation (IA)
    ├─→ Image Prompts Generation
    └─→ Workflow Execution
    ↓
[Content Processing Layer]
    ├─→ Image Generation/Fetching
    ├─→ Content Refinement
    └─→ Media Optimization
    ↓
[Report Building]
    ├─→ Assembly
    ├─→ Formatting
    └─→ Validation
    ↓
[Export Layer]
    ├─→ PDF Generation
    └─→ Storage & Delivery
```

### 2. Composants système

#### **Couche Présentation** (Frontend)
- Interface utilisateur réactive (Livewire)
- Formulaires et saisie utilisateur
- Aperçu en temps réel
- Gestion des statuts (loading, succès, erreurs)

#### **Couche Application** (Controllers)
- Routage des requêtes
- Validation des entrées
- Orchestration des services

#### **Couche Service** (Business Logic)
- `AIService` → Génération de contenu
- `ImageService` → Gestion des images
- `ReportService` → Construction des rapports
- `ExportService` → Génération PDF

#### **Couche Données** (Database)
- Persistance des rapports
- Historique des générations
- Métadonnées utilisateurs

#### **Couche Externe** (APIs)
- Google Gemini (texte)
- DALL-E / Stable Diffusion (images)
- Services de stockage

---

## Stack technique

### Backend
- **Framework** : Laravel 11+
- **ORM** : Eloquent
- **Queue** : Redis / Supervisor
- **Cache** : Redis

### Frontend
- **Framework** : Livewire
- **Styling** : TailwindCSS
- **Icons** : FontAwesome

### IA & Media
- **LLM** : Google Gemini
- **Images** : DALL-E / Stable Diffusion / Unsplash API
- **Export** : DomPDF / Snappy

### Infrastructure
- **Database** : MySQL 8+ / PostgreSQL
- **Storage** : Local / S3
- **Queue Workers** : Supervisor

---

## Structure du projet

```
texts_to_book/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ReportController.php
│   │   │   └── ExportController.php
│   │   └── Requests/
│   │       └── GenerateReportRequest.php
│   ├── Services/
│   │   ├── AIService.php
│   │   ├── ImageService.php
│   │   ├── ReportService.php
│   │   └── ExportService.php
│   ├── Models/
│   │   ├── Report.php
│   │   ├── ReportSection.php
│   │   └── ReportImage.php
│   └── Jobs/
│       ├── GenerateReportOutline.php
│       ├── GenerateReportContent.php
│       └── GenerateImages.php
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── report-creator.blade.php
│   │   │   ├── report-viewer.blade.php
│   │   │   └── export-panel.blade.php
│   │   └── layouts/
│   └── css/
├── database/
│   ├── migrations/
│   └── seeders/
└── config/
    ├── ai.php
    ├── images.php
    └── export.php
```

---

## Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+ ou PostgreSQL
- Redis (optionnel mais recommandé)

### Setup initial

```bash
# Cloner le projet
git clone <repo-url>
cd texts_to_book

# Installer dépendances PHP
composer install

# Installer dépendances Node
npm install

# Créer fichier .env
cp .env.example .env

# Générer clé app
php artisan key:generate

# Migrer base de données
php artisan migrate

# Démarrer le serveur
php artisan serve
npm run dev
```

---

## Fonctionnalités

### MVP (v1.0)
- ✅ Génération basique de plans
- ✅ Génération de contenu par section
- ✅ Intégration une API image
- ✅ Export PDF simple
- ✅ Interface utilisateur minimale

### Features (v1.1+)
- 🔄 Régénération de sections
- 🎨 Templates de rapports
- 🌍 Multi-langue (FR/EN)
- 📊 Graphiques automatiques
- 💾 Sauvegarde cloud
- 🔐 Authentification utilisateurs
- 📈 Limites de génération
- 💳 Abonnements (Stripe)

---

## Développement

### IA

Le système utilise actuellement l'API Google Gemini (via Google AI Studio). La configuration se trouve dans `config/ai.php` et le service principal est `app/Services/AIService.php`.

### Ajouter une source d’images

1. Créer fournisseur dans `app/Services/Image/Providers/`
2. Implémenter `ImageProviderInterface`
3. Configurer clés API
4. Intégrer dans `ImageService`

### Tests

```bash
# Tests unitaires
php artisan test

# Tests avec coverage
php artisan test --coverage
```

php artisan queue:work --tries=1 --timeout=120
