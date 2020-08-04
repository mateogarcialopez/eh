$('#exportartablacalificaciones').on('click', function () {
    console.log('export');
    $('#tablacalificaciones').tableExport({
        type: 'png',
        escape: 'false'
    });

});

$('#exportartablaconsultas').on('click', function () {
    $('#tablaconsultas').tableExport({
        type: 'png',
        escape: 'false'
    });
    //onClick ="$('#tableID').tableExport({type:'pdf',escape:'false'});"

});

$('#btnconsultasFacebook').on('click', function () {

    var element = $("#consultasfacebook")[0];
    html2canvas((element), {
        onrendered: function (canvas) {
            var myImage = canvas.toDataURL();
            downloadURI(myImage, "odometer.png");
        }
    });

});

$('#btnconsultascalificaciones').on('click', function () {

    var element = $("#consultasCalificaciones")[0];
    html2canvas((element), {
        onrendered: function (canvas) {
            var myImage = canvas.toDataURL();
            downloadURI(myImage, "odometer.png");
        }
    });

});

function downloadURI(uri, name) {
    var link = document.createElement("a");

    link.download = name;
    link.href = uri;
    document.body.appendChild(link);
    link.click();
    
}