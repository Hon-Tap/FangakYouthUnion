<?php
// footer.php - Pro Version (Tailwind CSS Integrated)
$baseUrl = $baseUrl ?? "/FangakYouthUnion/public/";
?>

<footer class="bg-[#0a1f15] text-gray-300 border-t-4 border-fyu-primary relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-fyu-gold/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-fyu-primary/5 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 pointer-events-none"></div>

    <div id="fyu-footer-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8 relative z-10 transition-all duration-1000 ease-out transform translate-y-4 opacity-90">
        
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
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold border border-white/10">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-white font-medium">Headquarters</p>
                            <p class="text-gray-400">Juba, South Sudan (Field Ops in Fangak)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold border border-white/10">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="pt-1">
                            <a href="mailto:info@fangakyouthunion.org" class="hover:text-fyu-gold transition-colors">info@fangakyouthunion.org</a>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 text-fyu-gold border border-white/10">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="pt-1">
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
                            <a href="<?= $link ?>" target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 rounded-lg bg-white/5 text-white flex items-center justify-center border border-white/10 hover:bg-fyu-gold hover:border-fyu-gold hover:-translate-y-1 transition-all duration-300">
                                <i class="fa-brands <?= $icon ?>"></i>
                            </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">
            <p>&copy; <?= date("Y"); ?> Fangak Youth Union. All Rights Reserved.</p>
            <div class="flex gap-6">
                <button onclick="toggleModal('privacyModal', true)" class="hover:text-fyu-gold transition-colors">Privacy Policy</button>
                <button onclick="toggleModal('termsModal', true)" class="hover:text-fyu-gold transition-colors">Terms of Service</button>
            </div>
        </div>
    </div>
</footer>

<div id="privacyModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#050f0a]/90 backdrop-blur-md" onclick="toggleModal('privacyModal', false)"></div>
    <div class="bg-[#1a2e25] border border-white/10 text-gray-300 max-w-2xl w-full rounded-2xl shadow-2xl p-8 relative z-10 transform scale-95 transition-all duration-300 opacity-0 modal-content">
        <button onclick="toggleModal('privacyModal', false)" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-red-500/20 hover:text-red-400 transition-all">&times;</button>
        <h2 class="text-2xl font-serif font-bold mb-4 text-white border-b border-fyu-gold/30 pb-2">Privacy Policy</h2>
        <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-4 text-sm leading-relaxed custom-scrollbar">
            <p>Fangak Youth Union respects your privacy and is committed to protecting any information you share with us. We collect only the data necessary to provide services and improve our programs.</p>
            <p>Your personal information will never be sold or shared with third parties without consent unless required by law. We implement high-standard security measures to safeguard your information.</p>
        </div>
    </div>
</div>

<div id="termsModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#050f0a]/90 backdrop-blur-md" onclick="toggleModal('termsModal', false)"></div>
    <div class="bg-[#1a2e25] border border-white/10 text-gray-300 max-w-2xl w-full rounded-2xl shadow-2xl p-8 relative z-10 transform scale-95 transition-all duration-300 opacity-0 modal-content">
        <button onclick="toggleModal('termsModal', false)" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-red-500/20 hover:text-red-400 transition-all">&times;</button>
        <h2 class="text-2xl font-serif font-bold mb-4 text-white border-b border-fyu-gold/30 pb-2">Terms of Service</h2>
        <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-4 text-sm leading-relaxed custom-scrollbar">
            <p>By accessing this website, you agree to comply with all applicable laws and regulations. You are responsible for ensuring that your use of the site is lawful and respectful.</p>
            <p>Fangak Youth Union reserves the right to update these terms at any time. Continued use of the website constitutes acceptance of the revised terms.</p>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4a017; border-radius: 10px; }
    
    /* Modal active states */
    .modal-show .modal-content { transform: scale(1); opacity: 1; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const footer = document.getElementById('fyu-footer-content');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting){
                    footer.classList.remove('translate-y-4', 'opacity-90');
                    footer.classList.add('translate-y-0', 'opacity-100');
                }
            });
        }, { threshold: 0.1 });
        observer.observe(footer);
    });

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => modal.classList.add('modal-show'), 10);
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.remove('modal-show');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            document.body.style.overflow = 'auto';
        }
    }
</script>