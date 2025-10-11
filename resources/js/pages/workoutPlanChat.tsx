import React from "react"

import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import {
    DayExercises,
    WorkoutPlanData,
    WorkoutPlanEditor,
} from "@/components/WorkoutPlanEditor"
import {
    MuscleGroup,
    MuscleGroupOptions,
    WorkoutDay,
    WorkoutDayOptions,
    WorkoutPlanGoal,
    WorkoutPlanGoalOptions,
} from "@/types/enums"
import { usePage } from "@inertiajs/react"
import { Edit2 } from "lucide-react"
import { useState } from "react"

type Message = {
    type: "ai" | "user" | "choices" | "plan"
    content: string | React.ReactNode
    step?: Step
    editable?: boolean
}

type Step = "goal" | "muscles" | "focus" | "days" | "duration" | "summary"
function WorkoutPlanChat() {
    const page = usePage()
    const [currentStep, setCurrentStep] = useState<Step>("goal")
    const [messages, setMessages] = useState<Message[]>([
        {
            type: "ai",
            content:
                "Hi! Let's create your personalized workout plan 💪 First, what's your primary fitness goal?",
        },
        {
            type: "choices",
            content: "goal-choices",
            step: "goal",
        },
    ])

    const [selectedGoal, setSelectedGoal] = useState<WorkoutPlanGoal | null>(
        null,
    )
    // Default to all muscle groups checked
    const [selectedMuscles, setSelectedMuscles] = useState<MuscleGroup[]>(
        MuscleGroupOptions.map((m) => m.value),
    )
    const [primaryFocus, setPrimaryFocus] = useState<
        MuscleGroup | "No Preference" | null
    >(null)
    const [selectedDays, setSelectedDays] = useState<WorkoutDay[]>([])
    const [duration, setDuration] = useState<number>(60)
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false)
    const [generatedPlan, setGeneratedPlan] = useState<WorkoutPlanData | null>(
        null,
    )

    const durations = [30, 45, 60, 75, 90]

    // Get CSRF token
    const getCsrfToken = () => {
        const token = document.head.querySelector('meta[name="csrf-token"]')
        return token ? token.getAttribute("content") : ""
    }

    const handleGoalSelect = (goal: WorkoutPlanGoal) => {
        setSelectedGoal(goal)
        const goalOption = WorkoutPlanGoalOptions.find(
            (opt) => opt.value === goal,
        )
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "goal"),
            {
                type: "user",
                content: goalOption?.label || goal,
                step: "goal",
                editable: true,
            },
            {
                type: "ai",
                content:
                    "Great choice! Which muscle groups do you want to train? All groups are selected by default - uncheck any you want to skip.",
            },
            {
                type: "choices",
                content: "muscles-choices",
                step: "muscles",
            },
        ])
        setCurrentStep("muscles")
    }

    const handleEditStep = (step: Step) => {
        // Confirm before allowing edit
        if (
            !confirm(
                "Are you sure you want to edit this answer? This will reset all subsequent answers.",
            )
        ) {
            return
        }

        setCurrentStep(step)
        // Remove all messages after this step
        setMessages((prev) => {
            const stepIndex = prev.findIndex(
                (m) => m.step === step && m.type === "user",
            )
            if (stepIndex === -1) return prev
            return prev.slice(0, stepIndex)
        })

        // Re-add the choices for this step
        setMessages((prev) => [
            ...prev,
            {
                type: "choices",
                content: `${step}-choices`,
                step: step,
            },
        ])
    }

    const handleMusclesContinue = () => {
        if (selectedMuscles.length === 0) return

        const muscleLabels = selectedMuscles.map(
            (m) =>
                MuscleGroupOptions.find((opt) => opt.value === m)?.label || m,
        )
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "muscles"),
            {
                type: "user",
                content: muscleLabels.join(", "),
                step: "muscles",
                editable: true,
            },
            {
                type: "ai",
                content:
                    "Which muscle group would you like to prioritize? This will get extra attention in your plan ⭐",
            },
            {
                type: "choices",
                content: "focus-choices",
                step: "focus",
            },
        ])
        setCurrentStep("focus")
    }

    const handleFocusSelect = (focus: MuscleGroup | "No Preference") => {
        setPrimaryFocus(focus)
        const focusLabel =
            focus === "No Preference"
                ? "No Preference"
                : MuscleGroupOptions.find((opt) => opt.value === focus)
                      ?.label || focus
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "focus"),
            {
                type: "user",
                content: focusLabel,
                step: "focus",
                editable: true,
            },
            {
                type: "ai",
                content:
                    "Which days of the week do you want to train? Select at least one day.",
            },
            {
                type: "choices",
                content: "days-choices",
                step: "days",
            },
        ])
        setCurrentStep("days")
    }

    const handleDaysContinue = () => {
        if (selectedDays.length === 0) return

        const dayLabels = selectedDays.map(
            (d) => WorkoutDayOptions.find((opt) => opt.value === d)?.label || d,
        )
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "days"),
            {
                type: "user",
                content: dayLabels.join(", "),
                step: "days",
                editable: true,
            },
            {
                type: "ai",
                content: "How long do you want each workout session to be?",
            },
            {
                type: "choices",
                content: "duration-choices",
                step: "duration",
            },
        ])
        setCurrentStep("duration")
    }

    const toggleDay = (day: WorkoutDay) => {
        setSelectedDays((prev) =>
            prev.includes(day) ? prev.filter((d) => d !== day) : [...prev, day],
        )
    }

    const handleDurationSelect = (mins: number) => {
        setDuration(mins)
        const goalLabel =
            WorkoutPlanGoalOptions.find((opt) => opt.value === selectedGoal)
                ?.label || selectedGoal
        const muscleLabels = selectedMuscles.map(
            (m) =>
                MuscleGroupOptions.find((opt) => opt.value === m)?.label || m,
        )
        const focusLabel =
            primaryFocus === "No Preference"
                ? "No Preference"
                : MuscleGroupOptions.find((opt) => opt.value === primaryFocus)
                      ?.label || primaryFocus

        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "duration"),
            {
                type: "user",
                content: `${mins} minutes`,
                step: "duration",
                editable: true,
            },
            {
                type: "ai",
                content: (
                    <div className="space-y-3">
                        <p className="font-medium">
                            Perfect! Here's your workout plan summary:
                        </p>
                        <div className="space-y-2 rounded-lg border border-border bg-accent/50 p-4 text-sm">
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    🎯 Goal:
                                </span>
                                <span className="text-foreground">
                                    {goalLabel}
                                </span>
                            </div>
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    💪 Target Muscles:
                                </span>
                                <span className="text-foreground">
                                    {muscleLabels.join(", ")}
                                </span>
                            </div>
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    ⭐ Focus Area:
                                </span>
                                <span className="text-foreground">
                                    {focusLabel}
                                </span>
                            </div>
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    ⏱️ Session Duration:
                                </span>
                                <span className="text-foreground">
                                    {mins} minutes
                                </span>
                            </div>
                        </div>
                    </div>
                ),
            },
            {
                type: "choices",
                content: "summary-choices",
                step: "summary",
            },
        ])
        setCurrentStep("summary")
    }

    const toggleMuscle = (muscle: MuscleGroup) => {
        setSelectedMuscles((prev) =>
            prev.includes(muscle)
                ? prev.filter((m) => m !== muscle)
                : [...prev, muscle],
        )
    }

    const handleSubmitWorkoutPlan = async () => {
        setIsSubmitting(true)
        console.log("Submitting workout plan with:")
        console.log(
            JSON.stringify({
                //         'goal' => 'required|string|in:strength,hypertrophy,endurance,weight_loss,general_fitness',
                // 'muscle_groups' => 'required|array|min:1',
                // 'muscle_groups.*' => 'string',
                // 'primary_focus' => 'nullable|string',
                // 'session_duration' => 'required|integer|min:15|max:180',
                // 'workout_days' => 'required|array|min:1|max:7',
                // 'workout_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                goal: selectedGoal,
                muscle_groups: selectedMuscles,
                primary_focus:
                    primaryFocus === "No Preference" ? null : primaryFocus,
                session_duration: duration,
                workout_days: selectedDays,
            }),
        )
        try {
            const response = await fetch(
                "http://whatsapp-workout-ai-agent.test/api/workout-plans",
                {
                    method: "POST",
                    // @ts-ignore
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        //         'goal' => 'required|string|in:strength,hypertrophy,endurance,weight_loss,general_fitness',
                        // 'muscle_groups' => 'required|array|min:1',
                        // 'muscle_groups.*' => 'string',
                        // 'primary_focus' => 'nullable|string',
                        // 'session_duration' => 'required|integer|min:15|max:180',
                        // 'workout_days' => 'required|array|min:1|max:7',
                        // 'workout_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                        goal: selectedGoal,
                        muscle_groups: selectedMuscles,
                        primary_focus:
                            primaryFocus === "No Preference"
                                ? null
                                : primaryFocus,
                        session_duration: duration,
                        workout_days: selectedDays,
                    }),
                },
            )
            console.log("Workout plan response status:", response.status)
            console.log(response)
            const data = await response.json()
            console.log("Workout plan response data:", data)
            if (!response.ok) {
                throw new Error(data.error || "Failed to generate workout plan")
            }

            // Set the generated plan and show it
            setGeneratedPlan(data.workout_plan)
            setMessages((prev) => [
                ...prev,
                {
                    type: "ai",
                    content:
                        "🎉 Your workout plan is ready! You can drag and drop exercises between days to customize it.",
                },
                {
                    type: "plan",
                    content: "workout-plan-editor",
                },
            ])
            setIsSubmitting(false)
        } catch (error) {
            console.error("Error submitting workout plan:", error)
            alert(
                error instanceof Error
                    ? error.message
                    : "Failed to create workout plan. Please try again.",
            )
            setIsSubmitting(false)
        }
    }

    const handleSavePlan = async (editedPlan: DayExercises[]) => {
        if (!generatedPlan) return

        // Prepare exercises with updated day and order
        const updatedExercises = editedPlan.flatMap((dayData) =>
            dayData.exercises.map((exercise, exerciseIndex) => ({
                id: exercise.id,
                day_of_week: dayData.day,
                order: exerciseIndex + 1,
            })),
        )

        try {
            const response = await fetch(
                `/api/workout-plans/${generatedPlan.id}/reorder`,
                {
                    method: "PUT",
                    // @ts-ignore
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        exercises: updatedExercises,
                    }),
                },
            )

            const data = await response.json()

            if (!response.ok) {
                throw new Error(data.error || "Failed to save workout plan")
            }

            alert("✅ Workout plan saved successfully!")
        } catch (error) {
            console.error("Error saving workout plan:", error)
            alert(
                error instanceof Error
                    ? error.message
                    : "Failed to save workout plan. Please try again.",
            )
        }
    }
    const renderChoices = (step: Step) => {
        switch (step) {
            case "goal":
                return (
                    <div className="flex flex-wrap justify-end gap-2">
                        {WorkoutPlanGoalOptions.map((option) => (
                            <button
                                key={option.value}
                                onClick={() => handleGoalSelect(option.value)}
                                className="rounded-2xl border border-border bg-card px-4 py-2 text-sm text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                            >
                                {option.label}
                            </button>
                        ))}
                    </div>
                )
            case "muscles":
                return (
                    <div className="space-y-3">
                        <div className="flex flex-wrap justify-end gap-2">
                            {MuscleGroupOptions.map((option) => (
                                <button
                                    key={option.value}
                                    onClick={() => toggleMuscle(option.value)}
                                    className={`rounded-2xl border px-4 py-2 text-sm transition-colors ${
                                        selectedMuscles.includes(option.value)
                                            ? "border-primary bg-primary/20 text-foreground"
                                            : "border-border bg-card text-card-foreground hover:bg-accent"
                                    }`}
                                >
                                    {option.label}{" "}
                                    {selectedMuscles.includes(option.value) &&
                                        "✓"}
                                </button>
                            ))}
                        </div>
                        <div className="flex justify-end">
                            <Button
                                onClick={handleMusclesContinue}
                                disabled={selectedMuscles.length === 0}
                                size="sm"
                                className="bg-primary text-primary-foreground hover:bg-primary/90"
                            >
                                Continue
                            </Button>
                        </div>
                    </div>
                )
            case "focus":
                return (
                    <div className="flex flex-wrap justify-end gap-2">
                        {selectedMuscles.map((muscle) => {
                            const option = MuscleGroupOptions.find(
                                (opt) => opt.value === muscle,
                            )
                            return (
                                <button
                                    key={muscle}
                                    onClick={() => handleFocusSelect(muscle)}
                                    className="rounded-2xl border border-border bg-card px-4 py-2 text-sm text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    {option?.label || muscle}
                                </button>
                            )
                        })}
                        <button
                            onClick={() => handleFocusSelect("No Preference")}
                            className="rounded-2xl border border-border bg-card px-4 py-2 text-sm text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            No Preference
                        </button>
                    </div>
                )
            case "days":
                return (
                    <div className="space-y-3">
                        <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            {WorkoutDayOptions.map((option) => (
                                <div
                                    key={option.value}
                                    className="flex items-center gap-2 rounded-md border border-border bg-card p-3"
                                >
                                    <Checkbox
                                        id={option.value}
                                        checked={selectedDays.includes(
                                            option.value,
                                        )}
                                        onCheckedChange={() =>
                                            toggleDay(option.value)
                                        }
                                    />
                                    <Label
                                        htmlFor={option.value}
                                        className="cursor-pointer text-sm text-card-foreground"
                                    >
                                        {option.shortLabel}
                                    </Label>
                                </div>
                            ))}
                        </div>
                        <div className="flex justify-end">
                            <Button
                                onClick={handleDaysContinue}
                                disabled={selectedDays.length === 0}
                                size="sm"
                                className="bg-primary text-primary-foreground hover:bg-primary/90"
                            >
                                Continue
                            </Button>
                        </div>
                    </div>
                )
            case "duration":
                return (
                    <div className="flex flex-wrap justify-end gap-2">
                        {durations.map((mins) => (
                            <button
                                key={mins}
                                onClick={() => handleDurationSelect(mins)}
                                className="rounded-2xl border border-border bg-card px-4 py-2 text-sm text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                            >
                                {mins} min
                            </button>
                        ))}
                    </div>
                )
            case "summary":
                return (
                    <div className="flex justify-end">
                        <Button
                            onClick={handleSubmitWorkoutPlan}
                            disabled={isSubmitting}
                            className="bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {isSubmitting
                                ? "Creating..."
                                : "Generate My Workout Plan"}
                        </Button>
                    </div>
                )
        }
    }

    return (
        <main className="min-h-screen bg-background">
            <div className="mx-auto flex min-h-screen max-w-3xl flex-col px-4 py-8">
                {/* Messages */}
                <div className="mb-6 flex-1 space-y-4">
                    {messages.map((message, index) => {
                        if (message.type === "choices") {
                            return (
                                <div key={index} className="flex justify-end">
                                    <div className="max-w-[85%]">
                                        {renderChoices(message.step!)}
                                    </div>
                                </div>
                            )
                        }

                        if (message.type === "plan" && generatedPlan) {
                            return (
                                <div key={index} className="w-full">
                                    <WorkoutPlanEditor
                                        workoutPlan={generatedPlan}
                                        onSave={handleSavePlan}
                                    />
                                </div>
                            )
                        }

                        return (
                            <div
                                key={index}
                                className={`relative flex ${message.type === "user" ? "justify-end" : "justify-start"}`}
                            >
                                <div
                                    className={`max-w-[80%] rounded-2xl px-4 py-3 ${
                                        message.type === "user"
                                            ? "bg-green-500 text-white dark:bg-green-600"
                                            : "border border-border bg-muted text-foreground"
                                    }`}
                                >
                                    {message.content}
                                </div>
                                {message.editable && message.step && (
                                    <button
                                        onClick={() =>
                                            handleEditStep(message.step!)
                                        }
                                        className="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full border border-border bg-background text-muted-foreground shadow-sm transition-all hover:scale-110 hover:bg-accent hover:text-foreground"
                                        title="Edit"
                                    >
                                        <Edit2 className="h-3.5 w-3.5" />
                                    </button>
                                )}
                            </div>
                        )
                    })}
                </div>
            </div>
        </main>
    )
}

export default WorkoutPlanChat
