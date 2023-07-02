<?php

namespace DKLittleSite;

class View {
	public static array $functions = [];

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

	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @return string
	 */
	protected static function parseVariables(string $template, array $data): string
	{
		return preg_replace_callback('/{{\s*([^}]+)\s*}}/', function ($matches) use ($data) {
			$variable = trim($matches[1]);

			if (str_contains($variable, '(') && str_contains($variable, ')')) {
				return self::evaluateFunction($variable, $data);
			}

			$value = $data[$variable] ?? '';

			if (is_string($value)) {
				return $value;
			}

			return var_export($value, true);
		}, $template);
	}

	protected static function evaluateFunction(string $function, array $data): string
	{
		$functionParts = explode('(', $function, 2);
		$functionName = trim($functionParts[0]);

		$arguments = array_map('trim', explode(',', rtrim($functionParts[1], ')')));

		foreach ($arguments as &$argument) {
			if (isset($data[$argument])) {
				$argument = $data[$argument];
			} elseif (str_starts_with($argument, "'") || str_starts_with($argument, '"')) {
				$argument = trim($argument, "'\"");
			}
		}

		if (isset(self::$functions[$functionName])) {
			return call_user_func_array(self::$functions[$functionName], $arguments);
		} elseif (function_exists($functionName)) {
			return call_user_func_array($functionName, $arguments);
		}

		return '';
	}

	/**
	 * @param string   $name
	 * @param callable $callback
	 *
	 * @return void
	 */
	public static function registerFunction(string $name, callable $callback): void
	{
		self::$functions[$name] = $callback;
	}

	protected static function parseComments($template): string
	{
		return preg_replace('/{!\s*([^}]+)\s*!}/', '', $template);
	}

	protected static function executePHP($template): string
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
