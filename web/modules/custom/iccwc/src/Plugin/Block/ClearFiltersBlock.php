<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Url;

/**
 * Provides a 'ICCWC Clear Filter' Block.
 *
 * @Block(
 *   id = "iccwc_clear_filter",
 *   admin_label = @Translation("ICCWC Clear filter"),
 *   category = @Translation("ICCWC"),
 * )
 */
class ClearFiltersBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'link',
      '#title' => $this->t('Clear filters'),
      '#url' => Url::fromRoute('<current>'),
    ];
  }

}
