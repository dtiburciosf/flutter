import 'package:flutter/material.dart';

class CustomPopupMenu extends StatefulWidget {

  final List<String> items;

  final ValueChanged<int> onChange;

  final String text;

  final int selectedIndex;

  const CustomPopupMenu({

    required this.items,

    required this.onChange,

    required this.text,

    this.selectedIndex = 0,

  });

  @override

  _CustomPopupMenuState createState() => _CustomPopupMenuState();

}

class _CustomPopupMenuState extends State<CustomPopupMenu>

    with SingleTickerProviderStateMixin {

  late GlobalKey _key;

  bool isMenuOpen = false;

  late Offset buttonPosition;

  late Size buttonSize;

  late OverlayEntry _overlayEntry;

  late OverlayEntry _overlayEntry1;

  late AnimationController _animationController;

  @override

  void initState() {

    _animationController = AnimationController(vsync: this, duration: Duration(milliseconds: 250),);

    _key = LabeledGlobalKey('popup-button');
    

    super.initState();
//CustomPopup();
  }

  @override

  void dispose() {

    _animationController.dispose();

    super.dispose();

  }

  findButton() {

    RenderBox? renderBox = _key.currentContext!.findRenderObject() as RenderBox?;

    buttonSize = renderBox!.size;

    buttonPosition = renderBox.localToGlobal(Offset.zero);

  }

  closeMenu() {

    _overlayEntry.remove();

    _overlayEntry1.remove();

    _animationController.reverse();

    isMenuOpen = !isMenuOpen;

  }

  openMenu() {

    findButton();

    _animationController.forward();

    _overlayEntry = _overlayEntryBuilder();

    _overlayEntry1 = _overlayEntryBuilder1();

    Overlay.of(context)!.insert(_overlayEntry1);

    Overlay.of(context)!.insert(_overlayEntry);

    isMenuOpen = !isMenuOpen;

  }

  @override

  Widget build(BuildContext context) {

    return Container(

      key: _key,

      child: GestureDetector(

        onTap: ()=>{

          if (isMenuOpen) {

            closeMenu()

          } else {

            openMenu()

          }},

        child:

        // You can use your custom design below

        Text(

                widget.text,

                style: TextStyle(fontFamily: 'Raleway', color: Colors.black.withOpacity(0.5),),

        ),

      ),

    );

  }

  OverlayEntry _overlayEntryBuilder() {

    return OverlayEntry(

      builder: (context) {

        return Positioned(

          top: buttonPosition.dy + buttonSize.height,

          left: buttonPosition.dx,

          child: Container(

            decoration: BoxDecoration(

              borderRadius: BorderRadius.all(Radius.circular(6)),

              color: Colors.black,

            ),

            child: Material(

              color: Colors.transparent,

              child: Column(

                mainAxisSize: MainAxisSize.min,

                children: List.generate(widget.items.length, (index) {

                  return GestureDetector(

                    onTap: () {

                      widget.onChange(index);

                      closeMenu();

                    },

                    child: Container(

                      alignment: Alignment.centerLeft,

                      padding: EdgeInsets.symmetric(horizontal: 10),

                      color: index == widget.selectedIndex ? Colors.white.withOpacity(0.3): null,

                      child:  Container(

                                alignment: Alignment.centerLeft,

                                padding: EdgeInsets.symmetric(vertical: 5),

                                child: Text(widget.items[index], style: TextStyle(color: index == widget.selectedIndex ? Colors.white : Colors.white.withOpacity(0.3), fontFamily: 'Raleway', fontSize: 16,))

                      ),

                    ),

                  );

                }),

              ),

            ),

          ),

        );

      },

    );

  }

  OverlayEntry _overlayEntryBuilder1() {

    return OverlayEntry(

      builder: (context) {

        return GestureDetector(

          onTap: ()=>{closeMenu()},

          child: Container(

            height: MediaQuery.of(context).size.height,

            width: MediaQuery.of(context).size.width,

            color: Colors.transparent,

          ),

        );

      },

    );

  }

  /*CustomPopup(

          items: [“Item1”, “Item2”, “Item3”, “Item4”],

          onChange: (index){

            setState(() {

              selectedIndex = index; //saving the selected index into as variable named selectedIndex

              //here you can put your action on click of item

            });

          },

          text: “Popup Button”,

          selectedIndex: selectedIndex, //provide the index of selected item to highlight that 

        ),*/

}