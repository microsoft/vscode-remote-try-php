<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerExceptionInterface;
interface ExceptionInterface extends ContainerExceptionInterface, \Throwable
{
}
