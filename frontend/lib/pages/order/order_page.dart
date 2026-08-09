import 'package:flutter/material.dart';

import '../../models/order_model.dart';
import '../../services/api_service.dart';
import 'order_detail_page.dart';

class OrderPage extends StatefulWidget {
  const OrderPage({super.key});

  @override
  State<OrderPage> createState() => _OrderPageState();
}

class _OrderPageState extends State<OrderPage> {
  late Future<List<OrderModel>> orders;

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  void _loadOrders() {
    setState(() {
      orders = ApiService().getOrders();
    });
  }

  Color statusColor(String status) {
    switch (status.toLowerCase()) {
      case "pending":
      case "menunggu":
        return Colors.orange;
      case "diproses":
      case "process":
      case "preparing":
        return Colors.blue;
      case "selesai":
      case "completed":
      case "ready":
      case "served":
        return Colors.green;
      case "dibatalkan":
      case "cancelled":
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Pesanan Saya"),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadOrders,
          ),
        ],
      ),

      body: FutureBuilder<List<OrderModel>>(
        future: orders,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 50, color: Colors.red),
                  const SizedBox(height: 10),
                  Text(snapshot.error.toString()),
                  const SizedBox(height: 15),
                  ElevatedButton(
                    onPressed: _loadOrders,
                    child: const Text("Coba Lagi"),
                  ),
                ],
              ),
            );
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.receipt_long_outlined, size: 80, color: Colors.grey),
                  SizedBox(height: 15),
                  Text("Belum ada riwayat pesanan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                ],
              ),
            );
          }

          final data = snapshot.data!;

          return RefreshIndicator(
            onRefresh: () async => _loadOrders(),
            child: ListView.builder(
              padding: const EdgeInsets.only(left: 15, right: 15, top: 15, bottom: 120),
              itemCount: data.length,
              itemBuilder: (context, index) {
                final order = data[index];

                return Card(
                  margin: const EdgeInsets.only(bottom: 15),
                  elevation: 3,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.all(15),
                    leading: const CircleAvatar(
                      backgroundColor: Color(0xFFE53935),
                      child: Icon(Icons.receipt_long, color: Colors.white),
                    ),
                    title: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          order.orderCode,
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        Chip(
                          visualDensity: VisualDensity.compact,
                          backgroundColor: Colors.red.shade50,
                          label: Text(
                            order.orderType.toUpperCase(),
                            style: const TextStyle(fontSize: 10, color: Color(0xFFE53935), fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 6),
                        Text("Pembayaran : ${order.paymentMethod}"),
                        Text(
                          "Total : Rp ${order.grandTotal.toStringAsFixed(0)}",
                          style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87),
                        ),
                        const SizedBox(height: 5),
                        Text(
                          order.createdAt.length >= 10 ? order.createdAt.substring(0, 10) : order.createdAt,
                          style: const TextStyle(color: Colors.grey, fontSize: 12),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 5,
                              ),
                              decoration: BoxDecoration(
                                color: statusColor(order.status),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                order.status,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            if (order.tableNumber != null) ...[
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.grey.shade200,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Text(
                                  "Meja ${order.tableNumber}",
                                  style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 18),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => OrderDetailPage(orderId: order.id),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
