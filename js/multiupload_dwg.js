var testFxArr = Array();  
var tempArrKey = 0;
var dwgTempId = 0;

function bulkUploaderDWG(config){
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	bulkUploaderDWG.prototype._init = function(){
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
	
	bulkUploaderDWG.prototype._submit = function(e){
		e.stopPropagation(); e.preventDefault();
		if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
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
	
	bulkUploaderDWG.prototype._preview = function(data){
		this.items = data;
		
		var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

		if(this.items.length > 0){
			var html = "";		
			var uId = "";
			for(var i = 0; i<this.items.length; i++){ dwgTempId++;
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				uId = uId.replace(/'/g, "\\'");
				var sampleIcon = '<img src="images/pdf-24.png" />';
				if(FileExt == 'dwg')
					var sampleIcon = '<img src="images/dwg-24.png" />';
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert('Please choose .dwg file');		
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
					
html += '<div id="selectDrawingNameHolder_'+i+'" class="divId_'+dwgTempId+'" style="height:15px;"></div> <div class="bulkfilesdwg'+errorClass+' divId_'+dwgTempId+'" rel="'+uId+'" id="ID'+uId+'"> <img src="images/linked.png" onclick="showMappingTable(\''+fileNameTitle+revisionNo+'\', \''+uId+'\', '+i+');" style="position:absolute;right:5px;" title="Link to an existing document" /> <img onclick="removeBulkAttachment(\''+dwgTempId+'\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:30px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:13px;width: 41%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> </ul> <input type="hidden" name="isRemoved['+fileNameTitle+revisionNo+']" value="0" id="removeId_'+dwgTempId+'"> </div>';
				}
			}
			$("#innerDiv").append(html);
		}
	}

	bulkUploaderDWG.prototype._read = function(evt){
		if(evt.target.files){
			self._preview(evt.target.files);
			self.all.push(evt.target.files);
			//Custom
			testFxArr[tempArrKey] = evt.target.files[0];
			console.log(testFxArr[tempArrKey]);
			tempArrKey++;			
		} else 
			console.log("Failed file reading");
	}
	
	bulkUploaderDWG.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		if(FileExt == 'dwg'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	bulkUploaderDWG.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		self._preview(e.dataTransfer.files);
		self.all.push(e.dataTransfer.files);
		//console.log(self.all);
		for(var z=0;z<e.dataTransfer.files.length;z++){
		testFxArr[z] = e.dataTransfer.files[z];
		tempArrKey++;
		}
		
	}
//New single message start here
	var drawingDataFinal = new Array;
//New single message end here

	bulkUploaderDWG.prototype._uploader = function(file, f, k){
		//var tempFile = Array(testFxArr[k]);		
		//file = tempFile;
		//return false;		
			
		var drawingData = new Array();
		if(typeof file != undefined && self._validate(file.type, file.name) > 0){
			var requestCounter = 0;
			for(g=0; g<self.all.length; g++){
				if(self.all[g].length > 1){
					requestCounter = requestCounter + self.all[g].length;
				}else{
					requestCounter++;
				}
			}
			var data = new FormData();
			var ids = file.name._unique();
			data.append('file',file);
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
			drawingData[0] = $(".bulkfilesdwg[rel='"+ids+"']").find("h3 span").text();
			drawingData[1] = $(".bulkfilesdwg[rel='"+ids+"']").find("h3 span").text();
			drawingData[2] = $(".bulkfilesdwg[rel='"+ids+"']").find("div input").val();
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
			console.log("Invalid file format - "+file.name);
			
	}
	
/*	bulkUploaderDWG.prototype._startUpload = function(){
		if(this.all.length > 0){
			for(var k=0; k<this.all.length; k++){
				var file = this.all[k];
				this._uploader(file,0);
			}
		}
	}
*/


	bulkUploaderDWG.prototype._startUpload = function(){
		if(testFxArr.length > 0){
			for(var k=0; k<testFxArr.length; k++){
				var file = testFxArr[k];
				console.log('here it is');
				console.log(file);
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

function initbulkUploaderDWG(config){
	new bulkUploaderDWG(config);
}
