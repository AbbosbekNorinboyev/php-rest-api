<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TaskService;

// Controller: HTTP endpointlarni TaskService metodlariga yo'naltiradi.
final class TaskController
{
    public function __construct(private readonly TaskService $service)
    {
    }

    /**
     * GET /api/tasks
     *
     * @return array<string, mixed>
     */
    public function index(): array
    {
        return ['data' => array_map(static fn ($task) => $task->toArray(), $this->service->list())];
    }

    /**
     * GET /api/tasks/{id}
     *
     * @return array<string, mixed>
     */
    public function show(int $id): array
    {
        return ['data' => $this->service->get($id)->toArray()];
    }

    /**
     * POST /api/tasks
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function store(array $input): array
    {
        return ['data' => $this->service->create($input)->toArray()];
    }

    /**
     * PUT|PATCH /api/tasks/{id}
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function update(int $id, array $input): array
    {
        return ['data' => $this->service->update($id, $input)->toArray()];
    }

    /**
     * DELETE /api/tasks/{id}
     *
     * @return array<string, string>
     */
    public function destroy(int $id): array
    {
        $this->service->delete($id);

        return ['message' => 'Task o\'chirildi'];
    }
}
