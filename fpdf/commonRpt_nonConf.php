<?php
require('mc_table_v1.php');

class PDF extends PDF_MC_Table
{
	function Header(){
		if($this->PageNo()!=1){
			//$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
			$this->ln();	
			$this->SetFont('times', 'B', 10);
			$header = array("ID","Location","Quality Checklist","Description","Inspected By","Date Raised","Raised By","Issued To","Fix By Date","Status","Image 1","Image 2");
			$w = $this->header_width();
			$this->SetWidths($w);
			$best_height = 17;
			$this->row($header, $best_height);
		}
	}
		
	function Footer(){
		$this->SetY(-15);
		$this->SetFont('times','B',10);
		$this->Cell(0, 10, 'DefectID - Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
		
		$this->SetX(240);
		$this->Cell(0, 10, date("d/m/Y h:i:A"), 0, 0, 'R');
	}
		
	function header_width($width=array(24,26,24,30,24,22,22,26,21,18,26,26)){
		return $width;
	}	

# Page header
	function HaderWithImage($title='Title',$img='logo.png',$imgW=30, $imgH=''){
		
	// Logo
	$this->Image($img,10,6,$imgW,$imgH);
	if(!empty($title)){	// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Move to the right
		$this->Cell(80);
	
		// Title
		$this->Cell(30,10,$title,1,0,'C');
	}
	// Line break
	$this->Ln(20);
		
	}
	

	function addColorTable($data, $color, $lastWidth=12){
		//Calculate the height of the row
		//kamal , new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+2; // we have added 2 for a margin
				$nb=max($nb,$this->NbLines($this->widths[$i],$image_height));
			}else{
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])); 
			}
		}
		$h = 7 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			if($i==(count($data)-1)){
				$w = $lastWidth;
			}else{
				$w = $this->widths[$i];
			}
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
				$this->Rect($x,$y,$tmp[1],$tmp[2],'DF');
			}else{
				if(isset($color[$i]) && !empty($color[$i])){
					$c = explode(", ",$color[$i]);
					$c[0] = isset($c[0])?$c[0]:204;
					$c[1] = isset($c[1])?$c[1]:255;
					$c[2] = isset($c[2])?$c[2]:255;						
					//print_r($c);die;
					$this->SetFillColor($c[0], $c[1], $c[2]);
					//Draw the border
					$this->Rect($x,$y,$w,$h,'DF');
				}else{
					//Draw the border
					$this->Rect($x,$y,$w,$h);
				}
				$this->MultiCell($w,7,$data[$i],0,$a);
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function addTable($data, $lastWidth=12, $border = 1){
		//Calculate the height of the row
		//kamal , new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+2; // we have added 2 for a margin
			}else{
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])); 
			}
		}
		$h = 7 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			if($i==(count($data)-1)){
				$w = $lastWidth;
			}else{
				$w = $this->widths[$i];
			}
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
			}else{
				if($border == 1){
					$this->Rect($x,$y,$w,$h);
				}
				$this->MultiCell($w,7,$data[$i],0,$a);
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	# create custom table
	function createTable($data, $lastWidth=12, $boldColPos='-1', $border = 1){
		//Calculate the height of the row
		//kamal , new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+2; // we have added 2 for a margin
			}else{
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])); 
			}
		}
		$h = 7 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			if($i==(count($data)-1)){
				$w = $lastWidth;
			}else{
				$w = $this->widths[$i];
			}
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			if($boldColPos==$i){
				$this->SetFont('Arial','B');
				$boldColPos = '-1';
			}elseif($boldColPos=="-1"){
				$this->SetFont('Arial','');
			}
			
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
			}else{
				if($border == 1){
					$this->Rect($x,$y,$w,$h);
				}
				$this->MultiCell($w,7,$data[$i],0,$a);
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	# create custom table
	function createTableWithFormating($data, $lastWidth=12, $boldFontData = array(), $borderData = array(), $bgColorData = array(), $textColorData = array(), $newHeight = 7, $align = 'L'){
		if($newHeight == 0 || $newHeight == ""){ $newHeight = 7;}
		$defaultBorderArr = array('F', 'FD', 'DF', 'RB');
		//Calculate the height of the row
		//kamal , new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+2; // we have added 2 for a margin
			}else{
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])); 
			}
		}
		$h = $newHeight * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			if($i==(count($data)-1)){
				$w = $lastWidth;
			}else{
				$w = $this->widths[$i];
			}
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				
				//Apply background color
				$this->SetFillColor(255, 255, 255);
				if(isset($bgColorData[$i]) && !empty($bgColorData[$i])){
					$c = explode(", ",$bgColorData[$i]);
					$c[0] = isset($c[0])?$c[0]:204;
					$c[1] = isset($c[1])?$c[1]:255;
					$c[2] = isset($c[2])?$c[2]:255;						
					$this->SetFillColor($c[0], $c[1], $c[2]);
				}
				//Apply border
				if(isset($borderData[$i]) && !empty($borderData[$i]) && in_array($borderData[$i], $defaultBorderArr)){
				      /* D or empty string: draw. This is the default value.
				        F: fill
				      	DF or FD: draw and fill 
						RB: Reduce border width & height
						*/
					//Draw the border
					if($borderData[$i]=="RB"){
						$this->Rect($x,$y,($w-0.2),($h-0.2),$borderData[$i]);
					}else{
						$this->Rect($x,$y,($w),($h),$borderData[$i]);
					}
				}else{
					//Draw the border
					//$this->Rect($x,$y,$w,$h);
				}	
				
				# Add image
				if(isset($borderData[$i]) && !empty($borderData[$i]) && !in_array($borderData[$i], $defaultBorderArr)){
					$this->MultiCell($w, $h, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2],'PNG'),$borderData[$i],'C');
				}else{
					$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2],'PNG'),0,'C');
				}
				
			}else{
				//Apply font setting
				$this->SetFont('Arial','');
				if(isset($boldFontData[$i]) && !empty($boldFontData[$i])){
					$this->SetFont('Arial',$boldFontData[$i]);
				}
					
				//Apply Text color
				$this->SetTextColor(0, 0, 0);
				if(isset($textColorData[$i]) && !empty($textColorData[$i])){
					$tc = explode(", ",$textColorData[$i]);
					$tc[0] = isset($tc[0])?$tc[0]:0;
					$tc[1] = isset($tc[1])?$tc[1]:0;
					$tc[2] = isset($tc[2])?$tc[2]:0;						
					$this->SetTextColor($tc[0], $tc[1], $tc[2]);
				}	

				//Apply background color
				$this->SetFillColor(255, 255, 255);
				if(isset($bgColorData[$i]) && !empty($bgColorData[$i])){
					$c = explode(", ",$bgColorData[$i]);
					$c[0] = isset($c[0])?$c[0]:204;
					$c[1] = isset($c[1])?$c[1]:255;
					$c[2] = isset($c[2])?$c[2]:255;						
					$this->SetFillColor($c[0], $c[1], $c[2]);
				}	
				
				//Apply border
				if(isset($borderData[$i]) && !empty($borderData[$i]) && in_array($borderData[$i], $defaultBorderArr)){
				      /* D or empty string: draw. This is the default value.
				        F: fill
				      	DF or FD: draw and fill */
					//Draw the border
					$this->Rect($x,$y,$w,$h,$borderData[$i]);
				}else{
					//Draw the border
					$this->Rect($x,$y,$w,$h);
				}
				$this->MultiCell($w,$newHeight,$data[$i],0, $align);
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
}

?>
