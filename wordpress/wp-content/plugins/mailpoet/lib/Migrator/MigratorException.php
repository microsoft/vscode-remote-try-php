<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


use MailPoet\InvalidStateException;
use Throwable;

class MigratorException extends InvalidStateException {
  public static function templateFileReadFailed(string $path): self {
    return self::create()->withMessage(
      sprintf('Could not read migration template file "%s".', $path)
    );
  }

  public static function invalidMigrationLevel(string $level): self {
    return self::create()->withMessage(
      sprintf('Migration level "%s" is not supported! Use "app" or "db".', $level)
    );
  }

  public static function duplicateMigrationNames(array $names): self {
    return self::create()->withMessage(
      sprintf('Duplicate migration names are not allowed. Duplicate names found: "%s".', join(', ', $names))
    );
  }

  public static function migrationFileWriteFailed(string $path): self {
    return self::create()->withMessage(
      sprintf('Could not write migration file "%s".', $path)
    );
  }

  public static function migrationClassNotFound(string $className): self {
    return self::create()->withMessage(
      sprintf('Migration class "%s" not found.', $className)
    );
  }

  public static function migrationClassIsNotASubclassOf(string $className, string $parentClassName): self {
    return self::create()->withMessage(
      sprintf('Migration class "%1$s" is not a subclass of "%2$s".', $className, $parentClassName)
    );
  }

  public static function migrationFailed(string $className, Throwable $previous): self {
    return self::create($previous)->withMessage(
      sprintf('Migration "%1$s" failed. Details: %2$s', $className, $previous->getMessage())
    );
  }
}
