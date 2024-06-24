<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Persisters\PersisterException as BasePersisterException;
class PersisterException extends BasePersisterException
{
}
