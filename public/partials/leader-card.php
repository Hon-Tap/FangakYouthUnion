<?php
// expects $leader (array) and $index (int) to be set by caller
$hideClass = isset($hidden) ? $hidden : '';
?>

<div class="leader-card fade-in <?= $hideClass ?>" 
     data-index="<?= htmlspecialchars($index) ?>" 
     tabindex="0" 
     role="button" 
     aria-pressed="false"
     aria-label="<?= htmlspecialchars($leader['name'] . ', ' . $leader['role']) ?>">

    <!-- Leader Photo -->
    <div class="leader-photo-wrap" style="position:relative;">
        <img class="leader-photo" 
             loading="lazy" 
             src="<?= $baseUrl . htmlspecialchars($leader['img']) ?>" 
             alt="<?= htmlspecialchars($leader['name']) ?>">
        <!-- Optional overlay with name on hover -->
        <div class="leader-overlay">
            <h4><?= htmlspecialchars($leader['name']) ?></h4>
        </div>
    </div>

    <!-- Leader Info -->
    <div class="leader-info">
        <h3><?= htmlspecialchars($leader['name']) ?></h3>
        <p class="role"><?= htmlspecialchars($leader['role']) ?></p>
        <p class="desc"><?= htmlspecialchars($leader['desc']) ?></p>
    </div>
</div>

<style>
/* Uniform card height & hover effect */
.leader-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    height: 400px; /* uniform height */
}

.leader-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

/* Leader photo */
.leader-photo-wrap {
    flex-shrink: 0;
}
.leader-photo {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

/* Overlay on hover */
.leader-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(44,122,75,0.75);
    color: #fff;
    text-align: center;
    padding: 8px 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.leader-card:hover .leader-overlay {
    opacity: 1;
}

/* Leader info */
.leader-info {
    padding: 16px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1;
}
.leader-info h3 {
    margin: 10px 0 6px;
    font-size: 1.2rem;
}
.leader-info p.role {
    color: #2c7a4b;
    font-weight: 600;
    margin: 0;
}
.leader-info p.desc {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 8px;
    flex-grow: 1;
}
</style>
