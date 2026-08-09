import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/cart_provider.dart';
import '../../services/api_service.dart';
import '../main/main_page.dart';
import '../auth/login_page.dart';

class CheckoutPage extends StatefulWidget {
  const CheckoutPage({super.key});

  @override
  State<CheckoutPage> createState() => _CheckoutPageState();
}

class _CheckoutPageState extends State<CheckoutPage> {
  final TextEditingController addressController = TextEditingController();
  final TextEditingController tableController = TextEditingController();
  final TextEditingController voucherController = TextEditingController();
  final TextEditingController noteController = TextEditingController();

  String paymentMethod = "QRIS"; // Default ke QRIS karena Cash tidak boleh untuk dine_in & delivery
  String orderType = "delivery"; // dine_in, takeaway, delivery

  bool isLoading = false;
  bool isCheckingVoucher = false;
  String? voucherMessage;

  @override
  void initState() {
    super.initState();
    tableController.text = "01";
    _loadUserAddress();
  }

  Future<void> _loadUserAddress() async {
    try {
      final user = await ApiService().getProfile();
      if (user["address"] != null && user["address"].toString().isNotEmpty) {
        setState(() {
          addressController.text = user["address"];
        });
      } else {
        setState(() {
          addressController.text = "";
        });
      }
    } catch (e) {
      setState(() {
        addressController.text = "";
      });
    }
  }

  Future<void> checkVoucher() async {
    final code = voucherController.text.trim();
    if (code.isEmpty) return;

    final cart = context.read<CartProvider>();

    setState(() {
      isCheckingVoucher = true;
      voucherMessage = null;
    });

    final res = await ApiService().checkVoucher(code, cart.subtotalPrice);

    setState(() {
      isCheckingVoucher = false;
    });

    if (res["success"] == true) {
      cart.applyVoucher(code.toUpperCase(), res["discount_amount"]);
      setState(() {
        voucherMessage = "Voucher berhasil dipasang! Diskon: Rp ${res["discount_amount"]}";
      });
    } else {
      cart.removeVoucher();
      setState(() {
        voucherMessage = res["message"] ?? "Voucher tidak valid";
      });
    }
  }

  Future<void> checkout() async {
    final cart = context.read<CartProvider>();

    final loggedIn = await ApiService().isLoggedIn();
    if (!loggedIn) {
      if (!mounted) return;
      Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => const LoginPage()),
      );
      return;
    }

    if (orderType == "delivery" && addressController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Alamat pengiriman wajib diisi untuk delivery")),
      );
      return;
    }

    if (orderType == "dine_in" && tableController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Nomor meja wajib diisi untuk Dine-In")),
      );
      return;
    }

    if (cart.items.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Keranjang masih kosong")),
      );
      return;
    }

    setState(() {
      isLoading = true;
    });

    List<Map<String, dynamic>> items = cart.items.map((e) {
      List<Map<String, dynamic>> variants = e.selectedVariants.map((v) => {
        "variant_id": v.variant.id,
        "option_id": v.option.id,
      }).toList();

      return {
        "menu_id": e.menu.id,
        "quantity": e.quantity,
        "price": e.unitPrice,
        "note": e.note,
        "variants": variants,
      };
    }).toList();

    final res = await ApiService().checkout(
      paymentMethod: paymentMethod,
      shippingAddress: addressController.text.trim(),
      orderType: orderType,
      tableNumber: tableController.text.trim(),
      voucherCode: cart.voucherCode,
      deliveryFee: cart.deliveryFee,
      notes: noteController.text.trim(),
      items: items,
    );

    if (!mounted) return;

    setState(() {
      isLoading = false;
    });

    if (res["success"] == true) {
      cart.clearCart();

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.green,
          content: Text(res["message"] ?? "Pesanan berhasil dibuat!"),
        ),
      );

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const MainPage()),
        (route) => false,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.red,
          content: Text(res["message"] ?? "Checkout gagal"),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();

    final subtotal = cart.subtotalPrice;
    final discount = cart.discountAmount;
    final deliveryFee = orderType == "delivery" ? 10000.0 : 0.0;
    final grandTotal = (subtotal - discount + deliveryFee) > 0
        ? (subtotal - discount + deliveryFee)
        : 0.0;

    return Scaffold(
      appBar: AppBar(title: const Text("Checkout")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Tipe Pesanan (Dine-in, Takeaway, Delivery)
            const Text(
              "Tipe Pesanan",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: ChoiceChip(
                    label: const Text("Pesan Antar\n(Delivery)", textAlign: TextAlign.center),
                    selected: orderType == "delivery",
                    selectedColor: const Color(0xFFE53935),
                    labelStyle: TextStyle(
                      color: orderType == "delivery" ? Colors.white : Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                    onSelected: (val) {
                      if (val) {
                        setState(() {
                          orderType = "delivery";
                          if (paymentMethod == "Cash") {
                            paymentMethod = "QRIS";
                          }
                        });
                        cart.setOrderType("delivery");
                      }
                    },
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ChoiceChip(
                    label: const Text("Dine-In\n(Scan Meja)", textAlign: TextAlign.center),
                    selected: orderType == "dine_in",
                    selectedColor: const Color(0xFFE53935),
                    labelStyle: TextStyle(
                      color: orderType == "dine_in" ? Colors.white : Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                    onSelected: (val) {
                      if (val) {
                        setState(() {
                          orderType = "dine_in";
                          if (tableController.text.isEmpty) {
                            tableController.text = "01";
                          }
                          if (paymentMethod == "Cash") {
                            paymentMethod = "QRIS";
                          }
                        });
                        cart.setOrderType("dine_in");
                      }
                    },
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ChoiceChip(
                    label: const Text("Takeaway\n(Bawa Pulang)", textAlign: TextAlign.center),
                    selected: orderType == "takeaway",
                    selectedColor: const Color(0xFFE53935),
                    labelStyle: TextStyle(
                      color: orderType == "takeaway" ? Colors.white : Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                    onSelected: (val) {
                      if (val) {
                        setState(() {
                          orderType = "takeaway";
                        });
                        cart.setOrderType("takeaway");
                      }
                    },
                  ),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Input No Meja jika Dine-In (Dropdown Pilihan Meja 01 s/d 09)
            if (orderType == "dine_in") ...[
              const Text(
                "Nomor Meja",
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              DropdownButtonFormField<String>(
                value: tableController.text.isEmpty ? "01" : tableController.text,
                decoration: InputDecoration(
                  prefixIcon: const Icon(Icons.table_restaurant),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                items: ["01", "02", "03", "04", "05", "06", "07", "08", "09"]
                    .map((t) => DropdownMenuItem(
                          value: t,
                          child: Text("Meja $t"),
                        ))
                    .toList(),
                onChanged: (value) {
                  setState(() {
                    tableController.text = value ?? "01";
                  });
                },
              ),
              const SizedBox(height: 20),
            ],

            // Input Alamat jika Delivery
            if (orderType == "delivery") ...[
              const Text(
                "Masukan alamat anda",
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: addressController,
                maxLines: 2,
                decoration: InputDecoration(
                  hintText: "Masukkan alamat lengkap Anda...",
                  prefixIcon: const Icon(Icons.location_on),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
              const SizedBox(height: 20),
            ],

            // Voucher & Promo Input
            const Text(
              "Voucher Diskon",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: voucherController,
                    decoration: InputDecoration(
                      hintText: "Kode Voucher (Contoh: DIMVES50)",
                      prefixIcon: const Icon(Icons.local_offer),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFE53935),
                    foregroundColor: Colors.white,
                    minimumSize: const Size(80, 52),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  onPressed: isCheckingVoucher ? null : checkVoucher,
                  child: isCheckingVoucher
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                      : const Text("Pasang"),
                ),
              ],
            ),
            if (voucherMessage != null) ...[
              const SizedBox(height: 6),
              Text(
                voucherMessage!,
                style: TextStyle(
                  color: cart.voucherCode != null ? Colors.green : Colors.red,
                  fontSize: 13,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
            const SizedBox(height: 20),

            // Metode Pembayaran (Cash hanya untuk takeaway, QRIS & Transfer untuk semuanya)
            const Text(
              "Metode Pembayaran",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            if (orderType == "takeaway")
              RadioListTile(
                value: "Cash",
                groupValue: paymentMethod,
                onChanged: (value) => setState(() => paymentMethod = value!),
                title: const Text("Tunai / Cash (Bayar di Kasir)"),
              ),
            RadioListTile(
              value: "QRIS",
              groupValue: paymentMethod,
              onChanged: (value) => setState(() => paymentMethod = value!),
              title: const Text("QRIS (Scan QR Code Kasir)"),
            ),

            // Tampilkan QR image saat QRIS dipilih
            if (paymentMethod == "QRIS") ...[
              const SizedBox(height: 8),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE53935), width: 1.5),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.red.withValues(alpha: 0.08),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    const Text(
                      "Scan QR Berikut untuk Pembayaran",
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFFE53935),
                      ),
                    ),
                    const SizedBox(height: 12),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: Image.asset(
                        'assets/images/qr.jpeg',
                        width: 220,
                        height: 220,
                        fit: BoxFit.contain,
                      ),
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      "Tunjukkan bukti pembayaran kepada kasir / kurir",
                      style: TextStyle(fontSize: 12, color: Colors.grey),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 8),
            ],
            RadioListTile(
              value: "Transfer",
              groupValue: paymentMethod,
              onChanged: (value) => setState(() => paymentMethod = value!),
              title: const Text("Transfer Bank (Virtual Account / TF Manual)"),
            ),
            const SizedBox(height: 20),

            // Catatan Pesanan
            const Text(
              "Catatan Pesanan Khusus",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: noteController,
              decoration: InputDecoration(
                hintText: "Contoh: Posisikan pesanan dekat gerbang...",
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
            const SizedBox(height: 25),

            // Summary Card
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Subtotal Menu"),
                        Text("Rp ${subtotal.toStringAsFixed(0)}"),
                      ],
                    ),
                    if (discount > 0) ...[
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text("Diskon Voucher", style: TextStyle(color: Colors.green)),
                          Text("- Rp ${discount.toStringAsFixed(0)}", style: const TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ],
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Ongkos Kirim"),
                        Text(orderType == "delivery" ? "Rp ${deliveryFee.toStringAsFixed(0)}" : "GRATIS"),
                      ],
                    ),
                    const Divider(height: 20),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          "Total Bayar",
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                        Text(
                          "Rp ${grandTotal.toStringAsFixed(0)}",
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            color: Color(0xFFE53935),
                            fontSize: 20,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 30),

            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFE53935),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
                onPressed: isLoading ? null : checkout,
                child: isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text(
                        "Buat Pesanan Sekarang",
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
