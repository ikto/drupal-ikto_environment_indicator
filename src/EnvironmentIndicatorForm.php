<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;

class EnvironmentIndicatorForm extends EntityForm {

  /**
   * This actually builds your form.
   */
  public function form(array $form, FormStateInterface $form_state) {
    /* @var EnvironmentIndicatorInterface $environment_switcher */
    $environment_switcher = $this->getEntity();

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => $environment_switcher->label(),
    ];
    $form['machine'] = [
      '#type' => 'machine_name',
      '#machine_name' => [
        'source' => ['name'],
        'exists' => 'environment_indicator_load',
      ],
      '#default_value' => $environment_switcher->id(),
      '#disabled' => !empty($environment_switcher->machine),
    ];
    $form['description'] = [
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#default_value' => $environment_switcher->getDescription(),
      '#required' => FALSE,
    ];
    $form['url'] = [
      '#type' => 'url',
      '#title' => t('Hostname'),
      '#description' => t('The hostname you want to switch to.'),
      '#default_value' => $environment_switcher->getUrl(),
    ];
    $form['bg_color'] = [
      '#type' => 'color',
      '#title' => t('Background Color'),
      '#description' => t('Background color for the indicator. Ex: #0D0D0D.'),
      '#default_value' => $environment_switcher->getBgColor() ?: '#0D0D0D',
    ];
    $form['fg_color'] = [
      '#type' => 'color',
      '#title' => t('Color'),
      '#description' => t('Color for the indicator. Ex: #D0D0D0.'),
      '#default_value' => $environment_switcher->getFgColor() ?: '#D0D0D0',
    ];
    $form['weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#default_value' => $environment_switcher->getWeight(),
    ];

    return $form;
  }

  /**
   * Save your config entity.
   *
   * There will eventually be default code to rely on here, but it doesn't exist
   * yet.
   */
  public function save(array $form, FormStateInterface $form_state) {
    $environment = $this->getEntity();
    $environment->save();
    drupal_set_message(t('Saved the %label environment.', [
      '%label' => $environment->label(),
    ]));

    $form_state->setRedirect('entity.ikto_environment_indicator.collection');
  }

}
