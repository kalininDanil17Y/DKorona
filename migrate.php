<?php
require_once 'my_autoload.php';

use DKorona\DB;

/**
 * Путь к директории с миграциями
 */
$migrationsPath = "./Migrations";
$db = DB::getInstance()->getPDO();

/**
 * Создание таблицы для хранения выполненных миграций, если она не существует
 */
$db->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        name VARCHAR(255) PRIMARY KEY,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

/**
 * Команда создания новой миграции
 */
if ($argv[1] == "create") {
	/**
	 * Генерация имени миграции из текущей даты и времени, если имя не указано
	 */
	$migrationName = ($argv[2] ?? "Migration") . '_' . date("YmdHis");

	/**
	 * Создание файла миграции
	 */
	$migrationFilename = "{$migrationsPath}/{$migrationName}.php";
	file_put_contents($migrationFilename, "<?php\n\nclass {$migrationName} extends \\DKorona\\Abstract\\AbstractMigration\n{\n    public function up()\n    {\n        // Write your SQL queries here using \$this->query('')\n    }\n\n    public function down()\n    {\n        // Write your SQL queries here using \$this->query()\n    }\n}\n");

	echo "Migration '{$migrationName}' has been created.\n";
}

/**
 * Команда запуска всех миграций
 */
elseif ($argv[1] == "migrate") {
	/**
	 * Получение списка всех миграций
	 */
	$migrations = scandir($migrationsPath);
	sort($migrations);

	/**
	 * Получение списка выполненных миграций
	 */
	$executedMigrations = [];
	$query = $db->query("SELECT name FROM migrations");
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$executedMigrations[] = $row["name"];
	}

	/**
	 * Запуск каждой миграции, если она ещё не была выполнена
	 */
	foreach ($migrations as $migration) {
		if ($migration == "." || $migration == "..") {
			continue;
		}

		$migrationFilename = "{$migrationsPath}/{$migration}";

		/**
		 * Проверка, была ли уже выполнена эта миграция
		 */
		if (in_array($migration, $executedMigrations)) {
			echo "Migration '{$migration}' has already been executed.\n";
			continue;
		}

		/**
		 * Подключение класса миграции
		 */
		require_once($migrationFilename);
		$migrationClassName = pathinfo($migrationFilename, PATHINFO_FILENAME);
		$migrationInstance = new $migrationClassName($db);

		/**
		 * Выполнение метода up() миграции
		 */
		$migrationInstance->up();

		/**
		 * Запись названия выполненной миграции в базу данных
		 */
		$stmt = $db->prepare("INSERT INTO migrations (name) VALUES (?)");
		$stmt->execute([$migration]);

		echo "Migration '{$migration}' has been executed.\n";
	}

	echo "All migrations have been executed.\n";
}

/**
 * Команда удаления всех миграций
 */
elseif ($argv[1] == "reset") {
	/**
	 * Получение списка всех миграций
	 */
	$migrations = scandir($migrationsPath);
	sort($migrations);

	/**
	 * Удаление каждой миграции по очереди
	 */
	foreach ($migrations as $migration) {
		if ($migration == "." || $migration == "..") {
			continue;
		}

		$migrationFilename = "{$migrationsPath}/{$migration}";

		/**
		 * Подключение класса миграции
		 */
		require_once($migrationFilename);
		$migrationClassName = pathinfo($migrationFilename, PATHINFO_FILENAME);
		$migrationInstance = new $migrationClassName($db);

		/**
		 * Выполнение метода down() миграции
		 */
		$migrationInstance->down();

		/**
		 * Удаление файла миграции
		 */
		if ($argv[2] === 'hard') {
			unlink($migrationFilename);
		}

		/**
		 * Удаление записи о выполненной миграции из базы данных
		 */
		$stmt = $db->prepare("DELETE FROM migrations WHERE name = ?");
		$stmt->execute([$migration]);

		echo "Migration '{$migration}' has been deleted.\n";
	}

	echo "All migrations have been reset.\n";
} else {
	echo "Invalid command. Available commands: create, migrate, reset.\n";
}
