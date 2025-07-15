<?php 
require './app/utils.php';
include './app/views/admin/templates/header.php';
?>
<div class="uk-grid">
    <div class="uk-width-medium-1-1">
        <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions uk-padding-top-0 uk-margin-top--5">
                    <form>
                        <button class="md-btn md-btn-small md-btn-flat md-btn-primary uk-float-right uk-margin-small-left button-toolbar" type="submit" id="Search_Reserva">
                            <i class="uk-icon-search uk-text-contrast no_margin"></i>
                        </button>
                        <div class="uk-float-right">
                            <label>Buscar</label>
                            <input type="text" class="md-input" name="reserva_nombre" id="reserva_nombre">
                            <span class="md-input-bar"></span>
                        </div>
                    </form>
                </div>
                <h3 class="md-card-toolbar-heading-text">
                    Reservas
                </h3>
            </div>
            <div class="md-card-content">
                <div id="reservas_crud"></div>
            </div>
        </div>
    </div>
</div>
<script>
 var EVENTOS= <?= json_encode($eventos) ?>;
</script>
<?php 
include './app/views/admin/templates/footer.php';
?>
