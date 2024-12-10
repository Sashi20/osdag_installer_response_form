<?php
namespace Drupal\installer_response_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallerUploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'upload_installer';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Add form fields
   $form['#attributes'] = array(
    'enctype' => 'multipart/form-data',
  );

  // Release date field
  $form['release_date'] = array(
  '#type' => 'date',
  '#title' => t('Release date of the installer'),
  '#date_date_format' => 'd-m-Y',
  '#date_year_range' => '1960:+22',
  '#required' => TRUE,
);

  // Operating system field
  $form['operating_system'] = array(
    '#type' => 'select',
    '#title' => t('Select Operating System'),
    '#options' => array(
      'Ubuntu' => 'Ubuntu',
      'Windows' => 'Windows',
    ),
    '#required' => TRUE,
  );

  // OS version field
  $form['os_version'] = array(
    '#type' => 'select',
    '#title' => t('Select the version'),
    '#options' => array(
      'Ubuntu 16.04 and above - 64-bit' => 'Ubuntu 16.04 and above - 64-bit',
      'Ubuntu 14.04 and above - 64-bit' => 'Ubuntu 14.04 and above - 64-bit',
      'Windows 7,8 and 10 - 32-bit & 64-bit' => 'Windows 7,8 and 10 - 32-bit & 64-bit',
    ),
    '#required' => TRUE,
  );

  // File upload fields
  $form['upload_files'] = array(
    '#type' => 'fieldset',
    '#title' => t('Upload the files'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  // Installer file upload field
  $form['upload_files']['installer_file'] = array(
    '#type' => 'textfield',
    '#title' => t('Enter the filename of the installer file along with the extension'),
    '#required' => TRUE,
  );

  // Instructions file upload field
  $form['upload_files']['instructions_file'] = array(
    '#type' => 'textfield',
    '#title' => t('Enter the filename of the instructions file along with the extension'),
    '#required' => TRUE,
  );

  // Submit button
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  $values = $form_state->getValues();
  // Insert form data into the database using safe insert method
  \Drupal::database()->insert('installer_files')
    ->fields(array(
      'release_date' => $values['release_date'],
      'operating_system' => $values['operating_system'],
      'os_version' => $values['os_version'],
      'installer_path' => $values['installer_file'],
      'instruction_file_path' => $values['instructions_file'],
    ))
    ->execute();

  // Add a success message
  \Drupal::messenger()->addStatus(t('Installer uploaded successfully.'));
}
}