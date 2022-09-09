<?php

namespace Drupal\mapbox_un\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\mapbox_un\Services\MapboxUnManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'MapboxUnMapBlock' Block.
 *
 * @Block(
 *   id = "mapbox_un_map",
 *   admin_label = @Translation("Mapbox Map"),
 *   category = @Translation("Mapbox UN"),
 * )
 */
class MapboxUnMapBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * The Mapbox UN manager.
   *
   * @var \Drupal\mapbox_un\Services\MapboxUnManager
   */
  protected $mapboxManager;

  /**
   * MapboxUnMapBlock constructor.
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
   * @param \Drupal\Core\Extension\ModuleExtensionList $module_extension_list
   *   The module extension list.
   * @param \Drupal\mapbox_un\Services\MapboxUnManager $mapbox_manager
   *   The Mapbox UN manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, ModuleExtensionList $module_extension_list, MapboxUnManager $mapbox_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
    $this->moduleExtensionList =  $module_extension_list;
    $this->mapboxManager = $mapbox_manager;
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
      $container->get('extension.list.module'),
      $container->get('mapbox_un.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $map_disclaimer = NULL;
    $parties_list = NULL;
    $categories_list = NULL;
    $selected_regions = [];

    if (isset($this->configuration['dataset'])) {
      $term_id = $this->configuration['dataset'];

      $entity = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_id);
      if (empty($entity)) {
        return [];
      }

      $entity = $this->entityRepository->getTranslationFromContext($entity);
      $map_disclaimer['#markup'] = $entity->getDescription();

      $parties_list = $this->mapboxManager->getPartiesMapOverview($entity);
      $categories_list = $this->mapboxManager->getCategoriesMapOverview($entity);
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
      '#theme' => 'mapbox_un_map_overview',
      '#list_of_parties' => [],
      '#categories' => $categories_list,
      '#attached' => [
        'library' => [
          'mapbox_un/map',
        ],
        'drupalSettings' => [
          'mapbox_un' => [
            'parties' => $parties_list['fill'],
            'coordinates' => $parties_list['coordinates'],
            'module_path' => $this->moduleExtensionList->getPath('mapbox_un'),
          ],
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
    $options = [];

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->getQuery()
      ->condition('vid', 'map_datasets')
      ->condition('parent', 0)
      ->execute();

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($terms);

    foreach ($terms as $term) {
      $options[$term->id()] = $term->label();
    }

    // Attach extra field to block config form.
    $form['dataset'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Map dataset'),
      '#default_value' => $this->configuration['dataset'] ?? '',
    ];

    $form['regions'] = [
      '#title' => $this->t('Region filters'),
      '#description' => $this->t('Leave empty for no region filters.'),
      '#type' => 'checkboxes',
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
