<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ICCWC more links' Block.
 *
 * @Block(
 *   id = "iccwc_more_links",
 *   admin_label = @Translation("ICCWC more links"),
 *   category = @Translation("ICCWC"),
 * )
 */
class MoreLinksBlock  extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    $links = [];
    foreach ($node->get('field_links') as $item) {
      $links[$item->uri] = $item->title;
    }

    return [
      '#theme' => 'more_links',
      '#links' => $links
    ];
  }

}
