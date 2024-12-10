<?php
namespace Drupal\installer_response_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;


class InstallerController extends ControllerBase {

  /**
   * Display the installer responses table.
   */
  public function viewResponses() {
    // Call the view_responses_all function
    $content = view_responses_all();
    return $content;
  }
/**
 * Displays all responses in a table format.
 */

public function skipDownloadInstaller($release_date, $url_id) {
    // Get file data from the database
    $installer_data = $this->getInstallerData($release_date, $url_id);
    if (!$installer_data) {
      \Drupal::messenger()->addError(t('Installer file not found.'));
      return $this->redirect('<front>');
    }

    // Construct file path and send download response
    $root_path = $this->installerDirectoryPath();
    $filepath = $installer_data->installer_path;
    $file_full_path = $root_path . '/' . $filepath;

    return $this->createDownloadResponse($file_full_path, $filepath);
  }

  /**
   * Download the instructions file.
   */
  public function downloadInstructionsFile($release_date, $url_id) {
    // Get file data from the database
    $installer_data = $this->getInstallerData($release_date, $url_id);
    if (!$installer_data) {
      \Drupal::messenger()->addError(t('Instructions file not found.'));
      return $this->redirect('<front>');
    }

    // Construct file path and send download response
    $root_path = $this->installerDirectoryPath();
    $filepath = $installer_data->instruction_file_path;
    $file_full_path = $root_path . '/' . $filepath;

    return $this->createDownloadResponse($file_full_path, $filepath, 'application/txt');
  }

  /**
   * Helper function to get installer data from the database.
   */
  protected function getInstallerData($release_date, $url_id) {
    $query = \Drupal::database()->select('installer_files', 'i');
    $query->fields('i');
    $query->condition('release_date', $release_date);
    $query->condition('id', $url_id);
    $query->range(0, 1);
    $result = $query->execute();
    return $result->fetchObject();
  }

  /**
   * Helper function to get the installer directory path.
   */
  protected function installerDirectoryPath() {
    return \Drupal::service('file_system')->realpath('public://osdag/installers/');
  }

  /**
   * Create a download response for the file.
   */
  protected function createDownloadResponse($file_full_path, $filename, $mime_type = 'application/zip') {
    //var_dump($file_full_path);die;
    if (!file_exists($file_full_path)) {
      \Drupal::messenger()->addError(t('File does not exist.'));
      return $this->redirect('<front>');
    }

    $response = new StreamedResponse(function() use ($file_full_path) {
      readfile($file_full_path);
    });

    $response->headers->set('Content-Type', $mime_type);
    $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($file_full_path) . '"');
    $response->headers->set('Content-Length', filesize($file_full_path));
    $response->headers->set('Expires', '0');
    $response->headers->set('Pragma', 'no-cache');

    return $response;
  }


function installer_directory_path() {
  // Get the base path for the installation directory.
  $base_path = \Drupal::service('file_system')->realpath('public://osdag/installers/');

  // Make sure the path is valid.
  if (is_dir($base_path)) {
    return $base_path;
  }

  // If directory doesn't exist, return an empty string or handle error appropriately.
  return '';
}
}
