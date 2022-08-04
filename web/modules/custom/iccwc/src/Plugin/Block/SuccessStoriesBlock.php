<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\views\Views;
use Drupal\node\Entity\Node;

/**
 * Provides a 'ICCWC Success stories' Block.
 *
 * @Block(
 *   id = "iccwc_success_stories",
 *   admin_label = @Translation("ICCWC Success stories"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SuccessStoriesBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $featured_story = NULL;
    $story = $this->configuration['story'];

    if (isset($story)) {
      // If featured story selected in block.
      $node = Node::load($story);
    }

    if (!$node instanceof NodeInterface) {
      // No featured story selected in block.
      $latest_view = Views::getView('success_stories');
      $latest_view->setDisplay('latest_success_story');
      $latest_view->execute();
      $view_result = $latest_view->result;

      foreach ($view_result as $data) {
        $node = $data->_entity;
        $story = $node->id();
      }
    }

    if ($node instanceof NodeInterface) {
      $view_builder = $this->entityTypeManager->getViewBuilder('node');
      $featured_story = $view_builder->view($node, 'featured_teaser');
    }

    $view = [
      '#type' => 'view',
      '#view' => Views::getView('success_stories'),
      '#display_id' => 'block_success_stories',
      '#arguments' => [$story],
    ];

    return [
      '#theme' => 'success_stories',
      '#featured_story' => $featured_story,
      '#success_stories' => $view,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $story = $this->configuration['story'];

    // Default value for entity_autocomplete needs entities.
    $entity = Node::load($story);

    // Attach extra field to block config form.
    $form['story'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t("Leave empty to feature the latest Success Story, or select only one."),
      '#selection_handler' => 'default',
      '#target_type' => 'node',
      '#title' => $this->t('Featured Success story'),
      '#default_value' => $entity,
      '#selection_settings' => [
        'target_bundles' => ['success_story'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['story'] = $form_state->getValue('story');
  }

}
