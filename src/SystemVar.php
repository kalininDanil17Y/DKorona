<?php

namespace DKLittleSite;

/**
 * Class SystemVar
 * @package DKLittleSite
 */
class SystemVar
{
	private array $var = [];

	public function set(string $name, $value): static
	{
		$this->var[$name] = $value;
		return $this;
	}

	public function get(string $name, string|null $type = null): mixed
	{
		$value = null;

		if (array_key_exists($name, $this->var)) {
			$value = $this->var[$name];
		}

		if ($type == 'string') {
			$value = strval($value);
		} elseif (array_key_exists($type, ['int', 'number'])) {
			$value = intval($value);
		} elseif ($type == 'bool') {
			$value = boolval($value);
		}

		return $value;
	}

	public function load(string $confName, bool $is_prefix = false): void
	{
		$configFilePath = sprintf('config/%s.php', $confName);
		if (!file_exists($configFilePath)) {
			return;
		}

		$configData = include $configFilePath;
		if (is_array($configData)) {
			foreach ($configData as $key => $value) {
				if ($is_prefix) {
					$key = $confName . $key;
				}
				$this->var[$key] = $value;
			}
		}
	}
}
