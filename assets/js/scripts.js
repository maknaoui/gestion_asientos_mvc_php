$(function() {
    const seleccionados = new Set();

    $(document).on("change","#numero_personas",function(e){
        e.preventDefault();
        $(".asiento").each(function() {
            const $asiento = $(this);
            const id = parseInt($asiento.attr('data-id'));
            if ($asiento.hasClass('seleccionado')) {
                if (!seleccionados.has(id)) {
                    seleccionados.add(id);
                }
            }
        });
        
        let numeroPersonas = parseInt($(this).val());
        if (isNaN(numeroPersonas) || numeroPersonas < 1) {
            UIkit.modal.alert('Por favor, ingresa un número válido de personas.');
            $(this).val(1);
            return;
        }
        
        let max = parseInt($('#mapa_asientos').data('max')) || 1;
        if(numeroPersonas>max){
            UIkit.modal.alert('El número de personas no puede ser mayor que el máximo permitido (' + max + ').');
            numeroPersonas=max;
            $('#mapa_asientos').attr('data-max-select',numeroPersonas);
            $("#max_personas").text(numeroPersonas- seleccionados.size);
            $("#quedan").text(max- seleccionados.size);
            $(this).val(max);
        }
        
        $('#mapa_asientos').attr('data-max-select',numeroPersonas);
        $("#max_personas").text(numeroPersonas- seleccionados.size);
        $("#quedan").text(max- seleccionados.size);
        
    });

    if($("#numero_personas").length>0) {
        $("#numero_personas").trigger('change');
    }

    $(document).on('click','.asiento', function () {
        const $asiento = $(this);
        const id = parseInt($asiento.attr('data-id'));
        let max_select = parseInt($('#mapa_asientos').attr('data-max-select')) || 1;
        let max = parseInt($('#mapa_asientos').attr('data-max')) || 1;

        if ($asiento.hasClass('ocupado') || $asiento.hasClass('deshabilitado')) {
            return; // no se puede seleccionar un asiento ocupado
        }

        if ($asiento.hasClass('seleccionado')) {
            // deseleccionar
            $asiento.removeClass('seleccionado');
            seleccionados.delete(id);
        } else {
            if (seleccionados.size >= max_select) {
                UIkit.modal.alert('Ya has seleccionado el máximo permitido de asientos.');
                return;
            }
            // seleccionar
            $asiento.addClass('seleccionado');
            seleccionados.add(id);
        }

        // actualizar el input hidden
        $('#asientos_seleccionados').val(Array.from(seleccionados).join(','));

        // actualizar el contador
        $('#quedan').text(max - seleccionados.size);
        $("#max_personas").text(max_select - seleccionados.size);
    });
    var $phone = $('.telefono');
    if($phone.length) {
        $phone.kendoMaskedTextBox({
            mask: "9 0000 0000"
        });
    }
    

    // Inicializar tooltips
    setTimeout(() => {
        UIkit.tooltip('.asiento');
        rutInput();
    }, 100);
    
});

$(function() {
    // wizard
    altair_wizard.advanced_wizard();
});

function rutInput(){
    $(".rut")
        .rut({formatOn: 'keyup', validateOn: 'keyup'})
        .on('rutInvalido', function(){ 
        $(this).parents(".md-input-wrapper").addClass("md-input-wrapper-danger").removeClass("md-input-wrapper-success")
        })
        .on('rutValido', function(){ 
        $(this).parents(".md-input-wrapper").removeClass("md-input-wrapper-danger").addClass("md-input-wrapper-success")
    });
}

// wizard
altair_wizard = {
    content_height: function(this_wizard,step) {
        var this_height = $(this_wizard).find('.step-'+ step).actual('outerHeight');
        $(this_wizard).children('.content').animate({ height: this_height }, 140, bez_easing_swiftOut);
    },
    advanced_wizard: function() {
        var $wizard_advanced = $('#wizard_advanced'),
            $wizard_advanced_form = $('#wizard_advanced_form');

        if ($wizard_advanced.length) {
            $wizard_advanced.steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                trigger: 'change',
                onInit: function(event, currentIndex) {
                    altair_wizard.content_height($wizard_advanced,currentIndex);
                    // reinitialize textareas autosize
                    altair_forms.textarea_autosize();
                    // reinitialize checkboxes
                    altair_md.checkbox_radio($(".wizard-icheck"));
                    $(".wizard-icheck").on('ifChecked', function(event){
                        console.log(event.currentTarget.value);
                    });
                    // reinitialize uikit margin
                    altair_uikit.reinitialize_grid_margin();
                    // reinitialize selects
                    altair_forms.select_elements($wizard_advanced);
                    // reinitialize switches
                    $wizard_advanced.find('span.switchery').remove();
                    altair_forms.switches();
                    // reinitialize dynamic grid
                    altair_forms.dynamic_fields($wizard_advanced,true,true);
                    // resize content when accordion is toggled
                    $('.uk-accordion').on('toggle.uk.accordion',function() {
                        $window.resize();
                    });

                    setTimeout(function() {
                        $window.resize();
                    },100);
                },
                onStepChanged: function (event, currentIndex) {
                    altair_wizard.content_height($wizard_advanced,currentIndex);
                    setTimeout(function() {
                        $window.resize();
                    },100)
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    var step = $wizard_advanced.find('.body.current').attr('data-step'),
                        $current_step = $('.body[data-step=\"'+ step +'\"]');

                    // check input fields for errors
                    $current_step.find('.parsley-row').each(function() {
                        $(this).find('input,textarea,select').each(function() {
                            $(this).parsley().validate();
                        });
                    });

                    // adjust content height
                    $window.resize();

                    return $current_step.find('.parsley-error').length ? false : true;
                },
                onFinished: function() {
                    var form_serialized = JSON.stringify( $wizard_advanced_form.serializeObject(), null, 2 );
                    $("#wizard_advanced_form").trigger('submit');
                }
            })/*.steps("setStep", 2)*/;

            $window.on('debouncedresize',function() {
                var current_step = $wizard_advanced.find('.body.current').attr('data-step');
                altair_wizard.content_height($wizard_advanced,current_step);
            });

            // wizard
            $wizard_advanced_form
                .parsley()
                .on('form:validated',function() {
                    setTimeout(function() {
                        altair_md.update_input($wizard_advanced_form.find('.md-input'));
                        // adjust content height
                        $window.resize();
                    },100)
                })
                .on('field:validated',function(parsleyField) {

                    var $this = $(parsleyField.$element);
                    setTimeout(function() {
                        altair_md.update_input($this);
                        // adjust content height
                        var currentIndex = $wizard_advanced.find('.body.current').attr('data-step');
                        altair_wizard.content_height($wizard_advanced,currentIndex);
                    },100);

                });

        }
    }

};