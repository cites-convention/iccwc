<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use phpDocumentor\Reflection\Types\False_;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ICCWC Social Media Links' Block.
 *
 * @Block(
 *   id = "iccwc_social_media",
 *   admin_label = @Translation("ICCWC Social Media Links"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SocialMediaLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration for Social Media Links.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   *
   * @see \Drupal\iccwc\Form\SocialMediaConfigForm
   */
  protected $config;

  /**
   * SocialMediaLinksBlock constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->config = $config_factory->get('iccwc_social_media.links');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $twitter_link = $this->config->get('twitter_link');
    $youtube_link = $this->config->get('youtube_link');
    $linkedin_link = $this->config->get('linkedin_link');

    return [
      '#theme' => 'social_media_links',
      '#twitter_link' => $twitter_link,
      '#youtube_link' => $youtube_link,
      '#linkedin_link' => $linkedin_link,
      '#include_print_button' => $this->configuration['include_print_button'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [
      'config:iccwc.social_media.settings',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['include_print_button'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include print button'),
      '#default_value' => False,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['include_print_button'] = $form_state->getValue('include_print_button');
  }

}
