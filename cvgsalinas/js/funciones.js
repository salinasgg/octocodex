$(document).ready(function(){


    $("#modal-tp").load('modals/teleperformance.html');
    $("#modal-tf").load('modals/telefonica.html');
    $("#modal-teco").load('modals/telecom.html');
    // $("#modal-educa").load('modals/estudios.html');
    // $("#modal-destreza").load('modals/destrezas.html');
    
    // Datos personales al hacer click aparecen los datos
    $('#datos').click(function(){     
        $("#main").empty();   
        $.ajax({
            url: 'modulos/datos-personales.html',
            success: function(data) {
                $('#main').html(data);
            }
        });
    });

    //  Comienza Experiencia Laboral
    $("#experiencia").click(function(){        
        $("#main").empty();
       // $("#main").load('modulos/experiencia.html');
        $.ajax({
            url: 'modulos/experiencia.html',
            success: function(data) {
                $('#main').html(data);
            }
        });
    });
    //  Termina Experiencia Laboral

    //  Comienza Estudios
    $("#educacion").click(function(){        
        $("#main").empty();
       // $("#main").load('modulos/experiencia.html');
        $.ajax({
            url: 'modulos/estudios.html',
            success: function(data) {
                $('#main').html(data);
            }
        });
    });
    //  Termina Estudios    

    //  Comienza Destrezas
    $("#destreza").click(function(){        
        $("#main").empty();
       // $("#main").load('modulos/experiencia.html');
        $.ajax({
            url: 'modals/destrezas.html',
            success: function(data) {
                $('#main').html(data);
            }
        });
    });
    //  Termina Destrezas      


    //  Comienza certificados
    $("#certificados").click(function(){        
        $("#main").empty();
       // $("#main").load('modulos/experiencia.html');
        $.ajax({
            url: 'modulos/certificados.html',
            success: function(data) {
                $('#main').html(data);
            }
        });
    });
    //  Termina Certificados          

    $('table td a').click(function(event) {
        event.preventDefault(); // Evita que el enlace se siga normalmente
        alert("asda")
        // Obtener el atributo title del enlace
        var titulo = $(this).attr('title');

        // Hacer algo con el título obtenido
        console.log('El título del enlace es: ' + titulo);
        alert("asda")
        // Aquí puedes agregar tu lógica para utilizar el título obtenido
    });

    $(".img-linkedin").mouseover(function(){
        $(this).toggleClass("img-linkedin-grande");
    })

    $('#linkedin').click(function(){
        $("#carga").load("https://www.linkedin.com/in/gabriel-salinas-b312b82b/");
    });

    $("#imagen-datos").click(function(){
        $(this).toggleClass("imagen-grande");
    });

  });