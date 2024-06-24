<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\MessageCatalogueInterface;
interface TranslatorStrongTypeInterface
{
 public function getFromCatalogue(MessageCatalogueInterface $catalogue, string $id, string $domain = 'messages');
}
