<?php

namespace Drupal\iccwc_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\iccwc_map\Services\ICCWCMapBaseUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ICCWCMapBlock' Block.
 *
 * @Block(
 *   id = "iccwc_dashboard_map",
 *   admin_label = @Translation("ICCWC Map"),
 *   category = @Translation("ICCWC"),
 * )
 */
class ICCWCMapBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The ICCWC Map Utils.
   *
   * @var \Drupal\iccwc_map\Services\ICCWCMapBaseUtils
   */
  protected $iccwcMapUtils;

  /**
   * ICCWCMapBlock constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\iccwc_map\Services\ICCWCMapBaseUtils $iccwc_map_utils
   *   The ICCWC Map Utils.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, ICCWCMapBaseUtils $iccwc_map_utils) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
    $this->iccwcMapUtils = $iccwc_map_utils;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('iccwc_map_utils.utils')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $map_disclaimer = NULL;
    $parties_list = NULL;
    $categories_list = NULL;

    if (isset($this->configuration['dataset'])) {
      $term_id = $this->configuration['dataset'];

      $entity = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_id);
      if (empty($entity)) {
        return [];
      }

      $entity = $this->entityRepository->getTranslationFromContext($entity);
      $map_disclaimer['#markup'] = $entity->getDescription();

      $parties_list = $this->iccwcMapUtils->getPartiesMapOverview($entity);
      $categories_list = $this->iccwcMapUtils->getCategoriesMapOverview($entity);
    }

    $regions = $this->configuration['regions'] ?? NULL;
    $options = [
      'africa' => 'Africa',
      'asia' => 'Asia',
      'latin_america' => 'Central and South America and the Caribbean',
      'europe' => 'Europe',
      'north_america' => 'North America',
      'oceania' => 'Oceania',
    ];

    if ($regions != NULL) {
      foreach ($regions as $region) {
        $selected_regions[$region] = $options[$region];
      }
    }

    $build = [
      '#theme' => 'iccwc_map_overview',
      '#list_of_parties' => [],
      '#categories' => $categories_list,
      '#attached' => [
        'library' => [
          'iccwc_map/map',
        ],
        'drupalSettings' => [
          'parties' => $parties_list['fill'],
          'coordinates' => $parties_list['coordinates'],
        ],
      ],
      '#disclaimer' => [
        $map_disclaimer,
      ],
      '#regions' => $selected_regions,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    if (isset($this->configuration['dataset'])) {
      $term_id = $this->configuration['dataset'];

      // Default value for entity_autocomplete needs entities.
      $entity = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_id);
      if (!empty($entity)) {
        $entity = $this->entityRepository->getTranslationFromContext($entity);
      }
    }

    // Attach extra field to block config form.
    $form['dataset'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t('Select first level term, and only one value.'),
      '#selection_handler' => 'default',
      '#target_type' => 'taxonomy_term',
      '#title' => $this->t('Map dataset'),
      '#default_value' => $entity ?? NULL,
      '#selection_settings' => [
        'target_bundles' => ['map_datasets'],
      ],
    ];

    $form['regions'] = [
      '#title' => $this->t('Regions'),
      '#description' => $this->t('Leave empty for no regions.'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => [
        'africa' => $this->t('Africa'),
        'asia' => $this->t('Asia'),
        'latin_america' => $this->t('Central and South America and the Caribbean'),
        'europe' => $this->t('Europe'),
        'north_america' => $this->t('North America'),
        'oceania' => $this->t('Oceania'),
      ],
      '#default_value' => $this->configuration['regions'] ?? NULL,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['dataset'] = $form_state->getValue('dataset');
    $this->configuration['regions'] = $form_state->getValue('regions');
  }

}
