import 'package:flutter/material.dart';

import '../../core/app_colors.dart';
import '../../services/api_service.dart';
import '../main/main_page.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final nameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();

  bool isLoading = false;
  bool isPasswordObscured = true;
  bool isConfirmPasswordObscured = true;
  bool agreeTerms = true;

  Future<void> register() async {
    final name = nameController.text.trim();
    final email = emailController.text.trim();
    final phone = phoneController.text.trim();
    final password = passwordController.text;
    final confirmPassword = confirmPasswordController.text;

    if (name.isEmpty ||
        email.isEmpty ||
        password.isEmpty ||
        confirmPassword.isEmpty) {
      _showSnackBar("Semua kolom bertanda * wajib diisi", isError: true);
      return;
    }

    if (!agreeTerms) {
      _showSnackBar("Anda wajib menyetujui Syarat & Ketentuan", isError: true);
      return;
    }

    if (password != confirmPassword) {
      _showSnackBar("Konfirmasi password tidak cocok", isError: true);
      return;
    }

    if (password.length < 6) {
      _showSnackBar("Password minimal 6 karakter", isError: true);
      return;
    }

    setState(() {
      isLoading = true;
    });

    final res = await ApiService().register(
      name: name,
      email: email,
      phone: phone,
      password: password,
    );

    if (!mounted) return;

    setState(() {
      isLoading = false;
    });

    if (res["success"] == true) {
      _showSnackBar(
        res["message"] ??
            "Pendaftaran akun berhasil! Anda telah otomatis masuk.",
        isError: false,
      );

      await Future.delayed(const Duration(milliseconds: 900));

      if (!mounted) return;

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const MainPage()),
        (route) => false,
      );
    } else {
      _showSnackBar(
        res["message"] ??
            "Pendaftaran gagal. Silakan periksa kembali data Anda.",
        isError: true,
      );
    }
  }

  void _showSnackBar(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        backgroundColor: isError ? Colors.red.shade600 : Colors.green.shade600,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        content: Row(
          children: [
            Icon(
              isError ? Icons.error_outline : Icons.check_circle,
              color: Colors.white,
            ),
            const SizedBox(width: 10),
            Expanded(child: Text(message)),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      appBar: AppBar(
        title: const Text("Pendaftaran Akun"),
        centerTitle: true,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Top Header Banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(
                left: 24,
                right: 24,
                bottom: 25,
                top: 15,
              ),
              decoration: const BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(30),
                  bottomRight: Radius.circular(30),
                ),
              ),
              child: Column(
                children: [
                  const Icon(
                    Icons.person_add_alt_1_rounded,
                    size: 50,
                    color: Colors.white,
                  ),
                  const SizedBox(height: 10),
                  const Text(
                    "Bergabung Bersama DIMVES",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    "Buat akun baru untuk memudahkan pemesanan makanan & nikmati promo menarik",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.9),
                      fontSize: 13,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 18),

            // Form Card
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Card(
                elevation: 4,
                shadowColor: Colors.black.withOpacity(0.08),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(22),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        "Informasi Akun Baru",
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 18),

                      // Nama Lengkap
                      _buildTextField(
                        controller: nameController,
                        label: "Nama Lengkap *",
                        hint: "Masukkan nama lengkap Anda",
                        icon: Icons.person_outline,
                      ),

                      const SizedBox(height: 16),

                      // Email
                      _buildTextField(
                        controller: emailController,
                        label: "Alamat Email *",
                        hint: "contoh@email.com",
                        icon: Icons.email_outlined,
                        keyboardType: TextInputType.emailAddress,
                      ),

                      const SizedBox(height: 16),

                      // Nomor Telepon / HP
                      _buildTextField(
                        controller: phoneController,
                        label: "Nomor Telepon / HP",
                        hint: "Contoh: 08------",
                        icon: Icons.phone_android_outlined,
                        keyboardType: TextInputType.phone,
                      ),

                      const SizedBox(height: 16),

                      // Password Field
                      _buildTextField(
                        controller: passwordController,
                        label: "Password * (Min. 6 karakter)",
                        hint: "Masukkan password *",
                        icon: Icons.lock_outline,
                        isObscured: isPasswordObscured,
                        suffixIcon: IconButton(
                          icon: Icon(
                            isPasswordObscured
                                ? Icons.visibility_off_outlined
                                : Icons.visibility_outlined,
                            color: Colors.grey.shade600,
                          ),
                          onPressed: () {
                            setState(() {
                              isPasswordObscured = !isPasswordObscured;
                            });
                          },
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Confirm Password Field
                      _buildTextField(
                        controller: confirmPasswordController,
                        label: "Konfirmasi Password *",
                        hint: "Ulangi password di atas",
                        icon: Icons.lock_reset_outlined,
                        isObscured: isConfirmPasswordObscured,
                        suffixIcon: IconButton(
                          icon: Icon(
                            isConfirmPasswordObscured
                                ? Icons.visibility_off_outlined
                                : Icons.visibility_outlined,
                            color: Colors.grey.shade600,
                          ),
                          onPressed: () {
                            setState(() {
                              isConfirmPasswordObscured =
                                  !isConfirmPasswordObscured;
                            });
                          },
                        ),
                      ),

                      const SizedBox(height: 14),

                      // Terms Checkbox
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          SizedBox(
                            width: 24,
                            height: 24,
                            child: Checkbox(
                              value: agreeTerms,
                              activeColor: AppColors.primary,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(4),
                              ),
                              onChanged: (value) {
                                setState(() {
                                  agreeTerms = value ?? true;
                                });
                              },
                            ),
                          ),
                          const SizedBox(width: 10),
                          const Expanded(
                            child: Text(
                              "Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi DIMVES.",
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 22),

                      // Submit Button
                      SizedBox(
                        width: double.infinity,
                        height: 52,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            elevation: 3,
                            shadowColor: AppColors.primary.withOpacity(0.4),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                          onPressed: isLoading ? null : register,
                          child: isLoading
                              ? const SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(
                                    color: Colors.white,
                                    strokeWidth: 2.5,
                                  ),
                                )
                              : const Text(
                                  "Daftar Sekarang",
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            // Already registered?
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  "Sudah punya akun?",
                  style: TextStyle(color: Colors.grey.shade700, fontSize: 14),
                ),
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text(
                    "Masuk Di Sini",
                    style: TextStyle(
                      color: AppColors.primary,
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    TextInputType keyboardType = TextInputType.text,
    bool isObscured = false,
    Widget? suffixIcon,
  }) {
    return TextField(
      controller: controller,
      keyboardType: keyboardType,
      obscureText: isObscured,
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixIcon: Icon(icon, color: AppColors.primary),
        suffixIcon: suffixIcon,
        filled: true,
        fillColor: Colors.grey.shade50,
        contentPadding: const EdgeInsets.symmetric(
          vertical: 16,
          horizontal: 16,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.grey.shade300),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.grey.shade300),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
      ),
    );
  }
}
