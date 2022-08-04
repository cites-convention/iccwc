<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\node\NodeInterface;

/**
 * Provides a 'ICCWC Social Media Share' Block.
 *
 * @Block(
 *   id = "iccwc_social_media_share",
 *   admin_label = @Translation("ICCWC Social Media Share Links"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SocialMediaShareBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return [];
    }

    $link = $node->toUrl()->setAbsolute()->toString();

    return [
      '#theme' => 'social_media_share',
      '#link' => $link,
    ];
  }

}
