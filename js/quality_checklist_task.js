$(document).ready(function(){
  
});

function getAllTask(project_id){
  $.post('edit_task_qa_search.php?antiqueID='+Math.random(), 'project_id='+project_id).done(function(data) {
    console.log(data,'<<==data==');
    if(data != 'Duplicate task'){
      
    }
  });
}