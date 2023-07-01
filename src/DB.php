<?php

namespace DKLittleSite;

use PDO;

class DB {
	private static ?DB $instance = null;
	private PDO $pdo;
	private SystemVar $var;

	private function __construct() {
		$this->var = new SystemVar();
		$this->var->load('mysql');

		$dsn = sprintf(
			'mysql:host=%s;dbname=%s;charset=utf8',
			$this->var->get('mysqlHost'),
			$this->var->get('mysqlDatabase'),
		);
		$username = $this->var->get('mysqlUsername');
		$password = $this->var->get('mysqlPassword');

		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];
		$this->pdo = new PDO($dsn, $username, $password, $options);
	}

	public static function getInstance(): ?DB
	{
		if (self::$instance === null) {
			self::$instance = new DB();
		}
		return self::$instance;
	}

	public function query($sql, $params = []): bool|array
	{
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	public function lastInsertId(): bool|string
	{
		return $this->pdo->lastInsertId();
	}

	// Метод для параметризованного выполнения SQL-запросов
	public function execute($sql, $params = []): bool
	{
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value) {
			$stmt->bindValue($key + 1, $value);
		}
		return $stmt->execute();
	}

	public function getPDO(): PDO
	{
		return $this->pdo;
	}
}

