function bulkUploader(config){
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
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
		
		var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

		if(this.items.length > 0){
			var html = "";		
			var uId = "";
			for(var i = 0; i<this.items.length; i++){
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				uId = uId.replace(/'/g, "\\'");
				var sampleIcon = '<img src="images/pdf-24.png" />';
				if(FileExt == 'dwg')
					var sampleIcon = '<img src="images/dwg-24.png" />';
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert('Plese select .pdf file');		
						return;
						sampleIcon = '<img src="images/unknown.png" />';
						errorClass =" invalid";
					} 
					var fileTitle = this.items[i].name;
					tempArr = fileTitle.split(".");
					var lastEle = tempArr.pop();
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
					if(revisionNo == ""){
						tempArr = processFileName.split("-");
						var lastEle = tempArr.pop();
						var fileNameTitle = tempArr.join("-").trim();
						revisionNo = lastEle;
					}
					
					revisionNo = revisionNo.replace(/'/g, "\\'");
					fileNameTitle = fileNameTitle.replace(/'/g, "\\'");
					
					var jsArrtr2 = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
					
					jsArrtr2 = subTitleArr($('#drawingattribute1').val());
					
html += '<div id="selectDrawingNameHolder_'+i+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+'" rel="'+uId+'" id="ID'+uId+'"> <img src="images/linked.png" onclick="showMappingTable(\''+fileNameTitle+revisionNo+'\', \''+uId+'\', '+i+');" style="position:absolute;right:5px;" title="Link to an existing document" /> <ul id="filePanel" > <li style="margin-top:20px;width: 41%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox">Document Number <input class="drgTitle" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"> </div> <br/> <br/> <div id="revisionBox">Document Title <input  class="drgDesc" type="text" name="description['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"> <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Description and Number can\'t be same.</div></lable></div> <br/> <br/> <div id="revisionBox">Document Revision <input type="text" name="revisionNo['+fileNameTitle+revisionNo+']" value="'+revisionNo+'" size="10" maxlength="5"> </div> </li> <li style="margin:35px 5px 0 0;"> Attribute 2 <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL<br /> for multiple) </span></li> <li>  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="margin:35px 0px 0 5px;"><option value="">Select</option>';
						for(g=0; g<jsArrtr2.length; g++){
							html += '<option value="'+jsArrtr2[g]+'">'+jsArrtr2[g]+'</option>';	
						}
						html += '</select> </li> </ul> </div>';
				}
			}
			$("#innerDiv").append(html);
		}
	}

	bulkUploader.prototype._read = function(evt){
		if(evt.target.files){
			self._preview(evt.target.files);
			self.all.push(evt.target.files);
		} else 
			console.log("Failed file reading");
	}
	
	bulkUploader.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		if(FileExt == 'pdf'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	bulkUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		self._preview(e.dataTransfer.files);
		self.all.push(e.dataTransfer.files);
	}
//New single message start here
	var drawingDataFinal = new Array;
//New single message end here

	bulkUploader.prototype._uploader = function(file, f){
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
			console.log(mappingDocumentArr);
			
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
								RefreshTable();
							}
						}else{
							hideProgress();
							setTimeout(function(){closePopup(300);}, 1000);	
							RefreshTable();
						}
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
			
	}
	
	bulkUploader.prototype._startUpload = function(){
		if(this.all.length > 0){
			for(var k=0; k<this.all.length; k++){
				var file = this.all[k];
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

function initBulkUploader(config){
	new bulkUploader(config);
}
