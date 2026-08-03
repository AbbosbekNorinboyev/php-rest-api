<?php

class TaskController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** GET /api/tasks — barcha tasklarni qaytaradi */
    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM tasks ORDER BY id DESC');
        $tasks = $stmt->fetchAll();

        Response::json(['data' => $tasks]);
    }

    /** GET /api/tasks/{id} — bitta taskni qaytaradi */
    public function show(int $id): void
    {
        $task = $this->findOrFail($id);
        Response::json(['data' => $task]);
    }

    /** POST /api/tasks — yangi task yaratadi */
    public function store(array $input): void
    {
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');

        if ($title === '') {
            Response::json(['error' => 'title maydoni majburiy'], 422);
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO tasks (title, description) VALUES (:title, :description)'
        );
        $stmt->execute([
            'title' => $title,
            'description' => $description,
        ]);

        $id = (int) $this->db->lastInsertId();
        $task = $this->findOrFail($id);

        Response::json(['data' => $task], 201);
    }

    /** PUT/PATCH /api/tasks/{id} — taskni yangilaydi */
    public function update(int $id, array $input): void
    {
        $task = $this->findOrFail($id);

        $title = array_key_exists('title', $input) ? trim($input['title']) : $task['title'];
        $description = array_key_exists('description', $input) ? trim($input['description']) : $task['description'];
        $isDone = array_key_exists('is_done', $input) ? (int) (bool) $input['is_done'] : $task['is_done'];

        if ($title === '') {
            Response::json(['error' => 'title bo\'sh bo\'lishi mumkin emas'], 422);
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE tasks SET title = :title, description = :description, is_done = :is_done, updated_at = datetime("now") WHERE id = :id'
        );
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'is_done' => $isDone,
            'id' => $id,
        ]);

        Response::json(['data' => $this->findOrFail($id)]);
    }

    /** DELETE /api/tasks/{id} — taskni o'chiradi */
    public function destroy(int $id): void
    {
        $this->findOrFail($id);

        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);

        Response::json(['message' => 'Task o\'chirildi'], 200);
    }

    private function findOrFail(int $id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();

        if (!$task) {
            Response::json(['error' => "Task topilmadi (id: $id)"], 404);
            exit;
        }

        return $task;
    }
}
