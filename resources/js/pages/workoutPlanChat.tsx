import React from "react"

import { Button } from "@/components/ui/button"
import {
    MuscleGroup,
    MuscleGroupOptions,
    WorkoutPlanGoal,
    WorkoutPlanGoalOptions,
} from "@/types/enums"
import { Edit2 } from "lucide-react"
import { useState } from "react"

type Message = {
    type: "ai" | "user" | "choices"
    content: string | React.ReactNode
    step?: Step
    editable?: boolean
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
    const [duration, setDuration] = useState<number>(60)

    const durations = [30, 45, 60, 75, 90]

    const handleGoalSelect = (goal: WorkoutPlanGoal) => {
        setSelectedGoal(goal)
        const goalOption = WorkoutPlanGoalOptions.find(
            (opt) => opt.value === goal,
        )
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "goal"),
            { type: "user", content: goalOption?.label || goal, step: "goal", editable: true },
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
        setCurrentStep(step)
        // Remove all messages after this step
        setMessages((prev) => {
            const stepIndex = prev.findIndex((m) => m.step === step && m.type === "user")
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
            (m) => MuscleGroupOptions.find((opt) => opt.value === m)?.label || m,
        )
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "muscles"),
            { type: "user", content: muscleLabels.join(", "), step: "muscles", editable: true },
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
                : MuscleGroupOptions.find((opt) => opt.value === focus)?.label ||
                  focus
        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "focus"),
            { type: "user", content: focusLabel, step: "focus", editable: true },
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

    const handleDurationSelect = (mins: number) => {
        setDuration(mins)
        const goalLabel =
            WorkoutPlanGoalOptions.find((opt) => opt.value === selectedGoal)
                ?.label || selectedGoal
        const muscleLabels = selectedMuscles.map(
            (m) => MuscleGroupOptions.find((opt) => opt.value === m)?.label || m,
        )
        const focusLabel =
            primaryFocus === "No Preference"
                ? "No Preference"
                : MuscleGroupOptions.find((opt) => opt.value === primaryFocus)
                      ?.label || primaryFocus

        setMessages((prev) => [
            ...prev.filter((m) => m.step !== "duration"),
            { type: "user", content: `${mins} minutes`, step: "duration", editable: true },
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
    const renderChoices = (step: Step) => {
        switch (step) {
            case "goal":
                return (
                    <div className="flex flex-wrap gap-2 justify-end">
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
                        <div className="flex flex-wrap gap-2 justify-end">
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
                                    {option.label} {selectedMuscles.includes(option.value) && "✓"}
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
                    <div className="flex flex-wrap gap-2 justify-end">
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
            case "duration":
                return (
                    <div className="flex flex-wrap gap-2 justify-end">
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
                        <Button className="bg-primary text-primary-foreground hover:bg-primary/90">
                            Generate My Workout Plan
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

                        return (
                            <div
                                key={index}
                                className={`flex ${message.type === "user" ? "justify-end" : "justify-start"}`}
                            >
                                <div className="relative">
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
                                            onClick={() => handleEditStep(message.step!)}
                                            className="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-background border border-border shadow-sm text-muted-foreground transition-all hover:bg-accent hover:text-foreground hover:scale-110"
                                            title="Edit"
                                        >
                                            <Edit2 className="h-3.5 w-3.5" />
                                        </button>
                                    )}
                                </div>
                            </div>
                        )
                    })}
                </div>

            </div>
        </main>
    )
}

export default WorkoutPlanChat
