<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ICCWC Socia Media Links' Block.
 *
 * @Block(
 *   id = "iccwc_social_media",
 *   admin_label = @Translation("ICCWC Socia Media Links"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SocialMediaLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $twitter_link = \Drupal::config('iccwc_social_media.settings')->get('twitter_link');
    $youtube_link = \Drupal::config('iccwc_social_media.settings')->get('youtube_link');
    $linkedin_link = \Drupal::config('iccwc_social_media.settings')->get('linkedin_link');

    return [
      '#theme' => "social_media_links",
      '#twitter_link' => $twitter_link,
      '#youtube_link' => $youtube_link,
      '#linkedin_link' => $linkedin_link,
    ];
  }
}
