class Distcitas {

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
            //this.citas();
            //this.servicios();
            this.servicios_consulares();
        }

        if($('#nuevoregistro').length) {
            this.validatecitas();
        }
      

        if($('#citas').length) {
            this.citas();
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

    

        $('#pais').on('change', function() {         
            var pais = $("#pais").val();
            var obj_div = document.getElementById('DivResultado_consulado');
            var selec1 = '<select  class="form-select" id="consulado" name="consulado" >';
            selec1 += '<option value="" selected disabled>Selecciona...</option>';
            var selec2 = '</select>';
            var valor = selec1;
            if (pais != '') {        
                $.post(BASEURL + '/buscaConsulados',
                    {
                        _token: token,
                        pais: pais
                    }
                ).done(function( data ) {
                    if (data.response == true) {
                        $.each(data.data, function(id, value) {
                            $.each(value, function(id, valur) {
                                const distri = valur;
                                valor += distri;
                            });
                        });
                        valor += selec2;
                        obj_div.innerHTML = valor;
                    }
                }).fail(function() {
                    console.log("Error while fetching data.");
                }).always(function() {
                    console.log("Request finished.");
                }, "json");        
            } else {
                console.log('no entra al post');
                const distri = '<option value="" selected>S/A</option>';
                valor += distri;
                valor += selec2;
                obj_div.innerHTML = valor;
            }
        });


    $('#DivResultado_consulado').on('change', function() {        
        var consulado = $("#consulado").val();
        var obj_div = document.getElementById('DivResultado_serviciosconsulares');    
        var selec1 = '<select  class="form-select" id="servicios" name="servicios" >';
        selec1 += '<option value="" selected disabled>Selecciona...</option>';
        var selec2 = '</select>';
        var valor = selec1;
        if (consulado != '') {
            $.post(BASEURL + '/buscaServicios',
                {
                    _token: token,
                    consulado: consulado
                }
            ).done(function( data ) {
                if (data.response == true) {
                    $.each(data.data, function(id, value) {
                        $.each(value, function(id, valur) {
                            const distri = valur;
                            valor += distri;
                        });
                    });
                    valor += selec2;
                    obj_div.innerHTML = valor;
                }
            }).fail(function() {
                console.log("Error while fetching data.");
            }).always(function() {
                console.log("Request finished.");
            }, "json");    
        } else {
            console.log('no entra al post');
            const distri = '<option value="" selected>S/A</option>';
            valor += distri;
            valor += selec2;
            obj_div.innerHTML = valor;
        }
    });
   
    this.acciones();

    }

    acciones(){

        const _this = this;
                
        $( "#searchButton" ).off('click');
        $( "#searchButton" ).click(function() {
            _this.citas( $( "#search" ).val() );
        });

        $('#search').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                _this.citas( $( "#search" ).val());
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
      


        $('#consulado').on('change', function() { 
            alert('por aqui');

            //_this.servicios_consulares();
        });
  
        $('.modal .close, .modal .btn-secondary').on('click', function() {
            $('#citasModal').modal('hide');
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

                _this.citas();
                
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                $('#reporte_fecha_titulo span').html(' Desde ' + start.format('YYYY-MM-DD') + ' - Hasta ' + end.format('YYYY-MM-DD'));
                
                
        });

    
    
    
    }

        /*BEGIN TABLA USUARIO*/
        citas(search){
            //var BASEURL = window.location.origin; 
            //$('#loaderBoxImage').modal('show');
            //console.log(BASEURL);
            const _this = this

            const table = $('#citas').DataTable( {
                  "destroy": true,
                  "searching": false,
                  "serverSide": true,
                  "autoWidth": false,
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
                          var info = $('#citas').DataTable().page.info();
                          
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
                      { "data": "pais" },
                      { "data": "consulado" },
                      { "data": "servicios" },
                      { "data": "usuario" },
                      { "data": "codigo" },
                      { "data": "fecha_sol" },
                      { "data": "fecha" },
                   
                    //   { "data": "genero" },
                    //   { "data": "nacionalidad" },
                    //   { "data": "funcionario" },
                      { "data": "estatus" },
                      { "data": "detalle" , "orderable": false, className: "actions text-end"},
                  ],
                  "initComplete": function (settings, json) {

                  },
                  "infoCallback": function( settings, start, end, max, total, pre ) {

                    //$('#loaderBoxImage').modal('hide');

                      _this.desactivarcitas();
                      _this.mostrarcitas();
                      _this.imprimircitas();

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
          validatecitas(){

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
                    primerNombre: {
                        required: true,
                    },
                    primerApellido: {
                        required: true,
                    },
                    
                   
                  },
                  messages: {
                    primerNombre: {
                        required: "*  Favor Ingresar el Primer Nombre",
                    },
                    primerApellido: {
                        required: "*  Favor Ingresar el Primer Apellido",
                    },
                    
                  }
              });
          }


          servicios_consulares() {

            //console.log('entro a la funcion');
            //console.log(BASEURL);
        
            const _this = this;
            var consulado = $("#consulado").val();
            var obj_div = document.getElementById('DivResultado_serviciosconsulares');
        
            // Define the initial select HTML structure
            var selec1 = '<select class="form-select" id="servicios" name="servicios" style="width: 100%;">';
            selec1 += '<option value="" selected disabled>Selecciona...</option>';
            var selec2 = '</select>';
            var valor = selec1;
        
            // Check if the consulado value is not empty
            if (consulado != '') {        
                
                //console.log('por aqui entra al post');
        
                // Perform AJAX request to get the consular services
                $.post(BASEURL + '/buscaServicios',
                    {
                        _token: token,
                        consulado: consulado
                    }
                ).done(function(data) {
                    if (data.response === true) {
                        // Loop through the data and append options
                        $.each(data.data, function(id, value) {
                            valor += value.detalle; // Append the generated options
                        });
        
                        // Append the closing select tag and replace the content inside 'DivResultado_serviciosconsulares'
                        valor += selec2;
                        obj_div.innerHTML = valor;
                    }
                }).fail(function() {
                    console.log("Error while fetching data.");
                }).always(function() {
                    console.log("Request finished.");
                });
        
            } else {
                //console.log('no entra al post');
                valor += '<option value="" selected>S/A</option>';
                valor += selec2;
                obj_div.innerHTML = valor;
            }
        }
        
        
        


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


          mostrarcitas(){
            //console.log(BASEURL+' por aqui vamos citas');
            //$( ".mostrar" ).off('click');
            $( ".mostrar" ).click(function() {               

                const citasId = $(this).attr("attr-id"); // Obtén el ID del citas

                $.ajax({
                    url: BASEURL+`/mostrar/${citasId}`, // Ajusta la URL según tu ruta
                    method: "GET",
                    success: function (data) {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        const modalContent = `
                            <table class="table">
                                <tr><td><strong>Pais:</strong></td><td>${data.pais}</td></tr>    
                                <tr><td><strong>Consulados:</strong></td><td>${data.consulado}</td></tr>
                                <tr><td><strong>Servicio Consular:</strong></td><td>${data.servicios}</td></tr>
                                <tr><td><strong>Usuario:</strong></td><td>${data.usuario_nombre_completo}</td></tr>
                                <tr><td><strong>Codigo:</strong></td><td>${data.codigo}</td></tr>
                                <tr><td><strong>Fecha:</strong></td><td>${data.fecha}</td></tr>
                                <tr><td><strong>Estatus:</strong></td><td>${data.estatus}</td></tr>
                              </table> 
                        `;                   
        
                        $("#modal-content").html(modalContent); // Cargar el contenido
                        $("#citasModal").modal("show"); // Mostrar el modal
                    },
                    error: function (xhr) {
                        alert("Error al cargar los datos. Intente nuevamente.");
                        console.error(xhr);
                    },
                });

            });


          }

          imprimircitas(){

            const modal = document.getElementById("modalPDF");
            const pdfViewer = document.getElementById("pdfViewer");
            const downloadBtn = document.getElementById("downloadPdf");
            const printBtn = document.getElementById("printPdf");
            const closeModal = document.getElementById("closeModalImprimir");
            const closeButton = modal.querySelector(".close"); // Botón con la clase "close"

            // Evento para abrir el visor PDF
            document.querySelectorAll(".impresion").forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();

                    // Obtener el ID del PDF del atributo personalizado
                    const pdfId = this.getAttribute("attr-id");

                    // Generar la URL del PDF
                    const pdfUrl = `${BASEURL}/mostrar-pdf/${pdfId}`;

                    // Cargar el PDF en el iframe
                    pdfViewer.src = pdfUrl;

                    // Mostrar el modal
                    modal.style.display = "block";
                    modal.classList.add("show");
                });
            });

            // Evento para cerrar el modal con el botón de "Cerrar"
            closeModal.addEventListener("click", closeModalHandler);
            closeButton.addEventListener("click", closeModalHandler); // Asignar evento al botón de clase "close"

            // Botón para descargar el PDF
            downloadBtn.addEventListener("click", function () {
                const pdfUrl = pdfViewer.src;
                const a = document.createElement("a");
                a.href = pdfUrl;
                a.download = "archivo.pdf"; // Nombre del archivo descargado
                a.click();
            });

            // Botón para imprimir el PDF
            printBtn.addEventListener("click", function () {
                const iframe = pdfViewer.contentWindow || pdfViewer;
                iframe.focus();
                iframe.print();
            });

            // Cerrar modal si se hace clic fuera del contenido
            window.addEventListener("click", function (e) {
                if (e.target === modal) {
                    closeModalHandler();
                }
            });

            // Función para cerrar el modal
            function closeModalHandler() {
                modal.style.display = "none";
                modal.classList.remove("show");
                pdfViewer.src = ""; // Limpiar el src para evitar recargar el PDF innecesariamente
            }              
            
        }

          /*BEGIN DESACTIVAR UN USUARIO*/
          desactivarcitas(){

            console.log('desactivarcitas');

              const _this = this

              $( ".desactivar" ).off('click');
                $( ".desactivar" ).click(function() {
                    
                    const citasId = $( this ).attr( "attr-id" );
                    var opciones = {citasId:citasId};
                    const message = 'Seguro que desea cambiar de estatus el citas?'
                    const objConfirmacionmodal = new Confirmacionmodal(message, opciones, _this.callbackDesactivarcitas);
                  objConfirmacionmodal.init();
                  
              });
          }
          

          callbackDesactivarcitas(response, opciones){

   
              if(response == true){

                  const _this = this;

                  console.log(BASEURL +' por aqui vamos ' + opciones.citasId)

                  $.post( BASEURL+'/desactivar', 
                  {
                      citasId: opciones.citasId,
                      _token:token 
                      }
                  )
                  .done(function( data ) {

                    


                      if(data.response == true){
                          const modalTitle = 'citas';
                          const modalMessage = 'El citas ha sido cambiado de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          const objDistcitas = new Distcitas();							
                          objDistcitas.init();	
                          //objDistcitas.citas($( "#search" ).val());

                      }else{
                          
                          const modalTitle = 'citas';
                          const modalMessage = 'El citas no se ha podido cambiar de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          //const objDistcitas = new Distcitas();							
                          //objDistcitas.citas($( "#search" ).val());
                      }
                  })
                  .fail(function() {
                      
                      const modalTitle = 'citas';
                      const modalMessage = 'El citas no se ha podido cambiar de estatus';
                      const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                      objMessagebasicModal.init();

                      //const objDistcitas = new Distcitas();							
                      //objDistcitas.citas($( "#search" ).val());
                      
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

  const objDistcitas = new Distcitas();
  objDistcitas.init();

});