<?php session_start();
include("./includes/functions.php");
include('./includes/commanfunction.php');
require('./fpdf/mc_table.php');

$builder_id=$_SESSION['ww_builder_id'];
$obj = new DB_Class();
$common = new COMMAN_Class();

#Gre thread id.
$thread_id = $_POST['thread_id'];

#Get message id.
$msgIdQuery = 'SELECT message_id FROM pmb_user_message WHERE type="sent" AND thread_id = '. $thread_id .' LIMIT 0,1';
$msgIdResult = mysql_query($msgIdQuery);
$msgIdResult = mysql_fetch_object($msgIdResult);
$message_id = $msgIdResult->message_id;


#Get message data.
$msgDataQuery = 'SELECT message,project_id FROM pmb_message WHERE message_id = '. $message_id;
$msgDataResult = mysql_query($msgDataQuery);
$msgDataResult = mysql_fetch_object($msgDataResult);
$message = $msgDataResult->message;
$project_id = $msgDataResult->project_id;

#Get attachment data.
$attachmentQuery = "SELECT attach_id, name, attachment_name, is_attached_email FROM pmb_attachments WHERE message_id =". $message_id;
$attachResult = mysql_query($attachmentQuery);


if(isset($_GET['delparam'])){
	$key = $_POST['file_key'];
   unset($_SESSION[$_SESSION['idp'].'_pmbEmailfile'][$key]);
   return true;
}

/*
 * -----------------------------------
 * 	GENERATE PDF
 * ----------------------------------
 */

class PDF extends PDF_MC_Table {
    var $B;
	var $I;
	var $U;
	var $HREF;

	function PDF($orientation='P',$unit='mm',$format='A4')
	{
		//Call parent constructor
		$this->FPDF($orientation,$unit,$format);
		//Initialization
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
	}
    
    function header() {
        parent::Header();
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('times','B',10);
        $this->Cell(0, 10, "Wiseworker- Copyright Wiseworking ".date('Y'), 0, 0, 'C');
    }
    
    function messageTitle($label)
	{
		// Arial 12
		$this->SetFont('Arial','',12);
		// Background color
		$this->SetFillColor(200,220,255);
		// Title
		$this->Cell(0,6,"$label",0,1,'L',true);
		// Line break
		$this->Ln(4);
	}
    
    function messageBody($data)
	{
		// Times 12
		$this->SetFont('Times','',10);
		// Output justified text
		$this->MultiCell(0,5,$data);
		// Line break
		$this->Ln();
	}
	
	function WriteHTML($html)
	{
		$this->SetFont('Times','',10);
		//HTML parser
		$html=str_replace("\n",' ',$html);
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				//Tag
				if($e{0}=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag,$attr)
	{
		//Opening tag
		if($tag=='B' or $tag=='I' or $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF=$attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='B' or $tag=='I' or $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
	}
	
	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		$this->SetFont('',$style);
	}
}

#convert meesage to plan text.
$message = html_entity_decode($message);
//$message = strip_tags($message);
$message = str_replace('&nbsp;', ' ', ltrim($message));
$message = str_replace('\n', '', ltrim($message));
$message = str_replace('\r', '', ltrim($message));
//$message = ltrim($message);

#Create PDF object
$pdf = new PDF();

$pdf->AddPage('P', 'A4');
$pdf->messageTitle('Inbox');
$pdf->WriteHTML($message);
$pdf->Ln();
$pdf->messageTitle('Attachments:');


$i = 1;
while($row = mysql_fetch_object($attachResult)) {
	$html = '<a href="http://harrishmcdev.defectid.com/attachment/'. $row->attachment_name .'">'. $i .'-'. $row->name .'</a>';
	$pdf->WriteHTML($html);
	$pdf->Ln();
	$i++;
}


#Filename
$fileName = 'pmb_'. $project_id .'_'. $message_id. '.pdf';
$path = './attachment/'.$fileName;

#Generate output.
$pdf->Output($path, 'F');

$output = array(
	'imageName' => $fileName,
	'msg' => 'PMB Message added into attachment successfully'
);

#Add file name for email attachement.
$_SESSION[$_SESSION['idp'].'_pmbEmailfile'][] = $fileName;

echo json_encode($output);

/* Omit PHP closing tags to help avoid accidental output */
