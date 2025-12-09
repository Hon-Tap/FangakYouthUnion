<aside class="sidebar">
    <div class="widget">
        <h3 class="widget-title">Announcements</h3>
        <?php if($announcements): foreach($announcements as $ann): ?>
        <div class="announcement-item">
            <span class="ann-date"><?= date('M d', strtotime($ann['created_at'])) ?></span>
            <a href="#" class="ann-title"><?= htmlspecialchars($ann['title']) ?></a>
        </div>
        <?php endforeach; else: ?>
        <p style="color:var(--color-muted)">No recent announcements.</p>
        <?php endif; ?>
    </div>
    <div class="widget">
        <h3 class="widget-title">Newsletter</h3>
        <p style="margin-bottom:16px; font-size:0.9rem; color:var(--color-muted)">Get the latest updates delivered to your inbox.</p>
        <form id="subForm" class="sub-form" onsubmit="handleSubscribe(event)">
            <input type="email" name="email" class="sub-input" placeholder="Your email" required>
            <button type="submit" class="sub-btn"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
        <div id="subMsg"></div>
    </div>
</aside>