import 'package:flutter/material.dart';
import 'package:frontend/api/api_service.dart';
import 'package:frontend/api/url.dart';
import 'package:frontend/screen/create.dart';
import 'package:frontend/screen/product.dart';
import 'package:frontend/screen/uploadImage.dart';

class MyHome extends StatefulWidget {
  const MyHome({super.key});

  @override
  State<MyHome> createState() => _MyHomeState();
}

class _MyHomeState extends State<MyHome> {
  late final ApiService apiService;
  List<dynamic> _data = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    apiService = ApiService(AppUrl.url);
    _fetchData();
  }

  // Fetch data from the API
  Future<void> _fetchData() async {
    try {
      final data = await apiService.fetchData();
      setState(() {
        _data = data;
        _isLoading = false;
      });
    } catch (error) {
      setState(() {
        _isLoading = false;
      });
      print(error);
    }
  }

  Future<void> _deleteItem(String id) async {
    try {
      await apiService.deleteData(id); // Call to delete data from the API
      setState(() {
        _data.removeWhere((item) => item['id'].toString() == id); // Update list after deletion
      });
      print("Item deleted successfully");
    } catch (error) {
      print("Error deleting item: $error");
    }
  }

  void _showEditDialog(String id, String currentName, String currentEmail) {
    final TextEditingController nameController = TextEditingController(text: currentName);
    final TextEditingController emailController = TextEditingController(text: currentEmail);

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text("Edit Customer"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  border: OutlineInputBorder(),
                  labelText: 'Name',
                ),
              ),
              SizedBox(height: 10),
              TextField(
                controller: emailController,
                decoration: InputDecoration(
                  border: OutlineInputBorder(),
                  labelText: 'Email',
                ),
              ),
            ],
          ),
          actions: [
            ElevatedButton(
              onPressed: () async {
                try {
                  await apiService.updateData(id, {
                    'cname': nameController.text,
                    'detail': emailController.text,
                  });
                  _fetchData(); // Refresh the data after updating
                  Navigator.pop(context); // Close the dialog
                } catch (error) {
                  print("Error updating item: $error");
                }
              },
              child: Text("Update"),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context); // Close the dialog without action
              },
              child: Text("Cancel"),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.blue,
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text("ប្រភេទផលិតផល"),
            TextButton(onPressed: (){
              Navigator.push(context, MaterialPageRoute(builder: (context)=> const MyProduct()));
            }, child: Text("+បង្កើតផលិតផល", style: TextStyle(color: Colors.white),)),
            TextButton(onPressed: (){
              Navigator.push(context, MaterialPageRoute(builder: (context)=> const MyImage()));
            }, child: Text("+ រូបភាព", style: TextStyle(color: Colors.white),))
          ],
        ),
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : ListView.builder(
        itemCount: _data.length,
        itemBuilder: (context, index) {
          final item = _data[index];
          return ListTile(
            title: Text(item['cname']),
            subtitle: Text(item['detail']),
            trailing: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                IconButton(
                  icon: Icon(Icons.edit, color: Colors.blue),
                  onPressed: () => _showEditDialog(item['id'].toString(), item['cname'], item['detail']),
                ),
                IconButton(
                  icon: Icon(Icons.delete, color: Colors.red),
                  onPressed: () => _deleteItem(item['id'].toString()), // Deleting the item
                ),
              ],
            ),
          );

        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const MyCreate()));
        },
        child: Icon(Icons.add),
      ),
    );
  }
}
