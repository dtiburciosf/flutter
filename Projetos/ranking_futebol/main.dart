import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:ranking_futebol/splash.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);
  SystemChrome.setSystemUIOverlayStyle(
    SystemUiOverlayStyle.light.copyWith(
      statusBarColor: const Color.fromRGBO(82, 214, 130, 1),
      systemNavigationBarColor: const Color.fromRGBO(242, 242, 242, 1),
      statusBarBrightness: Brightness.dark,
      //Ícones superior e inferior
      statusBarIconBrightness: Brightness.dark,
      systemNavigationBarIconBrightness: Brightness.dark,
    ),
  );
  runApp(const Ranking());
}

class Ranking extends StatelessWidget {
  const Ranking({super.key});
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        appBarTheme: const AppBarTheme(
          elevation: 0,
          backgroundColor: Color.fromRGBO(82, 214, 130, 1),
        ),
        scaffoldBackgroundColor: const Color.fromRGBO(241, 255, 240, 1),
      ),
      home: const Splash(),
    );
  }
}
