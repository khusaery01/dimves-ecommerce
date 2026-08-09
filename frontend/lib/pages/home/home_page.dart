import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/app_colors.dart';
import '../../widgets/home/about_section.dart';
import '../../widgets/home/contact_section.dart';
import '../../widgets/home/drink_section.dart';
import '../../widgets/home/featured_menu_section.dart';
import '../../widgets/home/promo_section.dart';
import '../../widgets/home/testimonial_section.dart';
import '../cart/cart_page.dart';
import '../menu/menu_page.dart';
import '../../providers/cart_provider.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final ScrollController _scrollController = ScrollController();

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const double bannerHeight = 480.0;
    const double overlapOffset = 90.0;
    const double contentTopOffset = bannerHeight - overlapOffset;

    return Scaffold(
      backgroundColor: const Color(0xFF111111),
      body: Stack(
        children: [
          // Layer 0 (Back): Fixed Hero Banner with smooth scroll fade masking
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: bannerHeight,
            child: Stack(
              fit: StackFit.expand,
              children: [
                // Background Image
                Image.asset("assets/images/kedai.png", fit: BoxFit.cover),

                // Dark Gradient Overlay: Top shadow & bottom fade to black
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      stops: const [0.0, 0.3, 0.65, 1.0],
                      colors: [
                        Colors.black.withValues(alpha: 0.55),
                        Colors.black.withValues(alpha: 0.20),
                        Colors.black.withValues(alpha: 0.80),
                        Colors.black,
                      ],
                    ),
                  ),
                ),

                // Hero Banner Content (Sized and spaced to avoid top header collision)
                SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Top clearance ensuring zero collision with Logo DIMVES header
                        const Spacer(),

                        // Rating Badge
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 14,
                            vertical: 7,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(25),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.15),
                                blurRadius: 8,
                                offset: const Offset(0, 2),
                              ),
                            ],
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.star, color: Colors.orange, size: 18),
                              SizedBox(width: 5),
                              Text(
                                "Rating 4.9",
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 8),

                        // Title Text
                        const Text(
                          "Fresh dibuat\nsetiap hari",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 28,
                            fontWeight: FontWeight.bold,
                            height: 1.15,
                          ),
                        ),
                        const SizedBox(height: 8),

                        // Subtitle Text
                        const Text(
                          "Dimsum premium dengan berbagai varian rasa favorit.",
                          style: TextStyle(color: Colors.white70, fontSize: 14),
                        ),
                        const SizedBox(height: 16),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),

          // Layer 1 (Front/On Top): Scrollable White Content Container
          SingleChildScrollView(
            controller: _scrollController,
            physics: const BouncingScrollPhysics(),
            child: Column(
              children: [
                SizedBox(height: contentTopOffset),

                Container(
                  width: double.infinity,
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(35),
                    ),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.only(top: 22),
                    child: Column(
                      children: [
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          child: SizedBox(
                            width: double.infinity,
                            height: 50,
                            child: ElevatedButton.icon(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xffE53935),
                                foregroundColor: Colors.white,
                                elevation: 2,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(14),
                                ),
                              ),
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => const MenuPage(),
                                  ),
                                );
                              },
                              icon: const Icon(Icons.restaurant_menu),
                              label: const Text(
                                "Pesan Sekarang",
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 15,
                                ),
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 20),

                        const FeaturedMenuSection(),
                        const DrinkSection(),
                        const PromoSection(),
                        const AboutSection(),
                        const TestimonialSection(),
                        const ContactSection(),
                        const SizedBox(height: 45),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
          // Layer 2: Fixed Header App Bar (Logo, Title, Cart Icon)
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: AnimatedBuilder(
              animation: _scrollController,
              builder: (context, child) {
                final double offset = _scrollController.hasClients ? _scrollController.offset : 0.0;
                final double headerBgOpacity = (offset / 180.0).clamp(0.0, 1.0);

                return Container(
                  decoration: BoxDecoration(
                    color: Color.fromRGBO(229, 57, 53, headerBgOpacity),
                    gradient: headerBgOpacity == 0
                        ? const LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              Color.fromARGB(180, 0, 0, 0),
                              Colors.transparent,
                            ],
                          )
                        : null,
                    boxShadow: headerBgOpacity > 0.8
                        ? [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.1),
                              blurRadius: 6,
                              offset: const Offset(0, 2),
                            ),
                          ]
                        : null,
                  ),
                  child: child,
                );
              },
              child: SafeArea(
                bottom: false,
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 18,
                    vertical: 10,
                  ),
                  child: Row(
                    children: [
                      Image.asset("assets/images/logo.png", width: 38),
                      const SizedBox(width: 10),
                      const Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "DIMVES",
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                          Text(
                            "Nikmatnya Ga Penah Mogok!",
                            style: TextStyle(
                              fontSize: 11,
                              color: Colors.white70,
                            ),
                          ),
                        ],
                      ),
                      const Spacer(),
                      Consumer<CartProvider>(
                        builder: (context, cartProvider, child) {
                          final int itemCount = cartProvider.totalItem;
                          return Badge(
                            label: Text(
                              itemCount.toString(),
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            isLabelVisible: itemCount > 0,
                            backgroundColor: AppColors.primary,
                            child: IconButton(
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (_) => const CartPage()),
                                );
                              },
                              icon: const Icon(
                                Icons.shopping_cart_outlined,
                                color: Colors.white,
                              ),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
