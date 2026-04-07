<?php $site = app_config()['site']; ?>
    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <section class="footer-card">
                <h3>Akasha Production</h3>
                <p><strong>Responsable:</strong> <?= htmlspecialchars($site['responsable'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php foreach ($site['address_lines'] as $line): ?>
                    <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
                <p><strong>Tél :</strong> <?= htmlspecialchars($site['phone'], ENT_QUOTES, 'UTF-8'); ?>.</p>
                <p><strong>Mail :</strong> <a href="<?= htmlspecialchars(app_nav_href('contact'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($site['contact_email_label'], ENT_QUOTES, 'UTF-8'); ?></a></p>
                <div class="footer-hosting">
                    <p><strong>Hébergé par <?= htmlspecialchars($site['host']['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    <?php foreach ($site['host']['address_lines'] as $line): ?>
                        <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                    <?php foreach ($site['host']['legal_lines'] as $line): ?>
                        <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                    <p>Tél : <?= htmlspecialchars($site['host']['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><a href="<?= htmlspecialchars($site['host']['website'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">Site de l’hébergeur</a></p>
                </div>
            </section>
            <section class="footer-card">
                <h3>Liens internes</h3>
                <ul class="footer-links">
                    <?php foreach (app_config()['navigation'] as $entry): ?>
                        <li><a href="<?= htmlspecialchars($entry['href'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                    <li><a href="/admin">Panel administrateur</a></li>
                </ul>
            </section>
            <section class="footer-card">
                <h3>Facebook</h3>
                <div class="facebook-card">
                    <iframe
                        title="Facebook Akasha Production"
                        loading="lazy"
                        src="https://www.facebook.com/plugins/page.php?href=<?= rawurlencode($site['facebook_url']); ?>&tabs=timeline&width=340&height=280&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId="
                        width="340"
                        height="280"
                        style="border:none;overflow:hidden"
                        allowfullscreen="true"
                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
            </section>
        </div>
    </footer>
</div>
<script src="/assets/site.js?v=1" defer></script>
</body>
</html>
