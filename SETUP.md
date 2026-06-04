# MVP Setup Guide - Texts to Book v1.0

## Prerequisites

- PHP 8.2+
- PostgreSQL 12+ or MySQL 8+
- Node.js 18+
- Composer
- Git

## Installation Steps

### 1. Environment Setup

```bash
cp .env.example .env
```

Edit `.env` with your database and API credentials:
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `OPENAI_API_KEY` - Get from https://platform.openai.com/api-keys
- `UNSPLASH_API_KEY` - Get from https://unsplash.com/oauth/applications

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Database Setup

```bash
php artisan key:generate
php artisan migrate
```

### 4. Build Assets

```bash
npm run build
```

### 5. Start Development Server

```bash
php artisan serve
# In another terminal:
npm run dev
```

Visit `http://localhost:8000` and register an account.

## Configuration

### API Keys Required

1. **OpenAI API Key** (for text generation)
   - Sign up at https://platform.openai.com
   - Create API key
   - Set in `.env`: `OPENAI_API_KEY=sk-...`

2. **Unsplash API Key** (for images)
   - Register at https://unsplash.com/oauth/applications
   - Create new application
   - Set in `.env`: `UNSPLASH_API_KEY=...`

### Database

PostgreSQL recommended for production. SQLite for local development:

```bash
# For SQLite
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

## Features Implemented (MVP v1.0)

✅ User Authentication (Laravel Breeze)
✅ Report Creation & Management
✅ AI-powered Outline Generation (OpenAI)
✅ Content Generation per Section
✅ Image Fetching (Unsplash API)
✅ PDF Export (DomPDF)
✅ Responsive Web UI (TailwindCSS)

## File Structure

```
texts_to_book/
├── app/
│   ├── Http/Controllers/      # API & route handlers
│   ├── Models/                 # Database models
│   ├── Services/               # Business logic
│   └── Policies/               # Authorization
├── config/                     # Configuration files
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/
├── resources/
│   ├── views/                  # Blade templates
│   ├── css/                    # TailwindCSS
│   └── js/                     # Frontend JS
├── routes/                     # URL routes
└── storage/                    # Generated PDFs
```

## Workflow

1. User registers & logs in
2. Creates new report with topic
3. System generates outline (OpenAI)
4. Content is generated for each section
5. Images are fetched (Unsplash)
6. PDF is created and available for download

## Common Commands

```bash
# Serve application
php artisan serve

# Run migrations
php artisan migrate

# Create test data
php artisan tinker

# Build frontend
npm run build

# Development mode
npm run dev
```

## Troubleshooting

**404 errors**: Run `composer dump-autoload`

**Database connection**: Verify `.env` database settings

**Image not loading**: Check UNSPLASH_API_KEY

**Content generation slow**: Normal - OpenAI API takes ~30 seconds per report

## Next Steps (v1.1+)

- Async job processing (Laravel Queue)
- Report templates
- Multi-language support
- User subscription system
- Enhanced analytics
- Custom branding options

## Support

For issues, check the README.md or create an issue in the repository.
