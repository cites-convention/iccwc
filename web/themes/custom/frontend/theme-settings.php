<?php

/**
 * @file
 * Eau de Web Theme theme file.
 *
 * Demo description with code and link
 *  '#description' => t('Demo code <code>.theme-colors</code> and link @demo_link.', [
 *    '@demo_link' => Link::fromTextAndUrl('Containers',
 *                    Url::fromUri('https://getbootstrap.com/docs/5.0/layout/containers/', [
 *                      'absolute' => TRUE,
 *                      'fragment' => 'containers'
 *                     ]))->toString(),
 *  ]),
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 */
function frontend_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $options_theme = [
    'none' => 'do not apply theme',
    'light' => 'light (dark text/links against a light background)',
    'dark' => 'dark (light/white text/links against a dark background)',
  ];

  $options_container = [
    'container' => 'Fixed',
    'container-fluid' => 'Fluid',
  ];

  $form['edwt'] = [
    '#type' => 'vertical_tabs',
    '#title' => t('Theme settings'),
    // '#prefix' => '<div class="h2">' . t('Some text before title') . '</div>',
    '#weight' => -10,
  ];

  // Main settings
  $form['settings'] = array(
    '#type'         => 'details',
    '#title'        => t('Main'),
    //'#description'  => t('some description'),
    '#group' => 'edwt',
    '#weight' => 1,
  );
    include 'theme-settings/settings-global.inc';

    // Sections settings
  $form['sections'] = array(
    '#type'         => 'details',
    '#title'        => t('Sections'),
    '#group' => 'edwt',
    '#weight' => 2,
  );
    include 'theme-settings/settings-sections.inc';

  // Style settings
  $form['style'] = array(
    '#type'         => 'details',
    '#title'        => t('Style'),
    '#description'  => t('Style colors, sizes etc'),
    '#group' => 'edwt',
    '#weight' => 3,
    '#tree' => true,
  );

  $themeHandler = \Drupal::service('theme_handler');
  $themePath = $themeHandler->getTheme($themeHandler->getDefault())->getPath();

  $cssFilePath = $themePath . '/css/vars.css';
  $cssArray = require_once $themePath . '/src/CssArray.php';

  // Rad variables file.
  $content = file_get_contents($cssFilePath);

  $ca = new CssArray();
  $b = $ca->convert($content);

  $form['style']['default'] = [
    '#type' => 'details',
    '#title' => t('Default'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#tree' => true,
  ];

  $form['style']['colors'] = [
    '#type' => 'details',
    '#title' => t('Colors'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#tree' => true,
  ];

  $i = 0;

  foreach ($b[':root'] as $k => $v) {
    $var_value = trim(str_replace("'", "", $v));

    if (str_starts_with($var_value, '#')) {
      $form['style']['colors']['variable_' . $i]['name'] = [
        '#type'          => 'hidden',
        '#default_value' => $k,
      ];

      $form['style']['colors']['variable_' . $i]['value'] = [
        '#type'          => 'color',
        '#title'         => t('Variable: @var', ['@var' => $k]),
        '#default_value' => $var_value,
      ];
    }
    else {
      $form['style']['default']['variable_' . $i]['name'] = [
        '#type'          => 'hidden',
        '#default_value' => $k,
      ];

      $form['style']['default']['variable_' . $i]['value'] = [
        '#type'          => 'textfield',
        '#title'         => t('Variable: @var', ['@var' => $k]),
        '#default_value' => $var_value,
      ];
    }

    $i++;
  }

  $form['#submit'][] = 'frontend_form_system_theme_settings_submit';
}

/**
 * Implements hook_form_system_theme_settings_submit().
 */
function frontend_form_system_theme_settings_submit(&$form, FormStateInterface $form_state) {
  $values = $form_state->getValues();
  $variables = [];
  foreach ($values['style'] as $key => $value) {
    $variables = array_merge($variables, $value);
  }

  $themeHandler = \Drupal::service('theme_handler');
  $themePath = $themeHandler->getTheme($themeHandler->getDefault())->getPath();
  $cssFilePath = $themePath . '/css/variables.css';

  $css = ":root {\n";
  foreach ($variables as $row) {
    $css .= $row['name'] . ": '" . $row['value'] . "';\n";
  }
  $css .= "}\n";

  $themeHandler = \Drupal::service('theme_handler');
  $themePath = $themeHandler->getTheme($themeHandler->getDefault())->getPath();

  file_put_contents($cssFilePath, $css);
}
