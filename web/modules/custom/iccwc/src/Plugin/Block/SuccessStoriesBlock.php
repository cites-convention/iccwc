<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
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
    $featured_date = NULL;
    $featured_title = NULL;
    $featured_image = NULL;
    $featured_text = NULL;
    $featured_link = NULL;

    $story = $this->configuration['story'];

    if (isset($story)) {
      // If featured story selected in block
      $node = Node::load($story);
    } else {
      // No featured story selected in block
      $latest_view = Views::getView('success_stories');
      $latest_view->setDisplay('latest_success_story');
      $latest_view->execute();
      $view_result = $latest_view->result;

      foreach ($view_result as $data) {
        $node = $data->_entity;
        $story = $node->id();
      }
    }

    if (isset($node)) {
      $featured_date = $node->get('created')->getValue();
      $featured_title = $node->getTitle();
      $featured_image = $node->get('field_image')->view('teaser');
      $featured_text = $node->get('field_banner_text')->view('teaser');
      $featured_link = $node->toUrl()->toString();
    }

    $view = [
      '#type' => 'view',
      '#view' => Views::getView('success_stories'),
      '#display_id' => 'block_success_stories',
      '#arguments' => [$story],
    ];

    return [
      '#theme' => 'success_stories',
      '#featured_date' => $featured_date[0]['value'],
      '#featured_title' => $featured_title,
      '#featured_image' => $featured_image,
      '#featured_text' => $featured_text,
      '#featured_link' => $featured_link,
      '#success_stories' => $view,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    $story = $this->configuration['story'];

    // Default value for entity_autocomplete needs entities.
    $entity = Node::load($story);

    // Attach extra field to block config form
    $form['story'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t("Leave empty to feature the latest Success Story, or select only one."),
      '#selection_handler' => 'default',
      '#target_type' => 'node',
      '#title' => $this->t('Featured Success story:'),
      '#default_value' => $entity,
      '#selection_settings' => array(
        'target_bundles' => array('success_story'),
      ),
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
