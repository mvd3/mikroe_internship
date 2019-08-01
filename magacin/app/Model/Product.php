<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('Validation', 'Utility');
App::uses('CakeTime', 'Utility');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
App::import('Vendor', 'PDF', array('file' => 'tcpdf/pdf.php'));
/**
 * Product Model
 *
 */
class Product extends AppModel {

	private $statuses = array(
		1 => 'development',
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
				'rule' => array('check_status', 'product_status'),
				'message' => array('Enter HTS number!')
			)
		),
		'tax_group' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_status', 'product_status'),
				'message' => array('Enter tax group!')
			)
		),
		'product_eccn' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_eccn', 'product_status'),
				'message' => array('Enter ECCN!')
			)
		),
		'product_release_date' => array(
			'checkIfNeeded' => array(
				'rule' => array('check_status', 'product_status'),
				'message' => array('Enter release date!')
			)
		),
		'product_status' => array(
			'validValue' => array(
 			 'rule' => array('inList', array(1, 2, 3, 4, 5)),
 			 'message' => array('Enter a valid status!')
 		 )
		),
		'service_production' => array(
			'boolean' => array(
				'rule' => array('boolean')
			),
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

/*
	Returns all types for this model
*/
	public function prepareTypes($current = null) {
		$types = ClassRegistry::init('ItemType');
	 $cond = null;
	 if ($current!=null) {
		 $cond = array(
			 'AND' => array(
				 array('ItemType.class' => 'product'),
				 'OR' => array(
					 array('ItemType.active' => true),
					 array('ItemType.id' => $current)
				 )
			 )
		 );
	 } else {
		 $cond = array(
			 'AND' => array(
				 array('ItemType.class' => 'product'),
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

/*
	Returns all active measurement units
*/
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

/*
	Returns all associated data on model
*/
	 public function getCompleteData($id) {
 		$data = $this->find(
 		 'first', array(
 			 'conditions' => array(
 				 'Product.id' => $id
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
												 ->setTitle("Products")
												 ->setSubject("Products")
												 ->setDescription("List of products in all the warehouses.")
												 ->setKeywords("office PHPExcel php product")
												 ->setCategory("product");

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
				->setCellValue($col++ . $row, 'product_eccn')
				->setCellValue($col++ . $row, 'product_release_date')
				->setCellValue($col++ . $row, 'for_distributors')
				->setCellValue($col++ . $row, 'product_status')
				->setCellValue($col++ . $row, 'service_production')
				->setCellValue($col++ . $row, 'project')
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
					->setCellValue($col++ . $row, $d['Product']['pid'])
					->setCellValue($col++ . $row, $d['Product']['hts_number'])
					->setCellValue($col++ . $row, $d['Product']['tax_group'])
					->setCellValue($col++ . $row, $d['Product']['product_eccn'])
					->setCellValue($col++ . $row, $d['Product']['product_release_date'])
					->setCellValue($col++ . $row, $d['Product']['for_distributors'])
					->setCellValue($col++ . $row, $d['Product']['product_status'])
					->setCellValue($col++ . $row, $d['Product']['service_production'])
					->setCellValue($col++ . $row, $d['Product']['project'])
					->setCellValue($col++ . $row, $d['Product']['created'])
					->setCellValue($col++ . $row, $d['Product']['modified']);
		}

		$objPHPExcel->getActiveSheet()->setTitle('products');

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
				if ($highestColumnIndex>15) {
					$highestColumnIndex = 15;
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
							// $rawData[$currentRow-2]['Product'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue(); // To Product
							if(PHPExcel_Shared_Date::isDateTime($worksheet->getCellByColumnAndRow($currentColumn, $currentRow))) {
									$rawData[$currentRow-2]['Product'][$dataFields[$currentColumn]] = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue()));
							} else {
								$rawData[$currentRow-2]['Product'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
							}
						}
						$currentColumn++;
					}
					$cnt = 6;
					$currentRow++; //Go to next row
				}
				//The magic happens here
				$currentRow = 1;
				foreach ($rawData as $data) {
					if($data['Item']['name']==null) {
						break;
					}
					if ($data['Item']['measurement_unit']==null || $data['Item']['item_type']==null || $data['Product']['product_status']==null) {
						continue;
					}
					$this->create();
					$data['Item']['measurement_unit'] = $unit->getByName($data['Item']['measurement_unit'])['MeasurementUnit']['id'];
					$data['Item']['item_type'] = $itemType->getByName($data['Item']['item_type'])['ItemType']['id'];
					if ($item->checkCodeExistence($data['Item']['code']) || $data['Item']['code']==null) {
						$data['Item']['code'] = $item->generateCode($data['Item']['item_type']);
					}
					if (!$this->checkPID($data['Product']['pid'])) {
						$data['Product']['pid'] = null;
					}
					$data['Product']['product_status'] = array_flip($this->statuses)[$data['Product']['product_status']];
					if ($data['Product']['for_distributors']>0) {
						$data['Product']['for_distributors'] = true;
					} else {
						$data['Product']['for_distributors'] = false;
					}
					if ($data['Product']['service_production']>0) {
						$data['Product']['service_production'] = true;
					} else {
						$data['Product']['service_production'] = false;
					}
					//Save
					if ($this->saveAssociated($data, array('validate' => true, 'deep' => true))) {
						array_push($returnMessage['Success'], "Row $currentRow has been succesfully added.\n");
					} else {
						var_dump($data);
						// debug($this->validationErrors);exit;
						// array_push($returnMessage['Error'], "Error while uploading row $currentRow, check the data!\n");
					}
					$currentRow++; //Go to next row
				}
				return $returnMessage;
			}
		}

		public function createPDF() {
			//Create file
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			//Set meta data
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Nikola Bubic');
			$pdf->SetTitle('Products');
			$pdf->SetSubject('List of products');
			$pdf->SetKeywords('TCPDF, PDF, product');
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
			$w = array(17, 110, 15, 15, 15, 15, 20, 10, 20, 10, 15);
			// column titles
			$header = array('Code', 'Name', 'PID', 'HTS', 'Tax', 'ECCN', 'Release', 'Dist', 'Status', 'SP', 'Project');
			//Header
			$num_headers = count($header);
					for($i = 0; $i < $num_headers; ++$i) {
							$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
					}
			$pdf->Ln();
			//Load data from database
			$rawData = $this->find('all', array(
					'fields' => array(
						'Item.code', 'Item.name', 'pid', 'hts_number', 'tax_group', 'product_eccn', 'product_release_date', 'for_distributors', 'product_status', 'service_production', 'project'
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
				$sp = 'No';
				$fd = 'No';
				if ($data['Product']['service_production']) {
					$sp = 'Yes';
				}
				if ($data['Product']['for_distributors']) {
					$fd = 'Yes';
				}
				$pdf->Cell($w[0], 6, $data['Item']['code'], 'LR', 0, 'L', $fill);
				$pdf->Cell($w[1], 6, $data['Item']['name'], 'LR', 0, 'L', $fill);
				$pdf->Cell($w[2], 6, $data['Product']['pid'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[3], 6, $data['Product']['hts_number'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[4], 6, $data['Product']['tax_group'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[5], 6, $data['Product']['product_eccn'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[6], 6, CakeTime::format('d-m-Y', $data['Product']['product_release_date']), 'LR', 0, 'R', $fill);
				$pdf->Cell($w[7], 6, $fd, 'LR', 0, 'R', $fill);
				$pdf->Cell($w[8], 6, $data['Product']['product_status'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[9], 6, $sp, 'LR', 0, 'R', $fill);
				$pdf->Cell($w[10], 6, $data['Product']['project'], 'LR', 0, 'R', $fill);
				$pdf->Ln();
				$fill=!$fill;
			}
			$pdf->Cell(array_sum($w), 0, '', 'T');
			return $pdf;
		}
}
