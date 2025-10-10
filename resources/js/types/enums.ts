// This file is auto-generated. Do not edit manually.
// Run 'php artisan typescript:generate-enums' to regenerate.

// Gender
export enum Gender {
    MALE = 'male',
    FEMALE = 'female',
    OTHER = 'other',
    PREFER_NOT_TO_SAY = 'prefer_not_to_say',
}

export interface GenderOption {
    value: Gender
    label: string
}

export const GenderOptions: GenderOption[] = [
    {
        value: Gender.MALE,
        label: "Male",
    },
    {
        value: Gender.FEMALE,
        label: "Female",
    },
    {
        value: Gender.OTHER,
        label: "Other",
    },
    {
        value: Gender.PREFER_NOT_TO_SAY,
        label: "Prefer not to say",
    },
]

// FitnessGoal
export enum FitnessGoal {
    LOSE_WEIGHT = 'lose_weight',
    BUILD_MUSCLE = 'build_muscle',
    MAINTAIN = 'maintain',
    STRENGTH = 'strength',
    ENDURANCE = 'endurance',
}

export interface FitnessGoalOption {
    value: FitnessGoal
    label: string
}

export const FitnessGoalOptions: FitnessGoalOption[] = [
    {
        value: FitnessGoal.LOSE_WEIGHT,
        label: "Lose Weight",
    },
    {
        value: FitnessGoal.BUILD_MUSCLE,
        label: "Build Muscle",
    },
    {
        value: FitnessGoal.MAINTAIN,
        label: "Maintain",
    },
    {
        value: FitnessGoal.STRENGTH,
        label: "Strength",
    },
    {
        value: FitnessGoal.ENDURANCE,
        label: "Endurance",
    },
]

// ExperienceLevel
export enum ExperienceLevel {
    BEGINNER = 'beginner',
    INTERMEDIATE = 'intermediate',
    ADVANCED = 'advanced',
}

export interface ExperienceLevelOption {
    value: ExperienceLevel
    label: string
    description?: string
    fullDescription?: string
}

export const ExperienceLevelOptions: ExperienceLevelOption[] = [
    {
        value: ExperienceLevel.BEGINNER,
        label: "Beginner",
        description: "0\u20131 year",
        fullDescription: "Just starting or returning after a long break",
    },
    {
        value: ExperienceLevel.INTERMEDIATE,
        label: "Intermediate",
        description: "1\u20133 years",
        fullDescription: "Consistent training experience, understands basic form",
    },
    {
        value: ExperienceLevel.ADVANCED,
        label: "Advanced",
        description: "3+ years",
        fullDescription: "Regular training, focused goals, solid technique",
    },
]

// TrainingLocation
export enum TrainingLocation {
    GYM = 'gym',
    HOME_WITH_DUMBBELL = 'home_with_dumbbell',
    BOTH = 'both',
}

export interface TrainingLocationOption {
    value: TrainingLocation
    label: string
}

export const TrainingLocationOptions: TrainingLocationOption[] = [
    {
        value: TrainingLocation.GYM,
        label: "Gym",
    },
    {
        value: TrainingLocation.HOME_WITH_DUMBBELL,
        label: "Home (Dumbbell required)",
    },
    {
        value: TrainingLocation.BOTH,
        label: "Both",
    },
]

// WorkoutDay
export enum WorkoutDay {
    MONDAY = 'monday',
    TUESDAY = 'tuesday',
    WEDNESDAY = 'wednesday',
    THURSDAY = 'thursday',
    FRIDAY = 'friday',
    SATURDAY = 'saturday',
    SUNDAY = 'sunday',
}

export interface WorkoutDayOption {
    value: WorkoutDay
    label: string
    shortLabel?: string
}

export const WorkoutDayOptions: WorkoutDayOption[] = [
    {
        value: WorkoutDay.MONDAY,
        label: "Monday",
        shortLabel: "Mon",
    },
    {
        value: WorkoutDay.TUESDAY,
        label: "Tuesday",
        shortLabel: "Tue",
    },
    {
        value: WorkoutDay.WEDNESDAY,
        label: "Wednesday",
        shortLabel: "Wed",
    },
    {
        value: WorkoutDay.THURSDAY,
        label: "Thursday",
        shortLabel: "Thu",
    },
    {
        value: WorkoutDay.FRIDAY,
        label: "Friday",
        shortLabel: "Fri",
    },
    {
        value: WorkoutDay.SATURDAY,
        label: "Saturday",
        shortLabel: "Sat",
    },
    {
        value: WorkoutDay.SUNDAY,
        label: "Sunday",
        shortLabel: "Sun",
    },
]

