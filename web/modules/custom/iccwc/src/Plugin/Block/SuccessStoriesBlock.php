<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
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
    $story_id = $this->configuration['story'] ?? NULL;

    $latest_view = Views::getView('success_stories');
    $latest_view->setDisplay('latest_success_story');
    $latest_view->setArguments([$story_id]);
    $latest_view->execute();

    if (empty($story_id) && !empty($latest_view->result)) {
      $story_id = $latest_view->result[0]->nid;
    }

    $featured_story = $latest_view->render();

    $view = [
      '#type' => 'view',
      '#view' => Views::getView('success_stories'),
      '#display_id' => 'block_success_stories',
      '#arguments' => [$story_id],
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
    $story_id = $this->configuration['story'];

    // Default value for entity_autocomplete needs entities.
    $entity = Node::load($story_id);

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
