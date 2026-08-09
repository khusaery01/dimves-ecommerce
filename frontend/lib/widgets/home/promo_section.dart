import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../core/app_colors.dart';
import '../../models/menu_model.dart';
import '../../pages/menu/detail_menu_page.dart';
import '../../providers/cart_provider.dart';

class PromoSection extends StatefulWidget {
  const PromoSection({super.key});

  @override
  State<PromoSection> createState() => _PromoSectionState();
}

class _PromoSectionState extends State<PromoSection> {
  // Data Paket Bundling Hemat DIMVES
  final List<Map<String, dynamic>> _packageList = [
    {
      "id": 101,
      "badge": "HEMAT 18%",
      "badgeColor": const Color(0xFFE53935),
      "name": "Paket Solo Komplit",
      "description": "1 Box Dimsum Original + 1 Es Redves Segar",
      "originalPrice": 27000.0,
      "promoPrice": 22000.0,
      "image": "assets/images/dimsum_original.png",
      "menuModel": MenuModel(
        id: 101,
        categoryId: 1,
        name: "Paket Solo Komplit",
        description: "1 Box Dimsum Original + 1 Es Redves Segar",
        price: 22000,
        stock: 50,
        image: "assets/images/dimsum_original.png",
        status: true,
      ),
    },
    {
      "id": 102,
      "badge": "BEST SELLER",
      "badgeColor": Colors.orange.shade800,
      "name": "Paket Duaan Mesra",
      "description": "1 Dimsum Mentai + 1 Dimsum BBQ + 2 Thai Tea",
      "originalPrice": 61000.0,
      "promoPrice": 49000.0,
      "image": "assets/images/dimsum_mentai.png",
      "menuModel": MenuModel(
        id: 102,
        categoryId: 1,
        name: "Paket Duaan Mesra",
        description: "1 Dimsum Mentai + 1 Dimsum BBQ + 2 Thai Tea",
        price: 49000,
        stock: 50,
        image: "assets/images/dimsum_mentai.png",
        status: true,
      ),
    },
    {
      "id": 103,
      "badge": "PAKET PESTA 25%",
      "badgeColor": const Color(0xFFD4AF37),
      "name": "Paket Rame-Rame Pesta",
      "description": "4 Box All Varian Dimsum + 4 Minuman Favorit",
      "originalPrice": 130000.0,
      "promoPrice": 99000.0,
      "image": "assets/images/dimsum_bbq.png",
      "menuModel": MenuModel(
        id: 103,
        categoryId: 1,
        name: "Paket Rame-Rame Pesta",
        description: "4 Box All Varian Dimsum + 4 Minuman Favorit",
        price: 99000,
        stock: 30,
        image: "assets/images/dimsum_bbq.png",
        status: true,
      ),
    },
  ];

  // Data Voucher Promo Spesial
  final List<Map<String, dynamic>> _voucherList = [
    {
      "code": "DIMVES10K",
      "title": "Diskon Potongan Rp 10.000",
      "subtitle": "Min. Pembelian Rp 50.000 untuk semua menu",
      "amount": 10000.0,
      "bgGradient": const [Color(0xFFD62828), Color(0xFFFF6B35)],
    },
    {
      "code": "GRATISONGKIR",
      "title": "Voucher Gratis Ongkir",
      "subtitle": "Potongan Ongkir Rp 10.000 khusus area delivery",
      "amount": 10000.0,
      "bgGradient": const [Color(0xFF1B5E20), Color(0xFF4CAF50)],
    },
  ];

  void _claimVoucher(String code, double amount) {
    Clipboard.setData(ClipboardData(text: code));
    context.read<CartProvider>().applyVoucher(code, amount);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        backgroundColor: Colors.green.shade700,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        margin: const EdgeInsets.all(16),
        content: Row(
          children: [
            const Icon(Icons.confirmation_number_outlined, color: Colors.white),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                "Voucher $code berhasil dipasang & disalin!",
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // ================= SEKSI 1: PAKET HEMAT DIMVES =================
        Padding(
          padding: const EdgeInsets.only(left: 20, right: 20, top: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    "Paket Hemat DIMVES",
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  SizedBox(height: 2),
                  Text(
                    "Makan rame-rame & hemat lebih banyak!",
                    style: TextStyle(color: Colors.grey, fontSize: 13),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Text(
                  "Promo Bundling",
                  style: TextStyle(
                    color: AppColors.primary,
                    fontWeight: FontWeight.bold,
                    fontSize: 11,
                  ),
                ),
              ),
            ],
          ),
        ),

        const SizedBox(height: 14),

        SizedBox(
          height: 345,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            itemCount: _packageList.length,
            itemBuilder: (context, index) {
              final pkg = _packageList[index];
              final MenuModel menu = pkg["menuModel"];
              final Color badgeColor = pkg["badgeColor"];

              return RepaintBoundary(
                child: GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => DetailMenuPage(menu: menu),
                      ),
                    );
                  },
                  child: Container(
                    width: 255,
                    margin: const EdgeInsets.only(right: 18),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.grey.withOpacity(0.15),
                          blurRadius: 15,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Stack Image + Badge Hemat
                        Stack(
                          children: [
                            ClipRRect(
                              borderRadius: const BorderRadius.vertical(
                                top: Radius.circular(24),
                              ),
                              child: Image.asset(
                                pkg["image"],
                                height: 145,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (ctx, err, stack) => Container(
                                  height: 145,
                                  color: Colors.red.shade50,
                                  child: const Icon(
                                    Icons.fastfood_rounded,
                                    size: 60,
                                    color: AppColors.primary,
                                  ),
                                ),
                              ),
                            ),
                            // Badge Tag
                            Positioned(
                              top: 12,
                              left: 12,
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 5,
                                ),
                                decoration: BoxDecoration(
                                  color: badgeColor,
                                  borderRadius: BorderRadius.circular(12),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.2),
                                      blurRadius: 6,
                                    ),
                                  ],
                                ),
                                child: Text(
                                  pkg["badge"],
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 11,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),

                        Padding(
                          padding: const EdgeInsets.all(14),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                pkg["name"],
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 17,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                pkg["description"],
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: TextStyle(
                                  color: Colors.grey.shade600,
                                  fontSize: 12,
                                  height: 1.3,
                                ),
                              ),
                              const SizedBox(height: 12),
                              Row(
                                children: [
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          "Rp ${pkg["originalPrice"].toStringAsFixed(0)}",
                                          style: TextStyle(
                                            color: Colors.grey.shade500,
                                            decoration: TextDecoration.lineThrough,
                                            fontSize: 12,
                                          ),
                                        ),
                                        Text(
                                          "Rp ${pkg["promoPrice"].toStringAsFixed(0)}",
                                          style: const TextStyle(
                                            color: AppColors.primary,
                                            fontWeight: FontWeight.bold,
                                            fontSize: 17,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  // Button Tambah Paket
                                  _AddPackageButton(menu: menu),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),

        const SizedBox(height: 25),

        // ================= SEKSI 2: VOUCHER & PROMO SPESIAL =================
        const Padding(
          padding: EdgeInsets.symmetric(horizontal: 20),
          child: Text(
            "Voucher & Promo Spesial",
            style: TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
          ),
        ),

        const SizedBox(height: 12),

        SizedBox(
          height: 155,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            itemCount: _voucherList.length,
            itemBuilder: (context, index) {
              final v = _voucherList[index];

              return Container(
                width: 310,
                margin: const EdgeInsets.only(right: 16),
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(22),
                  gradient: LinearGradient(
                    colors: v["bgGradient"],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: (v["bgGradient"][0] as Color).withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 5),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(
                          Icons.confirmation_number_outlined,
                          color: Colors.white,
                          size: 26,
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            v["title"],
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                        ),
                      ],
                    ),
                    Text(
                      v["subtitle"],
                      style: TextStyle(
                        color: Colors.white.withOpacity(0.9),
                        fontSize: 12,
                      ),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 5,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.25),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: Colors.white.withOpacity(0.4)),
                          ),
                          child: Text(
                            v["code"],
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1.2,
                              fontSize: 13,
                            ),
                          ),
                        ),
                        ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.white,
                            foregroundColor: v["bgGradient"][0],
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            padding: const EdgeInsets.symmetric(
                              horizontal: 14,
                              vertical: 8,
                            ),
                            minimumSize: const Size(80, 34),
                          ),
                          onPressed: () => _claimVoucher(v["code"], v["amount"]),
                          child: const Text(
                            "Pakai Voucher",
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              );
            },
          ),
        ),

        const SizedBox(height: 15),
      ],
    );
  }
}

class _AddPackageButton extends StatefulWidget {
  final MenuModel menu;
  const _AddPackageButton({required this.menu});

  @override
  State<_AddPackageButton> createState() => _AddPackageButtonState();
}

class _AddPackageButtonState extends State<_AddPackageButton>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 180),
      vsync: this,
    );
    _scaleAnimation = Tween<double>(begin: 1.0, end: 0.85).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _onTap() async {
    await _controller.forward();
    await _controller.reverse();

    if (!mounted) return;

    context.read<CartProvider>().addToCart(
      widget.menu,
      quantity: 1,
      note: "",
      selectedVariants: const [],
    );

    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: AppColors.primary,
        content: Row(
          children: [
            const Icon(Icons.shopping_bag_outlined, color: Colors.white, size: 20),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                "${widget.menu.name} ditambahkan ke keranjang!",
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
        duration: const Duration(milliseconds: 1200),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        margin: const EdgeInsets.all(16),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return ScaleTransition(
      scale: _scaleAnimation,
      child: ElevatedButton.icon(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 2,
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
          minimumSize: const Size(90, 36),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
        onPressed: _onTap,
        icon: const Icon(Icons.add_shopping_cart, size: 16),
        label: const Text(
          "Tambah",
          style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
        ),
      ),
    );
  }
}
