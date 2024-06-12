<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
class EnvParameterException extends InvalidArgumentException
{
 public function __construct(array $envs, \Throwable $previous = null, string $message = 'Incompatible use of dynamic environment variables "%s" found in parameters.')
 {
 parent::__construct(\sprintf($message, \implode('", "', $envs)), 0, $previous);
 }
}
