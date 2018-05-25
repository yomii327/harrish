var testFxArr = new Array();  
var tempArrKey = 0;
var tempId = 0;

function bulkUploader(config){
	tempId = 0;
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
		//alert(5);
		//if($('#external_correspondance').prop("checked")==true){}else{jAlert('Please select External Correspondance to attach data in compose section');return false;}
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
				if(tempId>1){
					jAlert("Only one file can upload at a time");
					return false;
				}
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				var sampleIcon = '<img src="images/pdf-24.png" />';
				if(FileExt == 'dwg' || FileExt == 'DWG')
					var sampleIcon = '<img src="images/dwg-24.png" />';
				if(FileExt == 'cad'  || FileExt == 'CAD')
					var sampleIcon = '<img src="images/cad-24.png" />';	
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert('Please select .msg or .eml file');		
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

					var jsArrtr2 = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
					
					//jsArrtr2 = subTitleArr($('#drawingattribute1').val());

/*html += '<div id="selectDrawingNameHolder_'+fileNameTitle+revisionNo+'" class="divId_'+tempId+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+' divId_'+tempId+'" rel="'+uId+'" id="ID'+uId+'" > <img src="images/linked.png" onclick="showMappingTable(\''+fileNameTitle+revisionNo+'\', \''+uId+'\', \''+fileNameTitle+revisionNo+'\');" style="position:absolute;right:5px;" title="Link to an existing document" /><img onclick="removeBulkAttachment(\''+tempId+'\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:50px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:20px;width: 40%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox">Document Number <input class="drgTitle" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"> </div> <br/> <br/> <div id="revisionBox">Document Title <input  class="drgDesc" type="text" name="description['+fileNameTitle+revisionNo+']" value="'+documentTitle+'" size="10"> <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Description and Number can\'t be same.</div></lable></div> <br/> <br/> <div id="revisionBox">Document Revision <input type="text" name="revisionNo['+fileNameTitle+revisionNo+']" value="'+revisionNo+'" size="10" maxlength="5"> </div> <br/> <br/> <div id="revisionBox">Attribute <select class="drawingattribute3js" name="drawingattribute3js['+fileNameTitle+revisionNo+']" id="drawingattribute3js" style="width:90px;"><option value="PDF">PDF</option><option value="DWG"';
if(extensionforattr.toLowerCase() == 'dwg')
	html += 'selected="selected"';

html += '>DWG</option></select> </div> </li> <li style="margin:35px 5px 0 0;"> Attribute 2 <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL<br /> for multiple) </span></li> <li>  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="margin:35px 0px 0 5px;"><option value="">Select</option>';
						*/
						html += '<div id="selectDrawingNameHolder_'+fileNameTitle+revisionNo+'" class="divId_'+tempId+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+' divId_'+tempId+'" rel="'+uId+'" id="ID'+uId+'" ><img onclick="removeBulkAttachment(\''+tempId+'\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:50px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:20px;width: 40%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li>';
//if(extensionforattr.toLowerCase() == 'dwg')/
//	html += 'selected="selected"';

//html += '>DWG</option><option value="CAD"';
//if(extensionforattr.toLowerCase() == 'cad')
//	html += 'selected="selected"';

//html += '>CAD</option></select> </div> </li> <li style="margin:35px 5px 0 0;"> Attribute 2 <br /> <span style="font-size:10px;font-style: italic;"> (Hold CNTL<br /> for multiple) </span></li> <li>  <select class="drawingattribute2js" name="drawingattribute2js['+fileNameTitle+revisionNo+']" id="drawingattribute2js" multiple="multiple" size="3" style="margin:35px 0px 0 5px;"><option value="">Select</option>';
//						for(g=0; g<jsArrtr2.length; g++){
//							html += '<option value="'+jsArrtr2[g]+'">'+jsArrtr2[g]+'</option>';	
//						}
//						html += '</select></li> </ul>'
						html += ' <input type="hidden" name="isRemoved['+fileNameTitle+revisionNo+']" value="0" id="removeId_'+tempId+'"> </div>';
				}
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
		if(FileExt == 'msg' || FileExt == 'MSG'){
			return 1;
		}else if(FileExt == 'eml' || FileExt == 'EML'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	bulkUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		if(e.dataTransfer.files[0].size > 0){
			var status = self._preview(e.dataTransfer.files);
			if(status==false){
				return false;	
			}
			if($("#external_email").prop("checked")==true){
				if(self.all.length>1){
					jAlert('One file can uploaded at a time.');
					return false;	
				}else{
					self.all.push(e.dataTransfer.files);
				}
			}else{	
				self.all.push(e.dataTransfer.files);
			}
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
			if($("#external_email").prop("checked")==true){
				data.append("external_correspondance", 1);	
			}else{
				data.append("external_correspondance", 0);	
			}
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
					//alert(rponse);return false;
					$(".dfiles[rel='"+ids+"']").find(".progress").hide();	
					$(".dfiles[rel='"+ids+"']").find(".progress").parent().css({'border-color' : '#bcebbc', 'background-color' : '#ddffdd'});
					if (f+1 < file.length) {
						self._uploader(file,f+1);
					}else{//Send mail here
						if(rponse != ""){
							var jsonResult = JSON.parse(rponse);
							$('#imageName').append(jsonResult.anchor);
							$('#lastTd').append(jsonResult.fileArr);	
							if(jsonResult.status){
								$('#validateCheck').val(1);
								$('#subject').val(jsonResult.subject);
								$('#messageDetails').val(jsonResult.body);
								$('.nicEdit-main').html(jsonResult.body);
								hideProgress();
								setTimeout(function(){closePopup(300);}, 1000);
								//RefreshTable();
								tempId = 0;
								$('#attachEmailNew').hide();
								if($('#validateCheck').val()==1){
									$('#toL').remove();
									$('#toMT').remove();
									$('#toMSG').remove();
								}
								$('#sendMessage').click(function(){
									isFormSubmit =1;
									var recipTo = $('#recipTo').val();
									var subject = $('#subject').val();
									var messageType = $('#messageType').val();
									var messageDetails = $('.nicEdit-main').html();
									var messageDetailsText = $('.nicEdit-main').contents();
									var messageDetailsTextPDF = "";
									for(l=0;l<messageDetailsText.length;l++){
										if(messageDetailsText[l].nodeType == 3){
											if(messageDetailsTextPDF == ""){
												if(messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
													messageDetailsTextPDF = messageDetailsText[l].textContent;
											}else{
												if(messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
													messageDetailsTextPDF += '<br>'+messageDetailsText[l].textContent;
											}
										}else{
											console.log(messageDetailsText[l].textContent);	
											if(messageDetailsTextPDF == ""){
												if(messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
													messageDetailsTextPDF = messageDetailsText[l].innerHTML;
											}else{
												if(messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
													messageDetailsTextPDF += '<br>'+messageDetailsText[l].innerHTML;
											}
										}
									}
									$('#plainText').val(messageDetailsTextPDF);
									var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
									/*if(recipTo == 'Select' || recipTo === null){
										$('#recipToError').show(); isFormSubmit =0;
										return false;
									}else*/
									 if(subject == ''){
										$('#emailError').hide();
										//$('#recipToError').hide();
										$('#subjectError').show(); isFormSubmit =0;
										return false;
									}else if(messageType == ''){
										$('#subjectError').hide();
										$('#messageTypeError').show(); isFormSubmit =0;
										return false;
									}else if(messageDetails == '<br>'){
										$('#messageTypeError').hide();
										$('#messageDetailsError').show(); isFormSubmit =0;
										return false;
									}else{
										if($('#messageType').val() == 'Request For Information'){
											if($('#RFInumber').val() == ""){
												$('#RFInumberError').show(); isFormSubmit =0;
												return false;
											}else{
												$('#RFInumberError').hide();
											}
											/*if($('#fixedByDate').val() == ""){
												$('#fixedByDateError').show(); isFormSubmit =0;
												return false;
											}else{
												$('#fixedByDateError').hide();
											}*/
											if($('#RFInumber').val() == 0){
												$('#RFInumberErrorZero').show(); isFormSubmit =0;
												return false;
											}else{
												$('#RFInumberErrorZero').hide();
											}
											var newDynamicGenRefrenceNumber = 'Request for information # '+$('#RFInumber').val()+': '+$('#subject').val();
											$('#RFIstatus').val('Open');
											$('#newDynamicGenRefrenceNumber').val(newDynamicGenRefrenceNumber);
										}
										showProgress();
								/*		if(isAutoSaveActive == 1){
											showProgress();
										}
								*/
										//return true;
									}
									return false;
									//----------------------------MAIL SEND CODE START-----------------------------------
									var emailAttachedAjax = document.getElementById('emailAttachedAjax').value; 
									var recipTo = $('#recipTo').val();
									var recipCC = $('#recipCC').val();
									var subject = $('input[name=subject]').val();
									var purchaserLocation = $('input[name=purchaserLocation]').val();
									var tags = $('input[name=tags]').val();
									var companyTag = $('input[name=companyTag]').val();
									var messageType = $('#messageType').val();
									var newDynamicGenRefrenceNumber = $('input[name=newDynamicGenRefrenceNumber]').val();
									var plainText = $('input[name=plainText]').val();
									var composeId = $('input[name=composeId]').val();
									var RFInumber = $('input[name=RFInumber]').val();
									var fixedByDate = $('input[name=fixedByDate]').val();
									var RFIstatus = $('input[name=RFIstatus]').val();
									var correspondenceNumber = $('input[name=correspondenceNumber]').val();
									//var messageDetails = $('#messageDetails').val();
									var removeAttachment = $('input[name=removeAttachment]').val();
									var save = $('input[name=save]').val();
									
							$.ajax({
								url:"compose_ajax.php",
								data:{subject:subject,emailAttachedAjax:emailAttachedAjax,recipTo:recipTo,recipCC:recipCC,purchaserLocation:purchaserLocation,tags:tags,companyTag:companyTag,messageType:messageType,newDynamicGenRefrenceNumber:newDynamicGenRefrenceNumber,plainText:plainText,composeId:composeId,RFInumber:RFInumber,fixedByDate:fixedByDate,RFIstatus:RFIstatus,correspondenceNumber:correspondenceNumber,messageDetails:messageDetails,removeAttachment:removeAttachment,save:save,submit:'add'},
								type:"post",
								beforeSend: function(){
								 showProgress();
								},
								success:function(data){
									//alert(data);return false;
									jAlert('Mail Send Successfully.');
									$("#imageName").html('');
									$('.nicEdit-main').html('')
									$("#subject").val('');
									$('#recipTo').val('');
									$('#recipCC').val('');
									$(".result-selected").each(function(){
										var rId = $(this).attr('id');		
										$("#"+rId).removeClass('group-option result-selected');		
										$("#"+rId).addClass('active-result group-option');		   
									});		
									//$(".search-field").html('<input class="default" type="text" style="width: 65px;" autocomplete="off" value="Select">');		
									$('.search-choice').remove();
									hideProgress();
									window.location = 'pms.php?sect=sent_box';
								}

							});

	//----------------------------MAIL SEND CODE ENDS-----------------------------------
	});
							}else{
								hideProgress();
								setTimeout(function(){closePopup(300);}, 1000);
								//jAlert(jsonResult.msg);
								
								return false;	
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
		console.log('here here here');
		console.log(testFxArr);
		if(testFxArr.length > 0 && testFxArr[0]!=''){
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
