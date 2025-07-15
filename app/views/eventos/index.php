<?php 
require './app/utils.php';
include './app/views/templates/header.php';
?>
<h3 class="heading_b uk-margin-bottom">Eventos</h3>
<div class="blog_list uk-grid-width-medium-1-3 uk-grid-width-large-1-4" data-uk-grid="{gutter: 24}">
    <?php foreach ($eventos as $evento): ?>
    <div>
        <div class="md-card">
            <div class="md-card-content small-padding">
                <?php if (!empty($evento['imagen'])): ?>
                    <a href="/evento/detalle/<?= $evento['id'] ?>" class="evento_imagen"><img src="<?= htmlspecialchars($evento['imagen']) ?>" class="blog_list_teaser_image" alt="<?= htmlspecialchars($evento['nombre']) ?>"></a>
                <?php endif; ?>
                <div class="blog_list_teaser">
                    <h2 class="blog_list_teaser_title uk-text-truncate"><?= htmlspecialchars($evento['nombre']) ?></h2>
                    <p class="uk-text-large"><?= nl2br(htmlspecialchars($evento['descripcion'])) ?></p>
                </div>
                <div class="blog_list_footer uk-text-center">
                    <div class="blog_list_footer_info">
                        <span class="uk-margin-right"><i class="material-icons">today</i> <strong><?= date("d/m/Y H:i",strtotime($evento['fecha_evento'])) ?></strong></span>
                    </div>
                    <a href="/evento/detalle/<?= $evento['id'] ?>" class="md-btn md-btn-primary">Reservar</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php include './app/views/templates/footer.php'; ?>
