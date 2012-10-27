jQuery(function() {
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=templateFilme&jsoncallback=?", function(templates) {
					jQuery.each(templates, function (i, template) {
						ich.addTemplate(template.nome, template.string);
					});
				});
			});
			jQuery(function() {
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=datasDisponiveis&jsoncallback=?", function(data) {
					jQuery("#data").append('<option value="">Selecione uma data</option>');
					jQuery.each(data,function(i,data){
						dataformat = data.split("-");
						jQuery("#data").append('<option value="'+data+'">'+dataformat[2]+'/'+dataformat[1]+'/'+dataformat[0]+'</option>');
					});
					jQuery("label[for='data']").hide();
				});
			});
			jQuery(function() {
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=filmesDisponiveis&jsoncallback=?",	function(filme) {
					jQuery("#filme").append('<option value="">Todos os filmes</option>');
					jQuery.each(filme,function(i,filme){
						jQuery("#filme").append(
							'<option value="'+filme.id_filme+'">'+filme.nome+'</option>'
						);
					});
					jQuery("label[for='filme']").hide();
				});
			});
			jQuery(function() {
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=locaisDisponiveis&jsoncallback=?",	function(local) {
					jQuery("#local").append('<option value="">Todos os cinemas</option>');
					jQuery.each(local,function(i,local){
						jQuery("#local").append(
							'<option value="'+local.id_localidade+'">'+local.nome+'</option>'
						);
					});
					jQuery("label[for='local']").hide();
				});
			});
			
			function limpa(){
				jQuery("#ichfilme, #listafilmes, #nome, #alert").html("");
			}
			
			function imprimeHorariosCinemas(id, data){
				var nome = '';
				var vazio = true;
				var div = '#'+id+' .h';
				for(horarios in data){
					var horario = data[horarios];
					var hora = horario.horario.split("");
					if(horario.nome != nome){
					jQuery(div).append("<br/><a href='#' class='cinema' id='"+horario.id_localidade+"'>&raquo;"+horario.nome+" </a>");
					jQuery(div).append(hora[0]+hora[1]+":"+hora[2]+hora[3]);
					vazio = false;
					} else {
					jQuery(div).append(" - "+hora[0]+hora[1]+":"+hora[2]+hora[3]);
					}				
					nome = horario.nome;
				}
				if (vazio){
					jQuery("p.horarios").hide();
					jQuery(div).append("<b>Esse filme não está disponível nessa data. Tente buscar por outra data.</b>");
				}
			}
			
			function imprimeHorariosSimples(id, data){
				var nome = '';
				var div = '#'+id+' .h';
				for(horarios in data){
					var horario = data[horarios];
					var hora = horario.horario.split("");
					if(horario.nome != nome){
					jQuery(div).append("<br/>");
					jQuery(div).append(hora[0]+hora[1]+":"+hora[2]+hora[3]);
					} else {
					jQuery(div).append(" - "+hora[0]+hora[1]+":"+hora[2]+hora[3]);
					}
					nome = horario.nome;
				}
			}
			
			function pesquisaFilme(filme, datas){
				limpa();
				jQuery('#load').show();
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=pesquisaFilme&filme="+filme+"&data="+datas+"&jsoncallback=?", function(data) {
					var filme = ich.completo(data[1][0]);
					jQuery('#ichfilme').append(filme);
					var filmeHoras = data[0];
					var hnome = '';
					var id = data[1][0][0];
					imprimeHorariosCinemas(id, filmeHoras);
					jQuery('#load').hide();
					jQuery('input[type=submit]').removeAttr('disabled');
				});
			}
		
			function pesquisaFilmeLocal(filme, local, datas){
				limpa();
				jQuery('#load').show();
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=pesquisaFilmeLocal&filme="+filme+"&local="+local+"&data="+datas+"&jsoncallback=?", function(data) {
					var cinema = jQuery('#local option[value='+local+']').text();
					jQuery('#ichfilme').append('<h4>'+cinema+'</h4>');
					var filmeData = data[1][0];
					var id = data[1][0][0];
					var filmeHoras = data[0];
					if(filmeHoras == ''){
						jQuery('#alert').append("<p>"+filmeData.nome+" não está em cartaz nesse cinema</p>");
						jQuery('#alert').append("<a class='cinema' id='"+local+"' href='#'>Veja os outros filmes desse cinema</a> ou <a class='filme' id='"+id+"' href='#'>Veja aonde está passando esse filme</a>");
					} else {
					var filme = ich.completo(filmeData);
					jQuery('#ichfilme').append(filme);
					imprimeHorariosCinemas(id, filmeHoras);
					}
					jQuery('#load').hide();
					jQuery('input[type=submit]').removeAttr('disabled');
				});
			}
			
			function pesquisaLocal(local, datas){
				limpa();
				jQuery('#load').show();
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=pesquisaLocal&local="+local+"&data="+datas+"&jsoncallback=?",	function(data) {
					var cinema = jQuery('#local option[value='+local+']').text();
					jQuery('#ichfilme').append('<h4>'+cinema+'</h4>');
					for(filmes in data){
						var filmeData = data[filmes];
						var filme = ich.filme(filmeData);
						jQuery('#ichfilme').append(filme);
						imprimeHorariosSimples(filmeData.id_filme, filmeData.horario);
					}
					jQuery('#load').hide();
					jQuery('input[type=submit]').removeAttr('disabled');
				});
			}
			
			function pesquisa(datas){
				limpa();
				jQuery('#load').show();
				jQuery.getJSON("http://brasilia.deboa.com/mobileinterface.php?f=pesquisa&data="+datas+"&jsoncallback=?",	function(data) {
					for(filmes in data){
						var filmeData = data[filmes];
						var filme = ich.filme(filmeData);
						jQuery('#ichfilme').append(filme);
						imprimeHorariosCinemas(filmeData.id_filme, filmeData.horario);
						jQuery('#load').hide();
					}
					jQuery('input[type=submit]').removeAttr('disabled');
				});
			}
			
			
			function controle(filme, local, data){
				
				// Se a data estiver vazia abre alerta e volta.
				
				if(data==''){
					
					
					
					alert('Escolha uma data!');			
					window.history.back();
					


				} else{
					jQuery('input[type=submit]').attr('disabled', 'disabled');
					if(filme==''){
						if(local==''){
							pesquisa(data);
						} else {
							pesquisaLocal(local, data);
						}
					} else {
						if(local==''){
							pesquisaFilme(filme, data);
						} else {
							pesquisaFilmeLocal(filme, local, data);
						}
					}
				}
			}
			