class Adminmigrantes {
    constructor() {

        this.desde = '2000-01-01';
        this.hasta = moment().subtract(0, 'days').format('YYY-MM-DD');

        var date = new Date();
        var year1 = new Date();
        var year1 = year1.getFullYear() + 1;
        var field = year1.toString()+ '-' + (date.getMonth() + 1).toString().padStart(2, 0) + '-' + date.getDate().toString().padStart(2, 0);

        this.hasta = field;
    }

    init(){
        
        if($('#nuevoregistro').length) {
          //this.migrantes();
          //this.cambia_region();
      }

      if($('#nuevoregistro').length) {
          this.validatemigrantes();
      }

      if($('#migrantes').length) {
        this.migrantes();
    }

      if($('#nuevoimportar').length) {
        this.validateimportacion();
            /*INICIO Para el input File*/
            $(':file').on('fileselect', function(event, numFiles, label) {
                  var input = $(this).parents('.input-group').find(':text'),
                      log = numFiles > 1 ? numFiles + ' Archivo Seleccionado' : label;

                  if( input.length ) {
                      input.val(log);
                  } else {
                      if( log ) alert(log);
                  }

              });
              
            $(document).on('change', ':file', function() {
                var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });
            /*FINAL Para el input File*/
    }

   
      this.acciones();

    }

    acciones(){

        const _this = this;
                
        $( "#searchButton" ).off('click');
        $( "#searchButton" ).click(function() {
          _this.migrantes( $( "#search" ).val() );
      });

      $('#search').keypress(function(event){
          var keycode = (event.keyCode ? event.keyCode : event.which);
          if(keycode == '13'){
              _this.migrantes( $( "#search" ).val());
              event.preventDefault();
              return false;
          }
          event.stopPropagation();
      });

      $( ".descargarButton" ).click(function() {
        $( '#loaderBoxImage').modal('show');
        _this.descargarreporte(0);

        console.log('por aqui vamoszxczx');
        //$('.loaderBoxImage').modal('hide');
    });
      


      
      $(document).ready(function() {
        let familiarIndex = 0;
    
        // Mostrar el modal al hacer clic en el botón "Agregar Familiar"
        $("#agregarFamiliar").click(function() {
            $('#agregarFamiliarModal').modal('show');
        });
    
        // Guardar la información del familiar y agregarlo al formulario principal
        $("#guardarFamiliarBtn").click(function() {
            const primerNombre = $("#modalPrimerNombre").val().trim();
            const segundoNombre = $("#modalSegundoNombre").val().trim();
            const primerApellido = $("#modalPrimerApellido").val().trim();
            const segundoApellido = $("#modalSegundoApellido").val().trim();
            const fechaNacimiento = $("#modalFechaNacimiento").val();
            const documento = $("#modalDocumento").val().trim();
            const genero = $("#modalGenero").val();
            const afinidad = $("#modalAfinidad").val();
            const nacionalidad = $("#modalNacionalidad").val();
            const pais = $("#modalPais").val();
    
            if (primerNombre && primerApellido) {
                // Crear nuevo campo con los datos del familiar
                const familiarField = `
                    <div class="input-group mb-3" id="familiar-${familiarIndex}">
                        <input type="text" class="form-control" id="familiarNombre-${familiarIndex}" name="familiarNombre-${familiarIndex}" value="${primerNombre} ${primerApellido}" readonly>
                        <input type="hidden" name="primerNombre-${familiarIndex}" value="${primerNombre}">
                        <input type="hidden" name="segundoNombre-${familiarIndex}" value="${segundoNombre}">
                        <input type="hidden" name="primerApellido-${familiarIndex}" value="${primerApellido}">
                        <input type="hidden" name="segundoApellido-${familiarIndex}" value="${segundoApellido}">
                        <input type="hidden" name="fechaNacimiento-${familiarIndex}" value="${fechaNacimiento}">
                        <input type="hidden" name="documento-${familiarIndex}" value="${documento}">
                        <input type="hidden" name="genero-${familiarIndex}" value="${genero}">
                        <input type="hidden" name="afinidad-${familiarIndex}" value="${afinidad}">
                        <input type="hidden" name="nacionalidad-${familiarIndex}" value="${nacionalidad}">
                        <input type="hidden" name="pais-${familiarIndex}" value="${pais}">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-danger remove-familiar-btn" data-index="${familiarIndex}"><i class="fa fa-trash"></i></button>
                        </span>
                    </div>
                `;
    
                // Agregar el nuevo campo al contenedor
                $("#familiaresList").append(familiarField);
    
                // Incrementar el índice para el próximo familiar
                familiarIndex++;
    
                // Limpiar el formulario del modal
                $("#agregarFamiliarForm")[0].reset();
    
                // Ocultar el modal
                $('#agregarFamiliarModal').modal('hide');
            } else {
                alert("Por favor, complete el Primer Nombre y Primer Apellido.");
            }
        });
    
        // Función para eliminar un familiar usando delegación de eventos
        $("#familiaresList").on("click", ".remove-familiar-btn", function() {
            const index = $(this).data("index");
            $(`#familiar-${index}`).remove();
        });

         // Cerrar modal al hacer clic en los botones de cierre
    $('.modal .close, .modal .btn-secondary').on('click', function() {
        $('#agregarFamiliarModal').modal('hide');
    });
    });
    

    $('#reportrange span').html(this.desde + ' - ' + this.hasta);
    $('#reportrange').daterangepicker({
              format: 'DD-MM-YYYY',
              startDate: '01/01/2016',
              endDate: moment(),
              minDate: '01/01/2016',
              maxDate: moment().add(3, 'year').endOf('month'),
              showDropdowns: true,
              showWeekNumbers: true,
              timePicker: false,
              timePickerIncrement: 1,
              timePicker12Hour: true,
              ranges: {
                  'Hoy': [moment(), moment()],
                  'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                  'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                  'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                  'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                  'Todos': ['01/01/2016', moment()]
              },
              opens: 'right',
              drops: 'down',
              buttonClasses: ['btn', 'btn-sm'],
              applyClass: 'btn-success',
              cancelClass: 'btn-default',
              separator: ' to ',
              locale: {
                  applyLabel: 'Enviar',
                  cancelLabel: 'Cancelar',
                  fromLabel: 'Desde',
                  toLabel: 'Hasta',
                  customRangeLabel: 'Personalizar',
                  daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                  monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Augosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                  firstDay: 1
              }
          }, function (start, end, label) {
             // console.log(start.format('DD-MM-YYYY'), end.format('DD-MM-YYYY'));
              
              _this.desde = start.format('YYYY-MM-DD');
              _this.hasta = end.format('YYYY-MM-DD');

              _this.migrantes();
              
              $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
              $('#reporte_fecha_titulo span').html(' Desde ' + start.format('YYYY-MM-DD') + ' - Hasta ' + end.format('YYYY-MM-DD'));
              
              
          });

    
    
    
    }

        /*BEGIN TABLA USUARIO*/
        migrantes(search){

            //var BASEURL = window.location.origin; 

            //$('#loaderBoxImage').modal('show');

        console.log(BASEURL);

          const _this = this

              const table = $('#migrantes').DataTable( {
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
                          var info = $('#migrantes').DataTable().page.info();
                          
                          var orderColumnNumber = d.order[0].column;
                          
                          objComun.orderDirReporte = d.order[0].dir; //Variable global en comun.js
                          objComun.orderColumnReporte = d.columns[orderColumnNumber].data; //Variable global en comun.js
                          objComun.lengthActualReporte = info.length; //Variable global en comun.js
                          objComun.paginaActualReporte = info.page+1; //Variable global en comun.js
                          
                          d.currentPage = info.page + 1;
                          d.searchInput = search;
                          d._token=token;
                          d.desde = _this.desde;
                          d.hasta = _this.hasta;
                      }
                  },
                  "columns": [
                      { "data": "id"},
                      { "data": "descripcion" },
                      { "data": "codigo" },
                      //{ "data": "genero" },
                      //{ "data": "tipo" },
                      //{ "data": "pais" },
                      //{ "data": "region" },
                      //{ "data": "puesto_control" },
                      { "data": "estatus" },
                      { "data": "detalle" , "orderable": false, className: "actions text-right"},
                  ],
                  "initComplete": function (settings, json) {

                  },
                  "infoCallback": function( settings, start, end, max, total, pre ) {

                    //$('#loaderBoxImage').modal('hide');

                      //_this.desactivarmigrantes();

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
          validatemigrantes(){

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


        //   cambia_region() {

        //     console.log(BASEURL);
    
        //     const _this = this;
        // $('#nacionalidad').on('change', function() { 
        //     var provincia = $( "#provincia" ).val();  
        //     var obj_div = document.getElementById('DivResultado_distrito'); 
        //     var selec1 = '<div class="form-floating mb-3">';
        //     selec1 += '<select id="distrito" name="distrito"  class="form-select" > ';
        //     selec1 += '<option value="" selected disabled>Selecciona un departamento</option>';
        //     var selec2 = '</select> </div>';
        //     var valor = selec1;
                 
        //     if(provincia != ''){
    
        //         console.log('por aqui entra al post');
    
        //         $.post( BASEURL+'/buscadistrito', 
        //             {
        //             _token:token, provincia:provincia
        //             }
        //             ).done(function( data ) {
        //                 if(data.response == true){ 
        //                 $.each(data.data, function (id, value) {
        //                     $.each(value, function (id, valur) {
        //                         const distri = valur;
        //                         valor +=distri;
        //                     });
        //                 });
                        
        //                 valor +=selec2;
        //                 obj_div.innerHTML =valor;
        //                 //_this.cambia_corregimiento();
        //             }
        //             })
        //             .fail(function() {
        //             })
        //             .always(function() {
        //             }, "json");
        //         //console.log(provincia, distrito, corregimiento);
        //     }else{
    
        //         console.log('no entra al post');
    
        //         const distri = '<option value="" seleted >S/A</option>';            
        //         valor +=distri;            
        //         valor +=selec2;
        //         obj_div.innerHTML =valor;
        //        // _this.cambia_corregimiento();
        //     }
    
        //   });
    
        
    
        //         $('#DivResultado_distrito').on('change', function() { 
                    
        //               //  _this.cambia_corregimiento();
                    
        //         });
        // }
          /*END VALIDAR NUEVO USUARIO*/


          /* BEGIN  Validacion de importacion*/

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

          /* End Validar Importacion */


          /*BEGIN DESACTIVAR UN USUARIO*/
          desactivarmigrantes(){

              const _this = this

              $( ".desactivar" ).off('click');
                $( ".desactivar" ).click(function() {
                    
                    const migrantesId = $( this ).attr( "attr-id" );
                    var opciones = {migrantesId:migrantesId};
                    const message = 'Seguro que desea cambiar de estatus el migrantes?'
                    const objConfirmacionmodal = new Confirmacionmodal(message, opciones, _this.callbackDesactivarmigrantes);
                  objConfirmacionmodal.init();
                  
              });
          }
          

          callbackDesactivarmigrantes(response, opciones){

   
              if(response == true){

                  const _this = this;

                  $.post( BASEURL+'/desactivar', 
                  {
                      migrantesId: opciones.migrantesId,
                      _token:token 
                      }
                  )
                  .done(function( data ) {

                    console.log('por aqui vamos')


                      if(data.response == true){
                          const modalTitle = 'migrantes';
                          const modalMessage = 'El migrantes ha sido cambiado de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          const objAdminmigrantes = new Adminmigrantes();							
                          objAdminmigrantes.init();	
                          //objAdminmigrantes.migrantes($( "#search" ).val());

                      }else{
                          
                          const modalTitle = 'migrantes';
                          const modalMessage = 'El migrantes no se ha podido cambiar de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          //const objAdminmigrantes = new Adminmigrantes();							
                          //objAdminmigrantes.migrantes($( "#search" ).val());
                      }
                  })
                  .fail(function() {
                      
                      const modalTitle = 'migrantes';
                      const modalMessage = 'El migrantes no se ha podido cambiar de estatus';
                      const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                      objMessagebasicModal.init();

                      //const objAdminmigrantes = new Adminmigrantes();							
                      //objAdminmigrantes.migrantes($( "#search" ).val());
                      
                  })
                  .always(function() {
                      
                  }, "json");

              }
              
          }


        //   descargarreporte(key){

        //     const _this = this;
        //     var chunk = 1000;
        //     // var cliente = $( "#cliente" ).val();
        //     // var involucrarA = $( "#involucrarA" ).val();
        //     // var estatus = $( "#departamento" ).val();
        //     // var provincia = $( "#provinciaid" ).val();
        //     // var distrito = $( "#distritoid" ).val();
        //     // var corregimiento = $( "#corregimientoid" ).val();
        //     // var direccion = $( "#direccion" ).val();
        //     //var distribuidor = $( "#distribuidor" ).val();
        //     //var categoria = $( "#categoria" ).val();
        //     var search = $( "#search" ).val();
        //     var tipo = 1;

        //     //console.log(involucrarA);
        //     //console.log(distribuidor);
        //     //console.log(categoria);

        //     $.post( BASEURL+'/migranteinformacion', 
        //     {
        //         chunk:chunk,
        //         key:key,
        //         // involucrarA:involucrarA,
        //         // cliente:cliente,                    
        //         // estatus:estatus,
        //         // provincia:provincia,
        //         // distrito:distrito,
        //         // corregimiento:corregimiento,
        //         // direccion:direccion,
        //         searchInput:search,
        //         tipo:tipo,
        //         desde:_this.desde,
        //         hasta:_this.hasta,
        //         _token:token 
        //     }
        //     )
        //     .done(function( data ) {


        //         //console.log('por squi')
        //         //$('#loaderBoxImage').modal('hide');

        //         console.log('por aqui vamos')


        //         if(data.response == false){
        //             console.log('por aqui vamos1')
        //             //$('#loaderBoxImage').modal('hide');
        //             var downloadModalContent = '<a href="'+BASEURL+'/migrantereporte/'+tipo+'" target="_blank" class="btn btn-default bg-white m-b-5"> <i class="fa fa-file-excel-o m-r-5"></i> <span>Excel</span> </a>';
        //             // var downloadModalContent = '<a href="'+BASEURL+'/dist/impresora/impresorareporte/'+tipo+'" target="_blank" class="btn btn-default bg-white m-b-5"> <i class="fa fa-file-excel-o m-r-5"></i> <span>Excel</span> </a>';
                    
        //             $('#downloadModalContent').empty().append(downloadModalContent);
        //             $('#downloadModal').modal('show');
        //         }else{
        //             console.log('por aqui vamos2')
        //             key = key + chunk;
        //             _this.descargarreporte(key);
        //         }
        //     })
        //     .fail(function() {
        //     })
        //     .always(function() {
        //     }, "json");

        // }

        descargarreporte(key) {
            const _this = this;
            var chunk = 1000;
            var search = $("#search").val();
            var tipo = 1;
        
            $.post(BASEURL + '/migranteinformacion', {
                chunk: chunk,
                key: key,
                searchInput: search,
                tipo: tipo,
                desde: _this.desde,
                hasta: _this.hasta,
                _token: token 
            })
            .done(function(data) {
                console.log('Key:', key, 'Data Response:', data.response);
                if (data.response == false) {
                    var downloadModalContent = '<a href="' + BASEURL + '/migrantereporte/' + tipo + '" target="_blank" class="btn btn-default bg-white m-b-5"> <i class="fa fa-file-excel-o m-r-5"></i> <span>Excel</span> </a>';
                    $('#downloadModalContent').empty().append(downloadModalContent);
                    $('#downloadModal').modal('show');
                } else {
                    key += chunk;
                    setTimeout(function() {
                        _this.descargarreporte(key);
                    }, 100);  // Pequeña pausa para evitar recursión directa
                }
            })
            .fail(function() {
                console.error('Error en la solicitud');
            })
            .always(function() {
                console.log('Finalizó la solicitud para key:', key);
            }, "json");
        }
        

        

          /*END DESACTIVAR UN DISTRIBUIDOR*/



  }


$(document).ready(function(){

  const objAdminmigrantes = new Adminmigrantes();
  objAdminmigrantes.init();

  let valfon = "";


   var maelissa =  "sebastian"; 

   console.log(maelissa);


});