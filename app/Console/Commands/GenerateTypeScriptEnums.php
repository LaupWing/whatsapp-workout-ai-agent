<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ReflectionEnum;

class GenerateTypeScriptEnums extends Command
{
    protected $signature = 'typescript:generate-enums';

    protected $description = 'Generate TypeScript enums from PHP enums';

    private array $enums = [
        \App\Enums\Gender::class,
        \App\Enums\FitnessGoal::class,
        \App\Enums\ExperienceLevel::class,
        \App\Enums\TrainingLocation::class,
        \App\Enums\WorkoutDay::class,
    ];

    public function handle(): int
    {
        $output = $this->generateTypeScriptEnums();

        $filePath = resource_path('js/types/enums.ts');

        file_put_contents($filePath, $output);

        $this->info("TypeScript enums generated successfully at: {$filePath}");

        return Command::SUCCESS;
    }

    private function generateTypeScriptEnums(): string
    {
        $output = "// This file is auto-generated. Do not edit manually.\n";
        $output .= "// Run 'php artisan typescript:generate-enums' to regenerate.\n\n";

        foreach ($this->enums as $enumClass) {
            $output .= $this->generateEnum($enumClass);
        }

        return $output;
    }

    private function generateEnum(string $enumClass): string
    {
        $reflection = new ReflectionEnum($enumClass);
        $enumName = $reflection->getShortName();

        $output = "// {$enumName}\n";
        $output .= "export enum {$enumName} {\n";

        foreach ($enumClass::cases() as $case) {
            $caseName = $case->name;
            $caseValue = $case->value;
            $output .= "    {$caseName} = '{$caseValue}',\n";
        }

        $output .= "}\n\n";

        // Generate options type
        $output .= "export interface {$enumName}Option {\n";
        $output .= "    value: {$enumName}\n";
        $output .= "    label: string\n";

        // Add extra fields based on enum methods
        if (method_exists($enumClass, 'description')) {
            $output .= "    description?: string\n";
        }
        if (method_exists($enumClass, 'fullDescription')) {
            $output .= "    fullDescription?: string\n";
        }
        if (method_exists($enumClass, 'shortLabel')) {
            $output .= "    shortLabel?: string\n";
        }

        $output .= "}\n\n";

        // Generate options array
        $output .= "export const {$enumName}Options: {$enumName}Option[] = [\n";

        foreach ($enumClass::options() as $option) {
            $output .= "    {\n";
            $output .= "        value: {$enumName}.{$this->getEnumCaseName($enumClass, $option['value'])},\n";
            $output .= "        label: " . json_encode($option['label']) . ",\n";

            if (isset($option['description'])) {
                $output .= "        description: " . json_encode($option['description']) . ",\n";
            }
            if (isset($option['fullDescription'])) {
                $output .= "        fullDescription: " . json_encode($option['fullDescription']) . ",\n";
            }
            if (isset($option['shortLabel'])) {
                $output .= "        shortLabel: " . json_encode($option['shortLabel']) . ",\n";
            }

            $output .= "    },\n";
        }

        $output .= "]\n\n";

        return $output;
    }

    private function getEnumCaseName(string $enumClass, string $value): string
    {
        foreach ($enumClass::cases() as $case) {
            if ($case->value === $value) {
                return $case->name;
            }
        }

        throw new \Exception("Could not find enum case for value: {$value}");
    }
}
