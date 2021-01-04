<?php
function view_responses_all()
{
	$output = "";
	$query = db_select('installer_response');
	$query->fields('installer_response');
	$result = $query->execute();
	while ($row = $result->fetchObject())
		{
			$preference_rows[] = array(
				$row->id,
				$row->name_title."."." ".$row->first_name." ".$row->last_name,
				//$row->first_name,
				//$row->last_name,
				$row->institute,
				$row->email_id,
				$row->designation,
				//$row->insatller_type
			);
		} //$row = $result->fetchObject()
		$preference_header = array(
			'S.No',
			'Name',
			'Institute',
			'Email_ID',
			'Designation',
			//'Insatller Type'
		);
		$output .= theme('table', array(
			'header' => $preference_header,
			'rows' => $preference_rows
		));

	return $output;
}
