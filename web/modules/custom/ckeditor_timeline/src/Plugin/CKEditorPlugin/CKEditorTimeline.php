<?php

namespace Drupal\ckeditor_timeline\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "CKEditorTimeline" plugin.
 *
 * @CKEditorPlugin (
 *   id = "timeline",
 *   label = @Translation("CKEditor Timeline"),
 *   module = "ckeditor_timeline"
 * )
 */
class CKEditorTimeline extends CKEditorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * Constructs a new CKEditorTimeline plugin object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extension_list_module
   *   The module extension list.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleExtensionList $extension_list_module) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleExtensionList = $extension_list_module;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->moduleExtensionList->getPath('ckeditor_timeline') . '/js/plugins/timeline/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = $this->moduleExtensionList->getPath('ckeditor_timeline') . '/js/plugins/timeline/icons';
    return [
      'Timeline' => [
        'label' => $this->t('Add Timline'),
        'image' => $path . '/timeline.png',
      ],
    ];
  }

}
