<?php
require('fpdf.php');

class PDF_MC_Table extends FPDF{
	var $widths;
	var $aligns;
	
	function SetWidths($w){
		//Set the array of column widths
		$this->widths=$w;
	}
	
	function SetAligns($a){
		//Set the array of column alignments
		$this->aligns=$a;
	}
	
	function Row($data){
		//Calculate the height of the row
		//new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+7; // we have added 2 for a margin
			}else{
				if (strpos($data[$i], "STATUS##") > -1){
					$tmp = explode("~~", $data[$i]);
					$value = $tmp[1];
					$tmp = explode("##", $tmp[0]);
					$status = $tmp[1];
					$nb=max($nb,$this->NbLines($this->widths[$i],$value));
				}else{
					$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				}
			}
		}
		$h = 5 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->Rect($x,$y,$w,$h);
				$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
			}else if (strpos($data[$i], "STATUS##") > -1){
				$tmp = explode("~~", $data[$i]);
				$value = $tmp[1];
				$tmp = explode("##", $tmp[0]);
				$status = $tmp[1];
				if (strpos($data[$i], "H##") > -1)
					$status = $tmp[2];
				if($status == 'In progress'){
					$this->SetFillColor(255, 165, 00);	
				}
				else if($status == 'Behind'){
					$this->SetFillColor(255, 00, 00);
				}
				else if($status == 'Complete'){
					$this->SetFillColor(00, 128, 00);
				}
				else if($status == 'Signed off'){
					$this->SetFillColor(00, 00, 255);	
				}else{
					if (strpos($data[$i], "H##") > -1)
						$this->SetFillColor(200, 200, 200);
					else
						$this->SetFillColor(255, 255, 255);
				}
				$this->Rect($x,$y,$w,$h,"F");
				$this->MultiCell($w,5,$value,1,'C', true);
			}
			else{
				if (strpos($data[$i], "H##") > -1 )
				    {
					$tmp = explode("##", $data[$i]);
					$this->SetFillColor(200, 200, 200);
					$this->Rect($x,$y,$w,$h,"F");
					$this->MultiCell($w,5,$tmp[1],0,$a, true);
				    }
				    else{
					$this->Rect($x,$y,$w,$h);
					$this->MultiCell($w,5,$data[$i],0,$a);
				    }
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function Row_QA_Wall_Chart($data){
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
		$h = 5 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			//Print the text
			if (strpos($data[$i], "FILL##") > -1){
				$tmp = explode("~~", $data[$i]);
				$value = $tmp[1];
				$tmp = explode("##", $tmp[0]);
				$fill = explode(',', $tmp[1]);
#echo $fill.'<br />';
				$this->SetFillColor($fill[0], $fill[1], $fill[2]);	
				$he = max($h, 5);
				$this->Cell($w, $he, $value, 1, 0, 'L', true);
#				$this->Rect($x,$y,$w,$h,"F");
			}else{
				if (strpos($data[$i], "H##") > -1 ){
					$tmp = explode("##", $data[$i]);
#					$this->cell($w,$h,'',1);
					$this->cell(5,$h,'','');
					$this->SetFillColor(255, 255, 255);
					$this->Rect($x,$y,$w,$h);
					$this->MultiCell($w-5,5,$tmp[1],0,$a);
				}else{
					$this->SetFillColor(255, 255, 255);
					$this->Rect($x,$y,$w,$h);
					$this->MultiCell($w,5,$data[$i],0,$a);
				}
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function Row_Wall_Chart_Summary($data, $bHeight){
		//Calculate the height of the row
		//new code to fix 
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
				if (strpos($data[$i], "STATUS##") > -1){
					$tmp = explode("~~", $data[$i]);
					$value = $tmp[1];
					$tmp = explode("##", $tmp[0]);
					$status = $tmp[1];
					$nb=max($nb,$this->NbLines($this->widths[$i],$value));
				}else{
					$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				}
			}
		}
		$h = $bHeight * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->Rect($x,$y,$w,$h);
				$this->MultiCell($w, $bHeight, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
			}else if (strpos($data[$i], "STATUS##") > -1){
				$tmp = explode("~~", $data[$i]);
				$value = $tmp[1];
				$tmp = explode("##", $tmp[0]);
				$status = $tmp[1];
				if (strpos($data[$i], "H##") > -1)
					$status = $tmp[2];
				if($status == 'In progress'){
					$this->SetFillColor(255, 165, 00);	
				}
				else if($status == 'Behind'){
					$this->SetFillColor(255, 00, 00);
				}
				else if($status == 'Complete'){
					$this->SetFillColor(00, 128, 00);
				}
				else if($status == 'Signed off'){
					$this->SetFillColor(00, 00, 255);	
				}else{
					if (strpos($data[$i], "H##") > -1)
						$this->SetFillColor(200, 200, 200);
					else
						$this->SetFillColor(255, 255, 255);
				}
				$this->Rect($x,$y,$w,$h,"F");
				$this->MultiCell($w,$bHeight,$value,1,'C', true);
			}
			else{
				if (strpos($data[$i], "H##") > -1 )
				    {
					$tmp = explode("##", $data[$i]);
					$this->SetFillColor(200, 200, 200);
					$this->Rect($x,$y,$w,$h,"F");
					$this->MultiCell($w,$bHeight,$tmp[1],1,$a, true);
				    }
				    else{
					$this->Rect($x,$y,$w,$h);
					$this->MultiCell($w,$bHeight,$data[$i],1,$a);
				    }
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function CheckPageBreak($h){
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt){
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)	{
			$c=$s[$i];
			if($c=="\n"){
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax){
				if($sep==-1){
					if($i==$j)
						$i++;
				}else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}else
				$i++;
		}
		return $nl;
	}
}?>
