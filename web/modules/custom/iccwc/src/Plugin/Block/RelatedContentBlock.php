<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Views;

/**
 * Provides a 'ICCWC Related Content' Block.
 *
 * @Block(
 *   id = "iccwc_related_content",
 *   admin_label = @Translation("ICCWC Related Content"),
 *   category = @Translation("ICCWC"),
 * )
 */
class RelatedContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration for Social Media Links.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   *
   * @see \Drupal\iccwc\Form\SocialMediaConfigForm
   */
  protected $config;

  /**
   * SocialMediaLinksBlock constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->config = $config_factory->get('iccwc_related_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $ref_type = $this->configuration['ref_type'];
    $ct_types = $this->configuration['ct_type'];
    $result = NULL;
    $args = NULL;
    $block_display_id = NULL;

    if ("view_data" == $ref_type) {
      // Show most recent content of selected content types
      $args = implode('+', $ct_types);
      $block_display_id = 'block_related_content';

    } else {
      // Shw data from field from node
      $nids = [];

      $entity = \Drupal::routeMatch()->getParameter('node');
      if ($entity instanceof \Drupal\node\NodeInterface) {
        $related_ents = $entity->get('field_related_content')->getValue();
        if (isset($related_ents)) {
          foreach ($related_ents as $related_ent) {
            $nids[] = $related_ent['target_id'];
          }

          $args = implode('+', $nids);
          $block_display_id = 'block_related_node';
        }
      }
    }

    if (isset($args) && isset($block_display_id)) {
      // Generate view results based on selected options
      $view = Views::getView('related_content');
      if (is_object($view)) {
        $view->setArguments([$args]);
        $view->setDisplay($block_display_id);
        $view->preExecute();
        $view->execute();
        $render = $view->render();

        $result = \Drupal::service('renderer')->render($render);
      }
    }

    return [
      '#theme' => 'related_content',
      '#ref_type' => $ref_type,
      '#related_content_view' => $result,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    // Attach extra fields to block config form
    $options['field_data'] = $this->t('Display content from current page');
    $options['view_data'] = $this->t('Display latest content');

    $form['ref_type'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => $this->t('Reference type'),
      '#default_value' => $this->configuration['ref_type'],
    );

    // Get a list of all content types
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $types = [];
    $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $contentType) {
      $types[$contentType->id()] = $contentType->label();
    }

    $form['ct_type'] = array(
      '#type' => 'select',
      '#options' => $types,
      '#required' => TRUE,
      '#title' => $this->t('Reference type'),
      '#multiple' => TRUE,
      '#default_value' => $this->configuration['ct_type'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['ref_type'] = $form_state->getValue('ref_type');
    $this->configuration['ct_type'] = $form_state->getValue('ct_type');
  }
}
