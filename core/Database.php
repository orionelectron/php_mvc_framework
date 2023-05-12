<?php



namespace orion\core;

use PDO;
use PDOException;

class Database
{
    public ?PDO $pdo = null;
    private string $APP_ROOT;
    public function __construct(array $config)
    {
        try {
            $configuration = $config["db"];
            $dsn = $configuration['dsn'] ?? '';
            $user = $configuration['user'] ?? '';
            $password = $configuration['password'] ?? '';
            $this->APP_ROOT = $config["APP_ROOT"];
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $newMigrations = [];

        $files = scandir(__DIR__ . '/../migrations');
        // print_r($files);
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }
            require_once __DIR__ . '/../migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration" . PHP_EOL);
            if ($instance->up() !== "" && $instance->up() !== null) {
                $this->pdo->exec($instance->up());
            }

            $this->log("Applied migration $migration" . PHP_EOL);
            $newMigrations[] = $migration;
        }
        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied!!");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveMigrations($migrations)
    {
        $migrations = array_map(fn ($m) => "('$m')", $migrations);
        $str = implode(",", $migrations);
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message;
    }
}
