<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ICCWC banner' Block.
 *
 * @Block(
 *   id = "iccwc_banner",
 *   admin_label = @Translation("ICCWC banner block"),
 *   category = @Translation("ICCWC"),
 * )
 */
class ICCWCBannerBlock  extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');

    $sum = $node->get('body')->summary;
    $title = $node->get('title')->value;
    $breadcrumb = \Drupal::service('breadcrumb')->build(\Drupal::routeMatch())->toRenderable();

    if ($node->get('field_image')->entity) {
      /** @var Drupal\media\MediaInterface $media */
      $media = $node->get('field_image')->entity;
      $caption = $media->get('field_caption')->value;
      /** @var Drupal\file\FileInterface $file */
      $file = $media->get('field_media_image')->entity;
      $image = $file->createFileUrl();
    }

    return [
      '#theme' => 'banner_block',
      '#summary' => $sum ?? '',
      '#title' => $title ?? '',
      '#breadcrumb' => $breadcrumb ?? '',
      '#caption' => $caption ?? '',
      '#image' => $image ?? '',
    ];
  }

}
