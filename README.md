aravel Workout Logger - Complete Project Documentation
📁 All Files Created
Configuration Files
├── .env # Environment variables (API keys, database config)
├── config/services.php # Third-party service configs (WhatsApp, ADK)
Database
├── database/
│ ├── migrations/
│ │ ├── _\_create_users_table.php # User profiles & fitness data
│ │ ├── _\_create_conversations_table.php # WhatsApp message history
│ │ ├── _\_create_ai_interactions_table.php # AI agent logs & performance
│ │ ├── _\_create_exercises_table.php # Exercise library (bench press, squats, etc.)
│ │ ├── _\_create_workouts_table.php # Workout sessions
│ │ ├── _\_create_workout_exercises_table.php # Individual sets/reps/weight data
│ │ └── \*\_create_progress_metrics_table.php # Body measurements over time
│ │
│ ├── seeders/
│ │ ├── DatabaseSeeder.php # Master seeder (runs all others)
│ │ ├── ExerciseSeeder.php # Populates 25 common exercises
│ │ ├── UserSeeder.php # Creates 4 test users
│ │ └── WorkoutSeeder.php # Creates sample workout data
│ │
│ └── factories/
│ ├── UserFactory.php # Generate fake users for testing
│ ├── WorkoutFactory.php # Generate fake workouts
│ └── WorkoutExerciseFactory.php # Generate fake exercise data
Models (Database representations)
├── app/Models/
│ ├── User.php # User account & fitness profile
│ ├── Conversation.php # WhatsApp messages sent/received
│ ├── AiInteraction.php # AI agent processing logs
│ ├── Exercise.php # Exercise library (with aliases for matching)
│ ├── Workout.php # Workout session (date, duration, totals)
│ ├── WorkoutExercise.php # Individual sets (3x8 @ 80kg)
│ └── ProgressMetric.php # Body measurements (weight, measurements)
Services (Business logic)
├── app/Services/
│ ├── AdkAgentService.php # Sends messages to AI agent, handles responses
│ ├── WhatsAppService.php # Sends messages to WhatsApp (text, buttons, templates)
│ ├── WorkoutService.php # Logs workouts, updates streaks, checks PRs
│ ├── AnalyticsService.php # Calculates progress, detects plateaus
│ └── ResponseFormatterService.php # Formats data into nice WhatsApp messages
Controllers (Handle HTTP requests)
├── app/Http/Controllers/
│ ├── WhatsAppWebhookController.php # Receives WhatsApp messages from Meta
│ ├── WorkoutController.php # API endpoints for AI agent (log workouts, get history)
│ └── ExerciseController.php # Search exercises API
Middleware (Security/Authentication)
├── app/Http/Middleware/
│ └── AdkAuthMiddleware.php # Checks API key from AI agent requests
Commands (Terminal commands)
├── app/Console/Commands/
│ ├── TestAdkAgent.php # Test AI agent without WhatsApp (php artisan adk:test)
│ ├── InspectUserData.php # View user's workouts & data (php artisan user:inspect)
│ └── SendDailyReminders.php # Send workout reminders (php artisan reminders:send-daily)
Routes
├── routes/
│ └── api.php # API endpoints (webhooks, workout logging, history)
Tests
├── tests/Feature/
│ └── AdkIntegrationTest.php # Automated tests for AI integration

🎯 What Each File Does (Plain English)
Configuration Files
.env
What: Secret keys and settings
Why: Stores passwords, API keys that shouldn't be in code
Contains: Database password, WhatsApp token, AI service URL
config/services.php
What: Settings for external services
Why: Central place to configure WhatsApp and AI agent
Contains: WhatsApp API credentials, AI agent URL and API key

Database Migrations (Database structure)
_\_create_users_table.php
What: Creates user accounts table
Why: Store user profiles (name, weight, fitness goals, streak)
When used: When someone first messages the bot
_\_create_conversations_table.php
What: Creates WhatsApp message history table
Why: Keep record of every message sent/received for debugging
When used: Every WhatsApp interaction
_\_create_ai_interactions_table.php
What: Creates AI processing logs table
Why: Track what the AI did (which agent, tokens used, response time)
When used: Every time AI processes a message
_\_create_exercises_table.php
What: Creates exercise library table
Why: Store all exercises (Bench Press, Squats) with aliases
When used: When AI needs to match "bench" to "Bench Press"
_\_create_workouts_table.php
What: Creates workout sessions table
Why: Group exercises into sessions (Monday's workout, Tuesday's workout)
When used: When user logs any exercise
_\_create_workout_exercises_table.php
What: Creates individual set records table
Why: Store each set's details (Set 1: 8 reps @ 80kg)
When used: When logging "3 sets of bench press"
\*\_create_progress_metrics_table.php
What: Creates body measurements table
Why: Track weight, body fat %, measurements over time
When used: When user logs weight or takes measurements

Seeders (Test data generators)
DatabaseSeeder.php
What: Master file that runs all other seeders
Why: One command to populate entire database
Run with: php artisan db:seed
ExerciseSeeder.php
What: Adds 25 common exercises to database
Why: Pre-populate so AI can recognize exercises immediately
Creates: Bench Press, Squat, Deadlift, Pull-ups, etc.
UserSeeder.php
What: Creates 4 test users with different profiles
Why: Test the system without real WhatsApp users
Creates: Beginner user, intermediate user, advanced user, inactive user
WorkoutSeeder.php
What: Adds sample workout history for one test user
Why: Test progress tracking features with realistic data
Creates: 3 workouts (push, pull, legs) from past week

Factories (Fake data generators)
UserFactory.php
What: Template for generating random users
Why: Create 100 fake users for testing
Used in: Automated testing
WorkoutFactory.php
What: Template for generating random workouts
Why: Create workout history quickly
Used in: Testing analytics features
WorkoutExerciseFactory.php
What: Template for generating random sets/reps
Why: Fill workouts with exercise data
Used in: Testing progress calculations

Models (Database interaction)
User.php
What: Represents a user account
Why: Access user data (profile, workouts, conversations)
Example: $user->workouts gets all their workouts
Conversation.php
What: Represents a WhatsApp message
Why: Store and retrieve message history
Example: $user->conversations gets all their messages
AiInteraction.php
What: Represents one AI processing event
Why: Track AI performance and debug issues
Example: See which agent handled a message and how long it took
Exercise.php
What: Represents one exercise (Bench Press)
Why: Store exercise details and search by name/alias
Example: Find "bench" matches "Bench Press"
Workout.php
What: Represents one workout session
Why: Group exercises together by date
Example: Monday's workout with bench press and squats
WorkoutExercise.php
What: Represents one set of one exercise
Why: Store exact details (8 reps @ 80kg)
Example: Set 1 of Bench Press on Monday
ProgressMetric.php
What: Represents body measurements on a date
Why: Track weight loss/gain over time
Example: Weekly weigh-ins

Services (Business logic - the brain)
AdkAgentService.php
What: Communicates with AI agent
Why: Send user messages to AI and get responses
Does:

Takes user message
Adds context (recent workouts, goals, streak)
Sends to AI agent
Saves AI's response
Sends response to WhatsApp

WhatsAppService.php
What: Communicates with WhatsApp
Why: Send messages to users
Does:

Send text messages
Send messages with buttons
Send template messages (reminders)
Track delivery status

WorkoutService.php
What: Handles all workout logging
Why: Core business logic for fitness tracking
Does:

Log workouts to database
Calculate total volume
Update user's streak
Check for personal records (PRs)
Update workout totals

AnalyticsService.php
What: Calculates fitness insights
Why: Provide progress analysis to users
Does:

Find most improved exercises
Detect plateaus (stuck at same weight)
Show muscle group balance
Calculate progressive overload score

ResponseFormatterService.php
What: Formats data into messages
Why: Make stats look nice in WhatsApp
Does:

Format workout confirmations
Format weekly summaries
Format progress reports
Add emojis and formatting

Controllers (Handle incoming requests)
WhatsAppWebhookController.php
What: Receives messages from WhatsApp
Why: WhatsApp sends messages here via webhook
Does:

Verify WhatsApp connection
Receive incoming messages
Save to database
Send to AI agent
Update delivery status

WorkoutController.php
What: Provides API for AI agent
Why: AI needs to log workouts and get history
Endpoints:

POST /workouts/log - Log a workout
GET /workouts/history - Get workout list
GET /workouts/summary - Get statistics

ExerciseController.php
What: Search exercises API
Why: AI needs to find exercises by name
Endpoint:

GET /exercises/search?q=bench - Find exercises

Middleware (Security layer)
AdkAuthMiddleware.php
What: Checks if AI agent is authorized
Why: Prevent unauthorized access to workout APIs
Does:

Checks for API key in request
Compares to secret key
Returns 401 if invalid

Commands (Terminal tools)
TestAdkAgent.php
What: Test AI without WhatsApp
Why: Debug and develop faster
Usage: php artisan adk:test 2 "I did bench press"
Does: Sends message to AI, shows response
InspectUserData.php
What: View user data in terminal
Why: Quick debugging of user workouts
Usage: php artisan user:inspect 2
Does: Shows profile, workouts, AI interactions
SendDailyReminders.php
What: Sends workout reminders
Why: Keep users engaged
Usage: Runs automatically at 8 AM daily
Does: Checks who hasn't worked out, sends reminder

Routes (routes/api.php)
Public Routes (WhatsApp calls these)
GET /api/webhook/whatsapp → Verify webhook
POST /api/webhook/whatsapp → Receive messages
Protected Routes (AI agent calls these)
POST /api/workouts/log → Log workout
GET /api/workouts/history → Get workout list
GET /api/workouts/summary → Get statistics
GET /api/exercises/search → Find exercises
Test Route (Local development only)
POST /api/test/adk → Test AI integration

Tests
AdkIntegrationTest.php
What: Automated tests for AI integration
Why: Ensure AI logging works correctly
Tests:

Can query workout history
Can log new workout
Handles beginner queries appropriately

🤖 ADK Python Service (Separate Project)
What is it?
Python application that runs your AI agents using Google's framework
Location:
fitness_coach_adk/ (separate folder from Laravel)
Structure:
fitness_coach_adk/
├── .env # API keys, Laravel URL
├── requirements.txt # Python packages needed
└── fitness_coach/
├── agent.py # AI agent definitions
└── tools.py # Functions that call Laravel
What it does:

Receives messages from Laravel
Processes with AI (Gemini model)
Decides which specialist agent to use:

workout_logger: Parses "I did bench 3x8 @ 60kg"
progress_tracker: Analyzes workout history
motivator: Provides encouragement
fitness_coach: Coordinates everything

Calls Laravel APIs to log workouts or get data
Returns response to Laravel

How Laravel calls it:
Laravel → POST http://localhost:8000/run
→ ADK processes message
→ ADK calls Laravel APIs if needed
→ Returns response
Laravel → Sends response to WhatsApp
How ADK calls Laravel:
python# tools.py functions make HTTP requests:
log_workout() → POST /api/workouts/log
get_workout_history() → GET /api/workouts/history
get_workout_summary() → GET /api/workouts/summary
Running it:
bashcd fitness_coach_adk
adk api_server # Starts on port 8000
Key concept:
Uses tool_context to know WHO is talking:

Laravel passes user_id in session state
ADK automatically injects it into tools
Tools use it to query the right user's data

🔄 Complete Flow (How it all works together)

User sends WhatsApp message: "I did bench press 3x8 at 60kg"
WhatsApp → Laravel webhook:

WhatsAppWebhookController receives it
Creates/finds user by phone number
Saves to conversations table

Laravel → ADK:

AdkAgentService sends message to AI
Includes user context (profile, recent workouts)

ADK processes:

fitness_coach agent decides: "This is workout logging"
Routes to workout_logger specialist agent
Agent parses: exercise="Bench Press", sets=3, reps=8, weight=60

ADK → Laravel API:

Calls log_workout tool
Tool calls POST /api/workouts/log

Laravel logs workout:

WorkoutController receives request
WorkoutService creates workout record
Saves to workouts and workout_exercises tables
Updates user streak
Checks for PR

Response flows back:

Laravel → ADK → Laravel → WhatsApp → User
"💪 Logged! Bench Press 3×8 @ 60kg. That's 5kg more than last week!"

📊 Data Flow Example
User: Sarah (ID: 2, Phone: 31612345002)
Message: "What did I do this week?"
Flow:

WhatsApp → conversations table (saves message)
Laravel → ADK with user_id: 2 in state
ADK fitness_coach → routes to progress_tracker
ADK calls get_workout_history(days=7)
Tool extracts user_id from context → calls Laravel
Laravel WorkoutController → queries database
Returns: 3 workouts (Push, Pull, Legs)
ADK formats response
Laravel saves to ai_interactions table
Laravel → WhatsApp: "This week you crushed it! 💪..."

🚀 Quick Start Guide
First Time Setup:
bash# 1. Setup Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# 2. Start Laravel

php artisan serve # Port 8000

# 3. Setup ADK (different terminal)

cd fitness_coach_adk
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
adk api_server # Port 8001 (change in Laravel .env: ADK_API_URL=http://localhost:8001)

# 4. Test

php artisan adk:test 2 "I did squats 5x5 at 100kg"
Environment Variables Needed:
Laravel .env:
DB_DATABASE=workout_logger
WHATSAPP_TOKEN=your_token
WHATSAPP_PHONE_NUMBER_ID=your_number
WHATSAPP_VERIFY_TOKEN=random_string
ADK_API_URL=http://localhost:8001
ADK_API_KEY=shared_secret_123
ADK .env:
GOOGLE_CLOUD_PROJECT=your-project
GOOGLE_CLOUD_LOCATION=us-central1
GOOGLE_GENAI_USE_VERTEXAI=True
LARAVEL_API_URL=http://localhost:8000/api
LARAVEL_API_KEY=shared_secret_123
