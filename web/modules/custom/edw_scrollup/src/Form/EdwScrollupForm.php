<?php

namespace Drupal\edw_scrollup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ThemeHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure animated scroll to top settings for this site.
 */
class EdwScrollupForm extends ConfigFormBase {

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
  const SETTINGS = 'edw_scrollup.settings';

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
    return 'edw_scrollup_form';
  }

  /**
   * Constructs the EdwScrollupForm.
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

    $form['edw_themename_fieldset'] = [
      '#title' => $this->t('Theme Visibility Configuration'),
      '#type' => 'fieldset',
    ];
    $form['edw_themename_fieldset']['edw_scrollup_themename'] = [
      '#title' => $this->t('Themes Name'),
      '#description' => $this->t('Scroll up button add multiple themes.'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $this->getThemeNames(),
      '#default_value' => $config->get('edw_scrollup_themename'),
    ];
    $form['edw_scrolling_fieldset'] = [
      '#title' => $this->t('Scrolling behaviour Configuration'),
      '#type' => 'fieldset',
    ];
    $form['edw_scrolling_fieldset']['edw_scrollup_title'] = [
      '#title' => $this->t('Scrollup Button Title'),
      '#description' => $this->t('scrollup button title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('edw_scrollup_title'),
    ];
    $form['edw_scrolling_fieldset']['edw_scrollup_hover_title'] = [
      '#title' => $this->t('Scrollup Button hover Title'),
      '#description' => $this->t('scrollup button hover title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('edw_scrollup_hover_title'),
    ];
    $form['edw_scrolling_fieldset']['edw_scrollup_window_position'] = [
      '#title' => $this->t('Window scrollup fadeIn and fadeout position'),
      '#description' => $this->t('Enter the value of fadeIn & fadeout window scrollup in ms.'),
      '#type' => 'number',
      '#required' => TRUE,
      '#default_value' => $config->get('edw_scrollup_window_position'),
    ];
    $form['edw_scrolling_fieldset']['edw_scrollup_speed'] = [
      '#title' => $this->t('Scrollup speed'),
      '#description' => $this->t('Enter the value of Scrollup speed in ms.'),
      '#type' => 'number',
      '#required' => TRUE,
      '#default_value' => $config->get('edw_scrollup_speed'),
    ];
    $form['edw_button_fieldset'] = [
      '#title' => $this->t('Scrollup Button Configuration'),
      '#type' => 'fieldset',
    ];
    $form['edw_button_fieldset']['edw_scrollup_position'] = [
      '#title' => $this->t('Button Position'),
      '#description' => $this->t('Scrollup button position.'),
      '#type' => 'select',
      '#options' => [
        1 => $this->t('right'),
        2 => $this->t('left'),
      ],
      '#default_value' => $config->get('edw_scrollup_position'),
    ];
    $form['edw_button_fieldset']['edw_scrollup_button_bg_color'] = [
      '#title' => $this->t('Scrollup button background color'),
      '#description' => $this->t('Scrollup button background color.'),
      '#type' => 'color',
      '#default_value' => $config->get('edw_scrollup_button_bg_color'),
    ];
    $form['edw_button_fieldset']['edw_scrollup_button_hover_bg_color'] = [
      '#title' => $this->t('Scrollup button hover background color'),
      '#description' => $this->t('Scrollup button hover background color.'),
      '#type' => 'color',
      '#default_value' => $config->get('edw_scrollup_button_hover_bg_color'),
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
    $edw_scrollup_position = $form_state->getValue('edw_scrollup_position');
    $edw_scrollup_button_bg_color = $form_state->getValue('edw_scrollup_button_bg_color');
    $edw_scrollup_button_hover_bg_color = $form_state->getValue('edw_scrollup_button_hover_bg_color');
    $edw_scrollup_title = $form_state->getValue('edw_scrollup_title');
    $edw_scrollup_hover_title = $form_state->getValue('edw_scrollup_hover_title');
    $edw_scrollup_window_position = $form_state->getValue('edw_scrollup_window_position');
    $edw_scrollup_speed = $form_state->getValue('edw_scrollup_speed');
    $edw_scrollup_themename = $form_state->getValue('edw_scrollup_themename');

    $this->config(static::SETTINGS)
      ->set('edw_scrollup_position', $edw_scrollup_position)
      ->set('edw_scrollup_button_bg_color', $edw_scrollup_button_bg_color)
      ->set('edw_scrollup_button_hover_bg_color', $edw_scrollup_button_hover_bg_color)
      ->set('edw_scrollup_title', $edw_scrollup_title)
      ->set('edw_scrollup_hover_title', $edw_scrollup_hover_title)
      ->set('edw_scrollup_window_position', $edw_scrollup_window_position)
      ->set('edw_scrollup_speed', $edw_scrollup_speed)
      ->set('edw_scrollup_themename', $edw_scrollup_themename)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
