# Laravel Workout Logger - Complete Project Documentation

## 📁 All Files Created

### **Configuration Files**

```
├── .env                              # Environment variables (API keys, database config)
├── config/services.php               # Third-party service configs (WhatsApp, ADK)
```

### **Database**

```
├── database/
│   ├── migrations/
│   │   ├── *_create_users_table.php              # User profiles & fitness data
│   │   ├── *_create_conversations_table.php      # WhatsApp message history
│   │   ├── *_create_ai_interactions_table.php    # AI agent logs & performance
│   │   ├── *_create_exercises_table.php          # Exercise library (bench press, squats, etc.)
│   │   ├── *_create_workouts_table.php           # Workout sessions
│   │   ├── *_create_workout_exercises_table.php  # Individual sets/reps/weight data
│   │   └── *_create_progress_metrics_table.php   # Body measurements over time
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php        # Master seeder (runs all others)
│   │   ├── ExerciseSeeder.php        # Populates 25 common exercises
│   │   ├── UserSeeder.php            # Creates 4 test users
│   │   └── WorkoutSeeder.php         # Creates sample workout data
│   │
│   └── factories/
│       ├── UserFactory.php           # Generate fake users for testing
│       ├── WorkoutFactory.php        # Generate fake workouts
│       └── WorkoutExerciseFactory.php # Generate fake exercise data
```

### **Models** (Database representations)

```
├── app/Models/
│   ├── User.php                      # User account & fitness profile
│   ├── Conversation.php              # WhatsApp messages sent/received
│   ├── AiInteraction.php             # AI agent processing logs
│   ├── Exercise.php                  # Exercise library (with aliases for matching)
│   ├── Workout.php                   # Workout session (date, duration, totals)
│   ├── WorkoutExercise.php           # Individual sets (3x8 @ 80kg)
│   └── ProgressMetric.php            # Body measurements (weight, measurements)
```

### **Services** (Business logic)

```
├── app/Services/
│   ├── AdkAgentService.php           # Sends messages to AI agent, handles responses
│   ├── WhatsAppService.php           # Sends messages to WhatsApp (text, buttons, templates)
│   ├── WorkoutService.php            # Logs workouts, updates streaks, checks PRs
│   ├── AnalyticsService.php          # Calculates progress, detects plateaus
│   └── ResponseFormatterService.php  # Formats data into nice WhatsApp messages
```

### **Controllers** (Handle HTTP requests)

```
├── app/Http/Controllers/
│   ├── WhatsAppWebhookController.php # Receives WhatsApp messages from Meta
│   ├── WorkoutController.php         # API endpoints for AI agent (log workouts, get history)
│   └── ExerciseController.php        # Search exercises API
```

### **Middleware** (Security/Authentication)

```
├── app/Http/Middleware/
│   └── AdkAuthMiddleware.php         # Checks API key from AI agent requests
```

### **Commands** (Terminal commands)

```
├── app/Console/Commands/
│   ├── TestAdkAgent.php              # Test AI agent without WhatsApp
│   ├── InspectUserData.php           # View user workouts and data
│   └── SendDailyReminders.php        # Send workout reminders
```

### **Routes**

```
├── routes/
│   └── api.php                       # API endpoints
```

### **Tests**

```
├── tests/Feature/
│   └── AdkIntegrationTest.php        # Automated tests for AI integration
```

---

## 🎯 What Each File Does

### **Configuration Files**

#### `.env`

- **What:** Secret keys and settings
- **Why:** Stores passwords, API keys that shouldn't be in code
- **Contains:** Database password, WhatsApp token, AI service URL

#### `config/services.php`

- **What:** Settings for external services
- **Why:** Central place to configure WhatsApp and AI agent
- **Contains:** WhatsApp API credentials, AI agent URL and API key

---

### **Database Migrations**

#### `*_create_users_table.php`

- **What:** Creates user accounts table
- **Why:** Store user profiles (name, weight, fitness goals, streak)
- **When used:** When someone first messages the bot

#### `*_create_conversations_table.php`

- **What:** Creates WhatsApp message history table
- **Why:** Keep record of every message sent/received for debugging
- **When used:** Every WhatsApp interaction

#### `*_create_ai_interactions_table.php`

- **What:** Creates AI processing logs table
- **Why:** Track what the AI did (which agent, tokens used, response time)
- **When used:** Every time AI processes a message

#### `*_create_exercises_table.php`

- **What:** Creates exercise library table
- **Why:** Store all exercises (Bench Press, Squats) with aliases
- **When used:** When AI needs to match "bench" to "Bench Press"

#### `*_create_workouts_table.php`

- **What:** Creates workout sessions table
- **Why:** Group exercises into sessions (Monday's workout, Tuesday's workout)
- **When used:** When user logs any exercise

#### `*_create_workout_exercises_table.php`

- **What:** Creates individual set records table
- **Why:** Store each set's details (Set 1: 8 reps @ 80kg)
- **When used:** When logging "3 sets of bench press"

#### `*_create_progress_metrics_table.php`

- **What:** Creates body measurements table
- **Why:** Track weight, body fat %, measurements over time
- **When used:** When user logs weight or takes measurements

---

### **Seeders**

#### `DatabaseSeeder.php`

- **What:** Master file that runs all other seeders
- **Why:** One command to populate entire database
- **Run with:** `php artisan db:seed`

#### `ExerciseSeeder.php`

- **What:** Adds 25 common exercises to database
- **Why:** Pre-populate so AI can recognize exercises immediately
- **Creates:** Bench Press, Squat, Deadlift, Pull-ups, etc.

#### `UserSeeder.php`

- **What:** Creates 4 test users with different profiles
- **Why:** Test the system without real WhatsApp users
- **Creates:** John Beginner, Sarah Lifter, Mike Strong, Lazy Larry

#### `WorkoutSeeder.php`

- **What:** Adds sample workout history for one test user
- **Why:** Test progress tracking features with realistic data
- **Creates:** 3 workouts (push, pull, legs) from past week

---

### **Factories**

#### `UserFactory.php`

- **What:** Template for generating random users
- **Why:** Create 100 fake users for testing
- **Used in:** Automated testing

#### `WorkoutFactory.php`

- **What:** Template for generating random workouts
- **Why:** Create workout history quickly
- **Used in:** Testing analytics features

#### `WorkoutExerciseFactory.php`

- **What:** Template for generating random sets/reps
- **Why:** Fill workouts with exercise data
- **Used in:** Testing progress calculations

---

### **Models**

#### `User.php`

- **What:** Represents a user account
- **Why:** Access user data (profile, workouts, conversations)
- **Relationships:** hasMany workouts, conversations, progressMetrics, aiInteractions
- **Example:** `$user->workouts` gets all their workouts

#### `Conversation.php`

- **What:** Represents a WhatsApp message
- **Why:** Store and retrieve message history
- **Relationships:** belongsTo user, hasOne aiInteraction
- **Example:** `$user->conversations` gets all their messages

#### `AiInteraction.php`

- **What:** Represents one AI processing event
- **Why:** Track AI performance and debug issues
- **Relationships:** belongsTo user, belongsTo conversation
- **Example:** See which agent handled a message and how long it took

#### `Exercise.php`

- **What:** Represents one exercise (Bench Press)
- **Why:** Store exercise details and search by name/alias
- **Relationships:** hasMany workoutExercises
- **Example:** Find "bench" matches "Bench Press"

#### `Workout.php`

- **What:** Represents one workout session
- **Why:** Group exercises together by date
- **Relationships:** belongsTo user, hasMany workoutExercises
- **Example:** Monday's workout with bench press and squats

#### `WorkoutExercise.php`

- **What:** Represents one set of one exercise
- **Why:** Store exact details (8 reps @ 80kg)
- **Relationships:** belongsTo workout, belongsTo exercise
- **Example:** Set 1 of Bench Press on Monday

#### `ProgressMetric.php`

- **What:** Represents body measurements on a date
- **Why:** Track weight loss/gain over time
- **Relationships:** belongsTo user
- **Example:** Weekly weigh-ins

---

### **Services**

#### `AdkAgentService.php`

**Purpose:** Communicates with AI agent

**Methods:**

##### `processMessage(User $user, Conversation $conversation): void`

- Sends user message to ADK agent
- Passes user context (profile, recent workouts, streak)
- Saves AI interaction to database
- Sends response back to user via WhatsApp
- **Args:** User model, Conversation model
- **Returns:** void (sends WhatsApp message as side effect)

##### `getUserContext(User $user): array` (private)

- Builds context data for AI agent
- **Returns:** Array with user_profile, recent_workouts (last 5), streak, last_workout

---

#### `WhatsAppService.php`

**Purpose:** Handle WhatsApp Business API communication

**Methods:**

##### `sendMessage(string $to, string $message): ?string`

- Sends text message to WhatsApp user
- Saves outgoing message to conversations table
- **Args:** Phone number (31612345678), Message text
- **Returns:** WhatsApp message ID or null

##### `sendInteractiveButtons(string $to, string $bodyText, array $buttons): ?string`

- Sends message with quick reply buttons
- **Args:** Phone number, Body text, Array of buttons
- **Example:** `[['id' => 'btn1', 'title' => 'Log Workout']]`
- **Returns:** Message ID or null

##### `sendTemplate(string $to, string $templateName, array $parameters): ?string`

- Sends pre-approved template message (for proactive notifications)
- **Args:** Phone number, Template name, Array of parameter values
- **Returns:** Message ID or null

---

#### `WorkoutService.php`

**Purpose:** Business logic for workout logging and tracking

**Methods:**

##### `logWorkout(User $user, array $workoutData): Workout`

- Creates workout session and logs exercises
- Updates workout totals (volume, sets)
- Checks for personal records
- Updates user streak
- **Args:** User model, Array with exercises data
- **Returns:** Workout model

##### `getWorkoutSummary(User $user, int $days = 7): array`

- Calculates training statistics
- **Args:** User model, Number of days to analyze
- **Returns:** Array with total_workouts, total_volume_kg, total_sets, average_duration, workout_days, exercises_performed

##### `addExerciseToWorkout(Workout $workout, array $exerciseData): void` (private)

- Adds exercise sets to workout
- Finds or creates exercise in database
- **Args:** Workout model, Exercise data array

##### `updateWorkoutTotals(Workout $workout): void` (private)

- Recalculates total volume and sets
- Updates duration if start/end times exist

##### `checkForPR(int $userId, int $exerciseId, array $exerciseData): void` (private)

- Compares current performance to previous bests
- Flags personal records

##### `updateUserStreak(User $user): void` (private)

- Updates consecutive workout days streak
- Resets if gap > 1 day

---

#### `AnalyticsService.php`

**Purpose:** Advanced workout analytics and insights

**Methods:**

##### `getMostImprovedExercises(User $user, int $days = 30): array`

- Calculates improvement percentage for each exercise
- **Returns:** Array of top 5 exercises with improvement_percent

##### `detectPlateau(User $user, int $exerciseId): bool`

- Checks if user hasn't increased weight in 3 consecutive workouts
- **Returns:** Boolean (true if plateaued)

##### `getMuscleGroupBalance(User $user, int $days = 30): array`

- Shows distribution of training across muscle groups
- **Returns:** Array ['chest' => 45, 'back' => 38, 'legs' => 52...]

##### `getProgressiveOverloadScore(User $user, int $days = 30): float`

- Calculates percentage of exercises showing progression
- **Returns:** Float percentage (0-100)

---

#### `ResponseFormatterService.php`

**Purpose:** Format data into conversational WhatsApp messages

**Methods:**

##### `formatWorkoutLogged(Workout $workout): string`

- Creates confirmation message with exercise details, volume, PRs
- **Returns:** Formatted string with emojis

##### `formatWeeklySummary(User $user, array $summary): string`

- Creates weekly progress report
- **Returns:** Formatted string

##### `formatProgressInsights(User $user): string`

- Combines multiple analytics into insights message
- **Returns:** Formatted string with improvements, progressive overload score, muscle balance

---

### **Controllers**

#### `WhatsAppWebhookController.php`

##### `verify(Request $request)`

- Handles WhatsApp webhook verification
- **Route:** GET `/api/webhook/whatsapp`
- **Auth:** None (public)
- **Returns:** Challenge string or 403

##### `webhook(Request $request)`

- Receives incoming WhatsApp messages
- Routes to `handleIncomingMessage()` or `handleMessageStatus()`
- **Route:** POST `/api/webhook/whatsapp`
- **Auth:** None (public)
- **Returns:** JSON `{status: 'ok'}`

##### `handleIncomingMessage($value)` (private)

- Creates/gets user from WhatsApp number
- Saves conversation to database
- Calls ADK service to process message

##### `handleMessageStatus($value)` (private)

- Updates message delivery status (delivered/read)

---

#### `WorkoutController.php`

##### `log(Request $request)`

- Logs workout from ADK agent
- **Route:** POST `/api/workouts/log`
- **Auth:** `adk.auth` middleware
- **Body:** `{user_id: int, workout_data: {...}}`
- **Returns:** JSON with workout and success message

##### `history(Request $request)`

- Gets workout history
- **Route:** GET `/api/workouts/history?user_id=2&days=30`
- **Auth:** `adk.auth` middleware
- **Returns:** JSON array of workouts

##### `summary(Request $request)`

- Gets workout statistics
- **Route:** GET `/api/workouts/summary?user_id=2&days=7`
- **Auth:** `adk.auth` middleware
- **Returns:** JSON with stats

---

#### `ExerciseController.php`

##### `search(Request $request)`

- Searches exercises by name or alias
- **Route:** GET `/api/exercises/search?q=bench`
- **Auth:** `adk.auth` middleware
- **Returns:** JSON array of matching exercises

---

### **Middleware**

#### `AdkAuthMiddleware.php`

- **Purpose:** Authenticate requests from ADK service
- **How it works:** Checks `X-ADK-API-Key` header or Bearer token, compares to `config('services.adk.api_key')`, returns 401 if invalid
- **Usage:** Applied to internal API routes

---

### **Commands**

#### `TestAdkAgent.php`

```bash
php artisan adk:test {user_id?} {message?}

# Examples:
php artisan adk:test 2 "What did I do yesterday?"
php artisan adk:test 1 "I did bench press 3x8 at 60kg"
```

- Creates test conversation record
- Calls ADK service
- Shows agent response, tokens used, response time

#### `InspectUserData.php`

```bash
php artisan user:inspect {user_id}

# Example:
php artisan user:inspect 2
```

- Shows user profile details
- Lists recent workouts with exercises
- Shows recent AI interactions

#### `SendDailyReminders.php`

```bash
php artisan reminders:send-daily
```

- Sends workout reminders to active users
- Scheduled to run daily at 8 AM
- Checks streak status and sends appropriate message

---

### **Routes** (`routes/api.php`)

#### Public Routes (WhatsApp calls these)

```
GET  /api/webhook/whatsapp  → Verify webhook
POST /api/webhook/whatsapp  → Receive messages
```

#### Protected Routes (AI agent calls these)

```
Middleware: adk.auth

POST /api/workouts/log      → Log workout
GET  /api/workouts/history  → Get workout list
GET  /api/workouts/summary  → Get statistics
GET  /api/exercises/search  → Find exercises
```

#### Test Route (Local development only)

```
POST /api/test/adk  → Test AI integration
```

---

### **Tests**

#### `AdkIntegrationTest.php`

- **What:** Automated tests for AI integration
- **Why:** Ensure AI logging works correctly
- **Tests:** Can query workout history, can log new workout, handles beginner queries appropriately

---

## 🤖 ADK Python Service

### **What is it?**

Python application that runs your AI agents using Google's framework

### **Location:**

`fitness_coach_adk/` (separate folder from Laravel)

### **Structure:**

```
fitness_coach_adk/
├── .env                    # API keys, Laravel URL
├── requirements.txt        # Python packages needed
└── fitness_coach/
    ├── agent.py           # AI agent definitions
    └── tools.py           # Functions that call Laravel
```

### **What it does:**

1. Receives messages from Laravel
2. Processes with AI (Gemini model)
3. Decides which specialist agent to use:
    - **workout_logger**: Parses "I did bench 3x8 @ 60kg"
    - **progress_tracker**: Analyzes workout history
    - **motivator**: Provides encouragement
    - **fitness_coach**: Coordinates everything
4. Calls Laravel APIs to log workouts or get data
5. Returns response to Laravel

### **How Laravel calls it:**

```
Laravel → POST http://localhost:8000/run
        → ADK processes message
        → ADK calls Laravel APIs if needed
        → Returns response
Laravel → Sends response to WhatsApp
```

### **How ADK calls Laravel:**

```
POST /api/workouts/log      (from log_workout tool)
GET  /api/workouts/history  (from get_workout_history tool)
GET  /api/workouts/summary  (from get_workout_summary tool)
GET  /api/exercises/search  (from search_exercises tool)
```

### **Running it:**

```bash
cd fitness_coach_adk
adk api_server  # Starts on port 8000
```

### **Key concept:**

Uses `tool_context` to know WHO is talking:

- Laravel passes `user_id` in session state
- ADK automatically injects it into tools
- Tools use it to query the right user's data

---

## 🔄 Complete Flow

### **Example: User logs a workout**

1. **User sends WhatsApp message:** "I did bench press 3x8 at 60kg"

2. **WhatsApp → Laravel webhook:**
    - `WhatsAppWebhookController` receives it
    - Creates/finds user by phone number
    - Saves to `conversations` table

3. **Laravel → ADK:**
    - `AdkAgentService` sends message to AI
    - Includes user context (profile, recent workouts)

4. **ADK processes:**
    - `fitness_coach` agent decides: "This is workout logging"
    - Routes to `workout_logger` specialist agent
    - Agent parses: exercise="Bench Press", sets=3, reps=8, weight=60

5. **ADK → Laravel API:**
    - Calls `log_workout` tool
    - Tool calls `POST /api/workouts/log`

6. **Laravel logs workout:**
    - `WorkoutController` receives request
    - `WorkoutService` creates workout record
    - Saves to `workouts` and `workout_exercises` tables
    - Updates user streak
    - Checks for PR

7. **Response flows back:**
    - Laravel → ADK → Laravel → WhatsApp → User
    - "💪 Logged! Bench Press 3×8 @ 60kg. That's 5kg more than last week!"

---

## 📊 Data Flow Example

### **User:** Sarah (ID: 2, Phone: 31612345002)

### **Message:** "What did I do this week?"

### **Flow:**

1. WhatsApp → `conversations` table (saves message)
2. Laravel → ADK with `user_id: 2` in state
3. ADK `fitness_coach` → routes to `progress_tracker`
4. ADK calls `get_workout_history(days=7)`
5. Tool extracts `user_id` from context → calls Laravel
6. Laravel `WorkoutController` → queries database
7. Returns: 3 workouts (Push, Pull, Legs)
8. ADK formats response
9. Laravel saves to `ai_interactions` table
10. Laravel → WhatsApp: "This week you crushed it! 💪..."

---

## 🚀 Quick Start Guide

### **First Time Setup:**

```bash
# 1. Setup Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# 2. Start Laravel
php artisan serve  # Port 8000

# 3. Setup ADK (different terminal)
cd fitness_coach_adk
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
adk api_server  # Port 8001

# 4. Test
php artisan adk:test 2 "I did squats 5x5 at 100kg"
```

### **Environment Variables Needed:**

#### **Laravel `.env`:**

```
DB_DATABASE=workout_logger
WHATSAPP_TOKEN=your_token
WHATSAPP_PHONE_NUMBER_ID=your_number
WHATSAPP_VERIFY_TOKEN=random_string
ADK_API_URL=http://localhost:8001
ADK_API_KEY=shared_secret_123
```

#### **ADK `.env`:**

```
GOOGLE_CLOUD_PROJECT=your-project
GOOGLE_CLOUD_LOCATION=us-central1
GOOGLE_GENAI_USE_VERTEXAI=True
LARAVEL_API_URL=http://localhost:8000/api
LARAVEL_API_KEY=shared_secret_123
```

---

## 📋 Database Schema Summary

### **users**

- User profiles, fitness goals, streak tracking
- Key fields: `whatsapp_number`, `fitness_goal`, `streak_days`

### **conversations**

- WhatsApp message history
- Key fields: `whatsapp_message_id`, `direction`, `message_content`, `status`

### **ai_interactions**

- AI processing logs
- Key fields: `agent_name`, `user_input`, `agent_response`, `tokens_used`

### **exercises**

- Exercise library with aliases
- Key fields: `name`, `aliases`, `muscle_group`, `equipment`

### **workouts**

- Workout sessions
- Key fields: `workout_date`, `total_volume_kg`, `total_sets`

### **workout_exercises**

- Individual sets
- Key fields: `set_number`, `reps`, `weight_kg`, `is_pr`

### **progress_metrics**

- Body measurements
- Key fields: `recorded_date`, `body_weight_kg`, `body_fat_percentage`

---

## 🎯 Test Users Created

After running `php artisan db:seed`:

1. **John Beginner** (ID: 1, Phone: 31612345001)
    - Goal: Lose weight
    - Level: Beginner
    - Status: No workouts yet

2. **Sarah Lifter** (ID: 2, Phone: 31612345002) ⭐
    - Goal: Build muscle
    - Level: Intermediate
    - Status: Has 3 workouts, 12-day streak

3. **Mike Strong** (ID: 3, Phone: 31612345003)
    - Goal: Strength
    - Level: Advanced
    - Status: 45-day streak

4. **Lazy Larry** (ID: 4, Phone: 31612345004)
    - Goal: Lose weight
    - Level: Beginner
    - Status: Inactive for 4 weeks

---

## 💡 Key Concepts

### **Session Management**

- **One session per user** (session_id = user's WhatsApp number)
- Session persists entire conversation history
- State includes `user_id` for tool calls

### **Multi-Agent System**

- **fitness_coach** (coordinator) routes to specialists
- **workout_logger** parses and logs exercises
- **progress_tracker** analyzes data
- **motivator** provides encouragement

### **Tool Context**

- ADK automatically passes `user_id` to tools
- Tools extract it from `tool_context.state['user_id']`
- No manual user tracking needed

### **Authentication**

- WhatsApp webhook: Custom verify token
- ADK API calls: Shared secret API key
- Middleware checks `X-ADK-API-Key` header

---

**That's everything you've built!** 🎉
