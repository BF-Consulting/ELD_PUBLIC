
jQuery(document).ready(function($) {

  $('#upload_form').on('submit', function(event){

    event.preventDefault();
    $('#spinner-load-error-file').hide();
    $('#spinner-load-success').hide();
    $('#spinner-load').show();
    $.ajax({
      url:"/uploadBaseFile",
      type:"POST",
      data:new FormData(this),
      dataType:'json',
      contentType:false,
      cache:false,
      processData:false,
      success:function(data,status,xhr)
      {
        
        if(data['error'] == '2'){
          $('#spinner-load').hide();
          $('#spinner-load-error-file').show();
        }else{
          $('#spinner-load').hide();
          $('#spinner-load-success').show();
        }
        //var rep = JSON.parse(data)
      },
      beforeSend: function(){
        //$('.loader').show();
      }
    });

  });
  $('#upload_form_comparator').on('submit', function(event){
  
    event.preventDefault();
    $('#spinner-load-error-file').hide();
    $('#spinner-load-success').hide();
    $('#spinner-load').show();
    $.ajax({
      url:"/resultat",
      type:"POST",
      data:new FormData(this),
      dataType:'json',
      contentType:false,
      cache:false,
      processData:false,
      success:function(data,status,xhr)
      {
        
        if(data['error'] == '2'){
          $('#spinner-load').hide();
          $('#spinner-load-error-file').show();

        }else{
          $('#spinner-load').hide();
          $('#spinner-load-success').show();
          
          window.location.reload();
        }
        //var rep = JSON.parse(data)
      },
      beforeSend: function(){
        //$('.loader').show();
      }
    });

  });
});