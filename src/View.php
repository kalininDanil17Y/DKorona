<?php

namespace DKLittleSite;

class View {
	public static function render(string $template, array $data = [], $isReturn = true): bool|string
	{
		if (!self::check($template)) {
			return false;
		}

		$template = self::getPath($template);

		ob_start();
		include $template;
		$output = ob_get_clean();
		$output = self::parseVariables($output, $data);
		$output = self::parseComments($output);
		$output = self::executePHP($output);

		if ($isReturn) {
			return $output;
		}

		echo $output;
		return true;
	}

	protected static function parseVariables(string $template, array $data): array|string|null
	{
		return preg_replace_callback('/{{\s*([^}]+)\s*}}/', function($matches) use ($data) {
			$variable = trim($matches[1]);
			return $data[$variable] ?? '';
		}, $template);
	}

	protected static function parseComments($template): array|string|null
	{
		return preg_replace('/{!\s*([^}]+)\s*!}/', '', $template);
	}

	protected static function executePHP($template): array|string|null
	{
		return (preg_replace_callback('/{%\s*([^%]+)\s*%}/', function($matches) {
			$phpCode = trim($matches[1]);
			ob_start();
			eval($phpCode);
			return ob_get_clean();
		}, $template));
	}

	public static function check(string $template): bool
	{
		return file_exists(self::getPath($template));
	}

	public static function getPath(string $template): string
	{
		return sprintf(__DIR__ . '/../src/templates/%s.php', $template);
	}
}
