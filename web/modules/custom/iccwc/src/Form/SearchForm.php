<?php

namespace Drupal\iccwc\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * The search form.
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iccwc_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    try {
      $form['#action'] = Url::fromRoute('view.search.page_1')->toString();
    }
    catch (\Exception $e) {
      return [];
    }
    $form['#method'] = 'GET';

    $form['search'] = [
      '#type' => 'textfield',
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Search'),
      '#default_value' => $this->getRequest()->query->get('search') ?? '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
