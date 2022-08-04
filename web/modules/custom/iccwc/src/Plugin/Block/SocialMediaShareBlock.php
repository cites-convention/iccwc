<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    if ($node instanceof NodeInterface){
      $link = $node->toUrl()->setAbsolute()->toString();
    }

    return [
      '#theme' => 'social_media_share',
      '#link' => $link,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [
      'config:iccwc.social_media.settings',
    ]);
  }

}
