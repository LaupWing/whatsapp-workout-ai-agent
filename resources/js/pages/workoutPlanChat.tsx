import React from "react"

import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { useState } from "react"

type WorkoutGoal =
    | "Strength"
    | "Hypertrophy"
    | "Endurance"
    | "Weight Loss"
    | "General Fitness"
type MuscleGroup =
    | "Chest"
    | "Back"
    | "Legs"
    | "Arms"
    | "Shoulders"
    | "Core"
    | "Full Body"

type Message = {
    type: "ai" | "user"
    content: string | React.ReactNode
}

type Step = "goal" | "muscles" | "focus" | "duration" | "summary"
function WorkoutPlanChat() {
    const [currentStep, setCurrentStep] = useState<Step>("goal")
    const [messages, setMessages] = useState<Message[]>([
        {
            type: "ai",
            content:
                "Hi! Let's create your personalized workout plan 💪 First, what's your primary fitness goal?",
        },
    ])

    const [selectedGoal, setSelectedGoal] = useState<WorkoutGoal | null>(null)
    const [selectedMuscles, setSelectedMuscles] = useState<MuscleGroup[]>([])
    const [primaryFocus, setPrimaryFocus] = useState<
        MuscleGroup | "No Preference" | null
    >(null)
    const [duration, setDuration] = useState<number>(60)

    const workoutGoals: WorkoutGoal[] = [
        "Strength",
        "Hypertrophy",
        "Endurance",
        "Weight Loss",
        "General Fitness",
    ]
    const muscleGroups: MuscleGroup[] = [
        "Chest",
        "Back",
        "Legs",
        "Arms",
        "Shoulders",
        "Core",
        "Full Body",
    ]
    const durations = [30, 45, 60, 75, 90]

    const handleGoalSelect = (goal: WorkoutGoal) => {
        setSelectedGoal(goal)
        setMessages((prev) => [
            ...prev,
            { type: "user", content: goal },
            {
                type: "ai",
                content:
                    "Great choice! Which muscle groups do you want to focus on? Select all that apply.",
            },
        ])
        setCurrentStep("muscles")
    }

    const handleMusclesContinue = () => {
        if (selectedMuscles.length === 0) return

        setMessages((prev) => [
            ...prev,
            { type: "user", content: selectedMuscles.join(", ") },
            {
                type: "ai",
                content:
                    "Which muscle group would you like to prioritize? This will get extra attention in your plan ⭐",
            },
        ])
        setCurrentStep("focus")
    }

    const handleFocusSelect = (focus: MuscleGroup | "No Preference") => {
        setPrimaryFocus(focus)
        setMessages((prev) => [
            ...prev,
            { type: "user", content: focus },
            {
                type: "ai",
                content: "How long do you want each workout session to be?",
            },
        ])
        setCurrentStep("duration")
    }

    const handleDurationSelect = (mins: number) => {
        setDuration(mins)
        setMessages((prev) => [
            ...prev,
            { type: "user", content: `${mins} minutes` },
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
                                    {selectedGoal}
                                </span>
                            </div>
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    💪 Target Muscles:
                                </span>
                                <span className="text-foreground">
                                    {selectedMuscles.join(", ")}
                                </span>
                            </div>
                            <div className="flex items-start gap-2">
                                <span className="text-muted-foreground">
                                    ⭐ Focus Area:
                                </span>
                                <span className="text-foreground">
                                    {primaryFocus}
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
    return (
        <main className="min-h-screen bg-background">
            <div className="mx-auto flex min-h-screen max-w-3xl flex-col px-4 py-8">
                {/* Messages */}
                <div className="mb-6 flex-1 space-y-6">
                    {messages.map((message, index) => (
                        <div
                            key={index}
                            className={`flex ${message.type === "user" ? "justify-end" : "justify-start"}`}
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
                        </div>
                    ))}
                </div>

                {/* Input Area */}
                <div className="border-t border-border pt-6">
                    {currentStep === "goal" && (
                        <div className="flex flex-wrap gap-2">
                            {workoutGoals.map((goal) => (
                                <Button
                                    key={goal}
                                    onClick={() => handleGoalSelect(goal)}
                                    variant="outline"
                                    className="border-border bg-card text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    {goal === "Hypertrophy"
                                        ? "Hypertrophy (Muscle Growth)"
                                        : goal}
                                </Button>
                            ))}
                        </div>
                    )}

                    {currentStep === "muscles" && (
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                {muscleGroups.map((muscle) => (
                                    <div
                                        key={muscle}
                                        onClick={() => toggleMuscle(muscle)}
                                        className={`flex cursor-pointer items-center gap-2 rounded-lg border p-3 transition-colors ${
                                            selectedMuscles.includes(muscle)
                                                ? "border-primary bg-primary/10"
                                                : "border-border bg-card hover:border-primary/50"
                                        }`}
                                    >
                                        <Checkbox
                                            id={muscle}
                                            checked={selectedMuscles.includes(
                                                muscle,
                                            )}
                                            onCheckedChange={() =>
                                                toggleMuscle(muscle)
                                            }
                                        />
                                        <Label
                                            htmlFor={muscle}
                                            className="cursor-pointer text-sm text-foreground"
                                        >
                                            {muscle}
                                        </Label>
                                    </div>
                                ))}
                            </div>
                            <Button
                                onClick={handleMusclesContinue}
                                disabled={selectedMuscles.length === 0}
                                className="w-full bg-primary text-primary-foreground hover:bg-primary/90"
                            >
                                Continue
                            </Button>
                        </div>
                    )}

                    {currentStep === "focus" && (
                        <div className="flex flex-wrap gap-2">
                            {selectedMuscles.map((muscle) => (
                                <Button
                                    key={muscle}
                                    onClick={() => handleFocusSelect(muscle)}
                                    variant="outline"
                                    className="border-border bg-card text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    {muscle}
                                </Button>
                            ))}
                            <Button
                                onClick={() =>
                                    handleFocusSelect("No Preference")
                                }
                                variant="outline"
                                className="border-border bg-card text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                            >
                                No Preference
                            </Button>
                        </div>
                    )}

                    {currentStep === "duration" && (
                        <div className="flex flex-wrap gap-2">
                            {durations.map((mins) => (
                                <Button
                                    key={mins}
                                    onClick={() => handleDurationSelect(mins)}
                                    variant="outline"
                                    className="border-border bg-card text-card-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    {mins} minutes
                                </Button>
                            ))}
                        </div>
                    )}

                    {currentStep === "summary" && (
                        <div className="text-center">
                            <Button className="bg-primary text-primary-foreground hover:bg-primary/90">
                                Generate My Workout Plan
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </main>
    )
}

export default WorkoutPlanChat
