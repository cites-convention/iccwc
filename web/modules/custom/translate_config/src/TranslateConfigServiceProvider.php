<?php

namespace Drupal\translate_config;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Service used to override the locale.config_manager service.
 */
class TranslateConfigServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('locale.config_manager');
    $definition->setClass('Drupal\translate_config\Service\TranslateConfigLocaleConfigManager');
  }

}
