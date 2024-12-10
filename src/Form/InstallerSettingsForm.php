<?php
namespace Drupal\installer_response_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class InstallerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // The name of the config file.
    return ['installer_response_form.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'installer_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load the configuration
    $config = \Drupal::config('installer_response_form.settings');

    $form['extensions']['installer_file'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed source file extensions for the installer file'),
      '#description' => $this->t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
      '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('installer_file_extensions'),
    ];

    $form['extensions']['instructions_file'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed file extensions for the installer instructions'),
      '#description' => $this->t('A comma separated list WITHOUT SPACE of dependency file extensions that are permitted to be uploaded on the server'),
      '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('instructions_file_extensions'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save configuration values.
    \Drupal::configFactory()->getEditable('installer_response_form.settings')
      ->set('installer_file_extensions', $form_state->getValue('installer_file'))
      ->set('instructions_file_extensions', $form_state->getValue('instructions_file'))
      ->save();

    // Display success message
    \Drupal::messenger()->addStatus($this->t('Settings updated'));
  }
}
