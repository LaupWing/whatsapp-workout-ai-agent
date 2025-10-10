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
import { useState } from "react"

const DAYS = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
]

export default function Onboarding() {
    const [trainingLocation, setTrainingLocation] = useState<string>("")
    const [hasDumbbells, setHasDumbbells] = useState(false)
    const [selectedDays, setSelectedDays] = useState<string[]>([])
    const [receiveMotivation, setReceiveMotivation] = useState(true)
    const [consent1, setConsent1] = useState(false)
    const [consent2, setConsent2] = useState(false)
    const [experienceLevel, setExperienceLevel] = useState<string>("beginner")
    const [email, setEmail] = useState<string>("")
    const [whatsappNumber] = useState<string>("+1 234 567 8900")

    const toggleDay = (day: string) => {
        setSelectedDays((prev) =>
            prev.includes(day) ? prev.filter((d) => d !== day) : [...prev, day],
        )
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        // Handle form submission
        console.log("Form submitted")
    }
    return (
        <main className="min-h-screen bg-background">
            <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-12 text-center">
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
                                    value={whatsappNumber}
                                    readOnly
                                    required
                                    className="cursor-not-allowed border-border bg-card text-card-foreground opacity-60"
                                />
                                <p className="text-sm text-muted-foreground">
                                    Used for identification and WhatsApp
                                    communication
                                </p>
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
                                    required
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                        className="border-border bg-card text-card-foreground"
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="gender"
                                        className="text-foreground"
                                    >
                                        Gender
                                    </Label>
                                    <Select>
                                        <SelectTrigger
                                            id="gender"
                                            className="border-border bg-card text-card-foreground"
                                        >
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male">
                                                Male
                                            </SelectItem>
                                            <SelectItem value="female">
                                                Female
                                            </SelectItem>
                                            <SelectItem value="other">
                                                Other
                                            </SelectItem>
                                            <SelectItem value="prefer-not-to-say">
                                                Prefer not to say
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
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
                                    min="1"
                                    max="120"
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                    min="1"
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                    min="1"
                                    step="0.1"
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                    min="1"
                                    step="0.1"
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                <Select>
                                    <SelectTrigger
                                        id="fitnessGoal"
                                        className="border-border bg-card text-card-foreground"
                                    >
                                        <SelectValue placeholder="Select your goal" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="lose-weight">
                                            Lose Weight
                                        </SelectItem>
                                        <SelectItem value="build-muscle">
                                            Build Muscle
                                        </SelectItem>
                                        <SelectItem value="maintain">
                                            Maintain
                                        </SelectItem>
                                        <SelectItem value="strength">
                                            Strength
                                        </SelectItem>
                                        <SelectItem value="endurance">
                                            Endurance
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-3">
                                <Label className="text-foreground">
                                    Experience Level
                                </Label>
                                <RadioGroup
                                    value={experienceLevel}
                                    onValueChange={setExperienceLevel}
                                    className="space-y-3"
                                >
                                    <div className="relative">
                                        <RadioGroupItem
                                            value="beginner"
                                            id="beginner"
                                            className="peer sr-only"
                                        />
                                        <Label
                                            htmlFor="beginner"
                                            className="flex cursor-pointer flex-col gap-1 rounded-md border-2 border-border bg-card p-4 transition-colors peer-data-[state=checked]:border-foreground peer-data-[state=checked]:bg-foreground/5 hover:border-foreground/50"
                                        >
                                            <span className="font-semibold text-card-foreground">
                                                Beginner (0–1 year)
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                Just starting or returning after
                                                a long break
                                            </span>
                                        </Label>
                                    </div>

                                    <div className="relative">
                                        <RadioGroupItem
                                            value="intermediate"
                                            id="intermediate"
                                            className="peer sr-only"
                                        />
                                        <Label
                                            htmlFor="intermediate"
                                            className="flex cursor-pointer flex-col gap-1 rounded-md border-2 border-border bg-card p-4 transition-colors peer-data-[state=checked]:border-foreground peer-data-[state=checked]:bg-foreground/5 hover:border-foreground/50"
                                        >
                                            <span className="font-semibold text-card-foreground">
                                                Intermediate (1–3 years)
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                Consistent training experience,
                                                understands basic form
                                            </span>
                                        </Label>
                                    </div>

                                    <div className="relative">
                                        <RadioGroupItem
                                            value="advanced"
                                            id="advanced"
                                            className="peer sr-only"
                                        />
                                        <Label
                                            htmlFor="advanced"
                                            className="flex cursor-pointer flex-col gap-1 rounded-md border-2 border-border bg-card p-4 transition-colors peer-data-[state=checked]:border-foreground peer-data-[state=checked]:bg-foreground/5 hover:border-foreground/50"
                                        >
                                            <span className="font-semibold text-card-foreground">
                                                Advanced (3+ years)
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                Regular training, focused goals,
                                                solid technique
                                            </span>
                                        </Label>
                                    </div>
                                </RadioGroup>
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
                                    value={trainingLocation}
                                    onValueChange={setTrainingLocation}
                                >
                                    <SelectTrigger
                                        id="trainingLocation"
                                        className="border-border bg-card text-card-foreground"
                                    >
                                        <SelectValue placeholder="Select location" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="gym">Gym</SelectItem>
                                        <SelectItem value="home">
                                            Home
                                        </SelectItem>
                                        <SelectItem value="both">
                                            Both
                                        </SelectItem>
                                    </SelectContent>
                                </Select>

                                {trainingLocation === "home" && (
                                    <div className="flex items-start gap-3 rounded-md border border-border bg-card p-4">
                                        <Checkbox
                                            id="dumbbells"
                                            checked={hasDumbbells}
                                            onCheckedChange={(checked) =>
                                                setHasDumbbells(
                                                    checked as boolean,
                                                )
                                            }
                                            required
                                        />
                                        <Label
                                            htmlFor="dumbbells"
                                            className="cursor-pointer text-sm leading-relaxed text-card-foreground"
                                        >
                                            I have at least one pair of
                                            dumbbells for training.{" "}
                                            <span className="text-muted-foreground">
                                                *
                                            </span>
                                        </Label>
                                    </div>
                                )}
                            </div>

                            <div className="space-y-3">
                                <Label className="text-foreground">
                                    Workout Days
                                </Label>
                                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    {DAYS.map((day) => (
                                        <div
                                            key={day}
                                            className="flex items-center gap-2 rounded-md border border-border bg-card p-3"
                                        >
                                            <Checkbox
                                                id={day}
                                                checked={selectedDays.includes(
                                                    day,
                                                )}
                                                onCheckedChange={() =>
                                                    toggleDay(day)
                                                }
                                            />
                                            <Label
                                                htmlFor={day}
                                                className="cursor-pointer text-sm text-card-foreground"
                                            >
                                                {day.slice(0, 3)}
                                            </Label>
                                        </div>
                                    ))}
                                </div>
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
                                    className="border-border bg-card text-card-foreground"
                                />
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
                                    checked={receiveMotivation}
                                    onCheckedChange={setReceiveMotivation}
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
                                    checked={consent1}
                                    onCheckedChange={(checked) =>
                                        setConsent1(checked as boolean)
                                    }
                                    required
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

                            <div className="flex items-start gap-3">
                                <Checkbox
                                    id="consent2"
                                    checked={consent2}
                                    onCheckedChange={(checked) =>
                                        setConsent2(checked as boolean)
                                    }
                                    required
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
                        </div>
                    </section>

                    {/* Submit Button */}
                    <div className="flex justify-center pt-6">
                        <Button
                            type="submit"
                            size="lg"
                            className="min-w-[200px] bg-foreground text-background hover:bg-foreground/90"
                        >
                            Complete Onboarding
                        </Button>
                    </div>
                </form>
            </div>
        </main>
    )
}
