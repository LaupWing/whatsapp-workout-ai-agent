import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { Switch } from "@/components/ui/switch"
import { ThemeToggle } from "@/components/theme-toggle"
import {
    ExperienceLevel,
    ExperienceLevelOptions,
    FitnessGoal,
    FitnessGoalOptions,
    Gender,
    GenderOptions,
    TrainingLocation,
    TrainingLocationOptions,
    WorkoutDay,
    WorkoutDayOptions,
} from "@/types/enums"
import { useForm } from "@inertiajs/react"

export default function Onboarding() {
    const { data, setData, post, processing, errors } = useForm({
        whatsapp_number: "+1 234 567 8900",
        email: "",
        name: "",
        gender: null as Gender | null,
        age: null as number | null,
        height: null as number | null,
        current_weight: null as number | null,
        target_weight: null as number | null,
        fitness_goal: null as FitnessGoal | null,
        experience_level: ExperienceLevel.BEGINNER,
        training_location: null as TrainingLocation | null,
        workout_days: [] as WorkoutDay[],
        reminder_time: null as string | null,
        receive_motivation_messages: true,
        consent_whatsapp: false,
        consent_data_usage: false,
    })

    const toggleDay = (day: WorkoutDay) => {
        const currentDays = data.workout_days
        setData(
            "workout_days",
            currentDays.includes(day)
                ? currentDays.filter((d) => d !== day)
                : [...currentDays, day],
        )
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        post("/onboarding")
    }
    return (
        <main className="min-h-screen bg-background">
            <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-12 text-center">
                    <div className="flex justify-end mb-4">
                        <ThemeToggle />
                    </div>
                    <h1 className="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">
                        User Onboarding
                    </h1>
                    <p className="mt-4 text-lg text-muted-foreground">
                        Complete your fitness profile to get started
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-12">
                    {/* Section 1 - Basic Information */}
                    <section className="space-y-6">
                        <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-sm bg-foreground font-mono text-sm font-bold text-background">
                                1
                            </div>
                            <h2 className="text-2xl font-semibold text-foreground">
                                Basic Information
                            </h2>
                        </div>

                        <div className="space-y-4 pl-11">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="whatsapp"
                                    className="text-foreground"
                                >
                                    WhatsApp Number{" "}
                                    <span className="text-muted-foreground">
                                        *
                                    </span>
                                </Label>
                                <Input
                                    id="whatsapp"
                                    type="tel"
                                    value={data.whatsapp_number}
                                    readOnly
                                    className="cursor-not-allowed border-border bg-card text-card-foreground opacity-60"
                                />
                                <p className="text-sm text-muted-foreground">
                                    Used for identification and WhatsApp
                                    communication
                                </p>
                                {errors.whatsapp_number && (
                                    <p className="text-sm text-red-500">
                                        {errors.whatsapp_number}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="email"
                                    className="text-foreground"
                                >
                                    Email Address{" "}
                                    <span className="text-muted-foreground">
                                        *
                                    </span>
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    placeholder="john@example.com"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.email && (
                                    <p className="text-sm text-red-500">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label
                                        htmlFor="fullName"
                                        className="text-foreground"
                                    >
                                        Full Name
                                    </Label>
                                    <Input
                                        id="fullName"
                                        type="text"
                                        placeholder="John Doe"
                                        value={data.name || ""}
                                        onChange={(e) =>
                                            setData("name", e.target.value)
                                        }
                                        className="border-border bg-card text-card-foreground"
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-500">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="gender"
                                        className="text-foreground"
                                    >
                                        Gender
                                    </Label>
                                    <Select
                                        value={data.gender || ""}
                                        onValueChange={(value) =>
                                            setData("gender", value as Gender)
                                        }
                                    >
                                        <SelectTrigger
                                            id="gender"
                                            className="border-border bg-card text-card-foreground"
                                        >
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {GenderOptions.map((option) => (
                                                <SelectItem
                                                    key={option.value}
                                                    value={option.value}
                                                >
                                                    {option.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.gender && (
                                        <p className="text-sm text-red-500">
                                            {errors.gender}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="age"
                                    className="text-foreground"
                                >
                                    Age
                                </Label>
                                <Input
                                    id="age"
                                    type="number"
                                    placeholder="25"
                                    value={data.age || ""}
                                    onChange={(e) =>
                                        setData(
                                            "age",
                                            e.target.value
                                                ? parseInt(e.target.value)
                                                : null,
                                        )
                                    }
                                    min="1"
                                    max="120"
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.age && (
                                    <p className="text-sm text-red-500">
                                        {errors.age}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>

                    {/* Section 2 - Body Stats */}
                    <section className="space-y-6">
                        <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-sm bg-foreground font-mono text-sm font-bold text-background">
                                2
                            </div>
                            <h2 className="text-2xl font-semibold text-foreground">
                                Body Stats
                            </h2>
                        </div>

                        <div className="grid gap-4 pl-11 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="height"
                                    className="text-foreground"
                                >
                                    Height (cm)
                                </Label>
                                <Input
                                    id="height"
                                    type="number"
                                    placeholder="175"
                                    value={data.height || ""}
                                    onChange={(e) =>
                                        setData(
                                            "height",
                                            e.target.value
                                                ? parseFloat(e.target.value)
                                                : null,
                                        )
                                    }
                                    min="1"
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.height && (
                                    <p className="text-sm text-red-500">
                                        {errors.height}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="currentWeight"
                                    className="text-foreground"
                                >
                                    Current Weight (kg)
                                </Label>
                                <Input
                                    id="currentWeight"
                                    type="number"
                                    placeholder="70"
                                    value={data.current_weight || ""}
                                    onChange={(e) =>
                                        setData(
                                            "current_weight",
                                            e.target.value
                                                ? parseFloat(e.target.value)
                                                : null,
                                        )
                                    }
                                    min="1"
                                    step="0.1"
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.current_weight && (
                                    <p className="text-sm text-red-500">
                                        {errors.current_weight}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="targetWeight"
                                    className="text-foreground"
                                >
                                    Target Weight (kg)
                                </Label>
                                <Input
                                    id="targetWeight"
                                    type="number"
                                    placeholder="65"
                                    value={data.target_weight || ""}
                                    onChange={(e) =>
                                        setData(
                                            "target_weight",
                                            e.target.value
                                                ? parseFloat(e.target.value)
                                                : null,
                                        )
                                    }
                                    min="1"
                                    step="0.1"
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.target_weight && (
                                    <p className="text-sm text-red-500">
                                        {errors.target_weight}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>

                    {/* Section 3 - Fitness Profile */}
                    <section className="space-y-6">
                        <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-sm bg-foreground font-mono text-sm font-bold text-background">
                                3
                            </div>
                            <h2 className="text-2xl font-semibold text-foreground">
                                Fitness Profile
                            </h2>
                        </div>

                        <div className="space-y-4 pl-11">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="fitnessGoal"
                                    className="text-foreground"
                                >
                                    Fitness Goal
                                </Label>
                                <Select
                                    value={data.fitness_goal || ""}
                                    onValueChange={(value) =>
                                        setData(
                                            "fitness_goal",
                                            value as FitnessGoal,
                                        )
                                    }
                                >
                                    <SelectTrigger
                                        id="fitnessGoal"
                                        className="border-border bg-card text-card-foreground"
                                    >
                                        <SelectValue placeholder="Select your goal" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {FitnessGoalOptions.map((option) => (
                                            <SelectItem
                                                key={option.value}
                                                value={option.value}
                                            >
                                                {option.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.fitness_goal && (
                                    <p className="text-sm text-red-500">
                                        {errors.fitness_goal}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-3">
                                <Label className="text-foreground">
                                    Experience Level
                                </Label>
                                <RadioGroup
                                    value={data.experience_level}
                                    onValueChange={(value) =>
                                        setData(
                                            "experience_level",
                                            value as ExperienceLevel,
                                        )
                                    }
                                    className="space-y-3"
                                >
                                    {ExperienceLevelOptions.map((option) => (
                                        <div
                                            key={option.value}
                                            className="relative"
                                        >
                                            <RadioGroupItem
                                                value={option.value}
                                                id={option.value}
                                                className="peer sr-only"
                                            />
                                            <Label
                                                htmlFor={option.value}
                                                className="flex cursor-pointer flex-col gap-1 rounded-md border-2 border-border bg-card p-4 transition-colors peer-data-[state=checked]:border-foreground peer-data-[state=checked]:bg-foreground/5 hover:border-foreground/50"
                                            >
                                                <span className="font-semibold text-card-foreground">
                                                    {option.label} (
                                                    {option.description})
                                                </span>
                                                <span className="text-sm text-muted-foreground">
                                                    {option.fullDescription}
                                                </span>
                                            </Label>
                                        </div>
                                    ))}
                                </RadioGroup>
                                {errors.experience_level && (
                                    <p className="text-sm text-red-500">
                                        {errors.experience_level}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>

                    {/* Section 4 - Preferences */}
                    <section className="space-y-6">
                        <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-sm bg-foreground font-mono text-sm font-bold text-background">
                                4
                            </div>
                            <h2 className="text-2xl font-semibold text-foreground">
                                Preferences
                            </h2>
                        </div>

                        <div className="space-y-6 pl-11">
                            <div className="space-y-3">
                                <Label
                                    htmlFor="trainingLocation"
                                    className="text-foreground"
                                >
                                    Training Location
                                </Label>
                                <Select
                                    value={data.training_location || ""}
                                    onValueChange={(value) =>
                                        setData(
                                            "training_location",
                                            value as TrainingLocation,
                                        )
                                    }
                                >
                                    <SelectTrigger
                                        id="trainingLocation"
                                        className="border-border bg-card text-card-foreground"
                                    >
                                        <SelectValue placeholder="Select location" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {TrainingLocationOptions.map(
                                            (option) => (
                                                <SelectItem
                                                    key={option.value}
                                                    value={option.value}
                                                >
                                                    {option.label}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                                {errors.training_location && (
                                    <p className="text-sm text-red-500">
                                        {errors.training_location}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-3">
                                <Label className="text-foreground">
                                    Workout Days
                                </Label>
                                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    {WorkoutDayOptions.map((option) => (
                                        <div
                                            key={option.value}
                                            className="flex items-center gap-2 rounded-md border border-border bg-card p-3"
                                        >
                                            <Checkbox
                                                id={option.value}
                                                checked={data.workout_days.includes(
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
                                {errors.workout_days && (
                                    <p className="text-sm text-red-500">
                                        {errors.workout_days}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="reminderTime"
                                    className="text-foreground"
                                >
                                    Preferred Reminder Time
                                </Label>
                                <Input
                                    id="reminderTime"
                                    type="time"
                                    value={data.reminder_time || ""}
                                    onChange={(e) =>
                                        setData("reminder_time", e.target.value)
                                    }
                                    className="border-border bg-card text-card-foreground"
                                />
                                {errors.reminder_time && (
                                    <p className="text-sm text-red-500">
                                        {errors.reminder_time}
                                    </p>
                                )}
                            </div>

                            <div className="flex items-center justify-between rounded-md border border-border bg-card p-4">
                                <div className="space-y-1">
                                    <Label
                                        htmlFor="motivation"
                                        className="cursor-pointer text-card-foreground"
                                    >
                                        Receive Motivation Messages
                                    </Label>
                                    <p className="text-sm text-muted-foreground">
                                        Get daily motivation and encouragement
                                    </p>
                                </div>
                                <Switch
                                    id="motivation"
                                    checked={
                                        data.receive_motivation_messages
                                    }
                                    onCheckedChange={(checked) =>
                                        setData(
                                            "receive_motivation_messages",
                                            checked,
                                        )
                                    }
                                />
                            </div>
                        </div>
                    </section>

                    {/* Section 5 - Consent */}
                    <section className="space-y-6">
                        <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-sm bg-foreground font-mono text-sm font-bold text-background">
                                5
                            </div>
                            <h2 className="text-2xl font-semibold text-foreground">
                                Consent
                            </h2>
                        </div>

                        <div className="space-y-4 pl-11">
                            <div className="flex items-start gap-3">
                                <Checkbox
                                    id="consent1"
                                    checked={data.consent_whatsapp}
                                    onCheckedChange={(checked) =>
                                        setData(
                                            "consent_whatsapp",
                                            checked as boolean,
                                        )
                                    }
                                />
                                <Label
                                    htmlFor="consent1"
                                    className="cursor-pointer text-sm leading-relaxed text-foreground"
                                >
                                    I agree to receive WhatsApp messages related
                                    to workouts and reminders.{" "}
                                    <span className="text-muted-foreground">
                                        *
                                    </span>
                                </Label>
                            </div>
                            {errors.consent_whatsapp && (
                                <p className="text-sm text-red-500">
                                    {errors.consent_whatsapp}
                                </p>
                            )}

                            <div className="flex items-start gap-3">
                                <Checkbox
                                    id="consent2"
                                    checked={data.consent_data_usage}
                                    onCheckedChange={(checked) =>
                                        setData(
                                            "consent_data_usage",
                                            checked as boolean,
                                        )
                                    }
                                />
                                <Label
                                    htmlFor="consent2"
                                    className="cursor-pointer text-sm leading-relaxed text-foreground"
                                >
                                    I consent to my data being used for
                                    personalized fitness tracking.{" "}
                                    <span className="text-muted-foreground">
                                        *
                                    </span>
                                </Label>
                            </div>
                            {errors.consent_data_usage && (
                                <p className="text-sm text-red-500">
                                    {errors.consent_data_usage}
                                </p>
                            )}
                        </div>
                    </section>

                    {/* Submit Button */}
                    <div className="flex justify-center pt-6">
                        <Button
                            type="submit"
                            size="lg"
                            disabled={
                                processing ||
                                !data.consent_whatsapp ||
                                !data.consent_data_usage
                            }
                            className="min-w-[200px] bg-foreground text-background hover:bg-foreground/90"
                        >
                            {processing
                                ? "Creating Account..."
                                : "Complete Onboarding"}
                        </Button>
                    </div>
                </form>
            </div>
        </main>
    )
}
