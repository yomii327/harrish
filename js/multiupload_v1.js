var testFxArr = Array();  
var tempArrKey = 0;
var tempId = 0;

function bulkUploader(config){
	this.config = config;
	this.items = "";
	var groupFilNameArr2 = new Array();//Global Array to store select element in 
	var groupFileNumberArr2 = new Array();//Global Array to store select element in
	var finalizeValue = {}; 
	var locatioArray = {};
	var MainFileArr = {};
	var fullFileNameArray1 = {};
	this.all = []
	var self = this;
	var tempId = 0;
	bulkUploader.prototype._init = function(){
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
	
	bulkUploader.prototype._submit = function(e){
		e.stopPropagation(); e.preventDefault();
		if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
		if($('#pdfStatusDyna').val().trim() == ''){$('#errorPdfStatus').show('slow');return false;}else{$('#errorPdfStatus').hide('slow');}			
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
		//var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;
		if(this.items.length > 0){
			var html = "";		
			var uId = "";
			
			for(var i = 0; i<this.items.length; i++){
				var FileExt = (/[.]/.exec(data[i].name)) ? /[^.]+$/.exec(data[i].name) : undefined;
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				var sampleIcon = '<img src="images/pdf-24.png" />';
				if(FileExt == 'dwg' || FileExt == 'DWG')
					var sampleIcon = '<img src="images/dwg-24.png" />';
				if(FileExt == 'cad'  || FileExt == 'CAD')
					var sampleIcon = '<img src="images/cad-24.png" />';
				if(FileExt == 'xls'  || FileExt == 'XLS')
					var sampleIcon = '<img src="images/xls_icon.png" />';
				if(FileExt == 'xlsx' || FileExt == 'XLSX')
					var sampleIcon = '<img src="images/xlsx_icon.png" />';
				if(FileExt == 'doc' || FileExt == 'DOC')
					var sampleIcon = '<img src="images/doc_icon.png" />';
				if(FileExt == 'docx' || FileExt == 'DOCX')
					var sampleIcon = '<img src="images/docx_icon.png" />';
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert('Please select either .pdf or .dwg or .cad or .xls or xlsx or .doc or .docx file only');		
						return;
						sampleIcon = '<img src="images/unknown.png" />';
						errorClass =" invalid";
					} 
					var fileTitle = this.items[i].name;
					tempArr = fileTitle.split(".");
					var lastEle = tempArr.pop();
					var extensionforattr = lastEle;
					var processFileName = tempArr.join(".");
					var fileNameTitle = processFileName; 
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
					console.log(revisionNo);
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
					//console.log(lastEle);
					//console.log(documentTitle);
					//console.log(fileNameTitle);
					if($('#project_Id').val()==242 || $('#project_Id').val()==243){
						var jsArrtr2 = new Array();	
					}else{
						var jsArrtr2 = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
					}	
					jsArrtr2 = subTitleArr($('#drawingattribute1').val());

/*html += '<div id="selectDrawingNameHolder_'+fileNameTitle+revisionNo+'" class="divId_'+tempId+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+' divId_'+tempId+'" rel="'+uId+'" id="ID'+uId+'" > <img src="images/linked.png" onclick="showMappingTable(\''+fileNameTitle+revisionNo+'\', \''+uId+'\', \''+fileNameTitle+revisionNo+'\');" style="position:absolute;right:5px;" title="Link to an existing document" /><img onclick="removeBulkAttachment(\''+tempId+'\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:50px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:20px;width: 40%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox">Document Number <input class="drgTitle" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"> </div> <br/> <br/> <div id="revisionBox">Document Title <input  class="drgDesc" type="text" name="description['+fileNameTitle+revisionNo+']" value="'+documentTitle+'" size="10"> <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Description and Number can\'t be same.</div></lable></div> <br/> <br/> <div id="revisionBox">Document Revision <input type="text" name="revisionNo['+fileNameTitle+revisionNo+']" value="'+revisionNo+'" size="10" maxlength="5"> </div> <br/> <br/> <div id="revisionBox">Attribute <select class="drawingattribute3js" name="drawingattribute3js['+fileNameTitle+revisionNo+']" id="drawingattribute3js" style="width:90px;"><option value="PDF">PDF</option><option value="DWG"';
if(extensionforattr.toLowerCase() == 'dwg')
	html += 'selected="selected"';

html += '>DWG</option></select> </div> </li> <li style="margin:35px 5px 0 0;"> Attribute 2 <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL<br /> for multiple) </span></li> <li>  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="margin:35px 0px 0 5px;"><option value="">Select</option>';
						*/
						html += '<div id="selectDrawingNameHolder_'+fileNameTitle+revisionNo+'" class="divId_'+tempId+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+' divId_'+tempId+'" rel="'+uId+'" id="ID'+uId+'" > <img src="images/linked.png" onclick="showMappingTable(\''+fileNameTitle+revisionNo+'\', \''+uId+'\', \''+fileNameTitle+revisionNo+'\');" style="position:absolute;right:5px;" title="Link to an existing document" /><img onclick="removeBulkAttachment(\''+tempId+'\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:50px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:20px;width: 40%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox">Document Number <input class="drgTitle documentnumber" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"  id="documentNumber'+tempId+'"> </div> <br/> <br/> <div id="revisionBox">Document Title <input  class="drgDesc documentname" type="text" name="description['+fileNameTitle+revisionNo+']" value="'+documentTitle+'" size="10"  id="documentName'+tempId+'"> <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Description and Number can\'t be same.</div></lable></div> <br/> <br/> <div id="revisionBox">Document Revision <input type="text" name="revisionNo['+fileNameTitle+revisionNo+']" value="'+revisionNo+'" size="10" maxlength="5"> </div> <br/> <br/> <div id="revisionBox">Attribute <select class="drawingattribute3js" name="drawingattribute3js['+fileNameTitle+revisionNo+']" id="drawingattribute3js" style="width:90px;"><option value="PDF">PDF</option><option value="DWG"';
if(extensionforattr.toLowerCase() == 'dwg')
	html += 'selected="selected"';

html += '>DWG</option><option value="CAD"';
if(extensionforattr.toLowerCase() == 'cad')
	html += 'selected="selected"';

html += '>CAD</option><option value="XLS"';
if(extensionforattr.toLowerCase() == 'xls' || extensionforattr.toLowerCase() == 'XLS')
	html += 'selected="selected"';

html += '>XLS</option><option value="XLSX"';
if(extensionforattr.toLowerCase() == 'xlsx' || extensionforattr.toLowerCase() == 'XLSX')
	html += 'selected="selected"';

html += '>XLSX</option><option value="DOC"';
if(extensionforattr.toLowerCase() == 'doc' || extensionforattr.toLowerCase() == 'DOC')
	html += 'selected="selected"';

html += '>DOC</option><option value="DOCX"';
if(extensionforattr.toLowerCase() == 'docx' || extensionforattr.toLowerCase() == 'DOCX')
	html += 'selected="selected"';

html += '>DOCX</option></select> </div> </li> <li style="margin:35px 5px 0 0;"> Attribute 2 <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL<br /> for multiple) </span></li> <li>  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="margin:35px 0px 0 5px;"><option value="">Select</option>';
						for(g=0; g<jsArrtr2.length; g++){
							html += '<option value="'+jsArrtr2[g]+'">'+jsArrtr2[g]+'</option>';	
						}
						html += '</select></li> </ul> <input type="hidden" name="isRemoved['+fileNameTitle+revisionNo+']" value="0" id="removeId_'+tempId+'"> <input style="height:15px;" type="hidden" name="" id="checkFlag" value="0"></div>';
				}
			++tempId;
			}
			$("#innerDiv").append(html);
		}
	}

	bulkUploader.prototype._read = function(evt){
		if(evt.target.files[0].size > 0){
			if(evt.target.files){
				self._preview(evt.target.files);
				self.all.push(evt.target.files);
				//Custom
				for (var i = 0; i < evt.target.files.length; i++) {
					testFxArr[tempArrKey] = evt.target.files[i];
					tempArrKey++;
				}
			} else {
				console.log("Failed file reading");
			}
		}else{
			jAlert('File size 0 bytes, Please select correct file');
		}
	}
	
	bulkUploader.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		if(FileExt == 'dwg' || FileExt == 'DWG'){
			return 1;
		}else if(FileExt == 'cad' || FileExt == 'CAD'){
			return 1;
		}else if(FileExt == 'xls' || FileExt == 'XLS'){
			return 1;
		}else if(FileExt == 'xlsx' || FileExt == 'XLSX'){
			return 1;
		}else if(FileExt == 'doc' || FileExt == 'DOC'){
			return 1;
		}else if(FileExt == 'docx' || FileExt == 'DOCX'){
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
			$.each($('#'+this.config.form).serializeArray(), function() {             
				data.append(this.name, this.value);
				
				if(this.name.substr(0,19) == 'drawingattribute2js'){
					if(oldAttName != this.name && oldAttName != ""){
						data.append(this.name, drawingattribute2Datajs);
						drawingattribute2Datajs = "";
					}
					if(drawingattribute2Datajs == ""){
						drawingattribute2Datajs = this.value;
					}else{
						drawingattribute2Datajs += '###'+this.value;
					}
					oldAttName = this.name;	
					data.append(this.name, drawingattribute2Datajs);
				}
				if(this.name == 'drawingattribute2'){
					if(drawingattribute2Data == ""){
						drawingattribute2Data = this.value;
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
								console.log('value----->'+f+1);
								console.log('length---->'+file.length);
								if(f+1 == file.length){
									hideProgress();
									setTimeout(function(){closePopup(300);}, 1000);
									//RefreshTable();
									searchDrawPdf($("#drawingattribute1").val());
								}
								//searchDrawPdf($("#drawingattribute1").val());
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
		/*if(testFxArr.length > 0){
			for(var k=0; k<testFxArr.length; k++){
				var file = testFxArr[k];
				this._uploader(file,0,k);
			}
			testFxArr = Array();  
			tempArrKey = 0;			
		}*/
		groupFilNameArr2 = new Array();
		groupFileNumberArr2 = new Array();
		if(testFxArr.length > 0){
			var datas = new FormData();
			var xkName = 0;
			var xkNumber = 0;
			$('.documentnumber').each(function(){
				console.log('test-->'+xkName);
				if($("#documentNumber"+xkName).val()!='undefined'){
					groupFileNumberArr2.push("'"+$("#documentNumber"+xkName).val()+"'");
					++xkName; 
				}			  
			});
			
			
			$('.documentname').each(function(){
				if($("#documentName"+xkNumber).val()!='undefined'){
					 groupFilNameArr2.push("'"+$("#documentName"+xkNumber).val()+"'");
					 ++xkNumber; 
				}			  
			});
			datas.append('groupFilNameArr', groupFilNameArr2);
			datas.append('groupFileNumberArr', groupFileNumberArr2);
		
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
		$.each(locatioArray,function(locKey, locVal){
			if(fullFileNameArray1[locKey]!=''){
				locMessage += fullFileNameArray1[locKey]+' : '+ locVal+'<br>';
			}else{
				locMessage += locKey+' : '+ locVal+'<br>';
			}
		});
		
		if($("#checkFlag").val()==1){	
			var supersedingmsg = 'A document already exists with this filename. These documents will be superseded.';
			//var r = jConfirm(''+groupFilNameArr2+' will be superseded in this upload? And '+msgs+' would be added as revision as this number is already exists. These will be location for each file:<br><br>'+locMessage, null, function(r) {
			var r = jConfirm(supersedingmsg, null, function(r) {
				if(r==true){
					showProgress();
					if(testFxArr.length > 0){
						for(var k=0; k<testFxArr.length; k++){
							var file = testFxArr[k];
							self._uploader(file,0,k);
						}
						testFxArr = Array();  
						tempArrKey = 0;			
					}
					//for closeing popup
				}else{
					//set checkflag condition 0 if user cancels superseded condition
					$("#checkFlag").val(0);
				}
			});
		}
		else{	
				for(var k=0; k<testFxArr.length; k++){
					var file = testFxArr[k];
					this._uploader(file,0,k);
				}
				testFxArr = Array();  
				tempArrKey = 0;			
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

function initBulkUploader(config){
	new bulkUploader(config);
}
