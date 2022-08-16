<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Template\Attribute;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Provides an Sidebar Filters block.
 *
 * @Block(
 *   id = "iccwc_sidebar_filters",
 *   admin_label = @Translation("Sidebar Filters"),
 *   category = @Translation("ICCWC")
 * )
 */
class SidebarFiltersBlock extends ICCWCBlockBase {

  /**
   * The facet manager service.
   *
   * @var \Drupal\facets\FacetManager\DefaultFacetManager|object|null
   */
  protected $facetManager;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack|null
   */
  protected $currentPath;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->facetManager = $container->get('facets.manager');
    $instance->currentPath = $container->get('path.current');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'facets' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $list = $this->entityTypeManager
      ->getStorage('facets_facet')
      ->getQuery('AND')
      ->condition('status', 1)
      ->execute();

    $facets = $this->entityTypeManager
      ->getStorage('facets_facet')
      ->loadMultiple($list);

    $options = [];

    foreach ($facets as $facet) {
      $options[$facet->id()] = $facet->label()
        . ':'
        . $facet->getFacetSourceConfig()->getName();
    }

    $form['facets'] = [
      '#type' => 'checkboxes',
      '#options' => $options,
      '#title' => $this->t('Select Filters'),
      '#default_value' => $this->configuration['facets'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['facets'] = $form_state->getValue('facets');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $facets = $this->configuration['facets'];

    $facets = $this->entityTypeManager
      ->getStorage('facets_facet')
      ->loadMultiple($facets);

    $sorted_facets = [];
    $keys = [];
    foreach ($keys as $key) {
      if (!empty($facets[$key])) {
        $sorted_facets[$key] = $facets[$key];
      }
    }
    foreach ($facets as $index => $value) {
      if (!in_array($index, $keys)) {
        $sorted_facets[$index] = $value;
      }
    }

    /** @var \Drupal\facets\FacetInterface $facet */
    foreach ($sorted_facets as $facet) {
      $result = $this->facetManager->build($facet);
      if (empty($facet->getActiveItems())
        && (empty($result[0]['#items']) || count($result[0]['#items']) <= 1)) {
        continue;
      }
      $build[] = $result;
    }

    return $build;
  }

}
