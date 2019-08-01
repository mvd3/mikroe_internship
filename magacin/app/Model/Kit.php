<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('Validation', 'Utility');
App::uses('CakeTime', 'Utility');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
App::import('Vendor', 'PDF', array('file' => 'tcpdf/pdf.php'));
/**
 * Kit Model
 *
 */
class Kit extends AppModel {

	private $statuses = array(
		1 => 'draft',
		2 => 'for sale',
		3 => 'phase out',
		4 => 'obsolete',
		5 => 'nrnd'
	);

	public $belongsTo = array(
				'Item' => array(
					'className' => 'Item',
					'foreignKey' => 'item',
					'dependent' => true
				)
		);
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'pid' => array(
			'checkIfCorrect' => array(
				'rule' => array('check_pid', 'id'),
				'message' => 'Enter correct PID!',
			),
		),
		'hts_number' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_status', 'kit_status'),
				'message' => array('Enter HTS number!')
			)
		),
		'tax_group' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_status', 'kit_status'),
				'message' => array('Enter tax group!')
			)
		),
		'eccn' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_eccn', 'kit_status'),
				'message' => array('Enter ECCN!')
			)
		),
		'release_date' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_status', 'kit_status'),
				'message' => array('Enter release date!')
			)
		),
		'for_distributors' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'hide_kid_content' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			)
		),
		'kit_status' => array(
			'validValue' => array(
 			 'rule' => array('inList', array(1, 2, 3, 4, 5)),
 			 'message' => array('Enter a valid status!')
 		 )
		)
	);

	/*
		Checks if the status requires pid and if the submited pid is valid
	*/
	public function check_pid($check, $query_field) {
		$id = Hash::get($this->data[$this->alias], $query_field);
		$pid = current($check);
		if ($this->checkPIDAndID($pid, $id)) {
			return true;
		}
		if (Validation::notBlank($pid)) {
				return $this->checkPID($pid);
		}
		return true;
	}

	/*
		Checks if the status requires the given field
	*/
		public function check_status($check, $query_field) {
			$value = Hash::get($this->data[$this->alias], $query_field);
			$statusesRev = array_flip($this->statuses);
			if($value == $statusesRev['for sale'] || $value == $statusesRev['phase out'] || $value == $statusesRev['obsolete'])
					{
							return Validation::notBlank(current($check));
					}
					return true;
		}

		/*
			Checks if the status requires eccn and the correct length
		*/
		public function check_eccn($check, $query_field) {
			$value = Hash::get($this->data[$this->alias], $query_field);
			$statusesRev = array_flip($this->statuses);
			if($value == $statusesRev['for sale'] || $value == $statusesRev['phase out'] || $value == $statusesRev['obsolete'])
					{
							return (Validation::notBlank(current($check)) & strlen(current($check))==5);
					}
					return true;
		}

	public function prepareStatus() {
		return $this->statuses;
	}

	/*
		Checks if the given pid already exists.
		Returns false in the case of existence.
	*/
		private function checkPID($pid) {
			$models = array('Product', 'Good', 'Kit', 'ServiceProduct');
			foreach ($models as $model) {
					$md = ClassRegistry::init($model);
					$cur = $md->find(
						'all', array(
						'fields' => 'COUNT(pid) as \'pid\'',
						'conditions' => array($model . '.pid' => $pid)
						)
					);
					if ($cur[0][0]['pid']>0) {
						return false;
					}
			}
			return true;
		}

		/*
			Check if the given id matches the given pid
			Returns true if it's a match, else false
		*/
			private function checkPIDAndID($pid, $id) {
				$models = array('Product', 'Good', 'Kit', 'ServiceProduct');
				foreach ($models as $model) {
						$md = ClassRegistry::init($model);
						$cond = array(
							'AND' => array(
								array($model . '.pid' => $pid),
								array($model . '.id' => $id)
							)
						);
						$cur = $md->find(
							'all', array(
							'fields' => 'COUNT(pid) as \'pid\'',
							'conditions' => $cond
							)
						);
						if ($cur[0][0]['pid']>0) {
							return true;
						}
				}
				return false;
			}

		/*
			Generates the next available pid
			Loads all the pids, then counts from 1 untill it finds a pid that is not used.
		*/
		public function generateNextPID() {
			$models = array('Product', 'Good', 'Kit', 'ServiceProduct');
			$pids = array();
			$counter = array();
			$lim = array();
			$nextPID = 1;
			foreach ($models as $model) {
					$md = ClassRegistry::init($model);
					$cur = $md->find(
						'all', array(
						'fields' => 'pid',
						'order' => array($model . '.pid')
						)
					);
					$cnt = 0;
					$counter[$model] = 0;
					if ($cur==null) {
						$lim[$model] = 0;
						continue;
					}
					foreach ($cur as $c) {
						$pids[$model][$cnt] = $c[$model]['pid'];
						$cnt++;
					}
					$lim[$model] = count($pids[$model]);
			}
			while (true) {
				$found = true;
				foreach ($models as $model) {
					while ($counter[$model]<$lim[$model]) {
						if ($nextPID>$pids[$model][$counter[$model]]) {
							$counter[$model]++;
						} else if ($nextPID==$pids[$model][$counter[$model]]) {
							$nextPID++;
							$found = false;
						} else {
							break;
						}
					}
				}
				if ($found) {
					break;
				}
			}
			return $nextPID;
		}

	public function prepareTypes($current = null) {
		$types = ClassRegistry::init('ItemType');
	 $cond = null;
	 if ($current!=null) {
		 $cond = array(
			 'AND' => array(
				 array('ItemType.class' => 'kit'),
				 'OR' => array(
					 array('ItemType.active' => true),
					 array('ItemType.id' => $current)
				 )
			 )
		 );
	 } else {
		 $cond = array(
			 'AND' => array(
				 array('ItemType.class' => 'kit'),
				 array('ItemType.active' => true)
			 )
		 );
	 }
		$data = $types->find('all', array('conditions' => $cond));
		$ret = array();
		foreach($data as $d) {
			$ret[$d['ItemType']['id']] = $d['ItemType']['name'];
		}
		return $ret;
	}

	public function prepareMeasurementUnits($current = null) {
		$unit = ClassRegistry::init('MeasurementUnit');
		$cond = null;
		if ($current!=null) {
			$cond = array(
				'OR' => array(
					array('MeasurementUnit.active' => true),
					array('MeasurementUnit.id' => $current)
				)
			);
		} else {
			$cond = array('MeasurementUnit.active' => true);
		}
		 $data = $unit->find('all', array('conditions' => $cond));
		 $ret = array();
		 foreach($data as $d) {
			 $ret[$d['MeasurementUnit']['id']] = $d['MeasurementUnit']['name'];
		 }
		 return $ret;
	 }

	 public function getCompleteData($id) {
			$data = $this->find(
			 'first', array(
				 'conditions' => array(
					 'Kit.id' => $id
				 )
			 )
		 );
			return $data;
		}

		public function createExcel($data) {
			$col = 'A';
			$row = 1;
			$objPHPExcel = new PHPExcel();
			$types = ClassRegistry::init('ItemType');
			$units = ClassRegistry::init('MeasurementUnit');

			$objPHPExcel->getProperties()->setCreator("Nikola Bubic")
													 ->setLastModifiedBy("Nikola Bubic")
													 ->setTitle("Kits")
													 ->setSubject("Kits")
													 ->setDescription("List of kits in all the warehouses.")
													 ->setKeywords("office PHPExcel php kits")
													 ->setCategory("kits");

			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($col++ . $row, 'code')
					->setCellValue($col++ . $row, 'name')
					->setCellValue($col++ . $row, 'description')
					->setCellValue($col++ . $row, 'weight')
					->setCellValue($col++ . $row, 'measurement_unit')
					->setCellValue($col++ . $row, 'item_type')
					->setCellValue($col++ . $row, 'pid')
					->setCellValue($col++ . $row, 'hts_number')
					->setCellValue($col++ . $row, 'tax_group')
					->setCellValue($col++ . $row, 'eccn')
					->setCellValue($col++ . $row, 'kit_release_date')
					->setCellValue($col++ . $row, 'for_distributors')
					->setCellValue($col++ . $row, 'hide_kid_content')
					->setCellValue($col++ . $row, 'kit_status')
					->setCellValue($col++ . $row, 'created')
					->setCellValue($col++ . $row, 'modified');
			$objPHPExcel->getActiveSheet()->getStyle("$row:$row")->getFont()->setBold( true );

			foreach ($data as $d) {
				$col = 'A';
				$row++;
				$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($col++ . $row, $d['Item']['code'])
						->setCellValue($col++ . $row, $d['Item']['name'])
						->setCellValue($col++ . $row, $d['Item']['description'])
						->setCellValue($col++ . $row, $d['Item']['weight'])
						->setCellValue($col++ . $row, $units->getCompleteData($d['Item']['measurement_unit'])['MeasurementUnit']['name'])
						->setCellValue($col++ . $row, $types->getCompleteData($d['Item']['item_type'])['ItemType']['name'])
						->setCellValue($col++ . $row, $d['Kit']['pid'])
						->setCellValue($col++ . $row, $d['Kit']['hts_number'])
						->setCellValue($col++ . $row, $d['Kit']['tax_group'])
						->setCellValue($col++ . $row, $d['Kit']['eccn'])
						->setCellValue($col++ . $row, $d['Kit']['kit_release_date'])
						->setCellValue($col++ . $row, $d['Kit']['for_distributors'])
						->setCellValue($col++ . $row, $d['Kit']['hide_kid_content'])
						->setCellValue($col++ . $row, $d['Kit']['kit_status'])
						->setCellValue($col++ . $row, $d['Kit']['created'])
						->setCellValue($col++ . $row, $d['Kit']['modified']);
			}

			$objPHPExcel->getActiveSheet()->setTitle('kits');

			return $objPHPExcel;
		}

		/*
			Loads data from excel file into database
		*/
			public function loadFromExcel($filePath) {
				//Global data
				$objPHPExcel = PHPExcel_IOFactory::load($filePath);
				$dataFields = array();
				$rawData = array();
				$returnMessage = ['Success' => array(), 'Error' => array()];
				//Prepare other models
				$item = ClassRegistry::init('Item');
				$itemType = ClassRegistry::init('ItemType');
				$unit = ClassRegistry::init('MeasurementUnit');
				//Iterate each worksheet
				foreach($objPHPExcel->getWorksheetIterator() as $worksheet) {
					//Get size of the table in the worksheet
					$highestRow         = $worksheet->getHighestRow();
					$highestColumn      = $worksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn)-2; //Skip created and modified columns (last 2)
					if ($highestRow>5000) {
						$highestRow = 5000;
					}
					if ($highestColumnIndex>14) {
						$highestColumnIndex = 14;
					}
					//Check if there is any data
					if ($highestRow<=1) {
						return 'There is no data in this file!';
					}
					//Prepare to iterate through data
					$currentRow = 1;
					$currentColumn = 0;
					//Get all the field names
					while($currentColumn<$highestColumnIndex) {
						$value = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
						$dataFields[$currentColumn++] = $value;
					}
					$currentRow++;
					$cnt = 6; //Number of fields in items table
					while($currentRow<=$highestRow) {
						$currentColumn = 0; //Restart column indicator
						//Add Data
						while ($currentColumn<$highestColumnIndex) {
							if ($cnt-->0) {
								$rawData[$currentRow-2]['Item'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue(); // To items
							} else {
								// $rawData[$currentRow-2]['Kit'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue(); // To Material
								if(PHPExcel_Shared_Date::isDateTime($worksheet->getCellByColumnAndRow($currentColumn, $currentRow))) {
										$rawData[$currentRow-2]['Kit'][$dataFields[$currentColumn]] = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue()));
								} else {
									$rawData[$currentRow-2]['Kit'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
								}
							}
							$currentColumn++;
						}
						$cnt = 6;
						$currentRow++; //Go to next row
					}
					//The magic happens here
					$currentRow = 0;
					foreach ($rawData as $data) {
						if($data['Item']['name']==null) {
							break;
						}
						if ($data['Item']['measurement_unit']==null || $data['Item']['item_type']==null || $data['Kit']['kit_status']==null) {
							continue;
						}
						$this->create();
						$data['Item']['measurement_unit'] = $unit->getByName($data['Item']['measurement_unit'])['MeasurementUnit']['id'];
						$data['Item']['item_type'] = $itemType->getByName($data['Item']['item_type'])['ItemType']['id'];
						if ($item->checkCodeExistence($data['Item']['code']) || $data['Item']['code']==null) {
							$data['Item']['code'] = $item->generateCode($data['Item']['item_type']);
						}
						$data['Kit']['kit_status'] = array_flip($this->statuses)[$data['Kit']['kit_status']];
						if ($data['Kit']['for_distributors']>0) {
							$data['Kit']['for_distributors'] = true;
						} else {
							$data['Kit']['for_distributors'] = false;
						}
						if ($data['Kit']['hide_kid_content']>0) {
							$data['Kit']['hide_kid_content'] = true;
						} else {
							$data['Kit']['hide_kid_content'] = false;
						}
						//Save
						if ($this->saveAssociated($data, array('validate' => true, 'deep' => true))) {
							array_push($returnMessage['Success'], "Row $currentRow has been succesfully added.\n");
						} else {
							array_push($returnMessage['Error'], "Error while uploading row $currentRow, check the data!\n");
						}
						$currentRow++; //Go to next row
					}
				}
				return $returnMessage;
			}

			public function createPDF() {
				//Create file
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				//Set meta data
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('Nikola Bubic');
				$pdf->SetTitle('Kits');
				$pdf->SetSubject('List of kits');
				$pdf->SetKeywords('TCPDF, PDF, kit');
				// remove default header/footer
				$pdf->setPrintFooter(false);
				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				// set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				// set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				// add a page
				$pdf->AddPage('L');
				//Styling the table
				$pdf->SetFillColor(0, 0, 255);
				$pdf->SetTextColor(255);
				$pdf->SetDrawColor(0, 0, 128);
				$pdf->SetLineWidth(0.3);
				$pdf->SetFont('', 'B', 10);
				//Column width
				$w = array(17, 135, 15, 15, 15, 15, 20, 10, 20);
				// column titles
				$header = array('Code', 'Name', 'PID', 'HTS', 'Tax', 'ECCN', 'Release', 'Dist', 'Status');
				//Header
				$num_headers = count($header);
						for($i = 0; $i < $num_headers; ++$i) {
								$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
						}
				$pdf->Ln();
				//Load data from database
				$rawData = $this->find('all', array(
						'fields' => array(
							'Item.code', 'Item.name', 'pid', 'hts_number', 'tax_group', 'eccn', 'kit_release_date', 'for_distributors', 'kit_status'
						)
					)
				);
				//Style for data
				$pdf->SetFillColor(224, 235, 255);
				$pdf->SetTextColor(0);
				$pdf->SetFont('', '', 7);
				$fill = 0;
				//Display data
				foreach($rawData as $data) {
					$fd = 'No';
					if ($data['Kit']['for_distributors']) {
						$fd = 'Yes';
					}
					$pdf->Cell($w[0], 6, $data['Item']['code'], 'LR', 0, 'L', $fill);
					$pdf->Cell($w[1], 6, $data['Item']['name'], 'LR', 0, 'L', $fill);
					$pdf->Cell($w[2], 6, $data['Kit']['pid'], 'LR', 0, 'R', $fill);
					$pdf->Cell($w[3], 6, $data['Kit']['hts_number'], 'LR', 0, 'R', $fill);
					$pdf->Cell($w[4], 6, $data['Kit']['tax_group'], 'LR', 0, 'R', $fill);
					$pdf->Cell($w[5], 6, $data['Kit']['eccn'], 'LR', 0, 'R', $fill);
					$pdf->Cell($w[6], 6, CakeTime::format('d-m-Y', $data['Kit']['kit_release_date']), 'LR', 0, 'R', $fill);
					$pdf->Cell($w[7], 6, $fd, 'LR', 0, 'R', $fill);
					$pdf->Cell($w[8], 6, $data['Kit']['kit_status'], 'LR', 0, 'R', $fill);
					$pdf->Ln();
					$fill=!$fill;
				}
				$pdf->Cell(array_sum($w), 0, '', 'T');
				return $pdf;
			}
}
