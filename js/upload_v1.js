function multiUploader(config){ 
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	var finalizeValue = {}; 
	var locatioArray = {};
	multiUploader.prototype._init = function(){
		if (window.File && 
			window.FileReader && 
			window.FileList && 
			window.Blob ||
			typeof FileReader !== undefined) {		
			 var inputId = $("#"+this.config.form).find("input[type='file']").eq(0).attr("id"); 
			 document.getElementById(inputId).addEventListener("change", this._read, false);
			 document.getElementById(this.config.dragArea).addEventListener("dragover", function(e){ e.stopPropagation(); e.preventDefault(); }, false);
			 document.getElementById(this.config.dragArea).addEventListener("drop", this._dropFiles, false);
			 document.getElementById(this.config.form).addEventListener("submit", this._submit, false);
		} else
			console.log("Browser supports failed");
	}
	
	multiUploader.prototype._submit = function(e){ 
		e.stopPropagation(); e.preventDefault();
		var byPassCon = 0;

		var validationFlagVal = parseInt($('#validationFlag').val());
		switch(validationFlagVal){
			case 1:
				if($('#'+self.config.dragArea+' div:nth-child(2)').text().trim() == 'PDF file upload here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
				if($('#pdfStatusDyna').val().trim() == ''){$('#errorPdfStatus').show('slow');return false;}else{$('#errorPdfStatus').hide('slow');}
			break;
			case 2:
				if($('#'+self.config.dragArea+' div:nth-child(2)').text().trim() == 'PDF file upload here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
				if($('#drawingTitle').val().trim() == ''){$('#errorDrawingTitle').show('slow');return false;}else{$('#errorDrawingTitle').hide('slow');}
				if($('#drawingNumber').val().trim() == ''){$('#errorDrawingNumber').show('slow');return false;}else{$('#errorDrawingNumber').hide('slow');}
				if($('#drawingTitle').val().trim() == $('#drawingNumber').val().trim()){$('#errorDrawingTitle1').show('slow');return false;}else{$('#errorDrawingTitle1').hide('slow');}
				if($('#pdfStatusDyna').val().trim() == ''){$('#errorPdfStatus').show('slow');return false;}else{$('#errorPdfStatus').hide('slow');}
			break;

			default:
				console.log('In Defaulst Case');
			break;
		}
		self._startUpload();	
	}
	
	multiUploader.prototype._preview = function(data){
		this.items = data;
		if(data[0].size > 0){
			var fileNameArr = data[0].name.split(".");
			var FileExt = fileNameArr.pop();
		
			var processFileName = fileNameArr.join(".");
			var revisionNo = '';
			if(processFileName.indexOf("[") !== -1){
				if(processFileName.indexOf("]") !== -1){
					tempArr = processFileName.split("[");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("[").trim();
					var revisionNoArr = lastEle.split("]");
					revisionNo = revisionNoArr[0];
				}
			}
			if(processFileName.indexOf("(") !== -1){
				if(processFileName.indexOf(")") !== -1){
					tempArr = processFileName.split("(");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("(").trim();
					var revisionNoArr = lastEle.split(")");
					revisionNo = revisionNoArr[0];
				}
			}
			if(processFileName.indexOf("{") !== -1){
				if(processFileName.indexOf("}") !== -1){
					tempArr = processFileName.split("{");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("{").trim();
					var revisionNoArr = lastEle.split("}");
					revisionNo = revisionNoArr[0];
				}
			}
			if(revisionNo == ""){
				if(processFileName.indexOf("_") !== -1){
					tempArr = processFileName.split("_");
					var lastEle = tempArr.pop();
					var documentTitle = tempArr.pop();
					var fileNameTitle = tempArr.join("_").trim();
					revisionNo = lastEle;	
				}else{
					tempArr = processFileName.split("-");
					var lastEle = tempArr.pop();
					var documentTitle = tempArr.pop();
					var fileNameTitle = tempArr.join("-").trim();
					revisionNo = lastEle;							
				}
			}
			$('#drawingNumber').val(fileNameTitle);
			$('#drawingRevision').val(revisionNo);
			$('#drawingTitle').val(documentTitle);
			if(deadlockPDF){
				var html = "";		
				var uId = "";
				if(this.items.length < 2){
					console.log('hie shadfsowoe');
					for(var i = 0; i<this.items.length; i++){
						uId = this.items[i].name._unique();
						var sampleIcon = '<img src="images/image-24.png" />';
						if(FileExt == 'dwg'  || FileExt == 'DWG')
							var sampleIcon = '<img src="images/dwg-24.png" />';
						if(FileExt == 'cad'  || FileExt == 'CAD')
							var sampleIcon = '<img src="images/cad-24.png" />';
						if(FileExt == 'pdf'  || FileExt == 'PDF')
							var sampleIcon = '<img src="images/pdf-24.png" />';
						if(FileExt == 'xls'  || FileExt == 'XLS')
							var sampleIcon = '<img src="images/xls_icon.png" />';
						if(FileExt == 'xlsx' || FileExt == 'XLSX')
							var sampleIcon = '<img src="images/xlsx_icon.png" />';
						if(FileExt == 'doc'  || FileExt == 'DOC')
							var sampleIcon = '<img src="images/doc_icon.png" />';
						if(FileExt == 'docx' || FileExt == 'DOCX')
							var sampleIcon = '<img src="images/docx_icon.png" />';

						var errorClass = "";
						if(typeof this.items[i] != undefined){
							if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
								jAlert('Plese select .pdf/ .dwg/ .cad file');		
								return;
							}
							html += '<div class="dfiles'+errorClass+'" rel="'+uId+'"><h3>'+sampleIcon+this.items[i].name+'</h3><div id="'+uId+'" class="progress" style="display:none;"><img src="images/ajax-loader.gif" /></div> <input style="height:15px;" type="hidden" name="" id="checkFlag" value="0"></div>';	
						}else{
							jAlert('Wera sdofw');
						}
					}
				}else{
					jAlert('Please select only one file ');
				}
				deadlockPDF = false;
				$("#innerDiv1").append(html);
			}else{
				jAlert('You can\'t add more files');
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}

	multiUploader.prototype._read = function(evt){
		if(evt.target.files[0].size > 0){
			
			if(evt.target.files){
				var fileNameArr = evt.target.files[0].name.split(".");
				var FileExt = fileNameArr.pop();
				var processFileName = fileNameArr.join(".");
				
				var revisionNo = '';
				if(processFileName.indexOf("[") !== -1){
					if(processFileName.indexOf("]") !== -1){
						tempArr = processFileName.split("[");
						var lastEle = tempArr.pop();
						var fileNameTitle = tempArr.join("[").trim();
						var revisionNoArr = lastEle.split("]");
						revisionNo = revisionNoArr[0];
					}
				}
				if(processFileName.indexOf("(") !== -1){
					if(processFileName.indexOf(")") !== -1){
						tempArr = processFileName.split("(");
						var lastEle = tempArr.pop();
						var fileNameTitle = tempArr.join("(").trim();
						var revisionNoArr = lastEle.split(")");
						revisionNo = revisionNoArr[0];
					}
				}
				if(processFileName.indexOf("{") !== -1){
					if(processFileName.indexOf("}") !== -1){
						tempArr = processFileName.split("{");
						var lastEle = tempArr.pop();
						var fileNameTitle = tempArr.join("{").trim();
						var revisionNoArr = lastEle.split("}");
						revisionNo = revisionNoArr[0];
					}
				}
				if(revisionNo == ""){
					if(processFileName.indexOf("_") !== -1){
						tempArr = processFileName.split("_");
						var lastEle = tempArr.pop();
						var documentTitle = tempArr.pop();
						var fileNameTitle = tempArr.join("_").trim();
						revisionNo = lastEle;	
					}else{
						tempArr = processFileName.split("-");
						var lastEle = tempArr.pop();
						var documentTitle = tempArr.pop();
						var fileNameTitle = tempArr.join("-").trim();
						revisionNo = lastEle;							
					}
				}
				
		
				var extList = ["pdf", "dwg", "cad"];
				var FileExt = FileExt.toLowerCase(); 
				
				if(deadlockPDF === true){
					if(FileExt == 'pdf'  || FileExt == 'PDF'){
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'dwg'  || FileExt == 'DWG'){//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'cad'  || FileExt == 'CAD') {//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'xls'  || FileExt == 'XLS') {//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'xlsx'  || FileExt == 'XLSX') {//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'doc'  || FileExt == 'DOC') {//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else if(FileExt == 'docx'  || FileExt == 'DOCX') {//Check revision number condtion here
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else{
						jAlert('Please select either .pdf or .dwg or .cad or .xls or xlsx or .doc or .docx file only');
					}	
				}else{
					jAlert('You can\'t add more files');
				}
			} else {
				console.log("Failed file reading");
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}
	
	multiUploader.prototype._validate = function(format, fileName){
		var fileNameArr = fileName.split(".");
		var FileExt = fileNameArr.pop();
		
		if(FileExt == 'dwg'  || FileExt == 'DWG'){
			$('#drawingattribute3').val('DWG');
			return 1;
		}else if(FileExt == 'cad'  || FileExt == 'CAD'){
			$('#drawingattribute3').val('CAD');
			return 1;
		}else if(FileExt == 'xls'  || FileExt == 'XLS'){
			$('#drawingattribute3').val('XLS');
			return 1;
		}else if(FileExt == 'xlsx'  || FileExt == 'XLSX'){
			$('#drawingattribute3').val('XLSX');
			return 1;
		}else if(FileExt == 'doc'  || FileExt == 'DOC'){
			$('#drawingattribute3').val('DOC');
			return 1;
		}else if(FileExt == 'docx'  || FileExt == 'DOCX'){
			$('#drawingattribute3').val('DOCX');
			return 1;
		}else{
			$('#drawingattribute3').val('PDF');
			var arr = this.config.support.split(",");
			console.log(arr.indexOf(format));
			return arr.indexOf(format);	
		}
	}
	
	multiUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
//		self._preview(e.dataTransfer.files);
//		self.all.push(e.dataTransfer.files);

		if(e.dataTransfer.files[0].size > 0){
			
			var fileNameArr = e.dataTransfer.files[0].name.split(".");
			var FileExt = fileNameArr.pop();
			FileExt = FileExt.toLowerCase();
			var processFileName = fileNameArr.join(".");
			var revisionNo = '';
			if(processFileName.indexOf("[") !== -1){
				if(processFileName.indexOf("]") !== -1){
					tempArr = processFileName.split("[");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("[").trim();
					var revisionNoArr = lastEle.split("]");
					revisionNo = revisionNoArr[0];
				}
			}
			if(processFileName.indexOf("(") !== -1){
				if(processFileName.indexOf(")") !== -1){
					tempArr = processFileName.split("(");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("(").trim();
					var revisionNoArr = lastEle.split(")");
					revisionNo = revisionNoArr[0];
				}
			}
			if(processFileName.indexOf("{") !== -1){
				if(processFileName.indexOf("}") !== -1){
					tempArr = processFileName.split("{");
					var lastEle = tempArr.pop();
					var fileNameTitle = tempArr.join("{").trim();
					var revisionNoArr = lastEle.split("}");
					revisionNo = revisionNoArr[0];
				}
			}
			if(revisionNo == ""){
				if(processFileName.indexOf("_") !== -1){
					tempArr = processFileName.split("_");
					var lastEle = tempArr.pop();
					var documentTitle = tempArr.pop();
					var fileNameTitle = tempArr.join("_").trim();
					revisionNo = lastEle;	
				}else{
					tempArr = processFileName.split("-");
					var lastEle = tempArr.pop();
					var documentTitle = tempArr.pop();
					var fileNameTitle = tempArr.join("-").trim();
					revisionNo = lastEle;							
				}
			}

			var extList = ["pdf", "dwg", "cad"];
			
			if(deadlockPDF === true){
				if(FileExt == 'pdf'  || FileExt == 'PDF'){
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'dwg'  || FileExt == 'DWG'){//Check revision number condtion here
					console.log('In drope');
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'cad'  || FileExt == 'CAD'){//Check revision number condtion here
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'xls'  || FileExt == 'XLS') {//Check revision number condtion here
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'xlsx'  || FileExt == 'XLSX') {//Check revision number condtion here
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'doc'  || FileExt == 'DOC') {//Check revision number condtion here
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else if(FileExt == 'docx'  || FileExt == 'DOCX') {//Check revision number condtion here
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else{
					jAlert('Please select either .pdf or .dwg or .cad or .xls or .xlsx or .doc or .docx file only');
				}	
			}else{
				jAlert('You can\'t add more files');
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}
	
	var pdfID = "";
	var pdfRevID = "";
	
	multiUploader.prototype._uploader = function(file, f){
		var string ='';

		var customFormData = $('#'+this.config.form).serialize();
		if((typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0 ) || (skeepUpload == 1)){
			var data = new FormData();
			var ids = file[f].name._unique();
			data.append('file',file[f]);
			data.append('index',ids);
			if(pdfID != "") data.append('pdfID', pdfID);
			if(pdfRevID != "") data.append('pdfRevID', pdfRevID);
			
			var drawingattribute2Data = '';
			$.each($('#'+this.config.form).serializeArray(), function() {             
				data.append(this.name, this.value);
				if(this.name == 'drawingattribute2'){
					if(drawingattribute2Data == ""){
						drawingattribute2Data = this.value;
					}else{
						drawingattribute2Data += '###'+this.value;
					}
					data.append(this.name, drawingattribute2Data);
				}
    	    });
			showProgress();	
			$.ajax({
				type:"POST",
				url:this.config.uploadUrl,
				data:data,
				cache: false,
				contentType: false,
				processData: false,
				success:function(rponse){
					hideProgress();
					var jsonResult = JSON.parse(rponse);	
					if(jsonResult.status){
						if(jsonResult.dataArr) {
							pdfID = jsonResult.dataArr.insertedDwgID;
							pdfRevID = jsonResult.dataArr.insertedDwgRevID;
							if (self.all[1]) {
								var file = self.all[1];
								self._uploader(file, f);
							}else{
								$('#outPutResultPara').text(jsonResult.msg);	
								$('#outPutResult').show('fast');	
								closePopup(300);
								//	RefreshTable();
								searchDrawPdf($("#drawingattribute1").val());
							}	
						}
					}else{
						jAlert('Error, Please Try Again ');	
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
	}
	
	multiUploader.prototype._startUpload = function(){
		console.log(this.all);
		console.log(self.all);
		if(this.all.length > 0){
			groupFilNameArr2 = new Array();
			groupFileNumberArr2 = new Array();
			var datas = new FormData();
			var xkName = 0;
			var xkNumber = 0;
			
			groupFileNumberArr2.push("'"+$("#drawingTitle").val()+"'");	
			groupFilNameArr2.push("'"+$("#drawingNumber").val()+"'");
			datas.append('groupFilNameArr', groupFileNumberArr2);
			datas.append('groupFileNumberArr', groupFilNameArr2);

			console.log(groupFilNameArr2);
			console.log(groupFileNumberArr2);
			//console.log(groupFileNumberArr2);
			$.ajax({
				type:"POST",
				url:self.config.ajaxCheckUploadUrl+'&type=1',
				data:datas,
				cache: false,
				async: false,
				contentType: false,
				processData: false,
				success:function(rponse){ 
					var obj = jQuery.parseJSON(rponse);
					$.each(obj.data, function(processKey, processVal){
						finalizeValue[processKey] = new Array(processVal.id, processVal.title, processVal.revision, processVal.newRevision);
						locatioArray[processVal.title] = processVal.chapter_name_tree;
					if(processVal.id){
						$('#checkFlag').val(1);
						//hideProgress();
					}

					});
					msgs = obj.dataArr;
					//$('#spinner').html('');
					//$('#spinner').css("color","");
					//$('#spinner').hide();
					//hideProgress();
				}
			});
			var locMessage = '';
			if($("#checkFlag").val()==1){	
				var supersedingmsg = 'A document already exists with this filename. These documents will be superseded.';
				//var r = jConfirm(''+groupFilNameArr2+' will be superseded in this upload? And '+msgs+' would be added as revision as this number is already exists. These will be location for each file:<br><br>'+locMessage, null, function(r) {
				var r = jConfirm(supersedingmsg, null, function(r) {
					if(r==true){
						showProgress();
						/*if(testFxArr.length > 0){
						for(var k=0; k<testFxArr.length; k++){
						var file = testFxArr[k];
						self._uploader(file,0,k);
						}
						testFxArr = Array();  
						tempArrKey = 0;			
						}*/
						for(var k=0; k<self.all.length; k++){
							var file = self.all[k];
							self._uploader(file, k);
						}
						//for closeing popup
					}else{
						//set checkflag condition 0 if user cancels superseded condition
						$("#checkFlag").val(0);
					}
				}); 
			}else{
			for(var k=0; k<self.all.length; k++){
					var file = self.all[k];
					self._uploader(file, k);
				}
			}
		}
	}
	
	String.prototype._unique = function(){
		return this.replace(/[a-zA-Z]/g, function(c){
     	   return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
    	});
	}
	
	this._init();
}

function initMultiUploader(config){
	new multiUploader(config);
}
