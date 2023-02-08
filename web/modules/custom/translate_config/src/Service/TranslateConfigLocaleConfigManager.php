<?php

namespace Drupal\translate_config\Service;

use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Drupal\locale\LocaleConfigManager;

/**
 * Override the LocaleConfigManager service.
 *
 * The LocaleConfigManager service is overridden so that when translation
 * are created in the interface translation screen, ANY configuration
 * with the same label gets translated instead of just shipped ones.
 */
class TranslateConfigLocaleConfigManager extends LocaleConfigManager {

  /**
   * {@inheritDoc}
   */
  public function getComponentNames(array $components = []) {
    $components = array_filter($components);
    if ($components) {
      $names = [];
      foreach ($components as $type => $list) {
        // InstallStorage::getComponentNames returns a list of folders keyed by
        // config name.
        $names = array_merge($names, $this->defaultConfigStorage->getComponentNames($type, $list));
      }
      return $names;
    }
    else {
      return $this->configStorage->listAll();
    }
  }

  /**
   * {@inheritDoc}
   */
  public function isSupported($name) {
    $data = $this->configStorage->read($name);
    return !empty($data) && !empty($data['langcode']) && $data['langcode'] == 'en';
  }

  /**
   * {@inheritDoc}
   */
  public function getTranslatableDefaultConfig($name) {
    if ($this->isSupported($name)) {
      // Create typed configuration wrapper based on install storage data.
      $data = $this->configStorage->read($name);
      $typed_config = $this->typedConfigManager->createFromNameAndData($name, $data);
      if ($typed_config instanceof TraversableTypedDataInterface) {
        return $this->getTranslatableData($typed_config);
      }
    }
    return [];
  }

}
