<?php

namespace DKorona\Abstract;

use PDO;

abstract class AbstractMigration {

	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	protected function query($string): bool|\PDOStatement
	{
		return $this->db->query($string);
	}
}
