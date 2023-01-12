<?php

//ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
error_reporting(E_ALL);
$charset = 'UTF-8';
mb_internal_encoding($charset);
setlocale(LC_ALL, 'pt_BR.'.$charset, 'pt_BR');
mb_language('uni');

//echo '<pre>';
$consulta = $_GET['c'];
$params = $_GET['p'];
if ($params == '' or is_null($params)) {
    $params = '';
} else {
    $params = explode(';', $_GET['p']);
}
$ordem = $_GET['o'];

$sqls = array(
// 0 Pontuação até aproveitamento    
  // opc_num = 1 = Pontuação 
  // opc_num = 2 = Melhor ataque
  // opc_num = 3 = Saldo de Gols
  // opc_num = 4 = Mais Vitórias
  // opc_num = 5 = Mais Jogos
  // opc_num = 6 = Aproveitamento
  // opc_num = 7 = Total por Estado/País
  // opc_num = 9 = Campeões
  // opc_num = 20 = Comparação entre clubes (só pontuação)
  // opc_num = 23 = Rebaixados
  // opc_num = 24 = Promovidos
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, d.est_estado, clu_fund, clu_estadi, " .
  "       sum(pon_pontos + pon_perdid) as pon_pontos, " .
  "       sum(pon_jogos) as pon_jogos, " .
  "       sum(pon_vitori) as pon_vitori, " .
  "       sum(pon_empate) as pon_empate, " .
  "       sum(pon_derrot) as pon_derrot, " .
  "       sum(pon_golpro) as pon_golpro, " .
  "       sum(pon_golcon) as pon_golcon, " .
  "       sum(pon_saldo) as pon_saldo, " .
  "       round( " .
  "             sum(pon_pontos + pon_perdid) * 100 / " .
  "             sum(pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      b.clu_estado = d.clu_estado and " . 
  "      pon_ano between ? and ? " .
  "group by clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, d.est_estado, clu_fund, clu_estadi ",

// 1 Pontuação de um ano só
  // opc_num = 1
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, a.pon_grupo, a.pon_ano, d.est_estado,  " .
  "       (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, pon_vitori, " .
  "       pon_empate, pon_derrot, pon_golpro, " .
  "       pon_golcon, pon_saldo, pon_classi, clu_fund, clu_estadi, " .
  "       (case when tor_golvit <> 'V' then pon_saldo else pon_vitori end) as sequen, " .
  "       round( " .
  "			    (pon_pontos + pon_perdid) * 100 / " .
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove, " .
  "       pon_observ, pon_artilh, " .
  "      (case when a.pon_grupo > '' then a.pon_grupo else 'ZZZZZ' end) as grupo " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d  " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      b.clu_estado = d.clu_estado and " .
  "      pon_ano between ? and ? ",
  	
// 2 Estado/país
  // opc_num = 7
  "select e.est_estado, " .
  "       (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) as clu_estado, " .
  "       0 as ordem, " .
  "       sum(pon_pontos + pon_perdid) as pon_pontos, " .
  "       sum(pon_jogos) as pon_jogos, " .
  "       sum(pon_vitori) as pon_vitori, " .
  "       sum(pon_empate) as pon_empate, " .
  "       sum(pon_derrot) as pon_derrot, " .
  "       sum(pon_golpro) as pon_golpro, " .
  "       sum(pon_golcon) as pon_golcon, " .
  "       sum(pon_saldo) as pon_saldo, " .
  "       round( " .
  "             sum(pon_pontos + pon_perdid) * 100 / " .
  "             sum(pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove " .
  "from PONTOS a, CLUBES b, ESTADOS c, TORNEIOS d, ESTADOS e " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      a.tor_codigo = d.tor_codigo and " .
  "      e.clu_estado = (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) and " .
  "      pon_ano between ? and ? " .
  "group by e.est_estado, clu_estado, ordem " .
  "order by pon_aprove desc, pon_jogos desc, pon_pontos desc, pon_vitori desc, pon_saldo desc, pon_golpro desc, e.est_estado",
				  
// 3 Ranking 1o ao 10º
  // opc_num = 8
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, c.est_estado, " .
  "       sum(11 - pon_classi) as pon_classi " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      pon_classi < 11 and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_ano between ? and ? " .
  "group by a.clu_ordem, clu_nome, b.clu_estado, clu_cidade, c.est_estado " .
  "order by pon_classi desc, clu_nome",
					 
// 4 Campeões // DESATIVADO
  // opc_num = 9
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, d.est_estado, " .
  "       pon_vitori, pon_empate, pon_derrot, pon_golpro, pon_golcon, pon_observ, pon_artilh, " .
  "       pon_saldo, pon_ano, pon_grupo, " .
  "       round( " .
  "             (pon_pontos + pon_perdid) * 100 / " .
  "             pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end) " .
  "             , 1) as pon_aprove " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      b.clu_estado = d.clu_estado and " .
  "      pon_classi = 1 " .
  "order by pon_ano desc",

// 5 Maiores campeões
  // opc_num = 10
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, count(*) as pon_titulo, c.est_estado,  " .
  "       GROUP_CONCAT(PON_ANO order by PON_ANO desc) as anos " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      pon_classi = 1 and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_ano between ? and ? " .
  "group by clu_nome, b.clu_estado, a.clu_ordem, c.est_estado " .
  "order by pon_titulo desc, clu_nome",

// 6 Títulos por estado/país
  // opc_num = 11
  "select e.est_estado, " .
  "   (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) as est_pais, " .
  "   count(*) as pon_titulo " .
  "from PONTOS a, CLUBES b, ESTADOS c, TORNEIOS d, ESTADOS e " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      a.tor_codigo = d.tor_codigo and " .
  "      e.clu_estado = (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) and " .
  "      pon_classi = 1 and " .
  "      pon_ano between ? and ? " .
  "group by e.est_estado, est_pais " .
  "order by pon_titulo desc, est_pais",

// 7 Mais participações
  // opc_num = 12
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, c.est_estado, count(*) as pon_titulo " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_ano between ? and ? " .
  "group by clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, c.est_estado " .
  "order by pon_titulo desc, clu_nome",
  
// 8 Invictos
  // opc_num = 13
  "select clu_nome, b.clu_estado, clu_cidade, pon_ano, pon_classi, a.clu_ordem, c.est_estado " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_derrot = 0 " .
  "order by pon_ano desc, pon_classi, clu_nome",

// 9 Artilheiros
  // opc_num = 14
  "select clu_nome, b.clu_estado, clu_cidade, pon_ano, a.clu_ordem, pon_artilh, c.est_estado " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_artilh > ' ' " .
  "order by pon_ano, pon_artilh desc",
  
// 10 Maiores artilheiros
  // opc_num = 15
  "select clu_nome, b.clu_estado, clu_cidade, pon_ano, a.clu_ordem, pon_artilh, c.est_estado " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_artilh > ' ' " .
  "order by pon_artilh desc, pon_ano",
  
// 11 Gols por Ano 
  // opc_num = 16
  "select pon_ano, sum(pon_golpro) as pon_golpro, " .
  "       round(sum(pon_jogos) / 2) as pon_jogos, " .
  "       round(sum(pon_golpro) / sum(pon_jogos) * 2, 3) as pon_media " .
  "from PONTOS " .
  "where tor_codigo = ? " .
  "group by pon_ano " .
  "order by pon_ano",
  
// 12 Mais Gols por Ano
  // opc_num = 17
  "select pon_ano, sum(pon_golpro) as pon_golpro, " .
  "       round(sum(pon_jogos) / 2) as pon_jogos, " .
  "       round(sum(pon_golpro) / sum(pon_jogos) * 2, 3) as pon_media " .
  "from PONTOS " .
  "where tor_codigo = ? " .
  "group by pon_ano " .
  "order by pon_golpro desc, pon_jogos, pon_ano",
  
// 13 Média de Gols por Ano
  // opc_num = 18
  "select pon_ano, sum(pon_golpro) as pon_golpro, " .
  "       round(sum(pon_jogos) / 2) as pon_jogos, " .
  "       round(sum(pon_golpro) / sum(pon_jogos) * 2, 3) as pon_media " .
  "from PONTOS " .
  "where tor_codigo = ? " .
  "group by pon_ano " .
  "order by pon_media desc, pon_golpro desc, pon_ano",

// 14 Clube
  // opc_num = 14
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, pon_ano, c.tor_ano, d.est_estado, " .
  "       pon_classi, (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, pon_vitori, " .
  "       pon_empate, pon_derrot, pon_golpro, " .
  "       pon_golcon, pon_saldo, " .
  "       round( " .
  "             (pon_pontos + pon_perdid) * 100 / " .
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove, " .
  "       pon_observ, pon_artilh, clu_fund, clu_estadi " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = ? and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = d.clu_estado and " .
  "      clu_nome = ? and " .
  "      b.clu_estado = ? " .
  "order by pon_ano desc",
  
// 15 Anos do torneio selecionado
  // Campeões do Ano
  "select distinct(pon_ano) as pon_ano " .
  "from PONTOS a, TORNEIOS b " .
  "where pon_classi = 1 and " .
  "      a.tor_codigo = b.tor_codigo and "  .
  "      b.con_codigo = ? " .
  "order by pon_ano",
  
// 16 Sigla do Torneio
  // Atualização de dados
  "select tor_codigo from TORNEIOS where tor_descri = ?",

// 17 Anos do torneio
  // Montando Opções
  "select distinct(pon_ano) as pon_ano " .
  "from PONTOS " .
  "where tor_codigo = ? and "  .
  "	     (pon_vitori + pon_empate + pon_derrot) > 0 " . 
  "order by pon_ano",

// 18 Verificação se tem mais de um estado/país
  // Montando opções
  "select clu_estado, count(*) as num " .
  "from PONTOS a, CLUBES b " .
  "where a.clu_ordem = b.clu_ordem and tor_codigo = ?" .
  " group by clu_estado",
  
// 19 Clubes do torneio
  // Montando opções (lista de clubes)
  "select clu_nome, clu_estado, c.clu_ordem, count(*) as num " .
  "from CLUBES c " .
  "inner join PONTOS f on c.clu_ordem = f.clu_ordem and tor_codigo = ? " .
  "group by clu_nome, clu_estado, c.clu_ordem " .
  "order by clu_nome, clu_estado",
  
// 20 Continentes NÃO DEU CERTO ESTE ORDER
  // uContin (tele inicial)
  "select con_descri, con_espano, con_franca, con_alemao, con_englis, con_codigo from CONTINENTE order by ?",

// 21 Melhores 50 campanhas entre 10 primeiros colocados
  // opc_num = 21
  "select pon_ano, clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, a.pon_grupo, d.est_estado, " . 
  "       (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, pon_vitori, " . 
  "       pon_empate, pon_derrot, pon_golpro, " . 
  "       pon_golcon, pon_saldo, pon_classi, " . 
  "       round( " . 
  "             (pon_pontos + pon_perdid) * 100 / " .
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " . 
  "				, 1) as pon_aprove, " .
  "       pon_observ, pon_artilh, clu_fund, clu_estadi " . 
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " . 
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      pon_ano between ? and ? and " .
  "      b.clu_estado = d.clu_estado and " .
  "		 a.pon_classi < 11 " .
  "order by pon_aprove desc, pon_jogos desc, pon_pontos desc, pon_golpro desc, pon_saldo desc " .
  "limit 50",
  
// 22 Campeões
  // opc_num = 9
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, d.est_estado, " .
  "       pon_vitori, pon_empate, pon_derrot, pon_golpro, pon_golcon, pon_observ, pon_artilh, " .
  "       pon_saldo, pon_ano, pon_grupo, clu_fund, clu_estadi, " .
  "       round( " .
  "             (pon_pontos + pon_perdid) * 100 / ".
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) ".
  "             , 1) as pon_aprove " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      b.clu_estado = d.clu_estado and " .
  "      pon_ano between ? and ? and " .
  "      pon_classi = 1 " .
  "order by pon_ano desc",
  
// 23 Número de clubes do torneio no ano por código
  // Clube
  "select pon_ano, count(*) as numero " .
  "from PONTOS " .
  "where tor_codigo = ? " .
  "group by pon_ano",
  
// 24 Campeões do ano
  // Campeões do Ano
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, a.pon_grupo, d.est_estado, " .
  "       (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, pon_vitori, " .
  "       pon_empate, pon_derrot, pon_golpro, " .
  "       pon_golcon, pon_saldo, pon_classi, " .
  "       round( " .
  "             (pon_pontos + pon_perdid) * 100 / " .
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove, " .
  "       pon_observ, pon_artilh, tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = c.tor_codigo and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      pon_classi = 1 and " .
  "      pon_ano = ? and " .
  "      b.clu_estado = d.clu_estado and " .
  "      c.con_codigo = ? " .
  "order by tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, a.pon_grupo",
  
// 25 Cidades do Estado/País selecionado
  // uCidades
  "select clu_cidade, count(*) as numero " .
  "from CLUBES " .
  "where clu_estado = ? " .
  "group by clu_cidade",
  
// 26 Clubes do Estado/País/Cidade selecionada
  // uClubCid
  "select clu_nome, clu_ordem, b.est_estado, clu_fund, clu_estadi " .
  "from CLUBES a, ESTADOS b " .
  "where a.clu_estado = ? and clu_cidade = ? and " .
  "      a.clu_estado = b.clu_estado " .
  "order by clu_nome",
  
// 27 Campanhas do Clube
  // uCampanha
  "select tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, a.tor_codigo, clu_ordem, count(*) as numero, " .
  "       sum(pon_pontos + pon_perdid) as pon_pontos, " .
  "       sum(pon_jogos) as pon_jogos, " .
  "       sum(pon_vitori) as pon_vitori, " .
  "       sum(pon_empate) as pon_empate, " .
  "       sum(pon_derrot) as pon_derrot, " .
  "       sum(pon_golpro) as pon_golpro, " .
  "       sum(pon_golcon) as pon_golcon, " .
  "       sum(pon_saldo) as pon_saldo, " .
  "       round( " .
  "             sum(pon_pontos + pon_perdid) * 100 / " .
  "             sum(pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove " .
  "from PONTOS a, TORNEIOS b " .
  "where clu_ordem = ? and " .
  "      a.tor_codigo = b.tor_codigo " .
  "group by tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, a.tor_codigo, clu_ordem",

// 28 Atualiza pontuação
  "update PONTOS " .
  "set pon_classi = ?, " .
  "    pon_pontos = ?, " .
  "    pon_jogos = ?, " .
  "    pon_vitori = ?, " .
  "    pon_empate = ?, " .
  "    pon_derrot = ?, " .
  "    pon_golpro = ?, " .
  "    pon_golcon = ?, " .
  "    pon_saldo = ?, " .
  "    pon_grupo = ? " .
  "where clu_ordem = ? and " .
  "      tor_codigo = ? and " .
  "      pon_ano = ? ",
	
// 29 Por estado/país
  // opc_num = 19 (Pontuacao)
  "select clu_nome, clu_fund, clu_estadi, " . 
  "       (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) as clu_estado, " .
  "       clu_cidade, a.clu_ordem, c.est_estado, " .
  "       sum(pon_pontos) as pon_pontos, " .
  "       sum(pon_jogos) as pon_jogos, " .
  "       sum(pon_vitori) as pon_vitori, " .
  "       sum(pon_empate) as pon_empate, " .
  "       sum(pon_derrot) as pon_derrot, " .
  "       sum(pon_golpro) as pon_golpro, " .
  "       sum(pon_golcon) as pon_golcon, " .
  "       sum(pon_saldo) as pon_saldo, " .
  "       round( " .
  "             sum(pon_pontos + pon_perdid) * 100 / " .
  "             sum(pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove " .
  "from PONTOS a, CLUBES b, ESTADOS c, TORNEIOS d " .
  "where a.tor_codigo = ? and " .
  "      a.tor_codigo = d.tor_codigo and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_ano between ? and ? and " .
  "      ? = (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end)" .
  "group by clu_estado, clu_nome, clu_cidade, a.clu_ordem, est_estado, clu_fund, clu_estadi " .
  "order by pon_pontos desc, pon_jogos, pon_saldo desc, pon_golpro desc, clu_nome",

// 30 estados/países do período
  // uEstPais
  "select (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) as clu_estado, " .
  "       e.est_estado, count(*) as numero " . 
  "from PONTOS a, CLUBES b, ESTADOS c, TORNEIOS d, ESTADOS e " .
  "where a.tor_codigo = ? and " .
  "      pon_ano between ? and ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      b.clu_estado = c.clu_estado and " .
  "      a.tor_codigo = d.tor_codigo and " .
  "      e.clu_estado = (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) " .
  "group by e.est_estado, clu_estado " .
  "order by e.est_estado, clu_estado",
  
// 31 Continentes
  // uContin (tele inicial)
  "select con_descri, con_espano, con_franca, con_alemao, con_englis, con_codigo from CONTINENTE order by con_descri",
  
// 32 Torneios do Continente
  // uTorneios
  "select tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, tor_codigo, est_pais " .
  "from TORNEIOS " .
  "where con_codigo = ? or tor_codigo < 'AC' " .
  "order by tor_descri",
  
// 33 Estados e países
  // uEstados
  "select clu_estado, est_estado " .
  "from ESTADOS " .
  "where con_codigo = ? " .
  "order by est_estado",
  
// 34 Número de clubes por estado/país
  // opc_num = 22
  "select e.est_estado, count(distinct(a.clu_ordem)) as num, " . 
  "      (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) as est_pais " .  
  "from PONTOS a, CLUBES b, ESTADOS c, TORNEIOS d, ESTADOS e " .  
  "where a.tor_codigo = ? and " .  
  "      a.clu_ordem = b.clu_ordem and " .  
  "      b.clu_estado = c.clu_estado and " .  
  "      a.tor_codigo = d.tor_codigo and " .  
  "      e.clu_estado = (case when d.tor_intern = 'S' then c.est_pais else b.clu_estado end) and " .  
  "      pon_ano between ? and ? " . 
  "group by e.est_estado, est_pais " . 
  "order by num desc, est_pais",
  
// 35 Verificação se tem promovido ou rebaixado // INATIVO
  // uOpcao
  "select count(*) as num " .
  "from PONTOS " .
  "where tor_codigo = ? and pon_proreb = ?",
  
// 36 Rebaixados ou promovidos
  // opc_num = 23 = Rebaixados
  // opc_num = 24 = Promovidos
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, a.pon_grupo, pon_ano, d.est_estado, " .
  "       (pon_pontos + pon_perdid) as pon_pontos, pon_jogos, pon_vitori, " .
  "       pon_empate, pon_derrot, pon_golpro, " .
  "       pon_golcon, pon_saldo, pon_classi, " .
  "       (case when tor_golvit <> 'V' then pon_saldo else pon_vitori end) as sequen, " .
  "       round( " .
  "             (pon_pontos + pon_perdid) * 100 / " .
  "             (pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
  "				, 1) as pon_aprove, " .
  "       pon_observ, pon_artilh, clu_fund, clu_estadi " .
  "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      a.tor_codigo = c.tor_codigo and " .
  "      pon_ano between ? and ? and " .
  "      b.clu_estado = d.clu_estado and " .
  "      pon_proreb = ? " .
  "order by pon_ano, pon_classi",
  
// 37 Maiores rebaixados ou promovidos
  // opc_num = 25 = Rebaixados
  // opc_num = 26 = Promovidos*
  "select clu_nome, b.clu_estado, clu_cidade, a.clu_ordem, count(*) as pon_titulo, c.est_estado, " .
  "       GROUP_CONCAT(PON_ANO order by PON_ANO desc) as anos " .
  "from PONTOS a, CLUBES b, ESTADOS c " .
  "where a.tor_codigo = ? and " .
  "      a.clu_ordem = b.clu_ordem and " .
  "      pon_ano between ? and ? and " .
  "      b.clu_estado = c.clu_estado and " .
  "      pon_proreb = ? " .
  "group by clu_nome, b.clu_estado, a.clu_ordem, c.est_estado " .
  "order by pon_titulo desc, clu_nome",
  
// 38 Evolução dos clubes				  
  // opc_num = 27
   "select a.clu_ordem, pon_ano, tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, pon_classi, clu_fund, clu_estadi " .
   "from PONTOS a, CLUBES b, TORNEIOS c " .
   "where a.clu_ordem = ? and " .
   "      a.clu_ordem = b.clu_ordem and  " .
   "      a.tor_codigo = c.tor_codigo " .
   "order by pon_ano desc, tor_descri",
   
// 39 Clubes do continente
  // uEvoClube
  "select clu_nome, clu_estado, a.clu_ordem, count(*) as num, pon_classi  " .
  "from CLUBES a  " . 
  "inner join PONTOS f on a.clu_ordem = f.clu_ordem  " . 
  "inner join TORNEIOS g on f.tor_codigo = g.tor_codigo and con_codigo = ?  " . 
  "group by clu_nome, clu_estado, a.clu_ordem " . 
  "order by clu_nome, clu_estado",
  
// 40 código do clube
  // uEvolucao
  // uCompClube
  "select clu_ordem from CLUBES where clu_nome = ? and clu_estado = ?",
  
// 41 iOS mostra fotos?
  // uContin (Menu inicial)
  "select * from PARAME",
  
// 42 Evolução dos clubes por torneio
  // opc_num = 28
   "select a.clu_ordem, pon_ano, tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, pon_classi, clu_fund, clu_estadi " .
   "from PONTOS a, CLUBES b, TORNEIOS c " .
   "where a.clu_ordem = ? and " .
   "      a.clu_ordem = b.clu_ordem and  " .
   "      a.tor_codigo = c.tor_codigo " .
   "order by tor_descri, pon_ano desc",
   
// 43 Fundação dos clubes
   "select clu_nome, clu_ordem, a.clu_estado, clu_cidade, clu_estadi, " .
   "extract(year from clu_fund) as clu_ano, b.est_estado, extract(year from clu_fund) AS pon_ano " .
   "from CLUBES a, ESTADOS b " .
   "where extract(day from clu_fund) = ? and extract(month from clu_fund) = ? " . 
   "and a.clu_estado = b.clu_estado " .
   "order by clu_ano, clu_nome",
   
// 44 Clubes que jogaram em algum torneio do continente no ano
   "select distinct(g.clu_ordem) as clu_ordem " .
   "from TORNEIOS e, CONTINENTE f, PONTOS g, CLUBES h " .
   "where pon_ano = ? and " .
   "      f.con_codigo = ? and " .
   "      e.con_codigo = f.con_codigo and " . 
   "      e.tor_codigo = g.tor_codigo and " .
   "      g.clu_ordem = h.clu_ordem " .
   "order by clu_ordem",
   
// 45 Aproveitamento total dos clubes no ano
   "select clu_nome, b.clu_estado, b.clu_cidade, a.clu_ordem, d.est_estado, " .
   "       sum(pon_pontos + a.pon_perdid) as pon_pontos, " .
   "       sum(pon_jogos) as pon_jogos, " .
   "       sum(pon_vitori) as pon_vitori, " .
   "       sum(pon_empate) as pon_empate, " .
   "       sum(pon_derrot) as pon_derrot, " .
   "       sum(pon_golpro) as pon_golpro, " .
   "       sum(pon_golcon) as pon_golcon, " .
   "       sum(pon_saldo) as pon_saldo, " .
   "       round( " .
   "             sum(pon_pontos + pon_perdid) * 100 / " .
   "             sum(pon_jogos * (case when pon_ano < tor_ano then 2 else 3 end)) " .
   "  				, 1) as pon_aprove " .
   "from PONTOS a, CLUBES b, TORNEIOS c, ESTADOS d " .
   "where a.tor_codigo <> 'BRASIL' and " .
   "	  (a.pon_vitori + a.pon_empate + a.pon_derrot) > 0 and " .
   "      a.pon_ano = ? and " .
   "      ? like concat(\"%,\", a.clu_ordem, \",%\") and " .
   "      a.clu_ordem = b.clu_ordem and " .
   "      a.tor_codigo = c.tor_codigo and " .
   "      ? like concat(\"%,\", c.tor_catego, \",%\") and " .
   "	  b.clu_estado = d.clu_estado " .   
   "group by b.clu_nome, b.clu_estado, b.clu_cidade, a.clu_ordem, d.est_estado ",
   //"order by pon_aprove desc, pon_jogos desc, pon_saldo desc, pon_golpro desc, pon_vitori desc, b.clu_nome",
   
// 46 Anos do torneio selecionado
  // opc_num = 30 = Aproveitamento no Ano (Continente)
  "select distinct(pon_ano) as pon_ano " .
  "from PONTOS a, TORNEIOS b " .
  "where a.tor_codigo = b.tor_codigo and "  .
  "	     (a.pon_vitori + a.pon_empate + a.pon_derrot) > 0 and " .  
  "      b.con_codigo = ? " .
  "order by pon_ano",
  
// 47 Clubes que jogaram no torneio selecionado
   "select distinct(g.clu_ordem) as clu_ordem " .
   "from TORNEIOS e, PONTOS g, CLUBES h " .
   "where pon_ano = ? and " .
   "      e.tor_codigo = ? and " .
   "      e.tor_codigo = g.tor_codigo and " .
   "      g.clu_ordem = h.clu_ordem " .
   "order by clu_ordem",
   
// 48 Torneios do Continente
  // uTorneios
  "select tor_descri, tor_espano, tor_franca, tor_alemao, tor_englis, tor_codigo, est_pais, tor_ordem " .
  "from TORNEIOS " .
  "where con_codigo = ? or tor_codigo < 'AC' " .
  "order by tor_ordem, ?"
   
);

$ordens = array(
  // 0 Pontuação
  "order by pon_pontos desc, pon_jogos, pon_saldo desc, pon_vitori desc, pon_golpro desc",
  
  // 1 Melhor Ataque
  "order by pon_golpro desc, pon_jogos, pon_golcon",
  
  // 2 Melhor Saldo de Gols
  "order by pon_saldo desc, pon_golpro desc, pon_jogos",
  
  // 3 Vitórias
  "order by pon_vitori desc, pon_saldo desc, pon_golpro desc, pon_jogos desc",
  
  // 4 Aproveitamento
  "order by pon_aprove desc, pon_jogos desc, pon_saldo desc, pon_golpro desc, pon_vitori desc, clu_nome",
  
  // 5 Partidas
  "order by pon_jogos desc, pon_pontos desc, pon_saldo desc, pon_golpro desc, pon_vitori desc",
  
  // 6 ano1 = ano2
  " order by grupo, pon_classi, pon_pontos desc, sequen desc, pon_saldo desc, pon_golpro desc, clu_nome"
);


if (($consulta == 45) and ($ordem == '')) {
	$ordem = 4;
}

if ($consulta > count($sqls) or $consulta < 0) {
	die('consulta inválida');
}
$sql = $sqls[intval($consulta)];


if (($ordem != '') and ($ordem >= 0) and ($ordem < count($ordens))) {
	$sql .= ' ' . $ordens[intval($ordem)];
}
/*$host="191.252.103.153";
$username="root";
$password="id278126";
$db_name="rankingfutebol";

$connection = new mysqli($host, $username, $password, $db_name);
$connection->set_charset("utf8");*/

$host="198.27.105.21";
$username="root";
$password="Dt278126";
$db_name="rankingfutebol";

$connection = new mysqli($host, $username, $password, $db_name);
$connection->set_charset("utf8");

$types = ''; 

if ($params == '' or count($params) <= 0) {
//echo $sql . '<br/>';
//	echo "<br/>if param vazio<br/>";
	if ($result  = $connection->query($sql)) {
		
		//print_r($result);
		//echo "<br/>query<br/>";
		$results = array();
		while ($row = $result->fetch_assoc()) {
			$results[] = $row;
		}
		//echo "<br/>while<br/>";
		
		$result->close();
	}
}
else {

//    print_r($params);	
	foreach($params as $i => $param) {  
		if (is_numeric($param) and intval($param) == $param) {  
			$types .= 'i';              //integer
			$params[$i] = intval($param);
		} elseif (is_float($param)) {
			$types .= 'd';              //double
		} elseif (is_string($param)) {
			$types .= 's';              //string
		} else {
			$types .= 'b';              //blob and unknown
		}
	}
	
//	if ($consulta == 45) {
//    $params[1] = explode(',', $params[1]);
//    $types[1] = 'b';
//  }
	array_unshift($params, $types);

	// Start stmt
	$query = $connection->stmt_init(); 
	//if() {
	$query->prepare($sql);
	

	//print_r($params);
		// Bind Params
		//call_user_func_array(array($query,'bind_param'),$params);
		if (count($params) == 2) {
			$query->bind_param($params[0], $params[1]);
		} else if (count($params) == 3) {
			$query->bind_param($params[0], $params[1], $params[2]);
		} else if (count($params) == 4) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3]);
		} else if (count($params) == 5) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4]);
		} else if (count($params) == 6) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
		} else if (count($params) == 7) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
		} else if (count($params) == 8) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
		} else if (count($params) == 9) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
		} else if (count($params) == 10) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
		} else if (count($params) == 11) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9], $params[10]);
		} else if (count($params) == 12) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9], $params[10], $params[11]);
		} else if (count($params) == 13) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9], $params[10], $params[11], $params[12]);
		} else if (count($params) == 14) {
			$query->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9], $params[10], $params[11], $params[12], $params[13]);
		}

		$query->execute();
  	

		// Get metadata for field names
    $meta = $query->result_metadata();
    if ($consulta == 28) {
      $results = $meta;
	  //$results = 'ok';
    } else {
      // initialise some empty arrays
      $fields = $results = $fieldNames = array();

      // This is the tricky bit dynamically creating an array of variables to use
      // to bind the results
      while ($field = $meta->fetch_field()) {
        $var = $field->name;  
        $fieldNames[] = $var;
        $$var = null; 
        $fields[$var] = &$$var;
      }

      $fieldCount = count($fieldNames);

      // Bind Results     
      $result = call_user_func_array(array($query,'bind_result'), $fields);

      $i=0;
      while ($query->fetch()){
        for($l=0;$l<$fieldCount;$l++) 
          $results[$i][$fieldNames[$l]] = $fields[$fieldNames[$l]];
        $i++;
      }

      //$query->close();
      // And now we have a beautiful
      // array of results, just like
      //fetch_assoc
    }
    $query->close();
	
}
header('Content-Type: application/json'); 
echo json_encode($results);
?>