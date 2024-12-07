import 'package:flutter/material.dart';
import 'package:frontend/api/api_service.dart';
import 'package:frontend/api/api_service_product.dart';
import 'package:frontend/api/url.dart';

class MyProduct extends StatefulWidget {
  const MyProduct({super.key});

  @override
  State<MyProduct> createState() => _MyProductState();
}

class _MyProductState extends State<MyProduct> {
  @override
  Widget build(BuildContext context) {
    return const MaterialApp(
      home: MyHome(),
    );
  }
}

class MyHome extends StatefulWidget {
  const MyHome({super.key});

  @override
  State<MyHome> createState() => _MyHomeState();
}

class _MyHomeState extends State<MyHome> {
  late final ApiService apiService;
  late final ApiServiceProduct apiServiceProduct;
  List<dynamic> _data = [];
  String? _categoryId;

  final categoryIdController = TextEditingController();
  final productNameController = TextEditingController();
  final descriptionController = TextEditingController();
  final barCodeController = TextEditingController();
  final qtyController = TextEditingController();
  final expiredateController = TextEditingController();
  final priceInController = TextEditingController();
  final priceOutController = TextEditingController();


  @override
  void initState() {
    super.initState();
    apiService = ApiService(AppUrl.url);
    apiServiceProduct = ApiServiceProduct(AppUrl.url_post);
    _fetchProducts();
  }

  Future<void> _fetchProducts() async {
    try {
      final data = await apiService.fetchData();
      setState(() {
        _data = data;
      });
    } catch (err) {
      print("Error fetching products: $err");
    }
  }

  //save data
  void saveProduct() async {
    if (_categoryId == null ||
        productNameController.text.isEmpty ||
        descriptionController.text.isEmpty ||
        barCodeController.text.isEmpty ||
        qtyController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Please fill all fields"),
          backgroundColor: Colors.red,
        ),
      );

      return;
    }
    try {
      await apiServiceProduct.createData({
        'categoryId': _categoryId,
        'productName': productNameController.text,
        'description': descriptionController.text,
        'barcode': barCodeController.text,
        'qty': qtyController.text,
        'expiredate': expiredateController.text,
        'unitPriceIn': priceInController.text,
        'unitPriceOut': priceOutController.text
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Product saved successfully!"),
          backgroundColor: Colors.green,
        ),
      );

      // Clear fields after saving
      setState(() {
        productNameController.clear();
        descriptionController.clear();
        barCodeController.clear();
        qtyController.clear();
        _categoryId = null;
      });
    } catch (err) {
      print("Error saving product: $err");
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Failed to save product"),
          backgroundColor: Colors.red,
        ),
      );
    }
  }



  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.blue,
        title: const Text(
          "បង្កើតផលិតផល",
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Select a Category:',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 10),
                DropdownButtonFormField<String>(
                  value: _categoryId,
                  hint: const Text('Choose a category'),
                  decoration: InputDecoration(
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8.0),
                    ),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12.0),
                  ),
                  items: _data.map<DropdownMenuItem<String>>((cat) {
                    return DropdownMenuItem<String>(
                      value: cat['id'].toString(),
                      child: Text(cat['cname']),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _categoryId = value;
                    });
                  },
                ),
                const SizedBox(height: 20),
                if (_categoryId != null)
                  Text(
                    'Selected: ${_data.firstWhere((product) => product['id'].toString() == _categoryId)['cname']} (ID: $_categoryId)',
                    style: const TextStyle(fontSize: 16, fontStyle: FontStyle.italic),
                  ),
              ],
            ),
            const SizedBox(height: 10),


            TextFormField(
              controller: productNameController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Product Name',
              ),
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: descriptionController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Description',
              ),
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: barCodeController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Barcode',
              ),
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: qtyController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Qty',
              ),
            ),
            const SizedBox(height: 20),
            TextFormField(
              controller: expiredateController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Expiredate',
              ),
            ),
            const SizedBox(height: 20),
            TextFormField(
           controller: priceInController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'priceIn',
              ),
            ),

            const SizedBox(height: 20),
            TextFormField(
              controller: priceOutController,
              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'PriceOut',
              ),
            ),

            const SizedBox(height: 20),
            TextFormField(

              decoration: const InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Image',
              ),
            ),
            ElevatedButton(
              onPressed: saveProduct,
              child: const Text("រក្សាទុក"),
            ),
          ],
        ),
      ),
    );
  }
}
