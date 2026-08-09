import 'package:flutter/material.dart';

import '../../core/app_colors.dart';
import '../../services/api_service.dart';
import '../auth/login_page.dart';
import '../auth/register_page.dart';
import '../order/order_page.dart';
import 'edit_profile_page.dart';

class ProfilePage extends StatefulWidget {
  const ProfilePage({super.key});

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  late Future<bool> isLogin;

  @override
  void initState() {
    super.initState();
    refresh();
  }

  void refresh() {
    setState(() {
      isLogin = ApiService().isLoggedIn();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      appBar: AppBar(
        title: const Text("Profil Saya"),
        centerTitle: true,
        elevation: 0,
      ),
      body: FutureBuilder<bool>(
        future: isLogin,
        builder: (context, loginSnapshot) {
          if (loginSnapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            );
          }

          final loggedIn = loginSnapshot.data ?? false;

          if (!loggedIn) {
            return _buildGuest();
          }

          return _buildProfile();
        },
      ),
    );
  }

  // ================= GUEST VIEW (BELUM LOGIN) =================
  Widget _buildGuest() {
    return SingleChildScrollView(
      child: Column(
        children: [
          // Top Red Header
          Container(
            width: double.infinity,
            padding: const EdgeInsets.only(left: 24, right: 24, bottom: 35, top: 20),
            decoration: const BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(30),
                bottomRight: Radius.circular(30),
              ),
            ),
            child: Column(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.15),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Image.asset(
                    "assets/images/logo.png",
                    width: 75,
                    height: 75,
                    fit: BoxFit.contain,
                  ),
                ),
                const SizedBox(height: 16),
                const Text(
                  "Selamat Datang di DIMVES",
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  "Nikmati kemudahan memesan dimsum & kuliner favoritmu dengan membuat akun",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.9),
                    fontSize: 13,
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // Benefit Card Highlight
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Card(
              elevation: 3,
              shadowColor: Colors.black.withOpacity(0.06),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Keuntungan Memiliki Akun",
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 15),
                    _buildBenefitItem(
                      icon: Icons.track_changes_outlined,
                      title: "Lacak Pesanan Real-time",
                      subtitle: "Pantau proses pembuatan hingga pesanan diantar.",
                    ),
                    const Divider(height: 24),
                    _buildBenefitItem(
                      icon: Icons.location_on_outlined,
                      title: "Simpan Alamat Pengiriman",
                      subtitle: "Checkout lebih cepat tanpa mengetik ulang alamat.",
                    ),
                    const Divider(height: 24),
                    _buildBenefitItem(
                      icon: Icons.local_offer_outlined,
                      title: "Voucher & Promo Eksklusif",
                      subtitle: "Dapatkan kesempatan promo potongan harga khusus.",
                    ),
                  ],
                ),
              ),
            ),
          ),

          const SizedBox(height: 25),

          // Buttons Section
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Column(
              children: [
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      foregroundColor: Colors.white,
                      elevation: 3,
                      shadowColor: AppColors.primary.withOpacity(0.4),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                    icon: const Icon(Icons.login_rounded),
                    label: const Text(
                      "Masuk ke Akun",
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    onPressed: () async {
                      await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => LoginPage(
                            onLoginSuccess: () {
                              refresh();
                            },
                          ),
                        ),
                      );
                      refresh();
                    },
                  ),
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: OutlinedButton.icon(
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.primary,
                      side: const BorderSide(color: AppColors.primary, width: 1.8),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                    icon: const Icon(Icons.person_add_alt_1_outlined),
                    label: const Text(
                      "Daftar Akun Baru",
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    onPressed: () async {
                      await Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const RegisterPage()),
                      );
                      refresh();
                    },
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 130),
        ],
      ),
    );
  }

  Widget _buildBenefitItem({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: AppColors.primary, size: 24),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
              ),
              const SizedBox(height: 2),
              Text(
                subtitle,
                style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // ================= LOGGED-IN VIEW =================
  Widget _buildProfile() {
    return FutureBuilder<Map<String, dynamic>>(
      future: ApiService().getProfile(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(
            child: CircularProgressIndicator(color: AppColors.primary),
          );
        }

        if (snapshot.hasError) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.error_outline, size: 50, color: Colors.red),
                const SizedBox(height: 10),
                const Text("Gagal memuat informasi profil"),
                const SizedBox(height: 15),
                ElevatedButton(
                  onPressed: refresh,
                  child: const Text("Coba Lagi"),
                ),
              ],
            ),
          );
        }

        final user = snapshot.data ?? {};
        final name = user["name"] ?? "Pengguna DIMVES";
        final email = user["email"] ?? "-";
        final phone = user["phone"] ?? "Belum diatur";
        final address = user["address"] ?? "Belum diatur";

        return RefreshIndicator(
          onRefresh: () async => refresh(),
          color: AppColors.primary,
          child: ListView(
            padding: const EdgeInsets.only(bottom: 130),
            children: [
              // Header Card Profile
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(22),
                decoration: const BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(30),
                    bottomRight: Radius.circular(30),
                  ),
                ),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(3),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: CircleAvatar(
                        radius: 45,
                        backgroundColor: Colors.red.shade100,
                        child: Text(
                          name.isNotEmpty ? name[0].toUpperCase() : "U",
                          style: const TextStyle(
                            fontSize: 36,
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),
                    Text(
                      name,
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.email_outlined, size: 14, color: Colors.white70),
                        const SizedBox(width: 5),
                        Text(
                          email,
                          style: const TextStyle(color: Colors.white70, fontSize: 13),
                        ),
                      ],
                    ),
                    if (phone != "Belum diatur") ...[
                      const SizedBox(height: 4),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.phone_android_outlined, size: 14, color: Colors.white70),
                          const SizedBox(width: 5),
                          Text(
                            phone,
                            style: const TextStyle(color: Colors.white70, fontSize: 13),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              ),

              const SizedBox(height: 18),

              // Order Status Shortcut Section
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 18),
                child: Card(
                  elevation: 3,
                  shadowColor: Colors.black.withOpacity(0.06),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(18),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              "Pesanan Saya",
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: Colors.black87,
                              ),
                            ),
                            InkWell(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (_) => const OrderPage()),
                                );
                              },
                              child: const Row(
                                children: [
                                  Text(
                                    "Lihat Riwayat",
                                    style: TextStyle(
                                      color: AppColors.primary,
                                      fontSize: 12,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  Icon(Icons.chevron_right, size: 16, color: AppColors.primary),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            _buildOrderStatusIcon(
                              icon: Icons.account_balance_wallet_outlined,
                              label: "Belum Bayar",
                              onTap: () => _openOrders(context),
                            ),
                            _buildOrderStatusIcon(
                              icon: Icons.soup_kitchen_outlined,
                              label: "Diproses",
                              onTap: () => _openOrders(context),
                            ),
                            _buildOrderStatusIcon(
                              icon: Icons.delivery_dining_outlined,
                              label: "Dikirim",
                              onTap: () => _openOrders(context),
                            ),
                            _buildOrderStatusIcon(
                              icon: Icons.task_alt_outlined,
                              label: "Selesai",
                              onTap: () => _openOrders(context),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 16),

              // Menu Group 1: Akun & Informasi
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 18),
                child: Card(
                  elevation: 3,
                  shadowColor: Colors.black.withOpacity(0.06),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(18),
                  ),
                  child: Column(
                    children: [
                      _buildMenuItem(
                        icon: Icons.person_outline_rounded,
                        iconColor: Colors.blue,
                        title: "Edit Profil",
                        subtitle: "Ubah nama, nomor HP & alamat",
                        onTap: () async {
                          final result = await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => EditProfilePage(user: user),
                            ),
                          );
                          if (result == true) {
                            refresh();
                          }
                        },
                      ),
                      const Divider(height: 1, indent: 60),
                      _buildMenuItem(
                        icon: Icons.receipt_long_outlined,
                        iconColor: Colors.orange.shade700,
                        title: "Riwayat Pesanan",
                        subtitle: "Daftar transaksi pesanan Anda",
                        onTap: () => _openOrders(context),
                      ),
                      const Divider(height: 1, indent: 60),
                      _buildMenuItem(
                        icon: Icons.location_on_outlined,
                        iconColor: Colors.green,
                        title: "Alamat Pengiriman",
                        subtitle: address.length > 25 ? "${address.substring(0, 25)}..." : address,
                        onTap: () => _showAddressBottomSheet(context, address, user),
                      ),
                      const Divider(height: 1, indent: 60),
                      _buildMenuItem(
                        icon: Icons.lock_outline_rounded,
                        iconColor: Colors.purple,
                        title: "Ubah Password",
                        subtitle: "Perbarui kata sandi akun",
                        onTap: () => _showChangePasswordBottomSheet(context),
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 16),

              // Menu Group 2: Bantuan & Sesi
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 18),
                child: Card(
                  elevation: 3,
                  shadowColor: Colors.black.withOpacity(0.06),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(18),
                  ),
                  child: Column(
                    children: [
                      _buildMenuItem(
                        icon: Icons.support_agent_outlined,
                        iconColor: Colors.teal,
                        title: "Pusat Bantuan / CS",
                        subtitle: "Hubungi layanan pelanggan DIMVES",
                        onTap: () => _showHelpCenterDialog(context),
                      ),
                      const Divider(height: 1, indent: 60),
                      _buildMenuItem(
                        icon: Icons.logout_rounded,
                        iconColor: Colors.red,
                        title: "Keluar Akun (Logout)",
                        titleColor: Colors.red.shade700,
                        subtitle: "Keluar dari sesi akun saat ini",
                        onTap: () => _showLogoutDialog(context),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildOrderStatusIcon({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.08),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: AppColors.primary, size: 24),
            ),
            const SizedBox(height: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w500,
                color: Colors.grey.shade800,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    Color? titleColor,
    required VoidCallback onTap,
  }) {
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      leading: Container(
        padding: const EdgeInsets.all(9),
        decoration: BoxDecoration(
          color: iconColor.withOpacity(0.12),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Icon(icon, color: iconColor, size: 22),
      ),
      title: Text(
        title,
        style: TextStyle(
          fontWeight: FontWeight.w600,
          fontSize: 15,
          color: titleColor ?? Colors.black87,
        ),
      ),
      subtitle: Text(
        subtitle,
        style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
      ),
      trailing: const Icon(Icons.chevron_right, size: 20, color: Colors.grey),
      onTap: onTap,
    );
  }

  void _openOrders(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const OrderPage()),
    );
  }

  void _showAddressBottomSheet(BuildContext context, String address, Map<String, dynamic> user) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      builder: (context) {
        return Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    "Alamat Pengiriman Utama",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.pop(context),
                  ),
                ],
              ),
              const SizedBox(height: 15),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.grey.shade300),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.location_on, color: AppColors.primary, size: 28),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        address != "Belum diatur" && address.isNotEmpty
                            ? address
                            : "Belum ada alamat pengiriman tersimpan.",
                        style: const TextStyle(fontSize: 14, height: 1.4),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  icon: const Icon(Icons.edit),
                  label: const Text("Edit Alamat Pengiriman"),
                  onPressed: () async {
                    Navigator.pop(context);
                    final result = await Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => EditProfilePage(user: user),
                      ),
                    );
                    if (result == true) {
                      refresh();
                    }
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showChangePasswordBottomSheet(BuildContext context) {
    final oldPasswordController = TextEditingController();
    final newPasswordController = TextEditingController();
    bool isOldObscured = true;
    bool isNewObscured = true;
    bool isLoading = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Padding(
              padding: EdgeInsets.only(
                left: 24,
                right: 24,
                top: 24,
                bottom: MediaQuery.of(context).viewInsets.bottom + 24,
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        "Ubah Password Akun",
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close),
                        onPressed: () => Navigator.pop(context),
                      ),
                    ],
                  ),
                  const SizedBox(height: 15),
                  TextField(
                    controller: oldPasswordController,
                    obscureText: isOldObscured,
                    decoration: InputDecoration(
                      labelText: "Password Lama",
                      prefixIcon: const Icon(Icons.lock_outline, color: AppColors.primary),
                      suffixIcon: IconButton(
                        icon: Icon(
                          isOldObscured ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                        ),
                        onPressed: () {
                          setModalState(() {
                            isOldObscured = !isOldObscured;
                          });
                        },
                      ),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                  const SizedBox(height: 15),
                  TextField(
                    controller: newPasswordController,
                    obscureText: isNewObscured,
                    decoration: InputDecoration(
                      labelText: "Password Baru (Min. 6 karakter)",
                      prefixIcon: const Icon(Icons.lock_reset, color: AppColors.primary),
                      suffixIcon: IconButton(
                        icon: Icon(
                          isNewObscured ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                        ),
                        onPressed: () {
                          setModalState(() {
                            isNewObscured = !isNewObscured;
                          });
                        },
                      ),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onPressed: isLoading
                          ? null
                          : () async {
                              final oldPass = oldPasswordController.text;
                              final newPass = newPasswordController.text;

                              if (oldPass.isEmpty || newPass.isEmpty) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text("Password lama dan password baru wajib diisi"),
                                    backgroundColor: Colors.orange,
                                  ),
                                );
                                return;
                              }

                              if (newPass.length < 6) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text("Password baru minimal 6 karakter"),
                                    backgroundColor: Colors.orange,
                                  ),
                                );
                                return;
                              }

                              final messenger = ScaffoldMessenger.of(context);
                              final navigator = Navigator.of(context);

                              setModalState(() {
                                isLoading = true;
                              });

                              final result = await ApiService().changePassword(
                                oldPassword: oldPass,
                                newPassword: newPass,
                              );

                              if (!mounted) return;

                              setModalState(() {
                                isLoading = false;
                              });

                              if (result["success"] == true) {
                                navigator.pop();
                                messenger.showSnackBar(
                                  SnackBar(
                                    content: Text(result["message"] ?? "Password berhasil diperbarui!"),
                                    backgroundColor: Colors.green,
                                  ),
                                );
                              } else {
                                messenger.showSnackBar(
                                  SnackBar(
                                    content: Text(result["message"] ?? "Gagal mengubah password"),
                                    backgroundColor: Colors.red,
                                  ),
                                );
                              }
                            },
                      child: isLoading
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                            )
                          : const Text("Simpan Password Baru"),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _showHelpCenterDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Row(
          children: [
            Icon(Icons.support_agent, color: AppColors.primary),
            SizedBox(width: 10),
            Text("Pusat Bantuan"),
          ],
        ),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text("Butuh bantuan terkait pesanan atau kendala akun DIMVES?"),
            SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.phone, color: Colors.green, size: 20),
                SizedBox(width: 8),
                Text("WhatsApp: +62 812-3456-7890"),
              ],
            ),
            SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.email, color: AppColors.primary, size: 20),
                SizedBox(width: 8),
                Text("Email: support@dimves.com"),
              ],
            ),
            SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.access_time, color: Colors.orange, size: 20),
                SizedBox(width: 8),
                Text("Jam Operasional: 09.00 - 21.00 WIB"),
              ],
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text("Tutup", style: TextStyle(fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Row(
          children: [
            Icon(Icons.warning_amber_rounded, color: Colors.red),
            SizedBox(width: 10),
            Text("Konfirmasi Keluar"),
          ],
        ),
        content: const Text(
          "Apakah Anda yakin ingin keluar dari akun DIMVES Anda?",
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text("Batal", style: TextStyle(color: Colors.grey)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            onPressed: () async {
              Navigator.pop(context);
              await ApiService().logout();
              refresh();
              if (!mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text("Anda telah berhasil logout"),
                  backgroundColor: Colors.black87,
                ),
              );
            },
            child: const Text("Ya, Keluar"),
          ),
        ],
      ),
    );
  }
}
