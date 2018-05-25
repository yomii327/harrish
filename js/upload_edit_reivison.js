function multiUploaderRev(config){ 
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	multiUploaderRev.prototype._init = function(){
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
	
	multiUploaderRev.prototype._submit = function(e){ 
		e.stopPropagation(); e.preventDefault();
		var byPassCon = 0;
		var validationFlagVal = parseInt($('#validationFlag').val());
		switch(validationFlagVal){
			case 3:
				if($('#'+self.config.dragArea+' div:first-child').text().trim() == 'Drop File Here' &&
				   $('#'+self.config.dragArea+' div:nth-child(2) span').text().trim() == 'PDF file upload here' && 
				   $('#'+self.config.dragArea+' div:nth-child(3) span').text().trim() == 'DWG file upload here' && 
				   $('#'+self.config.dragArea+' div:nth-child(4) span').text().trim() == 'Image file upload here'){//By pass file uploading code
					byPassCon = 1;
				}
				if($('#'+self.config.dragArea+' div:first-child').text().trim() == 'Drop File Here' &&
				   $('#'+self.config.dragArea+' div:nth-child(2)').text().trim() != 'PDF file upload here' || 
				   $('#'+self.config.dragArea+' div:nth-child(3)').text().trim() != 'DWG file upload here' || 
				   $('#'+self.config.dragArea+' div:nth-child(4)').text().trim() != 'Image file upload here'){//By pass file uploading code
					byPassCon = 0;
				}
			break;
			
			default:
				console.log('In Defaulst Case');
			break;
		}
		if(byPassCon){
			self._uploaderSkeeped();
		}else{
			self._startUpload();	
		}
	}
	
	multiUploaderRev.prototype._preview = function(data){
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
		if(deadlockPDF || deadlockDWG || deadlockIMG){
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
								sampleIcon = '<img src="images/unknown.png" />';
								errorClass =" invalid";
							}
							html += '<div class="dfiles'+errorClass+'" rel="'+uId+'"><h3>'+sampleIcon+this.items[i].name+'</h3><div id="'+uId+'" class="progress" style="display:none;"><img src="images/ajax-loader.gif" /></div></div>';	
						}else{
						}
					}
				}else{
					jAlert('Please select only one file ');
				}
				if(FileExt == 'pdf'){
					deadlockPDF = false;
					$("#innerDiv1").append(html);
				}else{//Check revision number condtion here
					deadlockIMG = false;
					$("#innerDiv3").append(html);	
				}
			}
		}else{
			jAlert('You can\'t add more files');
		}
	}

	multiUploaderRev.prototype._read = function(evt){
		if(evt.target.files){
			console.log(evt.target.files);
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
			if(FileExt == 'pdf'){
				revision4check = revisionNo;
				self._preview(evt.target.files);
				self.all.push(evt.target.files);
			}else{//Check revision number condtion here
				try{
					if(revisionNo == revision4check){
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else{
						jAlert('Plese select same revision number files');
					}
				}catch(err){
					console.log('werw');
					var revision4checkDyn = $('#drawingRevision').val();
					console.log(revision4checkDyn);
					if(revisionNo == revision4checkDyn){
						self._preview(evt.target.files);
						self.all.push(evt.target.files);
					}else{
						jAlert('Plese select same revision number files');
					}
				}
			}
		} else 
			console.log("Failed file reading");
	}
	
	multiUploaderRev.prototype._validate = function(format, fileName){
		var fileNameArr = fileName.split(".");
		var FileExt = fileNameArr.pop();
		if(FileExt == 'pdf'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	

		}
	}
	
	multiUploaderRev.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
//		self._preview(e.dataTransfer.files);
//		self.all.push(e.dataTransfer.files);
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

		if(FileExt == 'pdf'){
			revision4check = revisionNo;
			self._preview(e.dataTransfer.files);
			self.all.push(e.dataTransfer.files);
		}else{//Check revision number condtion here
			try{
				if(revisionNo == revision4check){
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else{
					jAlert('Plese select same revision number files');
				}
			}catch(err){
				var revision4checkDyn = $('#drawingRevision').val();
				if(revisionNo == revision4checkDyn){
					self._preview(e.dataTransfer.files);
					self.all.push(e.dataTransfer.files);
				}else{
					jAlert('Plese select same revision number files');
				}
			}
		}
	}
	
	var pdfID = "";
	var pdfRevID = "";
	
	multiUploaderRev.prototype._uploader = function(file, f){
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
							validator = true;
							pdfID = jsonResult.dataArr.insertedDwgID;
							pdfRevID = jsonResult.dataArr.insertedDwgRevID;
							if (self.all[1]) {
								console.log(self.all);
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
							console.log(validator);	
							if(requestComplete == 'dwg'){	
								console.log(requestComplete);
								if (self.all[1] && validator) {
									validator = false;
									console.log(self.all);
									var file = self.all[1];
									self._uploader(file, f);
								}else if (self.all[2]) {
									console.log(self.all);
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
					}else{
						jAlert('Error, Please Try Again ');	
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
	}
	
	multiUploaderRev.prototype._uploaderSkeeped = function(){
		var string ='';
		var customFormData = $('#'+this.config.form).serialize();
		if(1){
			var data = new FormData();
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
					console.log(rponse);
					if(jsonResult.status){	
						$('#outPutResultPara').text(jsonResult.msg);	
						$('#outPutResult').show('fast');	
						closePopup(300);
						RefreshTable();
					}else{
						jAlert('Error, Please Try Again ');	
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
	}
	
	multiUploaderRev.prototype._startUpload = function(){
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

function initMultiUploaderRev(config){
	new multiUploaderRev(config);
}
