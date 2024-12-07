import 'package:flutter/material.dart';
import 'package:frontend/api/api_service.dart';
import 'package:frontend/api/url.dart';
class MyCreate extends StatefulWidget {
  const MyCreate({super.key});

  @override
  State<MyCreate> createState() => _MyCreateState();
}

class _MyCreateState extends State<MyCreate> {


  final apiService = ApiService(AppUrl.url);

  final categoryNameController = TextEditingController();
  final detailController = TextEditingController();


  @override
  void initState() {
    super.initState();
  }

  void saveData () async{

    try{
        apiService.createData({
          'cname': categoryNameController.text,
          'detail': detailController.text,
        });

        print("Save successfully");
        categoryNameController.clear();
        detailController.clear();
    }
    catch(err){
      print(err);
    }

  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: Scaffold(
        appBar: AppBar(
          title: Text("បង្កើត"),
        ),
        body: Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(8.0),
              child: Column(
                children: [
                  SizedBox(
                    width: double.infinity,
                    child: TextField(
                      controller: categoryNameController,
                      obscureText: true,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Category Name',
                      ),
                    ),
                  ),

                  SizedBox(height: 10,),
                  SizedBox(
                    width: double.infinity,
                    child: TextField(
                      controller: detailController,
                      obscureText: true,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Detail',
                      ),
                    ),
                  ),

                  ElevatedButton(onPressed: ()  {
                    saveData();
                  }, child: Text("រក្សាទុក")),

                  ElevatedButton(onPressed: (){
                    Navigator.pop(context);
                  }, child: Text("ត្រលប់ក្រោយ"))
                ],
              )
            )
          ],
        ),
      ),
    );
  }
}
