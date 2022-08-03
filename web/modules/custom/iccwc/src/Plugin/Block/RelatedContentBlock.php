<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
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
class RelatedContentBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $reference_type = $this->configuration['reference_type'];
    $content_types = $this->configuration['content_type'];
    $arg_content_types = 'all';
    $arg_nids = 'all';

    // Show most recent content of selected content types.
    if ($reference_type == 'view_data') {
      $arg_content_types = implode('+', $content_types);
      if (empty($arg_content_types)) {
        $arg_content_types = 'all';
      }
    }
    else {
      // Show data from field from node.
      $entity = $this->routeMatch->getParameter('node');
      if ($entity instanceof NodeInterface) {
        $related_entities = array_column($entity->get('field_related_content')->getValue(), 'target_id');
        $arg_nids = implode('+', $related_entities);
      }
    }

    $view = [
      '#type' => 'view',
      '#view' => Views::getView('related_content'),
      '#display_id' => 'block_related_content',
      '#arguments' => [$arg_content_types, $arg_nids],
    ];

    $see_more_page = $this->configuration['see_more_page'];
    $see_more_text = $this->configuration['see_more_text'];
    $see_more_link = NULL;
    if (!empty($see_more_page)) {
      $node = $this->entityTypeManager->getStorage('node')->load($see_more_page);
      if ($node instanceof NodeInterface) {
        $see_more_link = $node->toUrl()->toString();
      }
    }

    return [
      '#theme' => 'related_content',
      '#related_content_view' => $view,
      '#see_more_link' => $see_more_link,
      '#see_more_text' => $see_more_text,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['reference_type'] = [
      '#type' => 'select',
      '#options' => [
        'field_data' => $this->t('Display content from current page'),
        'view_data' => $this->t('Display latest content'),
      ],
      '#required' => TRUE,
      '#title' => $this->t('Reference type'),
      '#default_value' => $this->configuration['reference_type'] ?? 'field_data',
    ];

    $form['content_type'] = [
      '#type' => 'select',
      '#options' => [
        'news' => $this->t('News'),
        'success_story' => $this->t('Success stories'),
        'page' => $this->t('Pages'),
      ],
      '#title' => $this->t('Content type'),
      '#multiple' => TRUE,
      '#default_value' => $this->configuration['content_type'],
      '#states' => [
        'visible' => [
          'select[name="settings[reference_type]"]' => ['value' => 'view_data'],
        ],
      ],
    ];

    $form['see_more_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('See more text'),
      '#default_value' => $this->configuration['see_more_text'],
    ];

    $form['see_more_page'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('See more page'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['page'],
      ],
      '#default_value' => $this->configuration['see_more_page'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['reference_type'] = $form_state->getValue('reference_type');
    $this->configuration['content_type'] = $form_state->getValue('content_type');
    $this->configuration['see_more_text'] = $form_state->getValue('see_more_text');
    $this->configuration['see_more_page'] = $form_state->getValue('see_more_page');
  }

}
