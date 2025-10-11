# Workout Plan Generation

This document explains how to use the AI-powered workout plan generation feature in your WhatsApp-based workout logging application.

## Overview

The workout plan generation system uses OpenAI to create personalized workout plans based on user preferences. It integrates seamlessly with the existing multi-service architecture (Laravel + ADK AI Service).

## Architecture

```
User via WhatsApp → Laravel Webhook → ADK AI Service
                                       ↓
                    ADK calls Laravel API: POST /api/workout-plans/generate
                                       ↓
                    WorkoutPlanGeneratorService → OpenAI API
                                       ↓
                    Creates WorkoutPlan + WorkoutPlanExercises
                                       ↓
                    Returns plan to ADK → WhatsApp → User
```

## API Endpoint

### POST `/api/workout-plans/generate`

Generate a new AI-powered workout plan for a user.

**Request Body:**
```json
{
  "user_id": 2,
  "workout_days": ["monday", "wednesday", "friday"],
  "muscle_groups": ["chest", "back", "legs"],
  "focus_muscles": ["chest", "back"],
  "session_duration": 60,
  "goal": "hypertrophy"
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `user_id` | integer | Yes | User ID from database |
| `workout_days` | array | Yes | Days of the week to workout (1-7 days) |
| `muscle_groups` | array | Yes | Target muscle groups (chest, back, legs, shoulders, arms, core, full_body) |
| `focus_muscles` | array | No | Primary focus muscle groups (subset of muscle_groups) |
| `session_duration` | integer | Yes | Duration per session in minutes (15-180) |
| `goal` | string | No | Fitness goal: strength, hypertrophy, endurance, weight_loss, general_fitness |

**Success Response (201):**
```json
{
  "success": true,
  "message": "Workout plan generated successfully",
  "workout_plan": {
    "id": 5,
    "user_id": 2,
    "name": "Hypertrophy - Chest & Back Plan",
    "description": "AI-generated 3-day workout plan focused on hypertrophy. Each session is approximately 60 minutes.",
    "goal": "hypertrophy",
    "status": "active",
    "duration_weeks": 4,
    "start_date": "2025-10-11",
    "end_date": "2025-11-08",
    "schedule": {
      "workout_days": ["monday", "wednesday", "friday"],
      "muscle_groups": ["chest", "back", "legs"],
      "focus_muscles": ["chest", "back"],
      "session_duration": 60
    },
    "plan_exercises": [
      {
        "id": 1,
        "workout_plan_id": 5,
        "exercise_id": 1,
        "day_of_week": "monday",
        "order": 1,
        "target_sets": 4,
        "target_reps": 10,
        "rest_seconds": 90,
        "exercise": {
          "id": 1,
          "name": "Bench Press",
          "muscle_group": "chest",
          "difficulty": "intermediate"
        }
      }
      // ... more exercises
    ]
  }
}
```

**Error Response (422):**
```json
{
  "errors": {
    "workout_days": ["The workout days field is required."],
    "muscle_groups": ["The muscle groups field is required."]
  }
}
```

**Error Response (500):**
```json
{
  "success": false,
  "error": "Failed to generate workout plan: OpenAI API request failed"
}
```

## ADK Agent Integration

The ADK AI agents can call this endpoint to generate workout plans for users. Here's an example implementation:

### Python Example (ADK Agent Tool)

```python
import requests
from typing import Dict, List

def generate_workout_plan(
    user_id: int,
    workout_days: List[str],
    muscle_groups: List[str],
    focus_muscles: List[str] = None,
    session_duration: int = 60,
    goal: str = None
) -> Dict:
    """
    Generate a personalized workout plan for a user.

    Args:
        user_id: User's database ID
        workout_days: Days to workout (e.g., ["monday", "wednesday", "friday"])
        muscle_groups: Target muscle groups (e.g., ["chest", "back", "legs"])
        focus_muscles: Primary focus muscles (optional, subset of muscle_groups)
        session_duration: Minutes per session (15-180, default 60)
        goal: Fitness goal (strength, hypertrophy, endurance, weight_loss, general_fitness)

    Returns:
        Dict containing the generated workout plan
    """
    url = f"{os.getenv('LARAVEL_API_URL')}/workout-plans/generate"
    headers = {
        "X-ADK-API-Key": os.getenv('LARAVEL_API_KEY'),
        "Content-Type": "application/json"
    }

    payload = {
        "user_id": user_id,
        "workout_days": workout_days,
        "muscle_groups": muscle_groups,
        "session_duration": session_duration
    }

    if focus_muscles:
        payload["focus_muscles"] = focus_muscles

    if goal:
        payload["goal"] = goal

    response = requests.post(url, json=payload, headers=headers)
    response.raise_for_status()

    return response.json()

# Usage example
plan = generate_workout_plan(
    user_id=2,
    workout_days=["monday", "wednesday", "friday"],
    muscle_groups=["chest", "back", "legs"],
    focus_muscles=["chest"],
    session_duration=60,
    goal="hypertrophy"
)
```

### Conversational Flow Example

**User:** "I want a new workout plan for Monday, Wednesday, and Friday focusing on chest and back"

**ADK Agent Processing:**
1. Extract intent: workout plan generation
2. Extract parameters:
   - workout_days: ["monday", "wednesday", "friday"]
   - muscle_groups: ["chest", "back"]
   - focus_muscles: ["chest", "back"]
   - session_duration: 60 (default or ask user)
   - goal: "hypertrophy" (infer from user profile or ask)

3. Call Laravel API to generate plan
4. Format response for WhatsApp

**ADK Agent Response:**
```
✅ I've created your new workout plan!

📋 Hypertrophy - Chest & Back Plan
⏱ 3 days/week, ~60 min/session
📅 Runs for 4 weeks

Monday - Chest Focus:
1. Bench Press: 4 sets × 10 reps
2. Incline Dumbbell Press: 4 sets × 10 reps
3. Cable Flyes: 3 sets × 12 reps

Wednesday - Back Focus:
1. Pull-ups: 4 sets × 8 reps
2. Barbell Rows: 4 sets × 10 reps
3. Lat Pulldown: 3 sets × 12 reps

Friday - Upper Body:
1. Overhead Press: 4 sets × 10 reps
2. Face Pulls: 3 sets × 15 reps
3. Tricep Dips: 3 sets × 10 reps

Your plan is now active! Ready to start?
```

## How It Works

### 1. WorkoutPlanGeneratorService

The `WorkoutPlanGeneratorService` handles the entire generation process:

- **Exercise Selection**: Filters available exercises by muscle groups
- **AI Prompt Engineering**: Builds specialized prompts for OpenAI
- **Goal-Based Programming**: Adjusts sets/reps based on fitness goals:
  - Strength: 4-6 sets × 3-6 reps (heavy weight)
  - Hypertrophy: 3-5 sets × 8-12 reps (moderate weight)
  - Endurance: 2-4 sets × 15-25 reps (light weight)
  - Weight Loss: 3-4 sets × 12-20 reps (circuit-style)
  - General Fitness: 3-5 sets × 8-15 reps (balanced)

- **Plan Structure**: Creates proper database structure with exercises grouped by day
- **Progressive Overload**: Prioritizes compound movements first
- **Recovery**: Ensures adequate rest between muscle groups

### 2. OpenAI Integration

The service uses OpenAI's `gpt-3.5-turbo-1106` model with JSON response format to ensure structured output.

**System Prompt Strategy:**
- Provides available exercises with muscle groups
- Defines output structure requirements
- Sets training principles (progressive overload, muscle balance, recovery)
- Specifies set/rep ranges based on goal

**Validation & Retry Logic:**
- Validates response structure before accepting
- Retries up to 3 times on failure
- Normalizes response data (handles case sensitivity, data types)

### 3. Database Structure

Generated plans create:

**WorkoutPlan Record:**
- User association
- Plan metadata (name, description, goal)
- Status (active/completed)
- Schedule configuration
- Duration (4-week default)

**WorkoutPlanExercise Records:**
- Links to exercises from exercise library
- Day of week assignment
- Exercise order
- Target sets/reps
- Rest periods

## Configuration

### Environment Variables

Add to your `.env` file:

```env
# OpenAI Configuration
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_MODEL=gpt-3.5-turbo-1106
```

### Database Seeding

Ensure your database has exercises seeded with proper muscle group classifications:

```php
// database/seeders/ExerciseSeeder.php
Exercise::create([
    'name' => 'Bench Press',
    'muscle_group' => MuscleGroup::CHEST,
    'difficulty' => ExerciseDifficulty::INTERMEDIATE,
    'is_active' => true,
]);
```

## Testing

### Manual Testing with Artisan

```bash
# Test the ADK agent (which can call workout plan generation)
php artisan adk:test 2 "Create me a workout plan for Monday, Wednesday, Friday focusing on chest and back"
```

### API Testing with cURL

```bash
curl -X POST http://localhost:8000/api/workout-plans/generate \
  -H "Content-Type: application/json" \
  -H "X-ADK-API-Key: your-api-key" \
  -d '{
    "user_id": 2,
    "workout_days": ["monday", "wednesday", "friday"],
    "muscle_groups": ["chest", "back", "legs"],
    "focus_muscles": ["chest", "back"],
    "session_duration": 60,
    "goal": "hypertrophy"
  }'
```

### Feature Test Example

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutPlanGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_workout_plan()
    {
        // Arrange
        $user = User::factory()->create();
        Exercise::factory()->count(20)->create();

        // Act
        $response = $this->postJson('/api/workout-plans/generate', [
            'user_id' => $user->id,
            'workout_days' => ['monday', 'wednesday', 'friday'],
            'muscle_groups' => ['chest', 'back'],
            'session_duration' => 60,
            'goal' => 'hypertrophy',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Workout plan generated successfully',
            ]);

        $this->assertDatabaseHas('workout_plans', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }
}
```

## Error Handling

The service includes comprehensive error handling:

1. **Validation Errors (422)**: Invalid input parameters
2. **OpenAI API Errors**: Automatic retry with exponential backoff
3. **Structure Validation**: Ensures AI response matches expected format
4. **Logging**: All errors logged for debugging

## Best Practices

1. **Deactivate Old Plans**: The system automatically marks existing active plans as completed
2. **Exercise Library**: Ensure your database has diverse exercises across all muscle groups
3. **User Context**: Consider user's fitness level and goals from their profile
4. **Rate Limiting**: Implement rate limiting on the endpoint to prevent abuse
5. **Cost Management**: Monitor OpenAI API usage and implement caching where appropriate

## Future Enhancements

- [ ] Progressive plan updates based on user progress
- [ ] Alternative exercise suggestions for equipment limitations
- [ ] Integration with calendar for scheduling
- [ ] Video demonstrations for exercises
- [ ] Community sharing of effective plans
- [ ] A/B testing different prompt strategies
