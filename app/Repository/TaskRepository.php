<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use PDO;
use RuntimeException;

// Repository: Task ma'lumotlarini SQLite'dan o'qish va yozish qatlami.
final class TaskRepository
{
    public function __construct(private readonly PDO $database)
    {
    }

    /** @return list<Task> */
    public function findAll(): array
    {
        $rows = $this->database->query('SELECT * FROM tasks ORDER BY id DESC')->fetchAll();

        return array_map(Task::fromRow(...), $rows);
    }

    public function findById(int $id): ?Task
    {
        $statement = $this->database->prepare('SELECT * FROM tasks WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Task::fromRow($row);
    }

    public function create(string $title, ?string $description): Task
    {
        $statement = $this->database->prepare(
            'INSERT INTO tasks (title, description) VALUES (:title, :description)'
        );
        $statement->execute(['title' => $title, 'description' => $description]);

        return $this->findById((int) $this->database->lastInsertId())
            ?? throw new RuntimeException('Yaratilgan task topilmadi');
    }

    public function update(int $id, string $title, ?string $description, bool $isDone): Task
    {
        $statement = $this->database->prepare(
            'UPDATE tasks
             SET title = :title, description = :description, is_done = :is_done, updated_at = datetime("now")
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'is_done' => (int) $isDone,
        ]);

        return $this->findById($id)
            ?? throw new RuntimeException('Yangilangan task topilmadi');
    }

    public function delete(int $id): void
    {
        $statement = $this->database->prepare('DELETE FROM tasks WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
