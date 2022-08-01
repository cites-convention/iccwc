<?php

namespace Drupal\iccwc\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class SocialMediaConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'iccwc_social_media.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iccwc_social_media_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['twitter_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter link'),
      '#default_value' => $config->get('twitter_link'),
    ];

    $form['youtube_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Youtube link'),
      '#default_value' => $config->get('youtube_link'),
    ];

    $form['linkedin_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Linkedin link'),
      '#default_value' => $config->get('linkedin_link'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      ->set('twitter_link', $form_state->getValue('twitter_link'))
      ->set('youtube_link', $form_state->getValue('youtube_link'))
      ->set('linkedin_link', $form_state->getValue('linkedin_link'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
