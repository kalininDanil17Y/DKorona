<?php

namespace DKLittleSite\Abstract;

use DKLittleSite\DB;
use DKLittleSite\Logger;

/**
 * Базовый класс модели
 */
abstract class AbstractModel {
	/**
	 * Имя таблицы в базе данных
	 * @var string
	 */
	protected string $table;

	/**
	 * Данные модели
	 * @var array|mixed
	 */
	protected array $data;

	/**
	 * Конструктор модели
	 *
	 * @param array $data
	 */
	public function __construct(array $data = []) {
		$this->data = $data;
	}

	/**
	 * Получить все записи из таблицы
	 * @return array
	 */
	public static function all(): array
	{
		$db = DB::getInstance();
		$table = (new static)->table;
		$sql = "SELECT * FROM $table";
		$result = $db->query($sql);
		$models = [];
		foreach ($result as $row) {
			$models[] = new static($row);
		}
		return $models;
	}

	/**
	 * Получить одну запись по ID
	 *
	 * @param $id
	 * @param bool $safeMode
	 *
	 * @return static|null
	 */
	public static function find($id, bool $safeMode = false): ?static
	{
		$db = DB::getInstance();
		$table = (new static)->table;
		$sql = "SELECT * FROM $table WHERE id = ?";
		$params = [$id];
		$result = $db->query($sql, $params);
		if (count($result) > 0) {
			return new static($result[0]);
		} else {
			if ($safeMode)
				return new static([]);
			return null;
		}
	}

	public function load(): bool
	{
		$db = DB::getInstance();
		$table = (new static)->table;
		$sql = "SELECT * FROM $table WHERE id = ?";
		$params = [$this->data['id']];
		$result = $db->query($sql, $params);
		if (count($result) > 0) {
			$this->data = $result[0];
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Сохранить данные модели в базе данных (новая запись или обновление существующей)
	 * @return void
	 */
	public function save(): void
	{
		$db = DB::getInstance();
		$table = $this->table;
		if (isset($this->data['id'])) {
			// Обновление записи
			$id = $this->data['id'];
			unset($this->data['id']);
			$updates = [];
			$params = [];
			foreach ($this->data as $key => $value) {
				$updates[] = "$key = ?";
				$params[] = $value;
			}
			$params[] = $id;
			$updates = implode(', ', $updates);
			$sql = "UPDATE $table SET $updates WHERE id = ?";
			$db->execute($sql, $params);
			$this->data['id'] = $id;
		} else {
			// Создание новой записи
			$keys = array_keys($this->data);
			$values = array_values($this->data);
			$keys = implode(', ', $keys);
			$params = array_fill(0, count($values), '?');
			$params = implode(', ', $params);
			$sql = "INSERT INTO $table ($keys) VALUES ($params)";
			$db->execute($sql, $values);
			$this->data['id'] = $db->lastInsertId();
		}
	}

	/**
	 * Удалить запись из базы данных по ID
	 * @return void
	 */
	public function delete(): void
	{
		$db = DB::getInstance();
		$table = $this->table;
		if (isset($this->data['id'])) {
			$id = $this->data['id'];
			$sql = "DELETE FROM $table WHERE id = ?";
			$params = [$id];
			$db->execute($sql, $params);
			unset($this->data['id']);
		}
	}

	/**
	 * Выполнить произвольный SQL-запрос
	 *
	 * @param $sql
	 * @param array $params
	 *
	 * @return array
	 */
	public static function query($sql, array $params = []): array
	{
		$db = DB::getInstance();
		$result = $db->query($sql, $params);
		$models = [];
		foreach ($result as $row) {
			$models[] = new static($row);
		}
		return $models;
	}

	/**
	 * Получить данные модели
	 * @return array|mixed
	 */
	public function getData(): mixed
	{
		return $this->data;
	}

	/**
	 * Установить данные модели
	 * @param $data
	 *
	 * @return void
	 */
	public function setData($data): void
	{
		$this->data = $data;
	}

	/**
	 * Получить значение одного поля модели
	 * @param $name
	 *
	 * @return mixed|void
	 */
	public function __get($name) {
		if (isset($this->data[$name])) {
			return $this->data[$name];
		}
	}

	/**
	 * Установить значение одного поля модели
	 * @param $name
	 * @param $value
	 *
	 * @return void
	 */
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
}
