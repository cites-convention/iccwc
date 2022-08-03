<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Views;

/**
 * Provides a 'ICCWC Success stories' Block.
 *
 * @Block(
 *   id = "iccwc_success_stories",
 *   admin_label = @Translation("ICCWC Success stories"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SuccessStoriesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration for Success stories.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   *
   * @see \Drupal\iccwc\Form\SocialMediaConfigForm
   */
  protected $config;

  /**
   * SuccessStoriesBlock constructor.
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

    $this->config = $config_factory->get('iccwc_success_stories');
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
      '#theme' => 'success_stories',
      '#related_content_view' => $result,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    // Attach extra field to block config form
    $form['stories'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Success Stories'),
//      '#description' => $this->t('Select Success Stories:'),
//      '#default_value' => $default_entities,
      '#tags' => TRUE,
      '#selection_settings' => array(
        'target_bundles' => array('page', 'article'),
      ),
      '#weight' => '0',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['stories'] = $form_state->getValue('stories');
  }
}
