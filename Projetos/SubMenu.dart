import 'package:flutter/material.dart';
import 'package:ranking_futebol/Clube.dart';
import 'package:ranking_futebol/unitglob.dart' as unitglob;
import 'package:ranking_futebol/Evolucao.dart';

enum Menu { itemOne, itemTwo, itemThree }
//String Menu {'Campanhas', 'Evolução por Ano', 'Evolução por Ano'}; // itemOne, itemTwo, itemThree, itemFour }

class SubMenuPage extends StatefulWidget {
  const SubMenuPage({super.key});

  @override
  State<SubMenuPage> createState() => _SubMenuPageState();
}

class _SubMenuPageState extends State<SubMenuPage> {
  @override
  void initState() {
    super.initState();
    /*PopupMenuButton<Menu>(
        // Callback that sets the selected popup menu item.
        onSelected: (Menu item) {
          setState(() {
            if (item == Menu.itemOne) {
                    Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const ClubePage()));
                  } else {
                    print(item);
                  }
          });
        },
        itemBuilder: (BuildContext context) => <PopupMenuEntry<Menu>>[
              const PopupMenuItem<Menu>(
                value: Menu.itemOne,
                child: Text('Campanhas'),
              ),
              const PopupMenuItem<Menu>(
                value: Menu.itemTwo,
                child: Text('Evolução por Ano'),
              ),
              const PopupMenuItem<Menu>(
                value: Menu.itemThree,
                child: Text('Evolução por Torneio'),
              ),
            ]);*/
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        actions: <Widget>[
          // This button presents popup menu items.
          PopupMenuButton<Menu>(
              // Callback that sets the selected popup menu item.
              onSelected: (Menu item) {
                setState(() {
                  if (item == Menu.itemOne) {
                    Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const ClubePage()));
                  } else if (item == Menu.itemTwo) {
                    unitglob.opcnum = 27;
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => const EvolucaoPage()));
                  } else {
                    unitglob.opcnum = 28;
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => const EvolucaoPage()));
                  }
                });
              },
              itemBuilder: (BuildContext context) => <PopupMenuEntry<Menu>>[
                    const PopupMenuItem<Menu>(
                      value: Menu.itemOne,
                      child: Text('Campanhas'),
                    ),
                    const PopupMenuItem<Menu>(
                      value: Menu.itemTwo,
                      child: Text('Evolução por Ano'),
                    ),
                    const PopupMenuItem<Menu>(
                      value: Menu.itemThree,
                      child: Text('Evolução por Torneio'),
                    ),
                  ]),
        ],
      ),
    );
  }
}
