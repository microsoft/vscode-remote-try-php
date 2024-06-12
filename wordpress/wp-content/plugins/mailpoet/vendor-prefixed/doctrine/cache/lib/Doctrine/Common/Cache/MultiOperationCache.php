<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface MultiOperationCache extends MultiGetCache, MultiDeleteCache, MultiPutCache
{
}
