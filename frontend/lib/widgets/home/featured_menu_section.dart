import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../models/menu_model.dart';
import '../../services/api_service.dart';
import '../../pages/menu/detail_menu_page.dart';
import '../../providers/cart_provider.dart';

class FeaturedMenuSection extends StatefulWidget {
  const FeaturedMenuSection({super.key});

  @override
  State<FeaturedMenuSection> createState() => FeaturedMenuSectionState();
}

class FeaturedMenuSectionState extends State<FeaturedMenuSection> {
  late Future<List<MenuModel>> futureMenus;

  static final List<MenuModel> _favoriteDimsums = [
    MenuModel(
      id: 1,
      categoryId: 1,
      name: "Dimsum Original",
      description: "Dimsum kukus lembut isi ayam udang",
      price: 15000,
      stock: 100,
      image: "assets/images/dimsum_original.png",
      status: true,
    ),
    MenuModel(
      id: 2,
      categoryId: 1,
      name: "Dimsum BBQ",
      description: "Dimsum lezat dengan saus BBQ melimpah",
      price: 20000,
      stock: 100,
      image: "assets/images/dimsum_bbq.png",
      status: true,
    ),
    MenuModel(
      id: 4,
      categoryId: 1,
      name: "Dimsum Mentai",
      description: "Dimsum topping mentai creamy dan torch gurih",
      price: 25000,
      stock: 100,
      image: "assets/images/dimsum_mentai.png",
      status: true,
    ),
  ];

  @override
  void initState() {
    super.initState();
    futureMenus = ApiService().getMenus();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 12, bottom: 18),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Row(
              children: const [
                Expanded(
                  child: Text(
                    "Dimsum Favorit",
                    style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 12),

          FutureBuilder<List<MenuModel>>(
            future: futureMenus,
            builder: (context, snapshot) {
              List<MenuModel> displayMenus = [];

              if (snapshot.hasData && snapshot.data!.isNotEmpty) {
                final allMenus = snapshot.data!;
                for (int i = 0; i < _favoriteDimsums.length; i++) {
                  final fav = _favoriteDimsums[i];
                  final matched = allMenus.firstWhere(
                    (m) => m.name.toLowerCase().contains(
                      fav.name.toLowerCase().replaceAll("dimsum ", ""),
                    ),
                    orElse: () => allMenus[i % allMenus.length],
                  );
                  displayMenus.add(
                    MenuModel(
                      id: matched.id, // ID asli dari DB Laravel
                      categoryId: 1,
                      name: fav.name,
                      description: fav.description,
                      price: fav.price,
                      stock: matched.stock > 0 ? matched.stock : fav.stock,
                      image: fav.image,
                      status: true,
                      variants: matched.variants,
                    ),
                  );
                }
              } else {
                displayMenus = _favoriteDimsums;
              }

              return SizedBox(
                height: 350,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  itemCount: displayMenus.length,
                  itemBuilder: (context, index) {
                    final menu = displayMenus[index];

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
                        width: 210,
                        margin: const EdgeInsets.only(right: 18),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.grey.withValues(alpha: 0.15),
                              blurRadius: 15,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ClipRRect(
                              borderRadius: const BorderRadius.vertical(
                                top: Radius.circular(24),
                              ),
                              child: Image.asset(
                                menu.image,
                                height: 150,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (ctx, err, stack) => Container(
                                  height: 150,
                                  color: const Color(0xFFFFE5D9),
                                  child: const Icon(
                                    Icons.restaurant,
                                    size: 60,
                                    color: Color(0xFFE53935),
                                  ),
                                ),
                              ),
                            ),

                            Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: const [
                                      Icon(
                                        Icons.star,
                                        color: Colors.amber,
                                        size: 18,
                                      ),
                                      SizedBox(width: 4),
                                      Text(
                                        "4.9",
                                        style: TextStyle(
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ],
                                  ),

                                  const SizedBox(height: 10),

                                  Text(
                                    menu.name,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 18,
                                    ),
                                  ),

                                  const SizedBox(height: 6),

                                  Text(
                                    menu.description,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(color: Colors.grey),
                                  ),

                                  const SizedBox(height: 14),

                                  Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          "Rp ${menu.price.toStringAsFixed(0)}",
                                          style: const TextStyle(
                                            color: Color(0xffE53935),
                                            fontWeight: FontWeight.bold,
                                            fontSize: 18,
                                          ),
                                        ),
                                      ),
                                      // Tombol tambah ke keranjang
                                      _AddToCartButton(menu: menu),
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
              );
            },
          ),
        ],
      ),
    );
  }
}

class _AddToCartButton extends StatefulWidget {
  final MenuModel menu;
  const _AddToCartButton({required this.menu});

  @override
  State<_AddToCartButton> createState() => _AddToCartButtonState();
}

class _AddToCartButtonState extends State<_AddToCartButton>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 200),
      vsync: this,
    );
    _scaleAnimation = Tween<double>(
      begin: 1.0,
      end: 0.85,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));
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
        backgroundColor: const Color(0xFFE53935),
        content: Row(
          children: [
            const Icon(Icons.shopping_cart, color: Colors.white, size: 18),
            const SizedBox(width: 8),
            Text(
              "${widget.menu.name} ditambahkan ke keranjang",
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ],
        ),
        duration: const Duration(milliseconds: 1200),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        margin: const EdgeInsets.all(12),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return ScaleTransition(
      scale: _scaleAnimation,
      child: GestureDetector(
        onTap: _onTap,
        child: Container(
          decoration: BoxDecoration(
            color: const Color(0xffE53935),
            borderRadius: BorderRadius.circular(15),
          ),
          child: const Padding(
            padding: EdgeInsets.all(10),
            child: Icon(Icons.add, color: Colors.white),
          ),
        ),
      ),
    );
  }
}
