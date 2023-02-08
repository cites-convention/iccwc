<?php

namespace Drupal\iccwc\Plugin\BootstrapStyles\Style;

use Drupal\bootstrap_styles\Style\StylePluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bootstrap Styles setting for section title.
 *
 * @Style(
 *   id = "section_title",
 *   title = @Translation("Section title"),
 *   group_id = "title",
 *   weight = 1
 * )
 */
class SectionTitle extends StylePluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a StylePluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $form['section_title'] = [
      '#type' => 'textfield',
      '#default_value' => $storage['section_title'],
      '#title' => $this->t('Section title'),
    ];

    $form['social_media'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add social media links next to section title'),
      '#default_value' => $storage['social_media'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'section_title' => $group_elements['section_title'],
      'social_media' => $group_elements['social_media'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    if (!empty($storage['section_title'])) {
      // phpcs:ignore
      $build['#section_title'] = $this->t($storage['section_title']);
    }
    if (!empty($storage['social_media'])) {
      $block = $this->entityTypeManager
        ->getStorage('block')
        ->load('iccwcsocialmedialinks_2');

      $build['#social_media'] = $this->entityTypeManager
        ->getViewBuilder('block')
        ->view($block);
    }
    return $build;
  }

}
