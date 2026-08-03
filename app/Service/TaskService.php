<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Exception\HttpException;
use App\Repository\TaskRepository;

// Service: Task biznes qoidalari va request ma'lumotlari validatsiyasi.
final class TaskService
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    /** @return list<Task> */
    public function list(): array
    {
        return $this->repository->findAll();
    }

    public function get(int $id): Task
    {
        return $this->findOrFail($id);
    }

    /** @param array<string, mixed> $input */
    public function create(array $input): Task
    {
        $title = $this->requiredTitle($input);
        $description = $this->description($input, null);

        return $this->repository->create($title, $description);
    }

    /** @param array<string, mixed> $input */
    public function update(int $id, array $input): Task
    {
        $task = $this->findOrFail($id);
        $title = array_key_exists('title', $input) ? $this->requiredTitle($input) : $task->title;
        $description = $this->description($input, $task->description);
        $isDone = $this->isDone($input, $task->isDone);

        return $this->repository->update($id, $title, $description, $isDone);
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id);
        $this->repository->delete($id);
    }

    private function findOrFail(int $id): Task
    {
        return $this->repository->findById($id)
            ?? throw new HttpException("Task topilmadi (id: $id)", 404);
    }

    /** @param array<string, mixed> $input */
    private function requiredTitle(array $input): string
    {
        if (!isset($input['title']) || !is_string($input['title'])) {
            throw new HttpException('title maydoni matn va majburiy', 422);
        }

        $title = trim($input['title']);
        if ($title === '') {
            throw new HttpException('title bo\'sh bo\'lishi mumkin emas', 422);
        }

        return $title;
    }

    /** @param array<string, mixed> $input */
    private function description(array $input, ?string $current): ?string
    {
        if (!array_key_exists('description', $input)) {
            return $current;
        }

        if ($input['description'] !== null && !is_string($input['description'])) {
            throw new HttpException('description matn yoki null bo\'lishi kerak', 422);
        }

        return $input['description'] === null ? null : trim($input['description']);
    }

    /** @param array<string, mixed> $input */
    private function isDone(array $input, bool $current): bool
    {
        if (!array_key_exists('is_done', $input)) {
            return $current;
        }

        if (is_bool($input['is_done'])) {
            return $input['is_done'];
        }

        if (in_array($input['is_done'], [0, 1, '0', '1'], true)) {
            return (bool) $input['is_done'];
        }

        throw new HttpException('is_done boolean bo\'lishi kerak', 422);
    }
}
