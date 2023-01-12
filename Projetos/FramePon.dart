import 'package:flutter/material.dart';
import 'package:ranking_futebol/unitglob.dart' as unitglob;
import 'package:ranking_futebol/Clube.dart';
import 'package:ranking_futebol/Evolucao.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';

class PontosFrame extends StatelessWidget {
  final Map item;
  final Function(String texto) funcao;
  final int? index;
  PontosFrame({
    Key? key,
    required this.item,
    required this.funcao,
    this.index,
  }) : super(key: key);

//  List<String> lista2 = [];

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Visibility(
          visible: item['pon_ano'] != null &&
                  (unitglob.opcnum == 23 || unitglob.opcnum == 24)
              ? unitglob.mostrarep[int.parse(index.toString())]
              : ((unitglob.opcnum) == 9 ||
                  (unitglob.opcnum) == 23 ||
                  (unitglob.opcnum) == 24),
          child: Container(
            color: Colors.blue.shade100,
            child: Text(
              item['pon_ano'] != null
                  ? item['pon_ano'].toString()
                  : unitglob.opcnum == 9
                      ? 'Média dos Campeões'
                      : 'Média dos Rebaixados',
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 20.0,
                color: Colors.black,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
        Visibility(
          visible: (unitglob.opcnum == 32),
          child: Container(
              color: Colors.yellow.shade100,
              child: item['tor_descri'] != null
                  ? Text(
                      item['tor_descri'],
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 20.0,
                        color: Colors.redAccent,
                        fontWeight: FontWeight.bold,
                      ),
                    )
                  : const SizedBox()),
        ),
        Visibility(
          visible: item['pon_grupo'] != null &&
              item['pon_grupo'] != '' &&
              unitglob.opcnum == 1 &&
              unitglob.mostrarep[int.parse(index.toString())],
          //: false,
          //item['pon_grupo'] != null && item['pon_grupo'] != '',
          child: Container(
              color: Colors.yellow.shade100,
              child: item['pon_grupo'] != null
                  ? Text(
                      grupo(item['pon_grupo']),
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 17.0,
                        color: Colors.redAccent,
                        fontWeight: FontWeight.bold,
                      ),
                    )
                  : const SizedBox()),
        ),
        Container(
          color: Colors.yellow.shade200,
          child: Visibility(
            visible: (unitglob.opcnum != 9 &&
                    unitglob.opcnum != 23 &&
                    unitglob.opcnum != 24) ||
                double.parse(item['pon_aprove']) <= 200.0,
            child: Text(
              item['clu_nome'] == null
                  ? '${index.toString()}º ${item['est_estado']}'
                  : classif(
                          item['pon_classi'] == null
                              ? index
                              : item['pon_classi'],
                          index,
                          item['pon_grupo'] == null ? '' : item['pon_grupo']
                          //)
                          ) +
                      '${item['clu_nome']} / ${item['clu_cidade']} / ' +
                      item['est_estado'] +
                      (unitglob.opcnum != 21
                          ? ''
                          : ' (${item['pon_classi']}º em ${item['pon_ano']})'),
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.only(left: 3, right: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Visibility(
                visible: unitglob.mostrafig &&
                    (item['clu_ordem'] == null || item['clu_ordem'] > 0),
                child: Expanded(
                  flex: 1,
                  child: Center(
                      child: InkWell(
                          onTap: () {
                            setState() {}
                            if (unitglob.opcnum != 7) {
                              unitglob.clubenome = item['clu_nome'];
                              unitglob.sigla = item['clu_estado'];
                              unitglob.estado = item['est_estado'];
                              unitglob.cluordem = item['clu_ordem'];
                              unitglob.cidade = item['clu_cidade'];
                              if (item['clu_fund'] == null) {
                                unitglob.fundacao = '';
                              } else {
                                Intl.defaultLocale = 'pt_BR';
                                initializeDateFormatting('pt_BR', null);
                                unitglob.fundacao = DateFormat('dd/MM/yyyy')
                                    .format(DateTime.parse(item['clu_fund']))
                                    .toString();
                              }
                              if (item['clu_estadi'] == null) {
                                unitglob.estadio = '';
                              } else {
                                unitglob.estadio =
                                    item['clu_estadi'].toString();
                              }

                              Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (_) => const ClubePage()));

                              /*unitglob.opcnum2 = 27;
                              Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (_) => const EvolucaoPage()));*/
                            }
                          },
                          child: Image.network(
                            item['clu_ordem'] == null
                                ? '${unitglob.fotos}est_${item['''clu_estado''']}.jpg'
                                : '${unitglob.fotos}cl${item['''clu_ordem''']}.jpg',
                            width: 40,
                            height: 40,
                          ))),
                ),
              ),
              Expanded(
                flex: 5,
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Texto(
                          titulo: 'Pontos',
                          value: item['pon_pontos'].toString(),
                          funcao: () => funcao('Pontos'),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Jogos',
                          value: item['pon_jogos'].toString(),
                          funcao: () => funcao('Jogos'),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Vitórias',
                          value: item['pon_vitori'].toString(),
                          funcao: () => funcao('Vitórias'),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Empates',
                          value: item['pon_empate'].toString(),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Derrotas',
                          value: item['pon_derrot'].toString(),
                          //inMarcado: item['marcado'],
                        ),
                      ],
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Texto(
                          titulo: 'Gols Pró',
                          value: item['pon_golpro'].toString(),
                          funcao: () => funcao('Gols'),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Gols Contra',
                          value: item['pon_golcon'].toString(),
                          //inMarcado: item['marcado'],
                        ),
                        Texto(
                          titulo: 'Saldo',
                          value: item['pon_saldo'].toString(),
                          funcao: () => funcao('Saldo'),
                          //inMarcado: item['marcado'],
                        ),
                        Visibility(
                          visible: (unitglob.opcnum != 9 &&
                                  unitglob.opcnum != 23 &&
                                  unitglob.opcnum != 24) ||
                              double.parse(item['pon_aprove']) <= 200.0,
                          child: Texto(
                            titulo: 'Aproveit',
                            value: '${item['pon_aprove']}%',
                            funcao: () => funcao('Aprov'),
                            //inMarcado: item['marcado'],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
        Visibility(
          visible: item['pon_artilh'] != null,
          child: Text(
            item['pon_artilh'] != null
                ? "Artilheiro(s): ${item['pon_artilh']}"
                : 'pon_artilh',
            //}
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 16.0,
              //color: Colors.black,
            ),
          ),
        ),
        Visibility(
          visible: item['pon_observ'] != null,
          child: Container(
              //color: Colors.yellow.shade100,

              child: item['pon_observ'] != null
                  ? Text(
                      item['pon_observ'] != null
                          ? "Obs: ${item['pon_observ']}"
                          : 'pon_observ',
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 16.0,
                        //color: Colors.redAccent,
                        //fontWeight: FontWeight.bold,
                      ),
                    )
                  : const SizedBox()),
        ),
      ],
    );
  }

  String classif(int? classi, int? indice, String grupo) {
    unitglob.numreg++;
    String resulta = '';

    if ((unitglob.opcnum == 1) ||
        (unitglob.opcnum == 20) &&
            (int.parse(unitglob.ano1) == int.parse(unitglob.ano2))) {
      if (int.parse(unitglob.ano1) <= int.parse(unitglob.anomax)) {
        resulta = '$classiº ';
      } else if (grupo == '' && unitglob.prigrupo == '') {
        resulta = '$indiceº ';
      } else {
        resulta = '$classiº ';
      }
    } else if (unitglob.opcnum == 23 || unitglob.opcnum == 24) {
      resulta = '$classiº ';
    } else if (unitglob.opcnum != 9 && unitglob.opcnum != 32) {
      resulta = '$indiceº '; // '$classiº '; //
    }
//    lista2[unitglob.numreg] = resulta;
    return resulta;
  }

  String grupo(String grup) {
    unitglob.antgrupo = grup;
    return grup;
  }
}

//Montei um widget padrão, para ter um texto em cima do outro
//além da opção de clique
class Texto extends StatelessWidget {
  final String titulo;
  final String value;
  final Function()? funcao;
  final bool inMarcado;
  const Texto({
    Key? key,
    required this.titulo,
    required this.value,
    this.funcao,
    this.inMarcado = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.all(0),
      decoration: inMarcado
          ? BoxDecoration(
              borderRadius: BorderRadius.circular(5),
              color: Colors.green,
              border: Border.all(),
            )
          : null,
      child: InkWell(
        onTap: funcao ?? funcao,
        child: Column(
          children: [
            Text(titulo,
                style: const TextStyle(
                  fontSize: 10.0,
                  color: Color(0xFF616161),
                  fontWeight: FontWeight.bold,
                )),
            Text(value,
                style: const TextStyle(
                  fontSize: 16.0,
                  color: Color(0xFF009688),
                  fontWeight: FontWeight.bold,
                )),
          ],
        ),
      ),
    );
  }
}
