<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;
use Drupal\node\Entity\Node;

/**
 * Provides a 'ICCWC Latest News' Block.
 *
 * @Block(
 *   id = "iccwc_latest_news",
 *   admin_label = @Translation("ICCWC Latest News"),
 *   category = @Translation("ICCWC"),
 * )
 */
class LatestNewsBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $news_id = $this->configuration['news'] ?? NULL;

    $latest_view = Views::getView('latest_news');
    $latest_view->setDisplay('latest_one_news');
    $latest_view->setArguments([$news_id]);
    $latest_view->execute();

    if (empty($news_id) && !empty($latest_view->result)) {
      $news_id = $latest_view->result[0]->nid;
    }

    $featured_view = $latest_view->render();

    $latest_view = [
      '#type' => 'view',
      '#view' => Views::getView('latest_news'),
      '#display_id' => 'block_latest_news',
      '#arguments' => [$news_id],
    ];

    return [
      '#theme' => 'latest_news',
      '#featured_news' => $featured_view,
      '#latest_news' => $latest_view,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $news_id = $this->configuration['news'];

    // Default value for entity_autocomplete needs entities.
    $entity = Node::load($news_id);

    // Attach extra field to block config form.
    $form['news'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t("Leave empty to feature the latest News, or select only one."),
      '#selection_handler' => 'default',
      '#target_type' => 'node',
      '#title' => $this->t('Featured News'),
      '#default_value' => $entity,
      '#selection_settings' => [
        'target_bundles' => ['news'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['news'] = $form_state->getValue('news');
  }

}
