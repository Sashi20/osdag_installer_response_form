<?php
namespace Drupal\installer_response_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;


class InstallerForm extends FormBase {

  public function getFormId() {
    return 'download_installer';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $url_id = \Drupal::routeMatch()->getParameter('url_id'); // Get URL parameter
    $release_date = \Drupal::routeMatch()->getParameter('release_date'); // Get release date

    // Introduction section
    $form['life_story'] = [
      '#type' => 'item',
      '#attributes' => ['readonly' => 'readonly'],
      '#markup' => $this->t('
        <center><p style="font-size:14px;">Osdag®️ license and its resources are completely free of cost!</p>
        <p style="font-size:14px;">Osdag®️ and the Osdag logo are the registered trademarks of the Indian Institute of Technology, Bombay</p></center><br>
        <p style="font-size:14px;">Please fill the `Osdag User Information Form’ given below. Your data is completely safe with IIT Bombay and shall be used only for internal quality assessment by the team, sending timely e-mail notification(s) regarding Osdag events/workshops, installer updates/patches, etc.</p>
        <p style="font-size:14px;">We will not spam you with frequent emails!</p>'),
    ];

    // Form fields
    $form['name_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Title'),
      '#options' => [
        'Dr' => 'Dr',
        'Prof' => 'Prof',
        'Mr' => 'Mr',
        'Ms' => 'Ms',
      ],
      '#required' => TRUE,
       '#attributes' => array('class' => array('form-control')),
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
      '#description' => $this->t('Please enter your first name.'),
      '#size' => 50,
      '#maxlength' => 255,
       '#attributes' => array('class' => array('form-control')),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
      '#description' => $this->t('Please enter your last name.'),
      '#size' => 50,
      '#maxlength' => 255,
       '#attributes' => array('class' => array('form-control')),
    ];

    $form['institute'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Institution/Organisation/Company/School (Full name)'),
      '#required' => TRUE,
      '#description' => $this->t('Please enter your institution/organisation/company/school (Full name).'),
      '#size' => 50,
      '#maxlength' => 255,
       '#attributes' => array('class' => array('form-control')),
    ];

    $form['email_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email (Official, if available)'),
      '#size' => 50,
      '#maxlength' => 255,
      '#description' => $this->t('Please enter your email ID'),
      '#required' => TRUE,
       '#attributes' => array('class' => array('form-control')),
    ];

    $form['designation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Designation'),
      '#required' => TRUE,
      '#description' => $this->t('Please enter your designation.'),
      '#size' => 50,
      '#maxlength' => 255,
       '#attributes' => array('class' => array('form-control')),
    ];

    // Hidden fields for URL ID and release date
    $form['installer_id'] = [
      '#type' => 'hidden',
      '#value' => $url_id,
    ];

    $form['release_date'] = [
      '#type' => 'hidden',
      '#value' => $release_date,
    ];

    // Submit button
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['skip'] = array(
  '#type' => 'item',
  '#markup' => Link::fromTextAndUrl(t('Skip'), Url::fromUri('internal:/skip/download-installer/' . $release_date . '/' . $url_id))->toString(),
);

    // Note and Forum Information
    $form['download_note'] = [
      '#type' => 'item',
      '#markup' => $this->t('<span style="color:red">Note:</span> The installer will start downloading after clicking Submit/Skip. Click <a href="https://osdag.fossee.in/resources/downloads">here</a> to go back to the Downloads page.'),
    ];

    $form['forum_text'] = [
      '#type' => 'item',
      '#markup' => $this->t('<center><h6>Did you know?</h6></center>
        <p style="font-size:14px;">You can ask queries related to Osdag and take part in discussions by connecting with the Osdag team and other users through the FOSSEE Forums. Click <a href="https://osdag.fossee.in/forum" target="_blank">here</a> to get started.</p>'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get submitted form values
    $v = $form_state->getValues();
    $name_title = $v['name_title'];
    $first_name = trim($v['first_name']);
    $last_name = trim($v['last_name']);
    $institute = trim($v['institute']);
    $email_id = trim($v['email_id']);
    $designation = trim($v['designation']);
    $url_id = $v['installer_id'];
    $release_date = $v['release_date'];

    // Fetch installer details
    $query = \Drupal::database()->select('installer_files', 'i')
      ->fields('i')
      ->condition('release_date', $release_date)
      ->condition('id', $url_id)
      ->range(0, 1);
    $installer_data = $query->execute()->fetchObject();

    // Insert form data into installer_response table
    $result = \Drupal::database()->insert('installer_response')
      ->fields([
        'name_title' => $name_title,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'institute' => $institute,
        'email_id' => $email_id,
        'designation' => $designation,
      ])
      ->execute();

    // File download handling
    $root_path = installer_directory_path();
    $filepath = $installer_data->installer_path;
    $file_full_path = $root_path . '/' . $filepath;
    //var_dump($root_path . $filepath);die;
    if (file_exists($root_path . '/' . $filepath)) {
      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
      header('Content-Length: ' . filesize($root_path . '/' . $filepath));
      header('Expires: 0');
      header('Pragma: no-cache');
      ob_clean();
      flush();
      readfile($root_path . '/' . $filepath);
      ob_clean();
      flush();
      //exit;
    } 
        $form_state->setRedirectUrl('https://osdag.fossee.in');

  }
}
