<?php

function upload_osdag_installer_form($form, &$form_state){
	$form['#attributes'] = array(
		'enctype' => "multipart/form-data"
	);
	$form['release_date'] = array(
		'#type' => 'date_popup',
		'#title' => t('Release date of the installer'),
		'#date_label_position' => '',
		'#description' => '',
		'#default_value' => '',
		'#date_format' => 'd-m-Y',
		'#date_increment' => 0,
		'#date_year_range' => '1960: +22',
		
		'#required' => TRUE
	);
	$form['operating_system'] = array(
		'#type' => 'select',
		'#title' => t('Select Operating System'),
		'#options' => array(
			"Ubuntu" => "Ubuntu",
			"Windows" => "Windows"
			),
		'#required' => TRUE
	);
	$form['os_version'] = array(
		'#type' => 'select',
		'#title' => t('Select the version'),
		'#options' => array(
			"Ubuntu 16.04 and above - 64-bit" => "Ubuntu 16.04 and above - 64-bit",
			"Ubuntu 14.04 and above - 64-bit" => "Ubuntu 14.04 and above - 64-bit",
			"Windows 7,8 and 10 - 32-bit & 64-bit" => "Windows 7,8 and 10 - 32-bit & 64-bit"
			),
		'#required' => TRUE
	);
	$form['upload_files'] = array(
		'#type' => 'fieldset',
		'#title' => t('Upload the files'),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,
	);
	$form['upload_files']['installer_file'] = array(
		'#type' => 'textfield',
		'#title' => t('Enter the filename of the installer file along with the extension')
	);
	$form['upload_files']['instructions_file'] = array(
		'#type' => 'textfield',
		'#title' => t('Enter the filename the installer instructions file along with the extension')
	);
	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Submit')
	);

	return $form;
}

function upload_osdag_installer_form_validate($form, &$form_state)
{
	/*if (isset($_FILES['files']))
		{
			/* check for valid filename extensions 
			foreach ($_FILES['files']['name'] as $file_form_name => $file_name)
			{
				if ($file_name)
				{
					/* checking file type 
					if (strstr($file_form_name, 'installer_file'))
						$file_type = 'Z';
					else
						$file_type = 'T';
					$allowed_extensions_str = '';
					switch ($file_type)
					{
						case 'Z':
							$allowed_extensions_str = variable_get('installer_file_extensions', '');
							break;
						case 'T':
							$allowed_extensions_str = variable_get('instructions_file_extensions', '');
							break;
					} //$file_type
					$allowed_extensions = explode(',', $allowed_extensions_str);
					$fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
					$temp_extension = end($fnames);
					if (!in_array($temp_extension, $allowed_extensions))
						form_set_error($file_form_name, t('Only file with ' . $allowed_extensions_str . ' extensions can be uploaded.'));
					if ($_FILES['files']['size'][$file_form_name] <= 0)
						form_set_error($file_form_name, t('File size cannot be zero.'));
				} //$file_name
			} //$_FILES['files']['name'] as $file_form_name => $file_name*/
		
//} //isset($_FILES['files'])

}

function upload_osdag_installer_form_submit($form, &$form_state){
	$v = $form_state["values"];
	$result = "INSERT INTO {installer_files} (release_date, operating_system, os_version, installer_path, instruction_file_path) VALUES(:release_date, :operating_system, :os_version, :installer_path, :instruction_file_path)";
	$args = array(
		":release_date" => $v['release_date'],
		":operating_system" => $v['operating_system'],
		":os_version" => $v['os_version'],
		":installer_path" => $v['installer_file'],
		":instruction_file_path" => $v['instructions_file']
		);
	$installer_id = db_query($result, $args, array(
		'return' => Database::RETURN_INSERT_ID
	));
	/*$root_path = installer_directory_path();
	if (!is_dir($root_path))
		mkdir($root_path);
	foreach ($_FILES['files']['name'] as $file_form_name => $file_name)
		{
			if ($file_name)
			{
				/* checking file type 
				if (strstr($file_form_name, 'installer_file'))
						$file_type = 'Z';
					else
						$file_type = 'T';
				switch ($file_type)
					{
						case 'Z':
							if (file_exists($root_path . $_FILES['files']['name'][$file_form_name]))
							{
								// drupal_set_message(t("Error uploading file. File !filename already exists.", array('!filename' => $_FILES['files']['name'][$file_form_name])), 'error');
								unlink($root_path . $_FILES['files']['name'][$file_form_name]);
							} //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
							/* uploading file 
							if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $_FILES['files']['name'][$file_form_name]))
							{
								$query = "UPDATE {installer_files} SET installer_path = :installer_path WHERE id = :id";
								$args = array(
									":installer_path" => $_FILES['files']['name'][$file_form_name],
									":id" => $installer_id
								);
								$updateresult = db_query($query, $args);
								drupal_set_message($file_name . ' uploaded successfully.', 'status');
							} //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
							else
							{
								drupal_set_message('Error uploading file : ' . $root_path . $file_name, 'error');
							}
							break;
						case 'T':
							if (file_exists($root_path . $_FILES['files']['name'][$file_form_name]))
							{
								// drupal_set_message(t("Error uploading file. File !filename already exists.", array('!filename' => $_FILES['files']['name'][$file_form_name])), 'error');
								unlink($root_path . $_FILES['files']['name'][$file_form_name]);
							} //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
							/* uploading file 
							if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $_FILES['files']['name'][$file_form_name]))
							{
								$query = "UPDATE {installer_files} SET instruction_file_path = :instruction_file_path WHERE id = :id";
								$args = array(
									":instruction_file_path" => $_FILES['files']['name'][$file_form_name],
									":id" => $installer_id
								);
								$updateresult = db_query($query, $args);
								drupal_set_message($file_name . ' uploaded successfully.', 'status');
							} //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
							else
							{
								drupal_set_message('Error uploading file : ' . $root_path . $file_name, 'error');
							}
							break;
					}
			} //$file_name
		}*/ //$_FILES['files']['name'] as $file_form_name => $file_name
	drupal_set_message(t('Installer uploaded succesfully'), 'status');
}