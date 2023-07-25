<?php

namespace DKorona;

use mysql_xdevapi\SqlStatementResult;

/**
 * Class Logger
 * @package DKorona
 */
class Logger
{
	private string $logName;
	private bool $detail;

	/**
	 * Logger constructor.
	 *
	 * @param string $logName
	 * @param bool   $detail
	 */
	public function __construct(string $logName, bool $detail = false)
	{
		$this->logName = $logName;
		$this->detail = $detail;
	}

	/**
	 * @param string|null $level
	 *
	 * @return string
	 */
	private function getLogFile(string|null $level = null): string
	{
		return sprintf(__DIR__ . "/../logs/%s.log", $level ? ($this->logName . '-' . $level) : $this->logName);
	}

	/**
	 * Logs a message with the specified level to the log file.
	 *
	 * @param string $level The log level (e.g., warning, info, error, log)
	 * @param mixed $message The message to be logged
	 * @return void
	 */
	public function logMessage(string $level, mixed $message): void
	{
		if (is_array($message)) {
			$message = print_r($message, true);
		}
		$message = strval($message);

		$logEntry = $this->formatLogEntry($level, $message);

		/**
		 * Пишем в разные файлы, если нужно разделить логи ошибок, технические логи
		 */
		if ($this->detail) {
			file_put_contents($this->getLogFile($level), $logEntry, FILE_APPEND);
		}

		/**
		 * Пишем все логи в один файл, если не нужны детальные логи
		 */
		file_put_contents($this->getLogFile(), $logEntry, FILE_APPEND);
	}

	/**
	 * Formats the log entry with the specified level and message.
	 *
	 * @param string $level The log level
	 * @param string $message The log message
	 * @return string The formatted log entry
	 */
	private function formatLogEntry(string $level, string $message): string
	{
		$logEntry = "[";
		$logEntry .= date('Y-m-d H:i:s');
		$logEntry .= "] [{$level}] {$message}" . PHP_EOL;
		return $this->colorizeLogEntry($level, $logEntry);
	}

	/**
	 * Colorizes the log entry based on the log level.
	 *
	 * @param string $level The log level
	 * @param string $logEntry The log entry to colorize
	 * @return string The colorized log entry
	 */
	private function colorizeLogEntry(string $level, string $logEntry): string
	{
		$colors = [
			'warning' => "\033[1;33m%s\033[0m", // Yellow
			'log' => "\033[0m%s\033[0m", // Default
			'error' => "\033[1;31m%s\033[0m", // Red
			'info' => "\033[1;36m%s\033[0m", // Cyan
		];

		if (array_key_exists($level, $colors)) {
			return sprintf($colors[$level], $logEntry);
		}

		return $logEntry;
	}

	/**
	 * Logs a warning message to the log file.
	 *
	 * @param string $message The warning message
	 * @return void
	 */
	public function warning(mixed $message): void
	{
		$this->logMessage('warning', $message);
	}

	/**
	 * Logs an informational message to the log file.
	 *
	 * @param string $message The informational message
	 * @return void
	 */
	public function info(mixed $message): void
	{
		$this->logMessage('info', $message);
	}

	/**
	 * Logs an error message to the log file.
	 *
	 * @param string $message The error message
	 * @return void
	 */
	public function error(mixed $message): void
	{
		$this->logMessage('error', $message);
	}

	/**
	 * Logs a general message to the log file.
	 *
	 * @param string $message The log message
	 * @return void
	 */
	public function log(mixed $message): void
	{
		$this->logMessage('log', $message);
	}

	public static function logFile(mixed $message, string $logName, string $level): void
	{
		$logger = new self($logName, false);
		$logger->logMessage($level, $message);
	}
}
