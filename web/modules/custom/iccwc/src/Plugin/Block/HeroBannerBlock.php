<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Hero banner' Block.
 *
 * @Block(
 *   id = "iccwc_hero_banner",
 *   admin_label = @Translation("Hero banner"),
 *   category = @Translation("ICCWC"),
 * )
 */
class HeroBannerBlock extends ICCWCBlockBase {

  /**
   * The breadcrumb manager.
   *
   * @var \Drupal\Core\Breadcrumb\BreadcrumbManager
   */
  protected $breadcrumb;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->breadcrumb = $container->get('breadcrumb');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $image = NULL;
    $caption = '';
    $tag = NULL;

    $breadcrumb = $this->breadcrumb->build($this->routeMatch)->toRenderable();

    $node = $this->routeMatch->getParameter('node');

    if (!$node instanceof NodeInterface) {
      return [
        '#theme' => 'banner_block',
        '#title' => $this->t('Title'),
        '#summary' => $this->t('Subtitle'),
        '#breadcrumb' => $breadcrumb,
        '#caption' => $this->t('Image caption'),
        '#image' => NULL,
      ];
    }

    $summary = $node->get('body')->summary;
    $title = $node->get('title')->value;


    if ($node->hasField('field_banner_image')) {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $node->get('field_banner_image')->entity;
      if ($media instanceof MediaInterface) {
        $image = $node->get('field_banner_image')->view([
          'type' => 'media_responsive_thumbnail',
          'label' => 'hidden',
          'settings' => [
            'responsive_image_style' => 'hero_banner',
          ],
        ]);
        $caption = $media->get('field_caption')->value;
        /** @var \Drupal\file\FileInterface $file */
      }
    }

    if ($node->bundle() == 'news' && !$node->get('field_tags')->isEmpty()) {
      /** @var \Drupal\taxonomy\TermInterface $tag_term */
      $tag_term = $node->get('field_tags')->entity;
      $tag = $tag_term->label();
    }

    return [
      '#theme' => 'banner_block',
      '#summary' => $summary,
      '#title' => $title,
      '#breadcrumb' => $breadcrumb,
      '#caption' => $caption,
      '#image' => $image,
      '#date' => $this->dateFormatter->format($node->getCreatedTime(), 'd_f_y'),
      '#tag' => $tag,
      '#bundle' => $node->bundle(),
    ];
  }

}
