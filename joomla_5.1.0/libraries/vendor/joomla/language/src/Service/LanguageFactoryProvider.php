<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Language\LanguageFactory;

/**
 * LanguageFactory object service provider
 *
 * @since  2.0.0-alpha
 */
class LanguageFactoryProvider implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   2.0.0-alpha
     * @throws  \RuntimeException
     */
    public function register(Container $container)
    {
        $container->share(
            'Joomla\\Language\\LanguageFactory',
            function (Container $container) {
                $factory = new LanguageFactory();

                /** @var \Joomla\Registry\Registry $config */
                $config = $container->get('config');

                $baseLangDir = $config->get('language.basedir');
                $defaultLang = $config->get('language.default', 'en-GB');

                if ($baseLangDir) {
                    $factory->setLanguageDirectory($baseLangDir);
                }

                $factory->setDefaultLanguage($defaultLang);

                return $factory;
            },
            true
        );
    }
}
