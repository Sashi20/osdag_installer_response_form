<?php
function skip_download_osdag_installer()
{
  $url_id = arg(3);
  $release_date = arg(2);
  $root_path = installer_directory_path();$query = db_select('installer_files');
  $query->fields('installer_files');
  $query->condition('release_date', $release_date);
  $query->condition('id', $url_id);
  $query->range(0, 1);
  $installer_q = $query->execute();
  $installer_data = $installer_q->fetchObject();
  $filepath = $installer_data->installer_path;
  header('Content-Type: application/zip');
      header('Content-disposition: attachment; filename="' . $filepath . '"');
      header('Content-Length: ' . filesize($root_path . $filepath));
      header("Content-Transfer-Encoding: Binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      //ob_end_flush();
      ob_clean();
      flush();
      //readfile($filename);
      if(@readfile($root_path . $filepath)){
        exit;
        drupal_goto('');
        drupal_set_message(t('The form has been submitted.'),'status');
        
      }
      else{
        drupal_set_message(t('Could not download file'), 'error');
      }
}


function download_osdag_instructions_file()
{
  $url_id = arg(2);
  $release_date = arg(1);
  $root_path = installer_directory_path();$query = db_select('installer_files');
  $query->fields('installer_files');
  $query->condition('release_date', $release_date);
  $query->condition('id', $url_id);
  $query->range(0, 1);
  $installer_q = $query->execute();
  $installer_data = $installer_q->fetchObject();
  $filepath = $installer_data->instruction_file_path;
  //var_dump($filepath);die;
      header('Content-Type: application/txt');
      header('Content-disposition: attachment; filename="' . $filepath . '"');
      header('Content-Length: ' . filesize($root_path . $filepath));
      header("Content-Transfer-Encoding: Binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      //ob_end_flush();
      ob_clean();
      flush();
      //readfile($filename);
      if(@readfile($root_path . $filepath)){
        exit;
        drupal_goto('');
        drupal_set_message(t('The form has been submitted.'),'status');
        
      }
      else{
        drupal_set_message(t('Could not download file'), 'error');
      }
}