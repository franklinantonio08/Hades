class Distsolicitud {
    constructor() {
    }

    init(){
        
        if($('#editarregistro').length) {
            this.cambia_motivo();
      }

      if($('#nuevoregistro').length) {
        
        this.validatesolicitud();

        this.toggleViviendaFields();
      }

      if($('#solicitud').length) {
        this.solicitud();
    }

    
      this.acciones();

    }

    acciones(){

        const _this = this;
                
        $( "#searchButton" ).off('click');
        $( "#searchButton" ).click(function() {
            _this.solicitud( $( "#search" ).val() );
        });   

        $('#search').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                _this.solicitud( $( "#search" ).val());
                event.preventDefault();
                return false;
            }
            event.stopPropagation();
        });

        $('#tv_casa').on('change', function() { 
              _this.toggleViviendaFields();
        });

        $('#tv_edificio').on('change', function() { 
              _this.toggleViviendaFields();
        });

        $('#tv_hotel').on('change', function() { 
              _this.toggleViviendaFields();
        });


        $('#recibo_tercero').on('change', function() {
            _this.toggleReciboFields();
        });    
        
        $('#recibo_mio').on('change', function() {
            _this.toggleReciboFields();
        });


        $('#dom_escritura').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_arrendamiento').on('change', function() {
            _this.toggleDocsByDomicilio();
        });
        
        $('#dom_responsabilidad').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_juezpaz').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_reservahotel').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $("#provincia").on("change", function () {
            _this.buscaDistrito();
        });

        $("#distrito").on("change", function () {
            _this.buscaCorregimiento();
        });
        

        $('#guardarForm').off('click').on('click', function() {
          _this.preSubmitCheck();
        });

    
    }

        buscaDistrito() {
            objComun.cargarSelectDependiente({
                origenId: "provincia",
                destinoId: "distrito",
                rutaAjax: "/dist/solicitud/buscaDistrito",
                parametro: "provincia",
                limpiarDestinoId: "corregimiento",
                mensajeCargando: 'Cargando...',
                mensajeDefault: 'Selecciona...',
            });
        }

        buscaCorregimiento(){
            objComun.cargarSelectDependiente({
                origenId: "distrito",
                destinoId: "corregimiento",
                rutaAjax: "/dist/solicitud/buscaCorregimiento",
                parametro: "distrito",
                mensajeCargando: 'Cargando...',
                mensajeDefault: 'Selecciona...',
            });
        }


        toggleViviendaFields() {

            const isCasa     = document.getElementById('tv_casa')?.checked === true;
            const isEdificio = document.getElementById('tv_edificio')?.checked === true;
            const isHotel    = document.getElementById('tv_hotel')?.checked === true;

            // Grupos
            const gCasa   = document.getElementById('grupoCasa');
            const gEdNom  = document.getElementById('grupoEdificio_nombre');
            const gEdPiso = document.getElementById('grupoEdificio_piso');
            const gEdApto = document.getElementById('grupoEdificio_apto');
            const gHotel  = document.getElementById('grupoHotel');

            // Inputs
            const iCasa  = document.getElementById('numero_casa');
            const iEdNom = document.getElementById('nombre_edificio');
            const iPiso  = document.getElementById('piso');
            const iApto  = document.getElementById('apartamento');
            const iHotel = document.getElementById('nombre_hotel');

            // Helper para mostrar/ocultar y required
            const show = (el) => el?.classList.remove('d-none');
            const hide = (el) => el?.classList.add('d-none');
            const req  = (input, on) => {
                if (!input) return;
                if (on) input.setAttribute('required', 'required');
                else    input.removeAttribute('required');
            };

            // 1) Ocultar todo y quitar required
            [gCasa, gEdNom, gEdPiso, gEdApto, gHotel].forEach(hide);
            [iCasa, iEdNom, iPiso, iApto, iHotel].forEach(inp => req(inp, false));

            // 2) Mostrar según selección y marcar required
            if (isCasa) {
                show(gCasa);
                req(iCasa, true);

                // Limpiar lo que NO aplica
                if (iEdNom) iEdNom.value = '';
                if (iPiso)  iPiso.value  = '';
                if (iApto)  iApto.value  = '';
                if (iHotel) iHotel.value = '';
            } else if (isEdificio) {
                show(gEdNom); show(gEdPiso); show(gEdApto);
                req(iEdNom, true); req(iPiso, true); req(iApto, true);

                if (iCasa)  iCasa.value  = '';
                if (iHotel) iHotel.value = '';
            } else if (isHotel) {
                show(gHotel);
                req(iHotel, true);

                if (iCasa)  iCasa.value  = '';
                if (iEdNom) iEdNom.value = '';
                if (iPiso)  iPiso.value  = '';
                if (iApto)  iApto.value  = '';
            }
        }

         toggleReciboFields() {
            const isMio      = document.getElementById('recibo_mio')?.checked === true;
            const isTercero  = document.getElementById('recibo_tercero')?.checked === true;

            // Grupos
            const gNotariado = document.getElementById('recibo_notariado_group');
            const gCedula    = document.getElementById('cedula_titular_group');

            // Inputs
            const iNotariado = document.getElementById('recibo_notariado_archivo');
            const iCedula    = document.getElementById('recibo_cedula_titular');

            // Helpers
            const show = (el) => el?.classList.remove('d-none');
            const hide = (el) => el?.classList.add('d-none');
            const req  = (input, on) => {
                if (!input) return;
                if (on) input.setAttribute('required', 'required');
                else    input.removeAttribute('required');
            };

            // 1) Ocultar y limpiar todo
            [gNotariado, gCedula].forEach(hide);
            [iNotariado, iCedula].forEach(inp => {
                req(inp, false);
                if (inp?.type === 'file') inp.value = ''; // limpiar archivo si no aplica
            });

            // 2) Mostrar según selección
            if (isTercero) {
                show(gNotariado); show(gCedula);
                req(iNotariado, true);
                req(iCedula, true);
            }
        }


        toggleDocsByDomicilio() {

            const _this = this

            const show  = (el) => el?.classList.remove('d-none');
            const hide  = (el) => el?.classList.add('d-none');
            const req   = (input, on) => { if (!input) return; on ? input.setAttribute('required','required') : input.removeAttribute('required'); };
            const dis   = (input, on) => { if (!input) return; input.disabled = !!on; };
            const clearFile = (input) => { if (input && input.type === 'file') input.value = ''; };


            const isHotel = document.getElementById('dom_reservahotel')?.checked === true;

            // Bloque Recibo
            const gRecibo = document.getElementById('grupoRecibo');

            // Inputs dentro del bloque Recibo
            const iReciboFile      = document.getElementById('recibo_archivo');
            const iReciboMio       = document.getElementById('recibo_mio');
            const iReciboTercero   = document.getElementById('recibo_tercero');
            const iNotariado       = document.getElementById('recibo_notariado_archivo');
            const iCedulaTitular   = document.getElementById('recibo_cedula_titular');

            // Subgrupos para poder ocultarlos también si hace falta
            const gNotariado = document.getElementById('recibo_notariado_group');
            const gCedula    = document.getElementById('cedula_titular_group');

            // Nota bajo “Prueba de domicilio”
            const nota = document.getElementById('domicilioNota');

            if (isHotel) {
                // 1) Ocultar bloque
                hide(gRecibo);

                // 2) Quitar required y deshabilitar TODOS los campos del bloque
                [iReciboFile, iNotariado, iCedulaTitular].forEach(inp => {
                req(inp, false);
                clearFile(inp);
                dis(inp, true);
                });
                [iReciboMio, iReciboTercero].forEach(inp => dis(inp, true));

                // 3) Asegurar que los subgrupos dependientes no quedan visibles
                hide(gNotariado); hide(gCedula);

                // 4) Mensaje guía
                if (nota) nota.textContent = 'Si presenta reserva de hotel, no necesita recibo de servicio.';
            } else {
                // 1) Mostrar bloque
                show(gRecibo);

                // 2) Habilitar campos
                [iReciboFile, iNotariado, iCedulaTitular, iReciboMio, iReciboTercero].forEach(inp => dis(inp, false));

                // 3) Required por defecto del recibo principal
                req(iReciboFile, true);

                // 4) Volver a evaluar propio/tercero para pedir lo que corresponda
                _this.toggleReciboFields();

                // 5) Mensaje guía por defecto
                if (nota) nota.textContent = 'Requiere notaría.';
            }
        }

        preSubmitCheck() {

            console.log('Por Aqui vamos');

            const _this = this 

            const $form = $("#nuevoregistro");

            // 1) Ejecutar validación jQuery Validate
            if (!$form.valid()) {
                const objMessagebasicModal = new MessagebasicModal(
                    'Validación',
                    'Por favor corrige los campos marcados antes de continuar.'
                );
                objMessagebasicModal.init();
                return;
            }

            // 2) Construir el HTML de vista previa
            const previewHtml = this._buildPreviewHtml();

            // 3) Usar MessagebasicModal
            const objMessagebasicModal = new MessagebasicModal(
                'Revisión de Datos',
                previewHtml,
                {
                    showConfirm: true,           // mostramos botón "Confirmar"
                    confirmText: 'Confirmar y Enviar',
                    onConfirm: () => {
                        $form.trigger('submit'); // al confirmar se envía el form
                    }
                }
            );

            objMessagebasicModal.init();
        }

        _buildPreviewHtml() {

            console.log('Por Aqui vamos 2');


            const val = (sel) => $(sel).val() || '';
            const textOfSelect = (sel) => {
                const v = $(sel).val();
                return v ? $(sel).find('option:selected').text() : '';
            };
            const radioVal = (name) => $(`input[name="${name}"]:checked`).val() || '';
            const fileNames = (inputSel) => {
                const input = document.querySelector(inputSel);
                if (!input || !input.files || input.files.length === 0) return '—';
                return Array.from(input.files).map(f => f.name).join(', ');
            };

            // Armar listas de preview
            return `
                <h6 class="fw-bold text-primary">Datos Personales</h6>
                <ul>
                    <li><b>Nombre:</b> ${val('#primerNombre')} ${val('#segundoNombre')} ${val('#primerApellido')} ${val('#segundoApellido')}</li>
                    <li><b>Correo:</b> ${val('#correo')}</li>
                    <li><b>Pasaporte:</b> ${val('#pasaporte')}</li>
                </ul>

                <h6 class="fw-bold text-primary">Dirección</h6>
                <ul>
                    <li><b>Provincia:</b> ${textOfSelect('#provincia')}</li>
                    <li><b>Distrito:</b> ${textOfSelect('#distrito')}</li>
                    <li><b>Corregimiento:</b> ${textOfSelect('#corregimiento')}</li>
                    <li><b>Barrio:</b> ${val('#barrio')}</li>
                    <li><b>Calle:</b> ${val('#calle')}</li>
                </ul>

                <h6 class="fw-bold text-primary">Documentos</h6>
                <ul>
                    <li><b>Prueba domicilio:</b> ${fileNames('#domicilio_archivo')}</li>
                    <li><b>Recibo:</b> ${fileNames('#recibo_archivo')}</li>
                    <li><b>Carnet Frente:</b> ${fileNames('#carnet_frente')}</li>
                    <li><b>Carnet Reverso:</b> ${fileNames('#carnet_reverso')}</li>
                </ul>

                <h6 class="fw-bold text-primary">Comentario</h6>
                <div>${val('textarea[name="comentario"]') || '—'}</div>
            `;
        }

        /*BEGIN TABLA USUARIO*/
        solicitud(search){

            //var BASEURL = window.location.origin; 

        console.log(BASEURL);

          const _this = this

              const table = $('#solicitud').DataTable( {
                  "destroy": true,
                  "searching": false,
                  "serverSide": true,
                  "info": true,
                  "lengthMenu": objComun.lengthMenuDataTable,
                  "pageLength": pageLengthDataTable, //Variable global en el layout
                  "language": {
                      "lengthMenu": "Mostrar _MENU_ por página",
                      "zeroRecords": "No se ha encontrado información",
                      "info": "Mostrando _PAGE_ de _PAGES_",
                      "infoEmpty": "",
                  },
                  "ajax": {
                      "url":BASEURL,
                      "type": "POST",
                      "error": this.handleAjaxError, 
                      "data": function ( d ) {
                          var info = $('#solicitud').DataTable().page.info();
                          
                          var orderColumnNumber = d.order[0].column;
                          
                          objComun.orderDirReporte = d.order[0].dir; //Variable global en comun.js
                          objComun.orderColumnReporte = d.columns[orderColumnNumber].data; //Variable global en comun.js
                          objComun.lengthActualReporte = info.length; //Variable global en comun.js
                          objComun.paginaActualReporte = info.page+1; //Variable global en comun.js
                          
                          d.currentPage = info.page + 1;
                          d.searchInput = search;
                          d._token=token;
                      }
                  },
                  "columns": [
                      { "data": "id"},
                      { "data": "departamento" },
                      { "data": "TipoAtencion"},
                      { "data": "codigo" },
                      { "data": "estatus" },
                      { "data": "detalle" , "orderable": false, className: "actions text-right"},
                  ],
                  "initComplete": function (settings, json) {

                  },
                  "infoCallback": function( settings, start, end, max, total, pre ) {

                      _this.desactivarsolicitud();

                      var api = this.api();
                      var pageInfo = api.page.info();
                      return 'Mostrando '+ (pageInfo.page+1) +' de '+ pageInfo.pages;
                  }
              });
  
          }

          handleAjaxError( xhr, textStatus, error ) {
              console.log(error);
          }

          /*END TABLA USUARIO*/

          /*BEGIN VALIDAR NUEVO USUARIO*/
          validatesolicitud(){

            $('#nuevoregistro').submit(function(){
                $(this).find(':submit').attr('disabled','disabled');
              });
            //console.log('por aqui vamos')

              $("#nuevoregistro").validate({
                  submitHandler: function(form) {
                      console.log('submit');
                      form.submit();
                   },
                   invalidHandler:function(form) {
                   },
                   highlight: function(element) {
                    $('#submitForm').removeAttr("disabled");

                       var titleElemnt = $( element ).attr( "id" );
                       $("button[data-id*='"+titleElemnt+"']").addClass( "errorValidate" );
                       $(element).addClass( "errorValidate" );
                    },
                   unhighlight: function(element) {
                       var titleElemnt = $( element ).attr( "id" );
                       $("button[data-id*='"+titleElemnt+"']").addClass( "successValidate" );
                       $(element).addClass( "successValidate" ); 
                    },
                  rules: {
                    nombre: {
                        required: false,
                    },
                   
                  },
                  messages: {
                    regional: {
                        required: "",
                    },
                   
                  }
              });
          }

          validateimportacion(){

            var validator = $("#nuevoimportar").validate({
                ignore: [],
                submitHandler: function(form) {
                    form.submit();
                },
                invalidHandler:function(form) {
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "archivoPlantilla") {
                    error.appendTo('#fileError');
                    } else {
                    error.insertAfter(element);
                    }
                },
                rules: {
                    archivoPlantilla: {
                        required: true,
                        extension: "xls|xlsx",
                        filesize: 1048576
                    }
                },
                messages: {
                    archivoPlantilla: {
                        required: "Este elemento es requerido",
                        extension: "Solo xls o xlsx",
                        filesize: "Tamaño plantilla incorrecto"
                    }
                }
            });

            $.validator.addMethod('filesize', function(value, element, param) {
                // param = size (in bytes) 
                // element = element to validate (<input>)
                // value = value of the element (file name)
                return this.optional(element) || (element.files[0].size <= param) 
            });	
            
        }

                  desactivarsolicitud(){

              const _this = this

              $( ".desactivar" ).off('click');
                $( ".desactivar" ).click(function() {
                    
                    const solicitudId = $( this ).attr( "attr-id" );
                    var opciones = {solicitudId:solicitudId};
                    const message = 'Seguro que desea cambiar de estatus el solicitud?'
                    const objConfirmacionmodal = new Confirmacionmodal(message, opciones, _this.callbackDesactivarsolicitud);
                  objConfirmacionmodal.init();
                  
              });
          }
          

          callbackDesactivarsolicitud(response, opciones){

   
              if(response == true){

                  const _this = this;

                  $.post( BASEURL+'/desactivar', 
                  {
                      solicitudId: opciones.solicitudId,
                      _token:token 
                      }
                  )
                  .done(function( data ) {

                    console.log('por aqui vamos')


                      if(data.response == true){
                          const modalTitle = 'Solicitud';
                          const modalMessage = 'El solicitud ha sido cambiado de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          const objDistsolicituds = new Distsolicitud();							
                          objDistsolicituds.init();	
                          //objDistsolicituds.solicituds($( "#search" ).val());

                      }else{
                          
                          const modalTitle = 'Solicitud';
                          const modalMessage = 'El solicitud no se ha podido cambiar de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          //const objDistsolicituds = new Distsolicitud();							
                          //objDistsolicituds.solicituds($( "#search" ).val());
                      }
                  })
                  .fail(function() {
                      
                      const modalTitle = 'Solicitud';
                      const modalMessage = 'El solicitud no se ha podido cambiar de estatus';
                      const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                      objMessagebasicModal.init();

                      //const objDistsolicituds = new Distsolicitud();							
                      //objDistsolicituds.solicituds($( "#search" ).val());
                      
                  })
                  .always(function() {
                      
                  }, "json");

              }
              
          }

          /*END DESACTIVAR UN DISTRIBUIDOR*/



  }


$(document).ready(function(){
  const objDistsolicitud = new Distsolicitud();
  objDistsolicitud.init();
});