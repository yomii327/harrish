var testFxArr = Array();
var tempArrKey = 0;
var tempId = 0;

function multipleAttachment(config) {
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	
	multipleAttachment.prototype._init = function(){
		if (window.File && 
			window.FileReader && 
			window.FileList && 
			window.Blob) {
			var inputId = $("#"+this.config.form).find("input[type='file']").eq(0).attr("id");
			document.getElementById(inputId).addEventListener("change", this._read, false);
			document.getElementById(this.config.dragArea).addEventListener("dragover", function(e){ e.stopPropagation(); e.preventDefault(); }, false);
			document.getElementById(this.config.dragArea).addEventListener("drop", this._dropFiles, false);
			document.getElementById(this.config.form).addEventListener("submit", this._submit, false);
		} else
			console.log("Browser supports failed");
	}
	
	multipleAttachment.prototype._submit = function(e){
		e.stopPropagation(); e.preventDefault();
		self._startUpload();
	}
	
	multipleAttachment.prototype._preview = function(data){
		self._readcount(data);
		this.items = data;
        var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

        if (this.items.length > 0) {
            var html = "";
            var uId = "";
            
            for (var i = 0; i < this.items.length; i++) {

                tempId++;
                relId = this.items[i].name._unique();
                uId = this.items[i].name._unique();

                uId = uId.replace(/\./gi, "");
                var sampleIcon = '<img src="images/pdf-24.png" />';
                if (FileExt == 'dwg' || FileExt == 'DWG')
                    sampleIcon = '<img src="images/dwg-24.png" />';
                if (FileExt == 'cad' || FileExt == 'CAD')
                    sampleIcon = '<img src="images/cad-24.png" />';
                var errorClass = "";
                //alert(this.items[i]);
                if (typeof this.items[i] != undefined) {					
                    var fileTitle = this.items[i].name;
                    //alert(fileTitle);
                    tempArr = fileTitle.split(".");
                    //document.write(tempArr[1]);
                    //document.write(this.items[i].name);
                    var lastEle = tempArr.pop();
                    var extensionforattr = lastEle;
                    var processFileName = tempArr.join(".");
                    //document.write(processFileName);
                    var fileNameTitle = processFileName;
                    if(lastEle=='zip' || lastEle=='ZIP' || lastEle == 'exe' || lastEle == 'EXE'){
                    	jAlert('Invalid file format -  zip and exe');
                    	//return false;
                    }else{
                    		//document.write(processFileName);
		                    var revisionNo = '';
		                    if (processFileName.indexOf("[") !== -1) {
		                        if (processFileName.indexOf("]") !== -1) {
		                            tempArr = processFileName.split("[");
		                            var lastEle = tempArr.pop();
		                            var fileNameTitle = tempArr.join("[").trim();
		                            var revisionNoArr = lastEle.split("]");
		                            revisionNo = revisionNoArr[0];
		                        }
		                    }
		                    if (processFileName.indexOf("(") !== -1) {
		                        if (processFileName.indexOf(")") !== -1) {
		                            tempArr = processFileName.split("(");
		                            var lastEle = tempArr.pop();
		                            var fileNameTitle = tempArr.join("(").trim();
		                            var revisionNoArr = lastEle.split(")");
		                            revisionNo = revisionNoArr[0];
		                        }
		                    }
		                    if (processFileName.indexOf("{") !== -1) {
		                        if (processFileName.indexOf("}") !== -1) {
		                            tempArr = processFileName.split("{");
		                            var lastEle = tempArr.pop();
		                            var fileNameTitle = tempArr.join("{").trim();
		                            var revisionNoArr = lastEle.split("}");
		                            revisionNo = revisionNoArr[0];
		                        }
		                    }
		                    if (revisionNo == "") {
		                        if (processFileName.indexOf("_") !== -1) {
		                            tempArr = processFileName.split("_");
		                            var lastEle = tempArr.pop();
		                            var documentTitle = tempArr.pop();
		                            var fileNameTitle = tempArr.join("_").trim();
		                            revisionNo = lastEle;
		                        } else {
		                            tempArr = processFileName.split("-");
		                            var lastEle = tempArr.pop();
		                            var documentTitle = tempArr.pop();
		                            var fileNameTitle = tempArr.join("-").trim();
		                            revisionNo = lastEle;
		                        }
		                    }
		                    
		                    //document.write(tempArr);
		                    
			                    html += '<div id="selectDrawingNameHolder_' + fileNameTitle + revisionNo + '" class="divId_' + tempId + '" style="height:15px;"></div> <div class="bulkfiles' + errorClass + ' divId_' + tempId + '" rel="' + relId + '" id="ID' + uId + '" ><img onclick="removeMultipleAttachment(\'' + tempId + '\');" title="Remove Attachment" src="images/delete.png"  style="float:right; margin-top:50px; margin-right:5px;"> <ul id="filePanel" > <li style="margin-top:20px;width: 40%;"> <h3 id="uploaderBulk">' + sampleIcon + '<span>' + this.items[i].name + '</span></h3> </li>';                      
			                    html += ' <input type="hidden" name="isRemoved[' + fileNameTitle + revisionNo + ']" value="0" id="removeId_' + tempId + '"> </div>';
		                	
		                    //$("#innerDiv").append(html);
		            }
                }
            }
            $("#innerDiv").append(html);
        }
	}
	
	multipleAttachment.prototype._readcount = function(file) {
			console.log(file);//return false;
			//alert(file);
			this.items = file;
        	var FileExt = (/[.]/.exec(file[0].name)) ? /[^.]+$/.exec(file[0].name) : undefined;
	        if (this.items.length > 0) {
	            var html = "";
	            var uId = "";
	            var totalcount = 0;
	            for (var i = 0; i < this.items.length; i++) {
	                tempId++;
	                if(this.items[i] === undefined){
					    xs[offset] = xs[i]
					    offset++
					}
	                relId = this.items[i].name._unique();
	                uId = this.items[i].name._unique();
	                uId = uId.replace(/\./gi, "");
	                var sampleIcon = '<img src="images/pdf-24.png" />';
	                if (FileExt == 'dwg' || FileExt == 'DWG')
	                    sampleIcon = '<img src="images/dwg-24.png" />';
	                if (FileExt == 'cad' || FileExt == 'CAD')
	                    sampleIcon = '<img src="images/cad-24.png" />';
	                var errorClass = "";
	                //alert(this.items[i]);
	                if (typeof this.items[i] != undefined) {					
	                    var fileTitle = this.items[i].name;
	                    //alert(fileTitle);
	                    //alert(file.indexOf(fileTitle));
	                    //this.items[i].splice(i, 1);
	                    tempArr = fileTitle.split(".");
	                    var lastEle = tempArr.pop();
	                    if(lastEle=='zip' || lastEle=='ZIP' || lastEle == 'exe' || lastEle == 'EXE'){     	
                    		jAlert('Invalid file format -  zip and exe');
                    	}else{
                    		totalcount = parseInt(totalcount)+1;
                    	}
	                }
	            }
	            //alert(totalcount);
	        } 
	        totalcount = parseInt(totalcount) + parseInt($("#addAttachmentCount").val());   
	        $("#addAttachmentCount").val(totalcount);
			$("#fileCount").html(totalcount +' files selected');
	        return false;
			//var fCount = $("#addAttachmentCount").val();		
			//fCount = parseInt(fCount) + parseInt(file.length);
			//$("#addAttachmentCount").val(fCount);
			//$("#fileCount").html(fCount +' files selected');
	}

	multipleAttachment.prototype.remove = function(value) {
	       this.splice(this.indexOf(value), 1);
	       return true;
	};	

	multipleAttachment.prototype._read = function(evt) {
		if(evt.target.files){
			self._preview(evt.target.files);
			self.all.push(evt.target.files);
		} else 
			console.log("Failed file reading");
	}
	
	multipleAttachment.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		if (FileExt == 'zip' || FileExt == 'ZIP') {
			return -1;
		} else if (FileExt == 'exe' || FileExt == 'EXE') {
			return -1;
		} else {
			return 1;
		}
		/*if (FileExt == 'pdf' || FileExt == 'PDF') {
            return 1;
        } else if (FileExt == 'eml' || FileExt == 'EML') {
            return 1;
        } else if(FileExt == 'doc' || FileExt == 'doc') {
			return 1;
		} else if(FileExt == 'docx' || FileExt == 'DOCX') {
			return 1;
		} else if(FileExt == 'jpg' || FileExt == 'JPG') {
			return 1;
		} else if(FileExt == 'jpeg' || FileExt == 'JPEG') {
			return 1;
		} else if(FileExt == 'png' || FileExt == 'PNG') {
			return 1;
		} else if(FileExt == 'csv' || FileExt == 'CSV') {
			return 1;
		} else {
            var arr = this.config.support.split(",");
            return arr.indexOf(format);
        }*/
	}
	
	multipleAttachment.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		self._preview(e.dataTransfer.files);
		self.all.push(e.dataTransfer.files);
	}
	
	multipleAttachment.prototype._uploader = function(file,f){
		//if(typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0){
			var data = new FormData();
			var ids = file[f].name._unique();
			var fileTitle = file[f].name.trim();
			tempArr = fileTitle.split(".");
	        var lastEle = tempArr.pop();
	        //alert(lastEle);
	        //if(lastEle=='zip' || lastEle=='ZIP' || lastEle == 'exe' || lastEle == 'EXE'){
	        	//do nothing
	        //	return true;
	        //}else{
				var totUnsupportFiles = 0;
				data.append('file',file[f]);
				data.append('index',ids);
				//alert(this.config.uploadUrl);
				$(".dfiles[rel='"+ids+"']").find(".progress").show();
				$.ajax({
					type:"POST",
					url:this.config.uploadUrl,
					data:data,
					cache: false,
					contentType: false,
					processData: false,
					success:function(rponse){
						$("#"+ids).hide();
						var obj = $(".bulkfiles").get();
						$.each(obj,function(k,fle){
							var $data = JSON.parse(rponse);
							if($(fle).attr("rel") == $data.rel){
	                            var attachNo = parseInt($('#emailAttachedAjax').val());
								$('#imageName').append(' <span id="' + attachNo + '"><a href="attachment/' + $data.filePath + '" target="_blank" style="color:#06C;" class="thickbox" >' + $data.imageName + '</a>[<a onclick="removeMaessage(' + attachNo + ', 0, \'' + $data.uploadedImageName + '\');" style="color:red;">X</a>]</span>');
								attachNo = attachNo + 1;
								$('#emailAttachedAjax').val(attachNo);
							}
						});
						if (f+1 < file.length) {	
									//alert(file);					
									//if(lastEle !='zip' || lastEle !='zip'){
									   self._uploader(file,f+1);			
									//}   
									totUnsupportFiles = parseInt(totUnsupportFiles)+1;						
						} else {
							closePopup(100);
						}
					}
				});
			//}
		//} else {
			//console.log("Invalid file format - "+file[f].name);
		//	jAlert('Invalid file format - '+ file[f].name);
			//closePopup(5000);
		//}
		//alert(totUnsupportFiles);
	}
	
	multipleAttachment.prototype._startUpload = function(){
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

function initMultipleAttachment(config){
	new multipleAttachment(config);
}
