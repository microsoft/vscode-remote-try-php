<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
interface TaggedContainerInterface extends ContainerInterface
{
 public function findTaggedServiceIds(string $name);
}
