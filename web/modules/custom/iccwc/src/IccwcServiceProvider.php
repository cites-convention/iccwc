<?php

namespace Drupal\iccwc;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the language manager service.
 */
class IccwcServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides language_manager class to test domain language negotiation.
    // Adds entity_type.manager service as an additional argument.

    // Note: it's safest to use hasDefinition() first, because getDefinition() will
    // throw an exception if the given service doesn't exist.
    if ($container->hasDefinition('asset.css.collection_renderer.critical_css')) {
      $definition = $container->getDefinition('asset.css.collection_renderer.critical_css');
      $definition->setClass('Drupal\iccwc\Asset\IccwcCssCollectionRenderer');
    }
  }
}
