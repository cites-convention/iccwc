<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\system\Plugin\Block\SystemMenuBlock;

/**
 * Provides a 'Sidebar menu' Block.
 *
 * @Block(
 *   id = "sidebar_menu",
 *   admin_label = @Translation("Sidebar menu"),
 *   category = @Translation("ICCWC"),
 *   deriver = "Drupal\system\Plugin\Derivative\SystemMenuBlock",
 *   forms = {
 *     "settings_tray" = "\Drupal\system\Form\SystemMenuOffCanvasForm",
 *   },
 * )
 */
class SidebarMenuBlock extends SystemMenuBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    if (empty($build)) {
      return [];
    }

    $build['#theme'] = 'menu__main__sidebar';
    return $build;
  }

}
