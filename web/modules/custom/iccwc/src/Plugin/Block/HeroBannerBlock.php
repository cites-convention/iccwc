<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\iccwc\Form\SearchForm;
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
   * The ICCWC manager.
   *
   * @var \Drupal\iccwc\ICCWCManager
   */
  protected $iccwcManager;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->breadcrumb = $container->get('breadcrumb');
    $instance->iccwcManager = $container->get('iccwc.manager');
    $instance->formBuilder = $container->get('form_builder');
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
      return [];
    }

    $summary = $node->get('body')->summary;
    $title = $node->get('title')->value;

    if (!$node->hasField('field_banner_image')
      || $node->get('field_banner_image')->isEmpty()
    ) {
      return [];
    }

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

    if ($node->bundle() == 'news' && !$node->get('field_tags')->isEmpty()) {
      /** @var \Drupal\taxonomy\TermInterface $tag_term */
      $tag_term = $node->get('field_tags')->entity;
      $tag = $tag_term->label();
    }

    $form = NULL;
    if ($this->iccwcManager->isNewsPage()
      || $this->iccwcManager->isSearchPage()) {
      $form = $this->formBuilder->getForm(SearchForm::class, TRUE);
      $form['form_token']['#access'] = FALSE;
      $form['form_id']['#access'] = FALSE;
      $form['form_build_id']['#access'] = FALSE;
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
      '#form' => $form,
    ];
  }

}
