<?php

use App\DTOs\TodoDTO;
use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use App\Repositories\Eloquent\TodoRepository;

test('service container resolves repository contract to eloquent implementation', function () {
    $repository = app(TodoRepositoryInterface::class);

    expect($repository)->toBeInstanceOf(TodoRepository::class);
});

test('middleware attaches architecture tracking headers and request context', function () {
    $response = $this->get('/todos');

    $response->assertOk();
    $response->assertHeader('X-Architecture-Pipeline');
    $response->assertHeader('X-Request-Id');
    expect($response->headers->get('X-Architecture-Pipeline'))->toContain('HTTP -> Route -> Middleware');
});

test('can list todos and calculate statistics via web route', function () {
    $response = $this->get('/todos');

    $response->assertOk();
    $response->assertViewIs('todos.index');
    $response->assertViewHas(['todos', 'statistics', 'filters']);
});

test('can store a new todo through form request, DTO, service, and repository pipeline', function () {
    $payload = [
        'title' => 'Feature Test Architecture Pipeline',
        'description' => 'Validating the end-to-end multi-tier pipeline.',
        'priority' => 'high',
        'due_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $response = $this->post('/todos', $payload);

    $response->assertRedirect('/todos');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('todos', [
        'title' => 'Feature Test Architecture Pipeline',
        'priority' => 'high',
        'is_completed' => false,
    ]);
});

test('validation prevents storing todo without valid title and priority', function () {
    $response = $this->post('/todos', [
        'title' => '',
        'priority' => 'invalid-priority',
    ]);

    $response->assertSessionHasErrors(['title', 'priority']);
});

test('can view single todo item', function () {
    $todo = Todo::factory()->create([
        'title' => 'Inspectable Task',
    ]);

    $response = $this->get("/todos/{$todo->id}");

    $response->assertOk();
    $response->assertViewIs('todos.show');
    $response->assertSee('Inspectable Task');
});

test('can update existing todo through update request and DTO', function () {
    $todo = Todo::factory()->create([
        'title' => 'Initial Title',
        'priority' => 'low',
    ]);

    $response = $this->put("/todos/{$todo->id}", [
        'title' => 'Updated Title Value',
        'description' => 'Updated description content.',
        'priority' => 'high',
        'is_completed' => 1,
    ]);

    $response->assertRedirect("/todos/{$todo->id}");
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'title' => 'Updated Title Value',
        'priority' => 'high',
        'is_completed' => true,
    ]);
});

test('can toggle todo completion status', function () {
    $todo = Todo::factory()->pending()->create();

    $response = $this->patch("/todos/{$todo->id}/toggle");

    $response->assertRedirect();
    expect($todo->fresh()->is_completed)->toBeTrue();

    // Toggle back to false
    $this->patch("/todos/{$todo->id}/toggle");
    expect($todo->fresh()->is_completed)->toBeFalse();
});

test('can delete a todo item', function () {
    $todo = Todo::factory()->create();

    $response = $this->delete("/todos/{$todo->id}");

    $response->assertRedirect('/todos');
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('todos', [
        'id' => $todo->id,
    ]);
});

test('api endpoints operate through the same multi-tier architecture', function () {
    // 1. List API
    $listResponse = $this->getJson('/api/todos');
    $listResponse->assertOk()
        ->assertJsonStructure([
            'status',
            'data',
            'meta' => ['statistics', 'filters'],
        ]);

    // 2. Store API
    $storeResponse = $this->postJson('/api/todos', [
        'title' => 'API Created Task',
        'description' => 'Created via REST endpoint.',
        'priority' => 'medium',
    ]);

    $storeResponse->assertCreated()
        ->assertJsonPath('data.title', 'API Created Task');

    $createdId = $storeResponse->json('data.id');

    // 3. Show API
    $this->getJson("/api/todos/{$createdId}")
        ->assertOk()
        ->assertJsonPath('data.title', 'API Created Task');

    // 4. Update API
    $this->putJson("/api/todos/{$createdId}", [
        'title' => 'API Updated Task',
        'priority' => 'high',
        'is_completed' => true,
    ])->assertOk()
        ->assertJsonPath('data.title', 'API Updated Task')
        ->assertJsonPath('data.is_completed', true);

    // 5. Toggle API
    $this->patchJson("/api/todos/{$createdId}/toggle")
        ->assertOk()
        ->assertJsonPath('data.is_completed', false);

    // 6. Delete API
    $this->deleteJson("/api/todos/{$createdId}")
        ->assertOk()
        ->assertJsonPath('status', 'success');
});

test('TodoDTO safely parses and transforms data structures', function () {
    $dto = TodoDTO::fromArray([
        'title' => '  Clean Architecture  ',
        'description' => '  Trimming spaces  ',
        'priority' => 'high',
        'is_completed' => '1',
        'due_date' => '2026-10-01',
    ]);

    expect($dto->title)->toBe('Clean Architecture')
        ->and($dto->description)->toBe('Trimming spaces')
        ->and($dto->priority)->toBe('high')
        ->and($dto->isCompleted)->toBeTrue()
        ->and($dto->dueDate)->toBe('2026-10-01');

    $array = $dto->toArray();
    expect($array['title'])->toBe('Clean Architecture')
        ->and($array['is_completed'])->toBeTrue();
});
