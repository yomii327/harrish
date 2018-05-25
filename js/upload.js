function multiUploader(config){ 
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
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
		var fileNameArr = data[0].name.split(".");
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
			tempArr = processFileName.split("-");
			var lastEle = tempArr.pop();
			var fileNameTitle = tempArr.join("-").trim();
			revisionNo = lastEle;
		}
		if(FileExt == 'pdf'){
			$('#drawingNumber').val(fileNameTitle);
			$('#drawingRevision').val(revisionNo);
		}
		//var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

		if(((deadlockPDF || deadlockDWG || deadlockIMG) && !attrPhotoCon) || (attrPhotoCon && deadlockIMG)){
			if(this.items.length > 0){
				var html = "";		
				var uId = "";
				if(this.items.length < 2){
					for(var i = 0; i<this.items.length; i++){
						uId = this.items[i].name._unique();
						var sampleIcon = '<img src="images/image-24.png" />';
						if(FileExt == 'pdf')
							var sampleIcon = '<img src="images/pdf-24.png" />';

						var errorClass = "";
						if(typeof this.items[i] != undefined){
							if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
								jAlert('Plese select .pdf file');		
								return;
							}
							html += '<div class="dfiles'+errorClass+'" rel="'+uId+'"><h3>'+sampleIcon+this.items[i].name+'</h3><div id="'+uId+'" class="progress" style="display:none;"><img src="images/ajax-loader.gif" /></div></div>';	
						}else{
						}
					}
				}else{
					jAlert('Please select only one file ');
				}
				if(FileExt == 'pdf'){
					//if(deadlockPDF){
						deadlockPDF = false;
						$("#innerDiv1").append(html);
					//}
				}else{//Check revision number condtion here
					//if(deadlockIMG){
						deadlockIMG = false;
						$("#innerDiv3").append(html);	
					//}
				}
			}
		}else{
			jAlert('You can\'t add more files');
		}
	}

	multiUploader.prototype._read = function(evt){
		attrPhotoCon = false;
		if(!$('#drawingattribute2').val()){}else{
			if($('#drawingattribute2').val()[0] == "Photos" || $('#drawingattribute2').val() == "Photos")
				attrPhotoCon = true;
		}
		
		if(evt.target.files){
			var fileNameArr = evt.target.files[0].name.split(".");
			var FileExt = fileNameArr.pop();
			var processFileName = fileNameArr.join(".");
			FileExt = FileExt.toLowerCase();
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
				tempArr = processFileName.split("-");
				var lastEle = tempArr.pop();
				var fileNameTitle = tempArr.join("-").trim();
				revisionNo = lastEle;
			}
			if(attrPhotoCon) revisionNo = "";
		
			if(attrPhotoCon){
				self._preview(evt.target.files);
				self.all.push(evt.target.files);
			}else{
				var extList = ["pdf"];
				if(FileExt == 'pdf' && deadlockPDF === true){
					revision4check = revisionNo;
					self._preview(evt.target.files);
					self.all.push(evt.target.files);
				}else if(deadlockIMG === true && (extList.indexOf(FileExt) == -1)){//Check revision number condtion here
					try{
						if(revisionNo == revision4check){
							self._preview(evt.target.files);
							self.all.push(evt.target.files);
						}else{
							jAlert('Plese select same revision number files');
						}
					}catch(err){
						jAlert('Plese select pdf file first');
					}
				}
			}
		} else 
			console.log("Failed file reading");
	}
	
	multiUploader.prototype._validate = function(format, fileName){
		var fileNameArr = fileName.split(".");
		var FileExt = fileNameArr.pop();
		FileExt = FileExt.toLowerCase();
		if(FileExt == 'pdf'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	

		}
	}
	
	multiUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
//		self._preview(e.dataTransfer.files);
//		self.all.push(e.dataTransfer.files);
		attrPhotoCon = false;
		if(!$('#drawingattribute2').val()){}else{
			if($('#drawingattribute2').val()[0] == "Photos" || $('#drawingattribute2').val() == "Photos")
				attrPhotoCon = true;
		}
		
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
			tempArr = processFileName.split("-");
			var lastEle = tempArr.pop();
			var fileNameTitle = tempArr.join("-").trim();
			revisionNo = lastEle;
		}
		if(attrPhotoCon) revisionNo = "";
		
		if(attrPhotoCon){
			self._preview(e.dataTransfer.files);
			self.all.push(e.dataTransfer.files);
		}else{
			var extList = ["pdf", "dwg"];

			if(FileExt == 'pdf' && deadlockPDF === true){
				revision4check = revisionNo;
				self._preview(e.dataTransfer.files);
				self.all.push(e.dataTransfer.files);
			}else if(FileExt == 'dwg' && deadlockDWG === true){//Check revision number condtion here
				try{
					if(revisionNo == revision4check){
						self._preview(e.dataTransfer.files);
						self.all.push(e.dataTransfer.files);
					}else{
						jAlert('Plese select same revision number files');
					}
				}catch(err){
					jAlert('Plese select pdf file first');
				}
			}else if(deadlockIMG === true && (extList.indexOf(FileExt) == -1)){//Check revision number condtion here
				try{
					if(revisionNo == revision4check){
						self._preview(e.dataTransfer.files);
						self.all.push(e.dataTransfer.files);
					}else{
						jAlert('Plese select same revision number files');
					}
				}catch(err){
					jAlert('Plese select pdf file first');
				}
			}
		}
	}
	
	var pdfID = "";
	var pdfRevID = "";
	
	multiUploader.prototype._uploader = function(file, f){
		var string ='';

		var customFormData = $('#'+this.config.form).serialize();
		//if((typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0 ) || (skeepUpload == 1)){
		if((typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0 )){
			var data = new FormData();
			var ids = file[f].name._unique();
			data.append('file',file[f]);
			data.append('index',ids);
			if(pdfID != "") data.append('pdfID', pdfID);
			if(pdfRevID != "") data.append('pdfRevID', pdfRevID);
			
			if(!attrPhotoCon){}else{ data.append('attrPhotoCon', "Y"); }
			
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
						if(attrPhotoCon && jsonResult.isPhotoStatus == "Y"){
							closePopup(300);
							RefreshTable();
						}else{
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
									RefreshTable();
								}	
							}else{
								if(jsonResult.requestComplete)
									var requestComplete = jsonResult.requestComplete;
								if(requestComplete == 'dwg'){	
									if (self.all[2]) {
										var file = self.all[2];
										self._uploader(file, f);
									}else{
										$('#outPutResultPara').text(jsonResult.msg);	
										$('#outPutResult').show('fast');	
										closePopup(300);
										RefreshTable();
									}
								}else{
									$('#outPutResultPara').text(jsonResult.msg);	
									$('#outPutResult').show('fast');	
									closePopup(300);
									RefreshTable();
								}
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
		if(this.all.length > 0){
			var file = this.all[0];
			this._uploader(file, 0);
//			for(var k=0; k<this.all.length; k++){}
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
