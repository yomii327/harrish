function multiUploaderDT(config, gs){ 
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	multiUploaderDT.prototype._init = function(){
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
	
	multiUploaderDT.prototype._submit = function(e){ 
		e.stopPropagation(); e.preventDefault();

		var validationFlagVal = parseInt($('#validationFlag').val());
		console.log(validationFlagVal);
		switch(validationFlagVal){
			case 1:
				if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
				//if($('#drawingRevision').val().trim() == ''){$('#errorDrawingRevision').show('slow');return false;}else{$('#errorDrawingRevision').hide('slow');}	
			break;
			case 2:
				if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUploadDocumentTransmital').show('slow');return false;}else{$('#errorMultiUploadDocumentTransmital').hide('slow');}
				if($('#drawingTitleDocumentTransmital').val().trim() == ''){$('#errorDrawingTitle').show('slow');return false;}else{$('#errorDrawingTitle').hide('slow');}			
				//if($('#drawingRevision').val().trim() == ''){$('#errorDrawingRevision').show('slow');return false;}else{$('#errorDrawingRevision').hide('slow');}			
			break;
			case 3:
				//if($('#innerDiv').text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
				//if($('#drawingRevision').val().trim() == ''){$('#errorDrawingRevision').show('slow');return false;}else{$('#errorDrawingRevision').hide('slow');}			
			break;
			default:
				console.log('In Defaulst Case');
			break;
		}
		self._startUpload();
	}
	
	multiUploaderDT.prototype._preview = function(data){
		this.items = data;
		if(deadlockDocumentTransmital){
			if(this.items.length > 0){
				var html = "";		
				var uId = "";
				if(this.items.length < 2){
					for(var i = 0; i<this.items.length; i++){
						uId = this.items[i].name._unique();
						var sampleIcon = '<img src="images/pdf-24.png" />';
						var errorClass = "";
						if(typeof this.items[i] != undefined){
							if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
								jAlert('Plese select .pdf file');		
								return;
								sampleIcon = '<img src="images/unknown.png" />';
								errorClass =" invalid";
							}
							deadlockDocumentTransmital = false;
							html += '<div class="dfiles'+errorClass+'" rel="'+uId+'"><h3>'+sampleIcon+this.items[i].name+'</h3><div id="'+uId+'" class="progress" style="display:none;"><img src="images/ajax-loader.gif" /></div></div>';
						}else{
							console.log('werw dhewea')	;
						}
					}
				}else{
					jAlert('Please select only one file ');
				}
				$("#"+this.config.dragArea).append(html);
			}
		}else{
			jAlert('You can\'t add more files');
		}
	}

	multiUploaderDT.prototype._read = function(evt){
		if(evt.target.files){
			self._preview(evt.target.files);
			self.all.push(evt.target.files);
		} else 
			console.log("Failed file reading");
	}
	
	multiUploaderDT.prototype._validate = function(format, fileName){
		var fileNameArr = fileName.split(".");
		//var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		var FileExt = fileNameArr.pop();
		var tempFileName = fileNameArr.join(".");
		$('#drawingNumberDocumentTransmital').val(tempFileName);

		if(FileExt == 'dwg'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	multiUploaderDT.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		self._preview(e.dataTransfer.files);
		self.all.push(e.dataTransfer.files);
	}
	
	multiUploaderDT.prototype._uploader = function(file, f){
		var string ='';

		var customFormData = $('#'+this.config.form).serialize();
		if((typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0 ) || (skeepUpload == 1)){
			var data = new FormData();
			var ids = file[f].name._unique();
			data.append('file',file[f]);
			data.append('index',ids);
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
						$('#buttonFirstSubmit').prop( "disabled", false );
						$('#disableButtonDiv').hide();
						closePopup_gs(300, gs);
					}else{
						jAlert('Error, Please Try Again ');	
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
	}
	
	multiUploaderDT.prototype._uploaderSkeeped = function(){
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
						closePopup_gs(300);
					}else{
						jAlert('Error, Please Try Again ');	
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
	}
	
	multiUploaderDT.prototype._startUpload = function(){
		console.log('In Upload Condition');
		if(this.all.length > 0){
			for(var k=0; k<this.all.length; k++){
				var file = this.all[k];
				console.log(file);
				this._uploader(file,0);
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

function initMultiUploaderDT(config, gs){
	new multiUploaderDT(config, gs);
}