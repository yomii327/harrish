var testFxArr = Array();  
var tempArrKey = 0;
var tempId = 0;

function bulkUploader(config){
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	bulkUploader.prototype._init = function(){
		if (window.File && /**/
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
	
	bulkUploader.prototype._submit = function(e){
		e.stopPropagation(); e.preventDefault();
		if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
		if($("#uploaderBulk span").html() == null){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
		var returnVal = true;
		$('#innerDiv #filenameFormat').each(function() {
			//console.log($(this).val().trim()+" _ raj.");
			if($(this).val().trim() == "" ){
				$(this).focus();
				returnVal = false;
			}
		});	
		if(!returnVal){
			alert("Please select filename format.");
			return false;
		}
		//if($('#drawingattribute1').val().trim() == ''){$('#errorDrawingAttribute1').show('slow');return false;}else{$('#errorDrawingAttribute1').hide('slow');}			
		//if($('#pdfStatusDyna').val().trim() == ''){$('#errorPdfStatus').show('slow');return false;}else{$('#errorPdfStatus').hide('slow');}			
/*		var returnVal = true;
		$('#innerDiv').find('div.bulkfiles').each(function(){
			var currRel = $(this).attr('rel');
			var currDivID = $(this).attr('id');
			if($(this).find('.drgTitle').val().trim() == $(this).find('.drgDesc').val().trim()){
				$(this).find('.errorDrawingTitle').show('slow');
				returnVal = false;
			}else{
				$(this).find('.errorDrawingTitle').hide('slow');
			}
		});
		console.log(returnVal);
		if(!returnVal)
			return returnVal;*/
		self._startUpload();
	}
	
	bulkUploader.prototype._preview = function(data){
		this.items = data;
		var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

		if(this.items.length > 0){
			var html = "";		
			var uId = "";
			
			for(var i = 0; i<this.items.length; i++){ tempId++;
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				var sampleIcon = ''; //'<img src="images/pdf-24.png" />';
				//if(FileExt == 'dwg')
				//	var sampleIcon = '<img src="images/dwg-24.png" />';
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert("These file formats are acceptable [.xlsx, .xls, .docx, .doc, .jpg, .png, .dwg, .rvt, .pdf]");	
						return;
						//sampleIcon = '<img src="images/unknown.png" />';
						errorClass =" invalid";
					} 
					var fileTitle = this.items[i].name;
					
					tempArr = fileTitle.split(".");
					var lastEle = tempArr.pop();
					var extensionforattr = lastEle;
					var processFileName = tempArr.join(".");
					var fileNameTitle = processFileName; 
					var documentTitle = "";					
					var revisionNo = '';
					fileNameTitle = fileNameTitle.replace(/[^a-zA-Z0-9 _]+/g,'');
					
					fileNameFormat = $("#formatList option:selected").val();
					defaultSplitFileNameForDocument(fileNameFormat, 'divId_'+tempId, fileNameTitle);
					
					/*
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
					}*/

					var jsArrtr2 = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
					
					jsArrtr2 = subTitleArr($('#drawingattribute1').val());

html += '<div id="selectDrawingNameHolder_'+fileNameTitle+revisionNo+'" class="divId_'+tempId+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+' divId_'+tempId+'" rel="'+uId+'" id="ID'+uId+'" ><table width="100%" border="0"><tr><td rowspan="3" width="315"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </td><td width="125">Document Number</td><td width="95"><input class="drgTitle" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10" id="docNumber" style=""></td><td width="108">Filename Format</td><td width="125"><select class="drawingattribute2js" name="filenameFormat['+fileNameTitle+revisionNo+']" id="filenameFormat" style="width:125px;" onchange="splitFileNameForDocument(this.value, \'divId_'+tempId+'\');">';
		html += ($("#formatList").html())+'</select></td><td width="14"><img onclick="removeBulkAttachment(\''+tempId+'\');" title="Remove Attachment" src="images/delete.png" style=""></td></tr><tr><td>Document Title</td><td><input  class="drgDesc" type="text" name="description['+fileNameTitle+revisionNo+']" value="'+documentTitle+'" size="10"  id="docTitle" style=""></td><td rowspan="2">Trade <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL for <br />multiple) </span></td><td rowspan="2">  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="width:125px; height: 37px;">';
		html += ($("#tradeList").html())+'</select></td></tr><tr><td>Document Revision</td><td><input type="text" name="revisionNo['+fileNameTitle+revisionNo+']" value="'+revisionNo+'" size="10" maxlength="5" id="docRevision"> <input type="hidden" id="hiddenFileName" disabled value="'+processFileName+'" ></td></tr></table> <!--div id="revisionBox" style="float:left;">  <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Description and Number can\'t be same.</div></lable></div> <br/> <br/> <div id="revisionBox"> </div> <br/> <br/> </li><li style="margin:5px -2px 0 0; padding-left: 8px;"><!--br>File Type--></li> <!--li><!--br><div id="revisionBox"><select class="drawingattribute3js" name="drawingattribute3js['+fileNameTitle+revisionNo+'][]" id="drawingattribute3js" style="width:125px; margin-left: 30px; margin-top: 5px;"><option value="PDF">PDF</option><option value="DWG"';
		if(extensionforattr.toLowerCase() == 'dwg'){
			html += 'selected="selected"';
		}
		html += '>DWG</option></select> </div--></li--> </ul--> <input type="hidden" name="isRemoved['+fileNameTitle+revisionNo+']" value="0" id="removeId_'+tempId+'"> </div>';
				}
			}
			$("#innerDiv").append(html);
			
			//Start:- Split File Name For Document
				fileNameFormat = $("#formatList option:selected").val();//'Document Title###_###Document Number###[###Document Revision###]';
				if(fileNameFormat != ""){
					splitFileNameForDocument(fileNameFormat, 'divId_'+tempId);
				}
			//End:- Split File Name For Document
		}
	}

	bulkUploader.prototype._read = function(evt){
		if(evt.target.files[0].size > 0){
			if(evt.target.files){
				self._preview(evt.target.files);
				self.all.push(evt.target.files);
				
				//Custom
				testFxArr[tempArrKey] = evt.target.files[0];
				tempArrKey++;			
			} else {
				console.log("Failed file reading");
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}
	
	bulkUploader.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		
		if(FileExt == 'dwg' || FileExt == 'DWG' ){
			return 1;
		}else if(FileExt == 'pdf' || FileExt == 'PDF' ){
			return 1;
		}else if(FileExt == 'xlsx' || FileExt == 'XLSX' ){
			return 1;
		}else if(FileExt == 'xls' || FileExt == 'XLS' ){
			return 1;
		}else if(FileExt == 'docx' || FileExt == 'DOCX' ){
			return 1;
		}else if(FileExt == 'doc' || FileExt == 'DOC' ){
			return 1;
		}else if(FileExt == 'jpg' || FileExt == 'JPG' ){
			return 1;
		}else if(FileExt == 'png' || FileExt == 'PNG'){
			return 1;
		}else if(FileExt == 'zip' || FileExt == 'ZIP'){
			return 1;
		}else if(FileExt == 'rvt' || FileExt == 'RVT'){
			return 1;
		}else if(FileExt == 'vsd' || FileExt == 'VSD'){
			return 1;
		}else if(FileExt == 'vdx' || FileExt == 'VDX'){
			return 1;
		}else if(FileExt == 'nwd' || FileExt == 'NWD'){
			return 1;
		}else if(FileExt == 'nwc' || FileExt == 'NWC'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	bulkUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		if(e.dataTransfer.files[0].size > 0){
			self._preview(e.dataTransfer.files);
			self.all.push(e.dataTransfer.files);
			
			for (i = 0; i < (e.dataTransfer.files).length; ++i) {
				testFxArr[tempArrKey] = e.dataTransfer.files[i];
				tempArrKey++;
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}
//New single message start here
	var drawingDataFinal = new Array;
//New single message end here

	bulkUploader.prototype._uploader = function(file, f, k){
		var tempFile = Array(testFxArr[k]);		
		file = tempFile;
		//return false;	
		
		var drawingData = new Array();
		if(typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0){
			var requestCounter = 0;
			for(g=0; g<self.all.length; g++){
				if(self.all[g].length > 1){
					requestCounter = requestCounter + self.all[g].length;
				}else{
					requestCounter++;
				}
			}
			var data = new FormData();
			var ids = file[f].name._unique();
			data.append('file',file[f]);
			data.append('index',ids);
			
			data.append('totalRequestCount', requestCounter);
			
			data.append("mappingDocumentArr", JSON.stringify(mappingDocumentArr));//New mapping array store here
			
			$(".dfiles[rel='"+ids+"']").find(".progress").show();
			var drawingattribute2Data = '';
  			var drawingattribute2Datajs = '';
			var oldAttName = "";
			var selTradeName = "";
			$.each($('#'+this.config.form).serializeArray(), function() {             
				data.append(this.name, this.value);
				
				if(this.name.substr(0,19) == 'drawingattribute2js'){
					if(oldAttName != this.name && oldAttName != ""){
						data.append(this.name, drawingattribute2Datajs);
						drawingattribute2Datajs = "";
					}
					if(drawingattribute2Datajs == ""){
						drawingattribute2Datajs = this.value;
						selTradeName = this.value;
					}else{
						drawingattribute2Datajs += '###'+this.value;
					}
					oldAttName = this.name;	
					data.append(this.name, drawingattribute2Datajs);
				}
				if(this.name == 'drawingattribute2'){
					if(drawingattribute2Data == ""){
						drawingattribute2Data = this.value;
						selTradeName = this.value;
					}else{
						drawingattribute2Data += '###'+this.value;
					}
					data.append(this.name, drawingattribute2Data);
				}
    	    });
			
			data.append("antiqueID", Math.random());//Updated for unique Request
			
			drawingData[0] = $(".bulkfiles[rel='"+ids+"']").find("h3 span").text();
			drawingData[1] = $(".bulkfiles[rel='"+ids+"']").find("h3 span").text();
			drawingData[2] = $(".bulkfiles[rel='"+ids+"']").find("div input").val();
			drawingDataFinal[f] = drawingData;
			showProgress();
			$.ajax({
				type:"POST",
				url:this.config.uploadUrl,
				data:data,
				cache: false,
				contentType: false,
				processData: false,
				success:function(rponse){
					$(".dfiles[rel='"+ids+"']").find(".progress").hide();	
					$(".dfiles[rel='"+ids+"']").find(".progress").parent().css({'border-color' : '#bcebbc', 'background-color' : '#ddffdd'});
					if (f+1 < file.length) {
						self._uploader(file,f+1);
					}else{//Send mail here
						if(rponse != ""){
							var jsonResult = JSON.parse(rponse);	
							if(jsonResult.status){
								hideProgress();
								setTimeout(function(){closePopup(300);}, 1000);
								
								//$("#searchTrade").attr('selected', 'selected');
								//$("#routetype option[value='"+selTradeName+"']").attr("selected", "selected");
								//alert(selTradeName);
								if($("#isTradeDetailsForm").val()!=0){
									refreshDocumentsTradeTable();
								}else{
									$("#searchTrade").val(selTradeName);
									searchDrawPdf(selTradeName)
									//RefreshTable();
								}
							}
						}else{
							console.log('We Are Here !');
						}
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
			
	}
	
/*	bulkUploader.prototype._startUpload = function(){
		if(this.all.length > 0){
			for(var k=0; k<this.all.length; k++){
				var file = this.all[k];
				this._uploader(file,0, k);
			}
		}
	}
*/
	bulkUploader.prototype._startUpload = function(){
		if(testFxArr.length > 0){
			for(var k=0; k<testFxArr.length; k++){
				var file = testFxArr[k];
				this._uploader(file,0,k);
			}
			testFxArr = Array();  
			tempArrKey = 0;			
		}
	}
		
	String.prototype._unique = function(){
		return this.replace(/[a-zA-Z]/g, function(c){
     	   return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
    	});
	}
	this._init();
}

function initBulkUploader(config){
	new bulkUploader(config);
}

function defaultSplitFileNameForDocument(fileNameFormat, divId, fileTitle){
	if(fileNameFormat != ""){
		showProgress();
		//var fileTitle = $("."+divId+" #hiddenFileName").val();
		$.post('add_estimate_document_register_bulk_v1.php?fileTitle='+fileTitle+'&fileNameFormat='+fileNameFormat+'&uniqueID='+Math.random(), {fileTitle : fileTitle, fileNameFormat : fileNameFormat}).done(function(data) {
			hideProgress();
			console.log(data);
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				//jAlert(jsonResult.msg);	
				$("."+divId+" #docTitle").val(jsonResult.docTitle);
				$("."+divId+" #docNumber").val(jsonResult.docNumber);
				$("."+divId+" #docRevision").val(jsonResult.docRevision);						
				//closePopup(300);
			}else{
				jAlert(jsonResult.msg);	
			}																							
		});
	}
}

function splitFileNameForDocument(fileNameFormat, divId){
	if(fileNameFormat != ""){
		showProgress();
		var fileTitle = $("."+divId+" #hiddenFileName").val();
		$.post('add_estimate_document_register_bulk_v1.php?fileTitle='+fileTitle+'&fileNameFormat='+fileNameFormat+'&uniqueID='+Math.random(), {fileTitle : fileTitle, fileNameFormat : fileNameFormat}).done(function(data) {
			hideProgress();
			console.log(data);
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				//jAlert(jsonResult.msg);	
				$("."+divId+" #docTitle").val(jsonResult.docTitle);
				$("."+divId+" #docNumber").val(jsonResult.docNumber);
				$("."+divId+" #docRevision").val(jsonResult.docRevision);						
				//closePopup(300);
			}else{
				jAlert(jsonResult.msg);	
			}																							
		});
	}
}