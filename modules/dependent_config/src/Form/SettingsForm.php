<?php

namespace Drupal\ikto_environment_indicator_dependent_config\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigNameException;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  const CONFIG_NAME = 'ikto_environment_indicator_dependent_config.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ikto_environment_indicator_dependent_config_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(static::CONFIG_NAME);

    $form['base_dir'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base directory'),
      '#description' => $this->t('Environment dependent configs will be stored in this directory.'),
      '#default_value' => $config->get('base_dir'),
    ];

    $form['config_list'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Config names list'),
      '#description' => $this->t('These configs will be considered as environment dependent.'),
      '#default_value' => implode("\n", is_array($config->get('config_list')) ? $config->get('config_list') : []),
      '#rows' => 10,
    ];

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [static::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $base_dir = trim($form_state->getValue('base_dir'));
    if ($base_dir) {
      if ($base_dir[0] != '/') {
        $base_dir = \Drupal::root() . '/' . $base_dir;
      }
      if (!is_dir($base_dir)) {
        $form_state->setError(
          $form['base_dir'],
          $this->t('The directory @dirname does not exist', ['@dirname' => $base_dir])
        );
      }
    }

    $config_list = explode("\n", $form_state->getValue('config_list'));
    $config_list = array_map(function ($item) {
      return trim($item);
    }, $config_list);
    $config_list = array_filter($config_list);

    try {
      array_walk($config_list, function ($item) {
        Config::validateName($item);
      });
    }
    catch (ConfigNameException $e) {
      $form_state->setError($form['config_list'], $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config(static::CONFIG_NAME);

    $config->set('base_dir', trim($form_state->getValue('base_dir')));

    $config_list = explode("\n", $form_state->getValue('config_list'));
    $config_list = array_map(function ($item) {
      return trim($item);
    }, $config_list);
    $config_list = array_filter($config_list);
    $config->set('config_list', $config_list);

    $config->save();

    parent::submitForm($form, $form_state);
  }
}
