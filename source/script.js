$(document).ready(function(){
        
	$('#contact-form').jqTransform();

	$("button").click(function(){

		$(".formError").hide();

	});

	var use_ajax=true;
	$.validationEngine.settings={};

	$("#contact-form").validationEngine({
		inlineValidation: false,
		promptPosition: "centerRight",
		success :  function(){use_ajax=true},
		failure : function(){use_ajax=false;}
	 })

	$("#contact-form").submit(function(e){
			
			if(use_ajax)
			{
				$('#loading').css('visibility','visible');
				$.post('submit.php',$(this).serialize()+'&ajax=1',
				
					function(data){
						if(parseInt(data)==-1)
                                                {
							$.validationEngine.buildPrompt("#captcha","* Numero di controllo errato!","error");
                                                        $('#loading').css('visibility','hidden');

						}	
						else if(parseInt(data)==-2)
						{
							$.validationEngine.buildPrompt("#email","* Utente non trovato!","error");
                                                        $('#loading').css('visibility','hidden');
						}
                                                else
						{
							$("#contact-form").hide('slow').after('<h1>Grazie!</h1><br><h2>Controlla la tua casella elettronica.</h2><br><h2>Clicca in basso sulla X o lateralmente per uscire...</h2>');
                                                        						$('#loading').css('visibility','hidden');
						}
						
					}
				
				);
			}
			e.preventDefault();
	})

});