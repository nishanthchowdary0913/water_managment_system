<?php

class SqliteResult
{
    public int $num_rows = 0;
    private array $rows = [];
    private int $position = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($this->rows);
    }

    public function fetch_assoc()
    {
        if (!isset($this->rows[$this->position])) {
            return null;
        }

        return $this->rows[$this->position++];
    }

    public function fetch_all($mode = null)
    {
        return $this->rows;
    }
}

class SqliteStatement
{
    private PDO $pdo;
    private string $sql;
    private array $params = [];
    private ?PDOStatement $statement = null;
    private ?SqliteResult $result = null;
    private bool $executed = false;
    public int $num_rows = 0;

    public function __construct(PDO $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    public function bind_param(string $types, ...$values): void
    {
        $this->params = array_values($values);
    }

    public function execute(): bool
    {
        $sql = $this->sql;
        $names = [];
        $index = 0;

        while (strpos($sql, '?') !== false) {
            $name = ':p' . $index++;
            $names[] = $name;
            $pos = strpos($sql, '?');
            $sql = substr($sql, 0, $pos) . $name . substr($sql, $pos + 1);
        }

        try {
            $this->statement = $this->pdo->prepare($sql);
            foreach ($names as $i => $name) {
                $this->statement->bindValue($name, $this->params[$i] ?? null);
            }

            $this->executed = $this->statement->execute();
            $this->result = null;
            $this->num_rows = 0;
            return $this->executed;
        } catch (Throwable $error) {
            error_log("Database query failed: " . $error->getMessage());
            return false;
        }
    }

    public function get_result(): ?SqliteResult
    {
        if (!$this->executed || !$this->statement) {
            return null;
        }

        if (!str_starts_with(strtolower(ltrim($this->sql)), 'select')) {
            return null;
        }

        $rows = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        $this->result = new SqliteResult($rows);
        $this->num_rows = $this->result->num_rows;
        return $this->result;
    }

    public function store_result(): void
    {
        $result = $this->get_result();
        $this->num_rows = $result ? $result->num_rows : 0;
    }

    public function close(): void
    {
        $this->statement = null;
    }
}

class SqliteConnection
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function prepare(string $sql): SqliteStatement
    {
        return new SqliteStatement($this->pdo, $sql);
    }

    public function query(string $sql): ?SqliteResult
    {
        try {
            $statement = $this->pdo->query($sql);
            if (!$statement) {
                return null;
            }

            if (str_starts_with(strtolower(ltrim($sql)), 'select')) {
                return new SqliteResult($statement->fetchAll(PDO::FETCH_ASSOC));
            }

            return new SqliteResult([]);
        } catch (Throwable $error) {
            error_log("Database query failed: " . $error->getMessage());
            return null;
        }
    }

    public function set_charset(string $charset): void
    {
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}

$databasePath = getenv("DB_PATH") ?: __DIR__ . "/data/water_management.sqlite3";
$directory = dirname($databasePath);

if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
    http_response_code(500);
    die("Unable to initialize local storage.");
}

try {
    $pdo = new PDO("sqlite:" . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS residents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            phone TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS water_facilities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            facility_name TEXT NOT NULL,
            facility_type TEXT NOT NULL,
            location TEXT NOT NULL,
            status TEXT NOT NULL,
            description TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS water_availability (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            area TEXT NOT NULL,
            supply_status TEXT NOT NULL,
            supply_time TEXT NOT NULL,
            remarks TEXT,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS complaints (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            resident_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            area TEXT NOT NULL,
            complaint_type TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Pending',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $adminExists = $pdo->query("SELECT COUNT(*) AS total FROM admin")->fetchColumn();
    $adminUsername = getenv("ADMIN_USERNAME");
    $adminPassword = getenv("ADMIN_PASSWORD");
    if (!$adminExists && $adminUsername && $adminPassword) {
        $adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        $statement = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $statement->execute([$adminUsername, $adminHash]);
    }
} catch (Throwable $error) {
    error_log("Database initialization failed: " . $error->getMessage());
    http_response_code(500);
    die("Unable to initialize local storage.");
}

$conn = new SqliteConnection($pdo);