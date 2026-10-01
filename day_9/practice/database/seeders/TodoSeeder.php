<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todos = [
            [
                'title' => 'Define Domain DTOs and Data Transfer Contract',
                'description' => 'Create immutable TodoDTO to safely transport validated data from HTTP Form Requests to the Service layer.',
                'is_completed' => true,
                'priority' => 'high',
                'due_date' => now()->subDays(2)->format('Y-m-d'),
            ],
            [
                'title' => 'Implement TodoRepositoryInterface and Service Container Binding',
                'description' => 'Abstract database access behind TodoRepositoryInterface and register binding in AppServiceProvider.',
                'is_completed' => true,
                'priority' => 'high',
                'due_date' => now()->subDay()->format('Y-m-d'),
            ],
            [
                'title' => 'Configure TodoActivityMiddleware for Pipeline Tracing',
                'description' => 'Capture request context, sanitize text inputs, and inject X-Architecture-Pipeline trace headers.',
                'is_completed' => true,
                'priority' => 'medium',
                'due_date' => now()->format('Y-m-d'),
            ],
            [
                'title' => 'Write Comprehensive Pest Feature Tests',
                'description' => 'Validate every step in the 12-tier architecture: HTTP -> Route -> Middleware -> Form Request -> Controller -> DTO -> Service -> Repo -> Eloquent -> DB.',
                'is_completed' => false,
                'priority' => 'high',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
            ],
            [
                'title' => 'Verify REST API Endpoints with JSON Responses',
                'description' => 'Ensure /api/todos supports full CRUD operations through the exact same architectural pipeline.',
                'is_completed' => false,
                'priority' => 'medium',
                'due_date' => now()->addDays(5)->format('Y-m-d'),
            ],
            [
                'title' => 'Final Codebase Polish and Pint Formatting',
                'description' => 'Run Laravel Pint code formatter and check test suite passes cleanly.',
                'is_completed' => false,
                'priority' => 'low',
                'due_date' => now()->addWeek()->format('Y-m-d'),
            ],
        ];

        foreach ($todos as $todo) {
            Todo::query()->firstOrCreate(
                ['title' => $todo['title']],
                $todo
            );
        }
    }
}
