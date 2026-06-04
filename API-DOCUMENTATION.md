# API Endpoints & Workflows - MVP v1.0

## Authentication Routes

### Public Routes
- `GET /` - Welcome page
- `GET /login` - Login form
- `POST /login` - Submit login
- `GET /register` - Registration form
- `POST /register` - Submit registration
- `GET /forgot-password` - Forgot password form
- `POST /forgot-password` - Send reset email
- `GET /reset-password/{token}` - Reset form
- `POST /reset-password` - Submit new password

## Authenticated Routes

### Dashboard
- `GET /dashboard` - User dashboard with stats

### Reports
- `GET /reports` - List all user reports (paginated)
- `GET /reports/create` - Report creation form
- `POST /reports` - Create and generate report
- `GET /reports/{id}` - View report with progress
- `GET /reports/{id}/download` - Download PDF
- `DELETE /reports/{id}` - Delete report

## Workflow: Report Generation

### Step 1: User Creates Report
```
POST /reports
├─ Title: string (required)
├─ Subject: string (required, min 10 chars)
└─ Returns: Redirect to report.show with report ID
```

### Step 2: Report Service Orchestrates Generation
```
ReportService::generateReport($report)
├─ Status: pending → generating → completed/failed
├─ Progress: 0 → 100%
└─ Events:
    ├─ AIService::generateOutline()
    │  └─ OpenAI API call (~15 sec)
    ├─ For each chapter in outline:
    │  ├─ AIService::generateContent()
    │  │  └─ OpenAI API call (~10 sec per section)
    │  └─ ImageService::fetchImage()
    │     └─ Unsplash API call (~5 sec per image)
    └─ ExportService::generatePDF()
       └─ DomPDF generation
```

### Step 3: PDF Export
```
ExportService::generatePDF($report)
├─ Generate from Blade template
├─ Save to storage/reports/{report_id}.pdf
└─ Update report.pdf_path
```

### Step 4: User Download
```
GET /reports/{id}/download
├─ Check user authorization
├─ Return PDF file from storage
└─ Browser downloads file
```

## Data Model

### Report
```
{
  id: integer,
  user_id: integer (FK),
  title: string,
  subject: text,
  outline: json array,
  status: enum(pending|generating|completed|failed),
  error_message: text,
  progress: integer (0-100),
  pdf_path: string,
  created_at: timestamp,
  updated_at: timestamp
}
```

### ReportSection
```
{
  id: integer,
  report_id: integer (FK),
  title: string,
  content: text,
  order: integer,
  created_at: timestamp,
  updated_at: timestamp
}
```

### ReportImage
```
{
  id: integer,
  report_section_id: integer (FK),
  prompt: string,
  image_url: string,
  source: enum(unsplash|dalle|etc),
  order: integer,
  created_at: timestamp,
  updated_at: timestamp
}
```

## External API Integrations

### OpenAI (GPT-4)
```
POST https://api.openai.com/v1/chat/completions
├─ Model: gpt-4o-mini
├─ Temperature: 0.7
├─ Max tokens: 2000 (outline) / 1500 (content)
└─ Cost: ~$0.001 per request
```

### Unsplash
```
GET https://api.unsplash.com/search/photos
├─ Query: chapter title
├─ Per page: 1
├─ Orientation: landscape
└─ No rate limit (10,000 req/hour free tier)
```

## Error Handling

### API Errors
- OpenAI unavailable → Report status: failed
- Unsplash error → Use placeholder or skip image
- Database error → Return 500 with message

### User Errors
- Missing required fields → Validation error
- Unauthorized access → 403 Forbidden
- Report not found → 404 Not Found

## Authorization

All protected routes require:
1. User authentication (`auth` middleware)
2. Report ownership (via `ReportPolicy`)

Example:
```php
// Only report owner can view/download/delete
$this->authorize('view', $report);
```

## Performance Notes

- Report generation: 30-60 seconds
- No caching in MVP
- PDF generation: 2-5 seconds
- Database queries: minimal (proper eager loading)

## Future Enhancements (v1.1)

- Async processing (Laravel Queue)
- WebSocket progress updates
- Batch PDF generation
- Report templates
- Multi-language generation
- Custom styling/branding
- Webhook notifications
- API rate limiting
