<?php
// footer.php - Pro Version (Tailwind CSS Integrated)
$baseUrl = $baseUrl ?? "/FangakYouthUnion/public/";
?>

<footer class="bg-[#0a1f15] text-gray-300 border-t-4 border-fyu-primary relative overflow-hidden">
    
    <div class="absolute top-0 right-0 w-64 h-64 bg-fyu-gold/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-fyu-primary/5 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8 relative z-10 fyu-footer-animate opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 mb-12">
            
            <div class="lg:col-span-4">
                <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3 mb-6 group inline-flex">
                    <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Logo" class="w-14 h-14 rounded-full border-2 border-fyu-gold shadow-lg group-hover:scale-105 transition-transform duration-300">
                    <span class="font-serif font-bold text-2xl text-white tracking-wide group-hover:text-fyu-gold transition-colors">Fangak Youth Union</span>
                </a>
                <p class="text-sm leading-relaxed text-gray-400 mb-6">
                    <strong class="text-white font-medium">Empowering youth</strong>, fostering innovation, and building a stronger, resilient community across Fangak County through targeted development projects and collective leadership.
                </p>
                <a href="<?= $baseUrl ?>about.php" class="inline-flex items-center text-sm font-medium text-fyu-gold hover:text-white transition-colors group">
                    Discover our mission 
                    <i class="fa-solid fa-arrow-right ml-2 text-xs transform group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="lg:col-span-3 lg:col-start-6">
                <h3 class="text-white font-semibold text-lg mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-fyu-gold"></span> Quick Links
                </h3>
                <ul class="space-y-3">
                    <?php 
                    $footerLinks = [
                        ['url' => 'index.php', 'icon' => 'fa-house', 'label' => 'Home'],
                        ['url' => 'about.php', 'icon' => 'fa-users', 'label' => 'About Us'],
                        ['url' => 'project.php', 'icon' => 'fa-lightbulb', 'label' => 'Our Projects'],
                        ['url' => 'blog.php', 'icon' => 'fa-newspaper', 'label' => 'News & Blog'],
                        ['url' => 'contact.php', 'icon' => 'fa-handshake', 'label' => 'Get Involved']
                    ];
                    foreach($footerLinks as $link): ?>
                        <li>
                            <a href="<?= $baseUrl . $link['url'] ?>" class="group flex items-center text-gray-400 hover:text-fyu-gold transition-all duration-300">
                                <i class="fa-solid <?= $link['icon'] ?> w-5 text-fyu-primary group-hover:text-fyu-gold transition-colors"></i>
                                <span class="transform group-hover:translate-x-1 transition-transform"><?= $link['label'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="lg:col-span-4">
                <h3 class="text-white font-semibold text-lg mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-fyu-gold"></span> Contact Us
                </h3>
                
                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="pt-1.5">
                            <p class="text-white font-medium">Headquarters</p>
                            <p class="text-gray-400">Juba, South Sudan<br>(Field Ops in Fangak)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="pt-1.5">
                            <a href="mailto:info@fangakyouthunion.org" class="hover:text-fyu-gold transition-colors">info@fangakyouthunion.org</a>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="pt-1.5">
                            <a href="tel:+211912345678" class="hover:text-fyu-gold transition-colors">+211 912345678</a>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <?php
                        $socials = [
                            'fa-facebook-f'  => 'https://www.facebook.com/profile.php?id=61576158274794',
                            'fa-x-twitter'   => '#',
                            'fa-linkedin-in' => '#',
                            'fa-instagram'   => '#'
                        ];

                        foreach ($socials as $icon => $link): ?>

                            <a href="<?= $link ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-10 h-10 rounded-lg bg-fyu-primary/20 text-white flex items-center justify-center hover:bg-fyu-gold hover:-translate-y-1 hover:shadow-[0_4px_12px_rgba(212,160,23,0.4)] transition-all duration-300">

                                <i class="fa-brands <?= $icon ?>"></i>

                            </a>

                        <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Footer Bottom Section with Modal Popups for Privacy Policy and Terms -->
<div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">
    
    <!-- Copyright -->
    <p>&copy; <?= date("Y"); ?> Fangak Youth Union. All Rights Reserved.</p>

    <!-- Policy Links as Buttons -->
    <div class="flex gap-4">
        <button onclick="openModal('privacyModal')" 
            class="hover:text-white transition-colors focus:outline-none">
            Privacy Policy
        </button>

        <button onclick="openModal('termsModal')" 
            class="hover:text-white transition-colors focus:outline-none">
            Terms of Service
        </button>
    </div>
</div>

<!-- ================= Privacy Policy Modal ================= -->
<div id="privacyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white text-gray-800 max-w-2xl w-[90%] rounded-2xl shadow-2xl p-6 relative animate-fadeIn">
        
        <!-- Close Button -->
        <button onclick="closeModal('privacyModal')" 
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-900 text-lg">
            &times;
        </button>

        <h2 class="text-xl font-semibold mb-4 text-fyu-primary">Privacy Policy</h2>

        <div class="max-h-[60vh] overflow-y-auto text-sm leading-relaxed space-y-3">
            <p>
                Fangak Youth Union respects your privacy and is committed to protecting any information you share with us.
                We collect only the data necessary to provide services, communicate with members, and improve our programs.
            </p>

            <p>
                Your personal information will never be sold or shared with third parties without consent unless required by law.
                We implement reasonable security measures to safeguard your information.
            </p>

            <p>
                By using our website, you agree to the collection and use of information in accordance with this policy.
            </p>
        </div>
    </div>
</div>

<!-- ================= Terms of Service Modal ================= -->
<div id="termsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white text-gray-800 max-w-2xl w-[90%] rounded-2xl shadow-2xl p-6 relative animate-fadeIn">
        
        <!-- Close Button -->
        <button onclick="closeModal('termsModal')" 
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-900 text-lg">
            &times;
        </button>

        <h2 class="text-xl font-semibold mb-4 text-fyu-primary">Terms of Service</h2>

        <div class="max-h-[60vh] overflow-y-auto text-sm leading-relaxed space-y-3">
            <p>
                By accessing this website, you agree to comply with all applicable laws and regulations.
                You are responsible for ensuring that your use of the site is lawful and respectful.
            </p>

            <p>
                Fangak Youth Union reserves the right to update or modify these terms at any time without prior notice.
                Continued use of the website constitutes acceptance of the revised terms.
            </p>

            <p>
                Misuse of the website, including unauthorized access or harmful activities, may result in restricted access or legal action.
            </p>
        </div>
    </div>
</div>
        </div>
    </div>
</footer>

<script>
    // Smooth scroll reveal observer
    document.addEventListener("DOMContentLoaded", () => {
        const footerContent = document.querySelector('.fyu-footer-animate');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting){
                    footerContent.classList.remove('opacity-0', 'translate-y-10');
                    footerContent.classList.add('opacity-100', 'translate-y-0');
                    observer.unobserve(footerContent); 
                }
            });
        }, { threshold: 0.1 });
        
        observer.observe(footerContent);
    });

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside content
        window.addEventListener('click', function(e) {
            ['privacyModal', 'termsModal'].forEach(function(id) {
                const modal = document.getElementById(id);
                if (e.target === modal) {
                    closeModal(id);
                }
            });
        });

        <!-- Optional simple fade animation -->
        <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.25s ease-out;
        }
        </style>

</script>