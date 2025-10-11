import { Button } from "@/components/ui/button"
import {
    DndContext,
    DragEndEvent,
    DragOverlay,
    DragStartEvent,
    MouseSensor,
    TouchSensor,
    closestCenter,
    useSensor,
    useSensors,
} from "@dnd-kit/core"
import {
    SortableContext,
    arrayMove,
    useSortable,
    verticalListSortingStrategy,
} from "@dnd-kit/sortable"
import { CSS } from "@dnd-kit/utilities"
import { GripVertical, Save } from "lucide-react"
import { useState } from "react"

export interface Exercise {
    id: number
    name: string
    muscle_group: string
    target_sets: number
    target_reps: number
    rest_seconds: number
}

export interface DayExercises {
    day: string
    mainFocus?: string
    exercises: Exercise[]
}

export interface WorkoutPlanData {
    id: number
    name: string
    description: string
    goal: string
    plan_exercises: Array<{
        id: number
        exercise_id: number
        day_of_week: string
        order: number
        target_sets: number
        target_reps: number
        rest_seconds: number
        exercise: {
            id: number
            name: string
            muscle_group: string
        }
    }>
}

interface WorkoutPlanEditorProps {
    workoutPlan: WorkoutPlanData
    onSave: (editedPlan: DayExercises[]) => void
}

const SortableExercise = ({
    exercise,
    isDragging,
}: {
    exercise: Exercise
    isDragging?: boolean
}) => {
    const {
        attributes,
        listeners,
        setNodeRef,
        transform,
        transition,
        isDragging: isSortableDragging,
    } = useSortable({ id: exercise.id.toString() })

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isSortableDragging ? 0.5 : 1,
    }

    return (
        <div
            ref={setNodeRef}
            style={style}
            className="group flex items-center gap-3 rounded-lg border border-border bg-card p-3 shadow-sm transition-colors hover:bg-accent"
        >
            <div
                {...attributes}
                {...listeners}
                className="cursor-grab active:cursor-grabbing"
            >
                <GripVertical className="h-5 w-5 text-muted-foreground transition-colors group-hover:text-foreground" />
            </div>
            <div className="flex-1">
                <p className="font-medium text-foreground">{exercise.name}</p>
                <p className="text-sm text-muted-foreground">
                    {exercise.target_sets} sets × {exercise.target_reps} reps •{" "}
                    {exercise.rest_seconds}s rest
                </p>
            </div>
        </div>
    )
}

export const WorkoutPlanEditor = ({
    workoutPlan,
    onSave,
}: WorkoutPlanEditorProps) => {
    const daysOfWeek = [
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
        "sunday",
    ]

    // Group exercises by day
    const initialGroupedExercises: DayExercises[] = daysOfWeek.map((day) => {
        const dayExercises = workoutPlan.plan_exercises
            .filter((pe) => pe.day_of_week.toLowerCase() === day)
            .sort((a, b) => a.order - b.order)
            .map((pe) => ({
                id: pe.id,
                name: pe.exercise.name,
                muscle_group: pe.exercise.muscle_group,
                target_sets: pe.target_sets,
                target_reps: pe.target_reps,
                rest_seconds: pe.rest_seconds,
            }))

        return {
            day,
            mainFocus: dayExercises.length > 0 ? undefined : undefined,
            exercises: dayExercises,
        }
    })

    const [groupedExercises, setGroupedExercises] = useState<DayExercises[]>(
        initialGroupedExercises,
    )
    const [activeId, setActiveId] = useState<string | null>(null)
    const [activeDay, setActiveDay] = useState<string | null>(null)

    const sensors = useSensors(
        useSensor(MouseSensor, {
            activationConstraint: {
                distance: 8,
            },
        }),
        useSensor(TouchSensor, {
            activationConstraint: {
                delay: 200,
                tolerance: 6,
            },
        }),
    )

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event
        setActiveId(active.id.toString())

        // Find which day this exercise belongs to
        for (const dayData of groupedExercises) {
            if (
                dayData.exercises.some((ex) => ex.id.toString() === active.id)
            ) {
                setActiveDay(dayData.day)
                break
            }
        }
    }

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event

        if (!over) {
            setActiveId(null)
            setActiveDay(null)
            return
        }

        // Find source day
        const sourceDay = groupedExercises.find((day) =>
            day.exercises.some((ex) => ex.id.toString() === active.id),
        )

        // Find target day - check if over is a day or an exercise
        let targetDay = groupedExercises.find((day) => day.day === over.id)

        if (!targetDay) {
            // over is an exercise, find its day
            targetDay = groupedExercises.find((day) =>
                day.exercises.some((ex) => ex.id.toString() === over.id),
            )
        }

        if (!sourceDay || !targetDay) {
            setActiveId(null)
            setActiveDay(null)
            return
        }

        const activeExercise = sourceDay.exercises.find(
            (ex) => ex.id.toString() === active.id,
        )
        if (!activeExercise) {
            setActiveId(null)
            setActiveDay(null)
            return
        }

        // Same day reorder
        if (sourceDay.day === targetDay.day) {
            const oldIndex = sourceDay.exercises.findIndex(
                (ex) => ex.id.toString() === active.id,
            )
            const newIndex = targetDay.exercises.findIndex(
                (ex) => ex.id.toString() === over.id,
            )

            if (oldIndex !== -1 && newIndex !== -1) {
                const newExercises = arrayMove(
                    sourceDay.exercises,
                    oldIndex,
                    newIndex,
                )

                setGroupedExercises((prev) =>
                    prev.map((day) =>
                        day.day === sourceDay.day
                            ? { ...day, exercises: newExercises }
                            : day,
                    ),
                )
            }
        } else {
            // Move between days
            setGroupedExercises((prev) =>
                prev.map((day) => {
                    if (day.day === sourceDay.day) {
                        return {
                            ...day,
                            exercises: day.exercises.filter(
                                (ex) => ex.id.toString() !== active.id,
                            ),
                        }
                    }
                    if (day.day === targetDay.day) {
                        const targetIndex =
                            over.id === targetDay.day
                                ? day.exercises.length
                                : day.exercises.findIndex(
                                      (ex) => ex.id.toString() === over.id,
                                  )

                        const newExercises = [...day.exercises]
                        newExercises.splice(
                            targetIndex >= 0 ? targetIndex : newExercises.length,
                            0,
                            activeExercise,
                        )
                        return { ...day, exercises: newExercises }
                    }
                    return day
                }),
            )
        }

        setActiveId(null)
        setActiveDay(null)
    }

    const handleSave = () => {
        onSave(groupedExercises)
    }

    const activeExercise =
        activeId && activeDay
            ? groupedExercises
                  .find((d) => d.day === activeDay)
                  ?.exercises.find((ex) => ex.id.toString() === activeId)
            : null

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-2xl font-bold text-foreground">
                        {workoutPlan.name}
                    </h2>
                    <p className="text-sm text-muted-foreground">
                        {workoutPlan.description}
                    </p>
                </div>
                <Button
                    onClick={handleSave}
                    className="bg-primary text-primary-foreground hover:bg-primary/90"
                >
                    <Save className="mr-2 h-4 w-4" />
                    Save Plan
                </Button>
            </div>

            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragStart={handleDragStart}
                onDragEnd={handleDragEnd}
            >
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {groupedExercises.map((dayData) => (
                        <div
                            key={dayData.day}
                            className="space-y-3 rounded-lg border border-border bg-muted/30 p-4"
                        >
                            <h3 className="font-semibold capitalize text-foreground">
                                {dayData.day}
                            </h3>

                            {dayData.exercises.length === 0 ? (
                                <div
                                    id={dayData.day}
                                    className="flex h-24 items-center justify-center rounded-lg border-2 border-dashed border-border bg-background/50 text-sm text-muted-foreground"
                                >
                                    Rest Day - Drag exercises here
                                </div>
                            ) : (
                                <SortableContext
                                    items={dayData.exercises.map((ex) =>
                                        ex.id.toString(),
                                    )}
                                    strategy={verticalListSortingStrategy}
                                >
                                    <div
                                        id={dayData.day}
                                        className="space-y-2"
                                    >
                                        {dayData.exercises.map((exercise) => (
                                            <SortableExercise
                                                key={exercise.id}
                                                exercise={exercise}
                                            />
                                        ))}
                                    </div>
                                </SortableContext>
                            )}
                        </div>
                    ))}
                </div>

                <DragOverlay>
                    {activeExercise ? (
                        <div className="flex items-center gap-3 rounded-lg border border-primary bg-card p-3 shadow-lg">
                            <GripVertical className="h-5 w-5 text-primary" />
                            <div className="flex-1">
                                <p className="font-medium text-foreground">
                                    {activeExercise.name}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    {activeExercise.target_sets} sets ×{" "}
                                    {activeExercise.target_reps} reps
                                </p>
                            </div>
                        </div>
                    ) : null}
                </DragOverlay>
            </DndContext>

            <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/30">
                <p className="text-sm text-blue-800 dark:text-blue-200">
                    💡 <strong>Tip:</strong> Drag exercises between days to
                    reorganize your plan. You can also reorder exercises within
                    the same day.
                </p>
            </div>
        </div>
    )
}
