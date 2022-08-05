<?php

namespace Drupal\iccwc_scrollup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ThemeHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure animated scroll to top settings for this site.
 */
class ICCWCScrollupForm extends ConfigFormBase {

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandler
   */
  protected $themeHandler;

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'iccwc_scrollup.settings';

  /**
   * Implements getEditableConfigNames().
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * Implements getFormId().
   */
  public function getFormId() {
    return 'iccwc_scrollup_form';
  }

  /**
   * Constructs the ICCWCScrollupForm.
   *
   * @param \Drupal\Core\Extension\ThemeHandler $theme_handler
   *   The theme handler.
   */
  public function __construct(ThemeHandler $theme_handler) {
    $this->themeHandler = $theme_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('theme_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['iccwc_themename_fieldset'] = [
      '#title' => $this->t('Theme Visibility Configuration'),
      '#type' => 'fieldset',
    ];
    $form['iccwc_themename_fieldset']['iccwc_scrollup_themename'] = [
      '#title' => $this->t('Themes Name'),
      '#description' => $this->t('Scroll up button add multiple themes.'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $this->getThemeNames(),
      '#default_value' => $config->get('iccwc_scrollup_themename'),
    ];
    $form['iccwc_scrolling_fieldset'] = [
      '#title' => $this->t('Scrolling behaviour Configuration'),
      '#type' => 'fieldset',
    ];
    $form['iccwc_scrolling_fieldset']['iccwc_scrollup_title'] = [
      '#title' => $this->t('Scrollup Button Title'),
      '#description' => $this->t('scrollup button title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('iccwc_scrollup_title'),
    ];
    $form['iccwc_scrolling_fieldset']['iccwc_scrollup_hoover_title'] = [
      '#title' => $this->t('Scrollup Button Hoover Title'),
      '#description' => $this->t('scrollup button hoover title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('iccwc_scrollup_hoover_title'),
    ];
    $form['iccwc_scrolling_fieldset']['iccwc_scrollup_window_position'] = [
      '#title' => $this->t('Window scrollup fadeIn and fadeout position'),
      '#description' => $this->t('Enter the value of fadeIn & fadeout window scrollup in ms.'),
      '#type' => 'number',
      '#required' => TRUE,
      '#default_value' => $config->get('iccwc_scrollup_window_position'),
    ];
    $form['iccwc_scrolling_fieldset']['iccwc_scrollup_speed'] = [
      '#title' => $this->t('Scrollup speed'),
      '#description' => $this->t('Enter the value of Scrollup speed in ms.'),
      '#type' => 'number',
      '#required' => TRUE,
      '#default_value' => $config->get('iccwc_scrollup_speed'),
    ];
    $form['iccwc_button_fieldset'] = [
      '#title' => $this->t('Scrollup Button Configuration'),
      '#type' => 'fieldset',
    ];
    $form['iccwc_button_fieldset']['iccwc_scrollup_position'] = [
      '#title' => $this->t('Button Position'),
      '#description' => $this->t('Scrollup button position.'),
      '#type' => 'select',
      '#options' => [
        1 => $this->t('right'),
        2 => $this->t('left'),
      ],
      '#default_value' => $config->get('iccwc_scrollup_position'),
    ];
    $form['iccwc_button_fieldset']['iccwc_scrollup_button_bg_color'] = [
      '#title' => $this->t('Scrollup button background color'),
      '#description' => $this->t('Scrollup button background color.'),
      '#type' => 'color',
      '#default_value' => $config->get('iccwc_scrollup_button_bg_color'),
    ];
    $form['iccwc_button_fieldset']['iccwc_scrollup_button_hover_bg_color'] = [
      '#title' => $this->t('Scrollup button hover background color'),
      '#description' => $this->t('Scrollup button hover background color.'),
      '#type' => 'color',
      '#default_value' => $config->get('iccwc_scrollup_button_hover_bg_color'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Retrieve theme names indexed by ID.
   *
   * @return array
   *   An array with theme names indexed by ID.
   */
  public function getThemeNames() {
    $themes = $this->themeHandler->listInfo();
    foreach ($themes as $key => $val) {
      $theme_arr[$key] = $val->info['name'];
    }

    return $theme_arr;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $iccwc_scrollup_position = $form_state->getValue('iccwc_scrollup_position');
    $iccwc_scrollup_button_bg_color = $form_state->getValue('iccwc_scrollup_button_bg_color');
    $iccwc_scrollup_button_hover_bg_color = $form_state->getValue('iccwc_scrollup_button_hover_bg_color');
    $iccwc_scrollup_title = $form_state->getValue('iccwc_scrollup_title');
    $iccwc_scrollup_hoover_title = $form_state->getValue('iccwc_scrollup_hoover_title');
    $iccwc_scrollup_window_position = $form_state->getValue('iccwc_scrollup_window_position');
    $iccwc_scrollup_speed = $form_state->getValue('iccwc_scrollup_speed');
    $iccwc_scrollup_themename = $form_state->getValue('iccwc_scrollup_themename');

    $this->config(static::SETTINGS)
      ->set('iccwc_scrollup_position', $iccwc_scrollup_position)
      ->set('iccwc_scrollup_button_bg_color', $iccwc_scrollup_button_bg_color)
      ->set('iccwc_scrollup_button_hover_bg_color', $iccwc_scrollup_button_hover_bg_color)
      ->set('iccwc_scrollup_title', $iccwc_scrollup_title)
      ->set('iccwc_scrollup_hoover_title', $iccwc_scrollup_hoover_title)
      ->set('iccwc_scrollup_window_position', $iccwc_scrollup_window_position)
      ->set('iccwc_scrollup_speed', $iccwc_scrollup_speed)
      ->set('iccwc_scrollup_themename', $iccwc_scrollup_themename)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
