<?php
session_start();
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
include("./includes/functions.php");
$db = new DB_Class();
# Include excel library.

$rows = array();
$typelist = array("Client","Consultant","Contractor","Sub Contractor","General Suppliers","Other");

$select = "SELECT I.company_name as name, I.issue_to_name as company, I.issue_to_phone as phone, I.issue_to_email as email, I.activity as activity, I.user_title as title FROM `inspection_issue_to` as I JOIN  `master_issue_to_contact` as M ON M.`master_issue_id` = `I`.`master_issue_id` WHERE `I`.`project_id` = ".$_SESSION['idp']." AND I.is_deleted = 0 AND M.is_deleted = 0 GROUP BY I.issue_to_id ORDER BY I.activity asc";



$getcontactlist = mysql_query($select);
while ($row = mysql_fetch_array( $getcontactlist ) ){
	if($row['activity'] == 'Client'){
		$rows[$row['activity']][] = $row;
	}elseif($row['activity'] == 'Consultant'){
		$rows[$row['activity']][] = $row;
	}elseif($row['activity'] == 'Contractor'){
		$rows[$row['activity']][] = $row;
	}elseif($row['activity'] == 'Sub Contractor'){
		$rows[$row['activity']][] = $row;
	}elseif($row['activity'] == 'General Suppliers'){
		$rows[$row['activity']][] = $row;
	}elseif($row['activity'] == 'Other'){
		$rows[$row['activity']][] = $row;
	}else{
		$rows['NotInList'][] = $row;	
	}	
	$getlist[] = $row['activity'];
}
//echo '<pre>';print_r($getlist);die;
	$activitylistu = array_filter(array_unique($getlist));

	$arg = implode(",",$activitylistu);
	$activitylist = explode(',', $arg);
//echo '<pre>';print_r($activitylist);die;

$getProjectname = mysql_query("SELECT project_name FROM user_projects WHERE project_id = ".$_SESSION['idp']);
$name = mysql_fetch_array( $getProjectname );

#print_r($name[0]);die;

# Include excel library.
require_once(dirname(__FILE__).'/PHPExcel_1.8.0/Classes/PHPExcel.php');
#Start : Excel sheet
	// Basic styles
    $titleWithBorderStyle = array(
        'font' => array(
            'bold' => true,
        ),
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
        ),
    );

    $borderStyle = array(
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
    //Header cell text font, and font color
    $styleArray2 = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => ''),
            'size' => 12,
            'name' => 'Verdana'
    ));
    // Table alignment center
    $tableAlignCC = array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'rotation'   => 0,
		'wrap'	=> TRUE
	);
	$tableAlignLL = array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
	);

	    //Heading section
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
	    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	    
	    #Set set title.
	    $objPHPExcel->getActiveSheet()->setTitle('DefectID HarrisHMC');
	    
	    # Create a new worksheet, after the default sheet
		//$objPHPExcel->createSheet();

		# Add some data to the second sheet, resembling some different data types
		

		$logoImage = "images/logo.png";//imagecreatefromjpeg("../images/logo.png");
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath($logoImage);
		$objDrawing->setHeight(20);
		$objDrawing->setWidth(100);
		$objDrawing->setCoordinates('A1');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);

		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->mergeCells('B1:E1');
		$sheet->getRowDimension(1)->setRowHeight(30); //set column A height
		$sheet->SetCellValue('B1', '');	

		# Set Title here.
		$sheet->SetCellValue('A2', '');	
		$sheet->getStyle('B2')->applyFromArray($styleArray2);
		$sheet->getStyle('B2')->getAlignment()->applyFromArray($tableAlignLL);
		$sheet->mergeCells('B2:E2');
		$sheet->getRowDimension(1)->setRowHeight(30); //set column A height
		$sheet->SetCellValue('B2', ucfirst($name[0]));		

		
		# Set header row here
		$rowNo = 3;
		$styleArray = array('font'  => array('bold' => true,'color' => array('rgb' => '000000'),'size'  => 10,));
		$header_name=array('', 'Company','Name', 'Phone', 'Email');
		$cell = array("A","B","C","D","E","F","G","H","I","J");
		$header_width =  array(18,20,15,15,30);
		$count=count($header_name);
		//$header_height = 30;
		for($i=0;$i<$count;$i++) {
			//$sheet->getRowDimension($rowNo)->setRowHeight($header_height);
			$sheet->getColumnDimension($cell[$i])->setWidth($header_width[$i]);
			$sheet->getStyle($cell[$i].($rowNo))->applyFromArray($titleWithBorderStyle);
			$sheet->getStyle($cell[$i].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);
			$sheet->getStyle($cell[$i].($rowNo))->applyFromArray(array_merge($titleWithBorderStyle, $styleArray));
			$sheet->setCellValue($cell[$i].($rowNo), $header_name[$i]);
		}
		
		$rowNo = 4;
		$styleArray = array('font'  => array('bold' => false,'color' => array('rgb' => '000000'),'size'  => 8,));
		
		if(isset($getcontactlist) && !empty($getcontactlist)) {

			for ($t=0; $t < count($activitylist); $t++) { 
				# Set Title here.
				if(!empty($activitylist[$t])){
					$sheet->SetCellValue($cell[0].($rowNo), $activitylist[$t]);
					$sheet->getStyle($cell[0].($rowNo).':'.$cell[4].($rowNo))
						    ->applyFromArray(
						        array(
						            'fill' => array(
						                'type' => PHPExcel_Style_Fill::FILL_SOLID,
						                'color' => array('rgb' => 'b2b2b2')
						            )
						        )
						    );
					
					$rowNo = $rowNo + 1;	
					$counter = 1;	
				}
				foreach ($rows[$activitylist[$t]] as $row) {	

					$sheet->getStyle($cell[0].($rowNo))->applyFromArray($borderStyle);
					$sheet->setCellValue($cell[0].($rowNo), $row['title']);
					$sheet->getStyle($cell[0].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);

					$sheet->getStyle($cell[1].($rowNo))->applyFromArray($borderStyle);
					$sheet->setCellValue($cell[1].($rowNo), $row['company']);
					$sheet->getStyle($cell[1].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);

					$sheet->getStyle($cell[2].($rowNo))->applyFromArray($borderStyle);
					$sheet->setCellValue($cell[2].($rowNo), $row['name']);
					$sheet->getStyle($cell[2].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);

					// Set the value as a number formatted with leading zeroes
					$sheet->getStyle($cell[3].($rowNo))->applyFromArray($borderStyle);
					$sheet->setCellValue($cell[3].($rowNo), $row['phone']);
					$sheet->getStyle($cell[3].($rowNo))->getNumberFormat()->setFormatCode('0000');
					$sheet->getStyle($cell[3].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);
					
					$sheet->getStyle($cell[4].($rowNo))->applyFromArray($borderStyle);
					$sheet->setCellValue($cell[4].($rowNo), $row['email']);
					$sheet->getCell($cell[4].($rowNo))->getHyperlink()->setUrl('mailto:'.$row['email']);
					$sheet->getStyle($cell[4].($rowNo))->getAlignment()->applyFromArray($tableAlignLL);

					$counter++;
					$rowNo = $rowNo + 1;
				}
			}						
		}else{
			$sheet->getStyle('A4')->applyFromArray($styleArray2);
			$sheet->getStyle('A4')->getAlignment()->applyFromArray($tableAlignCC);
			$sheet->mergeCells('A4:E4');
			$sheet->getRowDimension(1)->setRowHeight(30); //set column A height
			$sheet->SetCellValue('A4', 'No records found');
		}
		
		//die;
		#Set some data here.
	    
	# Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $objWriter->setIncludeCharts(TRUE);
	    $output_file = "report_csv/contactlist_" . time() . ".xlsx";
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objWriter->save($output_file);
	    // Download excel file.
	    header("location: $output_file"); die;
	#End : Excel sheet

