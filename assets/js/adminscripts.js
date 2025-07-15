var spanishMessages = {
    serverCommunicationError: 'Ocurrió un error al comunicarse con el servidor.',
    loadingMessage: 'Cargando registros...',
    noDataAvailable: '¡No hay registros disponibles!',
    addNewRecord: '+ Agregar nuevo registro',
    editRecord: 'Editar registro',
    areYouSure: '¿Estás seguro?',
    deleteConfirmation: 'Este registro será eliminado. ¿Está seguro?',
    save: 'Guardar',
    saving: 'Guardar',
    cancel: 'Cancelar',
    deleteText: 'Eliminar',
    deleting: 'Eliminando',
    error: 'Error',
    close: 'cerrar',
    canLoadOptionsFor: '¡Error al cargar las opciones para el campo {0}!',
    pagingInfo: 'Hay un total de {2}, se muestra desde {0} a {1}',
    canNotDeletedRecords: '¡Error al eliminar {0} de {1} registros!',
    deleteProggress: '{0} de {1} registros eliminados, en progreso...',
    pageSizeChangeLabel: 'Número de filas',
    gotoPageLabel: 'Ir a la página'
};
$(function () {
    if($('#eventos_crud').length) {
        $('#eventos_crud').jtable({
            messages: spanishMessages,
            title: '',
            paging: true,
            sorting: true,
            pageSize: 10,
            addRecordButton: $('#eventoAdd'),
            deleteConfirmation: function (data) {
                data.deleteConfirmMessage = '¿Estás seguro que deseas eliminar el evento "' + data.record.nombre + '"?';
            },
            actions: {
                listAction: '/admin/evento/list',
                createAction: '/admin/evento/create',
                updateAction: '/admin/evento/update',
                deleteAction: '/admin/evento/delete'
            },
            fields: {
                id: { key: true, create: false, edit: false, list: true, title: "ID" },
                nombre: { title: 'Nombre', create: true, edit: true,display: function (data) {
                        return `${data.record.nombre}<br><a href="/admin/asiento/list/${data.record.id}">Ver Asientos</a>`;
                    }
                },
                imagen: {
                    title: 'Imagen',
                    create: true,
                    edit: true,
                    display: function (data) {
                        if (data.record.imagen) {
                            return `<a href="/${data.record.imagen}" data-uk-lightbox="{group:'gallery'}"><img src="/${data.record.imagen}" style="height:50px;"></a>`;
                        }
                        return '';
                    },
                    input: function (data) {
                        let html = `<input type="file" name="imagen" accept="image/*" />`;
                        const record = data.record || {}; 
                        if (record.imagen) {
                            html += `<br><a href="/${record.imagen}" data-uk-lightbox="{group:'gallery'}"><img src="/${record.imagen}" style="height:50px;"><a>`;
                        }
                        if (data.value) {
                            html += `<input type="hidden" name="imagen_actual" value="${data.value}">`;
                        }
                        return html;
                    }
                },
                descripcion: {
                    title: 'Descripción',
                    create: true,
                    edit: true,
                    type: 'textarea',
                    display: function (data) {
                        if (!data.record.descripcion) return '';
                        const maxLength = 50; // o el número de caracteres que prefieras
                        let text = data.record.descripcion;
                        if (text.length > maxLength) {
                            text = text.substring(0, maxLength) + '...';
                        }
                        return `<span title="${data.record.descripcion.replace(/"/g, '&quot;')}">${text}</span>`;
                    }
                },
                maxima_capacidad: { title: 'Capacidad', create: true, edit: true,
                    display: function (data) {
                        return `${data.record.ocupados}/${data.record.maxima_capacidad}`;
                    }
                },
                tipo_id: {
                    title: 'Tipo',
                    create: true,
                    edit: false,
                    options: TIPOS
                },
                fecha_evento: {
                    title: 'Fecha',
                    create: true,
                    edit: true,
                    display: function (data) {
                        if (!data.record.fecha_evento) return '';
                        const date = new Date(data.record.fecha_evento);
                        const day = ('0' + date.getDate()).slice(-2);
                        const month = ('0' + (date.getMonth() + 1)).slice(-2);
                        const year = date.getFullYear();
                        const hours = ('0' + date.getHours()).slice(-2);
                        const minutes = ('0' + date.getMinutes()).slice(-2);
                        return `${day}/${month}/${year} ${hours}:${minutes}`;
                    }
                }
            },
            formCreated: function (event, data) {
                data.form.attr('enctype', 'multipart/form-data');
                data.form.find('input[type="file"]').addClass('md-input');
                data.form.find('.jtable-option-text-clickable').each(function() {
                    var $thisTarget = $(this).prev().attr('id');
                    $(this)
                        .attr('data-click-target',$thisTarget)
                        .off('click')
                        .on('click',function(e) {
                            e.preventDefault();
                            $('#'+$(this).attr('data-click-target')).iCheck('toggle');
                        })
                });
                // create selectize
                data.form.find('select').each(function() {
                    var $this = $(this);
                    $this.after('<div class="selectize_fix"></div>')
                    .selectize({
                        dropdownParent: 'body',
                        placeholder: 'Haz click para seleccionar ...',
                        onDropdownOpen: function($dropdown) {
                            $dropdown
                                .hide()
                                .velocity('slideDown', {
                                    begin: function() {
                                        $dropdown.css({'margin-top':'0'})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        },
                        onDropdownClose: function($dropdown) {
                            $dropdown
                                .show()
                                .velocity('slideUp', {
                                    complete: function() {
                                        $dropdown.css({'margin-top':''})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        }
                    });
                });
                // create icheck
                data.form
                    .find('input[type="checkbox"],input[type="radio"]')
                    .each(function() {
                        var $this = $(this);
                        $this.iCheck({
                            checkboxClass: 'icheckbox_md',
                            radioClass: 'iradio_md',
                            increaseArea: '20%'
                        })
                        .on('ifChecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Active');
                        })
                        .on('ifUnchecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Passive');
                        })
                    });
                var $fechaInput=$("#Edit-fecha_evento");
                $fechaInput.kendoDateTimePicker({
                    value: new Date(),
                    min: new Date(),
                    format: "dd/MM/yyyy HH:mm",
                    parseFormats: ["dd/MM/yyyy HH:mm"]
                });
                // reinitialize inputs
                data.form.find('.jtable-input').children('input[type="text"],input[type="password"],textarea').not('.md-input').each(function() {
                    $(this).addClass('md-input');
                    altair_forms.textarea_autosize();
                });
                altair_md.inputs();
            },
            formSubmitting: function (event, data) {
                event.preventDefault(); // evita el submit por defecto

                const form = data.form[0];
                const formData = new FormData(form);

                // detecta si es create o edit
                let url = '';
                if (data.formType === 'create') {
                    url = '/admin/evento/create';
                } else if (data.formType === 'edit') {
                    url = '/admin/evento/update';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        result = JSON.parse(result);
                        if (result.Result === 'OK') {
                            data.form.closest('.ui-dialog-content').dialog('close');
                            $('#eventos_crud').jtable('reload');
                        } else {
                            UIkit.modal.alert(result.Message);
                        }
                    },
                    error: function () {
                        UIkit.modal.alert('Error al guardar.');
                    }
                });

                return false;
            }


        });

        $('#Search_Evento').click(function (e) {
            e.preventDefault();
            $('#eventos_crud').jtable('load', {
                nombre: $('#evento_nombre').val()
            });
        });

        // cargar al iniciar
        $('#Search_Evento').click();
    }

    if($('#tipo_eventos_crud').length) {
        $('#tipo_eventos_crud').jtable({
            messages: spanishMessages,
            title: '',
            paging: true,
            sorting: true,
            pageSize: 10,
            addRecordButton: $('#tipoEventoAdd'),
            deleteConfirmation: function (data) {
                data.deleteConfirmMessage = '¿Estás seguro que deseas eliminar "' + data.record.nombre + '"?';
            },
            formCreated: function(event, data) {
                // replace click event on some clickable elements
                // to make icheck label works
                data.form.find('.jtable-option-text-clickable').each(function() {
                    var $thisTarget = $(this).prev().attr('id');
                    $(this)
                        .attr('data-click-target',$thisTarget)
                        .off('click')
                        .on('click',function(e) {
                            e.preventDefault();
                            $('#'+$(this).attr('data-click-target')).iCheck('toggle');
                        })
                });
                // create selectize
                data.form.find('select').each(function() {
                    var $this = $(this);
                    $this.after('<div class="selectize_fix"></div>')
                    .selectize({
                        dropdownParent: 'body',
                        placeholder: 'Haz click para seleccionar ...',
                        onDropdownOpen: function($dropdown) {
                            $dropdown
                                .hide()
                                .velocity('slideDown', {
                                    begin: function() {
                                        $dropdown.css({'margin-top':'0'})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        },
                        onDropdownClose: function($dropdown) {
                            $dropdown
                                .show()
                                .velocity('slideUp', {
                                    complete: function() {
                                        $dropdown.css({'margin-top':''})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        }
                    });
                });
                // create icheck
                data.form
                    .find('input[type="checkbox"],input[type="radio"]')
                    .each(function() {
                        var $this = $(this);
                        $this.iCheck({
                            checkboxClass: 'icheckbox_md',
                            radioClass: 'iradio_md',
                            increaseArea: '20%'
                        })
                        .on('ifChecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Active');
                        })
                        .on('ifUnchecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Passive');
                        })
                    });
                // reinitialize inputs
                data.form.find('.jtable-input').children('input[type="text"],input[type="password"],textarea').not('.md-input').each(function() {
                    $(this).addClass('md-input');
                    altair_forms.textarea_autosize();
                });
                altair_md.inputs();
            },
            actions: {
                listAction: '/admin/tipoevento/list',
                createAction: '/admin/tipoevento/create',
                updateAction: '/admin/tipoevento/update',
                deleteAction: '/admin/tipoevento/delete'
            },
            fields: {
                id: { key: true, create: false, edit: false, list: true, title: "ID" },
                nombre: { title: 'Nombre', create: true, edit: true },
                matrix: { 
                    title: 'Matriz', 
                    create: true, 
                    edit: true, 
                    type: 'textarea',
                    display: function (data) {
                        if (!data.record.matrix) return '';
                        const maxLength = 50;
                        let text = data.record.matrix;
                        if (text.length > maxLength) {
                            text = text.substring(0, maxLength) + '...';
                        }
                        return `<span title="${data.record.matrix.replace(/"/g, '&quot;')}">${text}</span>`;
                    }
                }
            }
        });

        $('#Search_TipoEvento').click(function (e) {
            e.preventDefault();
            $('#tipo_eventos_crud').jtable('load', {
                nombre: $('#tipo_evento_nombre').val()
            });
        });

        $('#Search_TipoEvento').click();
    }

    if($('#reservas_crud').length) {
        $('#reservas_crud').jtable({
            messages: spanishMessages,
            title: '',
            paging: true,
            sorting: true,
            pageSize: 10,
            addRecordButton: $('#reservaAdd'),
            deleteConfirmation: function (data) {
                data.deleteConfirmMessage = '¿Estás seguro que deseas eliminar a "' + data.record.nombre + ' ' + data.record.apellido + '"?';
            },
            actions: {
                listAction: '/admin/reserva/list',
                createAction: '/admin/reserva/create',
                updateAction: '/admin/reserva/update',
                deleteAction: '/admin/reserva/delete'
            },
            formCreated: function(event, data) {
                // replace click event on some clickable elements
                // to make icheck label works
                data.form.find('.jtable-option-text-clickable').each(function() {
                    var $thisTarget = $(this).prev().attr('id');
                    $(this)
                        .attr('data-click-target',$thisTarget)
                        .off('click')
                        .on('click',function(e) {
                            e.preventDefault();
                            $('#'+$(this).attr('data-click-target')).iCheck('toggle');
                        })
                });
                // create selectize
                data.form.find('select').each(function() {
                    var $this = $(this);
                    $this.after('<div class="selectize_fix"></div>')
                    .selectize({
                        dropdownParent: 'body',
                        placeholder: 'Haz click para seleccionar ...',
                        onDropdownOpen: function($dropdown) {
                            $dropdown
                                .hide()
                                .velocity('slideDown', {
                                    begin: function() {
                                        $dropdown.css({'margin-top':'0'})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        },
                        onDropdownClose: function($dropdown) {
                            $dropdown
                                .show()
                                .velocity('slideUp', {
                                    complete: function() {
                                        $dropdown.css({'margin-top':''})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        }
                    });
                });
                // create icheck
                data.form
                    .find('input[type="checkbox"],input[type="radio"]')
                    .each(function() {
                        var $this = $(this);
                        $this.iCheck({
                            checkboxClass: 'icheckbox_md',
                            radioClass: 'iradio_md',
                            increaseArea: '20%'
                        })
                        .on('ifChecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Active');
                        })
                        .on('ifUnchecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Passive');
                        })
                    });
                
                var $fecha = data.form.find('input[name="fecha_nacimiento"]');
                $fecha.attr('placeholder', 'AAAA-MM-DD');
                    
                // reinitialize inputs
                data.form.find('.jtable-input').children('input[type="text"],input[type="password"],textarea').not('.md-input').each(function() {
                    $(this).addClass('md-input');
                    altair_forms.textarea_autosize();
                });
                altair_md.inputs();
            },
            fields: {
                id: { key: true, create: false, edit: false, list: true, title: "ID" },
                nombre: { title: 'Nombre', create: true, edit: false },
                apellido: { title: 'Apellido', create: true, edit: false },
                correo: { title: 'Correo', create: true, edit: false },
                fecha_nacimiento: { title: 'Fecha Nacimiento', create: true, edit: false },
                telefono: { title: 'Teléfono', create: true, edit: false },
                rut: { title: 'RUT', create: true, edit: false },
                numero_personas: { title: '# Personas', create: true, edit: false },
                evento_id: {
                    title: 'Evento',
                    create: true,
                    edit: false,
                    options: EVENTOS
                },
                estado: {
                    title: 'Estado',
                    create: true,
                    edit: true,
                    options: { 'pendiente': 'Pendiente', 'confirmado': 'Confirmado', 'rechazado': 'Rechazado' }
                },
                asientos: {
                    title: 'Asientos',
                    list: true,
                    edit: false,
                    display: function(data) {
                        return data.record.asientos || '';
                    }
                },
                fecha_reserva: {
                    title: 'Fecha Reserva',
                    create: false,
                    edit: false,
                    display: function (data) {
                        if (!data.record.fecha_reserva) return '';
                        const d = new Date(data.record.fecha_reserva);
                        return d.toLocaleString();
                    }
                }
            }
        });

        $('#Search_Reserva').click(function (e) {
            e.preventDefault();
            $('#reservas_crud').jtable('load', {
                nombre: $('#reserva_nombre').val()
            });
        });

        $('#Search_Reserva').click();
    }
    if($('#asientos_crud').length){
        $('#asientos_crud').jtable({
            title: 'Asientos',
            paging: true,
            sorting: true,
            actions: {
                listAction:   '/admin/asiento/listjson/' + EVENTO_ID,
                updateAction: '/admin/asiento/update',
                deleteAction: '/admin/asiento/delete'
            },
            formCreated: function(event, data) {
                // replace click event on some clickable elements
                // to make icheck label works
                data.form.find('.jtable-option-text-clickable').each(function() {
                    var $thisTarget = $(this).prev().attr('id');
                    $(this)
                        .attr('data-click-target',$thisTarget)
                        .off('click')
                        .on('click',function(e) {
                            e.preventDefault();
                            $('#'+$(this).attr('data-click-target')).iCheck('toggle');
                        })
                });
                // create selectize
                data.form.find('select').each(function() {
                    var $this = $(this);
                    $this.after('<div class="selectize_fix"></div>')
                    .selectize({
                        dropdownParent: 'body',
                        placeholder: 'Haz click para seleccionar ...',
                        onDropdownOpen: function($dropdown) {
                            $dropdown
                                .hide()
                                .velocity('slideDown', {
                                    begin: function() {
                                        $dropdown.css({'margin-top':'0'})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        },
                        onDropdownClose: function($dropdown) {
                            $dropdown
                                .show()
                                .velocity('slideUp', {
                                    complete: function() {
                                        $dropdown.css({'margin-top':''})
                                    },
                                    duration: 200,
                                    easing: easing_swiftOut
                                })
                        }
                    });
                });
                // create icheck
                data.form
                    .find('input[type="checkbox"],input[type="radio"]')
                    .each(function() {
                        var $this = $(this);
                        $this.iCheck({
                            checkboxClass: 'icheckbox_md',
                            radioClass: 'iradio_md',
                            increaseArea: '20%'
                        })
                        .on('ifChecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Active');
                        })
                        .on('ifUnchecked', function(event){
                            $this.parent('div.icheckbox_md').next('span').text('Passive');
                        })
                    });
                // reinitialize inputs
                data.form.find('.jtable-input').children('input[type="text"],input[type="password"],textarea').not('.md-input').each(function() {
                    $(this).addClass('md-input');
                    altair_forms.textarea_autosize();
                });
                altair_md.inputs();
            },
            fields: {
                id: {
                    key: true,
                    create: false,
                    edit: false,
                    list: false
                },
                codigo: {
                    title: 'Código',
                    width: '10%',
                    edit: true
                },
                top_pos: {
                    title: 'Top',
                    width: '10%',
                    edit: true
                },
                left_pos: {
                    title: 'Left',
                    width: '10%',
                    edit: true
                },
                discapacitado: {
                    title: '¿Discapacitado?',
                    edit: true,
                    width: '10%',
                    type: 'checkbox',
                    values: { '0': 'No', '1': 'Sí' },
                    defaultValue: '0'
                },
                estado: {
                    title: 'Estado',
                    edit: true,
                    width: '15%',
                    type: 'combobox',
                    options: { 
                        'habilitado': 'Habilitado', 
                        'deshabilitado': 'Deshabilitado' 
                    },
                    defaultValue: 'habilitado'
                }
            }
        });


        $('#asientos_crud').jtable('load');

        $('#Search_Asiento').click(function(e){
            e.preventDefault();
            $('#asientos_crud').jtable('load', {
                codigo: $('#asiento_codigo').val()
            });
        });
    }
    $('.ui-dialog-buttonset')
        .children('button')
        .attr('class','')
        .addClass('md-btn md-btn-flat')
        .off('mouseenter focus');
    $('#AddRecordDialogSaveButton,#EditDialogSaveButton,#DeleteDialogButton').addClass('md-btn-flat-primary');
});
