<?php
namespace App\Models;

use PDO;

class News {
    public function __construct(private PDO $pdo) {}

    public function all(): array {
        return $this->pdo->query("SELECT * FROM news ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create(string $title, string $description): void {
        $stmt = $this->pdo->prepare("INSERT INTO news (title, description) VALUES (:t,:d)");
        $stmt->execute([':t'=>$title, ':d'=>$description]);
    }
    public function update(int $id, string $title, string $description): void {
        $stmt = $this->pdo->prepare("UPDATE news SET title=:t, description=:d WHERE id=:id");
        $stmt->execute([':t'=>$title, ':d'=>$description, ':id'=>$id]);
    }
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM news WHERE id=:id");
        $stmt->execute([':id'=>$id]);
    }
}
