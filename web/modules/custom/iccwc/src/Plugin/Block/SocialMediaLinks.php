<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "iccwc_social_media",
 *   admin_label = @Translation("ICCWC Socia Media Links"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SocialMediaLinks extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var ModuleHandlerInterface $module_handler */
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('iccwc')->getPath();

    $twitter_link = \Drupal::config('iccwc_social_media.settings')->get('twitter_link');
    $youtube_link = \Drupal::config('iccwc_social_media.settings')->get('youtube_link');
    $linkedin_link = \Drupal::config('iccwc_social_media.settings')->get('linkedin_link');


    $twitter_img = [
      '#theme' => 'social_media_item',
      '#link' => $twitter_link,
      '#icon' => $module_path.'/social-media-link-images/twitter.svg'
    ];
    $youtube_img = [
      '#theme' => 'social_media_item',
      '#link' => $youtube_link,
      '#icon' => $module_path.'/social-media-link-images/youtube.svg'
    ];
    $linkedin_img = [
      '#theme' => 'social_media_item',
      '#link' => $linkedin_link,
      '#icon' => $module_path.'/social-media-link-images/linkedIn.svg'
    ];
    return [
      '#theme' => "social_media_links",
      '#twitter' => $twitter_img,
      '#youtube' => $youtube_img,
      '#linkedin' => $linkedin_img,
    ];
  }

}
