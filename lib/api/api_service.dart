import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  final String baseUrl;
  ApiService(this.baseUrl);

  //fetch
  Future<List<dynamic>> fetchData() async {
    try {
      final response = await http.get(Uri.parse(baseUrl));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load data');
      }
    } catch (error) {
      throw Exception('Error fetching data: $error');
    }
  }

  //create
  Future<void> createData(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse(baseUrl),
        headers: {
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (err) {
      print(err);
    }
  }

  Future<void> deleteData(String id) async {
    if (id.isEmpty) {
      throw Exception('Customer ID is required.');
    }

    try {
      final response = await http.delete(
        Uri.parse('$baseUrl'),
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: {
          'id': id,
        },
      );

      if (response.statusCode == 200) {
        print("Data deleted successfully");
      } else {
        print("Failed to delete data. Status code: ${response.statusCode}");
        print("Response body: ${response.body}");
        throw Exception('Failed to delete data');
      }
    } catch (error) {
      print("Error deleting data: $error");
      throw Exception('Error deleting data: $error');
    }
  }

  //update by id
  Future<void> updateData(String id, Map<String, dynamic> data) async {
    if (id.isEmpty) {
      throw Exception('Customer ID is required for update.');
    }

    final Map<String, dynamic> updatedData = {
      'id': id,
      'cname': data['cname'],
      'detail': data['detail'],
    };
    try {
      final response = await http.put(
        Uri.parse('$baseUrl?id=$id'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(updatedData),
      );

      if (response.statusCode != 200) {
        print('Failed to update data. Status code: ${response.statusCode}');
        print('Response body: ${response.body}');
        throw Exception('Failed to update data: ${response.statusCode}');
      } else {
        print('Data updated successfully');
      }
    } catch (error) {
      print('Error updating data: $error');
      throw Exception('Error updating data: $error');
    }
  }
}
