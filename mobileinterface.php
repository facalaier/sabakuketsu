 <?php
	// banco

	$host = "localhost";
	$db = "cinema";
	$user = "cinema";
	$pass = "VLZ1604vlz";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error()); 
	
	// chamando os metodos


	if(isset($_GET['f']) && !empty($_GET['f'])) {
		if(function_exists($_GET['f'])) {
			$_GET['f']();
		}
	}


	
	/*
	
	$template['string'] = '<div id="{{id_filme}}"><img src="http://brasilia.deboa.com/imagens/cinema/{{id_filme}}.jpg"/><h2><a href="#" id="{{id_filme}}" class="filme">{{nome}}</a></h2><p>Gênero: {{genero}}</p><p>Classificação: {{classificacao}} anos</p><a href="#" id="{{id_filme}}" class="filme">Ver sinopse e trailer</a><br/><p class="horarios">Horário(s): </p><div class="h"></div></div>';
	
	$template['string'] = '<div id="{{id_filme}}"><img src="http://brasilia.deboa.com/imagens/cinema/{{id_filme}}.jpg"/><h2><a href="#" id="{{id_filme}}" class="filme">{{nome}}</a></h2><p>Gênero: {{genero}}</p><p>Classificação: {{classificacao}} anos</p><p>{{descricao}}</p><br/><p class="horarios">Horário(s): </p><div class="h"></div><p>Trailer: </p>{{trailer}}</div>';
	
	*/
	
	
	function templateFilme(){
		$template = array();
		$result = array();
		$template['nome'] = 'filme';
		$template['string'] = '<div id="{{id_filme}}">
<table width="320px" border="0" cellspacing="0" cellpadding="0" bgcolor="#E6E6E6" background="/imagens/back_nome_cinema.png">   <tr> <td colspan="2" align="center" background="/imagens/back_nome_cinema.png"></td> </tr>    <tr> <td rowspan="2"><img src="http://brasilia.deboa.com/imagens/cinema/{{id_filme}}.jpg" width="135px" height="110px"/></td> <td>  <a href="#" id="{{id_filme}}" class="filme"><font color="#333333" size="3"><b>{{nome}}</b></font></a><br /> <font color="#333333" size="2"> <b>Gênero:</b> {{genero}}<br /> <b>Classificação:</b> {{classificacao}} anos  </font> </td> </tr> <tr> <td><a href="#" id="{{id_filme}}" class="filme"><b>Ver sinopse</b></a></td> </tr> <tr> <td colspan="2"> <p class="horarios"><font color="#333333" size="3"><b>Horário(s):</b></font></p><div class="h"></div> </td> </tr> </table>
</div>';
		$result[] = $template;
		$template['nome'] = 'completo';
		$template['string'] = '<div id="{{id_filme}}"><img src="http://brasilia.deboa.com/imagens/cinema/{{id_filme}}.jpg"/><h2><a href="#" id="{{id_filme}}" class="filme">{{nome}}</a></h2><p>Gênero: {{genero}}</p><p>Classificação: {{classificacao}} anos</p><p>{{descricao}}</p><br/><p class="horarios">Horário(s): </p><div class="h"></div></div>';
		$result[] = $template;
		$result = json_encode($result);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $result . ')'; 
	}
	
	function maiorData(){
		$datas = mysql_query("SELECT dia, mes, ano FROM relacional");
		$data = array();
		while($l = mysql_fetch_array($datas)){
			$dia = $l['dia'];
			$mes = $l['mes'];
			$ano = $l['ano'];
			$data[] = mktime(0, 0, 0, $mes, $dia, $ano);
		}
		return max($data);
	}
	
	function datas() {
		$maxData = maiorData();
		$atual  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$datas = array();
		$i = 1;
		while ($atual <= $maxData) {
			$data = date("Y-m-d", $atual);
			$datas[] = $data;
			$atual = mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y"));
			$i++;
		}
		return $datas;
	}
	
	function filmesDisponiveis() {
		$datas = datas();
		$filmes = array();
		foreach ($datas as $dia){
			$val = explode("-", $dia);
			$d = $val[2];
			$m = $val[1];
			$a = $val[0];
			$filmes_sql = mysql_query("SELECT filmes.nome, filmes.id_filme FROM filmes JOIN relacional ON filmes.id_filme = relacional.id_filme WHERE relacional.dia = $d AND relacional.mes = $m AND relacional.ano = $a ORDER BY filmes.nome");
			while($filme = mysql_fetch_array($filmes_sql)){
				if(!in_array($filme, $filmes)){
					$filmes[] = $filme;
				}
			}
		}
		$filmes = json_encode($filmes);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $filmes . ')';
	}
	
	function datasDisponiveis() {
		$datas = datas();
		$datas = json_encode($datas);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $datas . ')';
	}
	
	function locaisDisponiveis() {
		$locais = array();
		$locais_sql = mysql_query("SELECT id_localidade, nome FROM localidade ORDER BY nome");
		while($local = mysql_fetch_array($locais_sql)){
			$locais[] = $local;
		}
		$locais = json_encode($locais);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $locais . ')';
	}
	
	function pesquisaFilme() {
		$data = $_GET['data'];
		$filme_id = $_GET['filme'];
		$locais = array();
		$localidades = array();
		$fim = array();
		$data  = explode("-", $data);
		$ano = $data[0];
		$mes = $data[1];
		$dia = $data[2];
		$filme_sql = mysql_query("SELECT * FROM filmes WHERE id_filme = $filme_id");
		while($filme = mysql_fetch_array($filme_sql)){
			$filmes[] = $filme;
		}
		$locais_sql = mysql_query("SELECT localidade.id_localidade, localidade.nome, relacional.horario FROM localidade JOIN relacional ON localidade.id_localidade = relacional.id_localidade WHERE relacional.id_filme = $filme_id AND relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano");
		while($local = mysql_fetch_array($locais_sql)){
			$localidades[] = $local;
		}
		$fim[] = $localidades;
		$fim[] = $filmes;
		$fim = json_encode($fim);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $fim . ')';
	}
	
	function pesquisaFilmeLocal() {
		$data = $_GET['data'];
		$filme_id = $_GET['filme'];
		$local_id = $_GET['local'];
		$locais = array();
		$localidades = array();
		$fim = array();
		$data  = explode("-", $data);
		$ano = $data[0];
		$mes = $data[1];
		$dia = $data[2];
		$filme_sql = mysql_query("SELECT * FROM filmes WHERE id_filme = $filme_id");
		while($filme = mysql_fetch_array($filme_sql)){
			$filmes[] = $filme;
		}
		$locais_sql = mysql_query("SELECT localidade.id_localidade, localidade.nome, relacional.horario FROM localidade JOIN relacional ON localidade.id_localidade = relacional.id_localidade WHERE relacional.id_localidade = $local_id AND relacional.id_filme = $filme_id AND relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano");
		while($local = mysql_fetch_array($locais_sql)){
			$localidades[] = $local;
		}
		$fim[] = $localidades;
		$fim[] = $filmes;
		$fim = json_encode($fim);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $fim . ')';
	}
	
	function pesquisa() {
		$data = $_GET['data'];
		$data  = explode("-", $data);
		$ano = $data[0];
		$mes = $data[1];
		$dia = $data[2];
		$filmes_id = array();
		$filmes = array();
		$result = array();
		$filmes_id_sql = mysql_query("SELECT DISTINCT filmes.id_filme FROM filmes JOIN relacional ON filmes.id_filme = relacional.id_filme WHERE relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano ORDER BY filmes.nome");
		while($filme_id = mysql_fetch_array($filmes_id_sql)){
			$filmes_id[] = $filme_id;
		}
		foreach($filmes_id as $filme_id){
			$localidades = array();
			$filme_id = $filme_id[0];
			$locais_sql = mysql_query("SELECT localidade.id_localidade, localidade.nome, relacional.horario FROM localidade JOIN relacional ON localidade.id_localidade = relacional.id_localidade WHERE relacional.id_filme = $filme_id AND relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano");
			while($local = mysql_fetch_array($locais_sql)){
				$localidades[] = $local;
			}
			$filme_sql = mysql_query("SELECT * FROM filmes WHERE id_filme = $filme_id");
			while($filme = mysql_fetch_array($filme_sql)){
				$filme["horario"] = $localidades;
				$filmes[] = $filme;
			}
		}
		$filmes = json_encode($filmes);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $filmes . ')';	
	}
	
	function pesquisaLocal() {
		$data = $_GET['data'];
		$local_id = $_GET['local'];
		$data  = explode("-", $data);
		$ano = $data[0];
		$mes = $data[1];
		$dia = $data[2];
		$filmes_id = array();
		$filmes = array();
		$result = array();
		$filmes_id_sql = mysql_query("SELECT DISTINCT filmes.id_filme FROM filmes JOIN relacional ON filmes.id_filme = relacional.id_filme WHERE relacional.id_localidade = $local_id AND relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano ORDER BY filmes.nome");
		while($filme_id = mysql_fetch_array($filmes_id_sql)){
			$filmes_id[] = $filme_id;
		}
		foreach($filmes_id as $filme_id){
			$localidades = array();
			$filme_id = $filme_id[0];
			$locais_sql = mysql_query("SELECT localidade.id_localidade, localidade.nome, relacional.horario FROM localidade JOIN relacional ON localidade.id_localidade = relacional.id_localidade WHERE relacional.id_filme = $filme_id AND relacional.id_localidade = $local_id AND relacional.dia = $dia AND relacional.mes = $mes AND relacional.ano = $ano");
			while($local = mysql_fetch_array($locais_sql)){
				$localidades[] = $local;
			}
			$filme_sql = mysql_query("SELECT * FROM filmes WHERE id_filme = $filme_id");
			while($filme = mysql_fetch_array($filme_sql)){
				$filme["horario"] = $localidades;
				$filmes[] = $filme;
			}
		}
		$filmes = json_encode($filmes);
		header("Content-type: text/javascript"); 
		echo $_GET['jsoncallback'] . '(' . $filmes . ')';
	}
	
	mysql_close();

?>