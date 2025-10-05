# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WhatsApp-based workout logging application with AI agent integration. Users send workout data via WhatsApp, which gets processed by AI agents and stored in a Laravel backend with a React frontend for data visualization.

**Tech Stack:**
- **Backend**: Laravel 12 with PHP 8.2+
- **Frontend**: React 19 with Inertia.js, TypeScript, Tailwind CSS v4, shadcn/ui
- **AI Integration**: Google ADK (Agent Development Kit) with multi-agent system
- **Database**: MySQL/SQLite with Eloquent ORM
- **External APIs**: WhatsApp Business API

## Development Commands

### Primary Development
```bash
# Start full development environment (recommended)
composer dev
# Runs: Laravel server + queue worker + logs + Vite in parallel

# Alternative: Start services individually
php artisan serve          # Laravel server (port 8000)
npm run dev                # Vite dev server for frontend
php artisan queue:listen   # Background job processing
php artisan pail           # Real-time log monitoring
```

### Testing
```bash
# Run all tests
composer test
# Equivalent to: php artisan config:clear && php artisan test

# Run specific test
php artisan test --filter=WorkoutLoggingTest

# Single test method
php artisan test --filter=test_can_log_workout_successfully
```

### Frontend Tools
```bash
npm run build              # Production build
npm run build:ssr          # Build with SSR support
npm run lint               # ESLint with auto-fix
npm run types              # TypeScript type checking
npm run format             # Prettier formatting
npm run format:check       # Check formatting only
```

### Database Management
```bash
# Fresh database with test data (recommended for development)
php artisan migrate:fresh --seed

# Individual operations
php artisan migrate         # Run pending migrations
php artisan db:seed        # Seed with test data
php artisan migrate:rollback
```

### Custom Development Commands
```bash
# Test AI agent integration without WhatsApp
php artisan adk:test {user_id?} {message?}
php artisan adk:test 2 "I did bench press 3x8 at 60kg"

# Inspect user data and workout history
php artisan user:inspect {user_id}
php artisan user:inspect 2

# Send daily workout reminders
php artisan reminders:send-daily
```

## Architecture Overview

### Multi-Service Architecture
The application consists of two main services that communicate via HTTP APIs:

1. **Laravel Backend** (port 8000)
   - Handles WhatsApp webhooks
   - Stores workout data and user profiles
   - Provides APIs for AI agent
   - Serves React frontend

2. **ADK AI Service** (port 8001, separate Python application)
   - Processes natural language workout messages
   - Multi-agent system (fitness_coach, workout_logger, progress_tracker, motivator)
   - Calls Laravel APIs to log workouts and retrieve data

### Data Flow Example
```
User: "I did bench press 3x8 at 60kg" (WhatsApp)
  ↓
WhatsApp Business API → Laravel Webhook
  ↓
Laravel → ADK AI Service (with user context)
  ↓
ADK processes message → workout_logger agent
  ↓
ADK calls Laravel API: POST /api/workouts/log
  ↓
Laravel saves workout → Updates user streak
  ↓
Response: "💪 Logged! Bench Press 3×8 @ 60kg. New PR!"
  ↓
Laravel → WhatsApp → User
```

### Database Schema (Key Tables)
- **users**: User profiles, fitness goals, streak tracking
- **conversations**: WhatsApp message history
- **ai_interactions**: AI processing logs and performance metrics
- **exercises**: Exercise library with aliases for flexible matching
- **workouts**: Workout sessions with date, duration, totals
- **workout_exercises**: Individual sets with reps, weight, PR flags
- **progress_metrics**: Body measurements over time
- **workout_plans**: Predefined workout templates
- **workout_plan_exercises**: Exercises within workout plans

### Service Layer Pattern
Business logic is organized in dedicated service classes:

- **AdkAgentService**: Communicates with AI agents, handles session management
- **WhatsAppService**: Manages WhatsApp API communication (messages, buttons, templates)
- **WorkoutService**: Handles workout logging, streak updates, PR detection
- **AnalyticsService**: Calculates progress insights, plateau detection
- **ResponseFormatterService**: Formats data into conversational messages

## API Endpoints

### WhatsApp Webhook (Public)
```
GET  /api/webhook/whatsapp   # Webhook verification
POST /api/webhook/whatsapp   # Receive incoming messages
```

### Internal APIs (ADK Agent Access)
Protected by `ValidateAdkRequest` middleware checking `X-ADK-API-Key` header:
```
POST /api/workouts/log       # Log workout data
GET  /api/workouts/history   # Get workout history
GET  /api/workouts/summary   # Get workout statistics
GET  /api/workout-plans/active    # Get user's active plan
GET  /api/workout-plans/today     # Get today's planned workout
```

## Development Patterns

### Model Relationships
Models use extensive Eloquent relationships. Key patterns:
- User hasMany workouts, conversations, progressMetrics
- Workout belongsTo user, hasMany workoutExercises
- WorkoutExercise belongsTo workout and exercise
- Conversation belongsTo user, hasOne aiInteraction

### Service Injection
Controllers are thin and delegate to services:
```php
public function log(Request $request)
{
    $workout = app(WorkoutService::class)->logWorkout(
        User::find($request->user_id),
        $request->workout_data
    );
    return response()->json(['workout' => $workout]);
}
```

### Factory Pattern for Testing
Use factories for generating test data:
```php
User::factory()->create(['fitness_goal' => 'build_muscle']);
Workout::factory()->for($user)->create();
WorkoutExercise::factory()->for($workout)->create();
```

## Environment Configuration

### Required Laravel .env Variables
```
# Database
DB_CONNECTION=mysql
DB_DATABASE=workout_logger

# WhatsApp Business API
WHATSAPP_TOKEN=your_token
WHATSAPP_PHONE_NUMBER_ID=your_number_id
WHATSAPP_VERIFY_TOKEN=your_verify_token

# ADK Service Integration
ADK_API_URL=http://localhost:8001
ADK_API_KEY=shared_secret_key
```

### ADK Service Configuration
The ADK service runs separately and requires its own .env configuration in the `fitness_coach_adk/` directory:
```
GOOGLE_CLOUD_PROJECT=your-project
GOOGLE_CLOUD_LOCATION=us-central1
LARAVEL_API_URL=http://localhost:8000/api
LARAVEL_API_KEY=shared_secret_key
```

## Test Users (Created by DatabaseSeeder)
1. **John Beginner** (ID: 1, Phone: 31612345001) - New user, no workout history
2. **Sarah Lifter** (ID: 2, Phone: 31612345002) - Active user with workout history and streak
3. **Mike Strong** (ID: 3, Phone: 31612345003) - Advanced user with long streak
4. **Lazy Larry** (ID: 4, Phone: 31612345004) - Inactive user for testing reminders

## Key Development Notes

### Authentication
- WhatsApp webhooks use verify token validation
- ADK API calls require shared secret in header
- Web interface uses Laravel Sanctum for session management

### Testing Strategy
- Feature tests cover WhatsApp webhook processing
- Unit tests for service layer business logic
- Use factories for generating realistic test data
- Database uses SQLite in-memory for testing

### Frontend Architecture
- React components in `resources/js/components/`
- TypeScript with strict type checking
- Inertia.js for seamless Laravel-React integration
- shadcn/ui component library with Tailwind CSS v4
- Server-side rendering support available

### AI Agent Integration
- Session-based conversations tied to WhatsApp numbers
- Context includes user profile and recent workout history
- Multi-agent routing based on message intent
- Tool calls logged for debugging and analytics
- Error handling with fallback responses