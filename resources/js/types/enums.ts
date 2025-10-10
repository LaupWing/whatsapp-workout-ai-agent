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

// ExerciseCategory
export enum ExerciseCategory {
    STRENGTH = 'strength',
    CARDIO = 'cardio',
    FLEXIBILITY = 'flexibility',
    SPORTS = 'sports',
}

export interface ExerciseCategoryOption {
    value: ExerciseCategory
    label: string
}

export const ExerciseCategoryOptions: ExerciseCategoryOption[] = [
    {
        value: ExerciseCategory.STRENGTH,
        label: "Strength",
    },
    {
        value: ExerciseCategory.CARDIO,
        label: "Cardio",
    },
    {
        value: ExerciseCategory.FLEXIBILITY,
        label: "Flexibility",
    },
    {
        value: ExerciseCategory.SPORTS,
        label: "Sports",
    },
]

// MuscleGroup
export enum MuscleGroup {
    CHEST = 'chest',
    LEGS = 'legs',
    BACK = 'back',
    SHOULDERS = 'shoulders',
    ARMS = 'arms',
    CORE = 'core',
    FULL_BODY = 'full_body',
}

export interface MuscleGroupOption {
    value: MuscleGroup
    label: string
}

export const MuscleGroupOptions: MuscleGroupOption[] = [
    {
        value: MuscleGroup.CHEST,
        label: "Chest",
    },
    {
        value: MuscleGroup.LEGS,
        label: "Legs",
    },
    {
        value: MuscleGroup.BACK,
        label: "Back",
    },
    {
        value: MuscleGroup.SHOULDERS,
        label: "Shoulders",
    },
    {
        value: MuscleGroup.ARMS,
        label: "Arms",
    },
    {
        value: MuscleGroup.CORE,
        label: "Core",
    },
    {
        value: MuscleGroup.FULL_BODY,
        label: "Full Body",
    },
]

// Equipment
export enum Equipment {
    BARBELL = 'barbell',
    DUMBBELL = 'dumbbell',
    MACHINE = 'machine',
    BODYWEIGHT = 'bodyweight',
    CABLE = 'cable',
    OTHER = 'other',
}

export interface EquipmentOption {
    value: Equipment
    label: string
}

export const EquipmentOptions: EquipmentOption[] = [
    {
        value: Equipment.BARBELL,
        label: "Barbell",
    },
    {
        value: Equipment.DUMBBELL,
        label: "Dumbbell",
    },
    {
        value: Equipment.MACHINE,
        label: "Machine",
    },
    {
        value: Equipment.BODYWEIGHT,
        label: "Bodyweight",
    },
    {
        value: Equipment.CABLE,
        label: "Cable",
    },
    {
        value: Equipment.OTHER,
        label: "Other",
    },
]

// ExerciseDifficulty
export enum ExerciseDifficulty {
    BEGINNER = 'beginner',
    INTERMEDIATE = 'intermediate',
    ADVANCED = 'advanced',
}

export interface ExerciseDifficultyOption {
    value: ExerciseDifficulty
    label: string
}

export const ExerciseDifficultyOptions: ExerciseDifficultyOption[] = [
    {
        value: ExerciseDifficulty.BEGINNER,
        label: "Beginner",
    },
    {
        value: ExerciseDifficulty.INTERMEDIATE,
        label: "Intermediate",
    },
    {
        value: ExerciseDifficulty.ADVANCED,
        label: "Advanced",
    },
]

// WorkoutPlanGoal
export enum WorkoutPlanGoal {
    STRENGTH = 'strength',
    HYPERTROPHY = 'hypertrophy',
    ENDURANCE = 'endurance',
    WEIGHT_LOSS = 'weight_loss',
    GENERAL_FITNESS = 'general_fitness',
}

export interface WorkoutPlanGoalOption {
    value: WorkoutPlanGoal
    label: string
}

export const WorkoutPlanGoalOptions: WorkoutPlanGoalOption[] = [
    {
        value: WorkoutPlanGoal.STRENGTH,
        label: "Strength",
    },
    {
        value: WorkoutPlanGoal.HYPERTROPHY,
        label: "Hypertrophy",
    },
    {
        value: WorkoutPlanGoal.ENDURANCE,
        label: "Endurance",
    },
    {
        value: WorkoutPlanGoal.WEIGHT_LOSS,
        label: "Weight Loss",
    },
    {
        value: WorkoutPlanGoal.GENERAL_FITNESS,
        label: "General Fitness",
    },
]

// WorkoutPlanStatus
export enum WorkoutPlanStatus {
    ACTIVE = 'active',
    COMPLETED = 'completed',
    PAUSED = 'paused',
    ARCHIVED = 'archived',
}

export interface WorkoutPlanStatusOption {
    value: WorkoutPlanStatus
    label: string
}

export const WorkoutPlanStatusOptions: WorkoutPlanStatusOption[] = [
    {
        value: WorkoutPlanStatus.ACTIVE,
        label: "Active",
    },
    {
        value: WorkoutPlanStatus.COMPLETED,
        label: "Completed",
    },
    {
        value: WorkoutPlanStatus.PAUSED,
        label: "Paused",
    },
    {
        value: WorkoutPlanStatus.ARCHIVED,
        label: "Archived",
    },
]

