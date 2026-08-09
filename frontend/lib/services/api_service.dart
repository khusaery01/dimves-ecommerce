import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../core/api.dart';
import '../models/category_model.dart';
import '../models/menu_model.dart';
import '../models/order_detail_model.dart';
import '../models/order_model.dart';

class ApiService {
  static const String baseUrl = Api.baseUrl;

  // ================= HEADER =================

  Map<String, String> _jsonHeaders([String? token]) {
    return {
      "Accept": "application/json",
      "Content-Type": "application/json",
      if (token != null) "Authorization": "Bearer $token",
    };
  }

  // ================= MENU =================

  Future<List<MenuModel>> getMenus({int? categoryId, String? search}) async {
    String url = "$baseUrl/menus";
    List<String> queryParams = [];
    if (categoryId != null && categoryId != 0) {
      queryParams.add("category_id=$categoryId");
    }
    if (search != null && search.isNotEmpty) {
      queryParams.add("search=${Uri.encodeComponent(search)}");
    }
    if (queryParams.isNotEmpty) {
      url += "?${queryParams.join('&')}";
    }

    final response = await http
        .get(Uri.parse(url), headers: {"Accept": "application/json"})
        .timeout(const Duration(seconds: 15));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data["menus"] as List).map((e) => MenuModel.fromJson(e)).toList();
    }

    throw Exception("Gagal mengambil data menu");
  }

  // ================= CATEGORY =================

  Future<List<CategoryModel>> getCategories() async {
    final response = await http
        .get(
          Uri.parse("$baseUrl/categories"),
          headers: {"Accept": "application/json"},
        )
        .timeout(const Duration(seconds: 15));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);

      return (data["categories"] as List)
          .map((e) => CategoryModel.fromJson(e))
          .toList();
    }

    throw Exception("Gagal mengambil kategori");
  }

  // ================= VOUCHER =================

  Future<Map<String, dynamic>> checkVoucher(String code, double orderTotal) async {
    final token = await getToken();

    final response = await http.post(
      Uri.parse("$baseUrl/vouchers/check"),
      headers: _jsonHeaders(token),
      body: jsonEncode({
        "voucher_code": code,
        "order_total": orderTotal,
      }),
    ).timeout(const Duration(seconds: 15));

    final data = jsonDecode(response.body);
    if (response.statusCode == 200) {
      return {
        "success": true,
        "discount_amount": double.parse(data["discount_amount"].toString()),
        "message": data["message"],
      };
    }

    return {
      "success": false,
      "message": data["message"] ?? "Voucher tidak berlaku",
    };
  }

  // ================= AUTHENTICATION & PROFILE =================

  Future<bool> login(String email, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse("$baseUrl/login"),
            headers: _jsonHeaders(),
            body: jsonEncode({"email": email, "password": password}),
          )
          .timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString("token", data["token"]);
        return true;
      }
    } catch (e) {
      print("Login Error: $e");
    }

    return false;
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    String? phone,
  }) async {
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/register"),
        headers: _jsonHeaders(),
        body: jsonEncode({
          "name": name,
          "email": email,
          "password": password,
          if (phone != null && phone.isNotEmpty) "phone": phone,
        }),
      ).timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);

      if (response.statusCode == 201 || (data is Map && data["success"] == true)) {
        if (data["token"] != null) {
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString("token", data["token"]);
        }
        return {
          "success": true,
          "message": data["message"] ?? "Register berhasil!",
        };
      }

      return {
        "success": false,
        "message": data["message"] ?? "Register gagal.",
      };
    } catch (e) {
      return {
        "success": false,
        "message": "Gagal terhubung ke server ($baseUrl). Pastikan server Laravel aktif.",
      };
    }
  }

  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString("token");
  }

  Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString("token") != null;
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove("token");
  }

  Future<Map<String, dynamic>> getProfile() async {
    final token = await getToken();

    final response = await http.get(
      Uri.parse("$baseUrl/user"),
      headers: _jsonHeaders(token),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }

    throw Exception(response.body);
  }

  Future<bool> updateProfile({
    required String name,
    required String phone,
    required String address,
  }) async {
    final token = await getToken();

    final response = await http.put(
      Uri.parse("$baseUrl/profile"),
      headers: _jsonHeaders(token),
      body: jsonEncode({"name": name, "phone": phone, "address": address}),
    );

    return response.statusCode == 200;
  }

  Future<Map<String, dynamic>> changePassword({
    required String oldPassword,
    required String newPassword,
  }) async {
    try {
      final token = await getToken();

      final response = await http.put(
        Uri.parse("$baseUrl/change-password"),
        headers: _jsonHeaders(token),
        body: jsonEncode({
          "old_password": oldPassword,
          "new_password": newPassword,
        }),
      ).timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data["success"] == true) {
        return {
          "success": true,
          "message": data["message"] ?? "Password berhasil diperbarui.",
        };
      }

      return {
        "success": false,
        "message": data["message"] ?? "Gagal mengubah password.",
      };
    } catch (e) {
      return {
        "success": false,
        "message": "Terjadi kesalahan koneksi.",
      };
    }
  }

  // ================= CHECKOUT =================

  Future<Map<String, dynamic>> checkout({
    required String paymentMethod,
    required String shippingAddress,
    required String orderType,
    String? tableNumber,
    String? voucherCode,
    double deliveryFee = 10000,
    required List<Map<String, dynamic>> items,
    String notes = "",
  }) async {
    final token = await getToken();

    final response = await http
        .post(
          Uri.parse("$baseUrl/checkout"),
          headers: _jsonHeaders(token),
          body: jsonEncode({
            "payment_method": paymentMethod,
            "shipping_address": shippingAddress,
            "order_type": orderType,
            "table_number": tableNumber,
            "voucher_code": voucherCode,
            "delivery_fee": deliveryFee,
            "notes": notes,
            "items": items,
          }),
        )
        .timeout(const Duration(seconds: 20));

    final data = jsonDecode(response.body);
    if (response.statusCode == 200 && data["success"] == true) {
      return {
        "success": true,
        "message": data["message"],
        "order": data["order"],
      };
    }

    return {
      "success": false,
      "message": data["message"] ?? "Checkout gagal",
    };
  }

  // ================= ORDER =================

  Future<List<OrderModel>> getOrders() async {
    final token = await getToken();

    final response = await http
        .get(Uri.parse("$baseUrl/orders"), headers: _jsonHeaders(token))
        .timeout(const Duration(seconds: 15));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);

      return (data["orders"] as List)
          .map((e) => OrderModel.fromJson(e))
          .toList();
    }

    throw Exception("Gagal mengambil pesanan");
  }

  Future<OrderDetailModel> getOrderDetail(int id) async {
    final token = await getToken();

    final response = await http
        .get(Uri.parse("$baseUrl/orders/$id"), headers: _jsonHeaders(token))
        .timeout(const Duration(seconds: 15));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);

      return OrderDetailModel.fromJson(data["order"]);
    }

    throw Exception("Gagal mengambil detail pesanan");
  }

  Future<Map<String, dynamic>> checkOrderStatus(int id) async {
    final token = await getToken();

    final response = await http
        .get(Uri.parse("$baseUrl/orders/$id/status"), headers: _jsonHeaders(token))
        .timeout(const Duration(seconds: 10));

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }

    throw Exception("Gagal mengecek status pesanan");
  }
}
