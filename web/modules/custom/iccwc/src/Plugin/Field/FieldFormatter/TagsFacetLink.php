<?php

namespace Drupal\iccwc\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\iccwc\ICCWCManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'tags_facet_link' formatter.
 *
 * @FieldFormatter(
 *   id = "tags_facet_link",
 *   label = @Translation("Tags facet link"),
 *   description = @Translation("Display the label of the referenced entities linked to a faceted search."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TagsFacetLink extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The ICCWC manager.
   *
   * @var \Drupal\iccwc\ICCWCManager
   */
  protected $iccwcManager;

  /**
   * Constructs a TagsFacetLink object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\iccwc\ICCWCManager $iccwc_manager
   *   The ICCWC manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ICCWCManager $iccwc_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->iccwcManager = $iccwc_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('iccwc.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $id = $entity->id();

      $elements[$delta] = [
        '#type' => 'link',
        '#url' => $this->iccwcManager->getNewsPage()->toUrl('canonical', [
          'query' => [
            'f' => [
              'tags:' . $id,
            ],
          ],
        ]),
        '#title' => $entity->label(),
      ];
    }
    return $elements;
  }

}
