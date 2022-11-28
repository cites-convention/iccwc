<?php

namespace Drupal\iccwc\Asset;

use Drupal\critical_css\Asset\CssCollectionRenderer;

class IccwcCssCollectionRenderer extends CssCollectionRenderer {

  /**
   * {@inheritdoc}
   */
  protected function getCriticalCssAsset() {
    $this->criticalCssProvider->getCriticalCss();
    if (empty($this->criticalCssProvider->getMatchedFilePath())) {
      return [];
    }

    return [
      '#type' => 'html_tag',
      '#tag' => 'link',
      '#attributes' => [
        'rel' => 'stylesheet',
        'href' => $this->criticalCssProvider->getMatchedFilePath(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(array $css_assets) {
    // Fixes https://www.drupal.org/project/critical_css/issues/2982651
    if (empty($css_assets)) {
      return [];
    }

    $css_assets = $this->cssCollectionRenderer->render($css_assets);
    $config = $this->configFactory->get('critical_css.settings');
    if (!$this->criticalCssProvider->isEnabled() || $this->criticalCssProvider->isAlreadyProcessed()) {
      return $css_assets;
    }

    $new_css_assets = [];
    // Add critical CSS asset. It will be added if a critical CSS file is found
    // or if Twig debug is enabled.
    $criticalCssAsset = $this->getCriticalCssAsset();
    if ($criticalCssAsset) {
      $new_css_assets[] = $criticalCssAsset;
    }

    // If a critical CSS is found, make other CSS files asynchronous.
    if (!empty($this->criticalCssProvider->getMatchedFilePath())) {
      $asyncAssets = $this->makeAssetsAsync($css_assets);
      $new_css_assets = array_merge($new_css_assets, $asyncAssets);
    }
    else {
      $new_css_assets = array_merge($new_css_assets, $css_assets);
    }

    return $new_css_assets;
  }

}
