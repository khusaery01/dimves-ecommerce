import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../models/menu_model.dart';
import '../../services/api_service.dart';
import '../../pages/menu/detail_menu_page.dart';
import '../../providers/cart_provider.dart';

class DrinkSection extends StatefulWidget {
  const DrinkSection({super.key});

  @override
  State<DrinkSection> createState() => _DrinkSectionState();
}

class _DrinkSectionState extends State<DrinkSection> {
  late Future<List<MenuModel>> futureDrinks;

  static final List<MenuModel> _drinks = [
    MenuModel(
      id: 12,
      categoryId: 2,
      name: "Redves",
      description: "Minuman segar rasa khas Redves",
      price: 12000,
      stock: 100,
      image: "assets/images/redves.png",
      status: true,
    ),
    MenuModel(
      id: 8,
      categoryId: 2,
      name: "Thai Tea",
      description: "Thai tea manis dan creamy",
      price: 8000,
      stock: 100,
      image: "assets/images/thai_tea.png",
      status: true,
    ),
    MenuModel(
      id: 9,
      categoryId: 2,
      name: "Lemon Tea",
      description: "Lemon tea segar dan nikmat",
      price: 6000,
      stock: 100,
      image: "assets/images/lemon_tea.png",
      status: true,
    ),
  ];

  @override
  void initState() {
    super.initState();
    futureDrinks = ApiService().getMenus();
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
                    "Minuman Favorit",
                    style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 12),

          FutureBuilder<List<MenuModel>>(
            future: futureDrinks,
            builder: (context, snapshot) {
              List<MenuModel> displayDrinks = [];

              if (snapshot.hasData && snapshot.data!.isNotEmpty) {
                final allMenus = snapshot.data!;
                for (int i = 0; i < _drinks.length; i++) {
                  final d = _drinks[i];
                  final matched = allMenus.firstWhere(
                    (m) => m.name.toLowerCase().contains(d.name.toLowerCase()),
                    orElse: () => allMenus[i % allMenus.length],
                  );
                  displayDrinks.add(MenuModel(
                    id: matched.id, // ID asli dari DB Laravel
                    categoryId: 2,
                    name: d.name,
                    description: d.description,
                    price: d.price,
                    stock: matched.stock > 0 ? matched.stock : d.stock,
                    image: d.image,
                    status: true,
                    variants: matched.variants,
                  ));
                }
              } else {
                displayDrinks = _drinks;
              }

              return SizedBox(
                height: 350,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  itemCount: displayDrinks.length,
                  itemBuilder: (context, index) {
                    final drink = displayDrinks[index];

                    return RepaintBoundary(
                      child: GestureDetector(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => DetailMenuPage(menu: drink),
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
                                drink.image,
                                height: 150,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (ctx, err, stack) => Container(
                                  height: 150,
                                  color: const Color(0xFFFFE5D9),
                                  child: const Icon(
                                    Icons.local_drink,
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
                                    drink.name,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 18,
                                    ),
                                  ),

                                  const SizedBox(height: 6),

                                  Text(
                                    drink.description,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(color: Colors.grey),
                                  ),

                                  const SizedBox(height: 14),

                                  Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          "Rp ${drink.price.toStringAsFixed(0)}",
                                          style: const TextStyle(
                                            color: Color(0xffE53935),
                                            fontWeight: FontWeight.bold,
                                            fontSize: 18,
                                          ),
                                        ),
                                      ),

                                      // Tombol tambah ke keranjang
                                      _AddToCartButton(menu: drink),
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
            child: Icon(
              Icons.add,
              color: Colors.white,
            ),
          ),
        ),
      ),
    );
  }
}
