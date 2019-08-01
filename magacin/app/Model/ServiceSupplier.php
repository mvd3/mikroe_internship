<?php
App::uses('AppModel', 'Model');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
App::import('Vendor', 'PDF', array('file' => 'tcpdf/pdf.php'));
/**
 * ServiceSupplier Model
 *
 */
class ServiceSupplier extends AppModel {

	private $statuses = array(
		1 => 'draft',
		2 => 'in use',
		3 => 'phase out',
		4 => 'obsolete'
	);

	private $recommendations = array(
		0 => '',
		1 => 'plantium',
		2 => 'gold',
		3 => 'silver'
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
		'item' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);


		/*
			Returns all active meausrement units and also the current unit of this model
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
		 Returns all statuses for this model
		*/
		public function prepareStatus() {
			return $this->statuses;
		}

		/*
			Return all recommendations for this model
		*/
		public function prepareRecommendation() {
			return $this->recommendations;
		}

		/*
		 Returns item types for class semi_product
		*/
		public function prepareTypes($current = null) {
			$types = ClassRegistry::init('ItemType');
		 $cond = null;
		 if ($current!=null) {
			 $cond = array(
				 'AND' => array(
					 array('ItemType.class' => 'service_supplier'),
					 'OR' => array(
						 array('ItemType.active' => true),
						 array('ItemType.id' => $current)
					 )
				 )
			 );
		 } else {
			 $cond = array(
				 'AND' => array(
					 array('ItemType.class' => 'service_supplier'),
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
		Returns all associated data on this id
		*/
		public function getCompleteData($id) {
			$data = $this->find(
			 'first', array(
				 'conditions' => array(
					 'ServiceSupplier.id' => $id
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
													 ->setTitle("Service Suppliers")
													 ->setSubject("Service Suppliers")
													 ->setDescription("List of service suppliers in all the warehouses.")
													 ->setKeywords("office PHPExcel php servicesuppliers")
													 ->setCategory("servicesuppliers");

			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($col++ . $row, 'code')
					->setCellValue($col++ . $row, 'name')
					->setCellValue($col++ . $row, 'description')
					->setCellValue($col++ . $row, 'weight')
					->setCellValue($col++ . $row, 'measurement_unit')
					->setCellValue($col++ . $row, 'item_type')
					->setCellValue($col++ . $row, 'service_status')
					->setCellValue($col++ . $row, 'service_rating')
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
						->setCellValue($col++ . $row, $d['ServiceSupplier']['service_status'])
						->setCellValue($col++ . $row, $d['ServiceSupplier']['service_rating'])
						->setCellValue($col++ . $row, $d['ServiceSupplier']['created'])
						->setCellValue($col++ . $row, $d['ServiceSupplier']['modified']);
			}

			$objPHPExcel->getActiveSheet()->setTitle('consumables');

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
					if ($highestColumnIndex>8) {
						$highestColumnIndex = 8;
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
								$rawData[$currentRow-2]['ServiceSupplier'][$dataFields[$currentColumn]] = $worksheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue(); // To Material
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
						if ($data['Item']['measurement_unit']==null || $data['Item']['item_type']==null || $data['ServiceSupplier']['service_status']==null) {
							continue;
						}
						$this->create();
						$data['Item']['measurement_unit'] = $unit->getByName($data['Item']['measurement_unit'])['MeasurementUnit']['id'];
						$data['Item']['item_type'] = $itemType->getByName($data['Item']['item_type'])['ItemType']['id'];
						if ($item->checkCodeExistence($data['Item']['code']) || $data['Item']['code']==null) {
							$data['Item']['code'] = $item->generateCode($data['Item']['item_type']);
						}
						$data['ServiceSupplier']['service_status'] = array_flip($this->statuses)[$data['ServiceSupplier']['service_status']];
						$data['ServiceSupplier']['service_rating'] = array_flip($this->recommendations)[$data['ServiceSupplier']['service_rating']];
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
				$pdf->SetTitle('Service Suppliers');
				$pdf->SetSubject('List of service suppliers');
				$pdf->SetKeywords('TCPDF, PDF, service, supplier');
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
				$pdf->AddPage();
				//Styling the table
				$pdf->SetFillColor(0, 0, 255);
		    $pdf->SetTextColor(255);
		    $pdf->SetDrawColor(0, 0, 128);
		    $pdf->SetLineWidth(0.3);
		    $pdf->SetFont('', 'B', 10);
				//Column width
				$w = array(17, 120, 20, 20);
				// column titles
				$header = array('Code', 'Name', 'Status', 'Rating');
				//Header
				$num_headers = count($header);
		        for($i = 0; $i < $num_headers; ++$i) {
		            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
		        }
				$pdf->Ln();
				//Load data from database
				$rawData = $this->find('all', array(
						'fields' => array(
							'Item.code', 'Item.name', 'service_status', 'service_rating'
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
					$pdf->Cell($w[0], 6, $data['Item']['code'], 'LR', 0, 'L', $fill);
					$pdf->Cell($w[1], 6, $data['Item']['name'], 'LR', 0, 'L', $fill);
					$pdf->Cell($w[2], 6, $data['ServiceSupplier']['service_status'], 'LR', 0, 'R', $fill);
					$pdf->Cell($w[3], 6, $data['ServiceSupplier']['service_rating'], 'LR', 0, 'R', $fill);
					$pdf->Ln();
					$fill=!$fill;
				}
				$pdf->Cell(array_sum($w), 0, '', 'T');
				return $pdf;
			}
}
