$(document).ready(function() {
    
    // ==========================================
    // 1. FILTRO DINÁMICO PARA EL CATÁLOGO (Y FUTURA BD)
    // ==========================================
    // Si estás en la página del catálogo, agregamos dinámicamente una barra de filtrado rápido
    if ($('.rooms-grid').length > 0) {
        
        // Inyectamos visualmente los botones de filtro estilo Frutiger Aero sobre la grilla
        const filterHTML = `
            <div class="catalog-filters" style="text-align:center; margin-bottom: 30px;">
                <button class="btn-filter active" data-filter="all">Todas</button>
                <button class="btn-filter" data-filter="Suite Premium">Suites</button>
                <button class="btn-filter" data-filter="Habitación Estándar">Estándar</button>
            </div>
        `;
        $('.rooms-grid').before(filterHTML);

        // Evento Click en los botones de filtro
        $('.btn-filter').on('click', function() {
            // Cambiar clase activa del botón
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');

            const selectedFilter = $(this).data-filter;

            // Animación Fade al filtrar (Súper característico de los años 2000)
            $('.room-card').each(function() {
                // Buscamos el texto de la categoría dentro de la tarjeta
                const itemCategory = $(this).find('.room-features').prev().prev().text(); 
                // Nota técnica: En producción con BD, es mejor poner la categoría en un atributo data-category="${categoria}"
                
                if (selectedFilter === "all" || itemCategory.includes(selectedFilter)) {
                    $(this).fadeIn(400);
                } else {
                    $(this).fadeOut(400);
                }
            });
        });
    }

    // ==========================================
    // 2. INTERACTIVIDAD DEL MENÚ (INDEX & NAVEGACIÓN)
    // ==========================================
    // Efecto de iluminación brillante (Glow) al pasar el mouse por las opciones del menú
    $('.nav-menu a').on('mouseenter', function() {
        $(this).css({
            'text-shadow': '0 0 10px rgba(0, 210, 255, 0.8)',
            'transform': 'scale(1.05)'
        });
    }).on('mouseleave', function() {
        $(this).css({
            'text-shadow': 'none',
            'transform': 'scale(1)'
        });
    });

    // Cambiar la clase activa del menú dinámicamente según la sección visible (ScrollSpy)
    $(window).on('scroll', function() {
        const scrollPos = $(window).scrollTop();
        
        $('section').each(function() {
            const top = $(this).offset().top - 100;
            const bottom = top + $(this).outerHeight();
            
            if (scrollPos >= top && scrollPos <= bottom) {
                const id = $(this).attr('id');
                $('.nav-menu a').removeClass('active');
                $(`.nav-menu a[href="#${id}"]`).addClass('active');
            }
        });
    });

    // ==========================================
    // 3. VALIDACIÓN TOTAL DEL LADO DEL CLIENTE (jQuery)
    // ==========================================

    // Función auxiliar para validar un campo individual
    function validarCampo(campo) {
        let esValido = true;
        const valor = campo.val().trim();

        // Validar si es requerido y está vacío
        if (campo.prop('required') && valor === "") {
            esValido = false;
        } 
        // Validar formato de correo si es un campo de email
        else if (campo.attr('type') === 'email' && valor !== "") {
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(valor)) esValido = false;
        }
        // Validar que los precios no sean negativos
        else if (campo.attr('type') === 'number' && valor !== "") {
            if (parseFloat(valor) < 0) esValido = false;
        }

        // Aplicar estilos visuales según el resultado
        if (!esValido) {
            campo.css({
                'border-color': '#ff3b30',
                'box-shadow': '0 0 8px rgba(255, 59, 48, 0.4)'
            });
        } else {
            campo.css({
                'border-color': '#3aff78',
                'box-shadow': '0 0 8px rgba(58, 255, 120, 0.4)'
            });
        }

        return esValido;
    }

    // 1. Validar en tiempo real mientras el usuario escribe o sale del campo
    $('.contact-form input, .contact-form textarea, .admin-form input, .admin-form textarea, .admin-form select').on('blur input', function() {
        validarCampo($(this));
    });

    // 2. Validar al intentar enviar el Formulario de Contacto
    $('.contact-form').on('submit', function(event) {
        let formularioValido = true;

        // Revisamos cada campo del formulario de contacto
        $(this).find('input, textarea').each(function() {
            if (!validarCampo($(this))) {
                formularioValido = false;
            }
        });

        // Si hay algún error, detenemos el envío
        if (!formularioValido) {
            event.preventDefault(); // Evita que la página se recargue o envíe datos
            alert("Por favor, verifica que todos los campos del formulario de contacto estén correctos.");
        } else {
            alert("¡Mensaje enviado con éxito! (Simulado)");
            // Aquí es donde en el futuro dejarás que actúe tu archivo PHP/Backend
        }
    });

    // ==========================================
    // 3. VALIDACIÓN MANUAL DEL FORMULARIO ADMIN
    // ==========================================
    $('.admin-form').on('submit', function(event) {
        let formularioValido = true;

        // Validar que los campos obligatorios tengan texto
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if ($(this).val().trim() === '') {
                $(this).css('border-color', '#ff4d4d'); 
                formularioValido = false;
            } else {
                $(this).css('border-color', '');
            }
        });

        // Si falta algo, detenemos el envío. Si todo está bien, dejamos que corra nativamente hacia guardar.php
        if (!formularioValido) {
            event.preventDefault();
            alert("Por favor, completa todos los campos marcados en rojo.");
        }
    });

// ===================================================
    // VALIDACIÓN POR AJAX EN TIEMPO REAL (PASO 3) - OPTIMIZADO
    // ===================================================
    let correoRepetido = false; // Variable global de control para frenar el submit

    $('#user-email').on('blur', function() {
        const emailInput = $(this);
        const correoValue = emailInput.val().trim();
        const userId = $('#user-id').val(); 

        if (correoValue !== '' && (!userId || userId === '')) {
            $.ajax({
                url: 'verificar_usuario.php',
                method: 'POST',
                data: { correo: correoValue },
                dataType: 'json',
                success: function(response) {
                    $('#ajax-alert').remove();

                    if (response.repetido) {
                        correoRepetido = true;
                        
                        // Forzamos los estilos visuales de error cancelando el borde verde
                        emailInput.css({
                            'border-color': '#ff4d4d',
                            'box-shadow': '0 0 8px rgba(255, 77, 77, 0.4)'
                        });

                        emailInput.after(`
                            <small id="ajax-alert" style="color: #cc0000; display: block; margin-top: 4px; font-weight: 600;">
                                ❌ Este correo electrónico ya se encuentra registrado.
                            </small>
                        `);
                    } else {
                        correoRepetido = false;
                        
                        emailInput.css({
                            'border-color': '#3aff78',
                            'box-shadow': '0 0 8px rgba(58, 255, 120, 0.4)'
                        });

                        emailInput.after(`
                            <small id="ajax-alert" style="color: #008822; display: block; margin-top: 4px; font-weight: 600;">
                                ✨ ¡Nombre de usuario/correo disponible!
                            </small>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la comunicación asíncrona del servidor:', error);
                }
            });
        }
    });

    // Interceptamos el submit del formulario de usuarios para garantizar que no pase si está repetido
    $('#seccion-usuarios form').on('submit', function(event) {
        if (correoRepetido) {
            event.preventDefault(); // Bloquea por completo el envío a guardar_usuario.php
            alert("No puedes registrar este usuario. El correo electrónico ya existe.");
            $('#user-email').focus();
        }
    });

    // Limpiar alertas de AJAX al presionar el botón de limpiar o restablecer el formulario
    $('#btn-cancelar-usuario').on('click', function() {
        correoRepetido = false;
        $('#ajax-alert').remove();
        $('#user-email').css({ 'border-color': '', 'box-shadow': '' });
    });

    // EFECTO EXTRA DEL PANEL ADMIN (Simulación del flujo de Edición)
    // Al dar clic en "Editar", sube el scroll y carga los datos de la tabla en el formulario
    // EFECTO PANEL ADMIN: Flujo de Edición leyendo los atributos data de la BD
$('.btn-action.edit').on('click', function() {
    const btn = $(this);
    
    // Obtenemos los datos puros guardados en los data-attributes
    const id = btn.data('id');
    const nombre = btn.data('nombre');
    const categoria = btn.data('categoria');
    const precio = btn.data('precio');
    const estado = btn.data('estado');
    const descripcion = btn.data('descripcion');

    // Rellenamos los campos del formulario de manera exacta
    $('#item-id').val(id);
    $('#item-name').val(nombre);
    $('#item-type').val(categoria);
    $('#item-price').val(precio);
    $('#item-status').val(estado);
    $('#item-description').val(descripcion);
    
    // Modificamos el título visual para indicar el modo Edición
    $('#form-title').text(`Modificando Habitación #${id}`).css('color', '#00bcff');
    
    // Cambiamos el comportamiento estético del botón de limpiar formulario para que permita "Cancelar" la edición
    $('#btn-cancelar').text('Cancelar Edición').addClass('btn-alert');

    // Scroll suave hacia la sección del formulario
    $('html, body').animate({
        scrollTop: $(".admin-form-section").offset().top - 80
    }, 500);
});

// Resetear el formulario y devolver el título a su estado original
$('#btn-cancelar').on('click', function() {
    $('#item-id').val('');
    $('#form-title').text('Registrar Nueva Habitación o Servicio').css('color', '');
    $(this).text('Limpiar Formulario').removeClass('btn-alert');
});
});

// ==========================================
// EDICIÓN DINÁMICA DE USUARIOS
// ==========================================
$('.btn-action.edit-user').on('click', function() {
    const btn = $(this);
    
    const id = btn.data('id');
    const nombre = btn.data('nombre');
    const correo = btn.data('correo');
    const rol = btn.data('rol');

    // Rellenamos el formulario de usuarios
    $('#user-id').val(id);
    $('#user-name').val(nombre);
    $('#user-email').val(correo);
    $('#user-role').val(rol);
    $('#user-password').attr('placeholder', 'Escribe una nueva contraseña si deseas cambiarla');
    
    // Cambiamos el título visual
    $('#user-form-title').text(`Modificando Usuario #${id}`).css('color', '#00bcff');
    $('#btn-cancelar-usuario').text('Cancelar Edición').addClass('btn-alert');

    // Scroll suave al formulario de usuarios
    $('html, body').animate({
        scrollTop: $("#seccion-usuarios").offset().top - 80
    }, 500);
});

// Cancelar/Limpiar formulario de usuarios
$('#btn-cancelar-usuario').on('click', function() {
    $('#user-id').val('');
    $('#user-name').val('');
    $('#user-email').val('');
    $('#user-password').val('').attr('placeholder', 'Escribe una contraseña segura');
    $('#user-role').val('Cliente');
    
    $('#user-form-title').text('Registrar Nuevo Usuario').css('color', '');
    $(this).text('Limpiar').removeClass('btn-alert');
});