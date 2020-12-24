<?php
// $Id$
function installer_response_settings_form($form, $form_state)
{
	
	$form['extensions']['installer_file'] = array(
		'#type' => 'textfield',
		'#title' => t('Allowed source file extensions for the installer file'),
		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
		'#size' => 50,
		'#maxlength' => 255,
		'#required' => TRUE,
		'#default_value' => variable_get('installer_file_extensions', '')
	);
	$form['extensions']['instructions_file'] = array(
		'#type' => 'textfield',
		'#title' => t('Allowed file extensions for the installer instructions'),
		'#description' => t('A comma separated list WITHOUT SPACE of dependency file extensions that are permitted to be uploaded on the server'),
		'#size' => 50,
		'#maxlength' => 255,
		'#required' => TRUE,
		'#default_value' => variable_get('instructions_file_extensions', '')
	);
	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Submit')
	);
	return $form;
}
function installer_response_settings_form_validate($form, &$form_state)
{
	return;
}
function installer_response_settings_form_submit($form, &$form_state)
{
	variable_set('installer_file_extensions', $form_state['values']['installer_file']);
	variable_set('instructions_file_extensions', $form_state['values']['instructions_file']);
	drupal_set_message(t('Settings updated'), 'status');
}
