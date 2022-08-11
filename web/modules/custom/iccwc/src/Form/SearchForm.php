<?php

namespace Drupal\iccwc\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\iccwc\ICCWCManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The search form.
 */
class SearchForm extends FormBase {

  /**
   * The ICCWC manager.
   *
   * @var \Drupal\iccwc\ICCWCManager
   */
  protected $iccwcManager;

  /**
   * SearchForm constructor.
   *
   * @param \Drupal\iccwc\ICCWCManager $iccwc_manager
   *   The ICCWC Manager.
   */
  public function __construct(ICCWCManager $iccwc_manager) {
    $this->iccwcManager = $iccwc_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('iccwc.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iccwc_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $search_current_page = FALSE) {
    $form['#method'] = 'GET';
    if ($search_current_page) {
      $form['#action'] = Url::fromRoute('<current>')->toString();
    }
    else {
      $search_page = $this->iccwcManager->getSearchPage();
      if (empty($search_page)) {
        return [];
      }
      $form['#action'] = $search_page->toUrl()->toString();
    }

    $form['search'] = [
      '#type' => 'textfield',
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Search'),
      '#default_value' => '',
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
