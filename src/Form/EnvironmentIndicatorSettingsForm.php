<?php

/**
 * @file
 * Contains \Drupal\ikto_environment_indicator\Form\EnvironmentIndicatorSettingsForm.
 */

namespace Drupal\ikto_environment_indicator\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EnvironmentIndicatorSettingsForm extends ConfigFormBase implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'ikto_environment_indicator_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ikto_environment_indicator.settings');
    $form = parent::buildForm($form, $form_state);
    $form['git'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show git information'),
      '#description' => $this->t('If available, git information will be shown with the environment name.'),
      '#default_value' => $config->get('git') ?: FALSE,
    ];
    $form['toolbar_integration'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Toolbar integration'),
      '#options' => [
        'toolbar' => $this->t('Toolbar'),
      ],
      '#description' => $this->t('Select the toolbars that you want to integrate with.'),
      '#default_value' => $config->get('toolbar_integration') ?: [],
    ];
    $form['favicon'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show favicon'),
      '#description' => $this->t('If checked, a favicon will be added with the environment colors when the indicator is shown.'),
      '#default_value' => $config->get('favicon') ?: FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ikto_environment_indicator.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ikto_environment_indicator.settings');
    $properties = ['git', 'toolbar_integration', 'favicon'];
    array_walk($properties, function ($property) use ($config, $form_state) {
      $config->set($property, $form_state->getValue($property));
    });
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
