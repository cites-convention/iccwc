<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Breadcrumb\BreadcrumbManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\file\FileInterface;
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
class HeroBannerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The breadcrumb manager.
   *
   * @var \Drupal\Core\Breadcrumb\BreadcrumbManager
   */
  protected $breadcrumb;

  /**
   * HeroBannerBlock constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The current route match.
   * @param \Drupal\Core\Breadcrumb\BreadcrumbManager $breadcrumb
   *   The breadcrumb manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route_match, BreadcrumbManager $breadcrumb) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
    $this->breadcrumb = $breadcrumb;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('breadcrumb')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $image = '';
    $caption = '';

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

    /** @var \Drupal\media\MediaInterface $media */
    $media = $node->get('field_image')->entity;

    if ($media instanceof MediaInterface) {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $node->get('field_image')->entity;
      $caption = $media->get('field_caption')->value;
      /** @var \Drupal\file\FileInterface $file */
      $file = $media->get('field_media_image')->entity;
      if ($file instanceof FileInterface) {
        $image = $file->createFileUrl();
      }
    }

    return [
      '#theme' => 'banner_block',
      '#summary' => $summary,
      '#title' => $title,
      '#breadcrumb' => $breadcrumb,
      '#caption' => $caption,
      '#image' => $image,
    ];
  }

}
