<?php

namespace Drupal\iccwc\Form;


use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


class SearchForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'iccwc_search_form';
  }
  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    try {
      $form['#action'] = Url::fromRoute('view.searcg.page_1')->toString();
    }
    catch (\Exception $e) {
      return [];
    }
    $form['#method'] = 'GET';

    $form['search'] = [
      '#type' => 'search',
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Search'),
      '#default_value' => \Drupal::request()->query->get('search') ?? '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['title'] = [
      '#markup' => \Drupal::routeMatch()->getParameter('node')->getTitle(),
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
